<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Recherche un livre par titre et auteur exacts.
     *
     * @param string $title
     * @param string $author
     * @return Book|null Renvoie un livre ou null si aucun résultat.
     */
    public function findByTitleAndAuthor(string $title, string $author): ?Book
    {
        return $this->findOneBy([
            'title' => $title,
            'author' => $author
        ]);
    }

    /**
     * Recherche les livres dont l'auteur contient une chaîne spécifique (insensible à la casse).
     *
     * @param string $author
     * @return array Liste des livres correspondant au critère de recherche.
     */
    public function findBooksByAuthorLike(string $author): array
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.author) LIKE LOWER(:author)')
            ->setParameter('author', '%' . $author . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les livres dont le titre contient une chaîne spécifique (insensible à la casse).
     *
     * @param string $title
     * @return array Liste des livres correspondant au critère de recherche.
     */
    public function findBooksByTitleLike(string $title): array
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.title) LIKE LOWER(:title)')
            ->setParameter('title', '%' . $title . '%')
            ->getQuery()
            ->getResult();
    }
}
