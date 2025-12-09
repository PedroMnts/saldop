<?php

namespace App\Repository;

use App\Entity\Gasto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gasto>
 */
class GastoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gasto::class);
    }

    /**
     * @return Gasto[] Returns an array of Gasto objects
     */
    public function findAll(): array
    {
        return $this->findBy([], ['fecha' => 'DESC']);
    }

    /**
     * @return Gasto[] Returns gastos by category
     */
    public function findByCategoria(string $categoria): array
    {
        return $this->findBy(['categoria' => $categoria], ['fecha' => 'DESC']);
    }
}
