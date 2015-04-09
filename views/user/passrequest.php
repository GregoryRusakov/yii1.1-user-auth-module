   
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Password restore. Title');

    $formRender=new FormElements($this, $model);
    $formRender->startForm();
    $formRender->showErrors();
    $formRender->textField('email',Yii::t('AuthModule.forms', 'Password restore. Email placeholder'));
    $formRender->capthaField('verifyCode');
    $formRender->submitButton(Yii::t('AuthModule.forms', 'Password restore. Submit button'));
    $formRender->endForm();
    
?>
