<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private EntityManagerInterface $entityManager;
    private BookRepository $bookRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository
    ) {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    public function validateBookData(array $data): array
    {
        $errors = [];

        if (!isset($data['title']) || empty($data['title'])) {
            $errors[] = 'Le titre du livre est requis';
        }

        if (!isset($data['author']) || empty($data['author'])) {
            $errors[] = 'L\'auteur du livre est requis';
        }

        return $errors;
    }

    public function bookExists(string $title, string $author): bool
    {
        $existingBook = $this->bookRepository->findByTitleAndAuthor($title, $author);
        return $existingBook !== null;
    }

    public function createBook(array $data): Book
    {
        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }
}
