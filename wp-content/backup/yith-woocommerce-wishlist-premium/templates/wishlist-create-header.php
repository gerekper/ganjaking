<?php
/**
 * Wishlist create header template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Template variables:
 *
 * @var $page_title string Page title
 */
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'create' ) ); ?>" method="post">
	<!-- TITLE -->
	<?php
	do_action( 'yith_wcwl_before_wishlist_title' );

	if( ! empty( $page_title ) ) {
		echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	do_action( 'yith_wcwl_before_wishlist_create' );
	?>