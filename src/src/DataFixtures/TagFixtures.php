<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Tag;

class TagFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setName('pilulka');
        $manager->persist($tag);
        $manager->flush();

        $tag = new Tag();
        $tag->setName('@pilulka');
        $manager->persist($tag);
        $manager->flush();

        $tag = new Tag();
        $tag->setName('#pilulka');
        $manager->persist($tag);
        $manager->flush();   
    }
}