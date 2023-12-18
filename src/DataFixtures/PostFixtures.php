<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Service\PostManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public array $listPrompts;
    const FIXTURES_POSTS = [
        '01_bounce_sd.gif',
        '02_stretch_sd.gif',
        '03_roll_sd.gif',
        '04_shot_SD.gif',
        '05_flip_sd.gif',
        '06_speed_sd.gif',
        '07_jump_SD.gif',
        '08_bump_sd.gif',
        '09_curve_SD.gif',
        '10_stick_sd.gif',
        '11_fly_SD.gif',
        '12_slice_sd.gif',
        '13_twin_sd.gif',
        '15_gravity_sd.gif',
        '16_volume.gif',
        '17_evolve_sd.gif',
        '18_attract_sd.gif',
        '19_revolve_sd.gif',
        '20_trick_sd.gif',
        '21_walk_sd.gif',
        '22_start_sd.gif',
        '23_blow_sd.gif',
        '24_space_LOW.gif',
        '25_ride_sd.gif',
        '27_slide_sd.gif',
        '29_swing_sd.gif',
        '31_plug_sd.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
        'bejvrktnvjre.gif',
    ];

    public function __construct(
        PromptFixtures               $promptFixtures,
        private readonly PostManager $postManager)
    {
        $this->listPrompts = [
            ['fr' => $promptFixtures->twentyOneListFr, 'en' => $promptFixtures->twentyOneListEn],
            ['fr' => $promptFixtures->twentyTwoListFr, 'en' => $promptFixtures->twentyTwoListEn],
            ['fr' => $promptFixtures->twentyThreeListFr, 'en' => $promptFixtures->twentyThreeListEn]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $users = UserFixtures::USERNAMES;

        foreach (self::FIXTURES_POSTS as $postGif) {
            $post = new Post();
            $post->setPrompt($this->postManager->autoPromptSelect($postGif))
                ->setFileName($postGif)
                ->setFileSize(10)
                ->setUploadedOn($now)
                ->addUser($this->getReference('user_' . $users[rand(0, count($users) - 1)]));

            $manager->persist($post);
            $this->setReference('post_' . $postGif, $post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PromptFixtures::class,
        ];
    }
}
