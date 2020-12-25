<?php
/**
 * class-woocommerce-product-search-admin.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings.
 */
class WooCommerce_Product_Search_Admin {

	const NONCE                 = 'woocommerce-product-search-admin-nonce';
	const SETTINGS_POSITION     = 999;
	const SETTINGS_ID           = 'product-search';
	const SECTION_GENERAL       = 'general';
	const SECTION_WEIGHTS       = 'weights';
	const SECTION_THUMBNAILS    = 'thumbnails';
	const SECTION_CSS           = 'css';
	const SECTION_INDEX         = 'index';
	const SECTION_ASSISTANT     = 'assistant';
	const SECTION_HELP          = 'help';
	const SECTION_WELCOME       = 'welcome';
	const HELP_POSITION         = 999;
	const INDEXER_CONTROL_CAPABILITY = 'manage_woocommerce';
	const ASSISTANT_CONTROL_CAPABILITY = 'edit_theme_options';
	const WIDGET_NUMBER_START   = 2;

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head-widgets.php', array( __CLASS__, 'admin_head' ) );
		add_action( 'customize_controls_print_styles', array( __CLASS__, 'admin_head' ) );
		add_action( 'after_plugin_row_' . plugin_basename( WOO_PS_FILE ), array( __CLASS__, 'after_plugin_row' ), 10, 3 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 4 );
	}

	/**
	 * Registers the updater script and styles.
	 */
	public static function admin_enqueue_scripts() {
		wp_register_script( 'wps-indexer', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/indexer.js' : '/js/indexer.min.js' ), array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_style( 'wps-admin', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/admin.css' : '/css/admin.min.css' ), array(), WOO_PS_PLUGIN_VERSION );
	}

	/**
	 * Renders widget icon CSS for admin.
	 */
	public static function admin_head() {
		$output = '<style type="text/css">';
		$output .= '*[id*="woocommerce_product_search_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_attribute_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_category_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_price_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_rating_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_sale_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_tag_widget"] .widget-title,';
		$output .= '*[id*="woocommerce_product_search_filter_reset_widget"] .widget-title {';
		$output .= sprintf( 'background-image: url( %s );', esc_url( WOO_PS_PLUGIN_URL . '/images/woocommerce-product-search.png' ) );
		$output .= 'background-size: 1em;';
		$output .= 'background-repeat: no-repeat;';
		$output .= 'background-position: 0.62em center;';
		$output .= 'padding-left: 1em;';
		$output .= '}';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_attribute_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_category_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_price_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_rating_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_sale_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_tag_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_reset_widget"] .widget-title {';
		$output .= 'background-position: right 0.62em center;';
		$output .= 'padding-left: inherit;';
		$output .= 'padding-right: 1em;';
		$output .= '}';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_attribute_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_category_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_price_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_rating_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_sale_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_tag_widget"] .widget-title,';
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_reset_widget"] .widget-title {';
		$output .= 'background-position: 0 top;';
		$output .= 'padding-left: 1.3em;';
		$output .= '}';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_attribute_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_category_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_price_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_rating_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_sale_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_tag_widget"] .widget-title,';
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_reset_widget"] .widget-title {';
		$output .= 'background-position: right top;';
		$output .= 'padding-right: 1.3em;';
		$output .= '}';
		$output .= '</style>';
		echo $output;
	}

	/**
	 * Admin setup.
	 */
	public static function wp_init() {

		global $wpdb;

		add_filter( 'plugin_action_links_' . plugin_basename( WOO_PS_FILE ), array( __CLASS__, 'admin_settings_link' ) );
		add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'woocommerce_settings_tabs_array' ), self::SETTINGS_POSITION );
		add_action( 'woocommerce_settings_' . self::SETTINGS_ID, array( __CLASS__, 'woocommerce_product_search' ) );
		add_action( 'woocommerce_settings_save_' . self::SETTINGS_ID, array( __CLASS__, 'save' ) );
		add_action( 'current_screen', array( __CLASS__, 'current_screen' ), self::HELP_POSITION );

		if (
			isset( $_REQUEST['action'] ) &&
			( $_REQUEST['action'] == 'wps_indexer' ) &&
			isset( $_REQUEST['cmd'] ) &&
			isset( $_REQUEST['nonce'] ) &&
			wp_verify_nonce( $_REQUEST['nonce'], 'wps-index-js' )
		) {
			@set_time_limit( 0 );
			@ignore_user_abort( true );

			$errors = array();
			$notices = array();

			$start                   = function_exists( 'microtime' ) ? microtime( true ) : time();
			$status                  = WooCommerce_Product_Search_Worker::get_status();
			$processable             = 0;
			$total                   = 0;
			$pct                     = 0;
			$next_scheduled_datetime = '&mdash;';
			if ( $next_scheduled = WooCommerce_Product_Search_Worker::get_next_scheduled() ) {
				$next_scheduled_datetime = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_scheduled ) );
			}

			switch( $_REQUEST['cmd'] ) {
				case 'start' :
					if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
						WooCommerce_Product_Search_Worker::start();
						$notices[] = __( 'The indexer is running.', 'woocommerce-product-search' );
					}
					break;

				case 'stop' :
					if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
						WooCommerce_Product_Search_Worker::stop();
						$notices[] = __( 'The indexer is stopped.', 'woocommerce-product-search' );
					}
					break;

				case 'rebuild' :
					if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
						WooCommerce_Product_Search_Controller::rebuild();
						$notices[] = __( 'The index is building.', 'woocommerce-product-search' );
					}
					break;

				case 'run_once' :
					if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
						WooCommerce_Product_Search_Worker::work();
						$notices[] = __( 'The indexer is running once.', 'woocommerce-product-search' );
					}
					break;

				case 'status' :
					$indexer = new WooCommerce_Product_Search_Indexer();
					$processable = $indexer->get_processable_count();
					$total       = $indexer->get_total_count();
					if ( $total > 0 ) {
						$pct = 100 - $processable / $total * 100;
					} else {
						$pct = 100;
					}
					break;
			}

			$result = array(
				'time'    => ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $start,
				'notices' => $notices,
				'errors'  => $errors,
				'status'      => $status,
				'processable' => $processable,
				'total'       => $total,
				'pct'         => $pct,
				'next_scheduled_datetime' => $next_scheduled_datetime
			);
			echo json_encode( $result );
			exit;
		}

	}

	/**
	 * Adds the help section.
	 */
	public static function current_screen() {
		global $current_section;
		$screen = get_current_screen();
		if ( $screen && function_exists( 'wc_get_screen_ids' ) && in_array( $screen->id, wc_get_screen_ids() ) ) {
			$id = 'woocommerce_product_search_tab';
			$title = __( 'Search', 'woocommerce-product-search' );
			$content = '';
			$content .= '<div id="product-search-help-tab">';

			$content .= '<h3 class="section-heading">';
			$content .= esc_html__( 'Search', 'woocommerce-product-search' );
			$content .= '</h3>';

			$content .= '<h4 style="border-bottom:1px solid #9d9d9d">';
			$content .= esc_html__( 'Documentation', 'woocommerce-product-search' );
			$content .= '</h4>';

			$content .= '<p>';
			$content .= wp_kses(
				sprintf(
					__( 'Please refer to the <a href="%s">WooCommerce Product Search</a> documentation pages for detailed information.', 'woocommerce-product-search' ),
					esc_url( 'https://docs.woocommerce.com/document/woocommerce-product-search/' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= '</p>';

			$content .= '<h4 style="border-bottom:1px solid #9d9d9d">';
			$content .= esc_html__( 'Support', 'woocommerce-product-search' );
			$content .= '</h4>';

			$content .= '<p>';
			$content .= wp_kses(
				sprintf(
					__( 'For further assistance with <a href="%1$s">WooCommerce Product Search</a>, please use the <a href="%2$s">helpdesk</a>.', 'woocommerce-product-search' ),
					esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' ),
					esc_url( 'https://woocommerce.com/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= esc_html__( 'We also welcome your suggestions through this channel.', 'woocommerce-product-search' );
			$content .= '</p>';
			$content .= '<p>';
			$content .= wp_kses(
				sprintf(
					__( 'Please take a moment to <a href="%s">rate this extension</a> &mdash; this helps us to provide you with an excellent product and commit to continuous improvement.', 'woocommerce-product-search' ),
					esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= esc_html__( 'If you would rate any aspect below 5 stars, please let us know before you submit your rating so we can improve it.', 'woocommerce-product-search' );
			$content .= '</p>';

			$content .= '<h4 style="border-bottom:1px solid #9d9d9d">';
			$content .= esc_html__( 'Security', 'woocommerce-product-search' );
			$content .= '</h4>';

			$content .= '<p>';
			$content .= esc_html__( 'Do not compromise on security!', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( '<a href="%1$s">WooCommerce Product Search</a> is made available to you exclusively through <a href="%2$s">WooCommerce</a>.', 'woocommerce-product-search' ),
					esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' ),
					esc_url( 'https://woocommerce.com/?utm_source=helptab&utm_medium=product&utm_content=about&utm_campaign=woocommerceplugin' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'Please always make sure that you obtain or renew this official extension through the only trusted source at <a href="%s">WooCommerce Product Search</a>.', 'woocommerce-product-search' ),
					esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= '</p>';

			$content .= '<h4 style="border-bottom:1px solid #9d9d9d">';
			$content .= esc_html__( 'Setup', 'woocommerce-product-search' );
			$content .= '</h4>';

			$content .= '<p>';
			$content .= esc_html__( 'For your convenience and to minimize manual setup, the search engine will start to process data automatically in the background once activated.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= esc_html__( 'It will start to index your products to optimize search results for your visitors.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= esc_html__( 'Depending on the amount of products in your store and the processing capabilities of your server, this process may take a few minutes or hours to complete.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= esc_html__( 'It is designed to provide search results immediately, even while the indexing process continues in the background.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= esc_html__( 'New and updated content is normally indexed within seconds.', 'woocommerce-product-search' );
			$content .= '</p>';

			$content .= '<p>';
			$content .= esc_html__( 'The advanced Product Search Field automatically replaces the standard field when possible.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'You can adjust its settings in the <a href="%s">General</a> section.', 'woocommerce-product-search' ),
					esc_url( self::get_admin_section_url( self::SECTION_GENERAL ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= '</p>';

			$content .= '<p>';
			$content .= wp_kses(
				sprintf(
					__( 'Live search statistics are available in the <a href="%s">Search</a> section of the reports.', 'woocommerce-product-search' ),
					esc_url( admin_url( 'admin.php?page=wc-reports&tab=search' ) )
				),
				array( 'a' => array( 'href' => array(), 'class' => array() ) )
			);
			$content .= '</p>';

			$content .= '<p>';
			$content .= wp_kses(
				sprintf(
					__( 'Enable the use of <a href="%s">Weights</a> to improve the relevance in product search results, based on matches in product titles, descriptions, contents, categories, tags, attributes and their SKU.', 'woocommerce-product-search' ),
					esc_url( self::get_admin_section_url( self::SECTION_WEIGHTS ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= esc_html__( 'Weights can also be set for specific products and product categories.', 'woocommerce-product-search' );
			$content .= '</p>';

			$content .= '<p>';
			$content .= esc_html__( 'Several live filtering facilities help your visitors to find the desired products quickly.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'For this purpose, several <a href="%s">Widgets</a> can be added to sidebars.', 'woocommerce-product-search' ),
					esc_url( admin_url( 'widgets.php' ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'You can use the <a href="%s">Assistant</a> to add filter widgets to your sidebars.', 'woocommerce-product-search' ),
					esc_url( self::get_admin_section_url( self::SECTION_ASSISTANT ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= esc_html__( 'These live filters update the products shown in your shop instantly, to include those that are related.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= esc_html__( 'Among them, you will find:', 'woocommerce-product-search' );
			$content .= '</p>';
			$content .= '<ul>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Attributes', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Categories', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Price', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Rating', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Sale', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Search', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Tags', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '</ul>';

			$content .= '<p>';
			$content .= esc_html__( 'The advanced Product Search Field that can replace the standard search is also available as a widget:', 'woocommerce-product-search' );
			$content .= '</p>';
			$content .= '<ul>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Search Field', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '</ul>';

			$content .= '<p>';
			$content .= esc_html__( 'The same features are also available through shortcodes and API functions.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'Please refer to the <a href="%s">WooCommerce Product Search</a> documentation pages for detailed information.', 'woocommerce-product-search' ),
					esc_url( 'https://docs.woocommerce.com/document/woocommerce-product-search/' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= '</p>';

			$content .= '</div>';

			$screen->add_help_tab( array(
				'id'      => $id,
				'title'   => $title,
				'content' => $content
			) );
		}
	}

	/**
	 * Returns the admin URL for the default or given section.
	 *
	 * @param string $section
	 */
	public static function get_admin_section_url( $section = '' ) {
		$path = 'admin.php?page=wc-settings&tab=product-search';
		switch ( $section ) {
			case self::SECTION_GENERAL :
			case self::SECTION_WEIGHTS :
			case self::SECTION_THUMBNAILS :
			case self::SECTION_CSS :
			case self::SECTION_INDEX :
			case self::SECTION_ASSISTANT :
			case self::SECTION_HELP :
			case self::SECTION_WELCOME :
				break;
			default :
				$section = '';
		}
		if ( !empty( $section ) ) {
			$path .= '&section=' . $section;
		}
		return admin_url( $path );
	}

	/**
	 * Adds the Product Search tab to the WooCommerce Settings.
	 *
	 * @param array $pages
	 *
	 * @return array of settings pages
	 */
	public static function woocommerce_settings_tabs_array( $pages ) {
		$pages['product-search'] = __( 'Search', 'woocommerce-product-search' );
		return $pages;
	}

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {

		global $current_section;

		if ( empty( $current_section ) ) {
			$current_section = self::SECTION_GENERAL;
		}

		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html( __( 'Access denied.', 'woocommerce-product-search' ) ) );
		}

		$options = get_option( 'woocommerce-product-search', null );
		if ( $options === null ) {
			if ( add_option( 'woocommerce-product-search', array(), '', 'no' ) ) {
				$options = get_option( 'woocommerce-product-search' );
			}
		}

		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], 'set' ) ) {

				switch ( $current_section ) {

					case self::SECTION_GENERAL :
						$match_split = isset( $_POST[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) ? intval( $_POST[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) : WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;
						if ( $match_split < WooCommerce_Product_Search_Service::MATCH_SPLIT_MIN || $match_split > WooCommerce_Product_Search_Service::MATCH_SPLIT_MAX ) {
							$match_split = WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;
						}
						$options[WooCommerce_Product_Search_Service::MATCH_SPLIT] = $match_split;
						$options[WooCommerce_Product_Search::RECORD_HITS] = isset( $_POST[WooCommerce_Product_Search::RECORD_HITS] );
						$options[WooCommerce_Product_Search::FILTER_PROCESS_DOM] = isset( $_POST[WooCommerce_Product_Search::FILTER_PROCESS_DOM] );
						$options[WooCommerce_Product_Search::FILTER_PARSE_DOM] = isset( $_POST[WooCommerce_Product_Search::FILTER_PARSE_DOM] );
						$options[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] = isset( $_POST[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] );
						$options[WooCommerce_Product_Search::LOG_QUERY_TIMES] = isset( $_POST[WooCommerce_Product_Search::LOG_QUERY_TIMES] );
						$options[WooCommerce_Product_Search::DELETE_DATA] = isset( $_POST[WooCommerce_Product_Search::DELETE_DATA] );

						$options[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] = isset( $_POST[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] );

						$max_title_words = isset( $_POST[WooCommerce_Product_Search::MAX_TITLE_WORDS] ) && ( $_POST[WooCommerce_Product_Search::MAX_TITLE_WORDS] !== '' ) ? intval( $_POST[WooCommerce_Product_Search::MAX_TITLE_WORDS] ) : WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
						if ( $max_title_words < 0 ) {
							$max_title_words = WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
						}
						$options[WooCommerce_Product_Search::MAX_TITLE_WORDS] = $max_title_words;

						$max_title_characters = isset( $_POST[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) && ( $_POST[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] !== '' ) ? intval( $_POST[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) : WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
						if ( $max_title_characters < 0 ) {
							$max_title_characters = WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
						}
						$options[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] = $max_title_characters;

						$max_excerpt_words = isset( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) && ( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] !== '' ) ? intval( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) : WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
						if ( $max_excerpt_words < 0 ) {
							$max_excerpt_words = WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
						}
						$options[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] = $max_excerpt_words;

						$max_excerpt_characters = isset( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) && ( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] !== '' ) ? intval( $_POST[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) : WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;
						if ( $max_excerpt_characters < 0 ) {
							$max_excerpt_characters = WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;
						}
						$options[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] = $max_excerpt_characters;

						$options[WooCommerce_Product_Search::AUTO_REPLACE]       = isset( $_POST[WooCommerce_Product_Search::AUTO_REPLACE] );
						if ( WPS_EXT_PDS ) {
							$options[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] = isset( $_POST[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] );
							$options[WooCommerce_Product_Search::AUTO_REPLACE_JSON]  = isset( $_POST[WooCommerce_Product_Search::AUTO_REPLACE_JSON] );
							$json_limit = '';
							if ( isset( $_POST[WooCommerce_Product_Search::JSON_LIMIT] ) ) {
								if ( trim( $_POST[WooCommerce_Product_Search::JSON_LIMIT] ) !== '' ) {
									$json_limit = intval( $_POST[WooCommerce_Product_Search::JSON_LIMIT] );
									if ( $json_limit < 0 ) {
										$json_limit = WooCommerce_Product_Search::JSON_LIMIT_DEFAULT;
									}
								}
							}
							$options[WooCommerce_Product_Search::JSON_LIMIT] = $json_limit;
						}
						if ( WPS_EXT_REST ) {
							$options[WooCommerce_Product_Search::AUTO_REPLACE_REST] = isset( $_POST[WooCommerce_Product_Search::AUTO_REPLACE_REST] );
						}

						$options[WooCommerce_Product_Search::AUTO_REPLACE_FORM]  = isset( $_POST[WooCommerce_Product_Search::AUTO_REPLACE_FORM] );
						if ( $options[WooCommerce_Product_Search::AUTO_REPLACE_FORM] ) {
							$old_instance = isset( $options[WooCommerce_Product_Search::AUTO_INSTANCE] ) ? $options[WooCommerce_Product_Search::AUTO_INSTANCE] : WooCommerce_Product_Search_Widget::get_auto_instance_default();
							$search_widget_instance = new WooCommerce_Product_Search_Widget( 'wps-auto-instance' );
							$search_widget_instance->_set( 1 );
							$field_names = array(

								'query_title',
								'excerpt',
								'content',
								'categories',
								'tags',
								'attributes',
								'sku',
								'order_by',
								'order',
								'limit',
								'show_more',
								'category_results',
								'category_limit',
								'product_thumbnails',
								'show_description',
								'show_price',
								'show_add_to_cart',

								'delay',
								'characters',
								'inhibit_enter',
								'navigable',
								'placeholder',
								'show_clear',
								'submit_button',
								'submit_button_label',
								'dynamic_focus',
								'floating',
								'no_results',
								'height',
								'wpml'
							);
							$new_instance = array();
							foreach( $field_names as $field_name ) {
								$field = $search_widget_instance->get_field_name( $field_name );
								$parts = explode( ' ', trim( preg_replace( '/[ \[\]]+/', ' ', $field ) ) );
								$sub = $_POST;
								$n = count( $parts );
								$i = 0;
								$value = null;
								foreach( $parts as $part ) {
									if ( isset( $sub[$part] ) ) {
										$i++;
										$sub = $sub[$part];
										if ( $i === $n ) {
											$value = $sub;
										}
									} else {
										break;
									}
								}
								if ( $value !== null ) {
									$new_instance[$field_name] = stripslashes( $value );
								}
							}
							$new_instance = $search_widget_instance->update( $new_instance, $old_instance );
							$options[WooCommerce_Product_Search::AUTO_INSTANCE] = $new_instance;
						} else {
							unset( $options[WooCommerce_Product_Search::AUTO_INSTANCE] );
						}
						break;

					case self::SECTION_WEIGHTS :
						$options[WooCommerce_Product_Search::USE_WEIGHTS]       = isset( $_POST[WooCommerce_Product_Search::USE_WEIGHTS] );
						$options[WooCommerce_Product_Search::WEIGHT_TITLE]      = isset( $_POST[WooCommerce_Product_Search::WEIGHT_TITLE] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_TITLE] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_TITLE] ) : WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_EXCERPT]    = isset( $_POST[WooCommerce_Product_Search::WEIGHT_EXCERPT] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_EXCERPT] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_EXCERPT] ) : WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_CONTENT]    = isset( $_POST[WooCommerce_Product_Search::WEIGHT_CONTENT] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_CONTENT] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_CONTENT] ) : WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_TAGS]       = isset( $_POST[WooCommerce_Product_Search::WEIGHT_TAGS] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_TAGS] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_TAGS] ) : WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_CATEGORIES] = isset( $_POST[WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) : WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] = isset( $_POST[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) : WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT;
						$options[WooCommerce_Product_Search::WEIGHT_SKU]        = isset( $_POST[WooCommerce_Product_Search::WEIGHT_SKU] ) && strlen( trim( $_POST[WooCommerce_Product_Search::WEIGHT_SKU] ) ) > 0 ? intval( $_POST[WooCommerce_Product_Search::WEIGHT_SKU] ) : WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT;
						break;

					case self::SECTION_THUMBNAILS :
						$thumbnail_width = isset( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? intval( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
						if ( ( $thumbnail_width < 0 ) || $thumbnail_width > WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
							$thumbnail_width = WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
						}
						$options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] = $thumbnail_width;

						$thumbnail_height = isset( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? intval( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
						if ( ( $thumbnail_height < 0 ) || $thumbnail_height > WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
							$thumbnail_height = WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
						}
						$options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] = $thumbnail_height;

						$options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] = isset( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] );
						$options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] = isset( $_POST[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] );

						$product_taxonomies = WooCommerce_Product_Search_Thumbnail::get_product_taxonomies();
						foreach( $product_taxonomies as $product_taxonomy ) {
							if ( $taxonomy = get_taxonomy( $product_taxonomy ) ) {
								$thumbnail_width = isset( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? intval( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
								if ( ( $thumbnail_width < 0 ) || $thumbnail_width > WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
									$thumbnail_width = WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
								}
								$options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] = $thumbnail_width;

								$thumbnail_height = isset( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? intval( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
								if ( ( $thumbnail_height < 0 ) || $thumbnail_height > WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
									$thumbnail_height = WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
								}
								$options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] = $thumbnail_height;

								$options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] = isset( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] );
								$options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] = isset( $_POST[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] );
							}
						}

						break;

					case self::SECTION_CSS :
						$options[WooCommerce_Product_Search::ENABLE_CSS]        = isset( $_POST[WooCommerce_Product_Search::ENABLE_CSS] );
						$options[WooCommerce_Product_Search::ENABLE_INLINE_CSS] = isset( $_POST[WooCommerce_Product_Search::ENABLE_INLINE_CSS] );
						$options[WooCommerce_Product_Search::INLINE_CSS]        = isset( $_POST[WooCommerce_Product_Search::INLINE_CSS] ) ? trim( strip_tags( $_POST[WooCommerce_Product_Search::INLINE_CSS] ) ) : WooCommerce_Product_Search::INLINE_CSS_DEFAULT;
						break;

					case self::SECTION_INDEX :
						if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
							$work_cycle = isset( $_POST[WooCommerce_Product_Search_Worker::WORK_CYCLE] ) ? intval( $_POST[WooCommerce_Product_Search_Worker::WORK_CYCLE] ) : WooCommerce_Product_Search_Worker::get_work_cycle_default();
							if ( $work_cycle <= 0 ) {
								$work_cycle = WooCommerce_Product_Search_Worker::get_work_cycle_default();
							}
							$options[WooCommerce_Product_Search_Worker::WORK_CYCLE] = $work_cycle;

							$idle_cycle = isset( $_POST[WooCommerce_Product_Search_Worker::IDLE_CYCLE] ) ? intval( $_POST[WooCommerce_Product_Search_Worker::IDLE_CYCLE] ) : WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT;
							if ( $idle_cycle <= 0 ) {
								$idle_cycle = WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT;
							}
							$options[WooCommerce_Product_Search_Worker::IDLE_CYCLE] = $idle_cycle;

							$index_per_cycle = isset( $_POST[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) ? intval( $_POST[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) : WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT;
							if ( $index_per_cycle <= 0 ) {
								$index_per_cycle = WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT;
							}
							$options[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] = $index_per_cycle;

							$index_order = isset( $_POST[WooCommerce_Product_Search_Indexer::INDEX_ORDER] ) ? $_POST[WooCommerce_Product_Search_Indexer::INDEX_ORDER] : WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT;
							switch( $index_order ) {
								case WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENT :
								case WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENT :
								case WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENTLY_MODIFIED :
								case WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENTLY_MODIFIED :
									break;
								default :
									$index_order = WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT;
							}
							$options[WooCommerce_Product_Search_Indexer::INDEX_ORDER] = $index_order;
						}
						break;

					case self::SECTION_ASSISTANT :
						if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {
							global $wp_registered_sidebars;
							$sidebars_widgets     = get_option( 'sidebars_widgets', array() );
							$sidebar_id           = !empty( $_POST['wps-assistant-sidebar-id'] ) ? $_POST['wps-assistant-sidebar-id'] : null;
							$assistant_widget_ids = !empty( $_POST['wps-assistant-widget-ids'] ) ? $_POST['wps-assistant-widget-ids'] : null;
							if (
								$sidebar_id !== null &&
								$sidebar_id !== 'wp_inactive_widgets' &&
								isset( $sidebars_widgets[$sidebar_id] ) &&
								$assistant_widget_ids !== null &
								is_array( $assistant_widget_ids ) &&
								count( $assistant_widget_ids ) > 0
							) {
								$i = 0;
								foreach ( $assistant_widget_ids as $assistant_widget_id ) {
									$assistant_widget_id = explode( '-', $assistant_widget_id );
									$id_base             = isset( $assistant_widget_id[0] ) ? $assistant_widget_id[0] : null;
									$attribute_taxonomy  = isset( $assistant_widget_id[1] ) ? $assistant_widget_id[1] : null;
									if ( $id_base !== null ) {
										$widget_instances = get_option( 'widget_' . $id_base, array() );
										$numbers          = array_filter( array_keys( $widget_instances ), 'is_int' );
										$next             = ( count( $numbers ) > 0 ) ? max( $numbers ) + 1 : self::WIDGET_NUMBER_START;
										$widget           = null;
										$widget_settings  = null;
										switch( $id_base ) {
											case 'woocommerce_product_search_filter_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_attribute_widget' :
												if ( $taxonomy = get_taxonomy( $attribute_taxonomy ) ) {
													$widget = new WooCommerce_Product_Search_Filter_Attribute_Widget();
													$title = !empty( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : $taxonomy->label;
													$widget_settings = $widget->update( array_merge( $widget->get_default_instance(), array( 'taxonomy' => $taxonomy->name ) ), array() );
												}
												break;
											case 'woocommerce_product_search_filter_category_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Category_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_price_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Price_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_rating_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Rating_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_sale_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Sale_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_tag_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Tag_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
											case 'woocommerce_product_search_filter_reset_widget' :
												$widget = new WooCommerce_Product_Search_Filter_Reset_Widget();
												$widget_settings = $widget->update( $widget->get_default_instance(), array() );
												break;
										}
										if ( $widget !== null && $widget_settings !== null ) {
											$widget_instances[$next] = $widget_settings;
											$sidebars_widgets[$sidebar_id][] = $id_base . '-' . $next;
											update_option( 'widget_' . $id_base, $widget_instances );
											$i++;
										}
										unset( $widget );
										unset( $widget_settings );
									}
								}
								update_option( 'sidebars_widgets', $sidebars_widgets );
								if ( class_exists( 'WC_Admin_Settings' ) && method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
									WC_Admin_Settings::add_message(
										_n( 'One widget has been added.', sprintf( '%d widgets have been added.', $i ), $i, 'woocommerce-product-search' )
									);
								}
							}
						}
						break;
				}

				update_option( 'woocommerce-product-search', $options );
			}
		}
	}

	/**
	 * Renders the admin section.
	 */
	public static function woocommerce_product_search() {

		global $current_section, $wpdb;

		if ( empty( $current_section ) ) {
			$current_section = self::SECTION_GENERAL;
		}

		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html( __( 'Access denied.', 'woocommerce-product-search' ) ) );
		}

		wp_enqueue_script( 'product-search-admin', WOO_PS_PLUGIN_URL . '/js/product-search-admin.js', array( 'jquery', ), WOO_PS_PLUGIN_VERSION, true );
		wp_enqueue_style( 'wps-admin' );

		$options = get_option( 'woocommerce-product-search', null );
		if ( $options === null ) {
			if ( add_option( 'woocommerce-product-search', array(), '', 'no' ) ) {
				$options = get_option( 'woocommerce-product-search' );
			}
		}

		$auto_replace       = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE] : WooCommerce_Product_Search::AUTO_REPLACE_DEFAULT;
		$auto_replace_admin = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] : WooCommerce_Product_Search::AUTO_REPLACE_ADMIN_DEFAULT;
		$auto_replace_json  = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_JSON] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_JSON] : WooCommerce_Product_Search::AUTO_REPLACE_JSON_DEFAULT;
		$json_limit         = isset( $options[WooCommerce_Product_Search::JSON_LIMIT] ) ? ( $options[WooCommerce_Product_Search::JSON_LIMIT] !== '' ? intval( $options[WooCommerce_Product_Search::JSON_LIMIT] ) : '' ) : WooCommerce_Product_Search::JSON_LIMIT_DEFAULT;
		$auto_replace_rest  = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] : WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT;
		$auto_replace_form  = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_FORM] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_FORM] : WooCommerce_Product_Search::AUTO_REPLACE_FORM_DEFAULT;
		$auto_instance      = isset( $options[WooCommerce_Product_Search::AUTO_INSTANCE] ) ? $options[WooCommerce_Product_Search::AUTO_INSTANCE] : WooCommerce_Product_Search_Widget::get_auto_instance_default();

		$use_short_description  = isset( $options[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] ) ? $options[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] : WooCommerce_Product_Search::USE_SHORT_DESCRIPTION_DEFAULT;
		$max_title_words        = isset( $options[WooCommerce_Product_Search::MAX_TITLE_WORDS] ) ? intval( $options[WooCommerce_Product_Search::MAX_TITLE_WORDS] ) : WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
		$max_title_characters   = isset( $options[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) ? intval( $options[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) : WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
		$max_excerpt_words      = isset( $options[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) ? intval( $options[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) : WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
		$max_excerpt_characters = isset( $options[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) ? intval( $options[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) : WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;

		$match_split        = isset( $options[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) ? intval( $options[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) : WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;
		$record_hits        = isset( $options[WooCommerce_Product_Search::RECORD_HITS] ) ? $options[WooCommerce_Product_Search::RECORD_HITS] : WooCommerce_Product_Search::RECORD_HITS_DEFAULT;
		$filter_process_dom = isset( $options[WooCommerce_Product_Search::FILTER_PROCESS_DOM] ) ? $options[WooCommerce_Product_Search::FILTER_PROCESS_DOM] : WooCommerce_Product_Search::FILTER_PROCESS_DOM_DEFAULT;
		$filter_parse_dom   = isset( $options[WooCommerce_Product_Search::FILTER_PARSE_DOM] ) ? $options[WooCommerce_Product_Search::FILTER_PARSE_DOM] : WooCommerce_Product_Search::FILTER_PARSE_DOM_DEFAULT;
		$service_get_terms_args_apply = isset( $options[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] ) ? $options[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] : WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY_DEFAULT;
		$log_query_times    = isset( $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] ) ? $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] : WooCommerce_Product_Search::LOG_QUERY_TIMES_DEFAULT;
		$delete_data        = isset( $options[WooCommerce_Product_Search::DELETE_DATA] ) ? $options[WooCommerce_Product_Search::DELETE_DATA] : false;

		$use_weights       = isset( $options[WooCommerce_Product_Search::USE_WEIGHTS] ) ? $options[WooCommerce_Product_Search::USE_WEIGHTS] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
		$weight_title      = isset( $options[WooCommerce_Product_Search::WEIGHT_TITLE] ) ? $options[WooCommerce_Product_Search::WEIGHT_TITLE] : WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT;
		$weight_excerpt    = isset( $options[WooCommerce_Product_Search::WEIGHT_EXCERPT] ) ? $options[WooCommerce_Product_Search::WEIGHT_EXCERPT] : WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT;
		$weight_content    = isset( $options[WooCommerce_Product_Search::WEIGHT_CONTENT] ) ? $options[WooCommerce_Product_Search::WEIGHT_CONTENT] : WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT;
		$weight_tags       = isset( $options[WooCommerce_Product_Search::WEIGHT_TAGS] ) ? $options[WooCommerce_Product_Search::WEIGHT_TAGS] : WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT;
		$weight_categories = isset( $options[WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) ? $options[WooCommerce_Product_Search::WEIGHT_CATEGORIES] : WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT;
		$weight_attributes = isset( $options[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) ? $options[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] : WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT;
		$weight_sku        = isset( $options[WooCommerce_Product_Search::WEIGHT_SKU] ) ? $options[WooCommerce_Product_Search::WEIGHT_SKU] : WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT;

		$thumbnail_width   = isset( $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
		$thumbnail_height  = isset( $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
		$thumbnail_crop    = isset( $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] ) ? $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_CROP;
		$thumbnail_use_placeholder = isset( $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] ) ? $options[WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER_DEFAULT;

		$enable_css        = isset( $options[WooCommerce_Product_Search::ENABLE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_CSS] : WooCommerce_Product_Search::ENABLE_CSS_DEFAULT;
		$enable_inline_css = isset( $options[WooCommerce_Product_Search::ENABLE_INLINE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_INLINE_CSS] : WooCommerce_Product_Search::ENABLE_INLINE_CSS_DEFAULT;
		$inline_css        = isset( $options[WooCommerce_Product_Search::INLINE_CSS] ) ? $options[WooCommerce_Product_Search::INLINE_CSS] : WooCommerce_Product_Search::INLINE_CSS_DEFAULT;

		echo '<style type="text/css">';
		echo 'div.product-search-tabs ul li a { outline: none; }';
		echo '</style>';

		echo '<div class="woocommerce-product-search woocommerce-product-search-settings">';

		echo '<div class="product-search-tabs">';
		echo '<ul class="subsubsub">';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'general' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_GENERAL ) ) );
		echo esc_html( __( 'General', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'weights' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_WEIGHTS ) ) );
		echo esc_html( __( 'Weights', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'thumbnails' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_THUMBNAILS ) ) );
		echo esc_html( __( 'Thumbnails', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'css' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_CSS ) ) );
		echo esc_html( __( 'CSS', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == self::SECTION_INDEX ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_INDEX ) ) );
		echo esc_html( __( 'Index', 'woocommerce-product-search' ) );
		echo '</a>';
		if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {
			echo '|';
			echo '</li>';
			echo '<li class="tab-header">';
			printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == self::SECTION_ASSISTANT ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_ASSISTANT ) ) );
			echo esc_html( __( 'Assistant', 'woocommerce-product-search' ) );
			echo '</a>';
			echo '</li>';
		} else {
			echo '</li>';
		}
		echo '&mdash;';
		echo '<li class="tab-header">';
		echo '<a href="' . esc_url( admin_url( 'admin.php?page=wc-reports&tab=search' ) ) . '">' . esc_html__( 'Reports', 'woocommerce-product-search' ) . '</a>';
		echo '</li>';

		echo '<li class="tab-header">';
		echo '<a id="wps-faq-help-trigger" href="#">';
		echo wc_help_tip(
			wp_kses(
				__( 'The <strong>Search</strong> section in the <strong>Help</strong> tab above provides a brief overview.', 'woocommerce-product-search' ),
				array( 'strong' => array() )
			),
			true
		);
		echo '</a>';
		echo '</li>';

		echo '</ul>';
		echo '</div>';

		echo '<div style="clear:both"></div>';

		echo '<form action="" name="options" method="post">';
		echo '<div>';

		switch ( $current_section ) {

			case self::SECTION_GENERAL :

				echo '<div id="product-search-general-tab" class="product-search-tab">';

				echo '<h3 class="section-heading">' . esc_html( __( 'General Settings', 'woocommerce-product-search' ) ) . '</h3>';

				echo '<h4>';
				echo esc_html( __( 'Standard Product Search', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', WooCommerce_Product_Search::AUTO_REPLACE, $auto_replace ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Optimize front end product searches', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled and where possible, front end product searches will provide results powered by the search engine.', 'woocommerce-product-search' );
				echo '</p>';

				if ( !WPS_EXT_PDS ) {
					echo '<p>';
					esc_html_e( 'Back end and JSON searches are disabled via WPS_EXT_PDS.', 'woocommerce-product-search' );
					echo '</p>';
				}

				echo '<p>';
				echo '<label>';
				printf(
					'<input name="%s" type="checkbox" %s %s />',
					WooCommerce_Product_Search::AUTO_REPLACE_ADMIN,
					$auto_replace_admin ? ' checked="checked" ' : '',
					WPS_EXT_PDS ? '' : ' disabled="disabled" '
				);
				echo ' ';
				echo esc_html( __( 'Optimize back end product searches', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled and where possible, back end product searches will provide results powered by the search engine.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf(
					'<input name="%s" type="checkbox" %s %s />',
					WooCommerce_Product_Search::AUTO_REPLACE_JSON,
					$auto_replace_json ? ' checked="checked" ' : '',
					WPS_EXT_PDS ? '' : ' disabled="disabled" '
				);
				echo ' ';
				echo esc_html( __( 'Optimize JSON product searches', 'woocommerce-product-search' ) );
				echo '</label>';
				echo ' ';
				echo wc_help_tip( __( 'If enabled, JSON product searches are powered by the search engine when possible.', 'woocommerce-product-search' ) );
				echo ' &mdash; ';
				printf( '<label title="%s">', __( 'Limit JSON product search results', 'woocommerce-product-search' ) );
				echo esc_html( _x( 'Limit', 'Limit JSON product search results' , 'woocommerce-product-search' ) );
				echo ' ';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%s" placeholder="%s" %s/>',
					esc_attr( WooCommerce_Product_Search::JSON_LIMIT ),
					esc_attr( $json_limit ),
					esc_attr__( 'inherit', 'woocommerce-product-search' ),
					WPS_EXT_PDS ? '' : ' disabled="disabled" '
				);
				echo '</label>';
				echo ' ';
				echo wc_help_tip(
					__( 'The number of results is capped by the limit.', 'woocommerce-product-search' ) .
					' ' .
					__( 'For unlimited results, use 0 (zero).', 'woocommerce-product-search' ) .
					' ' .
					__( 'Leave empty to use internal limits.', 'woocommerce-product-search' )
				);
				echo '</p>';
				echo '<p class="description">';
				echo ' ';
				echo esc_html__( 'These are used to look up products in fields.', 'woocommerce-product-search' );
				echo ' ';
				echo wc_help_tip( __( 'Test the current configuration with this field &hellip;', 'woocommerce-product-search' ) );
				echo ' ';
				echo '<form action="" method="post">';
				echo '<select class="wc-product-search" multiple="multiple" style="width: 33%;" id="add_item_id" name="add_order_items[]" data-placeholder="';
				echo esc_attr__( 'Search for a product&hellip;', 'woocommerce' );
				echo '"></select>';
				echo '</form>';
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf(
					'<input name="%s" type="checkbox" %s %s />',
					WooCommerce_Product_Search::AUTO_REPLACE_REST,
					$auto_replace_rest ? ' checked="checked" ' : '',
					WPS_EXT_REST ? '' : ' disabled="disabled" '
				);
				echo ' ';
				echo esc_html( __( 'Optimize REST API product searches', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled and where possible, product searches via the REST API will provide results powered by the search engine.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf( '<input id="wps-auto-replace-form-checkbox" name="%s" type="checkbox" %s />', WooCommerce_Product_Search::AUTO_REPLACE_FORM, $auto_replace_form ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Replace the standard product search form', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled and where possible, the standard product search form is replaced automatically with the advanced Product Search Field.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'This provides the same functionality and options as the Product Search Field widget.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<div id="wps-auto-replace-instance-options" style="padding: 4px; border-radius: 4px; border: 1px dotted #aaa;">';
				echo '<p class="description">';
				esc_html_e( 'Product Search Field settings &hellip;', 'woocommerce-product-search' );
				echo '</p>';
				$search_widget_instance = new WooCommerce_Product_Search_Widget( 'wps-auto-instance' );
				$search_widget_instance->_set( 1 );
				echo $search_widget_instance->form( $auto_instance );
				echo '</div>';

				echo '<script type="text/javascript">';
				echo 'document.addEventListener( "DOMContentLoaded", function() {';
				echo 'if ( typeof jQuery !== "undefined" ) {';
				echo 'jQuery("#wps-auto-replace-instance-options").toggle(jQuery("#wps-auto-replace-form-checkbox").is(":checked"));';
				echo 'jQuery(document).on( "click", "#wps-auto-replace-form-checkbox", function() {';
				echo 'jQuery("#wps-auto-replace-instance-options").toggle(this.checked);';
				echo '});';
				echo '}';
				echo '} );';
				echo '</script>';

				echo '<h4>';
				echo esc_html( __( 'Shorten Titles and Descriptions', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p class="description">';
				esc_html_e( 'The results shown with the Product Search Field can have their titles and descriptions automatically shortened.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'Use any number higher than 0 to limit the number of words or characters shown.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'Where 0 is indicated, no limit applies.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<table>';
				echo '<tr>';

				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_WORDS ),
					esc_attr__( 'The maximum number of words shown in titles.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Words in Titles', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_WORDS ),
					esc_attr( $max_title_words ),
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT )
				);
				echo '</td>';

				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_WORDS ),
					esc_attr__( 'The maximum number of words shown in short descriptions.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Words in Descriptions', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_WORDS ),
					esc_attr( $max_excerpt_words ),
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT )
				);
				echo '</td>';

				echo '</tr>';
				echo '<tr>';

				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_CHARACTERS ),
					esc_attr__( 'The maximum number of characters shown in titles.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Characters in Titles', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_CHARACTERS ),
					esc_attr( $max_title_characters ),
					esc_attr( WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT )
				);
				echo '</td>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS ),
					esc_attr__( 'The maximum number of characters shown in short descriptions.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Characters in Descriptions', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';

				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS ),
					esc_attr( $max_excerpt_characters ),
					esc_attr( WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';
				echo '</table>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::USE_SHORT_DESCRIPTION ), $use_short_description ? ' checked="checked" ' : '' );
				echo ' ';
				esc_html_e( 'Use product short descriptions', 'woocommerce-product-search' );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled and where a product\'s short description is not empty, this is used to display the (shortened) description in the results of the Product Search Field.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<h4>';
				echo esc_html( __( 'Search Term Threshold', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p class="description">';
				esc_html_e( 'Searches are quicker for exact matches and take a bit longer for similar words &ndash; those that start with the search term.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'Exact matches may result in fewer search results, as search terms with a length below the threshold will only produce matches if the exact term is found.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'Similar matches produce more search results when the length of the search terms is at or above the treshold.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<table>';
				echo '<tr>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Service::MATCH_SPLIT ),
					esc_attr(
						__( 'This determines the minimum length of a search term for similar matches to be retrieved.', 'woocommerce-product-search' ) .
						' ' .
						__( 'Search terms that are shorter will only produce matches if the exact term is found.', 'woocommerce-product-search' ) .
						' ' .
						__( 'When set to 0, no minimum length is required for similar matches to be retrieved.', 'woocommerce-product-search' ) .
						' ' .
						__( 'In this context, &ndash;similar&ndash; means words starting with the search term.', 'woocommerce-product-search' )
					)
				);
				echo esc_html( __( 'Threshold', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<select name="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Service::MATCH_SPLIT ),
					esc_attr__( 'Minimum word length to search for similar occurrences.', 'woocommerce-product-search' )
				);
				for ( $i = WooCommerce_Product_Search_Service::MATCH_SPLIT_MIN; $i <= WooCommerce_Product_Search_Service::MATCH_SPLIT_MAX; $i++ ) {
					switch( $i ) {
						case 0 :
							$info = __( 'always produce any similar matches', 'woocommerce-product-search' );
							break;
						default :
							$info = sprintf( __( 'at least %d for similar terms', 'woocommerce-product-search' ), $i );
					}
					printf( '<option value="%d" %s>%s</option>',
						esc_attr( $i ),
						$i === $match_split ? ' selected="selected" ' : '',
						esc_html( $i ) . '&nbsp;&mdash;&nbsp;' . esc_attr( $info )
					);
				}
				echo '</select>';
				echo '&nbsp;';
				esc_html_e( 'characters', 'woocommerce-product-search' );
				echo '</td>';
				echo '</tr>';
				echo '</table>';

				echo '<h4>';
				echo esc_html( __( 'Statistics', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::RECORD_HITS ), $record_hits ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Record live search data', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled, statistical data for product searches is recorded.', 'woocommerce-product-search' );
				echo ' ';
				echo wp_kses(
					sprintf(
						__( 'Live search statistics are available in the <a href="%s">Search</a> section of the reports.', 'woocommerce-product-search' ),
						esc_url( admin_url( 'admin.php?page=wc-reports&tab=search' ) )
					),
					array( 'a' => array( 'href' => array(), 'class' => array() ) )
				);
				echo '</p>';

				echo '<h4>';
				echo esc_html( __( 'Optimization', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::FILTER_PROCESS_DOM ), $filter_process_dom ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Optimize responses for filter requests', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled, the HTML response for filter requests is optimized by removing unnecessary elements.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::FILTER_PARSE_DOM ), $filter_parse_dom ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Use accurate optimization', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled, a more accurate algorithm is used to process filter responses.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<h4>';
				echo esc_html( __( 'Filters', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY ), $service_get_terms_args_apply ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Apply product filters in general', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'If enabled, the current choice of product filters can affect the choices offered for products, product categories, product tags and product attributes presented more broadly, beyond the facilities provided by the extension.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'For example, if enabled, this can affect the product categories displayed with the standard Product Categories widget, reducing those displayed to the set of matching product categories only.', 'woocommerce-product-search' );

				echo '</p>';

				echo '<h4>';
				echo esc_html( __( 'Logs', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::LOG_QUERY_TIMES ), $log_query_times ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Log main query times', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				printf(
					wp_kses(
						__( 'If enabled, the query times for search terms will be logged. <a href="https://codex.wordpress.org/Debugging_in_WordPress">Debugging</a> must be enabled for query times to be recorded in the log.', 'woocommerce-product-search' ),
						array( 'a' => array( 'href' => array() ) )
					)
				);
				echo '</p>';

				echo '<h4>';
				echo esc_html( __( 'Delete Data', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::DELETE_DATA ), $delete_data ? ' checked="checked" ' : '' );
				echo ' ';
				esc_html_e( 'Delete search settings and data on deactivation', 'woocommerce-product-search' );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'This option will delete ALL search settings and data when the WooCommerce Product Search extension is deactivated.', 'woocommerce-product-search' );
				echo ' ';
				echo '<strong>';
				esc_html_e( 'This action cannot be reversed.', 'woocommerce-product-search' );
				echo '</strong>';
				echo '</p>';
				echo '<p class="description">';
				esc_html_e( 'CAUTION: If this option is active while the plugin is deactivated, ALL plugin settings and data will be DELETED.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'This includes the removal of all search settings, search weights for products and terms and the associations of search thumbnail images with product categories, tags and attributes.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'If you are going to use this option, NOW would be a good time to make a backup of your site and its database.', 'woocommerce-product-search' );
				echo '</p>';

				echo '</div>';
				break;

			case self::SECTION_WEIGHTS :
				echo '<div id="product-search-weights-tab" class="product-search-tab">';
				echo '<h3 class="section-heading">' . esc_html( __( 'Search Weights', 'woocommerce-product-search' ) ) . '</h3>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', WooCommerce_Product_Search::USE_WEIGHTS, $use_weights ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Use weights', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				echo esc_html( __( 'If enabled, the relevance in product search results is enhanced by taking weights into account.', 'woocommerce-product-search' ) );
				echo '</p>';

				echo '<h4>' . esc_html( __( 'Relevance', 'woocommerce-product-search' ) ) . '</h4>';

				echo '<p class="description">';
				echo esc_html( __( 'The following weights determine the relevance of matches in the product title, excerpt, content, tags, categories, attributes and SKU.', 'woocommerce-product-search' ) );
				echo ' ';
				echo esc_html( __( 'By default, a higher title and SKU weight will promote search results that have matches in the title and SKU.', 'woocommerce-product-search' ) );
				echo ' ';
				echo esc_html( __( 'The weight of products and product categories can be modified individually, the computed sum of weights determines the relevance of a product in search results.', 'woocommerce-product-search' ) );
				echo '</p>';

				echo '<table>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_TITLE ) );
				echo esc_html( __( 'Title', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_TITLE ),
					esc_attr( $weight_title ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_EXCERPT ) );
				echo esc_html( __( 'Excerpt', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_EXCERPT ),
					esc_attr( $weight_excerpt ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_CONTENT ) );
				echo esc_html( __( 'Content', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_CONTENT ),
					esc_attr( $weight_content ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_TAGS ) );
				echo esc_html( __( 'Tags', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_TAGS ),
					esc_attr( $weight_tags ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_CATEGORIES ) );
				echo esc_html( __( 'Categories', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_CATEGORIES ),
					esc_attr( $weight_categories ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_ATTRIBUTES ) );
				echo esc_html( __( 'Attributes', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_ATTRIBUTES ),
					esc_attr( $weight_attributes ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf( '<label for="%s">', esc_attr( WooCommerce_Product_Search::WEIGHT_SKU ) );
				echo esc_html( __( 'SKU', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
					esc_attr( WooCommerce_Product_Search::WEIGHT_SKU ),
					esc_attr( $weight_sku ),
					esc_attr( WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT )
				);
				echo '</td>';
				echo '</tr>';

				echo '</table>';

				echo '</div>';
				break;

			case self::SECTION_THUMBNAILS :
				echo '<div id="product-search-thumbnails-tab" class="product-search-tab">';

				echo '<h2 class="section-heading">';
				echo esc_html( __( 'Thumbnails', 'woocommerce-product-search' ) );
				echo '</h2>';

				echo '<h3>';
				echo esc_html( __( 'Product Thumbnails', 'woocommerce-product-search' ) );
				echo '</h3>';

				echo '<p>';
				esc_html_e( 'The size defined here applies to the product thumbnails shown in the results of the Product Search Field.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<p class="description">';
				echo esc_html( __( 'Width and height in pixels used for thumbnails displayed in product search results.', 'woocommerce-product-search' ) );
				echo '</p>';

				echo '<p>';

				echo '<label>';
				echo esc_html( __( 'Width', 'woocommerce-product-search' ) );
				echo ' ';
				printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH ), esc_attr( $thumbnail_width ) );
				echo ' ';
				echo esc_html( __( 'px', 'woocommerce-product-search' ) );
				echo '</label>';

				echo '&emsp;&emsp;';

				echo '<label>';
				echo esc_html( __( 'Height', 'woocommerce-product-search' ) );
				echo ' ';
				printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT ), esc_attr( $thumbnail_height ) );
				echo ' ';
				echo esc_html( __( 'px', 'woocommerce-product-search' ) );
				echo '</label>';

				echo '&emsp;&emsp;';

				printf( '<label title="%s">', esc_attr__( 'If enabled, the thumbnail images are cropped to match the dimensions exactly. Otherwise the thumbnails will be adjusted in size while matching the aspect ratio of the original image.', 'woocommerce-product-search' ) );
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP ), $thumbnail_crop ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Crop thumbnails', 'woocommerce-product-search' ) );
				echo '</label>';

				echo '&emsp;&emsp;';

				printf( '<label title="%s">', esc_attr__( 'If enabled, products without a featured product image will show a default placeholder thumbnail image.', 'woocommerce-product-search' ) );
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER ), $thumbnail_use_placeholder ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Placeholder thumbnails', 'woocommerce-product-search' ) );
				echo '</label>';

				echo '</p>';

				echo '<h3>';
				echo esc_html( __( 'Filter Thumbnails', 'woocommerce-product-search' ) );
				echo '</h3>';

				$product_taxonomies = WooCommerce_Product_Search_Thumbnail::get_product_taxonomies();

				echo '<p>';
				echo esc_html__( 'The sizes defined in this section determine the appearance of thumbnails used for product category, product tag and product attribute filters.', 'woocommerce-product-search' );
				echo '</p>';

				foreach( $product_taxonomies as $product_taxonomy ) {

					if ( !( $taxonomy = get_taxonomy( $product_taxonomy ) ) ) {
						continue;
					}

					$thumbnail_width   = isset( $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
					$thumbnail_height  = isset( $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
					$thumbnail_crop    = isset( $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] ) ? $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_CROP;
					$thumbnail_use_placeholder = isset( $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] ) ? $options[$taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] : WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER_DEFAULT;

					echo '<h4>';
					echo esc_html__( $taxonomy->label, 'woocommerce-product-search' );
					echo '</h4>';

					echo '<p>';

					echo '<label>';
					echo esc_html( __( 'Width', 'woocommerce-product-search' ) );
					echo ' ';
					printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( $taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH ), esc_attr( $thumbnail_width ) );
					echo ' ';
					echo esc_html( __( 'px', 'woocommerce-product-search' ) );
					echo '</label>';

					echo '&emsp;&emsp;';

					echo '<label>';
					echo esc_html( __( 'Height', 'woocommerce-product-search' ) );
					echo ' ';
					printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( $taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT ), esc_attr( $thumbnail_height ) );
					echo ' ';
					echo esc_html( __( 'px', 'woocommerce-product-search' ) );
					echo '</label>';

					echo '&emsp;&emsp;';

					printf( '<label title="%s">', esc_attr__( 'If enabled, the thumbnail images are cropped to match the dimensions exactly. Otherwise the thumbnails will be adjusted in size while matching the aspect ratio of the original image.', 'woocommerce-product-search' ) );
					printf( '<input name="%s" type="checkbox" %s />', esc_attr( $taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP ), $thumbnail_crop ? ' checked="checked" ' : '' );
					echo ' ';
					echo esc_html( __( 'Crop thumbnails', 'woocommerce-product-search' ) );
					echo '</label>';

					echo '&emsp;&emsp;';

					printf( '<label title="%s">', esc_attr__( 'If enabled, terms without a search filter image will show a default placeholder thumbnail image.', 'woocommerce-product-search' ) );
					printf( '<input name="%s" type="checkbox" %s />', esc_attr( $taxonomy->name . '-' . WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER ), $thumbnail_use_placeholder ? ' checked="checked" ' : '' );
					echo ' ';
					echo esc_html( __( 'Placeholder thumbnails', 'woocommerce-product-search' ) );
					echo '</label>';

					echo '</p>';
				}
				echo '</div>';
				break;

			case self::SECTION_CSS :
				echo '<div id="product-search-css-tab" class="product-search-tab">';
				echo '<h3 class="section-heading">' . esc_html( __( 'CSS', 'woocommerce-product-search' ) ) . '</h3>';

				echo '<p>';
				esc_html_e( 'These settings are related to the Product Search Field and Product Filters.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<h4>' . esc_html( __( 'Standard Stylesheet', 'woocommerce-product-search' ) ) . '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::ENABLE_CSS ), $enable_css ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Use the standard stylesheet', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				echo esc_html( __( 'If this option is enabled, the standard stylesheet is loaded when the Product Search Field or Product Filters are displayed.', 'woocommerce-product-search' ) );
				echo '</p>';

				echo '<h4>' . esc_html( __( 'Inline Styles', 'woocommerce-product-search' ) ) . '</h4>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', esc_attr( WooCommerce_Product_Search::ENABLE_INLINE_CSS ), $enable_inline_css ? ' checked="checked" ' : '' );
				echo ' ';
				echo esc_html( __( 'Use inline styles', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				echo esc_html( __( 'If this option is enabled, the inline styles are used when the Product Search Field or Product Filters are displayed.', 'woocommerce-product-search' ) );
				echo '</p>';

				echo '<p>';
				echo '<label>';
				echo esc_html( __( 'Inline styles', 'woocommerce-product-search' ) );
				echo '<br/>';
				printf( '<textarea style="font-family:monospace;width:50%%;height:25em;" name="%s">%s</textarea>', esc_attr( WooCommerce_Product_Search::INLINE_CSS ), esc_textarea( stripslashes( $inline_css ) ) );
				echo '</label>';
				echo '</p>';

				echo '</div>';
				break;

			case self::SECTION_INDEX :

				wp_enqueue_script( 'wps-indexer' );

				echo '<h4>';
				echo esc_html( __( 'Search Index', 'woocommerce-product-search' ) );
				echo '</h4>';

				echo '<p>';
				esc_html_e( 'Indexing is an automated process which is usually free of manual intervention.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'Normally, you would not need to modify the values here or stop the indexing process.', 'woocommerce-product-search' );
				echo ' ';
				esc_html_e( 'If the indexer is stopped while there are remaining entries left unprocessed, search results will not include all products.', 'woocommerce-product-search' );
				echo '</p>';

				echo '<h5>';
				esc_html_e( 'Status', 'woocommerce-product-search' );
				echo '</h5>';

				$status = WooCommerce_Product_Search_Worker::get_status();
				$indexer = new WooCommerce_Product_Search_Indexer();
				$processable = $indexer->get_processable_count();
				$total       = $indexer->get_total_count();
				if ( $total > 0 ) {
					$pct = 100 - $processable / $total * 100;
				} else {
					$pct = 100;
				}
				$next_scheduled_datetime = '&mdash;';
				if ( $next_scheduled = WooCommerce_Product_Search_Worker::get_next_scheduled() ) {
					$next_scheduled_datetime = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_scheduled ) );
				}

				echo '<div class="wps-index-status-display-wrapper">';
				esc_html_e( 'Indexed', 'woocommerce-product-search' );
				echo '&nbsp;';
				echo '<div id="wps-index-status-display" style="display:inline-block;padding:0 0.31em 0 1.618em;">';
				printf( '%.2f', $pct );
				echo '</div>';
				echo '%';

				echo '&nbsp;&nbsp;';

				echo '<div style="display:inline-block;padding:0 0.62em 0 1.618em;">';
				echo '&#91;&nbsp;';
				printf( '<div title="%s" id="wps-index-status-total" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Total', 'woocommerce-product-search' ) );
				echo esc_html( $total );
				echo '</div>';
				echo '&nbsp;&#47;&nbsp;';
				printf( '<div title="%s" id="wps-index-status-processable" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Remaining', 'woocommerce-product-search' ) );
				echo esc_html( $processable );
				echo '</div>';
				echo '&nbsp;&#93;';
				echo '</div>';

				echo '&nbsp;&nbsp;';

				echo '<div style="display:inline-block;padding:0 0.62em 0 1.618em;">';
				echo '&#91;&nbsp;';
				printf( '<div title="%s" id="wps-index-status-next-scheduled" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Next indexing cycle schedule', 'woocommerce-product-search' ) );
				echo esc_html( $next_scheduled_datetime );
				echo '</div>';
				echo '&nbsp;&#93;';
				echo '</div>';

				echo '</div>';

				$error = WooCommerce_Product_Search_Worker::cron_test();
				if ( $error === null ) {
					echo '<p>';
					esc_html_e( 'Scheduled tasks (cron) seem to be working and the indexer should process products automatically.', 'woocommerce-product-search' );
					echo '</p>';
				} else {
					echo '<div class="wps-cron-error">';
					echo '<p>';
					esc_html_e( 'Scheduled tasks (cron) seem to be failing:', 'woocommerce-product-search' );
					echo '</p>';
					echo '<p>';
					echo '<code>';
					esc_html_e( $error->get_error_message() );
					echo '</code>';
					echo '</p>';
					echo '<p>';
					echo ' ';
					esc_html_e( 'If the index is not completing automatically, click the "Run" button to run the indexer once.', 'woocommerce-product-search' );
					echo ' ';
					esc_html_e( 'Repeat if necessary until all products have been indexed.', 'woocommerce-product-search' );
					echo '</p>';
					echo '</div>';
				}

				$js_nonce = wp_create_nonce( 'wps-index-js' );

				echo '<h5>';
				esc_html_e( 'Indexer', 'woocommerce-product-search' );
				echo '</h5>';

				$work_cycle      = isset( $options[WooCommerce_Product_Search_Worker::WORK_CYCLE] ) ? $options[WooCommerce_Product_Search_Worker::WORK_CYCLE] : WooCommerce_Product_Search_Worker::get_work_cycle_default();
				$idle_cycle      = isset( $options[WooCommerce_Product_Search_Worker::IDLE_CYCLE] ) ? $options[WooCommerce_Product_Search_Worker::IDLE_CYCLE] : WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT;
				$index_per_cycle = isset( $options[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) ? $options[WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] : WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT;
				$index_order     = isset( $options[WooCommerce_Product_Search_Indexer::INDEX_ORDER] ) ? $options[WooCommerce_Product_Search_Indexer::INDEX_ORDER] : WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT;

				$can_control = current_user_can( self::INDEXER_CONTROL_CAPABILITY );

				echo '<table>';

				echo '<tr>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Worker::WORK_CYCLE ),
					esc_attr__( 'The indexer will process unindexed entries periodically every indicated number of seconds, while there are unprocessed entries.', 'woocommerce-product-search' )
				);
				esc_html_e( 'Work Cycle', 'woocommerce-product-search' );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d" %s/>',
					esc_attr( WooCommerce_Product_Search_Worker::WORK_CYCLE ),
					esc_attr( $work_cycle ),
					esc_attr( WooCommerce_Product_Search_Worker::get_work_cycle_default() ),
					( $can_control ? '' : ' readonly="readonly" ' )
				);
				echo '&nbsp;';
				esc_html_e( 'seconds', 'woocommerce-product-search' );
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Worker::IDLE_CYCLE ),
					esc_attr__( 'The indexer will check for new entries periodically every indicated number of seconds, once all entries have been indexed.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Idle Cycle', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%s" %s/>',
					esc_attr( WooCommerce_Product_Search_Worker::IDLE_CYCLE ),
					esc_attr( $idle_cycle ),
					esc_attr( WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT ),
					( $can_control ? '' : ' readonly="readonly" ' )
				);
				echo '&nbsp;';
				esc_html_e( 'seconds', 'woocommerce-product-search' );
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE ),
					esc_attr__( 'The indexer will try to process as many entries on each work cycle.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Process', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d" %s/>',
					esc_attr( WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE ),
					esc_attr( $index_per_cycle ),
					WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT,
					( $can_control ? '' : ' readonly="readonly" ' )
				);
				echo '&nbsp;';
				esc_html_e( 'entries', 'woocommerce-product-search' );
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>';
				printf(
					'<label for="%s" title="%s">',
					esc_attr( WooCommerce_Product_Search_Indexer::INDEX_ORDER ),
					esc_attr__( 'The indexer will process entries first as indicated.', 'woocommerce-product-search' )
				);
				echo esc_html( __( 'Order', 'woocommerce-product-search' ) );
				echo '</label>';
				echo '</td>';
				echo '<td>';
				printf(
					'<select name="%s" title="%s" %s>',
					esc_attr( WooCommerce_Product_Search_Indexer::INDEX_ORDER ),
					esc_attr__( 'Index entries in this order', 'woocommerce-product-search' ),
					( $can_control ? '' : ' disabled="disabled" ' )
				);
				$index_orders = array(
					WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENT => __( 'Most recent first', 'woocommerce-product-search' ),
					WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENT => __( 'Least recent first', 'woocommerce-product-search' ),
					WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENTLY_MODIFIED => __( 'Most recently modified first', 'woocommerce-product-search' ),
					WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENTLY_MODIFIED => __( 'Least recently modified first', 'woocommerce-product-search' )
				);
				foreach( $index_orders as $index_order_key => $index_order_label ) {
					printf( '<option value="%s" %s>%s</option>',
						esc_attr( $index_order_key ),
						$index_order === $index_order_key ? ' selected="selected" ' : '',
						esc_html( $index_order_label )
					);
				}
				echo '</select>';
				echo '</td>';
				echo '</tr>';

				echo '</table>';

				if ( $can_control ) {
					echo '<p>';

					printf(
						'<input class="button wps-index-start-button" type="button" id="wps_index_start" name="wps_index_start" value="%s" title="%s" %s/>',
						esc_attr__( 'Start', 'woocommerce-product-search' ),
						esc_attr__( 'Start indexing &hellip;', 'woocommerce-product-search' ),
						$status ? ' disabled="disabled" ' : ''
					);

					echo '&emsp;';

					printf(
						'<input class="button wps-index-stop-button" type="button" id="wps_index_stop" name="wps_index_stop" value="%s" title="%s" %s/>',
						esc_attr__( 'Stop', 'woocommerce-product-search' ),
						esc_attr__( 'Stop indexing &hellip;', 'woocommerce-product-search' ),
						$status ? '' : ' disabled="disabled" '
					);

					echo '&emsp;';

					printf(
						'<input class="button wps-index-rebuild-button" type="button" id="wps_index_rebuild" name="wps_index_rebuild" value="%s" title="%s"/>',
						esc_attr__( 'Rebuild', 'woocommerce-product-search' ),
						esc_attr__( 'Completely rebuild the index &hellip;', 'woocommerce-product-search' )
					);

					echo '</p>';

				}

				echo '<div id="wps-index-status"></div>';
				echo '<div id="wps-index-update"></div>';
				echo '<div id="wps-index-blinker"></div>';

				if ( $can_control ) {
					echo '<p>';
					esc_html_e( 'If necessary, you can trigger the indexer manually here &hellip;', 'woocommerce-product-search' );
					echo ' ';
					printf(
						'<input class="button wps-index-run-button" type="button" id="wps_index_run" name="wps_index_run" value="%s" title="%s"/>',
						esc_attr__( 'Run', 'woocommerce-product-search' ),
						esc_attr__( 'Run the indexer once &hellip;', 'woocommerce-product-search' )
					);
					echo '</p>';
				}

				echo '<script type="text/javascript">';
				echo 'document.addEventListener( "DOMContentLoaded", function() {';
				echo 'if ( typeof jQuery !== "undefined" ) {';

				echo 'if ( typeof wpsIndexer !== "undefined" ) {';
				printf( 'wpsIndexer.msg_starting = "%s";', esc_html__( 'The indexer is starting &hellip;', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_started = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_stopping = "%s";', esc_html__( 'The indexer is stopping &hellip;', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_stopped = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_rebuilding = "%s";', esc_html__( 'The index is being rebuilt &hellip;', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_rebuilt = "%s";', esc_html__( 'The index has been cleared and is being rebuilt.', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_run = "%s";', esc_html__( 'The indexer is running once &hellip;', 'woocommerce-produce-search' ) );
				printf( 'wpsIndexer.msg_ran = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
				echo '}';

				echo 'jQuery("#wps_index_start").click(function(e){';
				echo 'e.stopPropagation();';
				echo 'jQuery("#wps-index-status").html("");';
				echo 'jQuery("#wps-index-update").html("");';
				echo 'jQuery(this).prop( "disabled", true );';

				printf(
					'wpsIndexer.start("%s","%s");',
					add_query_arg(
						array(
							'action' => 'wps_indexer',
							'cmd'    => 'start',
							'nonce'  => $js_nonce
						),
						admin_url( 'admin-ajax.php' )
					),
					self::get_admin_section_url( self::SECTION_INDEX )
				);

				echo '});';

				echo 'jQuery("#wps_index_stop").click(function(e){';
				echo 'e.stopPropagation();';
				echo 'jQuery("#wps-index-status").html("");';
				echo 'jQuery("#wps-index-update").html("");';
				echo 'jQuery(this).prop( "disabled", true );';

				printf(
					'wpsIndexer.stop("%s","%s");',
					add_query_arg(
						array(
							'action' => 'wps_indexer',
							'cmd'    => 'stop',
							'nonce'  => $js_nonce
						),
						admin_url( 'admin-ajax.php' )
					),
					self::get_admin_section_url( self::SECTION_INDEX )
				);

				echo '});';

				echo 'jQuery("#wps_index_rebuild").click(function(e){';
				echo 'e.stopPropagation();';
				printf(
					'if ( confirm("%s") ) {',
					esc_html__( 'Are you sure that you wish to rebuild the index completely?', 'woocommerce-product-search' ) .
					' ' .
					esc_html__( 'Please note that this will remove all indexes and create them from scratch.', 'woocommerce-product-search' ) .
					' ' .
					esc_html__( 'Especially for sites with a large product base, it is highly recommended to run this process only during low traffic hours.', 'woocommerce-product-search' )
				);
				echo 'jQuery("#wps-index-status").html("");';
				echo 'jQuery("#wps-index-update").html("");';
				echo 'jQuery(this).prop( "disabled", true );';

				printf(
					'wpsIndexer.rebuild("%s","%s");',
					add_query_arg(
						array(
							'action' => 'wps_indexer',
							'cmd'    => 'rebuild',
							'nonce'  => $js_nonce
						),
						admin_url( 'admin-ajax.php' )
					),
					self::get_admin_section_url( self::SECTION_INDEX )
				);
				echo '} else {';
				echo 'e.preventDefault();';
				echo '}';

				echo '});';

				echo 'jQuery("#wps_index_run").click(function(e){';
				echo 'e.stopPropagation();';
				echo 'jQuery("#wps-index-status").html("");';
				echo 'jQuery("#wps-index-update").html("");';
				echo 'jQuery(this).prop( "disabled", true );';

				printf(
					'wpsIndexer.run_once("%s","%s");',
					add_query_arg(
						array(
							'action' => 'wps_indexer',
							'cmd'    => 'run_once',
							'nonce'  => $js_nonce
						),
						admin_url( 'admin-ajax.php' )
						),
					self::get_admin_section_url( self::SECTION_INDEX )
					);

				echo '});';

				printf(
					'wpsIndexerStatus.url = "%s";',
					add_query_arg(
						array(
							'action' => 'wps_indexer',
							'cmd'    => 'status',
							'nonce'  => $js_nonce
						),
						admin_url( 'admin-ajax.php' )
					)
				);

				printf(
					'wpsIndexerStatus.cron = "%s";',
					add_query_arg(
						array(
							'doing_wp_cron' => 1
						),
						site_url( 'wp-cron.php' )
					)
				);

				echo '}';
				echo '} );';
				echo '</script>';

				break;

			case self::SECTION_ASSISTANT :

				if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {

					global $wp_registered_sidebars;

					echo '<h3>';
					esc_html_e( 'Assistant', 'woocommerce-product-search' );
					echo '</h3>';

					echo '<p>';
					esc_html_e( 'This assistant helps you to add suitable live filters to your store.', 'woocommerce-product-search' );
					echo '</p>';

					echo '<p>';
					esc_html_e( 'We recommend to use a live search filter, one category filter, one for prices and one for each product attribute that is used to offer product variations.', 'woocommerce-product-search' );
					echo ' ';
					esc_html_e( 'If it makes sense within the context of your store, you can also add a filter for product tags.', 'woocommerce-product-search' );
					echo ' ';
					echo wp_kses(
						sprintf(
							__( 'You can add all of them now or just some, you can always come back here or simply add them in the <a href="%s">Widgets</a> section yourself.', 'woocommerce-product-search' ),
							esc_url( admin_url( 'widgets.php' ) )
						),
						array( 'a' => array( 'href' => array() ) )
					);
					echo '</p>';
					echo '<p>';
					esc_html_e( 'To get started quickly, simply choose the sidebar that seems most suitable.', 'woocommerce-product-search' );
					echo ' ';
					esc_html_e( 'It will contain the widgets that your customers use to filter the products in your store.', 'woocommerce-product-search' );
					echo ' ';
					esc_html_e( 'This assistant suggests all that are not already present in a sidebar.', 'woocommerce-product-search' );
					echo ' ';
					esc_html_e( 'The widgets will only display on relevant shop pages by default.', 'woocommerce-product-search' );
					echo ' ';
					echo wp_kses(
						sprintf(
							__( 'You can customize and <a href="%s">control</a> them further as you will find them in the <a href="%s">Widgets</a> section.', 'woocommerce-product-search' ),
							esc_url( 'https://wordpress.org/plugins/widgets-control/' ),
							esc_url( admin_url( 'widgets.php' ) )
						),
						array( 'a' => array( 'href' => array() ) )
					);
					echo '</p>';

					$sidebars_widgets = get_option( 'sidebars_widgets', array() );
					$sidebars_index = array();
					$filter_widget_id_bases = array(
						'woocommerce_product_search_filter_widget',
						'woocommerce_product_search_filter_attribute_widget',
						'woocommerce_product_search_filter_category_widget',
						'woocommerce_product_search_filter_price_widget',
						'woocommerce_product_search_filter_rating_widget',
						'woocommerce_product_search_filter_sale_widget',
						'woocommerce_product_search_filter_tag_widget',
						'woocommerce_product_search_filter_reset_widget'
					);
					$existing_filter_widgets = array();
					foreach( $filter_widget_id_bases as $id_base ) {
						$widget_instances = get_option( 'widget_' . $id_base, array() );
						foreach( $widget_instances as $widget_number => $widget_instance ) {
							$sidebar_id = null;
							if ( is_int( $widget_number ) ) {
								foreach ( $sidebars_widgets as $_sidebar_id => $widget_ids ) {
									if ( is_array( $widget_ids ) ) {
										if ( in_array( $id_base . '-' . $widget_number, $widget_ids ) ) {
											$sidebar_id = $_sidebar_id;
										}
									}
								}
								if ( $sidebar_id !== null && $sidebar_id !== 'wp_inactive_widgets' ) {
									switch( $id_base ) {
										case 'woocommerce_product_search_filter_attribute_widget' :
											if ( !empty( $widget_instance['taxonomy'] ) ) {
												$existing_filter_widgets[] = $id_base . '-' . $widget_instance['taxonomy'];
											} else {
												$existing_filter_widgets[] = $id_base;
											}
											break;
										default :
											$existing_filter_widgets[] = $id_base;
									}
									$sidebars_index[$existing_filter_widgets[count( $existing_filter_widgets ) - 1]][] = $sidebar_id;
								}
							}
						}
					}

					echo '<h4>';
					esc_html_e( '1. Select the sidebar', 'woocommerce-product-search' );
					echo '</h4>';

					echo '<table>';
					echo '<tr>';
					echo '<td>';
					printf(
						'<label for="%s" title="%s">',
						esc_attr( 'wps-assistant-sidebar-id' ),
						esc_attr__( 'Select the sidebar to which the assistant should add filter widgets.', 'woocommerce-product-search' )
					);
					echo esc_html( __( 'Sidebar', 'woocommerce-product-search' ) );
					echo '</label>';
					echo '</td>';
					echo '<td>';
					printf(
						'<select name="%s" title="%s">',
						esc_attr( 'wps-assistant-sidebar-id' ),
						esc_attr__( 'Add filter widgets to the selected sidebar.', 'woocommerce-product-search' )
					);
					if ( !empty( $wp_registered_sidebars ) && is_array( $wp_registered_sidebars ) ) {
						foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
							printf(
								'<option value="%s">%s</option>',
								esc_attr( $sidebar_id ),
								esc_html( $sidebar['name'] )
							);
						}
					}
					echo '</select>';
					echo '</td>';
					echo '</tr>';
					echo '</table>';

					echo '<h4>';
					esc_html_e( '2. Choose the filters to add', 'woocommerce-product-search' );
					echo '</h4>';

					echo '<p>';
					esc_html_e( 'The assistant will propose to add unused filters and select them for you by default.', 'woocommerce-product-search' );
					echo '</p>';

					echo '<table class="wps-filter-widget-entries">';

					$widgets = array(
						new WooCommerce_Product_Search_Filter_Widget(),
						new WooCommerce_Product_Search_Filter_Price_Widget(),
						new WooCommerce_Product_Search_Filter_Category_Widget(),
						new WooCommerce_Product_Search_Filter_Rating_Widget(),
						new WooCommerce_Product_Search_Filter_Sale_Widget(),
						new WooCommerce_Product_Search_Filter_Tag_Widget(),
						new WooCommerce_Product_Search_Filter_Reset_Widget()
					);
					$i = 0;
					foreach( $widgets as $widget ) {
						printf(
							'<tr class="%s" style="padding-bottom: 1em; display: block;">',
							$widget instanceof WooCommerce_Product_Search_Filter_Reset_Widget ? 'wps-filter-reset-widget-entry' : ''
						);

						echo '<td style="vertical-align:top">';
						printf(
							'<input class="wps-assistant" name="wps-assistant-widget-ids[]" type="checkbox" value="%s" %s id="assistant-widget-%d"/>',
							esc_attr( $widget->id_base ),
							!in_array( $widget->id_base, $existing_filter_widgets ) ? ' checked="checked" ' : '',
							$i
						);
						echo '</td>';

						echo '<td style="vertical-align:top">';
						printf( '<label title="%s" for="assistant-widget-%d">',
							!empty( $widget->widget_options['description'] ) ? esc_attr__( $widget->widget_options['description'] ) : '',
							$i
						);
						echo '<div>';
						echo '<strong>';
						echo esc_html( $widget->name );
						echo '</strong>';
						echo '</div>';
						if ( isset( $sidebars_index[$widget->id_base] ) ) {
							echo '<div class="description">';
							esc_html_e( 'Present in &hellip; ', 'woocommerce-product-search' );
							$sidebar_names = array();
							foreach( $sidebars_index[$widget->id_base] as $sidebar_id ) {
								if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
									$sidebar_names[] = $wp_registered_sidebars[$sidebar_id]['name'];
								}
							}
							echo '&nbsp;';
							echo esc_html( implode( ', ', $sidebar_names ) );
							echo '</div>';
						}
						echo '</label>';
						echo '</td>';

						echo '</tr>';
						$i++;
					}

					$product_attribute_taxonomies = wc_get_attribute_taxonomy_names();
					foreach( $product_attribute_taxonomies as $product_attribute_taxonomy ) {
						if ( $taxonomy = get_taxonomy( $product_attribute_taxonomy ) ) {
							$widget          = new WooCommerce_Product_Search_Filter_Attribute_Widget();
							$title           = !empty( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : $taxonomy->label;
							$widget_settings = $widget->update( array( 'title' => $title, 'taxonomy' => $taxonomy->name ), array() );

							echo '<tr style="padding-bottom: 1em; display: block;">';
							echo '<td style="vertical-align:top">';
							printf(
								'<input class="wps-assistant" name="wps-assistant-widget-ids[]" type="checkbox" value="%s-%s" %s id="assistant-widget-%d"/>',
								esc_attr( $widget->id_base ), esc_attr( $taxonomy->name ),
								!in_array( $widget->id_base . '-' . $taxonomy->name, $existing_filter_widgets ) ? ' checked="checked" ' : '',
								$i
							);
							echo '</td>';

							echo '<td style="vertical-align:top">';
							printf( '<label title="%s" for="assistant-widget-%d">',
								!empty( $widget->widget_options['description'] ) ? esc_attr__( $widget->widget_options['description'] ) : '',
								$i
							);
							echo '<div>';
							echo '<strong>';
							echo esc_html( $widget->name );
							echo '&nbsp;&mdash;&nbsp;';
							echo esc_html( $title );
							echo '</strong>';
							echo '</div>';
							if ( isset( $sidebars_index[$widget->id_base . '-' . $taxonomy->name] ) ) {
								echo '<div class="description">';
								esc_html_e( 'Present in &hellip; ', 'woocommerce-product-search' );
								$sidebar_names = array();
								foreach( $sidebars_index[$widget->id_base . '-' . $taxonomy->name] as $sidebar_id ) {
									if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
										$sidebar_names[] = $wp_registered_sidebars[$sidebar_id]['name'];
									}
								}
								echo '&nbsp;';
								echo esc_html( implode( ', ', $sidebar_names ) );
								echo '</div>';
							}
							echo '</label>';
							echo '</td>';
							echo '</tr>';
						}
						$i++;
					}
					echo '</table>';

					echo '<script type="text/javascript">';
					echo 'document.addEventListener( "DOMContentLoaded", function() {';
					echo 'if ( typeof jQuery !== "undefined" ) {';

					echo 'jQuery(".wps-filter-reset-widget-entry").appendTo(".wps-filter-widget-entries");';

					echo 'jQuery("#run-assistant-confirm").prop("disabled",jQuery("input.wps-assistant:checked").length === 0);';
					echo 'jQuery("input.wps-assistant").change(function(){';
					echo 'jQuery("#run-assistant-confirm").prop("disabled",jQuery("input.wps-assistant:checked").length === 0);';
					echo '});';

					echo 'jQuery("#run-assistant-confirm").click(function(e){';
					echo 'e.stopPropagation();';
					echo 'if ( jQuery("input.wps-assistant:checked").length > 0 ) {';
					printf(
						'if ( confirm("%s") ) {',
						esc_html__( 'Add the selected filters to the chosen sidebar?', 'woocommerce-product-search' )
					);
					echo '} else {';
					echo 'e.preventDefault();';
					echo '}';
					echo '} else {';
					echo 'e.preventDefault();';
					echo '}';
					echo '});';
					echo '}';
					echo '} );';
					echo '</script>';

				}
			break;

			case self::SECTION_WELCOME :
				WooCommerce_Product_Search_Admin_Notice::admin_notices_welcome( array( 'class' => '', 'epilogue' => false ) );
				break;
		}

		global $hide_save_button;
		$hide_save_button = true;

		wp_nonce_field( 'set', self::NONCE );
		wp_nonce_field( 'woocommerce-settings' );
		echo '<p class="submit">';
		switch ( $current_section ) {
			case self::SECTION_WELCOME :
				break;
			case self::SECTION_ASSISTANT :
				echo '<input id="run-assistant-confirm" class="button button-primary" type="submit" name="submit" value="' . esc_attr( __( 'Add selected', 'woocommerce-product-search' ) ) . '"/>';
				break;
			default :
				echo '<input class="button button-primary woocommerce-save-button" type="submit" name="submit" value="' . esc_attr( __( 'Save changes', 'woocommerce-product-search' ) ) . '"/>';
		}
		echo '</p>';
		echo '</div>';

		echo '<input type="hidden" name="save" value="1" />';

		echo '</form>';

		echo '</div>';

	}

	/**
	 * Adds plugin links.
	 *
	 * @param array $links with additional links
	 */
	public static function admin_settings_link( $links ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$settings_url = self::get_admin_section_url( self::SECTION_GENERAL );
			$current_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$show_welcome_url = wp_nonce_url( add_query_arg( WooCommerce_Product_Search_Admin_Notice::SHOW_WELCOME_NOTICE, true, $current_url ), 'show', 'wps_notice' );
			$links[] = '<a href="' . esc_url( $show_welcome_url ) . '">' . esc_html__( 'Welcome', 'woocommerce-product-search' ) . '</a>';
			$links[] = '<a style="font-weight:bold" href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'woocommerce-product-search' ) . '</a>';
			$links[] = '<a style="font-weight:bold" href="' . esc_url( admin_url( 'admin.php?page=wc-reports&tab=search' ) ) . '">' . esc_html__( 'Reports', 'woocommerce-product-search' ) . '</a>';
		}
		return $links;
	}

	/**
	 * Adds links to documentation and support to the plugin's row meta.
	 *
	 * @param array $plugin_meta plugin row meta entries
	 * @param string $plugin_file path to the plugin file - relative to the plugins directory
	 * @param array $plugin_data plugin data entries
	 * @param string $status current status of the plugin
	 *
	 * @return array[string]
	 */
	public static function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $plugin_file == plugin_basename( WOO_PS_FILE ) ) {
			$plugin_meta[] = '<a style="font-weight:bold" href="https://docs.woocommerce.com/document/woocommerce-product-search/">' . esc_html__( 'Documentation', 'woocommerce-product-search' ) . '</a>';
			$plugin_meta[] = '<a style="font-weight:bold" href="https://woocommerce.com/my-account/create-a-ticket/">' . esc_html__( 'Support', 'woocommerce-product-search' ) . '</a>';
		}
		return $plugin_meta;
	}

	/**
	 * Prints a warning when data is deleted on deactivation.
	 *
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $status
	 */
	public static function after_plugin_row( $plugin_file, $plugin_data, $status ) {
		$options = get_option( 'woocommerce-product-search', array() );
		if ( $plugin_file == plugin_basename( WOO_PS_FILE ) ) {
			$delete_data         = isset( $options[ WooCommerce_Product_Search::DELETE_DATA] ) ? $options[ WooCommerce_Product_Search::DELETE_DATA] : false;
			$delete_network_data = isset( $options[ WooCommerce_Product_Search::NETWORK_DELETE_DATA] ) ? $options[ WooCommerce_Product_Search::NETWORK_DELETE_DATA] : false;
			if (
				( is_plugin_active( $plugin_file ) && $delete_data && current_user_can( 'install_plugins' ) ) ||
				( is_plugin_active_for_network( $plugin_file ) && $delete_network_data  && current_user_can( 'manage_network_plugins' ) )
			) {
				echo '<tr class="active">';
				echo '<td>&nbsp;</td>';
				echo '<td colspan="2">';
				echo '<div style="border: 2px solid #dc3232; padding: 1em">';
				echo '<p>';
				echo '<strong>';
				echo esc_html__( 'Warning!', 'woocommerce-product-search' );
				echo '</strong>';
				echo '</p>';
				echo '<p>';
				echo esc_html__( 'The WooCommerce Product Search plugin is configured to delete its data on deactivation.', 'woocommerce-product-search' );
				echo '</p>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
			}
		}
	}
}
WooCommerce_Product_Search_Admin::init();
