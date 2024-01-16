<?php

namespace App\DataFixtures;

use App\Entity\PromptList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PromptListFixtures extends Fixture
{
    public const PROMPT_LISTS = [
        '2021',
        '2022',
        '2023',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PROMPT_LISTS as $promptList) {

            $list = new PromptList();
            $list->setYear($promptList);
            $manager->persist($list);
            $this->addReference('promptList_' . $promptList, $list);
            $manager->flush();

        }
    }
}
