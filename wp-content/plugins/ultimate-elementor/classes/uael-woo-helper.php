<?php
/**
 * UAEL Helper.
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAEL_Woo_Helper.
 */
class UAEL_Woo_Helper {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Short Description.
	 *
	 * @since 0.0.1
	 */
	public function woo_shop_short_desc() {
		if ( has_excerpt() ) {
			echo '<div class="uael-woo-products-description">';
				echo wp_kses_post( the_excerpt() );
			echo '</div>';
		}
	}

	/**
	 * Parent Category.
	 *
	 * @since 1.1.0
	 */
	public function woo_shop_parent_category() {
		if ( apply_filters( 'uael_woo_shop_parent_category', true ) ) : ?>
			<span class="uael-woo-product-category">
				<?php
				global $product;
				$product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), ',', '', '' ) : $product->get_categories( ',', '', '' );

				$product_categories = wp_strip_all_tags( $product_categories );
				if ( $product_categories ) {
					list( $parent_cat ) = explode( ',', $product_categories );
					echo esc_html( $parent_cat );
				}
				?>
			</span> 
			<?php
		endif;
	}

	/**
	 * Product Flip Image.
	 *
	 * @since 0.0.1
	 */
	public function woo_shop_product_flip_image() {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size  = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );
			$swap_images = apply_filters( 'uael_woocommerce_product_flip_image', wp_get_attachment_image( reset( $attachment_ids ), $image_size, false, array( 'class' => 'uael-show-on-hover' ) ) );

			echo wp_kses_post( $swap_images );
		}
	}
}
