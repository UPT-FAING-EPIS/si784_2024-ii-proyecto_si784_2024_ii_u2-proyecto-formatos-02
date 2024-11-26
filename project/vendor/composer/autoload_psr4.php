<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Tests\\Unit\\' => array($baseDir . '/tests/Unit'),
    'Tests\\Integration\\' => array($baseDir . '/tests/Integration'),
    'Tests\\Functional\\' => array($baseDir . '/tests/Functional'),
    'PhpParser\\' => array($vendorDir . '/nikic/php-parser/lib/PhpParser'),
    'Mockery\\' => array($vendorDir . '/mockery/mockery/library/Mockery'),
    'DeepCopy\\' => array($vendorDir . '/myclabs/deep-copy/src/DeepCopy'),
    'App\\' => array($baseDir . '/app'),
);