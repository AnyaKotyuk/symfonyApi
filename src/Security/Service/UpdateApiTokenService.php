<?php

namespace App\Security\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class UpdateApiTokenService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateToken(User $user): string
    {
        $newApiToken = $this->generateToken();
        $user->setApiToken($newApiToken);
        $user->setTokenExpire($this->generateTokenExpires());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $newApiToken;
    }

    private function generateToken(): string
    {
        return sha1(random_bytes(12));
    }

    private function generateTokenExpires()
    {
        return new DateTime('+1 day');
    }
}