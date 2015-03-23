<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

    <?php 
    $isNewRecord=($model->getScenario()=='insert');
    if ($isNewRecord){
        $this->pageTitle=Yii::t('AuthModule.forms', 'Registration form. Title');
        $this->h1=Yii::t('AuthModule.forms', 'Registration form. H1');
    }
    else{
        $this->pageTitle=Yii::t('AuthModule.forms', 'Update form. Title');
        $this->h1=Yii::t('AuthModule.forms', 'Update form. H1');
    }
    ?>

    <?php 
        $form=FormElements::startForm();
        FormElements::showErrors($form, $model);
        if (!$isNewRecord){
            FormElements::textFieldDisabled($form, $model, 'username');
        }
        else{
            FormElements::textField($form, $model, 'username', Yii::t('AuthModule.forms', 'Registration form. Username placeholder'));
        }
        FormElements::textField($form, $model, 'email', Yii::t('AuthModule.forms', 'User form. Email placeholder'));
        FormElements::textField($form, $model, 'full_name', Yii::t('AuthModule.forms', 'User form. Fullname placeholder'));
        FormElements::passwordField($form, $model, 'password_entered', Yii::t('AuthModule.forms', 'User form. Password placeholder'));
        FormElements::capthaField($form, $model, 'verifyCode');
        if ($isNewRecord){
            FormElements::submitButton(Yii::t('AuthModule.forms', 'Registration form. Submit button'));
        }
        else{
            FormElements::submitButton(Yii::t('AuthModule.forms', 'Update form. Submit button'));
        }
        FormElements::endForm($this);
    ?>

