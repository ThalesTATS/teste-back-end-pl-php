<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hospital;
use App\Validations\HospitalValidation;
use App\Repository\HospitalRepository;
use App\Services\ResponseService;

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
        return ResponseService::data($this->hospitalRepository->getAll($hospital));
    }

    #[Route('/hospital/{id}/show', name: 'hospital.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return ResponseService::error('Hospital não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }
        return ResponseService::data($this->hospitalRepository->get($hospital));
    }

    #[Route('/hospital/store', name: 'hospital.store', methods: ['POST'])]
    public function store(Request $request, HospitalValidation $hospitalValidation, ValidatorInterface $validator): JsonResponse
    {
        $errors = $hospitalValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->hospitalRepository->create($request->request->all());
        return ResponseService::success('Hospital criado com sucesso!', JsonResponse::HTTP_CREATED);
    }

    #[Route('/hospital/{id}/update', name: 'hospital.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, HospitalValidation $hospitalValidation, ValidatorInterface $validator): JsonResponse
    {

        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return $this->json(['error' => 'Hospital não encontrado.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $hospitalValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $hospital->setNome($request->request->get('nome'));
        $entityManager->flush();
        return ResponseService::success('Hospital atualizado com sucesso!');
    }

    #[Route('/hospital/{id}/destroy', name: 'hospital.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $hospital = $entityManager->getRepository(Hospital::class)->find($id);
        if (!$hospital) {
            return ResponseService::error('Hospital não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }

        $medicos = $hospital->getMedicos();
        if(count($medicos) > 0){
            return ResponseService::error('Hospital possui médicos.', JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $entityManager->remove($hospital);
        $entityManager->flush();
        return ResponseService::success('Hospital removido com sucesso!');
    }
}