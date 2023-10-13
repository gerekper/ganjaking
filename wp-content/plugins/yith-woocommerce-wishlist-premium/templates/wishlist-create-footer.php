<?php
/**
 * Wishlist create footer template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\Create
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

	<?php
	/**
	 * DO_ACTION: yith_wcwl_after_wishlist_create
	 *
	 * Allows to render some content or fire some action after the wishlist creation options.
	 */
	do_action( 'yith_wcwl_after_wishlist_create' );
	?>
</form>
