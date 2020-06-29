<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/class-vc-grids-common.php';
$masonryMediaGridParams = VcGridsCommon::getMasonryMediaCommonAtts();

return array(
	'name' => esc_html__( 'Masonry Media Grid', 'js_composer' ),
	'base' => 'vc_masonry_media_grid',
	'icon' => 'vc_icon-vc-masonry-media-grid',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Masonry media grid from Media Library', 'js_composer' ),
	'params' => $masonryMediaGridParams,
);
