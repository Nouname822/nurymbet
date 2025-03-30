<?php

/** ========================================
 *
 *
 *! Файл: Dealer.php
 ** Директория: alpha\src\Dealer.php
 *? Цель: Дилер нужен для получение текущего маршрута
 *? Описание: Получает все маршруты с Supplier(Поставщик) и сверяет с текущими данных клиента Request и выдает нужный маршрут
 * Создано: 2025-03-29 08:35:38
 *
 *
============================================ */

namespace Nurymbet\Route\Core;

use Nurymbet\Route\Http\Request;

class Dealer
{
    private static array $routes = [];

    /**
     * Инициализация/Получение всех маршрутов
     *
     * @return void
     */
    private static function init(): void
    {
        if (empty(static::$routes)) {
            static::$routes = Supplier::routes();
        }
    }

    /**
     * Метод для получение текущего маршрута
     *
     * @return array
     */
    public static function route(): array
    {
        static::init();
        $request = Request::getInstance();

        $path = trim($request->get('path'), '/');

        foreach (static::$routes[$request->get('method')] ?? [] as $route => $handler) {
            if (static::isSlug($route, $path)) {
                return [
                    'handler' => $handler,
                    'params' => static::getSlugParams($route, $path),
                ];
            }
        }

        return [];
    }

    /**
     * Метод для проверки является ли маршрут слагом сверяет паттерн и текущий маршрут
     *
     * @param string $pattern /api/admin/users/{id}/{id_2}
     * @param string $url /api/admin/users/1/2
     * @return boolean true/false
     */
    private static function isSlug(string $pattern, string $url): bool
    {
        $url = trim($url, '/');
        $regexPattern = self::convertSlugToRegex($pattern);
        return (bool) preg_match($regexPattern, $url);
    }

    /**
     * Для получение параметров слага
     *
     * @param string $pattern /api/admin/users/{id}/{id_2}
     * @param string $url /api/admin/users/1/2
     * @return array ['id' => '1', 'id_2' => '2']
     */
    private static function getSlugParams(string $pattern, string $url): array
    {
        $url = trim($url, '/');
        $regexPattern = self::convertSlugToRegex($pattern);

        if (preg_match($regexPattern, $url, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return [];
    }

    /**
     * Конвертаций слага в регулярное выражение
     *
     * @param string $pattern
     * @return string
     */
    private static function convertSlugToRegex(string $pattern): string
    {
        $pattern = preg_replace(['/^\^/', '/\$$/'], '', $pattern);

        $pattern = preg_replace_callback(
            '/\{(\w+):((?:[^{}]|\{[^{}]*\})+)\}/',
            function ($matches) {
                $param = $matches[1];
                $regex = $matches[2];

                return '(?P<' . $param . '>' . $regex . ')';
            },
            $pattern
        );

        return '#^' . $pattern . '$#';
    }
}
