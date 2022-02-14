<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Workflow\Registry;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{

    public function __construct(Registry $workflowRegistry) 
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    #[Route('/{id}/state/{transition}', name: 'article_state_change', methods: ['GET'])]
    public function stateChange(Request $request, Article $article, String $transition): Response
    {
        $workflow = $this->workflowRegistry->get($article);
        if($workflow->can($article, $transition))
        {
            $workflow->apply($article, $transition);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
        }
        return $this->redirectToRoute("article_index");
    }


    #[Security("is_granted('ROLE_LIST_ARTICLE')")]
    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Security("is_granted('ROLE_CREATE_ARTICLE')")]
    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $workflow =  $this->workflowRegistry->get($article);

        if ($form->isSubmitted() && $form->isValid() && $workflow->can($article, "submit")) {
            $workflow->apply($article, "submit");
            $article->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Security("is_granted('ROLE_READ_ARTICLE')")]
    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    //[Security("is_granted('ROLE_UPDATE_ARTICLE', article)")]
    #[Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_UPDATE_ARTICLE') and article.getAuthor() === user)")]
    #[Route('/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Security("is_granted('ROLE_DELETE_ARTICLE')")]
    #[Route('/{id}/delete', name: 'article_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/delete.html.twig', [
            'article' => $article,
        ]);
    }
}
