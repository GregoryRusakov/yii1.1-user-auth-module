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

class FormElements_rename{
    
    public $fieldClass="col-sm-10";
    public $form=null;

    private $context;
    private $model;
    
    function __construct($context, $model){
        $this->context=$context;
        $this->model=$model;
    }
        
    public function startForm($id='form', $ajaxValidation=false){
        
        
        echo '<div id="modalFormError" class="modal-form-error"></div>';
        
        echo '<div class="form">';
        $this->form=$this->context->beginWidget('CActiveForm', array(
            'id'=>$id,
            'enableAjaxValidation'=>$ajaxValidation,
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true, 
                'validateOnChange'=>false,
            ),
            'htmlOptions'=>array(
                'class'=>'form-horizontal',
            ),
        ));
        
    }

    public function endForm(){
        $this->context->endWidget();
        echo '</div>';
    }

    public function textField($fieldName, $fieldPlaceHolder='', $groupClass=''){
        echo '<div class="form-group ' . $groupClass . '">';
            echo $this->form->label($this->model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->textField($this->model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo    $this->form->error($this->model, $fieldName);
            echo '</div>';
        echo '</div>';
    }

    public function hiddenField($fieldName, $fieldPlaceHolder='', $groupClass=''){
        echo $this->form->hiddenField($this->model, $fieldName); 

    }

    public function textFieldDisabled($fieldName, $fieldPlaceHolder=''){
        echo '<div class="form-group">';
            echo $this->form->label($this->model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->textField($this->model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder,'disabled'=>'true')); 
            echo '</div>';
        echo '</div>';
    }    
        
    public function passwordField($fieldName, $fieldPlaceHolder=''){
        echo '<div class="form-group">';
            echo $this->form->label($this->model,$fieldName, array('class'=>'control-label col-sm-2'));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->passwordField($this->model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo    $this->form->error($this->model, $fieldName);
            echo '</div>';
        echo '</div>';
    }    
    
    public function submitButton($text){
        echo '<div class="form-group">';
            echo '<div class="col-sm-offset-2 '. $this->fieldClass . '">';
            echo CHtml::submitButton($text, array('class' => 'btn btn-primary',)); 
            echo '</div>';
	echo '</div>';
    }
    
    public function capthaField($fieldName){
         
        if(CCaptcha::checkRequirements() && Yii::app()->user->isGuest){
             echo '<div class="form-group" id="captcha">';
             echo CHtml::activeLabel($this->model, $fieldName, array('class'=>'control-label col-sm-2'));
             $this->form->widget('CCaptcha', array(
                        'clickableImage'=>true, 
                        'showRefreshButton'=>true, 
                        'buttonLabel' => '<span class="glyphicon glyphicon-refresh"></span>',
                        )
             );
             echo CHtml::activeTextField($this->model, $fieldName, array('class'=>'captha-field', 'placeholder'=>Yii::t('AuthModule.forms','Enter captcha placeholder')));
             echo $this->form->error($this->model, $fieldName, array('class'=>'captchaErrorMessage'));
             echo '</div>';
        }
    }

    public function checkBox($field){
        echo '<div class="form-group">';
            echo '<div class="col-sm-offset-2 '. $this->fieldClass . '">';
                echo $this->form->checkBox($this->model,$field);
                echo ' '.$this->form->label($this->model,$field);
            echo '</div>';        
        echo '</div>';        
    }
    
    public function showErrors($textHeader=''){
        $errArray=$this->model->getErrors();
        if (count($errArray)==0){
            return;
        }
        
        echo '<div class="alert alert-warning">';
        if (empty($textHeader)){
            echo $this->form->errorSummary($this->model);
        }else{
            echo $this->form->errorSummary($this->model, $textHeader);
        }
        echo '</div>';
    }
    
    public function ajaxSubmitPanel($buttonLabel, $url, $messageFormId='ajaxFormMessage'){
        echo '<div class="form-group">
            <label class="control-label col-sm-2 ajax-form-label"></label>
            <div class="col-sm-10">';
        
        echo "<input class='btn btn-primary' name='btSubmit' type='submit' value='".$buttonLabel."' id='btSubmit' />
            <script type='text/javascript'>
            jQuery('body').off('click','#btSubmit'); //clear listeners
            jQuery('body').on('click','#btSubmit',
                        function(e){
                            var ajaxData=$('#".$this->form->id."').serialize();
                                ajaxData=ajaxData + '&ajax=".$this->form->id."'; //for ajax validators
                            jQuery.ajax({'type':'POST',
                                'success':function(data){
                                    var response=$.parseJSON(data);
                                    //alert(data);
                                    $('#ajaxFormMessage').text(response.message);
                                    if (response.status==='success'){
                                        if (response.event==='LoggedIn'){
                                            window.location.reload();
                                            return;
                                        }
                                        var event = new CustomEvent(
                                            response.event, 
                                            {detail: {
                                                id: response.id, 
                                                name: response.name,
                                                message: response.message,
                                                }
                                            });
                                        document.dispatchEvent(event);
                                    }
                                    else if (response.status==='error'){
                                        message=response.message;
                                        $('#modalFormError').html(message);
                                        $('#modalFormError').show();
                                    }
                                    else{
                                        //standard ajax validation response
                                        erId='_em_';
                                        if (data.indexOf('{')==0) {
                                            $('div.errorMessage').hide(); //hide old errors
                                            //show errors
                                            jQuery.each(response, function(key, value) { 
                                                    message=value.toString();
                                                    message=message.replace('\.,', '.<br>');
                                                    //message=value[0];
                                                    jQuery('#'+key+erId).show().html(message); 
                                                }
                                            );
                                        }
                                    }
                                    //show captha if needed
                                    if (response.hasOwnProperty('captcha')){
                                        $('#captcha').show();
                                    }

                                },
                                'url':'".$url."',
                                'cache':false,
                                'data':ajaxData,
                            });
                            return false;
                        });

            </script>";
  
        echo '<span class="margin-left-mini"></span>';
        echo  CHtml::htmlButton('Cancel', array('class'=>'btn btn-default', 'data-dismiss'=>'modal'));
        echo '</div></div>';
    }
}