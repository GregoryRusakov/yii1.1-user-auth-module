
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Password change. Title');
    
    $formRender=new FormElements($this, $model);
    $formRender->startForm();
    $formRender->showErrors();
    $formRender->passwordField('password', Yii::t('AuthModule.forms', 'Password change. Password placeholder'));
    $formRender->textField('guid', Yii::t('AuthModule.forms', 'Password change. Guid placeholder'));
    $formRender->capthaField('verifyCode');
    $formRender->submitButton(Yii::t('AuthModule.forms', 'Password change. Submit button'));
    $formRender->endForm();

?>
