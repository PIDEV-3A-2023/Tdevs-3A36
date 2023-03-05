<?php
namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BanningService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function banUser(User $user)
    {
        if ($user->isIsbanned()==false)
        {
            $user->setIsbanned(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        else{
            $user->setIsbanned(false);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}