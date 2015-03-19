<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class authWebUser extends CWebUser{
    
    protected function beforeLogin($id,$states,$fromCookie) {
        
        if ($fromCookie){
            
            //the cookie isn't here, so we refuse the login
            if(!isset($states[UserIdentity::LOGIN_TOKEN])){
                return false;
            }

            $model = Users::model()->findByPk($id);            
            if ($model==null){
                return false;
            }

            //check if cookie is correct
            $cookieLoginToken = $states[UserIdentity::LOGIN_TOKEN];
            
            if(!isset($cookieLoginToken)|| $cookieLoginToken != $model->logintoken) {
                //throw new CHttpException(404, 'Автоматический вход по cookie не возможен.');
                return false;
            }

            if (!$model->activated || $model->blocked || $model->deleted){
                //user deleted
                return false;
            }
                                
        }
            
        if (!parent::beforeLogin($id,$states,$fromCookie)){
            return false;
        }
        
        return true;
        
    }   
      
}

