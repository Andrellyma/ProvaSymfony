<?php

namespace App\Controller;

use App\Entity\Aluno;
use App\Entity\Professor;
use App\Entity\Projeto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfessorController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    public function index()
    {
        return $this->json([
            'Mensagem' => 'Você está na rota de Professor!',
            'Inserir' => '/inserirprofessor',
            'Buscar' => '/buscarprofessor',
            'Vincular aluno a projeto' => '/vincularaluno/{id}',
            'Desvincular aluno a projeto' => '/desvincularaluno/{id}'
        ]);
    }
    public function criar(Request $request): Response {
        $content = json_decode($request->getContent());
        $prof = new Professor();
        $prof->nome = $content->nome;

        $this->entityManager->persist($prof);
        $this->entityManager->flush();
        return new JsonResponse($prof);
    }

    public function buscar() {
        $repository = $this->getDoctrine()->getRepository(Professor::class);
        $prof = $repository->findAll();
        $status = is_null($prof) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        return new JsonResponse($prof, $status);
    }

    public function vincularAluno(int $id,  Request $request) {
        $content = json_decode($request->getContent());
        $idAluno = $request->get("id");

        $repository = $this->getDoctrine()->getRepository(Projeto::class);
        $projeto = $repository->find($content->projeto);

        $repository = $this->getDoctrine()->getRepository(Aluno::class);
        $aluno = $repository->find($idAluno);

        if($aluno->getStatus()){
            return new JsonResponse(['Mensagem'=>"Aluno já vinculado a um projeto"], Response::HTTP_BAD_REQUEST);
        }
        $aluno->setProjeto($projeto);
        $aluno->setStatus(true);
        $this->entityManager->persist($aluno);
        $this->entityManager->flush();

        return new JsonResponse(['Mensagem'=>"Aluno vinculado ao projeto", 'Projeto'=>$projeto], Response::HTTP_OK);

    }

    public function desvincularAluno(int $id,  Request $request) {

        $idAluno = $request->get("id");

        $repository = $this->getDoctrine()->getRepository(Aluno::class);
        $aluno = $repository->find($idAluno);

        if(is_null($aluno)){
            return new JsonResponse(["Erro"=>"Aluno não encontrado"], Response::HTTP_NOT_FOUND);
        }

        if(is_null($aluno->getProjeto())){
            return new JsonResponse(["Erro"=>"Aluno não estar em nenhum projeto"], Response::HTTP_NOT_FOUND);
        }

        $aluno->setProjeto(null);
        $aluno->setStatus(false);
        $this->entityManager->persist($aluno);
        $this->entityManager->flush();
        return new JsonResponse(['Mensagem'=>"Aluno desvinculado do projeto", 'Aluno'=>$aluno], Response::HTTP_OK);
    }

}
