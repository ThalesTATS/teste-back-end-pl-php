<?php

namespace App\Repository;

use App\Entity\Anexo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Anexo>
 */
class AnexoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anexo::class);
    }

    public function get($anexo){
        return [
            'id' => $anexo->getId(),
            'nome' => $anexo->getNome(),
            'extensao' => $anexo->getExtensao(),
            'tamanho' => $anexo->getTamanho(),
            'url' => $anexo->getUrl(),
        ];
    }

    public function getAll($anexos): array
    {
        $data = [];
        foreach ($anexos as $anexo) {
            $data[] = $this->get($anexo);
        }
        return $data;
    }

}
