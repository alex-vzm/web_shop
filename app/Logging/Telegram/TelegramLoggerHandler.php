<?php

declare (strict_types=1);

namespace App\Logging\Telegram;

use App\Services\Telegram\TelegramBotApi;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class TelegramLoggerHandler extends AbstractProcessingHandler
{

    protected int $chatId;
    protected string $token;

    public function __construct(array $config)
    {
        $level = Logger::toMonologLevel($config['level']);
        parent::__construct($level);

        $this->chatId = $config['chat_id'];
        $this->token = $config['token'];
    }

    /**
     * @throws \App\Exceptions\CustomTelegramApiException
     */
    protected function write(array $record): void
    {
        $data = [
            'chat_id' => $this->chatId,
            'token' => $this->token,
            'text' => $record["formatted"]
        ];

        TelegramBotApi::sendMessage($data);
//        dd($record);
    }
}
