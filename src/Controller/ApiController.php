<?php

namespace App\Controller;

use App\Entity\Enchere;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/getEncheres', name: 'app_api_encheres_get_all', methods: ['GET'])]
    public function getAllEncheres(): JsonResponse
    {
        return $this->fetchAllEncheres();
    }

    private function fetchAllEncheres(): JsonResponse
    {
        $encheres = $this->entityManager->getRepository(Enchere::class)->findAll();

        $data = [];

        foreach ($encheres as $enchere) {
            $data[] = [
                'id' => $enchere->getId(),
                'name' => $enchere->getTitre(),
                'description' => $enchere->getDescription(),
                'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
                'statut' => $enchere->getStatut(),
                'leProduitId' => $enchere->getLeProduit()->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/getEncheresEnCour', name: 'app_api_encheres_En_Cour_get_all', methods: ['GET'])]
    public function getAllEncheresEnCour(): JsonResponse
    {
        return $this->fetchAllEncheresEnCour();
    }

    private function fetchAllEncheresEnCour(): JsonResponse
    {
        $encheres = $this->entityManager->getRepository(Enchere::class)->findAll();

        $data = [];

        foreach ($encheres as $enchere) {
            $data[] = [
                'id' => $enchere->getId(),
                'name' => $enchere->getTitre(),
                'description' => $enchere->getDescription(),
                'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
                'statut' => $enchere->getStatut(),
                'leProduitId' => $enchere->getLeProduit()->getId(),
            ];
        }

        return new JsonResponse($data);
    }
}
