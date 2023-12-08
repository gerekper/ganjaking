<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/class-vc-grids-common.php';
$masonryGridParams = VcGridsCommon::getMasonryCommonAtts();

return array(
	'name' => esc_html__( 'Post Masonry Grid', 'js_composer' ),
	'base' => 'vc_masonry_grid',
	'icon' => 'vc_icon-vc-masonry-grid',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Posts, pages or custom posts in masonry grid', 'js_composer' ),
	'params' => $masonryGridParams,
);
