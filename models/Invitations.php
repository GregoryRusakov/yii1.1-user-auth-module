<?php

/**
 * This is the model class for table "invitations".
 *
 * The followings are the available columns in table 'invitations':
 * @property integer $id
 * @property string $guid
 * @property string $date_issued
 * @property string $date_occuped
 * @property integer $username_created
 * @property string $comments
 */
class Invitations extends CActiveRecord
{
        const SHOW_ALL='all';
        const SHOW_USED='used';
        const SHOW_UNUSED='unused';
        
        public $verifyCode;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'auth_invitations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('guid', 'required'),
                        array('date_issued', 'required', 'on'=>'generation'),
                        array('date_occuped, used', 'required', 'on'=>'use'),
			array('username_created', 'length', 'max'=>100),
			array('guid', 'length', 'max'=>25),
			array('comments', 'length', 'max'=>255),
			array('guid, date_issued, date_occuped, username_created, comments', 'safe', 'on'=>'search'),
                        array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest || !CCaptcha::checkRequirements(),'except'=>'generation, use'),
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
			'guid' => Yii::t('AuthModule.forms', 'Invitation'),
			'date_issued' => 'Date Issued',
			'date_occuped' => 'Date Occuped',
			'username_created' => 'Username Created',
			'comments' => 'Comments',
                        'id' => 'ID',

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
	public function search($showMode=self::SHOW_ALL)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('guid',$this->guid);
		$criteria->compare('date_issued',$this->date_issued);
		$criteria->compare('date_occuped',$this->date_occuped);
		$criteria->compare('username_created',$this->username_created);
                
                switch ($showMode){
                    case self::SHOW_USED:
                        $criteria->compare('used',1);
                        break;
                    case self::SHOW_UNUSED:
                        $criteria->compare('used',0);
                        break;
                }
		$criteria->compare('comments',$this->comments);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                        'sort'=>array('attributes'=>array('*')),
		));
   
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Invitations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function checkAvaliable($guid){
            //find guid 

            $criteria=new CDbCriteria;
            $criteria->select='*';
            $criteria->addCondition('guid=:param1');
            $criteria->addCondition('used=:param2');
            $criteria->params=array(':param1'=>$guid, ':param2'=>false);
            $criteria->limit = 1;

            $invitation=self::model()->find($criteria);
            
            return ($invitation!=null);
        }
        
        public function saveModel(){
            
            $dt = new DateTime();
            $curDate=$dt->format(Helpers::getAppParam('dateFormat'));                   
            
            if ($this->scenario=='generate'){
                $this->date_issued=$curDate;
            }
            elseif ($this->scenario=='use'){
                $this->date_occuped=$curDate;
            }
                        
            if(!$this->save()){
                return false;
            }

            return true;
        }
        
        public static function setUsed($guid, $username){
            $invitation=self::model()->findByAttributes(array('guid'=>$guid));
            if ($invitation==null){
                return false;
            }
            $invitation->scenario='use';
            $invitation->username_created=$username;
            $invitation->used=true;
            
            if (!$invitation->saveModel()){
                return false;
            }
            
            return true;
        }

}
