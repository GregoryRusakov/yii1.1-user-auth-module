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

<script type="text/javascript">
        <?php if ($loginUrl) { ?>
        var newwindow;
        var intId;
        function login(){
            var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
                 screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
                 outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
                 outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
                 width    = 640,
                 height   = 480,
                 left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
                 top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
                 features = (
                    'width=' + width +
                    ',height=' + height +
                    ',left=' + left +
                    ',top=' + top

                  );
 
            newwindow=window.open('<?=$loginUrl?>','Login_by_facebook',features);
            //newwindow=window.open('<?=$loginUrl?>','Login_by_facebook');
 
           if (window.focus) {newwindow.focus()}
          return false;
        }
 
        <?php } ?>
 </script>       
        
 
   <a href="#" onclick="login();return false;">
FACEBOOK
     </a>
 


<p>
    old<br>
<?php 
    echo CHtml::link('Facebook', array('hybrid/login', 'service'=>'facebook'));
    echo '<br><br>';
    echo CHtml::link('VKontakte', array('hybrid/login', 'service'=>'vkontakte'));
?>
</p>

<?php
    echo '<span class="col-sm-2"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Restore password'),array('user/passrequest'));
    echo '<span class="margin-right-mid"></span>';
    echo CHtml::link(Yii::t('AuthModule.forms', 'Login. Register user'), array('user/registration'));
    
