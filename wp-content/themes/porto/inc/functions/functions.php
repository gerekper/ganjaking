<?php
/**
 * Theme Core Functions
 *
 * @package Porto
 */

require_once( PORTO_FUNCTIONS . '/general.php' );
require_once( PORTO_FUNCTIONS . '/shortcodes.php' );
require_once( PORTO_FUNCTIONS . '/widgets.php' );
require_once( PORTO_FUNCTIONS . '/post.php' );
if ( class_exists( 'WooCommerce' ) ) {
	require_once( PORTO_FUNCTIONS . '/woocommerce.php' );
}

require_once( PORTO_FUNCTIONS . '/layout.php' );
require_once( PORTO_FUNCTIONS . '/html_block.php' );

require_once( PORTO_FUNCTIONS . '/class-dynamic-style.php' );
