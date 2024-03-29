<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1483879aa0087d569caa66f4304b22ee
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static $classMap = array (
        'PiramideUploader' => __DIR__ . '/../..' . '/piramide-uploader/PiramideUploader.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit1483879aa0087d569caa66f4304b22ee::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit1483879aa0087d569caa66f4304b22ee::$classMap;

        }, null, ClassLoader::class);
    }
}
