<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VerifyUserRequest;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class VerifyUserController extends AbstractController
{
    private MailerInterface $mailer;
    private EntityManager $em;
    private string $email;
    public function __construct(MailerInterface $mailer, ManagerRegistry $doctrine, string $email=null)
    {
        $this->mailer = $mailer;
        $this->em = $doctrine->getManager();
        $this->email = $email;
    }
    public function add(User $user) : array
    {
        $verRequest = new VerifyUserRequest();
        $verRequest->setUser($user);
        $this->em->persist($verRequest);
        $this->em->flush();
        return $this->sendMail($user->getEmail(), $verRequest);
    }
    private function sendMail(string $email, VerifyUserRequest $verificationRequest): array
    {
        try{
            $email = (new TemplatedEmail())
            ->from(new Address($this->email, 'Upsocial Bot'))
            ->to($email)
            ->subject('Verify your email')
            ->htmlTemplate('verify_user_request/email.html.twig')
            ->context([
                'verifyToken' => $verificationRequest->getId()
            ]);
            $this->mailer->send($email);
            return ["isMailSent" => true];
        } catch(TransportExceptionInterface $e) {
            return ["isMailSent" => false, "verificationRequest" => $verificationRequest];
        }
    }
}
