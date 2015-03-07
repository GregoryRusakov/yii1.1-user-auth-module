<?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

class FormElements{
    
    public function startForm(){
        echo '<div class="form">';
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'users-change-form',
            'htmlOptions'=>array(
                      'class'=>'form-horizontal',
                    )
            // Please note: When you enable ajax validation, make sure the corresponding
            // controller action is handling ajax validation correctly.
            // See class documentation of CActiveForm for details on this,
            // you need to use the performAjaxValidation()-method described there.
            //'enableAjaxValidation'=>false,
            ));
        
        return $form;
    }

    public function endForm($context){
        $context->endWidget();
        echo '</div>';
    }

    public function textField($form, $model, $fieldName, $fieldPlaceHolder=''){
        echo '<div class="form-group">';
            echo $form->label($model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="col-sm-10">';
            echo    $form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo '</div>';
        echo '</div>';
    }
    
    public function textFieldDisabled($form, $model, $fieldName, $fieldPlaceHolder=''){
        echo '<div class="form-group">';
            echo $form->label($model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="col-sm-10">';
            echo    $form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder,'disabled'=>'true')); 
            echo '</div>';
        echo '</div>';
    }    
        
    public function passwordField($form, $model, $fieldName, $fieldPlaceHolder=''){
        echo '<div class="form-group">';
            echo $form->label($model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="col-sm-10">';
            echo    $form->passwordField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo '</div>';
        echo '</div>';
    }    
    
    public function submitButton($text){
        echo '<div class="form-group">';
            echo '<div class="col-sm-offset-2 col-sm-10">';
            echo CHtml::submitButton($text, array('class' => 'btn btn-primary',)); 
            echo '</div>';
	echo '</div>';
    }
    
    public function capthaField($model, $fieldName){
         
        if(CCaptcha::checkRequirements() && Yii::app()->user->isGuest){
             echo '<div class="form-group">';
             echo CHtml::activeLabelEx($model, $fieldName, array('class'=>'control-label col-sm-2'));
             $this->widget('CCaptcha', array(
                        'clickableImage'=>true, 
                        'showRefreshButton'=>true, 
                        //'buttonLabel' => CHtml::image(Yii::app()->baseUrl.'/css/images/captcha_refresh.png'),
                        'buttonLabel' => '<span class="glyphicon glyphicon-refresh"></span>',
                        )
             );
             echo CHtml::activeTextField($model, $fieldName, array('class'=>'captha-field', 'placeholder'=>Yii::t('AuthModule.forms','Enter captcha placeholder')));
             echo '</div>';
        }
    }

    public function checkBox($form, $model, $field){
        echo '<div class="form-group">';
            echo '<div class="col-sm-offset-2 col-sm-10">';
                echo $form->checkBox($model,$field);
                echo ' '.$form->label($model,$field);
            echo '</div>';        
        echo '</div>';        
    }
    
    public function showErrors($form, $model, $textHeader=''){
        $errArray=$model->getErrors();
        if (count($errArray)==0){
            return;
        }
        
        echo '<div class="alert alert-warning">';
        if (empty($textHeader)){
            echo $form->errorSummary($model);
        }else{
            echo $form->errorSummary($model, $textHeader);
        }
        echo '</div>';
    }
}