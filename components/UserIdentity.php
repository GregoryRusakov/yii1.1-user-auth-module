<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class UserIdentity extends CUserIdentity
{
        private $_id;
        private $_username;
        
        const LOGIN_TOKEN="logintoken";
        
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
            
            $ip=Common::getUserIp();
            
            $timeZoneLabel=Common::getParam('timeZoneLabel');
            $dateFormat=Common::getParam('dateFormat');
            
            if (!empty($ip)){
                $result=$this->checkIpBlocked($ip);
                if ($result!=null){
                    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                    yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','Your IP address has been blocked'), $ip, $result, $timeZoneLabel));
                    return false;             
                }
            }
            
            $modelUser=Users::model()->getByUserName($this->username);
            
            if (empty($modelUser)){
                $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                $this->saveUnsuccessfulIpAttempt($ip, $this->username);
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Inrorrect login or password'));
                return false;
            }
            
            if (!$modelUser->activated){
                $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                 Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','User not activated'), $modelUser->username));
                 return false;
            }                 
          
            if ($modelUser->blocked){
                //blocked by admin
                $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                 Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','User has been blocked'), $modelUser->username));
                 return false;
            }            
            
            if ($modelUser->deleted){
                $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                 Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','User has been deleted'), $modelUser->username));
                 return false;
            }            
            
            $result=$this->checkUserBlocked($modelUser);
            if ($result!=null){
                $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
                Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','User has been blocked until'), $result));
                return false;
            }
            
            $password_hash=$modelUser->password_hash;
            $pass=$this->password;

            if(!password_verify($pass, $password_hash)) {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
                $result=$this->saveUnsuccessfulIpAttempt($ip, $modelUser->username);
                if ($result!=null){
                    Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','Too much login attempts from IP'), $ip, $result->format($dateFormat), $timeZoneLabel));
                    return;
                }
                
                $result=$this->saveUnsuccessfulUserAttempt($modelUser);
                if ($result!=null){
                    Yii::app()->user->setFlash('error', sprintf(Yii::t('AuthModule.main','Too much login attempts from user'), $modelUser->username, $result->format($dateFormat), $timeZoneLabel));
                    return;
                }
                Yii::app()->user->setFlash('error', Yii::t('AuthModule.main','Login failed'));
                
                return false;
            }
            
            //login OK
            
            $this->errorCode=self::ERROR_NONE;
            
            $this->_id=$modelUser->id;
            $this->_username=$modelUser->username;
            
            $this->saveSuccessfulUserAttemt($modelUser);
            $this->saveSuccessfulIpAttemt($ip);

            // Generate a login token and save it in the DB
            $dt = new DateTime();
            $modelUser->date_lastlogin=$dt->format($dateFormat);
            $modelUser->setScenario('lastLogin');
            $modelUser->logintoken = sha1(uniqid(mt_rand(), true));
            if ($modelUser->save()){
                //the login token is saved as a state
                $this->setState(self::LOGIN_TOKEN, $modelUser->logintoken);            
            }
            else{
                yii::app()->user->setFlash('error', CHtml::errorSummary($modelUser));
            }
            
             $this->setState('username', $modelUser->username);
             
            return true;
	}
        
        public function getId(){
            return $this->_id;
        }
         
        public function saveUnsuccessfulUserAttempt($userModel){
 
            $userBlockedUntil=null;

            $unsafeUser=Unsafeusers::model()->getByUserId($userModel->id);
            if ($unsafeUser==null){
                $unsafeUser= new Unsafeusers();
                $unsafeUser->user_id=$userModel->id;
            }
            $unsafeUser->attempts++;
            $unsafeUser->attempts_total++;
            $unsafeUser->comments="user: ".$userModel->username;
            
            $maxAttempts=Common::getParam('userBlockMaxLoginAttempts');
            
            if ($maxAttempts>0){
                if ($unsafeUser->attempts<$maxAttempts){
                    $unsafeUser->blocked_until=null;
                }    
                else{
                    $userBlockTimeMinutes=Common::getParam('userBlockTimeMinutes');
                    if ($userBlockTimeMinutes>0){
                        //block user
                        $dt = new DateTime();
                        $dt->add(new DateInterval('PT' . $userBlockTimeMinutes . 'M'));
                        $unsafeUser->blocked_until=$dt->format(Common::getParam('dateFormat'));     
                        $userBlockedUntil=$dt;
                    }
                }
            }
            if (!$unsafeUser->saveModel()){
                //can't block user
                $userBlockedUntil=null;
            }
            
            return $userBlockedUntil;
            
        }
        
        public function saveUnsuccessfulIpAttempt($ip, $username){
 
            if (empty($ip)){
                return;
            }

            $ipBlockedUntil=null;

            $unsafeIp=Unsafeip::model()->getByIp($ip);
            if ($unsafeIp==null){
                $unsafeIp= new Unsafeip();
                $unsafeIp->ip_address=$ip;
            }
            $unsafeIp->attempts++;
            $unsafeIp->attempts_total++;
            $unsafeIp->comments="user: ".$username;
            
            $maxAttempts=Common::getParam('ipBlockMaxLoginAttempts');
            
            if ($maxAttempts>0){
                if ($unsafeIp->attempts<$maxAttempts){
                    $unsafeIp->blocked_until=null;
                }    
                else{
                    $ipBlockTimeMinutes=Common::getParam('ipBlockTimeMinutes');
                    if ($ipBlockTimeMinutes>0){
                        //block user
                        $dt = new DateTime();
                        $dt->add(new DateInterval('PT' . $ipBlockTimeMinutes . 'M'));
                        $unsafeIp->blocked_until=$dt->format(Common::getParam('dateFormat'));     
                        $ipBlockedUntil=$dt;
                    }
                }
            }
            if (!$unsafeIp->saveModel()){
                //can't block user
                $ipBlockedUntil=null;
            }
            
            return $ipBlockedUntil;
            
        }
        
        public function saveSuccessfulUserAttemt($userModel){
 
            $unsafeUser=Unsafeusers::model()->getbyUserId($userModel->id);
            if ($unsafeUser==null){
                //there is no any unsuccessful records for this user
                return;
            }
            
            $unsafeUser->attempts=0;
            $unsafeUser->blocked_until=null;
            
            $unsafeUser->saveModel();           
        }     
        
        public function saveSuccessfulIpAttemt($ip){
 
            if (empty($ip)){
                return;
            }
            
            $unsafeIp=Unsafeip::model()->getByIp($ip);
            if ($unsafeIp==null){
                //there is no any unsuccessful records for this ip
                return;
            }
            
            $unsafeIp->attempts=0;
            $unsafeIp->blocked_until=null;
            
            $unsafeIp->saveModel();           
        }     
        
      public function checkUserBlocked($userModel){
 
            $unsafeUser=Unsafeusers::model()->getByUserId($userModel->id);
            if ($unsafeUser==null){
                //there is no any unsuccessful records for this user
                return null;
            }
            
            $blocked_until=$unsafeUser->blocked_until;
            
            if ($blocked_until==null){
                return null;
            }
            
            //compare block until date and current date
            
            $curDate = new DateTime();
            $blockUntilInt=strtotime($blocked_until);
            $curDateInt=$curDate->getTimestamp();
            if ($curDateInt>$blockUntilInt){
                return null;
            }
            
            return $blocked_until;
        }       
        
        public function checkIpBlocked($ip){
 
            $unsafeIp=Unsafeip::model()->getByIp($ip);
            if ($unsafeIp==null){
                //there is no any unsuccessful records for this ip
                return null;
            }
            
            $blocked_until=$unsafeIp->blocked_until;
            
            if ($blocked_until==null){
                return null;
            }
            
            //compare block until date and current date
            
            $curDate = new DateTime();
            $blockUntilInt=strtotime($blocked_until);
            $curDateInt=$curDate->getTimestamp();
            if ($curDateInt>$blockUntilInt){
                return null;
            }
            
            return $blocked_until;
        }       
}