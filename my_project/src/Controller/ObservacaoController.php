<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Observacao;
use App\Requests\ObservacaoRequest;
use App\Repository\ObservacaoRepository;
use App\Entity\Consulta;

class ObservacaoController extends AbstractController
{
    private $observacaoRepository;

    public function __construct(ObservacaoRepository $observacaoRepository){
        $this->observacaoRepository = $observacaoRepository;
    }

    #[Route('/observacao', name: 'observacao.index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->findAll();
        return $this->json(['data' => $this->observacaoRepository->getAll($observacao)]);
    }

    #[Route('/observacao/{id}/show', name: 'observacao.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return $this->json(['error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $this->observacaoRepository->get($observacao)]);
    }

    #[Route('/observacao/store', name: 'observacao.store', methods: ['POST'])]
    public function store(Request $request, ObservacaoRequest $observacaoRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $errors = $observacaoRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->observacaoRepository->create($request->request->all(), $entityManager);

        return $this->json(['message' => 'Observação criada com sucesso!']);
    }

    #[Route('/observacao/{id}/update', name: 'observacao.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ObservacaoRequest $observacaoRequest, ValidatorInterface $validator): JsonResponse
    {

        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return $this->json(['status' => 'error', 'error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $observacaoRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse(['status' => 'error', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->observacaoRepository->update($observacao, $request->request->all(), $entityManager);

        return $this->json(['message' => 'Observação atualizada com sucesso!']);
    }

    #[Route('/observacao/{id}/destroy', name: 'observacao.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return $this->json(['error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $consulta = $entityManager->getRepository(Consulta::class)->find($id);
        if($consulta->isStatus()){
            return $this->json(['status' => 'error', 'error' => 'A consulta já foi concluída.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->remove($observacao);
        $entityManager->flush();
        return $this->json(['message' => 'Observação removida com sucesso!']);
    }
}
