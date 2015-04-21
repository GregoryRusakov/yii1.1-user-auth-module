<?php

/**
 * This is the model class for table "Users_complementary".
 */
class UsersComplementary extends CActiveRecord
{
    
        public $tmpUploadedPicture;
        
	public function tableName()
	{
		return 'users_complementary';
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
                        //array('user_id, picture_file', 'required', 'except'=>'serviceLogin'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('picture_file, city, country, comments', 'length', 'max'=>255),
                        array('language', 'length', 'max'=>50),
                        array('picture_url', 'length', 'max'=>1024),
			array('user_id, picture_file, picture_url, city, country, language, comments', 'safe', 'on'=>'search'),
                        array('tmpUploadedPicture', 'safe'),
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
                    //'users'=>array(self::BELONGS_TO, 'UserProfiles', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User ID',
			'picture_file' => 'Выбрать фото',
			'city' => 'Город',
			'comments' => 'Comments',
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
		$criteria->compare('picture_file',$this->picture_file,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('comments',$this->comments,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UsersComplementary the static model class
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

        public function getByUserById($user_id){
            
            $criteria=new CDbCriteria;
            $criteria->select = '*';
            $criteria->limit=1;
            $criteria->compare('user_id',$user_id, false); 
            $model=$this->find($criteria);

            if ($model==null){
                $model=new UsersComplementary;
            }
                        
            return $model;
        }        
}
