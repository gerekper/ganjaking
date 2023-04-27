<?php
/**
 * Plugin Name: WooCommerce Dropshipping
 * Plugin URI: http://woocommerce.com/products/woocommerce-dropshipping/
 * Description: Handle dropshipping from your WooCommerce. Create a packing slip, and notify the vendor when an order is paid. Import inventory updates via CSV from your vendors.
 * Version: 4.9.5
 * Author: OPMC Australia Pty Ltd
 * Author URI: https://opmc.com.au/
 * Developer: OPMC
 * Developer URI: https://opmc.com.au/
 * Requires at least: 4.5
 * Tested up to: 6.2
 * WC tested up to: 7.6
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

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

 * Dropshipping Pro Release Notice

 */
/** Check if Dropshipping Pro is active */
function is_dropshipping_pro_active() {
    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	foreach ( $active_plugins as $plugin ) {
		if ( strpos( $plugin, 'pro-add-on-for-woocommerce-dropshipping') !== false ) {
            return true;
        }
    }

    return false;
}

/**
* Function
*/
add_action( 'admin_notices', 'dropshipping_pro_admin_notice' );

/**
 * Function for dropshipping_pro_admin_notice
 */
function dropshipping_pro_admin_notice() {
	global $current_user;
	global $pagenow;
	$user_id = $current_user->ID;
	$notice_id = 'dropshipping_pro_admin_notice';
	if ( ! get_user_meta( $user_id, 'dropshipping_ignore_notice' ) ) {
		$dismiss_url = add_query_arg( 'dropshipping_nag_ignore', '0' );
		if ( 'index.php' == $pagenow && false == is_dropshipping_pro_active() ) {
			echo '<div class="updated notice notice-info" id="' . esc_attr( $notice_id ) . '">
				<p>Pro Add-on for WooCommerce Dropshipping - Now Released!</p>
				<p>Add marketing and customization features to the WooCommerce Dropshipping Plugin with Pro Add-on for WooCommerce Dropshipping.  <a href="https://woocommerce.com/products/pro-add-on-for-woocommerce-dropshipping/" target="_blank">Find out more!</a> <a href="' . esc_url( $dismiss_url ) . '"><button type="button" class="notice-dismiss  hidecbe" style="position: relative; border: none; margin: 0; padding: 0px; background: 0 0;		color: #787c82; cursor: pointer; float: right;"><span class="screen-reader-text"></span></button>

					</a></p>
        	</div>';
		}
	}
}

add_action( 'admin_init', 'dropshipping_nag_ignore' );

/**
 * Function for dropshipping_nag_ignore
 */
function dropshipping_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	if ( isset( $_GET['dropshipping_nag_ignore'] ) && '0' == $_GET['dropshipping_nag_ignore'] ) {
		add_user_meta( $user_id, 'dropshipping_ignore_notice', 'true', true );
	}
}
/**
 * End
 */

/**
 * Dropshipping allow_url_fopen Missing Notice
 */

if (!ini_get('allow_url_fopen') == 1)

{

	add_action( 'admin_notices', 'wcbd_allow_url_fopen' );

}

if ( ! function_exists( 'wcbd_allow_url_fopen' ) ) {

	/**

	* Dropshipping wcbd_allow_url_fopen

	*/
	function wcbd_allow_url_fopen() {

		/* translators: 1: href link to Fileinfo extension php doc */

		echo '<div class="notice"><p>' . sprintf( __( 'WooCommerce Dropshipping requires %s to be installed on your server.', 'woocommerce-dropshipping' ), '<a href="https://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">allow_url_fopen </a>extension' ) . '</p></div>';

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
function w_c_dropshipping() {

	return WC_Dropshipping::instance();

} // End w_c_dropshipping()

w_c_dropshipping();

/**

 * Start WC_Dropshipping Class

 */
final class WC_Dropshipping {

	/**

	 * WC_Dropshipping The single instance of WC_Dropshipping.

	 * @var 	object

	 * @access  private

	 * @since 	1.0.0

	 */

	private static $_instance = null;

	/**
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 * @param Data_table $data_table is a veriable used in dropshipping
	 *
	 * @return Data_table
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 *
	 * @var 	object
	 */
	public $data_table = '';

	/**
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 * @param Base_path $base_path is a veriable used in dropshipping
	 *
	 * @return Base_path
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 *
	 * @var 	object
	 */
	public $base_path = '';

	/**
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 * @param Admin $admin is a veriable used in dropshipping
	 *
	 * @return Admin
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 *
	 * @var 	object
	 */
	public $admin = null;

	/**
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 * @param Plugin_slug $plugin_slug is a veriable used in dropshipping
	 *
	 * @return Plugin_slug
	 * Returns the main instance of WC_Dropshipping to prevent the need to use globals.
	 *
	 * @var 	object
	 */
	public $plugin_slug = '';

	/**

	* Returns the main instance of __construct to prevent the need to use globals.

	*

	* @since  1.0.0

	* @return object Construct

	*/
	public function __construct() {

		$this->version = '3';

		$this->plugin_name = __( 'WooCommerce Dropshipping', 'woocommerce-dropshipping' );

		$this->base_path = plugin_dir_path( __FILE__ );

		require_once( 'woocommerce-dropshipping-functions.php' );

		// Include AliExpress file.

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

		add_action( 'admin_init', array($this, 'change_cost_of_goods_key'));

		add_action( 'admin_init', array($this, 'show_admin_notice_options'));

		add_filter( 'plugin_action_links', array( $this, 'wc_dropshipping_plugin_links' ), 10, 4 );

		add_filter( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action("admin_menu", array( $this,"graph_report_options_submenu"));

		add_filter("aliexpress_product_discount_pro",array($this,'aliexpress_product_discount'),10,3);
		add_filter("aliexpress_product_price_calculator_pro",array($this,'aliexpress_product_price_calculator'),10,3);
		add_filter("cdiscount_product_add_pro",array($this,'cdiscount_product_add'),10,3);
		add_filter("banggood_product_add_pro",array($this,'banggood_product_add'),10,3);
		add_filter("update_aliexpress_tracking_number_pro",array($this,'update_aliexpress_tracking_number'),10,3);
		add_action('admin_enqueue_scripts', array( $this, 'switch_onoff') );

	}

	public function switch_onoff( $hookget) {

		 if ( 'woocommerce_page_wc-settings' != $hookget ) {
			return;
		 }

		if (!isset($_REQUEST['tab']) || 'wc_dropship_settings' !== $_REQUEST['tab']) {
			return;
		}

		wp_enqueue_style('on-off-switch', plugins_url('assets/css/on-off-switch.css', __FILE__), array(), 'v1.0');
		wp_enqueue_style('opmc_af_admin_css', plugins_url( 'assets/css/app.css', __FILE__), array(), 'v1.0'  );
		wp_enqueue_script('on-off-switch', plugins_url('assets/js/on-off-switch.js', __FILE__), array(), 'v1.0');
		//wp_enqueue_script('app', plugins_url('assets/js/app.js', __FILE__), array(), 'v1.0');
		wp_enqueue_script('on-off-switch-onload', plugins_url('assets/js/on-off-switch-onload.js', __FILE__), array(), 'v1.0');
	}

	/**

	* Returns the main instance of Graph_report_options_submenu to prevent the need to use globals.

	*

	* @since  1.0.0

	* @return object Graph_report_options_submenu

	*/
	public function graph_report_options_submenu() {
		 $this->page_id = add_submenu_page(
		 'woocommerce',
		 __('Graph Reports', 'woocommerce-dropshipping'),
		 __('Graph Reports', 'woocommerce-dropshipping'),
		 'manage_woocommerce',
		 'Graph Reports',
		 array($this, 'graph_report_page')
		 );
		 }

		  /**

		* Returns the main instance of Init to prevent the need to use globals.

		*

		* @since  1.0.0

		* @return object Graph_report_page

		*/
		public function graph_report_page() {

			require('templates/graph-reports.php');
	   }

	    /**

		* Returns the main instance of Init to prevent the need to use globals.

		*

		* @since  1.0.0

		* @return object Init

		*/
		 public function init () {

			load_plugin_textdomain( 'woocommerce-dropshipping', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			do_action( 'wc_dropship_manager_init' );

			if ( is_admin() ) {

				require_once('inc/class-wc-dropshipping-admin.php');

				require_once('inc/class-wc-dropshipping-settings.php');

				$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

				foreach ( $active_plugins as $plugin ) {

					if ( strpos( $plugin, 'woocommerce.php' ) ) {

						include_once(WC()->plugin_path().'/includes/admin/reports/class-wc-admin-report.php');
						require_once('inc/class-wc-report-sales-by-supplier.php');
					}
				}

				$this->admin = new WC_Dropshipping_Admin();

			}
			if( !function_exists( 'is_plugin_inactive' ) ) :
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			endif;
			if( !class_exists( 'WooCommerce' ) ) :
				add_action( 'admin_init', 'yourplugin_deactivate' );
				add_action( 'admin_notices', 'yourplugin_admin_notice' );

				 /**

				* Returns the main instance of Init to prevent the need to use globals.

				*

				* @since  1.0.0

				* @return object Yourplugin_deactivate

				*/
				function yourplugin_deactivate() {
					deactivate_plugins( plugin_basename( __FILE__ ) );
					echo "<script>window.location.reload();</script>";
				}

				/**

				* Returns the main instance of Init to prevent the need to use globals.

				*

				* @since  1.0.0

				* @return object Yourplugin

				*/
				function yourplugin_admin_notice() {
					echo '<div class="error"><p><strong>WooCommerce</strong> must be installed and activated to use this WooCommerce Dropshipping Plugin.</p></div>';
					if( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
				}
			endif;

			require_once('inc/class-wc-dropshipping-orders.php');

			$this->orders = new WC_Dropshipping_Orders();

			require_once('inc/class-wc-dropshipping-checkout.php');

			$this->checkout = new WC_Dropshipping_Checkout();

			require_once( 'inc/class-wc-dropshipping-dashboard.php' );

			$this->dashboard = new WC_Dropshipping_Dashboard();

			// Limit Capabilities of Dropshipper.

			add_action( 'wp_before_admin_bar_render', array($this, 'limit_dropshipper_capabilities'), 99 );

			add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets') , 20);

			// Make temporary folder for uploading attachments after that files get auto remove after base64 encode.

			$upload = wp_upload_dir();

			$upload_dir = $upload['basedir'];

			$upload_dir = $upload_dir . '/droptmp';

			if (! is_dir($upload_dir)) {

				mkdir( $upload_dir, 0777 );

				$upload_convert = wp_upload_dir();

				$upload_convert = $upload_convert['basedir'];

				$upload_dir_convert = $upload_convert . '/droptmp/convert';

				if (! is_dir($upload_dir_convert)) {

					mkdir( $upload_dir_convert, 0777 );

				}

			}

		}

		/**

		* Start actaliexpress_product_discountivate

		*/
        public function aliexpress_product_discount(){
            echo "Aliexpress Product Discount<br/>";
         }

		 /**

		* Start aliexpress_product_price_calculator

		*/
        public function aliexpress_product_price_calculator(){
            echo "Aliexpress Product Price Calculator<br/>";
          }

		  /**

		* Start cdiscount_product_add

		*/
        public function cdiscount_product_add(){
            echo "Cdiscount Product Add<br/>";
          }

		/**

		* Start banggood_product_add

		*/
        public function banggood_product_add(){
            echo "Banggood Product Add<br/>";
          }

		/**

		* Start update_aliexpress_tracking_number

		*/
        public function update_aliexpress_tracking_number(){
            echo "Update Aliexpress Tracking Number<br/>";
          }

	/**

	* Start activate

	*/
	public function activate() {
		/**
		 * Function install_wc_customer_service_plugin.
		 *
		 * @since 2022
		 */
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			echo '<h4> WooCommerce plugin is missing. This plugin requires WooCommerce.', 'ap </h4>';
			/*Adding @ before will prevent XDebug output*/
			@trigger_error( 'Please install WooCommerce before activating.', E_USER_ERROR );
		}

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

				'show_admin_notice_option' => '1',

				'version' => $this->version

			);

			update_option( 'wc_dropship_manager', $options );

			update_option( $this->plugin_slug . '-version', $this->version );

		 	add_role( 'dropshipper', 'Dropshipper', array( 'read' => true, 'edit_posts' => true ) );

		}

	}

	/**
	 * Plugin actions
	 *
	 * @param actions $actions
	 *
	 * Plugin actions
	 *
	 * Plugin plugin_file
	 *
	 * @param plugin_file $plugin_file
	 *
	 * Plugin plugin_file
	 *
	 * Plugin plugin_data
	 *
	 * @param plugin_data $plugin_data
	 *
	 * Plugin plugin_data
	 *
	 * Plugin context
	 *
	 * @param context $context
	 *
	 * Plugin context

	 */
	 function wc_dropshipping_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {

		 $new_actions = array();

		 if ( basename( plugin_dir_path( __FILE__ ) ) . '/woocommerce-dropshipping.php' === $plugin_file ) {

			 	$new_actions[ 'wc_dropship_settings' ] = '<a href="'.get_site_url().'/wp-admin/admin.php?page=wc-settings&tab=wc_dropship_settings">' . __( 'Settings', 'woocommerce-dropshipping' ) . '</a>'; $new_actions[ 'wc_dropship_docs' ] = '<a target="_blank" href="https://docs.woocommerce.com/document/woocommerce-dropshipping/">' . __( 'Docs', 'woocommerce-dropshipping' ) . '</a>';

			}

		 	return array_merge( $new_actions, $actions );

		 }


	/**
	 * Get the show_admin_notice_options
	 *
	 *	@param options $options
	 *
	 */
	function show_admin_notice_options(){

		$options = get_option( 'wc_dropship_manager' );

		if (isset($options)){

			if (!empty($options['show_admin_notice_option'])){

				if ( '1' == $options['show_admin_notice_option']){

					add_action( 'admin_notices', array($this, 'plugin_activation_notice' ));

				}

			}

		}

	}

	/**
	 *
	 * Get the plugin_activation_notice
	 *
	 */
	function plugin_activation_notice(){

		$class = 'notice notice-info';

		$message = __( 'Woocommerce Dropshipping activated successfully! In order for the plugin to work correctly. Visit', 'woocommerce-dropshipping' );

		$message2 = __( 'and hit the "Save Changes" button', 'woocommerce-dropshipping' );

		$url = get_site_url().'/wp-admin/admin.php?page=wc-settings&tab=wc_dropship_settings';

		printf( '<div class="%1$s"><p>%2$s <a href="%3$s">Dropshipping Settings</a> %4$s.</p></div>', esc_attr( $class ), esc_html( $message ), esc_url( $url ), esc_html( $message2 ) );

	}

	/**
	 *
	 * Get the current logged in user
	 *
	 */
	public function get_current_user(){

		if( is_user_logged_in() ) {

 			$user = wp_get_current_user();

 			$role = $user->roles;

			return $role[0];

		}else{

			return false;

		}

	}

	/**
	 *
	 * Limit dropshipper capabilities
	 *
	 */
	public function limit_dropshipper_capabilities(){

		$role = $this->get_current_user();

		if ( false !== $role){

			if ( 'dropshipper' === $role ) {

				global $wp_admin_bar;

                $all_nodes = $wp_admin_bar->get_nodes();

                $except = array('user-actions','user-info','edit-profile','logout','site-name','view-site','view-store','top-secondary','my-account');

                foreach($all_nodes as $key => $val) {

                    if(!in_array($key,$except)) {

                        $current_node = $all_nodes[$key];

                        $wp_admin_bar->remove_node($key);

                    }
                }

				remove_all_actions( 'admin_notices' );

			}

		}

	}

	/**

	 * Plugin init_supplier_taxonomy

	 */
	public function remove_dashboard_widgets() {

	    $role = $this->get_current_user();

		if ( false !== $role ){

			if ( 'dropshipper' === $role ) {

	            global $wp_meta_boxes;
                unset($wp_meta_boxes['dashboard']);
                echo '<style>#dashboard-widgets .postbox-container .empty-container{display: none;}</style>';

			}
		}

	}

	/**

	 * Plugin init_supplier_taxonomy

	 */
	public function init_supplier_taxonomy() {

		$args = array(

			'public' => false,

			'hierarchical' => false,

			'label' => 'Suppliers (Dropshipping)',

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

	/**

	 * Plugin page links

	 */
	public function make_dropship_supplier_column_sort() {
		echo '<style>.fixed .column-date {width: auto !important; }
		table.wp-list-table .column-product_cat, table.wp-list-table .column-product_tag {width: 8%!important;	}</style>';

		 return array('taxonomy-dropship_supplier' => 'taxonomy-dropship_supplier' );

	}


	/**
	 * Plugin clauses variable for data
	 * @param clauses $clauses
	 *
	 *
	 * Plugin $clauses variable for data
	 *
	 * Plugin clauses variable for data
	 * @param wp_query $wp_query
	 *
	 *
	 * Plugin $wp_query variable for data
	 *

	 */
	public function dropship_supplier_column_orderby($clauses, $wp_query) {

		global $wpdb;



		if(isset( $wp_query->query['orderby'] ) && 'taxonomy-dropship_supplier' == $wp_query->query['orderby'] )

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

	 * Plugin page links

	 */
	public function uninstall(){

		wp_unschedule_hook('ali_run_cron_prod_check');

		update_option('cog_meta_key', 'incomplete');

	}


	/**

	 * Plugin page links

	 */
	public function change_cost_of_goods_key(){

		if ( get_option('cog_meta_key') != 'completed' ){

			global $wpdb;

			$sql = "UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s";

			$query = $wpdb->prepare( $sql, '_cost_of_goods',  '_custom_product_text_field');

			$res = $wpdb->get_results($query);

			update_option('cog_meta_key', 'completed');

		}

	}

	/**

	 * Plugin page links

	 */
	public function admin_menu() {

		add_menu_page(

			__( 'Dropshipping', 'woocommerce-dropshipping' ),

			__( 'Dropshipping', 'woocommerce-dropshipping' ),

			'administrator',

			'woocommerce_dropshipping', '', 'dashicons-exerpt-view', 50

		);

		add_submenu_page(

			'woocommerce_dropshipping',

			__( 'Dashboard', 'woocommerce-dropshipping' ),

			__( 'Dashboard', 'woocommerce-dropshipping' ),

			'administrator',

			'dashboard',

			array( $this, 'add_dashboard_sub_menu' )

		);

		remove_submenu_page('woocommerce_dropshipping','woocommerce_dropshipping');

	}

	/**

	 * Plugin page links

	 */
	public function add_dashboard_sub_menu(){

		require_once 'templates/wc-dropshipping-dashboard.php';

	}

	/**

	 * Main WC_Dropshipping Instance

	 *

	 * Ensures only one instance of WC_Dropshipping is loaded or can be loaded.

	 *

	 * @since 1.0.0

	 * @static

	 * @see w_c_dropshipping()

	 * @return Main WC_Dropshipping instance

	 */
	public static function instance () {

		if ( is_null( self::$_instance ) )

			self::$_instance = new self();

		return self::$_instance;

	} // End instance()

} // End Class
