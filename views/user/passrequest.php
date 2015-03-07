   
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Password restore. Title');

    $form=FormElements::startForm();
    FormElements::showErrors($form, $model);
    FormElements::textField($form, $model, 'email',Yii::t('AuthModule.forms', 'Password restore. Email placeholder'));
    FormElements::capthaField($model, 'verifyCode');
    FormElements::submitButton(Yii::t('AuthModule.forms', 'Password restore. Submit button'));
    FormElements::endForm($this);
    
?>
