<?php
/**
 * Wishlist search header template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\Search
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

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'search' ) ); ?>" method="post">
	<!-- TITLE -->
	<?php
	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_title
	 *
	 * Allows to render some content or fire some action before the wishlist title.
	 */
	do_action( 'yith_wcwl_before_wishlist_title' );

	if ( ! empty( $page_title ) ) {
		/**
		 * APPLY_FILTERS: yith_wcwl_wishlist_title
		 *
		 * Filter the title of the Wishlist page.
		 *
		 * @param string $title Wishlist page title
		 *
		 * @return string
		 */
		echo wp_kses_post( apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ) );
	}

	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_search
	 *
	 * Allows to render some content or fire some action before the wishlist search.
	 */
	do_action( 'yith_wcwl_before_wishlist_search' );
	?>
