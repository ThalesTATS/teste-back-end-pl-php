<?php

namespace App\Repository;

use App\Entity\Consulta;
use App\Entity\Hospital;
use App\Entity\Medico;
use App\Entity\Beneficiario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Consulta>
 */
class ConsultaRepository extends ServiceEntityRepository
{

    private $hospitalRepository;
    private $medicoRepository;
    private $beneficiarioRepository;
    private $observacaoRepository;

    public function __construct(
        ManagerRegistry $registry, 
        HospitalRepository $hospitalRepository, 
        MedicoRepository $medicoRepository, 
        BeneficiarioRepository $beneficiarioRepository,
        ObservacaoRepository $observacaoRepository
    ){
        parent::__construct($registry, Consulta::class);
        $this->hospitalRepository = $hospitalRepository;
        $this->medicoRepository = $medicoRepository;
        $this->beneficiarioRepository = $beneficiarioRepository;
        $this->observacaoRepository = $observacaoRepository;
    }

    public function create($values, $entityManager): void
    {
        $consulta = new Consulta();
        $values['status'] = false;
        $this->setValues($consulta, $values, $entityManager);
        $entityManager->persist($consulta);
        $entityManager->flush();
    }

    public function update($consulta, $values, $entityManager): void
    {
        $values['status'] = $consulta->isStatus();
        $this->setValues($consulta, $values, $entityManager);
        $entityManager->flush();
    }


    public function setValues($consulta, $values, $entityManager){
        $consulta->setData(new \DateTime($values['data']));
        $consulta->setStatus($values['status']);
        $hospital = $entityManager->getRepository(Hospital::class)->find($values['hospital_id']);
        $consulta->setHospital($hospital);
        $medico = $entityManager->getRepository(Medico::class)->find($values['medico_id']);
        $consulta->setMedico($medico);
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($values['beneficiario_id']);
        $consulta->setBeneficiario($beneficiario);
    }

    public function getAll($consultas): array
    {
        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = $this->get($consulta);
        }
        return $data;
    }

    public function get($consulta){
        $hospital = $this->hospitalRepository->get($consulta->getHospital());
        $beneficiario = $this->beneficiarioRepository->get($consulta->getBeneficiario());
        $medico = $this->medicoRepository->get($consulta->getMedico());
        $observacoes = $this->observacaoRepository->getAll($consulta->getObservacaos());
        return [
            'id' => $consulta->getId(),
            'data' => $consulta->getData(),
            'status' => $consulta->isStatus(),
            'hospital' => $hospital,
            'beneficiario' => $beneficiario,
            'medico' => $medico,
            'observacoes' => $observacoes
        ];
    }
}
