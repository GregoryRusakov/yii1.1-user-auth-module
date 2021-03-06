<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class AuthCommon {
    
    public function getParam($paramName){
        if ($paramName=='fromEmail' || $paramName=='adminEmail'){
            $value=Helpers::getAppParam($paramName);
        }
        else{
            $value=Yii::app()->controller->module->{$paramName};
        }
        return $value;
    }

    public function notifyAdminAboutUser(&$modelUser, $scenario){
        $userEmail=$modelUser->email;
        $adminEmail=AuthCommon::getParam('adminEmail');
        $userName=$modelUser->username;
        
        switch ($scenario){
            case 'insert':
                $actionSubject='Новый пользователь';
                $actionText='зарегистрировался';
                break;
            case 'activation':
                $actionSubject='Активация пользователя';
                $actionText='выполнил активацию';                
                break;
            case 'update':
                $actionSubject='Изменение данных пользователя';
                $actionText='изменил данные';                               
                break;
            case 'passRestore':
                $actionSubject='Пользователь восстановил пароль';
                $actionText='восстановил пароль';                                
                break;
            default:
                return;
        }
        
        $websiteUrl=Yii::app()->getBaseUrl(true);
        $siteName=Yii::app()->name;
                
        $headers=AuthCommon::createMailHeader();
                
        $subjectTemplate=AuthCommon::getTemplateValue('mail', 'notifyAdmin_subject');
        $subject=sprintf($subjectTemplate, $actionSubject, $siteName);
        
        $textTemplate=AuthCommon::getTemplateValue('mail', 'notifyAdmin_text');
        $body=sprintf($textTemplate, $userName, $userEmail, $actionText, $websiteUrl);
        
        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
        
        return mail($adminEmail, $subject, $body, $headers);
        
    }
    
    public function generateLicenceKey(){
        $key=self::generateKey();
        $i=0; $iMax=100;
        $model=Users::model()->getByLicenceKey($key);
        
        while ($model!=null){
            $key=self::generateKey();
            $model=Users::model()->getByLicenceKey($key);
            $i++;
            if ($i>=$iMax){
                Yii::log('Cannot generate licence key. ' . $iMax . ' attempts tried.', CLogger::LEVEL_ERROR, 'user');
                return '';
            }
        }
        
        return $key;
    
    }
    
    private function generateKey(){
        
        $prefix='tn';
        $data=openssl_random_pseudo_bytes(16);

        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        //return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        //$dataString=vsprintf('%s-%s%s-%s', str_split(bin2hex($data), 4));
        $dataString=vsprintf('%s-%s-%s', str_split(bin2hex($data), 4));
        $key=$prefix . '-' . $dataString;
                
        return $key;
            
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
    
    public function sendPassRequestEmail($email, $guid, $username){

        $siteName=Yii::app()->name;
        $fullPageAddress=Yii::app()->createAbsoluteUrl('auth/user/passchange');
                
        $headers=AuthCommon::createMailHeader();
                
        $subject=AuthCommon::getTemplateValue('mail', 'restore_subject');
        $subject=sprintf($subject, $siteName);
        
        $text=AuthCommon::getTemplateValue('mail', 'restore_text');

        $restoreLink=$fullPageAddress."&guid=$guid";
        $text=sprintf($text, $siteName, $username, $restoreLink, $guid, $fullPageAddress);
        
        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
        $body=$text;
        
        return mail($email,$subject,$body,$headers);
    }
        
    public function sendActivationtEmail($email, $guid, $username){
        $siteName=Yii::app()->name;
        $fullPageAddress=Yii::app()->createAbsoluteUrl('auth/user/activation');
        
        $headers=AuthCommon::createMailHeader();
                
        $subject=AuthCommon::getTemplateValue('mail', 'activation_subject');
        $subject=sprintf($subject, $siteName);
        
        $text=AuthCommon::getTemplateValue('mail', 'activation_text');
        $restoreLink=$fullPageAddress."&guid=$guid";
        
        $text=sprintf($text, $siteName, $username, $restoreLink, $guid, $fullPageAddress);
        
        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
        $body=$text;
        
        $result=mail($email,$subject,$body,$headers);
        
        return $result;
     
    }
    
    function createMailHeader($fromName='', $fromEmail=''){
        
        if (empty($fromEmail)){
            $fromEmail=self::getParam('fromEmail');
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
    
    public function renderExtAccountWindowJS(){
        
        Yii::app()->clientScript->registerScript("openExtAccountWindow", "
            function openExtAccountWindow(url, serviceName=''){
                var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
                     screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
                     outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
                     outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
                     width    = outerWidth/2+50,
                     height   = outerHeight/2+50,
                     left     = parseInt(screenX + ((outerWidth - width) / 2), 10)+25,
                     top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10)+25;

                if (serviceName=='google'){
                    width=520;
                    height=720;
                }
                var  features = (
                        'width=' + width +
                        ',height=' + height +
                        ',left=' + left +
                        ',top=' + top

                      );

                newwindow=window.open(url,'extServiceLogin',features);

               if (window.focus) {
                   newwindow.focus();
               }
               return false;
            }
            ", CClientScript::POS_HEAD);
    }
    
    public function renderSocialLogin($accountName){
        
        $accountNameLower=strtolower($accountName);
        $loginUrl=Yii::app()->createUrl('auth/hybrid/login', array('service'=>$accountNameLower));
        $imageUrl='images/icons/' . $accountNameLower . '.png';
        
        $onClickJS='openExtAccountWindow("' . $loginUrl . '", "' . $accountName . '"); return false;';
        
        $imgHtml=Chtml::image($imageUrl, $accountName, array('class'=>'socialIcon'));
        
        //show icon with link
        echo CHtml::link($imgHtml, $loginUrl, array('onclick'=>$onClickJS));
    }    
    
    public function renderSocialConnect($accountName, $formRender, $isDisconnect=false){
            
        $accountNameLower=strtolower($accountName);
        
        if ($isDisconnect){
            $actionUrl=Yii::app()->createUrl('auth/hybrid/disconnect', array('service'=>$accountNameLower));
            $onChangeJS='window.location.href=\'' . $actionUrl . '\'; return false;';
        }else{
            $actionUrl=Yii::app()->createUrl('auth/hybrid/connect', array('service'=>$accountNameLower));
            $onChangeJS='openExtAccountWindow(\'' . $actionUrl . '\', \'' . $accountName . '\'); return false;';
        }
        
        $checkBoxParams=array('label'=>Yii::t('userProfile', $accountName), 'checked'=>$isDisconnect, 'onChange'=>$onChangeJS);
        $formRender->checkboxField($checkBoxParams);

    }    
    
    public function renderExternalLoginCloseJS($url){
        echo '
            <script>
            if (window.opener){
                window.opener.location.href="' . $url . '"
                window.close();
            }else {
            }
            </script>';
    }
    
}
