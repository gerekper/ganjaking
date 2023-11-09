<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks\MultipleKB;

return [
    'multiple-kb'       => [
        'label'      => __( 'BetterDocs Multiple KB', 'betterdocs-pro' ),
        'value'      => 'multiple-kb',
        'visibility' => true,
        'object'     => MultipleKB::class,
        'demo'       => '',
        'docs'       => ''
    ],
];
