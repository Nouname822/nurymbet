<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6dd8608ad93b0b91dda7ddbf51576e20
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nurymbet\\Core\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nurymbet\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6dd8608ad93b0b91dda7ddbf51576e20::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6dd8608ad93b0b91dda7ddbf51576e20::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6dd8608ad93b0b91dda7ddbf51576e20::$classMap;

        }, null, ClassLoader::class);
    }
}
