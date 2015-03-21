<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

/**
 * This is the model class for table "users".
 */


class Users extends CActiveRecord{
    public $password_entered;
    public $password_initial;
    public $verifyCode;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'users';
    }

       /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('licence_key', 'length', 'max'=>36),
                    array('email, full_name, comments', 'length', 'max'=>255),
                    array('username', 'length', 'max'=>100),
                    array('email, username, full_name', 'safe', 'on'=>'search'),

                    array('password_entered', 'safe'),
                    array('email', 'email'),
                    array('username, email', 'required', 'on'=>'insert, update'),

                    //array('email', 'uniqueEmail','message'=>Yii::t('AuthModule.forms', 'Email already taken'),'except'=>'passRestore, lastlogin'),
                    //array('username', 'uniqueUsername','message'=>Yii::t('AuthModule.forms', 'Username already taken'),'on'=>'insert'),
                    //array('password_entered', 'passwordStrength', 'except'=>'passRestore, activation, update, lastlogin'),
 
                    array('password_entered', 'modules.auth.components.validators.passValidator', 'except'=>'passRestore, activation, update, lastlogin'),
                    array('email', 'modules.auth.components.validators.uniqueEmail','message'=>Yii::t('AuthModule.forms', 'Email already taken'),'except'=>'passRestore, lastlogin'),
                    array('username', 'modules.auth.components.validators.uniqueUsername','message'=>Yii::t('AuthModule.forms', 'Username already taken'),'on'=>'insert'),
                    
                    array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest || !CCaptcha::checkRequirements(),'except'=>'passRestore, activation, lastlogin'),

                    array('username', 'safe', 'except'=>'update'),
                    array('username', 'unsafe', 'on'=>'update'),

            );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
            );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'id' => Yii::t('AuthModule.forms','ID'),
                    'licence_key' => Yii::t('AuthModule.forms','Licence key'),
                    'email' => Yii::t('AuthModule.forms','Email'),
                    'username' => Yii::t('AuthModule.forms','Login'),
                    'full_name' => Yii::t('AuthModule.forms','Full name'),
                    'date_reg' => Yii::t('AuthModule.forms','Registration date'),
                    'comments' => Yii::t('AuthModule.forms','Comments'),
                    'password_entered' => Yii::t('AuthModule.forms','Password'),
                    'verifyCode'=>Yii::t('AuthModule.forms','Verify code'),
            );
    }


    public function accessRules() {
        return array(
            // если используется проверка прав, не забывайте разрешить доступ к
            // действию, отвечающему за генерацию изображения
            array('allow',
                'actions'=>array('captcha'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria=new CDbCriteria;

            $criteria->compare('email',$this->email,true);
            $criteria->compare('name',$this->name,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Users the static model class
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    public function saveModel(){

            if ($this->scenario=='insert'){
                $hash=password_hash($this->password_entered, PASSWORD_BCRYPT, array('cost' => 10));
                $this->password_hash = $hash;
                $dt = new DateTime();
                $this->date_reg=$dt->format(Common::getParam('dateFormat'));                   
                $ip=Yii::app()->request->getUserHostAddress();
                $this->ip_endorsed=$ip;
            }
            
            elseif ($this->scenario=='update' || $this->scenario=='passRestore'){
                if (!empty($this->password_entered)){
                    $hash=password_hash($this->password_entered, PASSWORD_BCRYPT, array('cost' => 10));
                    $this->password_hash = $hash;
                }
            }
            elseif ($this->scenario=='activation'){
                $this->activated=true;
            }

            if(!$this->save()){
                yii::app()->user->setFlash('error', CHtml::errorSummary($this));
                return false;
            }

         return true;
    }

    public function setInitialPassword($password){
        $this->password_initial=$password;
    }

    public function getByEmail($email)
    {
        $criteria=new CDbCriteria;
        $criteria->select = 'email,id';
        $criteria->limit=1;
        $criteria->compare('LOWER(email)',strtolower($email)); 
        $model=$this->find($criteria);
            
        return $model;
    }

    public function getByUserName($username)
    {
        $criteria=new CDbCriteria;
        $criteria->select = '*';
        $criteria->limit=1;
        $criteria->compare('LOWER(username)',strtolower($username)); 
        $model=$this->find($criteria);
        
        //$model=$this->findByAttributes(array('username'=>$username));
        return $model;
    }

    /*public function uniqueEmail($attribute, $params){
        if (empty($this->email)){
            return;
        }
        $modelFound=$this->getByEmail($this->email);
        if ($modelFound!=null && ($modelFound->id!=$this->id)){
            $this->addError($attribute, $params['message']);
        }
    }

    public function uniqueUsername($attribute, $params){
        if (empty($this->usrname)){
            return;
        }
        $modelFound=$this->getByUserName($this->username);
        if ($modelFound!=null && ($modelFound->id!=$this->id)){
            $this->addError($attribute, $params['message']);
        }
    }*/

  /*  public function passwordStrength($attribute){
        $passStrength=Yii::app()->params['passStrength'];
        if ($passStrength==null){
            $passStrength=0;
        }
        
        if ($passStrength==0){
            $pattern = '/^(?=.*[a-zA-Z0-9]).{4,}$/';  
            $message=Yii::t('AuthModule.forms', 'Password is not strong enough (weak)');
        }
        elseif ($passStrength==1){
            $pattern = '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/';  
            $message=Yii::t('AuthModule.forms', 'Password is not strong enough (strong)');
        }
        
        if(!preg_match($pattern, $this->$attribute)){
          $this->addError($attribute, $message);
        }
    }
*/
    
}
