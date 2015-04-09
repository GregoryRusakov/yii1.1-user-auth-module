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
        $formRender=new FormElements($this, $model);
        $formRender->startForm();
        $formRender->showErrors($model);
        if (!$isNewRecord){
            $formRender->textFieldDisabled('username');
        }
        else{
            $formRender->textField('username', Yii::t('AuthModule.forms', 'Registration form. Username placeholder'));
        }
        $formRender->textField('email', Yii::t('AuthModule.forms', 'User form. Email placeholder'));
        $formRender->textField('full_name', Yii::t('AuthModule.forms', 'User form. Fullname placeholder'));
        $formRender->passwordField('password_entered', Yii::t('AuthModule.forms', 'User form. Password placeholder'));
        $formRender->capthaField('verifyCode');
        if ($isNewRecord){
            $formRender->submitButton(Yii::t('AuthModule.forms', 'Registration form. Submit button'));
        }
        else{
            $formRender->submitButton(Yii::t('AuthModule.forms', 'Update form. Submit button'));
        }
        $formRender->endForm();
    ?>

