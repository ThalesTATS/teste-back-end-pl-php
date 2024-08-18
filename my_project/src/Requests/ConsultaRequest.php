<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hospital;
use App\Entity\Beneficiario;
use App\Entity\Medico;

class ConsultaRequest extends RequestValidation
{
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->rules = [
            'data' => [
                new Assert\NotBlank(['message' => 'A data da consulta é obrigatório.']),
                new Assert\NotNull(['message' => 'A data da consulta é obrigatório.']),
                new Assert\Date(['message' => 'Informe uma data válida.'])
            ],
            'hospital_id' => [
                new Assert\NotBlank(['message' => 'O hospital é obrigatório.']),
                new Assert\NotNull(['message' => 'O hospital é obrigatório.']),
            ],
            'medico_id' => [
                new Assert\NotBlank(['message' => 'O médico é obrigatório.']),
                new Assert\NotNull(['message' => 'O médico é obrigatório.']),
            ],
            'beneficiario_id' => [
                new Assert\NotBlank(['message' => 'O beneficiário é obrigatório.']),
                new Assert\NotNull(['message' => 'O beneficiário é obrigatório.']),
            ],
        ];
    }

    public function withValidation(){
        $errors = [];

        if(!empty($this->values['hospital_id'])){
            $hospital = $this->entityManager->getRepository(Hospital::class)->find($this->values['hospital_id']);
            if (!$hospital) {
                $errors['hospital_id'][] = 'O hospital informado não existe.';
            }
        }

        if(!empty($this->values['medico_id'])){
            $medico = $this->entityManager->getRepository(Medico::class)->find($this->values['medico_id']);
            if (!$medico) {
                $errors['medico_id'][] = 'O médico informado não existe.';
            }
        }

        if(!empty($this->values['beneficiario_id'])){
            $beneficiario = $this->entityManager->getRepository(Beneficiario::class)->find($this->values['beneficiario_id']);
            if (!$beneficiario) {
                $errors['beneficiario_id'][] = 'O beneficiário informado não existe.';
            }
        }

        return $errors;
    }
}
