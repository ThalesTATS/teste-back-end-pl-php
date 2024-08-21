<?php

namespace App\Validations;

use Symfony\Component\Validator\Constraints as Assert;

class HospitalValidation extends RequestValidation
{
    public function __construct(){
        $this->rules = [
            'nome' => [
                new Assert\NotBlank(['message' => 'O nome do hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'O nome do hospital é obrigatório.'])
            ],
            'cep' => [
                new Assert\NotBlank(['message' => 'O cep do hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'O cep do hospital é obrigatório.'])
            ],
            'estado' => [
                new Assert\NotBlank(['message' => 'O estado do hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'O estado do hospital é obrigatório.'])
            ],
            'cidade' => [
                new Assert\NotBlank(['message' => 'A cidade do hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'A cidade do hospital é obrigatório.'])
            ],
            'bairro' => [
                new Assert\NotBlank(['message' => 'O bairro do hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'O bairro do hospital é obrigatório.'])
            ],
            'localidade' => [
                
            ],
            'complemento' => [
                
            ],
        ];
    }
}
