<?php

namespace App\Infrastructure\Controller;

use App\Application\Command\Registration\RegisterUserCommand;
use App\Application\CommandHandler\Registration\RegisterUserCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private RegisterUserCommandHandler $registerUserHandler;

    public function __construct(RegisterUserCommandHandler $registerUserHandler)
    {
        $this->registerUserHandler = $registerUserHandler;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new RegisterUserCommand(
            $data['email'],
            $data['username'],
            $data['password']
        );

        try {
            $this->registerUserHandler->handle($command);
            return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
