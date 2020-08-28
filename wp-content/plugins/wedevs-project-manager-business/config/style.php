<?php
$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
$view_path = dirname (__FILE__) . '/../views/';

return [
    'pm-tinymce-mention' => [
		'id'         => 'pm-tinymce-mention',
        'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/css/tinymce-mention.css',
        'path'       => $view_path . '/assets/css/tinymce-mention.css',
		'dependency' => false,
	],
    'pm-pro-style' => [
        'id'         => 'pm-pro-style',
        'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/css/style.css',
        'path'       => $view_path . '/assets/css/style.css',
        'dependency' => [
            'pm-style',
            'pm-tinymce-mention'
        ],
    ],
    'pm-frontend-style' => [
        'id'         => 'pm-frontend-style',
        'url'        => plugin_dir_url( dirname( __FILE__ ) ) . 'views/assets/css/frontend.css',
        'path'       => $view_path . '/assets/css/frontend.css',
        'dependency' => [
            'pm-style',
            'pm-tinymce-mention'
        ],
    ],

];