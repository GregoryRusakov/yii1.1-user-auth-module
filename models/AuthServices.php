<?php

/**
 * This is the model class for table "auth_services".
 *
 */
class AuthServices extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'auth_services';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, provider_name, service_user_id', 'required'),
			array('user_id, connected_manual', 'numerical', 'integerOnly'=>true),
			array('provider_name', 'length', 'max'=>20),
			array('service_user_email', 'length', 'max'=>255),
			array('service_user_id', 'length', 'max'=>100),
			array('user_id, provider_name, date_connected, connected, service_user_email, service_user_id, connected_manual', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'user_id' => 'User',
			'provider_name' => 'Provider Name',
			'date_connected' => 'Date connected',
			'connected' => 'Connected',
			'service_user_email' => 'Service User Email',
			'service_user_id' => 'Service User',
			'connected_manual' => 'Connected Manual',
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
		$criteria->compare('provider_name',$this->provider_name,true);
		$criteria->compare('date_connected',$this->date_connected,true);
		$criteria->compare('connected',$this->connected);
		$criteria->compare('service_user_email',$this->service_user_email,true);
		$criteria->compare('service_user_id',$this->service_user_id,true);
		$criteria->compare('connected_manual',$this->connected_manual);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuthServices the static model class
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
        
        public function getUserByServiceIndentifier($service, $serviceUserId){
            $criteria=new CDbCriteria;
            $criteria->select = '*';
            $criteria->limit=1;
            $criteria->compare('LOWER(provider_name)',strtolower($service), false); 
            $criteria->compare('LOWER(service_user_id)',strtolower($serviceUserId), false); 
            $model=$this->find($criteria);

            return $model;
        }
}
