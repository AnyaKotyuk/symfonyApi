<?php

namespace App\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggerProcessor
{
    /** @var Request $resuest */
    private $request;

    /** @var Response $response */
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function __invoke(array $record)
    {
        $record['request']['body'] = $this->request->getContent();
        $record['request']['headers'] = $this->request->headers->all();

        $record['response']['body'] = $this->response->getContent();
        $record['response']['headers'] = $this->response->headers->all();

        return $record;
    }
}