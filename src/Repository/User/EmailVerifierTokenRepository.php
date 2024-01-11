<?php

namespace App\Repository\User;

use App\Entity\User\EmailVerifierToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVerifierToken>
 *
 * @method EmailVerifierToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailVerifierToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailVerifierToken[]    findAll()
 * @method EmailVerifierToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailVerifierTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerifierToken::class);
    }

    //    /**
    //     * @return EmailVerifierToken[] Returns an array of EmailVerifierToken objects
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

    //    public function findOneBySomeField($value): ?EmailVerifierToken
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
