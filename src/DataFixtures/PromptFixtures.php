<?php

namespace App\DataFixtures;

use App\Entity\Prompt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PromptFixtures extends Fixture implements DependentFixtureInterface
{
    public array $listFr2023 = [
        "Fluide",
        "Notification",
        "Géométrie",
        "Bébé",
        "Lumière",
        "Dinosaure",
        "Montagne",
        "Pizza",
        "Bracelet",
        "Drag Queen",
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
    public array $listEn2023 = [
        "Fluid",
        "Notification",
        "Geometry",
        "Baby",
        "Light",
        "Dinosaur",
        "Mountain",
        "Pizza",
        "Bracelet",
        "Drag Queen",
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
    public array $listFr2022 = [
        'scalpel',
        'boucle',
        'Chenille',
        'art Martial',
        'velu',
        'renard',
        'galaxie',
        'verticale',
        'torche',
        'vague',
        'astronome',
        'botanique',
        'foudre',
        'foulard',
        'totem',
        'invisible',
        'plan',
        'hallucination',
        'ligne',
        'chaud',
        'petit',
        'bar',
        'colline',
        'penché',
        'laser',
        'magicien',
        'gros',
        'reculer',
        'fleur',
        'lampe',
        'extensible',
    ];
    public array $listEn2022 = [
        'scalpel',
        'loop',
        'caterpillar',
        'martial art',
        'hairy',
        'fox',
        'galaxy',
        'vertical',
        'torch',
        'wave',
        'astronomer',
        'botanic',
        'lightning',
        'scarf',
        'totem',
        'invisible',
        'plan',
        'hallucination',
        'line',
        'hot',
        'small',
        'bar',
        'hill',
        'leaning',
        'laser',
        'wizard',
        'fat',
        'move back',
        'flower',
        'lamp',
        'expandable',
    ];
    public array $listFr2021 = [
        'rebond',
        'etirement',
        'roule',
        'tir',
        'retourne',
        'vitesse',
        'saut',
        'bosse',
        'courbe',
        'colle',
        'vole',
        'fendre',
        'jumeaux',
        'torsion',
        'gravité',
        'volume',
        'Evolue',
        'Attire',
        'tourner',
        'ruse',
        'marche',
        'commence',
        'soufle',
        'espace',
        'ride',
        'tombe',
        'glisse',
        'plie',
        'balance',
        'tire',
        'branche',
    ];
    public array $listEn2021 = [
        'bounce',
        'stretch',
        'roll',
        'shot',
        'flip',
        'speed',
        'jump',
        'bump',
        'curve',
        'stick',
        'fly',
        'slice',
        'twin',
        'twist',
        'gravity',
        'volume',
        'evolve',
        'attract',
        'revolve',
        'trick',
        'walk',
        'start',
        'blow',
        'space',
        'ride',
        'fall',
        'slide',
        'bend',
        'swing',
        'pull',
        'plug',
    ];
    public array $listPrompts;

    public function __construct()
    {
        $this->listPrompts = [
            ['fr' => $this->listFr2021, 'en' => $this->listEn2021],
            ['fr' => $this->listFr2022, 'en' => $this->listEn2022],
            ['fr' => $this->listFr2023, 'en' => $this->listEn2023]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($y = 0; $y < count(PromptListFixtures::PROMPT_LISTS); $y++) {
            for ($i = 0; $i <= 30; $i++) {
                $prompt = new Prompt();
                $prompt->setNameFr($this->listPrompts[$y]['fr'][$i])
                    ->setNameEn($this->listPrompts[$y]['en'][$i])
                    ->setDayNumber($i + 1)
                    ->addPromptList($this->getReference('promptList_' . PromptListFixtures::PROMPT_LISTS[$y]));
                $this->addReference('prompt_' . $this->listPrompts[$y]['fr'][$i], $prompt);
                $manager->persist($prompt);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PromptListFixtures::class];
    }
}
