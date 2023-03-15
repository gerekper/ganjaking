<?php return array(
    'root' => array(
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => 'c7e4842a1b9c8028351236ebe4677713fbf1764e',
        'name' => 'yoast/wordpress-seo',
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
        'yoast/whip' => array(
            'pretty_version' => '1.2.0',
            'version' => '1.2.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../yoast/whip',
            'aliases' => array(),
            'reference' => '49a6120d89a53874391d1451be8ef279feec0eae',
            'dev_requirement' => false,
        ),
        'yoast/wordpress-seo' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => 'c7e4842a1b9c8028351236ebe4677713fbf1764e',
            'dev_requirement' => false,
        ),
    ),
);
