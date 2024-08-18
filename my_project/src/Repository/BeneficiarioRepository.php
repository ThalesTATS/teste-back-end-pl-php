<?php

namespace App\Repository;

use App\Entity\Beneficiario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Beneficiario>
 */
class BeneficiarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beneficiario::class);
    }

    public function create($values, $entityManager): void
    {
        $beneficiario = new Beneficiario();
        $this->setValues($beneficiario, $values);
        $entityManager->persist($beneficiario);
        $entityManager->flush();
    }

    public function update($beneficiario, $values, $entityManager): void
    {
        $this->setValues($beneficiario, $values);
        $entityManager->flush();
    }


    public function setValues($beneficiario, $values){
        $beneficiario->setNome($values['nome']);
        $beneficiario->setEmail($values['email']);
        $beneficiario->setDataNascimento(new \DateTime($values['data_nascimento']));
    }

    public function getAll($beneficiarios): array
    {
        $data = [];
        foreach ($beneficiarios as $beneficiario) {
            $data[] = $this->get($beneficiario);
        }
        return $data;
    }

    public function get($beneficiario){
        return [
            'id' => $beneficiario->getId(),
            'nome' => $beneficiario->getNome(),
            'email' => $beneficiario->getEmail(),
            'data_nascimento' => $beneficiario->getDataNascimento(),
        ];
    }
}
