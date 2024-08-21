<?php

namespace App\Validations;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hospital;

class MedicoValidation extends RequestValidation
{
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->rules = [
            'nome' => [
                new Assert\NotBlank(['message' => 'O nome do médico é obrigatório.']),
                new Assert\NotNull(['message' => 'O nome do médico é obrigatório.'])
            ],
            'especialidade' => [
                new Assert\NotBlank(['message' => 'A especialidade do médico é obrigatório.']),
                new Assert\NotNull(['message' => 'A especialidade do médico é obrigatório.']),
            ],
            'hospital_id' => [
                new Assert\NotBlank(['message' => 'O hospital do médico é obrigatório.']),
                new Assert\NotNull(['message' => 'O hospital do médico é obrigatório.']),
            ],
        ];
    }

    public function withValidation(){
        $errors = [];

        if(!empty($this->values['hospital_id'])) {
            $hospital = $this->entityManager->getRepository(Hospital::class)->find($this->values['hospital_id']);
            if (!$hospital) {
                $errors['hospital_id'] = 'O hospital informado não existe.';
            }   
        }

        return $errors;
    }
}
