<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return '{{ featured_image: ' . http_build_query( $atts ) . ' }}';
