<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! defined( 'WCWL_VERSION' ) ) {
	define( 'WCWL_VERSION', '2.2.5' );
}
if ( ! defined( 'WCWL_SLUG' ) ) {
	define( 'WCWL_SLUG', 'woocommerce_waitlist' );
}
if ( ! defined( 'WCWL_ENQUEUE_PATH' ) ) {
	define( 'WCWL_ENQUEUE_PATH', plugins_url( '', __FILE__ ) );
}
