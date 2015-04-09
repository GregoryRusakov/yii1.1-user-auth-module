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

    echo "<br>".CHtml::link(Yii::t('AuthModule.forms', 'Login. Restore password'),array('user/passrequest'));

    echo "<br><br>";

    echo "<div>".CHtml::link(Yii::t('AuthModule.forms', 'Login. Register user'), array('user/registration'))."</div>";

