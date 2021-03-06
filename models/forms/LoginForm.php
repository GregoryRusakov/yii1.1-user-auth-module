<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
        public $verifyCode;
        
	private $_identity;
        
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
                        array('verifyCode', 'required', 'on' => 'withCaptcha'),
                        array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'on' => 'withCaptcha'),
                        array('verifyCode', 'safe'),
			array('username, password', 'required'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticatePass'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>Yii::t('AuthModule.forms','Remember me'),
                        'username'=>Yii::t('AuthModule.forms','Username'),
                        'password'=>Yii::t('AuthModule.forms','Password'),
                        'verifyCode'=>Yii::t('AuthModule.forms','verifyCode'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticatePass($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate()){
				$this->addError('password',Yii::t('AuthModule.main','Incorrect login or password'));
                        }

		}
                
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
                
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
                        $days=AuthCommon::getParam('cookieBasedLoginDays');
                        if (empty($days)){
                            $days=14;                            
                        }
			$duration=$this->rememberMe ? 3600*24*$days : 0;
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
                {
			return false;
                }
	}
}
