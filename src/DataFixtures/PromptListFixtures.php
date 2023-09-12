<?php

namespace App\DataFixtures;

use App\Entity\PromptList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PromptListFixtures extends Fixture
{
    public const PROMPTLISTS = '2023';

    public function load(ObjectManager $manager): void
    {
        $promptList = new PromptList();
        $promptList->setYear(self::PROMPTLISTS);
        $manager->persist($promptList);
        $this->addReference('promptList_' . self::PROMPTLISTS, $promptList);
        $manager->flush();
    }
}
