<?php
/**
 * A class for representing an item in a product catalog.
 *
 * @package WC_Instagram/Product_Catalog/Items
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_Item_Variation class.
 */
class WC_Instagram_Product_Catalog_Item_Variation extends WC_Instagram_Product_Catalog_Item {

	/**
	 * The parent product.
	 *
	 * @since 3.0.1
	 *
	 * @var WC_Product
	 */
	protected $parent;

	/**
	 * Returns the parent product to work with. Use this method to obtain the postmeta data.
	 *
	 * @{inheritdoc}
	 */
	protected function get_target() {
		return $this->get_parent();
	}

	/**
	 * Gets the parent product.
	 *
	 * @since 3.0.1
	 */
	public function get_parent() {
		if ( is_null( $this->parent ) ) {
			$this->parent = wc_get_product( $this->get_product()->get_parent_id() );
		}

		return $this->parent;
	}

	/**
	 * Gets the specified product property and use the parent value as a fallback.
	 *
	 * The property must exist in the product variation.
	 *
	 * @since 3.0.1
	 *
	 * @param string $prop The product property.
	 * @return mixed The property value. Null on failure.
	 */
	public function get_prop_with_parent_fallback( $prop ) {
		$getter = "get_{$prop}";

		if ( ! is_callable( array( $this->get_product(), $getter ) ) ) {
			return null;
		}

		$value = call_user_func( array( $this->get_product(), $getter ) );

		// Use the parent value instead.
		if ( ! $value ) {
			$parent = $this->get_parent();

			if ( $parent && is_callable( array( $parent, $getter ) ) ) {
				$value = call_user_func( array( $parent, $getter ) );
			}
		}

		return $value;
	}

	/**
	 * Gets the product's group ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Optional. The product group ID format. Default '{parent_id}'.
	 * @return int
	 */
	public function get_group_id( $format = '{parent_id}' ) {
		$group_id = $this->parse_format( $format, $this->get_product()->get_parent_id() );

		/**
		 * Filters the product's group ID.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $group_id The group ID.
		 * @param string     $format   The group ID format.
		 * @param WC_Product $product  Product object.
		 */
		return apply_filters( 'wc_instagram_product_group_id', $group_id, $format, $this->get_product() );
	}

	/**
	 * Gets the product description.
	 *
	 * @since 3.0.1
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->get_prop_with_parent_fallback( 'description' );
	}

	/**
	 * Gets the product short description.
	 *
	 * @since 3.4.2
	 *
	 * @return string
	 */
	public function get_short_description() {
		// Variations don't have short descriptions.
		$parent = $this->get_parent();

		return ( $parent ? $parent->get_short_description() : '' );
	}

	/**
	 * Gets the product category IDs.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function get_category_ids() {
		// Variations don't have category IDs.
		$parent = $this->get_parent();

		return ( $parent ? $parent->get_category_ids() : array() );
	}
}
