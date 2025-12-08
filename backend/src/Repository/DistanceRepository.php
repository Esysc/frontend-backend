<?php

namespace App\Repository;

use App\Entity\Distance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DistanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distance::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.lineName', 'ASC')
            ->addOrderBy('d.fromStationId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByStations(string $from, string $to): ?Distance
    {
        return $this->createQueryBuilder('d')
            ->where('d.fromStationId = :from AND d.toStationId = :to')
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
