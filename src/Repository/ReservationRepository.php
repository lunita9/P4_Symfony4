<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function nbBilletDate(\DateTime $dateBillet) 
    { return $this->createQueryBuilder('resa')
        ->andWhere('resa.dateBillet = :dateBillet')
        ->setParameter('dateBillet', $dateBillet)
        ->select('SUM(resa.nombreTotalTicket) as nbBilletDate')
        ->getQuery()
        ->getSingleScalarResult();

    }
}
