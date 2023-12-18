<?php

namespace App\DataFixtures;

use App\Entity\Violation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ViolationFixtures extends Fixture
{
    private array $violations = [];

    public function __construct()
    {
        $faker = Factory::create();

        for ($i = 0; $i < 6; $i++) {
            $this->setViolationsFixtures($faker->word());
        }
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < count($this->getViolationsFixtures()) - 1; $i++) {
            $violation = new Violation();
            $violation
                ->setName($this->getViolationsFixtures()[$i])
                ->setDescription($faker->paragraph());

            $manager->persist($violation);
            $this->setReference('violation_' . $this->getViolationsFixtures()[$i], $violation);
        }

        $manager->flush();
    }


    public function setViolationsFixtures(string $violation): void
    {
        $this->violations[] = $violation;
    }

    public function getViolationsFixtures(): array
    {
        return $this->violations;
    }
}
