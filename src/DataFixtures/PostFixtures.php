<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Repository\PromptRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public array $listPrompts;

    public function __construct(PromptFixtures $promptFixtures)
    {
        $this->listPrompts = [
            ['fr' => $promptFixtures->twentyOneListFr, 'en' => $promptFixtures->twentyOneListEn],
            ['fr' => $promptFixtures->twentyTwoListFr, 'en' => $promptFixtures->twentyTwoListEn],
            ['fr' => $promptFixtures->twentyThreeListFr, 'en' => $promptFixtures->twentyThreeListEn]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $now = new \DateTimeImmutable();
        $users = UserFixtures::USERNAMES;

        for ($i = 0; $i < 200; $i++) {
            $post = new Post();
            $post->setPrompt($this->getReference('prompt_' . $this->listPrompts[rand(0, 2)]['fr'][rand(0, 30)]))
                ->setFileName($faker->word())
                ->setUploadedOn($now)
                ->addUser($this->getReference('user_' . $users[rand(0, count($users) - 1)]));

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PromptFixtures::class
        ];
    }
}
