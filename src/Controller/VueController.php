<?php

namespace App\Controller;

use App\Entity\Enchere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VueController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/test', name: 'app_vue')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'VueController',
        ]);
    }

    #[Route('/api/encheres', name: 'api_encheres_get', methods: ['GET'])]
    public function getEncheres(): JsonResponse
    {
        $encheres = $this->entityManager->getRepository(Enchere::class)->findAll();

        $data = array_map(static function (Enchere $enchere) {
            return [
                'id' => $enchere->getId(),
                'titre' => $enchere->getTitre(),
                'description' => $enchere->getDescription(),
                'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
                'statut' => $enchere->getStatut(),
                'prixDebut' => $enchere->getPrixDebut(),
            ];
        }, $encheres);

        return new JsonResponse($data);
    }
}
