<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'admin@hospital.com', 'role' => 'ROLE_ADMIN', 'name' => 'Admin'],
            ['email' => 'doctor@hospital.com', 'role' => 'ROLE_DOCTOR', 'name' => 'Dr. García'],
            ['email' => 'nurse@hospital.com', 'role' => 'ROLE_NURSE', 'name' => 'Enf. López'],
            ['email' => 'reception@hospital.com', 'role' => 'ROLE_RECEPTIONIST', 'name' => 'Recepción'],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setRoles([$data['role']]);
            $user->setIsActive(true);
            $user->setPassword(
                $this->hasher->hashPassword($user, 'Password123!')
            );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
