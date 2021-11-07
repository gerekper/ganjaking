<?php
/**
 * Class to handle the Google Product Attributes.
 *
 * @package WC_Instagram
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Google_Product_Attributes class.
 */
class WC_Instagram_Google_Product_Attributes {

	/**
	 * Product attributes.
	 *
	 * @var array
	 */
	protected static $attributes = array();

	/**
	 * Loads the attributes.
	 *
	 * @since 3.7.0
	 */
	protected static function load_attributes() {
		$attributes = include WC_INSTAGRAM_PATH . '/data/google-product-attributes.php';

		/**
		 * Filters the Google product attributes.
		 *
		 * @since 3.7.0
		 *
		 * @param array $attributes The Google product attributes.
		 */
		self::$attributes = apply_filters( 'wc_instagram_google_product_attributes', $attributes );
	}

	/**
	 * Gets all attributes.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	public static function get_attributes() {
		if ( empty( self::$attributes ) ) {
			self::load_attributes();
		}

		return self::$attributes;
	}

	/**
	 * Gets an attribute by key.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_key Attribute key.
	 * @return array|false An array with the attribute data. False if not found.
	 */
	public static function get_attribute( $attribute_key ) {
		$attributes = self::get_attributes();

		return ( isset( $attributes[ $attribute_key ] ) ? $attributes[ $attribute_key ] : false );
	}

	/**
	 * Gets the attribute label.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_key Attribute key.
	 * @return string
	 */
	public static function get_label( $attribute_key ) {
		$attribute = self::get_attribute( $attribute_key );

		return ( $attribute && isset( $attribute['label'] ) ? $attribute['label'] : '' );
	}

	/**
	 * Gets the attribute options.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_key Attribute key.
	 * @return array
	 */
	public static function get_options( $attribute_key ) {
		$attribute = self::get_attribute( $attribute_key );

		return ( $attribute && isset( $attribute['options'] ) ? $attribute['options'] : array() );
	}

	/**
	 * Checks if the attribute has options.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_key Attribute key.
	 * @return bool
	 */
	public static function has_options( $attribute_key ) {
		$attribute = self::get_attribute( $attribute_key );

		return ( $attribute && ! empty( $attribute['options'] ) );
	}
}
