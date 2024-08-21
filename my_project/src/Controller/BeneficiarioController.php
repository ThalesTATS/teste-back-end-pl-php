<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BeneficiarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beneficiario;
use Symfony\Component\HttpFoundation\Request;
use App\Validations\Beneficiario\StoreValidation;
use App\Validations\Beneficiario\UpdateValidation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\ResponseService;

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
        return ResponseService::data($this->beneficiarioRepository->getAll($beneficiario));
    }

    #[Route('/beneficiario/{id}/show', name: 'beneficiario.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return ResponseService::error('Beneficiário não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }
        return ResponseService::data($this->beneficiarioRepository->get($beneficiario));
    }

    #[Route('/beneficiario/store', name: 'beneficiario.store', methods: ['POST'])]
    public function store(Request $request, StoreValidation $beneficiarioValidation, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        
        $errors = $beneficiarioValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->beneficiarioRepository->create($request->request->all(), $entityManager);
        return ResponseService::success('Beneficiário criado com sucesso!');
    }

    #[Route('/beneficiario/{id}/update', name: 'beneficiario.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {

        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return ResponseService::error('Beneficiário não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }

        $updateValidation = new UpdateValidation($id, $entityManager);
        $errors = $updateValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->beneficiarioRepository->update($beneficiario, $request->request->all(), $entityManager);
        return ResponseService::success('Beneficiário atualizado com sucesso!');
    }

    #[Route('/beneficiario/{id}/destroy', name: 'beneficiario.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $beneficiario = $entityManager->getRepository(Beneficiario::class)->find($id);
        if (!$beneficiario) {
            return ResponseService::error('Beneficiário não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($beneficiario);
        $entityManager->flush();
        return ResponseService::success('Beneficiário removido com sucesso!');
    }
}
