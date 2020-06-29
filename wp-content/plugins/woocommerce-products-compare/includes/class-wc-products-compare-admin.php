<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Products_Compare_Admin {
	private static $_this;

	/**
	 * init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'woocommerce_system_status_report', array( $this, 'render_debug_fields' ) );

    	return true;
	}

	/**
	 * Get object instance
	 *
	 * @access public
	 * @since 1.0.0
	 * @return instance object
	 */
	public function get_instance() {
		return self::$_this;
	}

	/**
	 * Renders the debug fields
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function render_debug_fields() {
	?>	
		<table class="wc_status_table widefat" cellspacing="0" id="status">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Store Catalog PDF Download">Products Compare</th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td data-export-label="Template Overrides"><?php _e( 'Template Overrides:', 'woocommerce-products-compare' ); ?></td>
					<td colspan="2">
						<?php $theme = wp_get_theme(); ?>
						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-products-compare/products-compare-page-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-products-compare/products-compare-page-html.php';
						} ?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php

	return true;
	}
}

new WC_Products_Compare_Admin();