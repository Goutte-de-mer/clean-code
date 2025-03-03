<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $books = [
            ['title' => 'Harry Potter & the Philosopher\'s Stone', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Chamber of Secrets', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Prisoner of Azkaban', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Goblet of Fire', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Order of the Phoenix', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Half-Blood Prince', 'author' => 'J.K Rowling'],
            ['title' => 'Harry Potter & the Deathly Hallows', 'author' => 'J.K Rowling'],
            ['title' => 'Nineteen Eighty-Four', 'author' => 'George Orwell'],
            ['title' => 'Brave New World', 'author' => 'Aldous Huxley'],
            ['title' => 'Fahrenheit 451', 'author' => 'Ray Bradbury'],
            ['title' => 'L\'Étranger', 'author' => 'Albert Camus'],
            ['title' => 'Преступление и наказание', 'author' => 'Fyodor Dostoevsky'],
            ['title' => 'Les Misérables', 'author' => 'Victor Hugo'],
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen'],
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald'],
            ['title' => 'Don Quijote de la Mancha', 'author' => 'Miguel de Cervantes'],
            ['title' => 'À la recherche du temps perdu', 'author' => 'Marcel Proust'],
            ['title' => 'Moby-Dick', 'author' => 'Herman Melville'],
            ['title' => 'Les Trois Mousquetaires', 'author' => 'Alexandre Dumas'],
            ['title' => 'Ὀδύσσεια', 'author' => 'Homer'],
            ['title' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien'],
            ['title' => 'Le Petit Prince', 'author' => 'Antoine de Saint-Exupéry'],
            ['title' => 'O Alquimista', 'author' => 'Paulo Coelho'],
            ['title' => 'The Shining', 'author' => 'Stephen King'],
            ['title' => 'The Grapes of Wrath', 'author' => 'John Steinbeck'],
            ['title' => 'Dune', 'author' => 'Frank Herbert'],
        ];

        foreach ($books as $book) {
            $bookEntity = new Book();
            $bookEntity->setTitle($book['title']);
            $bookEntity->setAuthor($book['author']);
            $manager->persist($bookEntity);
        }
        $manager->flush();
    }
}
