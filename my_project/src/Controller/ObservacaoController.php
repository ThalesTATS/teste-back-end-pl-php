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
use App\Entity\Anexo;
use App\Validations\ObservacaoValidation;
use App\Validations\AnexoValidation;
use App\Services\AnexoService;
use Symfony\Component\Validator\Constraints as Assert;

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
    public function store(
        Request $request, 
        ObservacaoValidation $observacaoValidation, 
        ValidatorInterface $validator, 
        EntityManagerInterface $entityManager, 
        AnexoValidation $anexoValidation
    ){
        $errors = $observacaoValidation->validate($request->request->all(), $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $anexoValidation->validate(['anexo' => $request->files->get('anexo')], $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }


        $observacao = $this->observacaoRepository->create($request->request->all(), $entityManager);
        AnexoService::upload($request->files->get('anexo'), $observacao, $entityManager, $this->getParameter('uploads_directory'));

        return $this->json(['message' => 'Observação criada com sucesso!']);
    }

    #[Route('/observacao/{id}/update', name: 'observacao.update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ObservacaoValidation $observacaoValidation, ValidatorInterface $validator): JsonResponse
    {

        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return $this->json(['status' => 'error', 'error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $observacaoValidation->validate($request->request->all(), $validator);
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

        AnexoService::remove($observacao->getAnexo(), $entityManager, $this->getParameter('uploads_directory'));
        $entityManager->remove($observacao);
        $entityManager->flush();
        return $this->json(['message' => 'Observação removida com sucesso!']);
    }

    #[Route('/observacao/{id}/uploadAnexo', name: 'observacao.uploadAnexo', methods: ['POST'])]
    public function uploadAnexo(int $id, EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, AnexoValidation $anexoValidation): JsonResponse
    {
        $observacao = $entityManager->getRepository(Observacao::class)->find($id);
        if (!$observacao) {
            return $this->json(['error' => 'Observação não encontrada.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = $anexoValidation->validate(['anexo' => $request->files->get('anexo')], $validator);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $consulta = $observacao->getConsulta();
        if($consulta->isStatus()){
            return $this->json(['status' => 'error', 'error' => 'A consulta já foi concluída.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        AnexoService::upload($request->files->get('anexo'), $observacao, $entityManager, $this->getParameter('uploads_directory'));

        return $this->json(['message' => 'Upload do anexo efetuado com sucesso!']);
    }
}
