<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/class-vc-grids-common.php';
$mediaGridParams = VcGridsCommon::getMediaCommonAtts();

return array(
	'name' => esc_html__( 'Media Grid', 'js_composer' ),
	'base' => 'vc_media_grid',
	'icon' => 'vc_icon-vc-media-grid',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Media grid from Media Library', 'js_composer' ),
	'params' => $mediaGridParams,
);
