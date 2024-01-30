<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setUsername('qimono')
            ->setPassword($this->userPasswordHasher->hashPassword(
                $user,
                'password'
            ))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}