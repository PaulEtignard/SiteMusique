<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\NiveauRepository;
use App\Repository\SequenceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private NiveauRepository $niveauRepository;
    private SequenceRepository $sequenceRepository;

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository, NiveauRepository $niveauRepository, SequenceRepository $sequenceRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->niveauRepository = $niveauRepository;
        $this->sequenceRepository = $sequenceRepository;
    }


    #[Route('/articles', name: 'app_articles')]
    public function index(PaginatorInterface $paginator,Request $request): Response
    {

        $articles = $paginator->paginate(
            $this->articleRepository->findBy([],["createdAt"=>"desc"]),
            $request->query->getInt("page",1),
            9
        );

        $niveaux = $this->niveauRepository->findAll();
        $nbcours = count($this->articleRepository->findBy([],["createdAt"=>"desc"]));


        return $this->render('articles/index.html.twig', [
            'nbcours'=>$nbcours,
            'articles' => $articles,
            'niveau' => $niveaux,
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_articles_niveau')]
    public function articleniveau(PaginatorInterface $paginator, Request $request,$slug): Response
    {

        $niveau = $this->niveauRepository->findOneBy(["slug"=>$slug]);
        $sequence = $this->sequenceRepository->findBy(["niveau"=>$niveau]);
        $articles = $paginator->paginate(
            $this->articleRepository->findBy(["sequence"=>$sequence],["createdAt"=>"desc"]),
            $request->query->getInt("page",1),
            9
        );
        $nbcours = count($this->articleRepository->findBy(["sequence"=>$sequence],["createdAt"=>"desc"]));

        $niveaux = $this->niveauRepository->findAll();


        return $this->render('articles/articleniveau.html.twig', [
            'nbcours'=>$nbcours,
            'articles' => $articles,
            'niveau' => $niveaux,
            'niveaux'=>$niveau,
        ]);
    }
    #[Route('/articles/{slug}/{sequence}', name: 'app_articles_niveau_sequence')]
    public function articleniveausequence(PaginatorInterface $paginator, Request $request,$slug,$sequence): Response
    {

        $niveau = $this->niveauRepository->findBy(["slug"=>$slug]);
        $sequenceob = $this->sequenceRepository->findBy(["niveau"=>$niveau,"slug"=>$sequence]);
        $articles = $paginator->paginate(
            $this->articleRepository->findBy(["sequence"=>$sequenceob],["createdAt"=>"desc"]),
            $request->query->getInt("page",1),
            9
        );
        $nbcours = count($this->articleRepository->findBy(["sequence"=>$sequenceob],["createdAt"=>"desc"]));

        $niveaux = $this->niveauRepository->findAll();


        return $this->render('articles/articleniveausequence.html.twig', [
            "sequence"=>$sequenceob,
            'nbcours'=>$nbcours,
            'articles' => $articles,
            'niveau' => $niveaux,
        ]);
    }
    #[Route('/article/{slug}', name: 'app_article_slug')]
    public function article($slug): Response
    {

        $article = $this->articleRepository->findOneBy(["slug"=>$slug]);

        $niveaux = $this->niveauRepository->findAll();

        return $this->render('articles/article.html.twig', [
            'niveau' => $niveaux,
            'article' =>$article
        ]);
    }
}
