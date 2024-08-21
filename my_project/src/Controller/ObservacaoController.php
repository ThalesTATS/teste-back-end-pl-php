<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Observacao;
use App\Repository\ObservacaoRepository;
use App\Entity\Consulta;
use App\Validations\ObservacaoValidation;
use App\Validations\AnexoValidation;
use App\Services\AnexoService;
use App\Services\ResponseService;

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
        return ResponseService::data($this->observacaoRepository->getAll($observacao));
    }

    #[Route('/observacao/{id}/show', name: 'observacao.show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return ResponseService::error('Observação não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }
        return ResponseService::data($this->observacaoRepository->get($observacao));
    }

    #[Route('/observacao/store', name: 'observacao.store', methods: ['POST'])]
    public function store(
        Request $request, 
        ObservacaoValidation $observacaoValidation, 
        ValidatorInterface $validator, 
        EntityManagerInterface $entityManager, 
        AnexoValidation $anexoValidation
    ){
        $errors = $observacaoValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $errors = $anexoValidation->validate(['anexo' => $request->files->get('anexo')], $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $observacao = $this->observacaoRepository->create($request->request->all(), $entityManager);
        AnexoService::upload($request->files->get('anexo'), $observacao, $entityManager, $this->getParameter('uploads_directory'));
        return ResponseService::success('Observação criada com sucesso!');
    }

    #[Route('/observacao/{id}/update', name: 'observacao.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ObservacaoValidation $observacaoValidation, ValidatorInterface $validator): JsonResponse
    {

        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return ResponseService::error('Observação não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $observacaoValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        $this->observacaoRepository->update($observacao, $request->request->all(), $entityManager);
        return ResponseService::success('Observação atualizada com sucesso!');
    }

    #[Route('/observacao/{id}/destroy', name: 'observacao.destroy', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return ResponseService::error('Observação não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }

        $consulta = $observacao->getConsulta();
        if($consulta->isStatus()){
            return ResponseService::error('A consulta já foi concluída.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $anexos = $observacao->getAnexos();
        foreach ($anexos as $anexo) {
            AnexoService::remove($anexo, $entityManager, $this->getParameter('uploads_directory'));
        }

        $entityManager->remove($observacao);
        $entityManager->flush();
        return ResponseService::success('Observação removida com sucesso!');
    }

    #[Route('/observacao/{id}/uploadAnexo', name: 'observacao.uploadAnexo', methods: ['POST'])]
    public function uploadAnexo(int $id, EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, AnexoValidation $anexoValidation): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return ResponseService::error('Observação não encontrada.', JsonResponse::HTTP_NOT_FOUND);
        }

        $consulta = $observacao->getConsulta();
        if($consulta->isStatus()){
            return ResponseService::error('A consulta já foi concluída.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $anexoValidation->validate(['anexo' => $request->files->get('anexo')], $validator);
        if (count($errors) > 0) {
            return ResponseService::error('Request inválido.', JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        AnexoService::upload($request->files->get('anexo'), $observacao, $entityManager, $this->getParameter('uploads_directory'));
        return ResponseService::success('Anexo adicionado com sucesso!');
    }
}
