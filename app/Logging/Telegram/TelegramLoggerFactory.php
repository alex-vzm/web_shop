<?php

declare (strict_types=1);

namespace App\Logging\Telegram;

use Monolog\Logger;

//class TelegramLoggerFactory
//{
//    public function __invoke(array $config)
//    {
//        $logger = new Logger('telegram');
//        $logger->pushHandler(new TelegramLoggerHandler($config));
//        return $logger;
//    }

class TelegramLoggerFactory
{
    public function __invoke($args)
    {
        $logger = new Logger('telegram');
        $logger->pushHandler(new TelegramLoggerHandler($args));
        return $logger;
    }
}
