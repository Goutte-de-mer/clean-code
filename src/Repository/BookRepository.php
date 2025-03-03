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

    public function findByTitleAndAuthor(string $title, string $author): ?Book
    {
        return $this->findOneBy([
            'title' => $title,
            'author' => $author
        ]);
    }

    public function findBooksByAuthorLike(string $author): array
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.author) LIKE LOWER(:author)')
            ->setParameter('author', '%' . $author . '%')
            ->getQuery()
            ->getResult();
    }

    public function findBooksByTitleLike(string $title): array
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.title) LIKE LOWER(:title)')
            ->setParameter('title', '%' . $title . '%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
