<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;
use App\Service\LoanService;
use App\Service\UserService;
use App\Repository\LoanRepository;


/**
 * Contrôleur gérant les emprunts et les retours de livres.
 * Il expose des endpoints pour emprunter, retourner et lister les emprunts en cours.
 */
final class LoanController extends AbstractController
{
    private LoanService $loanService;
    private UserService $userService;
    private BookRepository $bookRepository;

    /**
     * Injecte les services nécessaires dans le contrôleur.
     */
    public function __construct(LoanService $loanService, UserService $userService, BookRepository $bookRepository)
    {
        $this->loanService = $loanService;
        $this->userService = $userService;
        $this->bookRepository = $bookRepository;
    }


    /**
     * Endpoint permettant d'emprunter un livre.
     *
     * @Route("/borrow", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/borrow', methods: ['POST'])]
    public function borrowBook(Request $request): JsonResponse
    {
        try {

            $requestData = $request->toArray();

            $book = $this->bookRepository->find($requestData['book_id']);

            if (!$book) {
                return $this->json([
                    'error' => 'Livre introuvable',
                ], 404);
            }

            // Check if user exists
            if (!$this->userService->userExists($requestData['user_id'])) {
                return $this->json([
                    'error' => 'Utilisateur introuvable',
                ], 404);
            }

            // Check if book is available
            if ($book->isBorrowed()) {
                return $this->json([
                    'error' => 'Le livre est déjà emprunté',
                ], 409);
            }

            if (!$this->loanService->canLoanBook($requestData['user_id'])) {
                return $this->json([
                    'error' => 'Vous avez atteint la limite de location de livre',
                ], 403);
            }

            $loan = $this->loanService->loanBook($requestData['user_id'], $requestData['book_id']);
            return $this->json([
                'message' => 'Location du livre effectuée',
                'loan' => $loan,
            ], 201, [], ['groups' => 'loan:read']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de l\'emprunt du livre',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint permettant de retourner un livre et de gérer les pénalités de retard.
     *
     * @Route("/return", methods={"PUT"})
     * @param LoanRepository $loanRepository
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/return', methods: ['PUT'])]
    public function returnBook(LoanRepository $loanRepository, Request $request): JsonResponse
    {
        try {
            $requestData = $request->toArray();

            $loan = $loanRepository->findCurrentLoansByBookAndUser($requestData['book_id'], $requestData['user_id']);

            if (!$loan) {
                return $this->json([
                    'error' => 'Emprunt introuvable',
                ], 404);
            }

            if ($loan->getActualReturnDate() !== null) {
                return $this->json([
                    'error' => 'Le livre a déjà été retourné',
                ], 400);
            }

            $updatedLoan = $this->loanService->returnBook($loan);
            $lateFees = $this->loanService->findLateFees($loan);
            return $this->json([
                'message' => 'Livre retourné avec succès',
                'loan' => $updatedLoan,
                'late_fees' => $lateFees . '€',
            ], 200, [], ['groups' => 'loan:read']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors du retour du livre',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint permettant de récupérer tous les emprunts en cours.
     *
     * @Route("/loans", methods={"GET"})
     * @param LoanRepository $loanRepository
     * @return JsonResponse
     */
    #[Route('/loans', methods: ['GET'])]
    public function getCurrentLoans(LoanRepository $loanRepository): JsonResponse
    {
        $loans = $loanRepository->findBy(['actualReturnDate' => null]);

        if (count($loans) === 0) {
            return $this->json(['message' => 'Aucun emprunt actuel'], 200);
        }

        return $this->json($loans, 200, [], ['groups' => 'loan:read']);
    }
}
