<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class Common {
    
    public function getParam($paramName){
        $value=Yii::app()->controller->module->{$paramName};
        return $value;
    }

    public function getGUID(){
        if (function_exists('com_create_guid')){
            $guid=com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                    .substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12)
                    .chr(125);// "}"
            $guid=$uuid;
        }
        
        $guid=trim($guid, "{}");
        
        return $guid;
    }
    
    public function sendPassRequestEmail($email, $guid){

        $siteName=Yii::app()->name;
        $fullPageAddress=self::getParam('websiteHost').Yii::app()->createUrl('auth/user/passchange');
        
        $headers=Common::createMailHeader();
                
        $subject=Common::getTemplateValue('mail', 'restore_subject');
        $subject=sprintf($subject, $siteName);
        
        $text=Common::getTemplateValue('mail', 'restore_text');

        $restoreLink=$fullPageAddress."&guid=$guid";
        $text=sprintf($text, $siteName, $restoreLink, $guid, $fullPageAddress);
        
        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
        $body=$text;
        
        return mail($email,$subject,$body,$headers);
    }
        
    public function sendActivationtEmail($email, $guid){
        $siteName=Yii::app()->name;
        $fullPageAddress=self::getParam('websiteHost').Yii::app()->createUrl('auth/user/activation');
        
        $headers=Common::createMailHeader();
                
        $subject=Common::getTemplateValue('mail', 'activation_subject');
        $subject=sprintf($subject, $siteName);
        
        $text=Common::getTemplateValue('mail', 'activation_text');
        $restoreLink=$fullPageAddress."&guid=$guid";
        
        $text=sprintf($text, $siteName, $restoreLink, $guid, $fullPageAddress);
        
        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
        $body=$text;
        
        return mail($email,$subject,$body,$headers);
     
    }
    
    function createMailHeader($fromEmail='', $fromName=''){
        
        if (empty($fromEmail)){
            $fromEmail=self::getParam('adminEmail');
        }
                
        if (empty($fromName)){
            $fromName=Yii::app()->name;
        }
        
        $name='=?UTF-8?B?'.base64_encode($fromName).'?=';
        $headers="From: $name <{$fromEmail}>\r\n".
            "Reply-To: {$fromEmail}\r\n".
            "MIME-Version: 1.0\r\n".
            "Content-Type: text/html; charset=UTF-8";
    
        return $headers;
    }
    
    function showError($message, $title=''){
        $this->render('/user/user_error', array('title'=>$title,'message'=>$message));
    }
    
    function getTemplateValue($templateName, $valueName){
        $templateDir='protected/modules/auth/templates';
        $lang = Yii::app()->language;
        
        $fullPath=$templateDir.'/'.$lang.'/'.$templateName.'.php';
        $fileLocalPath=Yii::getPathOfAlias('webroot').'/'.$fullPath;
        $templates=require($fileLocalPath);
                
        if (array_key_exists($valueName, $templates)){
            $value=$templates[$valueName];
        }
        else{
            $value = '';
        }
        
        return $value;
               
    }

    function getUserIp(){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }
        else{
            if ($remote=="::1"){
                $remote="127.0.0.1";
            }
            $ip = $remote;
        }

        return $ip;
    }
    
    public function isAdminUser($id){
        return true;
    }
    
    public function renderSocialLogin($serviceName, $renderScript=false){
        
        if ($renderScript){
            Yii::app()->clientScript->registerScript("serviceWindow", "
                function login(url){

                    var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
                         screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
                         outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
                         outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
                         width    = 720,
                         height   = 480,
                         left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
                         top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
                         features = (
                            'width=' + width +
                            ',height=' + height +
                            ',left=' + left +
                            ',top=' + top

                          );

                    newwindow=window.open(url,'ServiceLogin',features);

                   if (window.focus) {
                       newwindow.focus();
                   }
                   return false;
                }
                ", CClientScript::POS_HEAD);
        } //render window script
        
        $serviceNameLower=strtolower($serviceName);
        $loginUrl=Yii::app()->createUrl('auth/hybrid/login', array('service'=>$serviceNameLower));
        $imageUrl='images/icons/' . $serviceNameLower . '.png';
        
        $onClickJS='login("' . $loginUrl . '"); return false;';
        
        $imgHtml=Chtml::image($imageUrl, $serviceName, array('class'=>'socialIcon'));
        
        //show icon with link
        echo CHtml::link($imgHtml, $loginUrl, array('onclick'=>$onClickJS));
        
        
    }
}
