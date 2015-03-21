<?php

class uniqueEmail extends CValidator
{
  
    protected function validateAttribute($object,$attribute){
        if (empty($object->email)){
            return;
        }
        
        $modelFound=$object->getByEmail($object->email);
        if ($modelFound!=null && ($modelFound->id!=$this->id)){
            $object->addError($attribute, $params['message']);
        }
        
    }
}