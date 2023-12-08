<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return '{{ vc_btn: ' . http_build_query( $atts ) . ' }}';
