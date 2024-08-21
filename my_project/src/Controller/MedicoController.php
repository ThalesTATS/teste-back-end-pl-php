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
use App\Validations\MedicoValidation;
use App\Services\ResponseService;

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
        return ResponseService::data($this->medicoRepository->getAll($medico));
    }

    #[Route('/medico/{id}/show', name: 'medico.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return ResponseService::error('Médico não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }
        return ResponseService::data($this->medicoRepository->get($medico));
    }

    #[Route('/medico/store', name: 'medico.store', methods: ['POST'])]
    public function store(Request $request, MedicoValidation $medicoValidation, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $errors = $medicoValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->medicoRepository->create($request->request->all(), $entityManager);
        return ResponseService::success('Médico criado com sucesso!');
    }

    #[Route('/medico/{id}/update', name: 'medico.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, MedicoValidation $medicoValidation, ValidatorInterface $validator): JsonResponse
    {

        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return ResponseService::error('Médico não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $medicoValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->medicoRepository->update($medico, $request->request->all(), $entityManager);
        return ResponseService::success('Médico atualizado com sucesso!');
    }

    #[Route('/medico/{id}/destroy', name: 'medico.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $medico = $entityManager->getRepository(Medico::class)->find($id);
        if (!$medico) {
            return ResponseService::error('Médico não encontrado.', JsonResponse::HTTP_NOT_FOUND);
        }

        $consultas = $medico->getConsultas();
        if(count($consultas) > 0){
            return ResponseService::error('Médico possui consultas.', JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $entityManager->remove($medico);
        $entityManager->flush();
        return ResponseService::success('Médico removido com sucesso!');
    }
}
