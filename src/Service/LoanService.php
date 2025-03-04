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

    /**
     * Vérifie si un utilisateur peut encore emprunter un livre.
     *
     * @param int $userId ID de l'utilisateur
     * @return bool Retourne true si l'utilisateur peut emprunter, sinon false.
     */
    public function canLoanBook(int $userId): bool
    {
        $currentLoans = $this->loanRepository->countCurrentLoansByUserId($userId);
        $maxLoans = 3;
        if ($currentLoans == $maxLoans) {
            return false;
        }
        return true;
    }

    /**
     * Crée un nouvel emprunt pour un livre et un utilisateur donnés.
     *
     * @param int $userId ID de l'utilisateur
     * @param int $bookId ID du livre
     * @return Loan L'objet Loan créé.
     */
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

    /**
     * Retourne un livre emprunté et met à jour son statut.
     *
     * @param Loan $loan L'objet Loan correspondant à l'emprunt.
     * @return Loan L'objet Loan mis à jour.
     * @throws \Exception Si le livre est déjà marqué comme disponible.
     */
    public function returnBook(Loan $loan): Loan
    {
        $book = $loan->getBook();
        if (!$book->isBorrowed()) {
            throw new \Exception('Le livre est déjà marqué comme disponible.');
        }
        $book->setIsBorrowed(false);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $loan->setActualReturnDate(new \DateTimeImmutable());
        $this->entityManager->persist($loan);
        $this->entityManager->flush();
        return $loan;
    }

    /**
     * Calcule les frais de retard pour un emprunt donné.
     *
     * @param Loan $loan L'objet Loan à évaluer.
     * @return int Montant des frais de retard en euros.
     */
    public function findLateFees(Loan $loan): int
    {
        $dueDate = $loan->getExpectedReturnDate();

        // Convertir en DateTimeImmutable si ce n'est pas déjà le cas
        if (!$dueDate instanceof \DateTimeImmutable) {
            $dueDate = \DateTimeImmutable::createFromMutable($dueDate);
        }

        // A minuit pour ignorer l'heure et ne compter que par jours de retard
        $dueDate = $dueDate->setTime(0, 0, 0);
        $now = (new \DateTimeImmutable())->setTime(0, 0, 0);

        if ($now > $dueDate) {
            $daysLate = $dueDate->diff($now)->days; // Nombre de jours de retard
            return $daysLate * 5; // 5€ par jour de retard
        }

        return 0;
    }
}
