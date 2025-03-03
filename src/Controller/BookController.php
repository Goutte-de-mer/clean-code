<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BookService;
use App\Repository\BookRepository;

#[Route("/book")]
final class BookController extends AbstractController
{
    private BookService $bookService;
    private BookRepository $bookRepository;

    public function __construct(BookService $bookService, BookRepository $bookRepository)
    {
        $this->bookService = $bookService;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/available', methods: ['GET'])]
    public function getAvailableBooks(BookRepository $bookRepository): JsonResponse
    {
        $availableBooks = $bookRepository->findBy(['isBorrowed' => false]);
        if (count($availableBooks) === 0) {
            return $this->json(['message' => 'Aucun livre disponible dans la bibliothèque'], 404);
        }

        return $this->json($availableBooks, 200, [], ['groups' => 'book:read']);
    }

    #[Route('/add', methods: ['POST'])]
    public function addBook(Request $request): JsonResponse
    {
        try {
            $requestData = $request->toArray();

            // Validate required fields
            $validationErrors = $this->bookService->validateBookData($requestData);
            if (!empty($validationErrors)) {
                return $this->json([
                    'error' => $validationErrors[0],
                ], 400);
            }

            // Check if book already exists
            $existingBook = $this->bookService->bookExists($requestData['title'], $requestData['author']);
            if ($existingBook) {
                return $this->json([
                    'error' => 'Ce livre existe déjà dans la bibliothèque',
                ], 409); // 409 Conflict
            }

            // If no existing book, create a new one and save it
            $book = $this->bookService->createBook($requestData);

            return $this->json([
                'message' => 'Livre ajouté avec succès!',
                'book' => $book,
            ], 201, [], ['groups' => 'book:read']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de l\'ajout du livre',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route("/search/author/{author}", methods: ["GET"], requirements: ["author" => "[a-zA-Z\s]+"])]
    public function searchByAuthor(string $author): JsonResponse
    {
        $booksByAuthor = $this->bookRepository->findBooksByAuthorLike($author);

        if (count($booksByAuthor) === 0) {
            return $this->json(['message' => 'Aucun livre trouvé pour cet auteur'], 404);
        }

        return $this->json($booksByAuthor, 200, [], ['groups' => 'book:read']);
    }

    #[Route("/search/title/{title}", methods: ["GET"], requirements: ["title" => "[a-zA-Z\s]+"])]
    public function searchByTitle(string $title): JsonResponse
    {
        $bookByTitle = $this->bookRepository->findBooksByTitleLike($title);

        if (!$bookByTitle) {
            return $this->json(['message' => 'Aucun livre trouvé pour ce titre'], 404);
        }

        return $this->json($bookByTitle, 200, [], ['groups' => 'book:read']);
    }
}
