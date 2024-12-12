<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Repository\EnchereRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/encheres-a-venir', name: 'get_encheres_a_venir')]
    public function getEncheresAVenir( Request $request, EnchereRepository $enchereRepository, Utils $utils ): Response 
    {
        // Récupérer les enchères à venir via le repository
        $encheresAVenir = $enchereRepository->findEncheresAVenir();

        // Utilisation de Utils pour générer la réponse JSON
        return $utils->GetJsonResponse($request, $encheresAVenir);
    }
    #[Route('/api/check-auth', name: 'check_auth')]
public function checkAuth(SessionInterface $session): Response
{
    $user = $session->get('user');
    return $this->json([
        'isAuthenticated' => $user !== null,
    ]);
}

    #[Route('/api/encheres-terminees-recemment', name: 'get_encheres_terminees_recemment')]
    public function getEncheresTermineesRecemment(EnchereRepository $enchereRepository,  Request $request, Utils $utils): Response 
    {
        // Récupérer les enchères terminées récemment via le repository
        $encheresTerminees = $enchereRepository->findEncheresTermineesRecemment();

        // Utilisation de Utils pour générer la réponse JSON
        return $utils->GetJsonResponse( $request, $encheresTerminees);
    }

    #[Route('/api/encheres-en-cours', name: 'get_encheres_en_cours')]
    public function getEncheresEnCours(
        EnchereRepository $enchereRepository,
        Request $request,
        Utils $utils,
        SessionInterface $session,
        UserRepository $userRepository
    ): Response {
        // Récupérer l'utilisateur connecté via la session
        $userId = $session->get('user');
        if (!$userId) {
            return $utils->ErrorCustom('Utilisateur non connecté.');
        }
    
        $user = $userRepository->find($userId);
        if (!$user) {
            return $utils->ErrorCustom('Utilisateur introuvable.');
        }
    
        // Récupérer les enchères en cours auxquelles l'utilisateur ne participe pas
        $encheresEnCours = $enchereRepository->findEncheresEnCoursSansParticipation($user);
    
        return $utils->GetJsonResponse($request, $encheresEnCours);
    }
    #[Route('/api/encheres-en-cours-user', name: 'get_encheres_en_cours_user')]
    public function getEncheresEnCoursUser(
        SessionInterface $session,
        ParticipationRepository $participationRepository,
        Request $request,
        Utils $utils
    ): Response {
        // Récupérer l'utilisateur actuellement connecté
        $user = $session->get('user');

        if (!$user) {
            return $utils->ErrorCustom('Utilisateur non connecté.');
        }

        // Récupérer lesencherir enchères en cours pour cet utilisateur
        $encheresEnCours = $participationRepository->findEncheresEnCoursByUser($user);

        // Utilisation de Utils pour générer la réponse JSON
        return $utils->GetJsonResponse($request, $encheresEnCours);
    }
    #[Route('/api/verifier-gain-enchere', name: 'verifier_gain_enchere', methods: ['POST'])]
    public function verifierGainEnchere(
        EnchereRepository $enchereRepository,
        ParticipationRepository $participationRepository,
        Utils $utils,
        Request $request
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $utils->ErrorCustom('Utilisateur non connecté.');
        }

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['idEnchere'])) {
            return $utils->ErrorMissingArguments();
        }

        $idEnchere = $data['idEnchere'];

        // Récupérer l'enchère correspondante
        $enchere = $enchereRepository->find($idEnchere);
        if (!$enchere) {
            return $utils->ErrorCustom('Enchère non trouvée.');
        }

        // Vérifier si l'enchère est terminée
        $now = new \DateTime();
        if ($enchere->getDateHeureFin() > $now) {
            return $utils->ErrorCustom('L\'enchère n\'est pas encore terminée.');
        }

        // Récupérer toutes les participations à cette enchère, triées par montant décroissant
        $participations = $participationRepository->findBy(
            ['laEnchere' => $enchere],
            ['prixEncheri' => 'DESC']
        );

        if (empty($participations)) {
            return $utils->ErrorCustom('Aucune participation pour cette enchère.');
        }

        // Identifier le gagnant et vérifier si l'utilisateur est le gagnant
        $gagnant = $participations[0];
        $montantGagnant = $gagnant->getPrixEncheri();

        foreach ($participations as $participation) {
            if ($participation->getLeUser() === $user) {
                if ($participation === $gagnant) {
                    // L'utilisateur est le gagnant
                    return $utils->GetJsonResponse($request, [
                        'resultat' => 'oui',
                        'montant' => $montantGagnant,
                    ]);
                } else {
                    // L'utilisateur a perdu
                    $difference = $montantGagnant - $participation->getPrixEncheri();
                    return $utils->GetJsonResponse($request, [
                        'resultat' => 'non',
                        'difference' => $difference,
                    ]);
                }
            }
        }

        // L'utilisateur n'a pas participé
        return $utils->ErrorCustom('Vous n\'avez pas participé à cette enchère.');
    }

    #[Route('/api/user-info', name: 'get_user_info', methods: ['GET'])]
    public function getUserInfo(Request $request,Utils $utils): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            return $utils->ErrorCustom('Utilisateur non connecté.');
        }

        // Préparer les informations utilisateur
        $userInfo = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
        ];

        // Retourner les informations utilisateur en JSON
        return $utils->GetJsonResponse($request, $userInfo);
    }

    #[Route('/api/encherir', name: 'api_encherir', methods: ['POST'])]
    public function encherir(
        SessionInterface $session,
        UserRepository $userRepository, 
        Request $request,
        EnchereRepository $enchereRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $data = json_decode($request->getContent(), true);
        $enchereId =  $data['idEnchere'] ?? null;
        $montant = $data['budgetMaximum'] ?? null;

        $userId = $session->get('user');
        $user = $userRepository->find($userId);
        if (!$user) {
            return new Response('Utilisateur introuvable.', Response::HTTP_NOT_FOUND);
        }
    

        if (!$enchereId || !$montant || $montant <= 0) {
            return new Response('Données invalides.', Response::HTTP_BAD_REQUEST);
        }

        $enchere = $enchereRepository->find($enchereId);
        if (!$enchere) {
            return new Response('enchere introuvable.', Response::HTTP_NOT_FOUND);
        }

        $participation = new Participation();

        // Mettez à jour le montant enchéri
        $participation->setBudgetMaximum($montant);
        $participation->setPrixEncheri(0);
        $participation->setLaEnchere($enchere);
        $participation->setLeUser($user);
        $entityManager->persist($participation);
        $entityManager->flush();

        return $this->json(['message' => 'Enchère soumise avec succès.'], Response::HTTP_OK);
    }

    #[Route('/api/update-prix-encheri', name: 'update_prix_encheri', methods: ['POST'])]
    public function updatePrixEncheri(
        ParticipationRepository $participationRepository,
        UserRepository $userRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        Utils $utils,
        Request $request
    ): Response {
        // Récupérer l'utilisateur connecté
        $userId = $session->get('user');
        $user = $userRepository->find($userId);
        if (!$user) {
            return new Response('Utilisateur introuvable.', Response::HTTP_NOT_FOUND);
        }
    

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['participationId']) || !isset($data['montant'])) {
            return $utils->ErrorMissingArguments();
        }

        $idParticipation = $data['participationId'];
        $nouveauPrixEncheri = $data['montant'];

        // Récupérer la participation correspondante
        $participation = $participationRepository->find($idParticipation);
        if (!$participation) {
            return $utils->ErrorCustom('Participation non trouvée.');
        }

        // Vérifier que l'utilisateur est bien associé à cette participation
        if ($participation->getLeUser() !== $user) {
            return $utils->ErrorCustom('Vous n\'êtes pas autorisé à modifier cette participation.');
        }

        // Récupérer l'enchère associée
        $enchere = $participation->getLaEnchere();
        if (!$enchere) {
            return $utils->ErrorCustom('Enchère associée introuvable.');
        }

        // Vérifier que l'enchère n'est pas terminée
        $now = new \DateTime();
        if ($enchere->getDateHeureFin() <= $now) {
            return $utils->ErrorCustom('L\'enchère est déjà terminée.');
        }

        // Vérifier le prix actuel le plus élevé pour l'enchère
        $participations = $participationRepository->findBy(
            ['laEnchere' => $enchere],
            ['prixEncheri' => 'DESC']
        );
        $prixLePlusHaut = $participations[0]->getPrixEncheri();

        if ($nouveauPrixEncheri <= $prixLePlusHaut) {
            return $utils->GetJsonResponse($request, [
                'resultat' => 'non',
                'message' => 'Votre enchère est inférieure ou égale au prix actuel le plus élevé.',
            ]);
        }

        // Mettre à jour le prix d'enchère
        $participation->setPrixEncheri($nouveauPrixEncheri);
        $entityManager->persist($participation);
        $entityManager->flush();

        // Retourner une réponse de succès
        return $utils->GetJsonResponse($request, [
            'resultat' => 'oui',
            'message' => 'Mise à jour effectuée avec succès.',
        ]);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        SessionInterface $session,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
        
        ): Response {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['access' => 'refusé', 'message' => 'Paramètres manquants'], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        // Récupérer l'utilisateur via l'email
        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['access' => 'refusé', 'message' => 'Utilisateur non trouvé'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier le mot de passe
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['access' => 'refusé', 'message' => 'Mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        // Connexion réussie
        $session->set('user', $user->getId());
        return $this->json(['access' => 'autorisé', 'message' => 'Connexion réussie'], Response::HTTP_OK);
    }

    /**
 * @Route("/api/encheres-en-cours-prix", name="encheres_en_cours_prix", methods={"GET"})
 */
public function getPrixEncheresEnCours(EnchereRepository $enchereRepository)
{
    $now = new \DateTime();

    // Récupérer les enchères en cours
    $encheres = $enchereRepository->findEncheresEnCours($now);

    $data = [];
    foreach ($encheres as $enchere) {
        $data[] = [
            'id' => $enchere->getId(),
            'titre' => $enchere->getTitre(),
            'prixPlusHaut' => $enchere->getPrixPlusHaut(), // Méthode pour obtenir le prix
        ];
    }

    return $this->json($data);
}
#[Route('/api/encheres-en-cours-prix', name: 'encheres_en_cours_prix', methods: ['GET'])]
public function getPrixEncheresEnCoursPrix(EnchereRepository $enchereRepository): JsonResponse
{
    $now = new \DateTime();

    // Récupérer les enchères en cours
    $encheres = $enchereRepository->findEncheresEnCours($now);

    $data = array_map(static function ($enchere) {
        return [
            'id' => $enchere->getId(),
            'titre' => $enchere->getTitre(),
            'prixPlusHaut' => $enchere->getPrixPlusHaut(),
        ];
    }, $encheres);

    return $this->json($data);
}

}
