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

<a href="#" onclick="js:myWindow('http://localhost/trader-news.ru/index.php?r=auth/user/login&service=facebook');" title="Показать">Facebook</a>
<br><br>
<script>
function myWindow(url){
    var params = "width=500, height=500, menubar=no,location=yes,resizable=yes,scrollbars=yes,status=yes"
    window.open(url, "Authorize", params)
}    
</script>
    
<a href="#" onclick="js:myWindow('http://localhost/trader-news.ru/index.php?r=auth/user/login&service=vkontakte');" title="Показать">VK</a>
<br><br>

    

<?php
    
    
    echo '<span class="col-sm-2"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Restore password'),array('user/passrequest'));
    echo '<span class="margin-right-mid"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Register user'), array('user/registration'));
    
    echo '<br><br><hr><span class="col-sm-2">Вы можете войти с помощью ваших учетных записей:</span>';
    echo '<span class="col-sm-10">';

    echo '</span>';
