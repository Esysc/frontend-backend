<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private JWTTokenManagerInterface $jwtManager)
    {
    }

    #[Route('/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return new JsonResponse(['message' => 'username and password required'], Response::HTTP_BAD_REQUEST);
        }

        $existing = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($existing) {
            return new JsonResponse(['message' => 'username already exists'], Response::HTTP_CONFLICT);
        }

        $user = new User($username, '');
        $hash = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hash);

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'user registered'], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return new JsonResponse(['message' => 'username and password required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['message' => 'invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }
}
