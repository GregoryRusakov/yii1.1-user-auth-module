<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class HybridController extends Controller
{
    
    public function actionIndex(){
        echo "OK";
    }     
    
    public function actionDisconnect($service){
        
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }   
        
        $userId=Yii::app()->user->id;
        if (empty($userId)){
            throw new CHttpException(404, 'Incorrect Id');
        }   

        $criteria = new CDbCriteria;
        $criteria->compare('user_id', $userId, false); 
        $criteria->compare('provider_name', $service, false); 

        $accountName=Yii::t('userProfile', $service);
                
        try{
            ExtAccounts::model()->deleteAll($criteria);
            Yii::app()->user->setFlash('info', 'Учетная запись ' . $accountName . ' успешно отключена.');    
            
        }catch(Exception $ex){
            $errorMessage=$ex->getMessage();
            Yii::log($errorMessage, CLogger::LEVEL_ERROR, 'hybridAuth');
        }
        
        $this->redirect(Yii::app()->createUrl('userprofiles'));
        //Common::renderExternalLoginCloseJS(Yii::app()->createUrl('userprofiles'));
        
    }

    public function actionConnect($service){
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }
        
        $serviceName=Yii::t('userProfile', $service);
        
        require_once(__DIR__ . "/../../../../myphp/hybridauth/Hybrid/Auth.php" );
        $config=require(__DIR__ . "/../../../../myphp/hybridauth/config.php" );
     
        $config['base_url']=Yii::app()->getBaseUrl(true) . $config['base_url'];
        
        try{
            $hybridauth = new Hybrid_Auth($config);
            $adapter = $hybridauth->authenticate($service);
            $user_profile = $adapter->getUserProfile();
            
        }catch(Exception $ex){
            $errorMessage=$ex->getMessage();
            Yii::log($errorMessage, CLogger::LEVEL_WARNING, 'hybridAuth');
            Yii::app()->user->setFlash('warning', 'Подключение учетной записи ' . $serviceName . ' не было выполнено.');
            AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(Yii::app()->params['errorLoginPage']));
            exit();
        }
        
        try{
            $this->connectServiceProfile($user_profile, $service);
            
        }catch (Exception $ex){
            Yii::log($ex->getMessage(), 'error', 'Connecting accout ' . $serviceName);
            AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(Yii::app()->params['errorLoginPage']));
            exit();
        }
      
        Yii::app()->user->setFlash('info', 'Учетная запись ' . $serviceName . ' подключена.');
        AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(Yii::app()->params['successLoginPage']));
    }
    
    public function actionLogin($service){
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }
        
        $serviceName=Yii::t('userProfile', $service);
        
        require_once(__DIR__ . "/../../../../myphp/hybridauth/Hybrid/Auth.php" );
        $config=require(__DIR__ . "/../../../../myphp/hybridauth/config.php" );
     
        $config['base_url']=Yii::app()->getBaseUrl(true) . $config['base_url'];
        
        try{
            $hybridauth = new Hybrid_Auth($config);
            $adapter = $hybridauth->authenticate($service);
            $user_profile = $adapter->getUserProfile();
            
        }catch(Exception $ex){
            $errorMessage=$ex->getMessage();
            Yii::log($errorMessage, CLogger::LEVEL_WARNING, 'hybridAuth');
            Yii::app()->user->setFlash('warning', 'Вход с учетной записью ' . $serviceName . ' не был выполнен.');

            AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(Yii::app()->params['errorLoginPage']));
            exit();
            
        }

        try{
            $user=$this->getUserByServiceProfile($user_profile, $service);
        }catch (Exception $ex){
            $errorMessage=$ex->getMessage();
            Yii::log($errorMessage, 'error', 'Login with account' . $service);
            Yii::app()->user->setFlash('error', $errorMessage);
            AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(''));
            exit();
        }
        
        //login user
        $username=$user->username;
        $password=$user->password_hash;
        $identity=new UserIdentity($username,$password);
        
        $identity->authenticate(true);
        
        if($identity->errorCode===UserIdentity::ERROR_NONE){
            Yii::app()->user->login($identity, 0);
        }
	else{
            echo 'Error loging in';
            exit();
        }        

        AuthCommon::renderExternalLoginCloseJS(Yii::app()->createUrl(Yii::app()->params['successLoginPage']));
    }
    
    private function connectServiceProfile($serviceProfile, $service){
        $userId=Yii::app()->user->id;
        
        $serviceUserId=$serviceProfile->identifier;
        $serviceUserEmail=$serviceProfile->emailVerified;
        
        $ExtAccount=ExtAccounts::model()->getService($userId, $service, $serviceUserId);

        $isChanged=false;
        if ($ExtAccount==null){
            //not connected before
            $ExtAccount=new ExtAccounts;
            $ExtAccount->user_id=$userId;
            $ExtAccount->provider_name=$service;
            $isChanged=true;
        }

        if (!$ExtAccount->connected || $ExtAccount->date_connected==null){
            $ExtAccount->connected=true;
            $dt = new DateTime();
            $currentDateString=$dt->format(AuthCommon::getParam('dateFormat'));    
            $ExtAccount->date_connected=$currentDateString;
            $ExtAccount->connected_manually=true;
            $ExtAccount->service_user_email=$serviceUserEmail;
            $ExtAccount->service_user_id=$serviceUserId;
            $isChanged=true;
        }
        
        if ($isChanged){
            if ($ExtAccount->saveModel()===false){
                throw new CHttpException(404, CHtml::errorSummary($ExtAccount));
            }  
        }
        
        return true;
                    
    }
    
    private function getUserByServiceProfile($serviceProfile, $service){
            
        //check if user exist in database
        $serviceUserId=$serviceProfile->identifier;
        $serviceUserEmail=$serviceProfile->emailVerified;

        //define service username
        if (array_key_exists('username', $serviceProfile) && !empty($serviceProfile->username)){
            $serviceUsername=$serviceProfile->username;
        }
        else{
            $serviceUsername=$serviceProfile->firstName . '' . $serviceProfile->lastName;
        }        
        
        $dt = new DateTime();
        $currentDateString=$dt->format(AuthCommon::getParam('dateFormat'));                
        
        $ExtAccount=ExtAccounts::model()->getUserByServiceIndentifier($service, $serviceUserId);
        if ($ExtAccount==null){
            //create external account
            $ExtAccount=new ExtAccounts;
            $ExtAccount->date_connected=$currentDateString;
            $ExtAccount->provider_name=$service;

            //check user in database by email
            if (!empty($serviceUserEmail)){
                $siteUser=Users::model()->getByEmail($serviceUserEmail);
            }
            else{
                //no external email, so we try to find by existing non manually created users
                //$isCreatedManually=false;
                //$siteUser=Users::model()->getByUsername($serviceUsername, $isCreatedManually);
                $accountName=Yii::t('userProfile', $service);
                throw new CHttpException(404, 'Нет адреса электронной почты в учетной записи ' . $accountName);
            }
        }
        else{
            //serivce found in database
            $userId=$ExtAccount->user_id;
            $siteUser=Users::model()->findByPk($userId);
        }
               
        if ($siteUser==null){
            //create database user
            $siteUser=new Users();
            $siteUser->created_manually=false;
            $siteUser->date_reg=$currentDateString;
            $siteUser->activated=true; //do not need activation by email
            $siteUser->ip_endorsed=AuthCommon::getUserIp();
            $userContemporary=new UsersComplementary;
        }
        else{
            //update database user
            $userContemporary=UsersComplementary::model()->getByUserById($siteUser->id);
        }
        
        if ($userContemporary==null){
            $userContemporary=new UsersComplementary;
        }
        
        $isNewUserContemporary=($userContemporary==null);
        
        $siteUser->scenario='serviceLogin';
        $siteUser->date_lastlogin=$currentDateString;
        
        if (!$siteUser->created_manually){
            //update user data if it is not created manually
            
            $siteUser->username=$serviceUsername;
            $siteUser->full_name=$serviceProfile->firstName . ' ' . $serviceProfile->lastName;

            if (empty($siteUser->email)){
               $siteUser->email=$serviceUserEmail;
            }
            
            $siteUser->comments='Updated from ' . ucwords($service);
        }
                    
        if ($siteUser->saveModel()===false){
            throw new CHttpException(404, CHtml::errorSummary($siteUser));
        }
        
        if ($isNewUserContemporary || !$siteUser->created_manually){
            $userContemporary->scenario='serviceLogin';
            $userContemporary->user_id=$siteUser->id;           
            $userContemporary->city=$serviceProfile->city;
            $userContemporary->country=$serviceProfile->country;
            $userContemporary->picture_url=$serviceProfile->photoURL;
            $userContemporary->language=$serviceProfile->language;
            $userContemporary->comments='Updated from ' . ucwords($service);

            if ($userContemporary->saveModel()===false){
                throw new CHttpException(404, CHtml::errorSummary($userContemporary));
            }  
        }
        
        //fill service user data
        $ExtAccount->user_id=$siteUser->id;
        $ExtAccount->connected=true;
        $ExtAccount->service_user_email=$serviceUserEmail;
        $ExtAccount->service_user_id=$serviceUserId;
              
        if ($ExtAccount->saveModel()===false){
            throw new CHttpException(404, CHtml::errorSummary($ExtAccount));
        }  
    
        return $siteUser;
    }
 
    public function actions()
    {
        return array(
            // captcha action only for AdminUser compatible model
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'maxLength'=> 4,
                'minLength'=> 4,
                'testLimit'=>3,
                'backColor'=>0xFFFFFF,

            ),
        );
    }       
}