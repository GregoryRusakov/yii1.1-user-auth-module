<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */
 
class AuthModule extends CWebModule
{
         
        public $attemptsBeforeCaptcha=0; //if zero then always use capcha
        public $cookieBasedLoginDays=0;
        
        //set user block parameters (if zero then do not use block)
        public $userBlockMaxLoginAttempts=0;
        public $userBlockTimeMinutes=0;
        public $ipBlockMaxLoginAttempts=0;
        public $ipBlockTimeMinutes=0;
        
        public $dateFormat='';
        public $timeZoneLabel='GMT';
        
        public $adminEmail='';
        public $websiteHost='';
        
        public $profilePage=array('user/index');
        
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'auth.models.*',
                        'auth.models.forms.*',
			'auth.components.*',
                    
		));
              
                if ($this->dateFormat==null){
                    try{
                        $value=Yii::app()->params[$paramName];
                        $this->dateFormat=$value;
                    }catch(Exception $ex){
                        //nothing to do because parameter in application is apsent
                        $ex=null;
                        $this->dateFormat='Y-m-d H:i:s';
                    
                    }
                    
                    
                }
                
                /*
                Yii::app()->setComponents(          
                        array('messages' => array(
                                'class'=>'CPhpMessageSource',
                                'basePath'=>'protected/modules/auth/messages',
                        )));
                 */
                
	}
        
    
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
