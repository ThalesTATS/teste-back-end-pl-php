<?php

namespace App\Validations\Beneficiario;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Beneficiario;
use Doctrine\ORM\EntityManagerInterface;
use App\Validations\RequestValidation;

class UpdateValidation extends RequestValidation
{
    private $id;

    public function __construct(int $id, EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->id = $id;
        $this->rules = [
            'nome' => [
                new Assert\NotBlank(['message' => 'O nome do beneficiário é obrigatório.']),
                new Assert\NotNull(['message' => 'O nome do beneficiário é obrigatório.'])
            ],
            'email' => [
                new Assert\NotBlank(['message' => 'O email do beneficiário é obrigatório.']),
                new Assert\NotNull(['message' => 'O email do beneficiário é obrigatório.']),
                new Assert\Email(['message' => 'O email não é válido.']),
            ],
            'data_nascimento' => [
                new Assert\NotBlank(['message' => 'A data de nascimento do beneficiário é obrigatório.']),
                new Assert\NotNull(['message' => 'A data de nascimento do beneficiário é obrigatório.']),
                new Assert\Date(['message' => 'Informe uma data válida.'])
            ],
        ];
    }

    public function withValidation(){
        $errors = [];

        if (!$this->idadeIsValid()) {
            $errors['data_nascimento'][] = 'A idade do beneficiário não é válida.';
        }

        if (!$this->emailIsUnique()) {
            $errors['email'][] = 'O email não é válido.';
        }

        return $errors;
    }

    private function idadeIsValid(){
        $dataNascimento = new \DateTime($this->values['data_nascimento']);
        $hoje = new \DateTime();
        $idade = $hoje->diff($dataNascimento)->y;
        return $idade >= 18;
    }

    private function emailIsUnique(){
        $beneficiario = $this->entityManager->getRepository(Beneficiario::class)
            ->createQueryBuilder('b')
            ->where('b.email = :email')
            ->andWhere('b.id != :id')
            ->setParameter('email', $this->values['email'])
            ->setParameter('id', $this->id)
            ->getQuery()
            ->getOneOrNullResult();
        return !$beneficiario;
    }
}
