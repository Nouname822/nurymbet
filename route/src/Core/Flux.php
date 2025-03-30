<?php

/** ========================================
 *
 *
 *! Файл: Flux.php
 ** Директория: alpha\src\Flux.php
 *? Цель: Поток используется как входная точка маршрутизатора он взаимодействует с проектом
 * Создано: 2025-03-29 08:40:40
 *
 *
============================================ */

namespace Nurymbet\Route\Core;

class Flux
{
    /**
     * Получение текущего маршрута
     *
     * @return array
     */
    public static function init(): array
    {
        $routes = Dealer::route();

        return $routes;
    }

    /**
     * Получение всех маршрутов
     *
     * @return array
     */
    public static function all(): array
    {
        $routes = Supplier::routes() ?? [];

        return $routes;
    }
}
