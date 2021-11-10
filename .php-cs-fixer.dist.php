<?php declare(strict_types=1);

use PhpCsFixer\Config;

require __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new Config())
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
    ]);
