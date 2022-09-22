<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Class WPSEO_WooCommerce_Yoast_Ids
 */
class WPSEO_WooCommerce_Yoast_Ids {

	/**
	 * The array of allowed identifier types.
	 *
	 * @var string[]
	 */
	protected $global_identifier_types = [
		'gtin8'  => 'GTIN8',
		'gtin12' => 'GTIN12 / UPC',
		'gtin13' => 'GTIN13 / EAN',
		'gtin14' => 'GTIN14 / ITF-14',
		'mpn'    => 'MPN',
	];

	/**
	 * WPSEO_WooCommerce_Yoast_Ids constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'add_variations_global_ids' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_data' ], 10, 1 );
	}

	/**
	 * Add global identifiers text fields to a variation description.
	 *
	 * @param int     $loop           The iteration number.
	 * @param array   $variation_data Data related to the variation.
	 * @param WP_Post $variation      The variation object.
	 *
	 * @return void
	 */
	public function add_variations_global_ids( $loop, $variation_data, $variation ) {
		echo '<h1>' . esc_html__( 'Yoast SEO options', 'yoast-woo-seo' ) . '</h1>';
		echo '<p>' . esc_html__( 'If this product variation has unique identifiers, you can enter them here', 'yoast-woo-seo' ) . '</p>';

		$variation_values = get_post_meta( $variation->ID, 'wpseo_variation_global_identifiers_values', true );

		echo '<div>';
		$is_left = true;
		foreach ( $this->global_identifier_types as $type => $label ) {
			$value = isset( $variation_values[ $type ] ) ? $variation_values[ $type ] : '';
			$this->input_field_for_identifier( $variation->ID, $type, $label, $value, $is_left );
			$is_left = ! $is_left;
		}
		wp_nonce_field( 'yoast_woo_seo_variation_identifiers', '_wpnonce_yoast_seo_woo_gids' );
		echo '</div>';
	}

	/**
	 * Validate the variation global identifiers upon sanitizing them.
	 *
	 * @param int $variation_id The id of the variation to be validated.
	 *
	 * @return array Validated variation global identifiers.
	 */
	protected function validate_variation_data( $variation_id ) {
		$values = [];
		foreach ( $this->global_identifier_types as $key => $label ) {
			// Ignoring nonce verification as we do that in save_data function.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$values[ $key ] = isset( $_POST['yoast_seo_variation'][ $variation_id ][ $key ] ) ? \sanitize_text_field( \wp_unslash( $_POST['yoast_seo_variation'][ $variation_id ][ $key ] ) ) : '';
		}

		return $values;
	}

	/**
	 * Save the $_POST values from the variation global identifiers input fiends.
	 *
	 * @param int $variation_id The variation ID.
	 *
	 * @return bool Whether or not we saved data.
	 */
	public function save_data( $variation_id ) {
		if ( ! isset( $_POST['_wpnonce_yoast_seo_woo_gids'] ) ) {
			return false;
		}
		$nonce = \sanitize_text_field( \wp_unslash( $_POST['_wpnonce_yoast_seo_woo_gids'] ) );
		if ( ! \wp_verify_nonce( $nonce, 'yoast_woo_seo_variation_identifiers' ) ) {
			return false;
		}

		$values = $this->validate_variation_data( $variation_id );
		if ( $values !== [] ) {
			return update_post_meta( $variation_id, 'wpseo_variation_global_identifiers_values', $values );
		}

		return false;
	}

	/**
	 * Displays an input field for an identifier.
	 *
	 * @param string $variation_id The id of the variation.
	 * @param string $type         Type of identifier, used for input name.
	 * @param string $label        Label for the identifier input.
	 * @param string $value        Current value of the identifier.
	 * @param bool   $is_left      Wether the field is on the left or not.
	 *
	 * @return void
	 */
	protected function input_field_for_identifier( $variation_id, $type, $label, $value, $is_left ) {
		$style = ( $is_left ) ? 'style="display: inline-block; margin-bottom: 0em; width: 48%; float: left;"' : 'style="display: inline-block; margin-bottom: 0em; width: 48%; float: right;"';
		// Ignoring escaping because it would mangle the double quotes.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p ', $style, '>';
		echo '<label for="', esc_attr( 'yoast_variation_identifier[' . $variation_id . '][' . $type . ']' ), '" style="display: block;">', esc_html( $label ), '</label>';
		echo '<input class="short" type="text" style="margin: 2px 0 0; line-height: 2.75; width: 100%;" id="', esc_attr( 'yoast_variation_identifier[' . $variation_id . '][' . $type . ']' ), '" name="', esc_attr( 'yoast_seo_variation[' . $variation_id . '][' . $type . ']' ), '" value="', esc_attr( $value ), '"/>';
		echo '</p>';
	}
}
