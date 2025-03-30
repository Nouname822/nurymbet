<?php

/** ========================================
 *
 *
 *! Файл: Functions.php
 ** Директория: alpha\src\Functions.php
 *? Цель: Функции для удобной работы
 * Создано: 2025-03-29 08:41:46
 *
 *
============================================ */

namespace Nurymbet\Route\Helpers;

class Functions
{
    /**
     * Получение корня библиотеки
     *
     * @param string $path
     * @return string
     */
    public static function root(string $path): string
    {
        $root = dirname(__DIR__, 1);

        if (str_starts_with($path, '@/')) {
            return $root . '/' . ltrim(substr($path, 2), '/');
        }

        return $root . '/' . ltrim($path, '/');
    }
}
