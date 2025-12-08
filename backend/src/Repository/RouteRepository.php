<?php

namespace App\Repository;

use App\Entity\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class RouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function findByAnalyticCodeAndDateRange(string $analyticCode, ?DateTime $from, ?DateTime $to): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.analyticCode = :code')
            ->setParameter(':code', $analyticCode);

        if ($from !== null) {
            $qb->andWhere('r.createdAt >= :from')
                ->setParameter(':from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('r.createdAt <= :to')
                ->setParameter(':to', $to->modify('+1 day'));
        }

        return $qb->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByDateRange(?DateTime $from, ?DateTime $to): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($from !== null) {
            $qb->where('r.createdAt >= :from')
                ->setParameter(':from', $from);
        }

        if ($to !== null) {
            if ($from !== null) {
                $qb->andWhere('r.createdAt <= :to');
            } else {
                $qb->where('r.createdAt <= :to');
            }
            $qb->setParameter(':to', $to->modify('+1 day'));
        }

        return $qb->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
