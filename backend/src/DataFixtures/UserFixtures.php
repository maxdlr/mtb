<?php

namespace App\DataFixtures;

use App\Entity\Page;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USERNAMES = [
        'maxdlr',
        'augusta',
        'joachim',
        'polygone',
        'pepper'
    ];

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERNAMES as $username) {
            $now = new \DateTimeImmutable();
            $user = new User();
            $page = new Page();

            $user->setUsername($username)
                ->setPassword($this->userPasswordHasher->hashPassword(
                    $user,
                    'password'
                ))
                ->setPage($page)
                ->setRegistrationDate($now);

            if ($username === 'maxdlr') {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            $this->addReference('user_' . $username, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
