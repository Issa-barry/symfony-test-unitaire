<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'articles' => $articleRepository->findBy(["state" =>"published"]),
        ]);
    }

    #[Security("is_granted('ROLE_READ_ARTICLE')")]
    #[Route('/article/{id}', name: 'home_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('default/article_show.html.twig', [
            'article' => $article,
        ]);
    }
}


