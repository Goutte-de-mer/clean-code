<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;
use App\Service\LoanService;
use App\Service\UserService;

final class LoanController extends AbstractController
{
    private LoanService $loanService;
    private UserService $userService;

    public function __construct(LoanService $loanService, UserService $userService)
    {
        $this->loanService = $loanService;
        $this->userService = $userService;
    }



    #[Route('/borrow', methods: ['POST'])]
    public function borrowBook(BookRepository $bookRepository, Request $request): JsonResponse
    {
        try {

            $requestData = $request->toArray();

            $book = $bookRepository->find($requestData['book_id']);

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
}
