<?php

namespace App\Validations;

use Symfony\Component\Validator\Constraints as Assert;

class AnexoValidation extends RequestValidation
{
    public function __construct(){
        $this->rules = [
            'anexo' => [
                new Assert\File([
                    'maxSize' => '1024k',
                    'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'img', 'IMG', 'JPG', 'JPEG', 'PNG', 'GIF'],
                    'extensionsMessage' => 'Formato de arquivo inválido.'
                ]),
            ],
        ];
    }
}
