<?php

namespace App\DataFixtures;

use App\Entity\SocialLink;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SocialLinkFixtures extends Fixture
{
    public const INSTAGRAM = [
        'name' => 'Instagram',
        'link' => 'https://www.instagram.com/motion.tober/',
        'iconName' => 'instagram'
    ];

    public const YOUTUBE = [
        'name' => 'Youtube',
        'link' => 'https://www.youtube.com/@motiontober',
        'iconName' => 'youtube'
    ];

    public const LINKEDIN = [
        'name' => 'Linkedin',
        'link' => 'https://www.linkedin.com/company/motiontober/',
        'iconName' => 'linkedin'
    ];

    public const TWITTER = [
        'name' => 'Twitter',
        'link' => 'https://twitter.com/motiontober',
        'iconName' => 'twitter-x'
    ];

    public function load(ObjectManager $manager)
    {
        $allSocialLinks = [
            self::INSTAGRAM,
            self::YOUTUBE,
            self::LINKEDIN,
            self::TWITTER,
        ];

        foreach ($allSocialLinks as $socialLink) {
            $link = new SocialLink();

            $link->setName($socialLink['name']);
            $link->setLink($socialLink['link']);
            $link->setIconName($socialLink['iconName']);

            $manager->persist($link);

        }
        $manager->flush();
    }

}