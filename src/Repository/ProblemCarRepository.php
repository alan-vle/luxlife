<?php

namespace App\Repository;

use App\Entity\ProblemCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProblemCar>
 *
 * @method ProblemCar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProblemCar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProblemCar[]    findAll()
 * @method ProblemCar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblemCarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProblemCar::class);
    }

    //    /**
    //     * @return ProblemCar[] Returns an array of ProblemCar objects
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

    //    public function findOneBySomeField($value): ?ProblemCar
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
