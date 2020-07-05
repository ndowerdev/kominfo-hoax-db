<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd84cbffe43bed0701617b8650a2a958b
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DiDom\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DiDom\\' => 
        array (
            0 => __DIR__ . '/..' . '/imangazaliev/didom/src/DiDom',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd84cbffe43bed0701617b8650a2a958b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd84cbffe43bed0701617b8650a2a958b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}