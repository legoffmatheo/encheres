<?php

namespace App\Repository;

use App\Entity\Enchere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enchere>
 */
class EnchereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enchere::class);
    }
    public function findEncheresAVenir(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.dateHeureDebut > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateHeureDebut', 'ASC');
    
        $encheres = $qb->getQuery()->getResult();
    
        // Transformer les objets en tableau associatif
        return array_map(function ($enchere) {
            return [
                'id' => $enchere->getId(),
                'titre' => $enchere->getTitre(),
                'description' => $enchere->getDescription(),
                'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
            ];
        }, $encheres);
    }

    public function findEncheresTermineesRecemment(): array
{
    $now = new \DateTime();
    $pastLimit = (clone $now)->modify('-15 days');

    $qb = $this->createQueryBuilder('e')
        ->where('e.dateHeureFin <= :now')
        ->andWhere('e.dateHeureFin > :pastLimit')
        ->setParameter('now', $now)
        ->setParameter('pastLimit', $pastLimit)
        ->orderBy('e.dateHeureFin', 'DESC');

    $encheres = $qb->getQuery()->getResult();

    // Transformer les objets en tableau associatif
    return array_map(function ($enchere) {
        return [
            'id' => $enchere->getId(),
            'titre' => $enchere->getTitre(),
            'description' => $enchere->getDescription(),
            'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
            'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
        ];
    }, $encheres);
}


public function findEncheresEnCoursSansParticipation($user): array
{
    $now = new \DateTime();

    $qb = $this->createQueryBuilder('e')
        ->select('e') // Sélectionner uniquement l'entité Enchere
        ->leftJoin('e.lesParticipations', 'p') // Relation OneToMany
        ->andWhere('e.dateHeureDebut <= :now')
        ->andWhere('e.dateHeureFin > :now')
        ->andWhere('(p.leUser IS NULL OR p.leUser != :user)') // Vérifier la propriété correcte
        ->setParameter('now', $now)
        ->setParameter('user', $user)
        ->orderBy('e.dateHeureDebut', 'ASC');

    $encheres = $qb->getQuery()->getResult();

    return array_map(function ($enchere) {
        if ($enchere instanceof Enchere) {
            return [
                'id' => $enchere->getId(),
                'titre' => $enchere->getTitre(),
                'description' => $enchere->getDescription(),
                'dateHeureDebut' => $enchere->getDateHeureDebut()->format('Y-m-d H:i:s'),
                'dateHeureFin' => $enchere->getDateHeureFin()->format('Y-m-d H:i:s'),
            ];
        }
        throw new \UnexpectedValueException('Résultat inattendu pour Enchere.');
    }, $encheres);
}

public function findEncheresEnCours(\DateTime $now): array
{
    return $this->createQueryBuilder('e')
        ->where('e.dateHeureDebut <= :now')
        ->andWhere('e.dateHeureFin >= :now')
        ->setParameter('now', $now)
        ->getQuery()
        ->getResult();
}
    //    /**
    //     * @return Enchere[] Returns an array of Enchere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Enchere
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
