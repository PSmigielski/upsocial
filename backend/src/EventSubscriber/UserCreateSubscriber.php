<?php

namespace App\EventSubscriber;

use App\Controller\VerifyUserController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\UserCreateEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;

class UserCreateSubscriber implements EventSubscriberInterface
{
    private ManagerRegistry $managerRegistry;
    private MailerInterface $mailer;
    private ContainerBagInterface $params;

    public function __construct(ManagerRegistry $doctrine, MailerInterface $mailer, ContainerBagInterface $params)
    {
        $this->managerRegistry = $doctrine;
        $this->mailer = $mailer;
        $this->params = $params;
    }
    public function onUserCreate(UserCreateEvent $event)
    {
        $verifyEmailController = new VerifyUserController($this->mailer, $this->managerRegistry,$this->params->get("email") );
        $user = $event->getUser();
        $data= $verifyEmailController->add($user);
        if ($data['isMailSent']) {
            $event->setResponse(new JsonResponse([
                "message" => "Your account has been created successfully! Check your email to verify your account",
            ], 201));
        } else {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($user);
            $entityManager->remove($data["verificationRequest"]);
            $entityManager->flush();
            $response = new JsonResponse(["error" => "email can't be sent. Try register again later"], 500);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            "user.create" => 'onUserCreate',
        ];
    }
}
