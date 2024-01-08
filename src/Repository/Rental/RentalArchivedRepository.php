<?php

namespace App\Repository\Rental;

use App\Entity\Rental\RentalArchived;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RentalArchived>
 *
 * @method RentalArchived|null find($id, $lockMode = null, $lockVersion = null)
 * @method RentalArchived|null findOneBy(array $criteria, array $orderBy = null)
 * @method RentalArchived[]    findAll()
 * @method RentalArchived[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentalArchivedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentalArchived::class);
    }

    //    /**
    //     * @return RentalArchived[] Returns an array of RentalArchived objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RentalArchived
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
