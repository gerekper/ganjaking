<?php
/**
 * Plugin Name: WooCommerce Dropshipping
 * Plugin URI: http://woocommerce.com/products/woocommerce-dropshipping/
 * Description: Handle dropshipping from your WooCommerce. Create a packing slip, and notify the vendor when an order is paid. Import inventory updates via CSV from your vendors.
 * Version: 2.5
 * Author: WooCommerce
 * Author URI: http://woocommerce.com/
 * Developer: OPMC
 * Developer URI: https://opmc.com.au/
 * Requires at least: 4.5
 * Tested up to: 5.2.3
 * Copyright: © 2009-2018 WooThemes.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 1923014:403b956c6bd33bb70b089df260b994ee
 *
 * Text Domain: woocommerce-dropshipping
 * Domain Path: /languages/
 *
 * @package WC_Dropshipping
 * @category Extension
 * @author WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '403b956c6bd33bb70b089df260b994ee', '1923014' );

/**
 * Dropshipping allow_url_fopen Missing Notice
 */
if (!ini_get('allow_url_fopen') == 1)
{


	add_action( 'admin_notices', 'wcbd_allow_url_fopen' );

}
if ( ! function_exists( 'wcbd_allow_url_fopen' ) ) {

	function wcbd_allow_url_fopen() {

		/* translators: 1: href link to Fileinfo extension php doc */
		echo '<div class="notice"><p>' . sprintf( __( 'WooCommerce Dropshipping requires %s to be installed on your server.', 'woocommerce-Dropshipping' ), '<a href="https://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">allow_url_fopen </a>extension' ) . '</p></div>';

	}
}

/**
 * End
 */

/**
 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WC_Dropshipping
 */
function WC_Dropshipping() {
	return WC_Dropshipping::instance();
} // End WC_Dropshipping()


WC_Dropshipping();

final class WC_Dropshipping {
	/**
	 * WC_Dropshipping The single instance of WC_Dropshipping.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	public $data_table = '';
	public $base_path = '';

	public $admin = null;

	public function __construct() {
		$this->version = '3';
		$this->plugin_name = __( 'WooCommerce Dropshipping', 'woocommerce-dropshipping' );
		$this->base_path = plugin_dir_path( __FILE__ );
		require_once( 'woocommerce-dropshipping-functions.php' );
		// Include AliExpress file
		require_once( 'ali-api/woocommerce_aliexpress.php' );
		require_once( 'ali-api/class-wc-dropshipping-product-extra-fields.php' );
		$this->fields = new WC_Dropshipping_Product_Extra_Fields();
		require_once( 'ali-api/ali-inc/aliprodfilter.inc.php' );
		register_activation_hook(__FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'init', array( $this,'init_supplier_taxonomy' ), 0 );
		add_filter( 'manage_edit-product_sortable_columns', array( $this, 'make_dropship_supplier_column_sort' ) );
		add_filter( 'posts_clauses', array($this,'dropship_supplier_column_orderby'),10,2 );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init () {
		do_action( 'wc_dropship_manager_init' );
		if ( is_admin() ) {
			require_once('inc/class-wc-dropshipping-admin.php');
			$this->admin = new WC_Dropshipping_Admin();
		}
		require_once('inc/class-wc-dropshipping-orders.php');
		$this->orders = new WC_Dropshipping_Orders();
	}

	public function activate() {
		$options = get_option( 'wc_dropship_manager' );
		if ( ! is_array( $options ) ) {
			$options = array(
				'inventory_pad' => '5',
				'packing_slip_url_to_logo' => '',
				'email_order_note' => sprintf( __( 'Please see the attached PDF. Thank you! - %s', 'woocommerce-dropshipping' ), get_bloginfo( 'name' ) ),
				'packing_slip_address' => '',
				'packing_slip_customer_service_email' => '',
				'packing_slip_customer_service_phone' => '',
				'packing_slip_thankyou' => sprintf( __( 'We hope you enjoy your order. Thank you for shopping with %s', 'woocommerce-dropshipping' ), get_bloginfo( 'name' ) ),
				'url_product_feed' => '',
				'version' => $this->version
			);
			update_option( 'wc_dropship_manager', $options );
			update_option( $this->plugin_slug . '-version', $this->version );
		 	add_role( 'dropshipper', 'Dropshipper', array( 'read' => true, 'edit_posts' => true ) );
		}
	}

	public function init_supplier_taxonomy() {
		$args = array(
			'public' => false,
			'hierarchical' => false,
			'label' => 'Drop Ship Suppliers',
			'labels' => array(
							'name'                       => __( 'Suppliers', 'woocommerce-dropshipping' ),
							'singular_name'              => __( 'Dropshipping Supplier', 'woocommerce-dropshipping' ),
							'menu_name'                  => __( 'Suppliers', 'woocommerce-dropshipping' ),
							'search_items'               => __( 'Search Dropshipping Suppliers', 'woocommerce-dropshipping' ),
							'all_items'                  => __( 'All Dropshipping Suppliers', 'woocommerce-dropshipping' ),
							'edit_item'                  => __( 'Edit Dropshipping Supplier', 'woocommerce-dropshipping' ),
							'update_item'                => __( 'Update Dropshipping Supplier', 'woocommerce-dropshipping' ),
							'add_new_item'               => __( 'Add New Dropshipping Supplier', 'woocommerce-dropshipping' ),
							'new_item_name'              => __( 'New Dropshipping Supplier Name', 'woocommerce-dropshipping' ),
							'popular_items'              => __( 'Popular Dropshipping Suppliers', 'woocommerce-dropshipping' ),
							'separate_items_with_commas' => __( 'Separate Dropshipping Suppliers with commas', 'woocommerce-dropshipping' ),
							'add_or_remove_items'        => __( 'Add or remove Dropshipping Suppliers', 'woocommerce-dropshipping' ),
							'choose_from_most_used'      => __( 'Choose from the most used Dropshipping Suppliers', 'woocommerce-dropshipping' ),
							'not_found'                  => __( 'No Dropshipping Suppliers found', 'woocommerce-dropshipping' ),
					),
			'show_ui' => true,
			'rewrite' => false,
			'query_var' => true,
			'show_admin_column' => true,
			'meta_box_cb'=> false,
		);
		register_taxonomy( 'dropship_supplier', 'product', $args );
	}




	// Make these columns sortable
	public function make_dropship_supplier_column_sort() {
		 return array('taxonomy-dropship_supplier' => 'taxonomy-dropship_supplier' );
	}


	public function dropship_supplier_column_orderby($clauses, $wp_query) {
		global $wpdb;
		if(isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'taxonomy-dropship_supplier' )
		{
			$clauses['join'] .= "	LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
						LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
						LEFT OUTER JOIN {$wpdb->terms} USING (term_id)";
			$clauses['where'] .= "AND (taxonomy = 'dropship_supplier' OR taxonomy IS NULL)";
			$clauses['groupby'] = "object_id";
			$clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";
			if(strtoupper($wp_query->get('order')) == 'ASC')
			{
				$clauses['orderby'] .= 'ASC';
			} else{
				$clauses['orderby'] .= 'DESC';
			}
		}
		return $clauses;
	}

	/**
	 * Main WC_Dropshipping Instance
	 *
	 * Ensures only one instance of WC_Dropshipping is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC_Dropshipping()
	 * @return Main WC_Dropshipping instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()
} // End Class
