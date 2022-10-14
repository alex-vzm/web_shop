<?php

declare (strict_types=1);

namespace App\Services\Telegram;

use App\Exceptions\CustomTelegramApiException;
use Exception;
use Illuminate\Support\Facades\Http;

final class TelegramBotApi
{

    public const HOST = 'https://api.telegram.org/bot';

    /**
     * @throws CustomTelegramApiException
     */
    public static function sendMessage(string $token, int $chat_id, string $text): bool
    {
        try {
            Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text
            ]);
            return true;
        } catch (Exception $e) {
            throw new CustomTelegramApiException();
        }
    }
}
