<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Formatter\Formatter;
use Behat\Config\Profile;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use DMore\ChromeExtension\Behat\ServiceContainer\ChromeExtension;
use FriendsOfBehat\MinkDebugExtension\ServiceContainer\MinkDebugExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;
use Robertfausk\Behat\PantherExtension\ServiceContainer\PantherExtension;
use SyliusLabs\SuiteTagsExtension\ServiceContainer\SuiteTagsExtension;

return (new Config())
    ->import('tests/Behat/Resources/suites.php')
    ->withProfile(
        (new Profile('default'))
        ->withFormatter(new Formatter('pretty', [
            'verbose' => true,
            'paths' => false,
            'snippets' => false,
        ]))
        ->withExtension(new Extension(ChromeExtension::class))
        ->withExtension(new Extension(PantherExtension::class))
        ->withExtension(new Extension(MinkDebugExtension::class, [
            'directory' => 'etc/build',
            'clean_start' => false,
            'screenshot' => true,
        ]))
        ->withExtension(new Extension(MinkExtension::class, [
            'files_path' => '%paths.base%/vendor/sylius/sylius/src/Sylius/Behat/Resources/fixtures/',
            'base_url' => 'https://127.0.0.1:8080/',
            'default_session' => 'symfony',
            'javascript_session' => 'chromedriver',
            'sessions' => [
                'symfony' => [
                    'symfony' => null,
                ],
                'chromedriver' => [
                    'chrome' => [
                        'api_url' => 'http://127.0.0.1:9222',
                        'validate_certificate' => false,
                    ],
                ],
                'chrome_headless_second_session' => [
                    'chrome' => [
                        'api_url' => 'http://127.0.0.1:9222',
                        'validate_certificate' => false,
                    ],
                ],
                'panther' => [
                    'panther' => [
                        'manager_options' => [
                            'connection_timeout_in_ms' => 5000,
                            'request_timeout_in_ms' => 120000,
                            'chromedriver_arguments' => [
                                '--log-path=etc/build/chromedriver.log',
                                '--verbose',
                            ],
                            'capabilities' => [
                                'acceptSslCerts' => true,
                                'acceptInsecureCerts' => true,
                                'unexpectedAlertBehaviour' => 'accept',
                            ],
                        ],
                    ],
                ],
            ],
            'show_auto' => false,
        ]))
        ->withExtension(new Extension(SymfonyExtension::class, [
            'bootstrap' => 'vendor/sylius/test-application/config/bootstrap.php',
            'kernel' => [
                'class' => 'Sylius\TestApplication\Kernel',
            ],
        ]))
        ->withExtension(new Extension(VariadicExtension::class))
        ->withExtension(new Extension(SuiteSettingsExtension::class, [
            'paths' => [
                'features',
            ],
        ]))
        ->withExtension(new Extension(SuiteTagsExtension::class)),
    )
;
