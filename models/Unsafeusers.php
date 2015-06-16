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
 * This is the model class for table "unsafeusers".
 *
 * The followings are the available columns in table 'unsafeusers':
 * @property integer $id
 * @property integer $user_id
 * @property integer $attempts
 * @property integer $attempts_total;
 * @property string $blocked_until
 * @property string $comments
 */

class Unsafeusers extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'auth_unsafeusers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('user_id', 'required'),
                    array('user_id, attempts, attempts_total', 'numerical', 'integerOnly'=>true),
                    array('comments', 'length', 'max'=>250),
                    array('id, user_id, attempts, attempts_total, blocked_until, comments', 'safe', 'on'=>'search'),
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
                'id' =>Yii::t('AuthModule.forms','ID'),
                'user_id' => Yii::t('AuthModule.forms','User ID'),
                'attempts' => Yii::t('AuthModule.forms','Attempts'),
               'attempts_total' => Yii::t('AuthModule.forms','Total attempts'),
                'blocked_until' => Yii::t('AuthModule.forms','Blocked until'),
                'comments' => Yii::t('AuthModule.forms','Comments'),
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

            $criteria->compare('id',$this->id);
            $criteria->compare('user_id',$this->user_id);
            $criteria->compare('attempts',$this->attempts);
            $criteria->compare('blocked_until',$this->blocked_until);
            $criteria->compare('comments',$this->comments,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Unsafeusers the static model class
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
        
    public function saveModel(){

        if(!$this->save()){
            yii::app()->user->setFlash('error', CHtml::errorSummary($this));
            return false;
        }
        
        return true;
    }
    
    public function getByUserId($userId){
        $model=$this->findByAttributes(array('user_id'=>$userId));
        return $model;
    }

    
}
