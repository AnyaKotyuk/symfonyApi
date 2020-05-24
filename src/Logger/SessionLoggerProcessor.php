<?php

namespace App\Logger;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionLoggerProcessor
{
    /** @var SessionInterface $session */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(array $record)
    {
        if (!$this->session->isStarted()) {
            return $record;
        }

        $record['extra']['token'] = $this->session->getId();

        return $record;
    }
}