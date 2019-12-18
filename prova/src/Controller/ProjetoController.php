<?php

namespace App\Controller;

use App\Entity\Professor;
use App\Entity\Projeto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjetoController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    public function index()
    {
        return $this->json([
            'Mensagem' => 'Você está na rota de Projeto!',
            'Inserir' => '/inserirprojeto',
            'Buscar' => '/buscarprojeto',
            'Buscar por id' => '/buscarprojeto/{id}',
            'Buscar projeto por professor' => '/buscarprojetoprofessor/{id_professor}',
            'Finalizar projeto' => '/finalizarprojeto/{id}',
            'Filtrar por status' => '/filtroprojeto/{status}'
        ]);
    }
    public function criar(Request $request): Response {
        $content = json_decode($request->getContent());
        $prof = $this->getDoctrine()->getRepository(Professor::class);
        $prof = $prof->find($content->professor);

        $proj = new Projeto();
        $proj->nome = $content->nome;
        $proj->status = $content->status;
        $proj->professor = $prof;

        $this->entityManager->persist($proj);
        $this->entityManager->flush();
        return new JsonResponse($proj);
    }

    public function buscar()
    {
        $repository = $this->getDoctrine()->getRepository(Projeto::class);
        $projetos = $repository->findAll();
        $status = is_null($projetos) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        return new JsonResponse($projetos, $status);
    }

    public function buscarProjetoId(string $id, Request $request): Response{
        $idP = $request->get("id");
        $repository = $this->getDoctrine()->getRepository(Projeto::class);
        $projetos = $repository->findById($idP);
        $id = is_null($projetos) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        return new JsonResponse($projetos, $id);
    }

    public function buscarProjetoProfessor(int $id_professor, Request $request): Response{
        $idProf = $request->get("id_professor");
        $repository = $this->getDoctrine()->getRepository(Professor::class);
        $professor = $repository->find($idProf);

        $repository = $this->getDoctrine()->getRepository(Projeto::class);
        $projetos = $repository->findByProfessor($professor);
        // $idp = is_null($projetos) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        //return new JsonResponse(['Professor'=>$professor, 'Projetos'=>$projetos], Response::HTTP_OK);
        return new JsonResponse($projetos);
    }

    public function finalizarProjeto(int $id) {
        $projeto = $this->getDoctrine()->getRepository(Projeto::class);
        $projetos = $projeto->find($id);
        $projetos->setStatus(false);
        $this->entityManager->persist($projetos);
        $this->entityManager->flush();
        return new JsonResponse(['Mensagem'=>"Projeto finalizado", 'Projeto'=>$projetos], Response::HTTP_OK);
    }

    public function filtroProjeto(string $status, Request $request) {
        if($status == 'true'){
            $status = 1;
        }else{
            $status = 0;
        }
        $projeto = $this->getDoctrine()->getRepository(Projeto::class);
        $projetos = $projeto->findByStatus($status);
        return new JsonResponse($projetos);
    }

}
