<?php

class uniqueUsername extends CValidator
{
    protected function validateAttribute($object,$attribute){
        
        if (empty($object->username)){
            return;
        }
        
        $modelFound=$object->getByUserName($object->username);
        if ($modelFound!=null && ($modelFound->id!=$object->id)){
            $object->addError($attribute, $this->message);
        }
    }
}