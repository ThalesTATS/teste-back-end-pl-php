<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ConsultaRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Consulta;
use App\Validations\ConsultaValidation;
use App\Services\ResponseService;

class ConsultaController extends AbstractController
{
    private $consultaRepository;

    public function __construct(ConsultaRepository $consultaRepository){
        $this->consultaRepository = $consultaRepository;
    }

    #[Route('/consulta', name: 'consulta.index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $consulta = $entityManager->getRepository(Consulta::class)->findAll();
        return ResponseService::data($this->consultaRepository->getAll($consulta));
    }

    #[Route('/consulta/{id}/show', name: 'consulta.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $consulta = $entityManager->getRepository(Consulta::class)->find($id);
        if (!$consulta) {
            return ResponseService::error('Consulta não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }
        return ResponseService::data($this->consultaRepository->get($consulta));
    }

    #[Route('/consulta/store', name: 'consulta.store', methods: ['POST'])]
    public function store(Request $request, ConsultaValidation $consultaValidation, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $errors = $consultaValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->consultaRepository->create($request->request->all(), $entityManager);
        return ResponseService::success('Consulta criada com sucesso!');
    }

    #[Route('/consulta/{id}/update', name: 'consulta.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ConsultaValidation $consultaValidation, ValidatorInterface $validator): JsonResponse
    {

        $consulta = $entityManager->getRepository(Consulta::class)->find($id);
        if (!$consulta) {
            return ResponseService::error('Consulta não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }
        if($consulta->isStatus()){
            return ResponseService::error('Não é possível alterar uma consulta concluída.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $consultaValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->consultaRepository->update($consulta, $request->request->all(), $entityManager);
        return ResponseService::success('Consulta atualizada com sucesso!');
    }

    #[Route('/consulta/{id}/destroy', name: 'consulta.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $consulta = $entityManager->getRepository(Consulta::class)->find($id);
        if (!$consulta) {
            return ResponseService::error('Consulta não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }
        if($consulta->isStatus()){
            return ResponseService::error('Não é possível alterar uma consulta concluída.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->remove($consulta);
        $entityManager->flush();
        return ResponseService::success('Consulta removida com sucesso!');
    }

    #[Route('/consulta/{id}/concluir', name: 'consulta.concluir', methods: ['PATCH'])]
    public function concluir(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $consulta = $entityManager->getRepository(Consulta::class)->find($id);
        if (!$consulta) {
            return ResponseService::error('Consulta não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }
        if($consulta->isStatus()){
            return ResponseService::error('Não é possível alterar uma consulta concluída.', JsonResponse::HTTP_BAD_REQUEST);
        }
        $consulta->setStatus(true);
        $entityManager->flush();
        return ResponseService::success('Consulta concluída com sucesso!');
    }
}
