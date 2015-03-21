<?php
   
    $this->pageTitle=Yii::t('AuthModule.forms', 'Login. Title');

    $isAjax=Yii::app()->request->isAjaxRequest;
    
    if (!isset($model)){
        $model=new LoginForm;
    }

    if (isset($username)){
        $model->username=$username;
    }    
    
    $form=FormElements::startForm('user-login', true);
    
    FormElements::showErrors($form, $model, Yii::t('AuthModule.forms', 'Login. Login failure'));
    FormElements::textField($form, $model, 'username', Yii::t('AuthModule.forms', 'Login. Username placeholder'));
    FormElements::passwordField($form, $model, 'password', Yii::t('AuthModule.forms', 'Login. Password placeholder'));
    FormElements::checkBox($form, $model, 'rememberMe', Yii::t('AuthModule.forms', 'Login. Remember me checkbox'));
    
    $buttonLabel=Yii::t('AuthModule.forms', 'Login. Submit button');
    
    if (!$isAjax){
        FormElements::submitButton($buttonLabel);
    }
    else{
        $url=$this->createUrl('login');
        FormElements::ajaxSubmitPanel($form, $buttonLabel, $url);
    }
    
    FormElements::endForm($this);

    echo "<br>".CHtml::link(Yii::t('AuthModule.forms', 'Login. Restore password'),array('user/passrequest'));

    echo "<br><br>";

    echo "<div>".CHtml::link(Yii::t('AuthModule.forms', 'Login. Register user'), array('user/registration'))."</div>";

