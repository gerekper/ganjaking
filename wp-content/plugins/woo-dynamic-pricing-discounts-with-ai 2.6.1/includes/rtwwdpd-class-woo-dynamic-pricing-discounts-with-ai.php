<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Loader    $rtwwdpd_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $rtwwdpd_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwwdpd_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $rtwwdpd_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $rtwwdpd_version    The current version of the plugin.
	 */
	protected $rtwwdpd_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() 
	{
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
		
		if ( defined( 'RTWWDPD_WOO_DYNAMIC_PRICING_DISCOUNTS_WITH_AI_VERSION' ) ) {
			$this->rtwwdpd_version = RTWWDPD_WOO_DYNAMIC_PRICING_DISCOUNTS_WITH_AI_VERSION;
		} else {
			$this->rtwwdpd_version = '1.0.0';
		}
		$this->rtwwdpd_plugin_name = 'woo-dynamic-pricing-discounts-with-ai';

		$this->rtwwdpd_load_dependencies();
		$this->rtwwdpd_set_locale();
		$this->rtwwdpd_define_admin_hooks();

		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
		{
			$this->rtwwdpd_define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Loader. Orchestrates the hooks of the plugin.
	 * - Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_i18n. Defines internationalization functionality.
	 * - Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Admin. Defines all hooks for the admin area.
	 * - Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwdpd_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwwdpd-class-woo-dynamic-pricing-discounts-with-ai-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rtwwdpd-class-woo-dynamic-pricing-discounts-with-ai-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/rtwwdpd-class-woo-dynamic-pricing-discounts-with-ai-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/rtwwdpd-class-woo-dynamic-pricing-discounts-with-ai-public.php';

		$this->rtwwdpd_loader = new Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwdpd_set_locale() {

		$rtwwdpd_plugin_i18n = new Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_i18n();

		$this->rtwwdpd_loader->rtwwdpd_add_action( 'plugins_loaded', $rtwwdpd_plugin_i18n, 'rtwwdpd_load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwdpd_define_admin_hooks() {
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );

		$rtwwdpd_plugin_admin = new Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Admin( $this->rtwwdpd_get_plugin_name(), $this->rtwwdpd_get_version() );

		$this->rtwwdpd_loader->rtwwdpd_add_action( 'admin_enqueue_scripts', $rtwwdpd_plugin_admin, 'rtwwdpd_enqueue_styles' );
		$this->rtwwdpd_loader->rtwwdpd_add_action( 'admin_enqueue_scripts', $rtwwdpd_plugin_admin, 'rtwwdpd_enqueue_scripts' );

		$this->rtwwdpd_loader->rtwwdpd_add_action( 'admin_menu', $rtwwdpd_plugin_admin, 'rtwwdpd_add_submenu' );
			
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
		{	
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_variation_options_pricing', $rtwwdpd_plugin_admin, 'rtwwdpd_variation',10,3);
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_save_product_variation', $rtwwdpd_plugin_admin, 'rtwwdpd_variation_save', 10, 2);

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_login', $rtwwdpd_plugin_admin, 'rtwwdpd_update_customer_visit', 10, 2 );

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_plus_member', $rtwwdpd_plugin_admin, 'rtwwdpd_plus_member_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_plus_text', $rtwwdpd_plugin_admin, 'rtwwdpd_plus_text_callback');

			
			//////// add extra column in user list table wordpress /////
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'manage_users_columns', $rtwwdpd_plugin_admin, 'rtwwdpd_new_colmn_user', 10, 1 );

			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'manage_users_custom_column', $rtwwdpd_plugin_admin, 'rtwwdpd_user_data', 10, 3 );

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtw_enable_plus', $rtwwdpd_plugin_admin, 'rtwwdpd_plus_enable_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtw_enable_nth_order', $rtwwdpd_plugin_admin, 'rtwwdpd_enable_nth_order_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_apply_shipping_discount', $rtwwdpd_plugin_admin, 'rtwwdpd_apply_shipping_discount_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_show_ship_on_chkout', $rtwwdpd_plugin_admin, 'rtwwdpd_show_ship_on_chkout_callback');
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_specific_enable', $rtwwdpd_plugin_admin, 'rtwwdpd_enable_specific_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_next_buy', $rtwwdpd_plugin_admin, 'rtwwdpd_enable_next_buy_bonus');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtw_cat_tbl', $rtwwdpd_plugin_admin, 'rtwwdpd_category_tbl_callback');
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_selected_attribute', $rtwwdpd_plugin_admin, 'rtwwdpd_selected_attribute_callback');

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_checkout_order_processed', $rtwwdpd_plugin_admin, 'rtwwdpd_next_buy_bonus');
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_enable_least_free', $rtwwdpd_plugin_admin, 'rtwwdpd_enable_least_free_callback');
			
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_order_get_total_discount', $rtwwdpd_plugin_admin, 'rtwwdpd_order_get_total_discount', 10, 2);
			
			////////////shipping customization 
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_shipping_rule_on', $rtwwdpd_plugin_admin, 'rtwwdpd_shipping_rule_on_callback');
			//delete_purchase_code
			if(isset($_GET['rtwwdpd_action']) && $_GET['rtwwdpd_action'] == 'delete_purchase_code')
			{
				$this->rtwwdpd_loader->rtwwdpd_add_action( 'admin_init', $rtwwdpd_plugin_admin, 'rtwwdpd_delete_purchase_code' );
			}
		}
		$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_verify_purchase_code', $rtwwdpd_plugin_admin, 'rtwwdpd_verify_purchase_code_callback');
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function rtwwdpd_define_public_hooks() {
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );

		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty( $rtwwdpd_verification_done['purchase_code'] ) )
		{
			$rtwwdpd_plugin_public = new Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public( $this->rtwwdpd_get_plugin_name(), $this->rtwwdpd_get_version() );
			
			// $this->rtwwdpd_loader->rtwwdpd_add_action( 'rest_api_init', $rtwwdpd_plugin_public, 'testing_function',10,1);
			// $this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_update_cart_action_cart_updated', $rtwwdpd_plugin_public, 'clear_notices_on_cart_update', 10, 1 );

			// $this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_calculated_shipping', $rtwwdpd_plugin_public, 'checking', 10, 1 );

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_enqueue_scripts', $rtwwdpd_plugin_public, 'rtwwdpd_enqueue_styles' );
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_enqueue_scripts', $rtwwdpd_plugin_public, 'rtwwdpd_enqueue_scripts' );
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_cart_calculate_fees', $rtwwdpd_plugin_public, 'rtwwdpd_discnt_on_pay_select' );
			
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_package_rates', $rtwwdpd_plugin_public, 'rtwwdpd_shipping_dscnt', 10, 2 );

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_cart_loaded_from_session', $rtwwdpd_plugin_public, 'rtwwdpd_cart_loaded_from_session', 98, 1 );

			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_calculate_totals', $rtwwdpd_plugin_public, 'rtwwdpd_before_calculate_totals', 98, 1 );

			/////////////////// display offers on shop page //////////////////
			$rtw_priority_option = get_option( 'rtwwdpd_setting_priority' );
		
			if(isset($rtw_priority_option['rtw_offer_show'] ) && $rtw_priority_option['rtw_offer_show'] == 'rtw_price_yes')
			{
				if($rtw_priority_option['rtwwdpd_offer_tbl_pos'] == 'rtw_bfore_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_shop_loop_item', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_pos'] == 'rtw_aftr_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_shop_loop_item', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_pos'] == 'rtw_bfore_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_shop_loop_item_title', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_pos'] == 'rtw_in_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_shop_loop_item_title', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_pos'] == 'rtw_aftr_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_shop_loop_item', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}

				$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_cart_table', $rtwwdpd_plugin_public, 'rtwwdpd_on_cart_page', 5);
			}
			//////////////// display offers on product page ////////////////
			if(isset($rtw_priority_option['rtw_offer_on_product']) && $rtw_priority_option['rtw_offer_on_product'] == 'rtw_price_yes')
			{
				if($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_bfore_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_single_product', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_aftr_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_single_product', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_bfore_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_in_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_aftr_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_bfre_add_cart_btn')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_add_to_cart_button', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_aftr_add_cart_btn')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_add_to_cart_button', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_bfre_add_cart_frm')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action('woocommerce_before_add_to_cart_form', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_aftr_add_cart_frm')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_add_to_cart_form', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_pro_meta_strt')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_product_meta_start', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
				elseif($rtw_priority_option['rtwwdpd_offer_tbl_prodct'] == 'rtw_pro_meta_end')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_product_meta_end', $rtwwdpd_plugin_public, 'rtwwdpd_on_product_page', 5);
				}
			}
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_cart_calculate_fees', $rtwwdpd_plugin_public, 'rtwwdpd_sale_custom_price' );
			//////////////////
			// $this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_cart_item_quantity_update', $rtwwdpd_plugin_public, 'rtwwdpd_after_cart_item_quantity_update', 1000000, 3 );
			
			/////////////////////////
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_cart_item_price', $rtwwdpd_plugin_public, 'rtwwdpd_on_display_cart_item_price_html', 10, 3 );
			
			// $this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_payment_complete_order_status', $rtwwdpd_plugin_public, 'woocommerce_payment_complete_order_status', 1000, 2 );
			
			add_shortcode('ShowOfferBanner',array($rtwwdpd_plugin_public , 'rtwwdpd_show_offer_banner_page' ));
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_rtwwdpd_variation_id', $rtwwdpd_plugin_public, 'rtwwdpd_variation_id_callback');
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'wp_ajax_nopriv_rtwwdpd_variation_id', $rtwwdpd_plugin_public, 'rtwwdpd_variation_id_callback');
			
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'pre_get_document_title', $rtwwdpd_plugin_public, 'rtwwdpd_change_titles', 10 ,1 );
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_cart_totals_after_order_total', $rtwwdpd_plugin_public, 'rtwwdpd_cart_totals_before_order_total', 99);
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_review_order_after_order_total', $rtwwdpd_plugin_public, 'rtwwdpd_cart_totals_before_order_total', 99);
			
			$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_checkout_create_order', $rtwwdpd_plugin_public, 'rtwwdpd_woocommerce_checkout_create_order', 10, 2);
			
			$message_settings = get_option('rtwwdpd_message_settings', array());
			$rtwwdpd_timer = get_option('rtwwdpd_setting_priority'); 
			
			// if( !empty( $rtwwdpd_timer ) && isset( $rtwwdpd_timer['rtwwdpd_enable_message_timer'] ) && $rtwwdpd_timer['rtwwdpd_enable_message_timer'] == 1 )
			// {
			  $this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_shop_loop', $rtwwdpd_plugin_public, 'rtw_sale_timer_htm');
			// }
			if( !empty( $message_settings ) && isset( $message_settings['rtwwdpd_enable_message'] ) && $message_settings['rtwwdpd_enable_message'] == 1 )
			{
				if( $message_settings['rtwwdpd_message_position'] == 1 )
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_shop_loop', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 10);

				}
				elseif( $message_settings['rtwwdpd_message_position'] == 2 )
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_shop_loop', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 10);

				}
				elseif( $message_settings['rtwwdpd_message_position'] == 3 )
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_archive_description', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 10);

				}
				elseif( $message_settings['rtwwdpd_message_position'] == 4 )
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_main_content', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 10);

				}

				if($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_bfore_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_single_product', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_aftr_pro')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_single_product', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_bfore_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_in_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_aftr_pro_sum')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_single_product_summary', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_bfre_add_cart_btn')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_before_add_to_cart_button', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_aftr_add_cart_btn')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_add_to_cart_button', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_bfre_add_cart_frm')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action('woocommerce_before_add_to_cart_form', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_aftr_add_cart_frm')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_after_add_to_cart_form', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_pro_meta_strt')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_product_meta_start', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
				elseif($message_settings['rtwwdpd_message_pos_propage'] == 'rtw_pro_meta_end')
				{
					$this->rtwwdpd_loader->rtwwdpd_add_action( 'woocommerce_product_meta_end', $rtwwdpd_plugin_public, 'rtwwdpd_offers_message', 5);
				}
			}

			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_get_price_html', $rtwwdpd_plugin_public, 'rtwwdpd_change_product_html', 10, 3 );

			/// updated code 2.50.0
			$this->rtwwdpd_loader->rtwwdpd_add_filter( 'woocommerce_bundled_items', $rtwwdpd_plugin_public, 'rtwwdpd_bundled_product_detials', '', 2 );
			
		}
		

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_run() {
		$this->rtwwdpd_loader->rtwwdpd_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function rtwwdpd_get_plugin_name() {
		return $this->rtwwdpd_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Dynamic_Pricing_Discounts_With_Ai_Loader    Orchestrates the hooks of the plugin.
	 */
	public function rtwwdpd_get_loader() {
		return $this->rtwwdpd_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function rtwwdpd_get_version() {
		return $this->rtwwdpd_version;
	}

}
