<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BeneficiarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beneficiario;
use Symfony\Component\HttpFoundation\Request;
use App\Requests\BeneficiarioRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BeneficiarioController extends AbstractController
{

    private $beneficiarioRepository;

    public function __construct(BeneficiarioRepository $beneficiarioRepository){
        $this->beneficiarioRepository = $beneficiarioRepository;
    }

    #[Route('/beneficiario', name: 'beneficiario.index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->findAll();
        return $this->json(['data' => $this->beneficiarioRepository->getAll($beneficiario)]);
    }

    #[Route('/beneficiario/{id}/show', name: 'beneficiario.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return $this->json(['error' => 'Beneficiário não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $this->beneficiarioRepository->get($beneficiario)]);
    }

    #[Route('/beneficiario/store', name: 'beneficiario.store', methods: ['POST'])]
    public function store(Request $request, BeneficiarioRequest $beneficiarioRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $errors = $beneficiarioRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->beneficiarioRepository->create($request->request->all(), $entityManager);

        return $this->json(['message' => 'Beneficiário criado com sucesso!']);
    }

    #[Route('/beneficiario/{id}/update', name: 'beneficiario.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, BeneficiarioRequest $beneficiarioRequest, ValidatorInterface $validator): JsonResponse
    {

        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return $this->json(['error' => 'Beneficiário não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $beneficiarioRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->beneficiarioRepository->update($beneficiario, $request->request->all(), $entityManager);

        return $this->json(['message' => 'Beneficiário atualizado com sucesso!']);
    }

    #[Route('/beneficiario/{id}/destroy', name: 'beneficiario.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return $this->json(['error' => 'Beneficiário não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($beneficiario);
        $entityManager->flush();
        return $this->json(['message' => 'Beneficiário removido com sucesso!']);
    }
}
