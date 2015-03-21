<?php

class uniqueUsername extends CValidator
{
    protected function validateAttribute($object,$attribute){
        
        if (empty($object->usrname)){
            return;
        }
        
        $modelFound=$object->getByUserName($object->username);
        if ($modelFound!=null && ($modelFound->id!=$this->id)){
            $object->addError($attribute, $params['message']);
        }
        
    }
}