<?php
/**
 * WC Attributes handler.
 *
 * @package WC_Instagram
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Attributes class.
 */
class WC_Instagram_Attributes {

	/**
	 * Gets the meta value of an attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param int    $attribute_id Attribute ID.
	 * @param string $key          Meta key.
	 * @param mixed  $default      Optional. Value if the meta doesn't exist. Default null.
	 * @return mixed
	 */
	public static function get_meta( $attribute_id, $key, $default = null ) {
		return get_option( self::get_meta_id( $attribute_id, $key ), $default );
	}

	/**
	 * Sets the meta value of an attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param int    $attribute_id Attribute ID.
	 * @param string $key          Meta key.
	 * @param mixed  $value        Meta value.
	 */
	public static function set_meta( $attribute_id, $key, $value ) {
		update_option( self::get_meta_id( $attribute_id, $key ), $value );
	}

	/**
	 * Deletes the meta from an attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param int    $attribute_id Attribute ID.
	 * @param string $key          Meta key.
	 */
	public static function delete_meta( $attribute_id, $key ) {
		delete_option( self::get_meta_id( $attribute_id, $key ) );
	}

	/**
	 * Gets the meta ID.
	 *
	 * @since 3.7.0
	 *
	 * @param int    $attribute_id Attribute ID.
	 * @param string $key          Meta key.
	 * @return string
	 */
	protected static function get_meta_id( $attribute_id, $key ) {
		return 'wc_instagram_pa_' . $attribute_id . '_' . $key;
	}
}
