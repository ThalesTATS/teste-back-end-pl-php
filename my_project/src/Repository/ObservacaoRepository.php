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

    private AnexoRepository $anexoRepository;

    public function __construct(ManagerRegistry $registry, AnexoRepository $anexoRepository)
    {
        parent::__construct($registry, Observacao::class);
        $this->anexoRepository = $anexoRepository;
    }

    public function create($values, $entityManager): Observacao
    {
        $observacao = new Observacao();
        $this->setValues($observacao, $values, $entityManager);
        $entityManager->persist($observacao);
        $entityManager->flush();
        return $observacao;
    }

    public function update($observacao, $values, $entityManager): Observacao
    {
        $this->setValues($observacao, $values, $entityManager);
        $entityManager->flush();
        return $observacao;
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
        $anexo = $this->anexoRepository->getAll($observacao->getAnexos());
        return [
            'id' => $observacao->getId(),
            'descricao' => $observacao->getDescricao(),
            'anexo' => $anexo,
        ];
    }
}
