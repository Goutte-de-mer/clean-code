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

    /**
     * Valide les données d'un livre avant création.
     *
     * @param array $data
     * @return array Retourne un tableau d'erreurs, vide si aucune erreur.
     */
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

    /**
     * Vérifie si un livre existe déjà dans la base de données.
     *
     * @param string $title Titre du livre
     * @param string $author Auteur du livre
     * @return bool Retourne true si le livre existe, sinon false.
     */
    public function bookExists(string $title, string $author): bool
    {
        $existingBook = $this->bookRepository->findByTitleAndAuthor($title, $author);
        return $existingBook !== null;
    }

    /**
     * Crée un nouveau livre et l'enregistre en base de données.
     *
     * @param array $data Données du livre
     * @return Book Le livre créé.
     */
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
