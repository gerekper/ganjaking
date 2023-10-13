<?php
/**
 * Add to wishlist button template - Added to list
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\AddToWishlist
 * @version 3.0.12
 */

/**
 * Template variables:
 *
 * @var $wishlist_url              string Url to wishlist page
 * @var $exists                    bool Whether current product is already in wishlist
 * @var $show_exists               bool Whether to show already in wishlist link on multi wishlist
 * @var $product_id                int Current product id
 * @var $parent_product_id         int Parent for current product
 * @var $product_type              string Current product type
 * @var $label                     string Button label
 * @var $browse_wishlist_text      string Browse wishlist text
 * @var $already_in_wishslist_text string Already in wishlist text
 * @var $product_added_text        string Product added text
 * @var $icon                      string Icon for Add to Wishlist button
 * @var $link_classes              string Classed for Add to Wishlist button
 * @var $available_multi_wishlist  bool Whether add to wishlist is available or not
 * @var $disable_wishlist          bool Whether wishlist is disabled or not
 * @var $template_part             string Template part
 * @var $loop_position             string Loop position
 * @var $is_single                 bool Whether we're on single template
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

global $product;
?>

<!-- ADDED TO WISHLIST MESSAGE -->
<div class="yith-wcwl-wishlistaddedbrowse" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-original-product-id="<?php echo esc_attr( $parent_product_id ); ?>">
	<span class="feedback">
		<?php echo yith_wcwl_kses_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo wp_kses_post( $product_added_text ); ?>
	</span>
	<a href="<?php echo esc_url( $wishlist_url ); ?>" rel="nofollow" data-title="<?php echo esc_attr( $browse_wishlist_text ); ?>">
		<?php echo ( ! $is_single && 'before_image' === $loop_position ) ? yith_wcwl_kses_icon( $icon ) : false; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php
		/**
		 * APPLY_FILTERS: yith_wcwl_browse_wishlist_label
		 *
		 * Filter the label to browse the wishlist.
		 *
		 * @param string $text       Browse wishlist text
		 * @param int    $product_id Product ID
		 * @param string $icon       Icon
		 *
		 * @return string
		 */
		echo wp_kses_post( apply_filters( 'yith_wcwl_browse_wishlist_label', $browse_wishlist_text, $product_id, $icon ) );
		?>
	</a>
</div>
