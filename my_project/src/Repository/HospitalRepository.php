<?php

namespace App\Repository;

use App\Entity\Hospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Hospital>
 */
class HospitalRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Hospital::class);
        $this->entityManager = $entityManager;
    }

    public function create($values): void
    {
        $hospital = new Hospital();
        $this->setValues($hospital, $values);
        $this->entityManager->persist($hospital);
        $this->entityManager->flush();
    }

    public function update($hospital, $values): void
    {
        $hospital = new Hospital();
        $this->setValues($hospital, $values);
        $this->entityManager->persist($hospital);
        $this->entityManager->flush();
    }


    public function setValues($hospital, $values){
        $hospital->setNome($values['nome']);
        $hospital->setCep($values['cep']);
        $hospital->setEstado($values['estado']);
        $hospital->setCidade($values['cidade']);
        $hospital->setBairro($values['bairro']);
        if(!empty($values['localidade'])){
            $hospital->setLocalidade($values['localidade']);
        }
        if(!empty($values['complemento'])){
            $hospital->setComplemento($values['complemento']);
        }
    }

    public function getAll($hospitals): array
    {
        $data = [];
        foreach ($hospitals as $hospital) {
            $data[] = $this->get($hospital);
        }
        return $data;
    }

    public function get($hospital){
        return [
            'id' => $hospital->getId(),
            'nome' => $hospital->getNome(),
            'cidade' => $hospital->getCidade(),
            'estado' => $hospital->getEstado(),
            'bairro' => $hospital->getBairro(),
            'localidade' => $hospital->getLocalidade(),
            'complemento' => $hospital->getComplemento(),
        ];
    }
}
