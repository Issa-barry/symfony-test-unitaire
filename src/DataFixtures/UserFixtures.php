<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder){
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr_FR");
        $count = 0;
        $users = [
            "user@yopmail.com" => [],
            "author1@yopmail.com" => ["ROLE_AUTHOR"],
            "author2@yopmail.com" => ["ROLE_AUTHOR"],
            "admin@yopmail.com" => ["ROLE_ADMIN"],
        ];

        foreach ($users as $email => $role) { 
            $user = new User();
            $user->setLastName($faker->lastName());
            $user->setFirstName($faker->firstName());
            $user->setEmail($email);
            $user->setRoles($role);
            $user->setIsVerified(mt_rand(0, 1)>0.5);
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword($user, "azertyuiop")
            );
            $manager->persist($user);
            $this->addReference("USER".$count, $user);
            $count++;
        }

        $manager->flush();
    }
}
