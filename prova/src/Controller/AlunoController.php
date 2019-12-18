<?php
namespace App\Controller;

use App\Entity\Aluno;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlunoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index()
    {
        return $this->json([
            'Mensagem' => 'Você está na rota de Aluno!',
            'Inserir' => '/inseriraluno',
            'Buscar' => '/buscaraluno'
        ]);
    }

    public function criar(Request $request): Response {

        $content = json_decode($request->getContent());
        $aluno = new Aluno();
        $aluno->nome = $content->nome;
        $aluno->status = $content->status;
        $aluno->projetos = $content->projetos;

        $this->entityManager->persist($aluno);
        $this->entityManager->flush();
        return new JsonResponse($aluno);
    }

    public function buscar() {
        $repository = $this->getDoctrine()->getRepository(Aluno::class);
        $aluno = $repository->findAll();
        $status = is_null($aluno) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        return new JsonResponse($aluno, $status);
    }
}

