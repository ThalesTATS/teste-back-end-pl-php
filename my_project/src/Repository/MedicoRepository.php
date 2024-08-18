<?php

namespace App\Repository;

use App\Entity\Medico;
use App\Entity\Hospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medico>
 */
class MedicoRepository extends ServiceEntityRepository
{

    private $hospitalRepository;

    public function __construct(ManagerRegistry $registry, HospitalRepository $hospitalRepository)
    {
        parent::__construct($registry, Medico::class);
        $this->hospitalRepository = $hospitalRepository;
    }

    public function create($values, $entityManager): void
    {
        $medico = new Medico();
        $this->setValues($medico, $values, $entityManager);
        $entityManager->persist($medico);
        $entityManager->flush();
    }

    public function update($medico, $values, $entityManager): void
    {
        $this->setValues($medico, $values, $entityManager);
        $entityManager->flush();
    }


    public function setValues($medico, $values, $entityManager){
        $medico->setNome($values['nome']);
        $medico->setEspecialidade($values['especialidade']);
        $hospital = $entityManager->getRepository(Hospital::class)->find($values['hospital_id']);
        $medico->setHospital($hospital);
    }

    public function getAll($medicos): array
    {
        $data = [];
        foreach ($medicos as $medico) {
            $data[] = $this->get($medico);
        }
        return $data;
    }

    public function get($medico){
        $hospital = $this->hospitalRepository->get($medico->getHospital());
        return [
            'id' => $medico->getId(),
            'nome' => $medico->getNome(),
            'especialidade' => $medico->getEspecialidade(),
            'hospital' => $hospital
        ];
    }
}
