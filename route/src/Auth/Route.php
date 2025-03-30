<?php

/** ========================================
 *
 *
 *! Файл: Route.php
 ** Директория: alpha\src\Auth\Route.php
 *? Цель: Для регистрации маршрутов и записи их в кэш
 * Создано: 2025-03-29 08:46:36
 *
 *
============================================ */

namespace Nurymbet\Route\Auth;

use Nurymbet\Route\Core\Config;
use Nurymbet\Route\Helpers\AllowMethods;
use Nurymbet\Route\Http\Resource;
use Exception;

class Route
{
    private static array $routes = [];
    private static array $callerFiles = [];
    private static array $names = [];
    private static array $groupOptions = [];






    /**
     * Для обработки GET запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function get(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::GET, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки POST запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function post(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::POST, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки PUT запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function put(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::PUT, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки PATCH запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function patch(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::PATCH, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки DELETE запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function delete(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::DELETE, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки OPTIONS запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function options(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::OPTIONS, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для обработки HEAD запросов
     *
     * @param string $path
     * @param string $name
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    public static function head(string $path, string $name, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        return static::addRoute(AllowMethods::HEAD, $name, $path, $handler, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для создание готового CRUD ресурса
     *
     * @param string $path
     * @param string $name
     * @param string $controller
     * @param array $middleware
     * @param array $options
     * @return void
     */
    public static function resource(string $path, string $name, string $controller, array $middleware = [], array $options = []): void
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        Resource::init($path, $name, $controller, $middleware, array_merge($options, ['caller_file' => $callerFile]));
    }





    /**
     * Для группирование маршрутов
     *
     * @param string $prefix
     * @param string $name
     * @param array $middleware
     * @param callable $callback
     * @return void
     */
    public static function group(string $prefix, string $name, array $middleware, callable $callback): void
    {
        $callerFile = static::getCallerFile();
        static::$callerFiles[$callerFile] = $callerFile;
        $previousGroupOptions = static::$groupOptions;

        static::$groupOptions = [
            'prefix'     => $prefix,
            'name'       => $name,
            'middleware' => $middleware
        ];

        $callback();

        static::$groupOptions = $previousGroupOptions;
    }







    private static function getCallerFile(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        if ($trace[1]['file']) {
            return $trace[1]['file'];
        }
        throw new Exception("Не удалось найти файл вызова");
    }








    /**
     * Добавление/Регистрация маршрута
     *
     * @param AllowMethods $method
     * @param string $name
     * @param string $path
     * @param callable|array $handler
     * @param array $middleware
     * @param array $options
     * @return static
     */
    private static function addRoute(AllowMethods $method, string $name, string $path, callable|array $handler, array $middleware = [], array $options = []): static
    {
        $config = Config::getInstance();
        $globalPrefix = $config->get('prefix') ?? '';

        if (!empty(static::$groupOptions)) {
            if ($globalPrefix !== '') {
                $path = trim($globalPrefix, '/') . '/' . trim(static::$groupOptions['prefix'], '/') . '/' . trim($path, '/');
            } else {
                $path = trim(static::$groupOptions['prefix'], '/') . '/' . trim($path, '/');
            }
            $middleware = array_merge(static::$groupOptions['middleware'], $middleware);
            $name = static::$groupOptions['name'] . '.' . $name;
        } else {
            if ($globalPrefix !== '') {
                $path = trim($globalPrefix, '/') . '/' . trim($path, '/');
            }
        }

        static::$routes[$method->value][trim($path, '/')] = [
            'path'       => trim($path, '/'),
            'name'       => $name,
            'method'     => $method->value,
            'handler'    => $handler,
            'middleware' => $middleware,
            'options'    => $options
        ];

        static::$names[$name] = $path;

        return new static();
    }



    /**
     * Для сохранение все маршрутов в кэш
     *
     * @return void
     */
    public static function save(): void
    {
        Save::save(static::$routes, static::$names, static::$callerFiles);
    }
}
