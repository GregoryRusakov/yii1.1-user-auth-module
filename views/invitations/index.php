<?php
    $this->pageTitle="Введите приглашение";
?>
<p>
В данное время регистрация новых пользователей производится по приглашениям (инвайтам).
</p>
<p>Если у вас нет приглашения, то его можно запросить, написав письмо на адрес:
    <?php 
        try{
            $email=Helpers::getAppParam('adminEmail');
        } catch (Exception $ex) {
            $email=AuthCommon::getParam('adminEmail');
        }
        echo CHtml::mailto($email, $email); 
    ?>
</p>

<div class="margin-bottom-30"></div>
<div class="row">
    <div class="table-responsive col-md-5">
    <?php
        $formRender=new FormElements($this, $model);

        $formRender->fieldClass="col-sm-8";
        $formRender->labelClass="col-sm-3";
        $formRender->submitOffcet="col-sm-offset-3";

        $formRender->startForm();
        $formRender->showErrors();
        $formRender->textField('guid','','',false);
        
        if ($model->scenario=='withCaptcha'){
            $formRender->capthaField('verifyCode');
        }
        
        $formRender->submitButton(Yii::t('AuthModule.forms', 'RegisterMe'));

        $formRender->endForm();

    ?>
    </div>

</div>

