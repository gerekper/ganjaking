<?php
/**
 * Product Add-ons admin
 *
 * @package WC_Product_Addons/Classes/Legacy/Admin
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Admin_Legacy class.
 */
class Product_Addon_Admin_Legacy extends Product_Addon_Admin {

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ), 100 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'tab' ) );
		add_action( 'woocommerce_product_write_panels', array( $this, 'panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box' ), 1 );
	}

	/**
	 * Add product panel.
	 */
	public function panel() {
		global $post;

		$exists         = isset( $post->ID );
		$product_addons = array_filter( (array) get_post_meta( $post->ID, '_product_addons', true ) );
		$exclude_global = get_post_meta( $post->ID, '_product_addons_exclude_global', true );

		include( dirname( __FILE__ ) . '/views/html-addon-panel.php' );
	}

	/**
	 * Process meta box.
	 *
	 * @param int $post_id Post ID.
	 */
	public function process_meta_box( $post_id ) {
		// Save addons as serialised array.
		$product_addons                = $this->get_posted_product_addons();
		$product_addons_exclude_global = isset( $_POST['_product_addons_exclude_global'] ) ? 1 : 0;

		update_post_meta( $post_id, '_product_addons', $product_addons );
		update_post_meta( $post_id, '_product_addons_exclude_global', $product_addons_exclude_global );
	}
}
