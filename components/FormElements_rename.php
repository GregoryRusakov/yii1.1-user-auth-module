    <?php 

/* This is an OpenSource Yii 1.1 authentification module
 * If you want to add or modify some code please open repository 
 * on GitHub. Any help will be appreciated.

 * Author: Gregory Rusakov
 * e-mail: greg@ontime.email
 * You can find module updates on GitHub:
 * http://gregoryrusakov.github.io/yii1.1-auth-module/
 */

Yii::import('application.modules.auth.*');

class FormElements_rename{

    public $relatedFieldNotSelected='<Не выбран>';
        
    public $fieldClass="col-sm-10";
    public $labelClass="col-sm-2";
    public $submitOffcet="col-sm-offset-2";
    public $form=null;
    
    private $context;
    private $model;
    
    function __construct($context, $model){
        $this->context=$context;
        $this->model=$model;
    }
        
    public function startForm($id='form', $ajaxValidation=false, $validateOnSubmit=true, $encodeData=false){
        
        CHtml::$beforeRequiredLabel = '<span class="requiredFormField">';
        CHtml::$afterRequiredLabel = '</span>';

        $htmlOptions=array('class'=>'form-horizontal');
        if ($encodeData){
            $htmlOptions['enctype']='multipart/form-data';
        }       
        
        echo '<div id="modalFormError" class="modal-form-error"></div>';
        
        echo '<div class="form">';
        $this->form=$this->context->beginWidget('CActiveForm', array(
            'id'=>$id,
            'enableAjaxValidation'=>$ajaxValidation,
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>$validateOnSubmit, 
                'validateOnChange'=>false,
            ),
            'htmlOptions'=>$htmlOptions,
        ));
        
    }

    public function endForm(){
        $this->context->endWidget();
        echo '</div>';
    }

    public function shortTextField($field, $label, $fieldPlaceHolder=''){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }
        echo '<div class="form-group ">';
            echo '<span class="control-label ' . $this->labelClass . '">' . $label . '</span>';
            echo '<div class="col-sm-4">';
            echo '<div class="shortFormField">' . $this->form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)) . '</div>'; 
            echo '<div class="shortFormError">' . $this->form->error($model, $fieldName) . '</div>'; 
            echo '</div>';
        echo '</div>';
    }

    public function fileField($field, $fieldPlaceHolder='', $groupClass='', $onChangeJS=''){
        $maxSizeKbJS=1*1024;
        $maxSizeBytesErrorMessageJS=sprintf(Yii::t('main', 'File size to big for load'), $maxSizeKbJS);
                
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }
        
        $logoImage=$model->$fieldName;
        if (empty($logoImage)){
            $imagePath='';
            $addImgClass='';
        } 
        else{
            $filePath=Files::getNewsImagesDir(true) . '/' . $logoImage;
            if (file_exists($filePath)){
                $imagePath=Files::getNewsImagesPath(false) . '/' . $logoImage;
                $addImgClass='margin-top-minus-20';
            }
            else{
                $imagePath='';
                $addImgClass='';
            }
                
        }
        
        $logoPicId='logo_pic_' . $fieldName;
        
        echo '<div class="form-group ' . $groupClass . '">';
            echo CHtml::image($imagePath, 'Лого', array('id'=>$logoPicId, 'class'=>'control-label col-sm-2 ' . $addImgClass));
            
            echo '<div class="' . $this->fieldClass . '">';
            
            echo '<div class="input-group">';
            echo '<span class="input-group-btn">
                        <span class="btn btn-default btn-file">' . Yii::t('main', 'Browse') . '&hellip; ';
            
            echo $this->form->fileField($model, $fieldName);
            
            echo '</span></span>';
            echo '<input type="text" value="' . $logoImage . '" class="form-control" readonly>';
            echo '</div>';
            
            echo $this->form->error($model, $fieldName);
            echo '</div>';
        echo '</div>';

        Yii::app()->clientScript->registerScript("fileSelect", "
            $(document).on('change', '.btn-file :file', function() {
                var input = $(this);
                var label = input.val();
                input.trigger('fileselect', label);
                readURL(this);
            });

            $(document).ready( function() {
                $('.btn-file :file').on('fileselect', function(event, label) {
                  var input = $(this).parents('.input-group').find(':text');
                  input.val(label);
                });
            });
            
            function readURL(input) {

                if (input.files && input.files[0]) {
                    if (input.files[0].size> " . $maxSizeKbJS*1024 . "){
                        alert('". $maxSizeBytesErrorMessageJS . "');
                    }
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#" . $logoPicId . "').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }

                $('#" . $logoPicId . "').css('margin-top','-20px');
            }     
        ");
                    
    }    
    
    public function textField($field, $fieldPlaceHolder='', $groupClass='', $useLabelEx=true){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
             $fieldName=$field;
        }
        echo '<div class="form-group ' . $groupClass . '">';
        if ($useLabelEx){
            echo $this->form->labelEx($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
        }
        else{
            echo $this->form->label($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
        }
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder)); 
            echo    $this->form->error($model, $fieldName);
            echo '</div>';
        echo '</div>';
    }
    
    public function checkboxField($field, $fieldPlaceHolder='', $groupClass=''){
        if (is_array($field)){
            $simpleField=true;
            $label=$field['label'];
            $checked=isset($field['checked']) ? $field['checked'] : '';
            $idTag=isset($field['id']) ? ' id="' . $field['id'] . '" ' : '';
            $onChange=isset($field['onChange']) ? $field['onChange'] : '';
            
        }
        else{
            //model
            $simpleField=false;
            $model=$this->model;
            $fieldName=$field;     
            $checked=($model->$fieldName==true);
        }
        
        $checkedString=$checked ? 'checked' : ''; 
        $onChangeTag=empty($onChange) ? '' : ' onchange="' . $onChange . '"';
        
        echo '<div class="form-group ' . $groupClass . '">';
        if ($simpleField){
            if (!empty($label)){
                echo '<label class="control-label col-sm-3">' . $label . '</label>';
            }
            echo '<div class="' . $this->fieldClass . '">';
            echo '<input ' . $checkedString . ' data-toggle="toggle" data-onstyle="success"' . $idTag . 'type="checkbox" ' . $onChangeTag . '>';
        }
        else{
            echo $this->form->label($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
            echo '<div class="' . $this->fieldClass . '">';
            echo $this->form->checkBox($model, $fieldName,  array('checked'=>$checkedString, 'data-toggle'=>'toggle', 'data-onstyle'=>'success' ));
        }
        echo '</div>';
        echo '</div>';    
        
    }
    
    public function termsField($field, $termsUrl, $label='', $groupClass=''){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }        

        echo '<div class="form-group ' . $groupClass . '">';
        
        if (empty($label)){
            $invisibleClass="hided";
        }
        else{
            $invisibleClass="";
        }
        
        echo $this->form->label($model,$fieldName, array('class'=>'control-label ' . $invisibleClass . ' ' . $this->labelClass));
        echo '<div class="' . $this->fieldClass . '">';
        echo $this->form->checkBox($model, $fieldName);
        
        echo ' С ' . CHtml::link('пользовательским соглашением', $termsUrl, array('target'=>'_terms')) . ' согласен';
        
        echo '</div>';
        echo '</div>';                    

    }
    
    public function textareaField($field, $fieldPlaceHolder='', $groupClass=''){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }
        echo '<div class="form-group ' . $groupClass . '">';
            echo $this->form->labelEx($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->textArea($model, $fieldName, array('class'=>'form-control')); 
            echo    $this->form->error($model, $fieldName);
            echo '</div>';
        echo '</div>';
    }    
    
    public function textFieldDisabled($field, $fieldPlaceHolder=''){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }        
        echo '<div class="form-group">';
            echo $this->form->label($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->textField($model, $fieldName, array('class'=>'form-control', 'placeholder'=>$fieldPlaceHolder,'disabled'=>'true')); 
            echo '</div>';
        echo '</div>';
    }    
        
    public function passwordField($field, $fieldPlaceHolder=''){
        if (is_array($field)){
            $model=$field['model'];
            $fieldName=$field['field'];
        }
        else{
            $model=$this->model;
            $fieldName=$field;
        }        
        echo '<div class="form-group">';
            echo $this->form->labelEx($model,$fieldName, array('class'=>'control-label ' . $this->labelClass));
            echo '<div class="' . $this->fieldClass . '">';
            echo    $this->form->passwordField($model, $fieldName, array('class'=>'form-control', 'onfocus'=>'this.value=""', 'placeholder'=>$fieldPlaceHolder)); 
            echo    $this->form->error($model, $fieldName);
            echo '</div>';
        echo '</div>';
    }    
    
    public function submitButton($text, $confirmQuestion=''){
        echo '<div class="form-group">';
            echo '<div class="' . $this->submitOffcet . ' '. $this->fieldClass . '">';
            if (empty($confirmQuestion)){
                echo CHtml::submitButton($text, array('class' => 'btn btn-primary')); 
            }else{
                
                Yii::app()->clientScript->registerScript("submitButtonQuestion", '
                        var confirmResult=null;
                        function submitQuestion(currButton, text){
                            if (confirmResult!=null){
                                return confirmResult;
                            }
                            confirmResult = confirm(text);
                            return confirmResult;
                        }
                ', CClientScript::POS_HEAD);
                echo CHtml::submitButton($text, array('class' => 'btn btn-primary', 'onclick'=>'return submitQuestion($(this), "' . $confirmQuestion . '");')); 
            }
            echo '</div>';
	echo '</div>';
    }
    
    public function submitAndCancelButtons($textSubmit, $confirmQuestion='', $textCancel=''){
        echo '<div class="form-group">';
            echo '<div class="' . $this->submitOffcet . ' '. $this->fieldClass . '">';
            if (empty($confirmQuestion)){
                echo CHtml::submitButton($textSubmit, array('class' => 'btn btn-primary')); 
            }else{
                
                Yii::app()->clientScript->registerScript("submitButtonQuestion", '
                        var confirmResult=null;
                        function submitQuestion(currButton, text){
                            if (confirmResult!=null){
                                return confirmResult;
                            }
                            confirmResult = confirm(text);
                            return confirmResult;
                        }
                ', CClientScript::POS_HEAD);
                echo CHtml::submitButton($text, array('class' => 'btn btn-primary', 'onclick'=>'return submitQuestion($(this), "' . $confirmQuestion . '");')); 
            }
            
            echo '<span class="margin-left-mid"></span>';
            if (empty($textCancel)){
                $textCancel=Yii::t('AdminModule.view','Cancel');
            }
            echo CHtml::htmlButton($textCancel, array('class'=>'btn btn-default', 'onclick' => 'history.go(-1)'));
            
            echo '</div>';
	echo '</div>';
    }
    
    public function capthaField($fieldName){
         
        if(CCaptcha::checkRequirements() && Yii::app()->user->isGuest){
             echo '<div class="form-group" id="captcha">';
             echo CHtml::activeLabel($this->model, $fieldName, array('class'=>'control-label ' . $this->labelClass));
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

    public function checkBox($field, $addClass='', $label=''){
        echo '<div class="form-group'    . ' ' . $addClass . '">';
            echo '<div class="col-sm-offset-2 '. $this->fieldClass . '">';
                echo $this->form->checkBox($this->model,$field);
                if (empty($label)){
                    echo ' ' . $this->form->label($this->model,$field);
                }
                else{
                    echo ' ' . $label;
                }
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
            <label class="control-label ' . $this->labelClass . ' ajax-form-label"></label>
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
        echo  CHtml::htmlButton(Yii::t('main','Cancel'), array('class'=>'btn btn-default', 'data-dismiss'=>'modal'));
        echo '</div></div>';
    }
 
    public function selectFieldButton($fieldId, $elementLabel, $targetAction, $targetController){
        
        $this->textField($fieldId, '', 'nojs-show');
        $elementLabelUpper=mb_convert_case($elementLabel, MB_CASE_TITLE, "UTF-8");
        $emptyFiledText=$this->relatedFieldNotSelected;
                
        $targetControllerFull=$targetAction. '/' . $targetController;
        $relFieldText=Helpers::getPresentationByField($fieldId, $this->model);
        if (empty($relFieldText)){
            $relFieldText=$emptyFiledText;
        }
                
        if (empty($relFieldText)){
            $relFieldText=$emptyFiledText;
        }

        $modelClassName=get_class($this->model);
        $formFieldId=$modelClassName . '_' . $fieldId;
        $formFieldButtonId=$fieldId . 'Button';

        //prepare modal form for opening
        Helpers::renderModalFormFrame('modalCard', array('size'=>'modal-lg', 'header'=>'Создать элемент'));
        Helpers::renderModalFormFrame('modalSelect', array('size'=>'modal-wide', 'header'=>'Выбрать элемент'));

        //create button and complementary elements
        
        echo '<div class="form-group nojs-hide">';
        echo '<label class="control-label col-sm-2" for="'.$formFieldId.'"><span class="requiredFormField">' . $elementLabelUpper . '</span></label>
        <div class="col-sm-10">
            <div class="btn-group">';
                echo '<input class="btn btn-default" id="'. $formFieldButtonId . '" name="yt0" type="button" value="' . $relFieldText . '" />';
                echo CHtml::htmlButton('<span class="caret"></span><span class="sr-only"></span>', array(
                        'class'=>'btn btn-default dropdown-toggle',
                        'data-toggle'=>'dropdown',
                        'aria-expanded'=>'false'
                        ));

                echo '<ul class="dropdown-menu my-dropdown-menu" role="menu">';

                    echo 
                     '<li>' . JSHelpers::renderAjaxModalLink(Yii::t('AdminModule.main', 'Select element'), $targetController . '/select', 'modalSelect', 'Выбрать элемент (' . $elementLabelUpper . ')').'</li>'
                    .'<li>' . JSHelpers::renderAjaxModalLink(Yii::t('AdminModule.main', 'Create element'), $targetController . '/create', 'modalCard', 'Создать элемент (' . $elementLabelUpper . ')').'</li>'
                    .'<li>' . CHtml::link(Yii::t('AdminModule.main', 'Clear element'), 
                                                '#', 
                                                array(
                                                    'onclick'=>''
                                                    . '$("#'.$formFieldButtonId.'").val("'.$emptyFiledText.'");'
                                                    . '$("#'.$formFieldId.'").val("");')
                                                ) . '</li>';
                echo '</ul>
            </div>
        </div>
        </div>';
                
        //add model window event listeners
        Yii::app()->clientScript->registerScript("ElementsListeners", '
        
        document.addEventListener("ElementCreated", function (e) {
                id=e.detail.id;
                name=e.detail.name;

                $("#' . $formFieldId . '").val(id);
                $("#' . $formFieldButtonId . '").val(name);    
                $("#modalCard").modal("hide");
                }, false);

        document.addEventListener("ElementUpdated", function (e) {
                id=e.detail.id;
                name=e.detail.name;

                $("#' . $formFieldId . '").val(id);
                $("#' . $formFieldButtonId . '").val(name);    
                $("#modalCard").modal("hide");
                }, false);
        ');
        
        //add listeners to filed button
        Yii::app()->clientScript->registerScript("openScripts", "
        
        jQuery('body').on('click','#" . $formFieldButtonId . "',
            function(){
                elementId=$('#" . $formFieldId . "').val();
                if(String(elementId)==''){
                    //select element
                    jQuery.ajax({
                        'url':'/trader-news.ru/index.php?r=" . $targetControllerFull . "/select',
                        'success':" . JSHelpers::renderOpenForm('modalSelect', 'Выбрать ' . $elementLabel) . ",
                    'cache':false});
                    return false;
                }
                
                //just open element by id
                jQuery.ajax({'url':'/trader-news.ru/index.php?r=" . $targetControllerFull . "/update&id='+elementId,
                    'success':function(r){
                        $('#modalCardBody').html(r);
                        $('#modalCard').modal('show'); 
                        return false;},
                    'cache':false});
                return false;
                
            });
            
        ");    
    }
    
    public function selectFieldClick($targetFieldId, $targetModelClassName){
        
        $formTargetId=$targetModelClassName . '_' . $targetFieldId;
        $formFieldButtonId=$targetFieldId . 'Button';
        
        Yii::app()->clientScript->registerScript("selectOnClick_" . $targetFieldId, '
        function selectOnClick(e){ 
        
            e.preventDefault(); 
            var url = $(this).attr("href");
            id=getParameterByName(url, "id");
            selected=getParameterByName(url, "selected");
            $("#' . $formTargetId . '").val(id);
            $("#' . $formFieldButtonId . '").val(selected);
            $("#modalSelect").modal("hide");
            return true;

        }');
    }
    
}