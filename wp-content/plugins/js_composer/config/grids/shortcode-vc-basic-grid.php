<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/class-vc-grids-common.php';
$gridParams = VcGridsCommon::getBasicAtts();

return array(
	'name' => esc_html__( 'Post Grid', 'js_composer' ),
	'base' => 'vc_basic_grid',
	'icon' => 'icon-wpb-application-icon-large',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Posts, pages or custom posts in grid', 'js_composer' ),
	'params' => $gridParams,
);
