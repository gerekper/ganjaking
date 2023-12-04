<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks\MultipleKB;
use WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks\AdvancedSearch;

add_filter('betterdocs_pro_blocks_config', function($blocks){
    $blocks['searchform']['object'] = AdvancedSearch::class;
    return $blocks;
});

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
