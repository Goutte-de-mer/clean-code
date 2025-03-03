<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create 10 users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUserName($faker->userName);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
