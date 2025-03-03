<?php

namespace App\Service;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Entity\User;

class LoanService
{
    private EntityManagerInterface $entityManager;
    private LoanRepository $loanRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoanRepository $loanRepository
    ) {
        $this->entityManager = $entityManager;
        $this->loanRepository = $loanRepository;
    }

    // Check if user has exceeded its maximum amount of loan requests
    public function canLoanBook(int $userId): bool
    {
        $currentLoans = $this->loanRepository->countCurrentLoansByUserId($userId);
        $maxLoans = 3;
        if ($currentLoans == $maxLoans) {
            return false;
        }
        return true;
    }

    // Create a new loan
    public function loanBook(int $userId, int $bookId): Loan
    {
        $borrowedAt = new \DateTimeImmutable();
        $expectedReturnDate = $borrowedAt->modify('+2 weeks');

        $book = $this->entityManager->getRepository(Book::class)->find($bookId);
        $borrower = $this->entityManager->getRepository(User::class)->find($userId);


        $loan = new Loan();
        $loan->setBorrower($borrower);
        $loan->setBook($book);
        $loan->setBorrowedAt($borrowedAt);
        $loan->setExpectedReturnDate($expectedReturnDate);

        // Mark the book as borrowed
        $book->setIsBorrowed(true);

        $this->entityManager->persist($loan);
        $this->entityManager->flush();
        return $loan;
    }
}
