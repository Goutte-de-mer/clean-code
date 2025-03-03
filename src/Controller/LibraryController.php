<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/library')]
class LibraryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/add-book', methods: ['POST'])]
    public function addBook(Request $req): JsonResponse
    {
        $data1 = $req->toArray();

        if (!isset($data1['t']) || !isset($data1['a'])) {
            return new JsonResponse(['error' => 'Informations incomplètes'], 400);
        }

        $b = new Book();
        $b->b = $data1['t'];
        $b->c = $data1['a'];
        $this->entityManager->persist($b);
        $this->entityManager->flush();
        return new JsonResponse(['m' => 'OK']);
    }

    #[Route('/borrow', methods: ['POST'])]
    public function borrowBook(Request $req): JsonResponse
    {
        $data1 = $req->toArray();

        if (!isset($data1['t']) || !isset($data1['u'])) {
            return new JsonResponse(['e' => '404'], 400);
        }

        $b = $this->entityManager->getRepository(Book::class)->findOneBy(['b' => $data1['t']]);
        $x = $this->entityManager->getRepository(User::class)->find($data1['u']);

        if (!$b || !$x) {
            return new JsonResponse(['e' => '404'], 400);
        }

        if ($b->d) {
            return new JsonResponse(['e' => 'Déjà pris'], 400);
        }

        $b->d = true;
        $x->z[] = $b;

        $this->entityManager->flush();
        return new JsonResponse(['m' => 'OK']);
    }
}
