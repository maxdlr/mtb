<?php

namespace App\DataFixtures;

use App\Entity\Prompt;
use App\Entity\PromptList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PromptFixtures extends Fixture implements DependentFixtureInterface
{
    public array $currentListFr = [
        "Fluide",
        "Notification",
        "Géométrie",
        "Bébé",
        "Lumière",
        "Dinosaure",
        "Montagne",
        "Pizza",
        "Bracelet",
        "Drag",
        "Queen",
        "Ondulation",
        "Pétrichor",
        "Citrouille",
        "Boite",
        "Cloporte",
        "Bougie",
        "Champignon",
        "Cuisine",
        "Papier",
        "Saut",
        "Corde",
        "Perspective",
        "Amis",
        "Vivant",
        "Escargot",
        "Isométrique",
        "Piano",
        "Brillant",
        "Sucreries",
        "Aventure",
        "Haltères"
    ];

    public array $currentListEn = [
        "Fluid",
        "Notification",
        "Geometry",
        "Baby",
        "Light",
        "Dinosaur",
        "Mountain",
        "Pizza",
        "Bracelet",
        "Queen	Drag Queen",
        "Ondulation",
        "Petrichor",
        "Pumpkin",
        "Box",
        "Woodlouse",
        "Candle",
        "Mushroom",
        "Kitchen",
        "Paper",
        "Jump",
        "Rope",
        "Perspective",
        "Friends",
        "Living",
        "Snail",
        "Isometric",
        "Piano",
        "Brilliant",
        "Sweets",
        "Adventure",
        "Dumbbells",
    ];

    public function __construct(private ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 30; $i++)
        {
            $prompt = new Prompt();
            $prompt->setNameFr($this->currentListFr[$i])
                ->setNameEn($this->currentListEn[$i])
                ->setDayNumber($i+1)
                ->addPromptList($this->getReference('promptList_' . PromptListFixtures::PROMPTLISTS));
                $manager->persist($prompt);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PromptListFixtures::class];
    }
}
