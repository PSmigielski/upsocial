<?php

namespace App\EventSubscriber;

use App\Controller\VerifyUserController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\UserCreateEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;

class UserCreateSubscriber implements EventSubscriberInterface
{
    private ManagerRegistry $managerRegistry;
    private MailerInterface $mailer;
    public function __construct(ManagerRegistry $doctrine, MailerInterface $mailer)
    {
        $this->managerRegistry = $doctrine;
        $this->mailer = $mailer;
    }
    public function onUserCreate(UserCreateEvent $event)
    {
        $verifyEmailController = new VerifyUserController($this->mailer, $this->managerRegistry);
        $user = $event->getUser();
        $data= $verifyEmailController->add($this->entityManager, $user);
        if ($data['isMailSent']) {
            $event->setResponse(new JsonResponse([
                "message" => "Your account has been created successfully! Check your email to verify your account",
            ], 201));
        } else {
            $this->entityManager->remove($user);
            $this->entityManager->remove($data["verificationRequest"]);
            $this->entityManager->flush();
            $response = new JsonResponse(["error" => "email can't be sent. Try register again later"], 500);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreateEvent::class => 'onUserCreateEvent',
        ];
    }
}
