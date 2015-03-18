<?php 

/*
 * 
 * 
 *************************************************************************
 * 
 *  Note please: Rename this module file to FormElements.php before use
 * 
 *************************************************************************
 * 
 * 
 */
 


/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */


class FormElements{
    
    public function startForm($id='form', $ajaxValidation=false){

        echo '<div class="form">';
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>$id,
            'enableAjaxValidation'=>$ajaxValidation,
            'htmlOptions'=>array(
                'class'=>'form-horizontal',
              ),
    
            'clientOptions'=>array(
                'validateOnSubmit'=>false,       
            ),  

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

    public function textField($form, $model, $fieldName, $fieldPlaceHolder='', $groupClass=''){
        echo '<div class="form-group ' . $groupClass . '">';
            echo $form->label($model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="col-sm-10">';
            echo    $form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo '</div>';
        echo '</div>';
    }

    public function hiddenField($form, $model, $fieldName, $fieldPlaceHolder='', $groupClass=''){
        echo $form->hiddenField($model, $fieldName); 

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
    
      public function ajaxSubmitPanel($form, $buttonLabel, $url, $messageFormId='ajaxFormMessage'){
        echo '<div class="form-group">
            <label class="control-label col-sm-2 ajax-form-label"></label>
            <div class="col-sm-10">';
        
        echo "<input class='btn btn-primary' name='btSubmit' type='submit' value='".$buttonLabel."' id='btSubmit' />
            <script type='text/javascript'>
            jQuery('body').on('click','#btSubmit',function(){jQuery.ajax({'type':'POST','success':function(data){
                                var response=$.parseJSON(data);
                                $('#ajaxFormMessage').text(response.message);
                                if (response.status==='success'){
                                    if (response.action==='reload'){
                                        window.location.reload();
                                        return;
                                    }
                                    var event = new CustomEvent(response.action, {detail: {id: response.id, username: response.username}});
                                    document.dispatchEvent(event);
                                }else{
                                    alert('Error: '+response.id);
                                }
                            },'url':'".$url."',
                            'cache':false,
                            'data':jQuery('#".$form->id."').serialize()
                            });
                            return false;});

            </script>";
  
        echo '<span class="margin-left-mini"></span>';
        echo  CHtml::htmlButton('Cancel', array('class'=>'btn btn-default', 'data-dismiss'=>'modal'));
        echo '</div></div>';
    }
        
    
}
