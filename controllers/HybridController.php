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

    public function actionConnect($service){
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }
        
        $serviceName=ucwords($service);
        
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
            $this->redirect(array('/userprofiles'));
        }
        
        //var_dump($user_profile);
        //exit();
        
        try{
            $this->connectServiceProfile($user_profile, $service);
            
        }catch (Exception $ex){
            Yii::log($ex->getMessage(), 'error', 'Connecting accout ' . $serviceName);
            throw new CHttpException(404, 'Error connecting account ' . $serviceName);
        }
      
        Yii::app()->user->setFlash('info', 'Учетная запись ' . $serviceName . ' подключена.');
        $this->redirect(array('/userprofiles'));
        
    }
    
    public function actionLogin($service){
        if (empty($service)){
            throw new CHttpException(404, 'Incorrect login query');
        }
        
        $serviceName=ucwords($service);
        
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
            $errorLoginUrl=Yii::app()->createUrl('');
            echo '
                <script>
                if (window.opener){
                    window.opener.location.href="' . $errorLoginUrl . '"
                    window.close();
                }else {
                }
                </script>';

            exit();
            
        }
        
        //var_dump($user_profile);
        //exit();
        
        try{
            $user=$this->getUserByServiceProfile($user_profile, $service);
        }catch (Exception $ex){
            Yii::log($ex->getMessage(), 'error', 'Login with account' . $service);
            throw new CHttpException(404, 'Error logging with account ' . ucwords($service));
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

        $successLoginUrl=Yii::app()->createUrl('userprofiles');
        echo '<script>
            if (window.opener){
                window.opener.location.href="' . $successLoginUrl . '"
                window.close();
            }else {
            }
            </script>';
        
    }
    
    private function connectServiceProfile($serviceProfile, $service){
        $userId=Yii::app()->user->id;
        
        $serviceUserId=$serviceProfile->identifier;
        $serviceUserEmail=$serviceProfile->emailVerified;
        
        $serviceUser=AuthServices::model()->getService($userId, $service, $serviceUserId);

        $isChanged=false;
        if ($serviceUser==null){
            //not connected before
            $serviceUser=new AuthServices;
            $serviceUser->user_id=$userId;
            $serviceUser->provider_name=$service;
            $isChanged=true;
        }

        if (!$serviceUser->connected || $serviceUser->date_connected==null){
            $serviceUser->connected=true;
            $dt = new DateTime();
            $currentDateString=$dt->format(Common::getParam('dateFormat'));    
            $serviceUser->date_connected=$currentDateString;
            $serviceUser->connected_manual=true;
            $serviceUser->service_user_email=$serviceUserEmail;
            $serviceUser->service_user_id=$serviceUserId;
            $isChanged=true;
        }
        
        if ($isChanged){
            if ($serviceUser->saveModel()===false){
                throw new CHttpException(404, CHtml::errorSummary($serviceUser));
            }  
        }
        
        return true;
                    
    }
    
    private function getUserByServiceProfile($serviceProfile, $service){
            
        //check if user exist in database
        $serviceUserId=$serviceProfile->identifier;
        $serviceUserEmail=$serviceProfile->emailVerified;

        $dt = new DateTime();
        $currentDateString=$dt->format(Common::getParam('dateFormat'));                
        
        $serviceUser=AuthServices::model()->getUserByServiceIndentifier($service, $serviceUserId);
        if ($serviceUser==null){
            //create service user
            $serviceUser=new AuthServices;
            $serviceUser->date_connected=$currentDateString;
            $serviceUser->provider_name=$service;

            //check user in database by email
            $siteUser=Users::model()->getByEmail($serviceUserEmail);
        }
        else{
            //serivce found in database
            $userId=$serviceUser->user_id;
            $siteUser=Users::model()->findByPk($userId);
        }
                
        if ($siteUser==null){
            //create database user
            $siteUser=new Users();
            $siteUser->date_reg=$currentDateString;
            $siteUser->activated=true; //do not need activation by email
            $siteUser->ip_endorsed=Common::getUserIp();
            $userContemporary=new UsersComplementary;
            
        }
        else{
            //update database user
            $userContemporary=UsersComplementary::model()->getByUserById($siteUser->id);
        }
        
        if ($userContemporary==null){
            $userContemporary=new UsersComplementary;
        }
        
        $siteUser->scenario='serviceLogin';
        $siteUser->full_name=$serviceProfile->firstName . ' ' . $serviceProfile->lastName;
        
        if (array_key_exists('username', $serviceProfile) && !empty($serviceProfile->username)){
            $siteUser->username=$serviceProfile->username;
        }
        else{
            $siteUser->username=$serviceProfile->firstName . '' . $serviceProfile->lastName;
        }
        
        $siteUser->date_lastlogin=$currentDateString;
        if (empty($siteUser->email)){
            $siteUser->email=$serviceUserEmail;
        }
        $siteUser->comments='Updated from ' . ucwords($service);
            
        if ($siteUser->saveModel()===false){
            throw new CHttpException(404, CHtml::errorSummary($siteUser));
        }
        
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
        
        //fill service user data
        $serviceUser->user_id=$siteUser->id;
        $serviceUser->connected=true;
        $serviceUser->service_user_email=$serviceUserEmail;
        $serviceUser->service_user_id=$serviceUserId;
              
        if ($serviceUser->saveModel()===false){
            throw new CHttpException(404, CHtml::errorSummary($serviceUser));
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