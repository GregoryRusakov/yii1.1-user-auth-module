<?php
   
    $this->pageTitle=Yii::t('AuthModule.forms', 'Login. Title');

    $isAjax=Yii::app()->request->isAjaxRequest;
    
    if (!isset($model)){
        $model=new LoginForm;
    }

    if (isset($username)){
        $model->username=$username;
    }    
    
    $formRender=new FormElements($this, $model);
    $formRender->startForm('user-login', true);
    
    $formRender->showErrors(Yii::t('AuthModule.forms', 'Login. Login failure'));
    $formRender->textField('username', Yii::t('AuthModule.forms', 'Login. Username placeholder'));
    $formRender->passwordField('password', Yii::t('AuthModule.forms', 'Login. Password placeholder'));
    $formRender->checkBox('rememberMe', Yii::t('AuthModule.forms', 'Login. Remember me checkbox'));
    $formRender->capthaField('verifyCode');
    
    if ($model->scenario!='withCaptcha'){
        Yii::app()->clientScript->registerScript("captcha", "
                $('#captcha').hide();
            ");
    }
    
    $buttonLabel=Yii::t('AuthModule.forms', 'Login. Submit button');
    
    if (!$isAjax){
        $formRender->submitButton($buttonLabel);
    }
    else{
        $url=$this->createUrl('login');
        $formRender->ajaxSubmitPanel($buttonLabel, $url);
    }
    
    $formRender->endForm();

    ?>
   

<?php 
    $loginUrl=Yii::app()->createUrl('auth/hybrid/login', array('service'=>'facebook'));
?>
   
<?php
    echo '<span class="col-sm-2"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Restore password'),array('user/passrequest'));
    echo '<span class="margin-right-mid"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Register user'), array('user/registration'));
?>

<hr>
  
<div class="nojs-hide">
<?php 

    if ($isAjax){
        $socialLoginLabel='';
    }else{
        $socialLoginLabel=Yii::t('AuthModule.forms', 'Social login');
    }
    echo '<span class="col-sm-2 align-right">'.$socialLoginLabel.'</span>';
    
    Common::renderSocialLogin('facebook', true);

    echo '<span class="margin-right-mid"></span>';
    Common::renderSocialLogin('google');

    echo '<span class="margin-right-mid"></span>';
    Common::renderSocialLogin('vkontakte');
    
?>
</div>
<div class="nojs-show">
    <p><?php echo Yii::t('AuthModule.forms', 'Unable to login through social networks without JavaScript');?></p>
</div>
    
</p>

