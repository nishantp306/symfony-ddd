<?php

namespace App\Application\CommandHandler\Registration;

use App\Application\Command\Registration\RegisterUserCommand;
use App\Domain\Model\Entity\User;
use App\Domain\Model\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserCommandHandler
{
    private $entityManager;
    private $passwordHasher;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @param RegisterUserCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(RegisterUserCommand $command)
    {
        if ($this->userRepository->findOneBy(['email' => $command->getEmail()])) {
            throw new Exception('Email already exists.');
        }

        if ($this->userRepository->findOneBy(['username' => $command->getUsername()])) {
            throw new Exception('Username already exists.');
        }

        $user = new User();
        $user->setEmail($command->getEmail());
        $user->setUsername($command->getUsername());
        $user->setPassword($this->passwordHasher->hashPassword($user, $command->getPassword()));
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
