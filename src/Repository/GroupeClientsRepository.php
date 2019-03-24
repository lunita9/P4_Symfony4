<?php

namespace App\Repository;

use App\Entity\GroupeClients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GroupeClients|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeClients|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeClients[]    findAll()
 * @method GroupeClients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeClientsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GroupeClients::class);
    }

    // /**
    //  * @return GroupeClients[] Returns an array of GroupeClients objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupeClients
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
