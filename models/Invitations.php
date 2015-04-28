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
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'invitations';
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
                        array('date_issued', 'required', 'on'=>'insert'),
                        array('date_occuped', 'required', 'on'=>'registration'),
			array('username_created', 'numerical', 'integerOnly'=>true),
			array('guid', 'length', 'max'=>32),
			array('comments', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('guid, date_issued, date_occuped, username_created, comments', 'safe', 'on'=>'search'),
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('guid',$this->guid,true);
		$criteria->compare('date_issued',$this->date_issued,true);
		$criteria->compare('date_occuped',$this->date_occuped,true);
		$criteria->compare('username_created',$this->username_created);
		$criteria->compare('comments',$this->comments,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
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
            $criteria->addCondition('date_occuped=:param2');
            $criteria->params=array(':param1'=>$guid, ':param2'=>null);
            $criteria->limit = 1;

            $invitation=self::model()->find($criteria);
            
            return $invitation;
        }

}
