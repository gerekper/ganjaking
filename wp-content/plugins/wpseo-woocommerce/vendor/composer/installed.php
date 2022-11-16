<?php return array(
    'root' => array(
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => '258ee7ed596f6962e7ae6def06e0735882ff8c92',
        'name' => 'yoast/wpseo-woocommerce',
        'dev' => false,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'yoast/i18n-module' => array(
            'pretty_version' => '3.1.1',
            'version' => '3.1.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../yoast/i18n-module',
            'aliases' => array(),
            'reference' => '9d0a2f6daea6fb42376b023e7778294d19edd85d',
            'dev_requirement' => false,
        ),
        'yoast/wpseo-woocommerce' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => '258ee7ed596f6962e7ae6def06e0735882ff8c92',
            'dev_requirement' => false,
        ),
    ),
);
