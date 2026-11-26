<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $env = $_ENV['APP_ENV'] ?? 'dev';

    if (str_starts_with($env, 'test')) {
        // Sylius >= 2.3 ships its Behat services as PHP; Symfony 8 can no longer load XML service files.
        $syliusBehatConfigDir = '../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/';
        $container->import(
            $syliusBehatConfigDir . (file_exists(__DIR__ . '/' . $syliusBehatConfigDir . 'services.php') ? 'services.php' : 'services.xml'),
        );
        $container->import('../../Behat/Resources/services.yaml');
    }
};
