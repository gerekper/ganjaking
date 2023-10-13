<?php
/**
 * Wishlist search footer template
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

	<?php
	/**
	 * DO_ACTION: yith_wcwl_after_wishlist_search
	 *
	 * Allows to render some content or fire some action after the wishlist search.
	 */
	do_action( 'yith_wcwl_after_wishlist_search' );
	?>

</form>
