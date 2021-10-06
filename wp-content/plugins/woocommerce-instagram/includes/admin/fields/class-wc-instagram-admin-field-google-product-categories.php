<?php
/**
 * Field: Google Product Category.
 *
 * @package    WC_Instagram/Admin/Fields
 * @since      3.3.0
 * @deprecated 3.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Instagram_Admin_Field_Google_Product_Categories', false ) ) {
	return;
}

/**
 * Class WC_Instagram_Admin_Field_Google_Product_Categories.
 */
class WC_Instagram_Admin_Field_Google_Product_Categories {

	/**
	 * Gets the field representing the Google product categories of the product.
	 *
	 * @since      3.3.0
	 * @deprecated 3.6.0
	 *
	 * @param int $category_id The category ID.
	 * @return string|false
	 */
	public static function render( $category_id ) {
		wc_deprecated_function( __FUNCTION__, '3.6.0', 'WC_Instagram_Admin_Field_Google_Product_Category::get_selectors()' );

		$categories   = WC_Instagram_Google_Product_Categories::get_parents( $category_id );
		$categories[] = $category_id;

		ob_start();
		?>
		<div id="wc-instagram-google-product-categories-block" class="options_groups">
			<?php
			foreach ( $categories as $index => $category_id ) {
				$options = WC_Instagram_Google_Product_Categories::get_sibling_titles( $category_id );
				$args    = array(
					'options' => $options,
					'value'   => $category_id,
				);

				if ( 0 === $index ) {
					$args = array_merge(
						$args,
						array(
							'label'       => _x( 'Product category', 'product data setting title', 'woocommerce-instagram' ),
							'description' => _x( 'A product category value provided by Google feed.', 'product data setting desc', 'woocommerce-instagram' ),
							'desc_tip'    => true,
						)
					);
				}

				woocommerce_wp_select(
					self::get_wc_wp_select(
						$category_id,
						$args,
						$index > 0
					)
				);
			}

			$category_children = WC_Instagram_Google_Product_Categories::get_children( $category_id );

			if ( ! empty( $category_children ) ) {
				$options = WC_Instagram_Google_Product_Categories::get_titles( $category_children );
				woocommerce_wp_select( self::get_wc_wp_select( null, array( 'options' => $options ), true ) );
			}
			?>

			<input type="hidden" id="_instagram_google_product_category" name="_instagram_google_product_category" value="<?php echo esc_attr( $category_id ); ?>"/>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Returns the data to build a new select depending on a parent one.
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $id             The field id.
	 * @param array      $args           Array of arguments to override defaults.
	 * @param bool       $is_subcategory Changes the default option placeholder text.
	 *
	 * @return array
	 */
	private static function get_wc_wp_select( $id, $args = array(), $is_subcategory = false ) {
		$defaults = array(
			'id'      => '_instagram_gpc_' . $id,
			'name'    => '_instagram_gpc_' . $id,
			'label'   => false,
			'class'   => 'select short wc-instagram-google-product-category-select',
			'options' => array(),
			'value'   => null,
		);

		$args            = wp_parse_args( $args, $defaults );
		$args['options'] = self::add_default_option( $args['options'], $is_subcategory );

		return $args;
	}

	/**
	 * Adds a default option with empty ("") value.
	 *
	 * @param array $options The array of options to add a default empty value.
	 * @param bool  $is_subcategory Used to set the default option placeholder text.
	 *
	 * @return array
	 */
	private static function add_default_option( $options, $is_subcategory = false ) {
		$text = $is_subcategory ? _x( 'Select a subcategory &hellip;', 'product data setting', 'woocommerce-instagram' ) : _x( 'Select a category &hellip;', 'product data setting', 'woocommerce-instagram' );

		return array( '' => $text ) + $options;
	}
}
