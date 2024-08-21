<?php

namespace App\Validations;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Consulta;

class ObservacaoValidation extends RequestValidation
{
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->rules = [
            'descricao' => [
                new Assert\NotBlank(['message' => 'A descrição é obrigatório.']),
                new Assert\NotNull(['message' => 'A descrição é obrigatório.']),
            ],
            'consulta_id' => [
                new Assert\NotBlank(['message' => 'A consulta é obrigatório.']),
                new Assert\NotNull(['message' => 'A consulta é obrigatório.']),
            ],
        ];
    }

    public function withValidation(){
        $errors = [];

        if(!empty($this->values['consulta_id'])){
            $consulta = $this->entityManager->getRepository(Consulta::class)->find($this->values['consulta_id']);
            if(!$consulta){
                $errors['consulta_id'][] = 'A consulta informada não existe.';
            } else {
                if($consulta->isStatus()){
                    $errors['consulta_id'][] = 'A consulta já foi concluída.';
                }
            }
        }

        return $errors;
    }

}
