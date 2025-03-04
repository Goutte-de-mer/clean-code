<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;


class UserService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Vérifie si un utilisateur existe dans la base de données.
     *
     * @param int $userId ID de l'utilisateur à vérifier.
     * @return bool Retourne true si l'utilisateur existe, sinon false.
     */
    public function userExists(int $userId): bool
    {
        $user = $this->userRepository->find($userId);
        return $user !== null;
    }
}
