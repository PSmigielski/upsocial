<?php

namespace App\EventSubscriber;

use App\Exception\ApiException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();
        $code = $throwable->getCode();
        if($throwable instanceof Exception){
            if($throwable instanceof UniqueConstraintViolationException){
                preg_match( "/unique_(?P<field>\w+)/", $throwable->getMessage(), $match, PREG_OFFSET_CAPTURE); 
                $event->setResponse(new JsonResponse(["error"=>"this ".$match['field'][0]." exist in database"], 409));
            }
        }
        if($throwable instanceof ApiException){
            $event->setResponse(new JsonResponse(["error"=>$throwable->getApiMessage()], $code));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
