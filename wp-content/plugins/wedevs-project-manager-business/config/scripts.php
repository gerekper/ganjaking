<?php

$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
$view_path = dirname (__FILE__) . '/../views/';

return [
    'pm-pro-library' => [
        'id'         => 'pm-pro-library',
        'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/js/pm-pro-library.js',
        'path'       => $view_path . '/assets/js/pm-pro-library.js',
        'dependency' => [
            'pm-const'
        ],
        'in_footer'  => true
    ],
	'pm-pro' => [
		'id'         => 'pm-pro-scripts',
		'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/js/pm-pro.js',
		'path'       => $view_path . '/assets/js/pm-pro.js',
		'dependency' => [
			'pm-pro-library'
		],
		'in_footer'  => true
	],
	'pm-frontend' => [
		'id'         => 'pm-frontend-scripts',
		'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/js/frontend.js',
		'path'       => $view_path . '/assets/js/frontend.js',
		'dependency' => [
			'pm-pro-scripts'
		],
		'in_footer'  => true
	],
];
