<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class PassRequestForm extends CFormModel
{
	public $email;
        public $verifyCode;
        
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email', 'required'),
                        //array('email', 'email'),
                        array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest || !CCaptcha::checkRequirements(),),
                        
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t('AuthModule.forms','Email'),
                        'verifyCode'=>Yii::t('AuthModule.forms','Verify code'),
		);
	}
        public function filters() {
            return array(
                'accessControl',
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
       
}
