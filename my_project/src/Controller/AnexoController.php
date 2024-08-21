<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Anexo;
use App\Services\AnexoService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AnexoController extends AbstractController
{

    #[Route('/anexo/{id}/show', name: 'anexo_show')]
    public function show(int $id, EntityManagerInterface $entityManager)
    {
        $anexo = $entityManager->getRepository(Anexo::class)->find($id);

        if (!$anexo) {
            throw $this->createNotFoundException('Anexo não encontrado');
        }

        $filePath = $this->getParameter('uploads_directory') . $anexo->getUrl();

        return new BinaryFileResponse($filePath);
    }

    #[Route('/anexo/{id}/destroy', name: 'anexo.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {

        $anexo = $entityManager->getRepository(Anexo::class)->find($id);
        if (!$anexo) {
            return $this->json(['error' => 'Anexo não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $observacao = $anexo->getObservacao();
        if (!$observacao) {
            return $this->json(['error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $consulta = $observacao->getConsulta();
        if($consulta->isStatus()){
            return $this->json(['status' => 'error', 'error' => 'A consulta já foi concluída.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        AnexoService::remove($anexo, $entityManager, $this->getParameter('uploads_directory'));
        return $this->json(['message' => 'Anexo removido com sucesso!']);
    }
}


