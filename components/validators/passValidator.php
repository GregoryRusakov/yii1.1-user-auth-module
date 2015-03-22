<?php

class passValidator extends CValidator
{
    protected function validateAttribute($object,$attribute){
        
        $passStrength=Yii::app()->params['passStrength'];
        if ($passStrength==null){
            $passStrength=0;
        }
        
        if ($passStrength==0){
            $pattern = '/^(?=.*[a-zA-Z0-9]).{4,}$/';  
            $message=Yii::t('AuthModule.forms', 'Password is not strong enough (weak)');
        }
        elseif ($passStrength==1){
            $pattern = '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/';  
            $message=Yii::t('AuthModule.forms', 'Password is not strong enough (strong)');
        }
        
        //compare data
        
        if(!preg_match($pattern, $object->$attribute)){
          $object->addError($attribute, $message);
        }
    }
}