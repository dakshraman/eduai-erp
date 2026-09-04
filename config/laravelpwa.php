<?php

return [
    'name' => env('APP_NAME', 'InfixEdu'),
    'manifest' => [
        'name' => env('APP_NAME', 'InfixEdu'),
        'short_name' => env('APP_NAME', 'InfixEdu'),
        'start_url' => env('APP_URL', '/'),
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '72x72' => [
                'path' => pwa_assetPath('public/images/icons/icon-72x72.png'),
                'purpose' => 'any',
            ],
            '96x96' => [
                'path' => pwa_assetPath('public/images/icons/icon-96x96.png'),
                'purpose' => 'any',
            ],
            '128x128' => [
                'path' => pwa_assetPath('public/images/icons/icon-128x128.png'),
                'purpose' => 'any',
            ],
            '144x144' => [
                'path' => pwa_assetPath('public/images/icons/icon-144x144.png'),
                'purpose' => 'any',
            ],
            '152x152' => [
                'path' => pwa_assetPath('public/images/icons/icon-152x152.png'),
                'purpose' => 'any',
            ],
            '192x192' => [
                'path' => pwa_assetPath('public/images/icons/icon-192x192.png'),
                'purpose' => 'any',
            ],
            '384x384' => [
                'path' => pwa_assetPath('public/images/icons/icon-384x384.png'),
                'purpose' => 'any',
            ],
            '512x512' => [
                'path' => pwa_assetPath('public/images/icons/icon-512x512.png'),
                'purpose' => 'any',
            ],
        ],
        'splash' => [
            '640x1136' => pwa_assetPath('public/images/icons/splash-640x1136.png'),
            '750x1334' => pwa_assetPath('public/images/icons/splash-750x1334.png'),
            '828x1792' => pwa_assetPath('public/images/icons/splash-828x1792.png'),
            '1125x2436' => pwa_assetPath('public/images/icons/splash-1125x2436.png'),
            '1242x2208' => pwa_assetPath('public/images/icons/splash-1242x2208.png'),
            '1242x2688' => pwa_assetPath('public/images/icons/splash-1242x2688.png'),
            '1536x2048' => pwa_assetPath('public/images/icons/splash-1536x2048.png'),
            '1668x2224' => pwa_assetPath('public/images/icons/splash-1668x2224.png'),
            '1668x2388' => pwa_assetPath('public/images/icons/splash-1668x2388.png'),
            '2048x2732' => pwa_assetPath('public/images/icons/splash-2048x2732.png'),
        ],
        'shortcuts' => [
            [
                'name' => 'Shortcut Link 1',
                'description' => 'Shortcut Link 1 Description',
                'url' => env('APP_URL', '/'),
                'icons' => [
                    'src' => pwa_assetPath('public/images/icons/icon-72x72.png'),
                    'purpose' => 'any',
                ],
            ],
            [
                'name' => 'Shortcut Link 2',
                'description' => 'Shortcut Link 2 Description',
                'url' => env('APP_URL', '/'),
            ],
        ],
        'custom' => [],
    ],
];
