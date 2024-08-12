<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9854a90653bc47d5c0125f488b6d87e8
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LeviZwannah\\MpesaSdk\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LeviZwannah\\MpesaSdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/levizwannah/mpesa-sdk-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9854a90653bc47d5c0125f488b6d87e8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9854a90653bc47d5c0125f488b6d87e8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9854a90653bc47d5c0125f488b6d87e8::$classMap;

        }, null, ClassLoader::class);
    }
}
