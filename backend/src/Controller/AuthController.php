<?php

namespace App\Controller;

use DateTime;
use App\Util\JsonDecoder;
use App\Entity\User;
use App\Exception\ApiException;
use App\Service\ValidatorService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/v1/api/auth', methods: ["POST"])]
class AuthController extends AbstractController
{
    private ValidatorService $validator;
    private UserPasswordHasherInterface $userPasswordHasher;
    private JsonDecoder $jsonDecoder;
    private ObjectManager $em;
    public function __construct(JsonDecoder $jsonDecoder,
        ManagerRegistry $doctrine,
        ValidatorService $validator,
        UserPasswordHasherInterface $userPasswordHasher
    ){
        $this->em = $doctrine->getManager();
        $this->jsonDecoder = $jsonDecoder;
        $this->validator = $validator;
        $this->userPasswordHasher = $userPasswordHasher;
    }
    #[Route('/register', name: 'app_auth_register')]
    public function index(Request $request): Response
    {
        $data = $this->jsonDecoder->decode($request);
        $this->validator->validateSchema('/../Schemas/register.json', (object)$data);
        $user = new User("adamka", "adam", "kowalski","male", new DateTime(), "adam@adaam.com" );
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();
        return new JsonResponse(["message" => "hello fuckers"], 201);
    }
}
