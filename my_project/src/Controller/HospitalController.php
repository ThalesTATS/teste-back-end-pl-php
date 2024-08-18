<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hospital;
use App\Requests\HospitalRequest;
use App\Repository\HospitalRepository;

class HospitalController extends AbstractController
{

    private $hospitalRepository;

    public function __construct(HospitalRepository $hospitalRepository){
        $this->hospitalRepository = $hospitalRepository;
    }

    #[Route('/hospital', name: 'hospital.index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $hospital = $entityManager->getRepository(Hospital::class)->findAll();
        return $this->json(['data' => $this->hospitalRepository->getAll($hospital)]);
    }

    #[Route('/hospital/{id}/show', name: 'hospital.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return $this->json(['error' => 'Hospital não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $this->hospitalRepository->get($hospital)]);
    }

    #[Route('/hospital/store', name: 'hospital.store', methods: ['POST'])]
    public function store(Request $request, HospitalRequest $hospitalRequest, ValidatorInterface $validator): JsonResponse
    {
        $errors = $hospitalRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->hospitalRepository->create($request->request->all());

        return $this->json(['message' => 'Hospital criado com sucesso!']);
    }

    #[Route('/hospital/{id}/update', name: 'hospital.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, HospitalRequest $hospitalRequest, ValidatorInterface $validator): JsonResponse
    {

        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return $this->json(['error' => 'Hospital não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $hospitalRequest->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $hospital->setNome($request->request->get('nome'));
        $entityManager->flush();

        return $this->json(['message' => 'Hospital atualizado com sucesso!']);
    }

    #[Route('/hospital/{id}/destroy', name: 'hospital.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return $this->json(['error' => 'Hospital não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($hospital);
        $entityManager->flush();
        return $this->json(['message' => 'Hospital removido com sucesso!']);
    }
}