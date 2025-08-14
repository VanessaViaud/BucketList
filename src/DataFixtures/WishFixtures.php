<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class WishFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {

            $wish = new Wish();
            $wish->setTitle($faker->city)
                ->setDescription($faker->paragraph($maxNbChars = 250, $indexSize = 1))
                ->setAuthor($faker->name())
                ->setDateCreated(new \DateTime())
                ->setIsPublished($faker->boolean());

            $manager->persist($wish);
        }
        $manager->flush();
    }
}
