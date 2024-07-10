<?php

namespace App\Infrastructure\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private UserProviderInterface $userProvider;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->jwtManager = $jwtManager;
        $this->userProvider = $userProvider;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];

        try {
            $user = $this->userProvider->loadUserByIdentifier($username);
        } catch (\Exception $e) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
