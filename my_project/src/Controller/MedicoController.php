<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MedicoRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Medico;
use App\Requests\MedicoRequest;

class MedicoController extends AbstractController
{
    private $medicoRepository;

    public function __construct(MedicoRepository $medicoRepository){
        $this->medicoRepository = $medicoRepository;
    }

    #[Route('/medico', name: 'medico.index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $medico = $entityManager->getRepository(Medico::class)->findAll();
        return $this->json(['data' => $this->medicoRepository->getAll($medico)]);
    }

    #[Route('/medico/{id}/show', name: 'medico.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return $this->json(['error' => 'Médico não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $this->medicoRepository->get($medico)]);
    }

    #[Route('/medico/store', name: 'medico.store', methods: ['POST'])]
    public function store(Request $request, MedicoRequest $medicoRequest, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $errors = $medicoRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->medicoRepository->create($request->request->all(), $entityManager);

        return $this->json(['message' => 'Médico criado com sucesso!']);
    }

    #[Route('/medico/{id}/update', name: 'medico.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, MedicoRequest $medicoRequest, ValidatorInterface $validator): JsonResponse
    {

        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return $this->json(['status' => 'error', 'error' => 'Médico não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $medicoRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse(['status' => 'error', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->medicoRepository->update($medico, $request->request->all(), $entityManager);

        return $this->json(['message' => 'Médico atualizado com sucesso!']);
    }

    #[Route('/medico/{id}/destroy', name: 'medico.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return $this->json(['error' => 'Hospital não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($medico);
        $entityManager->flush();
        return $this->json(['message' => 'Médico removido com sucesso!']);
    }
}
