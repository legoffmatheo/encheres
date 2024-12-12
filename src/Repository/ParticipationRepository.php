<?php

namespace App\Repository;
use App\Entity\User;
use App\Entity\Enchere;
use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participation>
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function findEncheresEnCoursByUser($user): array
{
    $now = new \DateTime();

    $qb = $this->createQueryBuilder('p')
        ->join('p.laEnchere', 'e') // Relation vers l'enchère
        ->where('e.dateHeureDebut <= :now')
        ->andWhere('e.dateHeureFin > :now')
        ->andWhere('p.leUser = :user') // Filtrer par utilisateur
        ->setParameter('now', $now)
        ->setParameter('user', $user)
        ->orderBy('e.dateHeureDebut', 'ASC');

    try {
        $participations = $qb->getQuery()->getResult();

        if (empty($participations)) {
            return []; // Aucun résultat
        }

        // Transformer les participations en tableau associatif pour inclure les enchères
        return array_map(function ($participation) {
            $enchere = $participation->getLaEnchere();
            if ($enchere instanceof Enchere) {
                return [
                    'id' => $participation->getId(),
                    'titre' => $enchere->getTitre(),
                    'description' => $enchere->getDescription(),
                    'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                    'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
                    'budgetMaximum' => $participation->getBudgetMaximum(), // Inclure le budget maximum
                ];
            }
            throw new \UnexpectedValueException('Résultat inattendu dans les participations.');
        }, $participations);

    } catch (\Exception $e) {
        error_log('Erreur Doctrine : ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des enchères en cours : ' . $e->getMessage());
    }
}


    //    /**
    //     * @return Participation[] Returns an array of Participation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Participation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
