<?php return array(
    'root' => array(
        'name' => 'woocommerce/woocommerce-subscriptions',
        'pretty_version' => 'dev-trunk',
        'version' => 'dev-trunk',
        'reference' => 'c58cc046eb6fc17cec783e270059000e6a9ebfb8',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
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
        'woocommerce/subscriptions-core' => array(
            'pretty_version' => '5.7.0',
            'version' => '5.7.0.0',
            'reference' => 'acf0ec932bea59c92bab0fe2a469a9313b3b6f05',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../woocommerce/subscriptions-core',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'woocommerce/woocommerce-subscriptions' => array(
            'pretty_version' => 'dev-trunk',
            'version' => 'dev-trunk',
            'reference' => 'c58cc046eb6fc17cec783e270059000e6a9ebfb8',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
