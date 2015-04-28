<?php
    $this->pageTitle="Введите приглашение";
?>

<div class="row">
    <div class="table-responsive col-md-5">
    <?php
        $formRender=new FormElements($this, $model);

        $formRender->fieldClass="col-sm-8";
        $formRender->labelClass="col-sm-3";
        $formRender->submitOffcet="col-sm-offset-3";

        $formRender->startForm();
        $formRender->showErrors();
        $formRender->textField('guid');

        $formRender->submitButton(Yii::t('AuthModule.forms', 'RegisterMe'));

        $formRender->endForm();

    ?>


    </div>

</div>

<div>
    
    <p>Если у вас нет приглашения, то его можно запросить, написав письмо на адрес:
    <?php 
        $email=Common::getParam('adminEmail');
        echo CHtml::mailto($email, $email); 
    ?>
</p>
    
</div>