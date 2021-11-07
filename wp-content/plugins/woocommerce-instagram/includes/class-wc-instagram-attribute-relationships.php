<?php
/**
 * Handles the WooCommerce attributes' relationships.
 *
 * @package WC_Instagram
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Attribute_Relationships class.
 */
class WC_Instagram_Attribute_Relationships {

	/**
	 * Relationships.
	 *
	 * Maps WC attributes with Google product attributes.
	 *
	 * @var array An array of pairs [attribute_id => google_pa].
	 */
	protected static $relationships;

	/**
	 * Loads the attributes' relationships.
	 *
	 * @since 3.7.0
	 */
	protected static function load_relationships() {
		self::$relationships = array();

		$attributes = wc_get_attribute_taxonomies();

		foreach ( $attributes as $attribute ) {
			$google_pa = WC_Instagram_Attributes::get_meta( $attribute->attribute_id, 'google_pa' );

			if ( $google_pa ) {
				self::$relationships[ $attribute->attribute_id ] = $google_pa;
			}
		}
	}

	/**
	 * Gets the attributes' relationships.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	public static function get_relationships() {
		if ( is_null( self::$relationships ) ) {
			self::load_relationships();
		}

		return self::$relationships;
	}

	/**
	 * Gets the Google product attribute related to the WC attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_id Attribute ID.
	 * @return string|false The Google product attribute ID. False if not found.
	 */
	public static function get_relationship( $attribute_id ) {
		$relationships = self::get_relationships();

		return ( isset( $relationships[ $attribute_id ] ) ? $relationships[ $attribute_id ] : false );
	}
}
