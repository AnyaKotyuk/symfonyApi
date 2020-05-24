<?php

namespace App\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\DateTimeImmutable;

class Logger extends \Monolog\Logger
{
    public function logError(string $errorMessage, int $level = self::DEBUG): bool
    {
        $record = [
            'error_message' => $errorMessage,
            'domain' => 'error',
            'level' => $level,
            'datetime' => new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
        ];

        return $this->customLog($record);
    }

    public function logRequest(Request $request, Response $response): bool
    {
        $level = self::DEBUG;

        $record = [
            'request' => [
                'body' => $request->getContent(),
                'headers' => $request->headers->all()
            ],
            'response' => [
                'body' => $response->getContent(),
                'headers' => $response->headers->all()
            ],
            'domain' => 'success',
            'level' => $level,
            'datetime' => new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
        ];

        return $this->customLog($record);
    }

    public function customLog(array $record): bool
    {
        $handlerKey = null;
        foreach ($this->handlers as $key => $handler) {
            if ($handler->isHandling(['level' => $record['level']])) {
                $handlerKey = $key;
                break;
            }
        }

        try {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }

            // advance the array pointer to the first handler that will handle this record
            reset($this->handlers);
            while ($handlerKey !== key($this->handlers)) {
                next($this->handlers);
            }

            while ($handler = current($this->handlers)) {
                if (true === $handler->handle($record)) {
                    break;
                }

                next($this->handlers);
            }
        } catch (\Throwable $e) {
            $this->handleException($e, $record);
        }

        return true;
    }

}