
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Activation form. Title');
    $this->h1=Yii::t('AuthModule.forms', 'Activation form. H1');
    
    $formRender=new FormElements($this, $model);
    $formRender->startForm();
    $formRender->showErrors();
    $formRender->textField('guid', Yii::t('AuthModule.forms', 'Activation form. Guid placeholder'));
    $formRender->submitButton(Yii::t('AuthModule.forms', 'Activation form. Submit button'));
    $formRender->endForm();

?>
