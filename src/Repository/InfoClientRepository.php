<?php

namespace App\Repository;

use App\Entity\InfoClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InfoClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoClient[]    findAll()
 * @method InfoClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoClientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InfoClient::class);
    }

    // /**
    //  * @return InfoClient[] Returns an array of InfoClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfoClient
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
