<?php
/**
 * The admin class.
 *
 * @package WC_Products_Compare
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Products_Compare_Admin class.
 *
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing, WordPress.Security.NonceVerification.Recommended
 */
class WC_Products_Compare_Admin {

	/**
	 * Class instance.
	 *
	 * @var WC_Products_Compare_Admin
	 */
	private static $instance;

	/**
	 * Init.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'woocommerce_system_status_report', array( $this, 'render_debug_fields' ) );
	}

	/**
	 * Get object instance
	 *
	 * @since 1.0.0
	 * @return instance object
	 */
	public function get_instance() {
		return self::$instance;
	}

	/**
	 * Renders the debug fields
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function render_debug_fields() {
		?>
		<table class="wc_status_table widefat" cellspacing="0" id="status">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Products Compare"><?php esc_html_e( 'Products Compare', 'woocommerce-products-compare' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td data-export-label="Template Overrides"><?php esc_html_e( 'Template Overrides:', 'woocommerce-products-compare' ); ?></td>
					<td colspan="2">
						<?php
						$theme = wp_get_theme();

						if ( file_exists( get_stylesheet_directory() . '/woocommerce-products-compare/products-compare-page-html.php' ) ) {
							echo esc_html( strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-products-compare/products-compare-page-html.php' );
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

		return true;
	}
}

new WC_Products_Compare_Admin();
