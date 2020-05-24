<?php

namespace App\EventSubscriber;

use App\Logger\LoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var LoggerService $loggerService */
    private $loggerService;

    public function __construct(LoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', -10],
                ['logException', 10]
            ],
        ];
    }

    public function processException(ExceptionEvent $exceptionEvent): void
    {
        $exception = $exceptionEvent->getThrowable();

        $exceptionMesaage = $exception->getMessage();

        $response = new Response();
        $response->setContent($exceptionMesaage);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $exceptionEvent->setResponse($response);
    }

    public function logException(ExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getThrowable();

        $exceptionMessage = $exception->getMessage();

        $this->loggerService->getLogger()->logError($exceptionMessage);
    }
}