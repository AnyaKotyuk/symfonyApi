<?php

namespace App\Logger;

use Monolog\Handler\ElasticsearchHandler;

class LoggerService
{
    /** @var Logger $logger */
    private $logger;

    /** @var SessionLoggerProcessor $sessionRequestProcessor */
    private $sessionRequestProcessor;

    public function __construct(SessionLoggerProcessor $sessionRequestProcessor)
    {
        $this->sessionRequestProcessor = $sessionRequestProcessor;
    }

    public function getLogger(): Logger
    {
        if (!$this->logger) {
            $client = \Elasticsearch\ClientBuilder::create()->setHosts(['elasticsearch'])->build();

            $options = array(
                'index' => 'shop_index'
            );

            $handler = new ElasticsearchHandler($client, $options, 0);
            $logger = new Logger('shop_app');
            $logger->pushHandler($handler);
            $logger->pushProcessor($this->sessionRequestProcessor);

            $this->logger = $logger;
        }

        return $this->logger;
    }
}