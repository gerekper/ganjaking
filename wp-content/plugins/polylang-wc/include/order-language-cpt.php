<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setups the order language model when orders are managed with a custom post type.
 *
 * @since 1.0
 */
class PLLWC_Order_Language_CPT extends PLLWC_Object_Language {

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 */
	public function __construct() {
		$this->object = PLL()->model->post;
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.0
	 * @since 1.9 Returns an instance of this object.
	 * @since 1.9 Type-hinted.
	 *
	 * @return self
	 */
	public function init() {
		add_filter( 'pll_get_post_types', array( $this, 'translated_post_types' ), 10, 2 );
		add_filter( 'pll_bulk_translate_post_types', array( $this, 'bulk_translate_post_types' ) );
		return $this;
	}

	/**
	 * Adds orders to the list of the translated post types.
	 *
	 * @since 1.0
	 *
	 * @param string[] $types List of post type names for which Polylang manages language and translations.
	 * @param bool     $hide  True when displaying the list in Polylang settings.
	 * @return string[] List of post type names for which Polylang manages language and translations.
	 *
	 * @phpstan-param array<non-falsy-string> $types
	 * @phpstan-return array<non-falsy-string>
	 */
	public function translated_post_types( $types, $hide ) {
		$translated_order_types = $this->get_post_types();

		return $hide ? array_diff( $types, $translated_order_types ) : array_merge( $types, $translated_order_types );
	}

	/**
	 * Removes the order post type from the bulk translate action.
	 *
	 * @since 1.0.4
	 *
	 * @param string[] $types List of post type names for which Polylang manages the bulk translate.
	 * @return string[]
	 *
	 * @phpstan-param array<non-falsy-string> $types
	 * @phpstan-return array<non-falsy-string>
	 */
	public function bulk_translate_post_types( $types ) {
		return array_diff( $types, $this->get_post_types( 'display' ) );
	}

	/**
	 * Gets the list of post types available for translation.
	 *
	 * @since 1.9
	 *
	 * @param string $context Either 'default' or 'display', defaults to 'default'.
	 *
	 * @return string[] List of post type names.
	 *
	 * @phpstan-return array<non-falsy-string>
	 */
	public function get_post_types( $context = 'default' ) {
		$woo_types = array( 'shop_order' );

		if ( 'display' !== $context ) {
			$woo_types[] = 'shop_order_placehold';
		}

		/**
		 * Filters the list of order types available for translation.
		 *
		 * @since 1.9
		 *
		 * @param string[] $order_types List of order type names.
		 * @param string   $context     Either 'default' or 'display'.
		 *
		 * @phpstan-param array<non-falsy-string> $order_types
		 */
		return apply_filters( 'pllwc_get_order_types', $woo_types, $context );
	}
}
