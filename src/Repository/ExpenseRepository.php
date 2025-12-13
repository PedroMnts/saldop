<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @return Expense[] Returns an array of Expense objects
     */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'DESC']);
    }

    /**
     * @return Expense[] Returns expenses by category
     */
    public function findByCategory(string $category): array
    {
        return $this->findBy(['category' => $category], ['date' => 'DESC']);
    }

    /**
     * @return Expense[] Returns expenses within a date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date >= :startDate')
            ->andWhere('e.date <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
