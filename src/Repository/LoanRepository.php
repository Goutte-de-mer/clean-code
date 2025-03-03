<?php

namespace App\Repository;

use App\Entity\Loan;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    // countCurrentLoansByUserId
    public function countCurrentLoansByUserId(int $userId): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.borrower = :userId AND l.actualReturnDate IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCurrentLoansByBookAndUser(int $bookId, int $userId): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->where('l.book = :bookId')
            ->andWhere('l.actualReturnDate IS NULL')
            ->andWhere('l.borrower = :userId')
            ->setParameter('bookId', $bookId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }



    //    /**
    //     * @return Loan[] Returns an array of Loan objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Loan
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
