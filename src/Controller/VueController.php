<?php

namespace App\Controller;

use App\Entity\Enchere;
use App\Entity\Produit;  
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
                'dateHeureDebut' => $enchere->getDateHeureDebut()?->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()?->format('Y-m-d H:i:s'),
                'statut' => $enchere->getStatut(),
                'prixDebut' => $enchere->getPrixDebut(),
                'produit' => $enchere->getLeProduit()?->getLibelle(), // Ajout du libelle du produit
            ];
        }, $encheres);
    
        return new JsonResponse($data);
    }
    

    #[Route('/api/enchere/add', name: 'app_api_encheres_add', methods: ['POST'])]
    public function addEnchere(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            // Recherche du produit associé
            $produit = $this->entityManager->getRepository(Produit::class)->find($data['produitId']);
    
            if (!$produit) {
                return new JsonResponse(['error' => 'Produit introuvable'], Response::HTTP_BAD_REQUEST);
            }
    
            $enchere = new Enchere();
            $enchere->setTitre($data['titre']);
            $enchere->setDescription($data['description']);
            $enchere->setDateHeureDebut(new \DateTime($data['dateHeureDebut']));
            $enchere->setDateHeureFin(new \DateTime($data['dateHeureFin']));
            $enchere->setStatut($data['statut']);
            $enchere->setPrixDebut($data['prixDebut']);
            $enchere->setLeProduit($produit); // Assignation du produit
    
            $this->entityManager->persist($enchere);
            $this->entityManager->flush();
    
            return $this->json(['message' => 'Enchère créée avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/enchere/delete/{id}', name: 'app_api_delete', methods: ['DELETE'])]
    public function deleteEnchere(int $id): JsonResponse
    {
        $enchere = $this->entityManager->getRepository(Enchere::class)->find($id);

        if (!$enchere) {
            return new JsonResponse(['error' => 'Enchère introuvable'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($enchere);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Enchère supprimée avec succès']);
    }

    #[Route('/api/enchere/update/{id}', name: 'app_api_enchere_update', methods: ['PUT'])]
    public function updateEnchere(int $id, Request $request): JsonResponse
    {
        $enchere = $this->entityManager->getRepository(Enchere::class)->find($id);

        if (!$enchere) {
            return new JsonResponse(['error' => 'Enchère introuvable'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }
        
        
   // Récupérer l'ID du produit dans les données envoyées
    $produit = $this->entityManager->getRepository(Produit::class)->find($data['produitId']);
    if (!$produit) {
     return new JsonResponse(['error' => 'Produit introuvable'], Response::HTTP_BAD_REQUEST);
    }



        try {
            $enchere->setTitre($data['titre']);
            $enchere->setDescription($data['description']);
            $enchere->setDateHeureDebut(new \DateTime($data['dateHeureDebut']));
            $enchere->setDateHeureFin(new \DateTime($data['dateHeureFin']));
            $enchere->setStatut($data['statut']);
            $enchere->setPrixDebut($data['prixDebut']);
            $enchere->setLeProduit($produit); // Assignation du produit


            $this->entityManager->flush();

            return $this->json(['message' => 'Enchère mise à jour avec succès.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/produits', name: 'api_produits_get', methods: ['GET'])]
    public function getProduits(): JsonResponse
    {
        $produits = $this->entityManager->getRepository(Produit::class)->findAll();

        $data = array_map(static function (Produit $produit) {
            return [
                'id' => $produit->getId(),
                'libelle' => $produit->getLibelle(),
                'description' => $produit->getDescription(),
                'prixPlancher' => $produit->getPrixPlancher(),
            ];
        }, $produits);

        return new JsonResponse($data);
    }

    #[Route('/api/produit/add', name: 'api_produit_add', methods: ['POST'])]
    public function addProduit(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $produit = new Produit();
            $produit->setLibelle($data['libelle']);
            $produit->setDescription($data['description']);
            $produit->setPrixPlancher($data['prixPlancher']);

            $this->entityManager->persist($produit);
            $this->entityManager->flush();

            return $this->json(['message' => 'Produit créé avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/produit/delete/{id}', name: 'api_produit_delete', methods: ['DELETE'])]
    public function deleteProduit(int $id): JsonResponse
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['error' => 'Produit introuvable'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Produit supprimé avec succès']);
    }

    #[Route('/api/produit/update/{id}', name: 'api_produit_update', methods: ['PUT'])]
    public function updateProduit(int $id, Request $request): JsonResponse
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['error' => 'Produit introuvable'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $produit->setLibelle($data['libelle']);
            $produit->setDescription($data['description']);
            $produit->setPrixPlancher($data['prixPlancher']);

            $this->entityManager->flush();

            return $this->json(['message' => 'Produit mis à jour avec succès.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
