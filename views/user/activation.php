
<?php
    $this->pageTitle=Yii::t('AuthModule.forms', 'Activation form. Title');
    $this->h1=Yii::t('AuthModule.forms', 'Activation form. H1');
    
    $form=FormElements::startForm();
    FormElements::showErrors($form, $model);
    FormElements::textField($form, $model, 'guid', Yii::t('AuthModule.forms', 'Activation form. Guid placeholder'));
    FormElements::submitButton(Yii::t('AuthModule.forms', 'Activation form. Submit button'));
    FormElements::endForm($this);

?>
