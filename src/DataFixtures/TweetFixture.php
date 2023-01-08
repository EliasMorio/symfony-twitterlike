<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TweetFixture extends Fixture implements DependentFixtureInterface{
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $userRepo = $manager->getRepository(User::class);
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 20; $i++){
            $tweet = new Tweet();
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'password'
            ));
            $manager->persist($user);

            $tweet->setAuthor($user);
            $tweet->setBody($faker->realText(140));
            $tweet->setCreatedAt($faker->dateTimeBetween('-'.rand(1, 6).' months'));
            $manager->persist($tweet);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }
}
