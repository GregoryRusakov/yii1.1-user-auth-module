<?php

class uniqueEmail extends CValidator
{
    protected function validateAttribute($object,$attribute){
        
        if (empty($object->email)){
            return;
        }
        
        $modelFound=Users::model()->getByEmail($object->email);
        if ($modelFound!=null && ($modelFound->id!=$object->id)){
            $object->addError($attribute, $this->message);
        }
    }
}