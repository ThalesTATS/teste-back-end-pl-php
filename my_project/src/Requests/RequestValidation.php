<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class RequestValidation
{

    protected $entityManager;
    protected $rules =[];
    protected $values =[];

    public function validate($values, ValidatorInterface $validator){
        $this->values = $values;
        $constraint = new Assert\Collection($this->rules);
        $errors = $validator->validate($values, $constraint);
        $errors = $this->getErrorsMessages($errors);
        $errors = array_merge($errors, $this->withValidation());
        if (count($errors) > 0) {
            return $errors;
        }
        return [];
    }

    private function getErrorsMessages($errors){
        $errorMessages = [];
        foreach ($errors as $error) {
            $key = str_replace("[", "", $error->getPropertyPath());
            $key = str_replace("]", "", $key);
            $errorMessages[$key][] = $error->getMessage();
        }
        return $errorMessages;
    }

    public function withValidation(){return [];}
}
