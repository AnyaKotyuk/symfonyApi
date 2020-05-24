<?php

namespace App\EventSubscriber;

use App\Logger\LoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    /** @var LoggerService $loggerService */
    private $loggerService;

    public function __construct(LoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['logRequestAndResponse']]
        ];
    }

    public function logRequestAndResponse(ResponseEvent $event): void
    {
        $this->loggerService->getLogger()->logRequest($event->getRequest(), $event->getResponse());
    }
}