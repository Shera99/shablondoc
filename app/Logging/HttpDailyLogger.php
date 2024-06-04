<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Illuminate\Support\Facades\Date;

class HttpDailyLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('custom_daily');

        $date = Date::now();
        $path = storage_path(sprintf('logs/http/%s/%s/%s.log', $date->year, $date->month, $date->day));

        $handler = new StreamHandler($path, $config['level']);
        $handler->pushProcessor(new PsrLogMessageProcessor());

        $logger->pushHandler($handler);

        return $logger;
    }
}
