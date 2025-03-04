<?php

namespace App\Repository;

use App\Entity\Loan;
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

    /**
     * Compte le nombre d'emprunts actifs d'un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @return int Nombre d'emprunts en cours
     */
    public function countCurrentLoansByUserId(int $userId): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.borrower = :userId AND l.actualReturnDate IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve un emprunt actif pour un livre donné et un utilisateur donné.
     *
     * @param int $bookId ID du livre
     * @param int $userId ID de l'utilisateur
     * @return Loan|null Renvoie l'emprunt si trouvé, sinon null
     */
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
}
