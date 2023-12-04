<?php

namespace App\DataFixtures;

use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReportFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ViolationFixtures $violationFixtures
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $report = new Report();
            $report
                ->setViolation($this->getReference('violation_' . $faker->randomElement($this->violationFixtures->getViolationsFixtures())))
                ->setReporter($this->getReference('user_' . $faker->randomElement(UserFixtures::USERNAMES)))
                ->setDescription($faker->paragraph)
                ->setReportedOn(new \DateTimeImmutable())
                ->setPost($this->getReference('post_' . $faker->randomElement(PostFixtures::FIXTURES_POSTS)));

            $manager->persist($report);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ViolationFixtures::class,
            UserFixtures::class,
            PostFixtures::class
        ];
    }
}
