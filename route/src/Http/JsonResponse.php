<?php

namespace Nurymbet\Route\Http;

use React\Http\Message\Response;

class JsonResponse
{
    protected static function getTemplate(int $code, array $data): array
    {
        $category = match (true) {
            $code >= 100 && $code < 200 => 'info',
            $code >= 200 && $code < 300 => 'success',
            $code >= 300 && $code < 400 => 'move',
            $code >= 400 && $code < 430 => 'warning',
            $code >= 500 => 'error',
            default => 'unknown',
        };

        $templatePath = root("/templates/{$category}/{$code}.json");

        if (file_exists($templatePath)) {
            $template = json_decode(file_get_contents($templatePath), true);
            return array_merge($template, $data);
        }

        return array_merge(["message" => "Unknown response code"], $data);
    }

    public static function send(int $code, array $data = []): Response
    {
        $responseData = static::getTemplate($code, $data);
        return new Response($code, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_UNESCAPED_UNICODE));
    }
}
