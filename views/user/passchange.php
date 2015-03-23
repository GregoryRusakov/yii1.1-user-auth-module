
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Password change. Title');
    
    $form=FormElements::startForm();
    FormElements::showErrors($form, $model);
    FormElements::passwordField($form, $model, 'password', Yii::t('AuthModule.forms', 'Password change. Password placeholder'));
    FormElements::textField($form, $model, 'guid', Yii::t('AuthModule.forms', 'Password change. Guid placeholder'));
    FormElements::capthaField($form, $model, 'verifyCode');
    FormElements::submitButton(Yii::t('AuthModule.forms', 'Password change. Submit button'));
    FormElements::endForm($this);

?>
