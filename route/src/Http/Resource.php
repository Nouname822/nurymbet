<?php

/** ========================================
 *
 *
 *! Файл: Resource.php
 ** Директория: alpha\src\Resource.php
 *? Цель: Для создание готовых CRUD маршрутов
 * Создано: 2025-03-29 07:41:12
 *
 *
============================================ */

namespace Nurymbet\Route\Http;

use Nurymbet\Route\Auth\Route;
use Nurymbet\Route\Core\Config;
use Nurymbet\Route\Helpers\AllowMethods;
use Exception;
use React\EventLoop\Loop;

class Resource
{
    /**
     * Проверка и регистрация маршрута
     *
     * @param string $path
     * @param string $httpMethod
     * @param string $name
     * @param string $controller
     * @param string $handlerMethod
     * @param array $options
     * @return void
     */
    private static function registerRoute(string $path, string $httpMethod, string $name, string $controller, string $handlerMethod, array $options = []): void
    {
        if (AllowMethods::tryFrom(strtoupper($httpMethod))) {
            $method = strtolower($httpMethod);
            Route::$method($path, $name, [$controller, $handlerMethod], [], $options);
        } else {
            throw new Exception("Неверный HTTP-метод задан для маршрута: $path, Название маршрута: $name");
        }
    }





    /**
     * Регистрация маршрутов из конфига
     *
     * @param array $config
     * @param string $path
     * @param string $name
     * @param string $controller
     * @param array $options
     * @return void
     */
    private static function registerConfigRoutes(array $config, string $path, string $name, string $controller, array $options): void
    {
        foreach ($config as $param) {
            if (!isset($param['http_method'], $param['prefix_name'], $param['handler_method_name'])) {
                throw new Exception('Неверно настроен маршрут для ресурса. Нужны параметры: http_method, prefix_name, handler_method_name');
                Loop::stop();
            }

            $routePath = $param['http_method'] === 'post' ? '' : '{id:\d+}';
            static::registerRoute($routePath, $param['http_method'], $param['prefix_name'], $controller, $param['handler_method_name'], $options);
        }
    }





    /**
     * Регистрация маршрутов по умолчанию
     *
     * @param string $controller
     * @param array $options
     * @return void
     */
    private static function registerDefaultRoutes(string $controller, array $options): void
    {
        Route::post("", "create", [$controller, 'create'], [], $options);
        Route::get("{id:\d+}", "show", [$controller, 'show'], [], $options);
        Route::put("{id:\d+}", "update", [$controller, 'update'], [], $options);
        Route::delete("{id:\d+}", "destroy", [$controller, 'destroy'], [], $options);
    }





    /**
     * Инициализация
     *
     * @param string $path
     * @param string $name
     * @param string $controller
     * @param array $middleware
     * @param array $options
     * @return void
     */
    public static function init(string $path, string $name, string $controller, array $middleware = [], array $options = []): void
    {
        $config = Config::getInstance()->get('resource_route') ?? [];

        Route::group($path, $name, $middleware, function () use ($config, $path, $name, $controller, $options) {
            if (!empty($config)) {
                static::registerConfigRoutes($config, $path, $name, $controller, $options);
            } else {
                static::registerDefaultRoutes($controller, $options);
            }
        });
    }
}
