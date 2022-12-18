<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'porto_supported_post_types' ) ) :

	function porto_supported_post_types() {
		return array( 'post', 'product', 'portfolio', 'member', 'faq' );
	}
endif;
