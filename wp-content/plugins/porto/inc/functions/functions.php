<?php
/**
 * Theme Core Functions
 *
 * @package Porto
 */

require_once( PORTO_FUNCTIONS . '/general.php' );

if ( defined( 'WPB_VC_VERSION' ) ) {
	require_once PORTO_FUNCTIONS . '/wpb-elements.php';
}

require_once( PORTO_FUNCTIONS . '/shortcodes.php' );
require_once( PORTO_FUNCTIONS . '/widgets.php' );
require_once( PORTO_FUNCTIONS . '/post.php' );

if ( class_exists( 'Woocommerce' ) ) {
	if ( porto_is_elementor_preview() ) {
		add_action(
			'init',
			function() {
				require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
			},
			8
		);
	} else {
		require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
	}
}

require_once( PORTO_FUNCTIONS . '/layout.php' );
require_once( PORTO_FUNCTIONS . '/html_block.php' );

require_once( PORTO_FUNCTIONS . '/class-dynamic-style.php' );

require_once( PORTO_FUNCTIONS . '/class-performance.php' );
