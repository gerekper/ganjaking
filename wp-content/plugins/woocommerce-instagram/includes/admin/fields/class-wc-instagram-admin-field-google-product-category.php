<?php
/**
 * Field: Google Product Category.
 *
 * @package WC_Instagram/Admin/Fields
 * @since   3.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Instagram_Admin_Field_Google_Product_Category', false ) ) {
	return;
}

/**
 * Class WC_Instagram_Admin_Field_Google_Product_Category.
 */
class WC_Instagram_Admin_Field_Google_Product_Category {

	/**
	 * Gets all the select fields for the Google Product Category.
	 *
	 * @since 3.6.0
	 *
	 * @param int $category_id The category ID.
	 * @return string
	 */
	public static function get_selectors( $category_id ) {
		$parents   = WC_Instagram_Google_Product_Categories::get_parents( $category_id );
		$parents[] = $category_id;

		$html = '';

		foreach ( $parents as $index => $parent_id ) {
			$options = WC_Instagram_Google_Product_Categories::get_sibling_titles( $parent_id );
			$args    = array(
				'options' => self::add_default_option( $options, $index > 0 ),
				'value'   => $parent_id,
			);

			$html .= self::get_select_field_html( $args );
		}

		$html .= self::get_child_selector( $category_id );

		return $html;
	}

	/**
	 * Gets a select field with the subcategories of the specified Google Product Category.
	 *
	 * @since 3.6.0
	 *
	 * @param int $category_id The category ID.
	 * @return string
	 */
	public static function get_child_selector( $category_id ) {
		$children = WC_Instagram_Google_Product_Categories::get_children( $category_id );

		if ( empty( $children ) ) {
			return '';
		}

		$options = WC_Instagram_Google_Product_Categories::get_titles( $children );
		$args    = array(
			'options' => self::add_default_option( $options, true ),
		);

		return self::get_select_field_html( $args );
	}

	/**
	 * Gets a select field HTML.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Array of arguments to override defaults.
	 * @return string
	 */
	private static function get_select_field_html( $args = array() ) {
		$defaults = array(
			'class'   => 'select',
			'value'   => '',
			'options' => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		ob_start();
		echo '<div class="wc-instagram-gpc-select-wrapper">';
		printf( '<select class="%1$s">', esc_attr( $args['class'] ) );
		foreach ( $args['options'] as $value => $label ) {
			printf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $value ),
				selected( $value, $args['value'], false ),
				esc_html( $label )
			);
		}
		echo '</select></div>';

		return ob_get_clean();
	}

	/**
	 * Adds a default option with empty ("") value.
	 *
	 * @since 3.6.0
	 *
	 * @param array $options The array of options to add a default empty value.
	 * @param bool  $is_subcategory Used to set the default option placeholder text.
	 * @return array
	 */
	private static function add_default_option( $options, $is_subcategory = false ) {
		$text = (
			$is_subcategory ?
			_x( 'Select a subcategory &hellip;', 'product data setting', 'woocommerce-instagram' ) :
			_x( 'Select a category &hellip;', 'product data setting', 'woocommerce-instagram' )
		);

		return array( '' => $text ) + $options;
	}
}
