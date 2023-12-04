<?php

namespace App\DataFixtures;

use App\Entity\Resolution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ResolutionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $resolution = new Resolution();
            $generatedName = $faker->word();

            $resolution
                ->setName($generatedName)
                ->setDescription($faker->paragraph());
            $manager->persist($resolution);
            $this->setReference('resolution_' . $generatedName, $resolution);
        }

        $manager->flush();
    }
}