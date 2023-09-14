<?php

namespace App\DataFixtures;

use App\Entity\Page;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher){}

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $user = new User();
        $page = new Page();

        $user->setUsername('maxdlr')
        ->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            'password'
        ))
            ->setRoles(['ROLE_ADMIN'])
            ->setPage($page)
            ->setRegistrationDate($now)
        ;

        $manager->persist($user);
        $manager->flush();
    }
}
