<?php
/**
 * Wishlist search footer template
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

	<?php do_action( 'yith_wcwl_after_wishlist_search' ); ?>

</form>