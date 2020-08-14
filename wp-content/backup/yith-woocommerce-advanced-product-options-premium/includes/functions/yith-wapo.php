<?php

if ( ! function_exists( 'YITH_WAPO' ) ) {
	/**
	 * Unique access to instance of YITH_Vendors class
	 *
	 * @since  1.0
	 * @author Your Inspiration Themes
	 */
	function YITH_WAPO() {
		return YITH_WAPO::instance();
	}
}

if ( ! function_exists( 'yith_wapo_init' ) ) {
	/**
	 * Product Add-ons Init
	 *
	 * @since  1.0
	 * @author Your Inspiration Themes
	 */
	function yith_wapo_init() {
		if ( ! apply_filters( 'yith_wapo_disable_init', false ) ) {
			if ( function_exists( 'WC' ) ) {
				require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-group.php' );
				require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-option.php' );
				require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-settings.php' );
				require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-type.php' );
				require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo.php' );
				if ( defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ) {
					require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-premium.php' );
				}
				if ( get_option( 'yith_wapo_settings_disable_wccl', false ) != 'yes' && ! function_exists( 'YITH_WCCL' ) ) {

					require_once( YITH_WAPO_DIR . 'includes/functions/yith-wccl.php' );
					require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wccl.php' );
					require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wccl-admin.php' );
					require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wccl-frontend.php' );
					! defined( 'YITH_WCCL_DB_VERSION' ) && define( 'YITH_WCCL_DB_VERSION', '1.0.0' );
					! defined( 'YITH_WAPO_WCCL' ) && define( 'YITH_WAPO_WCCL', true );
					YITH_WCCL();
					// Check for update table
					if ( function_exists( 'yith_wccl_update_db_check' ) ) {
						yith_wccl_update_db_check();
					}
				}
				load_plugin_textdomain( YITH_WAPO_LOCALIZE_SLUG, false, YITH_WAPO_DIR_NAME . '/languages' );
				YITH_WAPO();
			} else {
				add_action( 'admin_notices', 'yith_wapo_install_woocommerce_admin_notice' );
			}
		}
	}
}

if ( ! function_exists( 'yith_wapo_install_woocommerce_admin_notice' ) ) {
	/**
	 * Print an admin notice if WooCommerce is deactivated
	 *
	 * @since  1.0
	 * @author Your Inspiration Themes
	 */
	function yith_wapo_install_woocommerce_admin_notice() { ?>
		<div class="error">
			<p>YITH WooCommerce Product Add-Ons <?php _e( 'is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-add-ons' ); ?></p>
		</div>
	<?php
	}
}

if ( ! function_exists( 'yith_wapo_multi_products_select' ) ) {
	/**
	 * Print a multi product select component
	 *
	 * @since  1.5.2
	 * @author Your Inspiration Themes
	 */
	function yith_wapo_multi_products_select( $name, $selected_products ) { ?>
		<select
			class="wc-product-search"
			multiple="multiple"
			name="<?php echo $name; ?>"
			data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
			data-action="woocommerce_json_search_products"
			data-multiple="true"
			data-exclude=""
			style="width: 50%;"><?php
				if ( ! is_array( $selected_products ) ) {
					$selected_products = array_filter( array_map( 'absint', explode( ',', $selected_products ) ) );
				}
				foreach ( $selected_products as $product_id ) {
					$product = wc_get_product( $product_id );
					echo is_object( $product ) ? '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>' : '';
				}
			?>
		</select>
		<?php
	}
}
