<?php

namespace App\Controller;

use App\Repository\NiveauRepository;
use App\Repository\SequenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartialsController extends AbstractController
{
    private NiveauRepository $niveauRepository;
    private SequenceRepository $sequenceRepository;

    /**
     * @param NiveauRepository $niveauRepository
     * @param SequenceRepository $sequenceRepository
     */
    public function __construct(NiveauRepository $niveauRepository, SequenceRepository $sequenceRepository)
    {
        $this->niveauRepository = $niveauRepository;
        $this->sequenceRepository = $sequenceRepository;
    }


    #[Route('/partials', name: 'app_partials')]
    public function index(): Response
    {
        $sequences = $this->sequenceRepository->findAll();
        $niveau = $this->niveauRepository->findAll();
        return $this->render('partials/_navigation.html.twig', [
            'sequences' => $sequences,
            'niveau' => $niveau,
        ]);
    }
}
