<?php
/**
 * Wishlist search header template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $page_title string Page title
 * @var $pages_links string Pagination links
 * @var $search_string string Searched value
 * @var $search_results array Search results
 * @var $template_part string Template part currently being loaded (search)
 * @var $default_wishlist_title string Default wishlist title
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'search' ) ) ?>" method="post">
	<!-- TITLE -->
	<?php
	do_action( 'yith_wcwl_before_wishlist_title' );

	if( ! empty( $page_title ) ) {
		echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	do_action( 'yith_wcwl_before_wishlist_search' );
	?>