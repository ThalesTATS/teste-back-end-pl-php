<?php

namespace App\Repository;

use App\Entity\Observacao;
use App\Entity\Consulta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Observacao>
 */
class ObservacaoRepository extends ServiceEntityRepository
{

    private ConsultaRepository $consultaRepository;

    public function __construct(ManagerRegistry $registry, ConsultaRepository $consultaRepository)
    {
        parent::__construct($registry, Observacao::class);
        $this->consultaRepository = $consultaRepository;
    }

    public function create($values, $entityManager): void
    {
        $observacao = new Observacao();
        $this->setValues($observacao, $values, $entityManager);
        $entityManager->persist($observacao);
        $entityManager->flush();
    }

    public function update($observacao, $values, $entityManager): void
    {
        $this->setValues($observacao, $values, $entityManager);
        $entityManager->flush();
    }


    public function setValues($observacao, $values, $entityManager){
        $observacao->setDescricao($values['descricao']);
        $consulta = $entityManager->getRepository(Consulta::class)->find($values['consulta_id']);
        $observacao->setConsulta($consulta);
    }

    public function getAll($observacoes): array
    {
        $data = [];
        foreach ($observacoes as $observacao) {
            $data[] = $this->get($observacao);
        }
        return $data;
    }

    public function get($observacao){
        $consulta = $this->consultaRepository->get($observacao->getConsulta());
        return [
            'id' => $observacao->getId(),
            'data' => $observacao->getDescricao(),
            'consulta' => $consulta,
        ];
    }
}
