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

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Settings.
 */
class WooCommerce_Product_Search_Admin extends WooCommerce_Product_Search_Admin_Base {

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {

		require_once 'settings/class-admin-settings.php';

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
		wp_register_script( 'wps-reports', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/reports.js' : '/js/reports.min.js' ), array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );

		if ( WPS_DEBUG_SCRIPTS ) {
			wp_enqueue_script( 'woocommerce-product-search-settings', trailingslashit( WOO_PS_PLUGIN_URL ) . 'js/settings.js', array( 'jquery', 'jquery-ui-sortable', 'wp-api-request' ), WOO_PS_PLUGIN_VERSION, true );
		} else {
			wp_enqueue_script( 'woocommerce-product-search-settings', trailingslashit( WOO_PS_PLUGIN_URL ) . 'js/settings.min.js', array( 'jquery', 'jquery-ui-sortable', 'wp-api-request' ), WOO_PS_PLUGIN_VERSION, true );
		}
		if ( WPS_DEBUG_STYLES ) {
			wp_enqueue_style( 'woocommerce-product-search-settings', trailingslashit( WOO_PS_PLUGIN_URL ) . 'css/settings.css', array(), WOO_PS_PLUGIN_VERSION );
		} else {
			wp_enqueue_style( 'woocommerce-product-search-settings', trailingslashit( WOO_PS_PLUGIN_URL ) . 'css/settings.min.css', array(), WOO_PS_PLUGIN_VERSION );
		}

		wp_localize_script(
			'woocommerce-product-search-settings',
			'woocommerce_product_search_settings',
			array(
				'number_of_cache_files' => _x( 'Number of cache files', 'woocommerce-product-search' ),
				'size_of_cache_files' => _x( 'Size of cache files', 'woocommerce-product-search' ),
				'x_of_y' => _x( '%1$s of %2$s', 'storage status display', 'woocommerce-product-search' ),
				'storage_space' => _x( 'Storage', 'storage status display', 'woocommerce-product-search' ),

				'free_of' => _x( '%1$s free of %2$s', 'storage status display', 'woocommerce-product-search' ),
				'free_storage_space' => _x( 'Free storage', 'storage status display', 'woocommerce-product-search' ),
				'minimum_free_storage_space' => _x( 'Minimum free storage space', 'storage status display', 'woocommerce-product-search' ),
				'warning' => _x( 'Warning', 'storage status display', 'woocommerce-product-search' ),
				'storage_space_exhausted' => _x( 'Storage space is exhausted', 'storage status display', 'woocommerce-product-search' ),
				'storage_space_info_unavailable' => _x( 'Storage space information is not available', 'storage status display', 'woocommerce-product-search' ),
				'interval' => 10000,
				'timeout' => 9000
			)
		);
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
		$output .= '*[id*="woocommerce_product_search_filter_stock_widget"] .widget-title,';
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
		$output .= 'html[dir="rtl"] *[id*="woocommerce_product_search_filter_stock_widget"] .widget-title,';
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
		$output .= '.widget-tpl *[id*="woocommerce_product_search_filter_stock_widget"] .widget-title,';
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
		$output .= 'html[dir="rtl"] .widget-tpl *[id*="woocommerce_product_search_filter_stock_widget"] .widget-title,';
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
					esc_url( 'https://woo.com/document/woocommerce-product-search/' )
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
					esc_url( 'https://woo.com/products/woocommerce-product-search/' ),
					esc_url( 'https://woo.com/my-account/contact-support/' )
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
					esc_url( 'https://woo.com/products/woocommerce-product-search/' )
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
					__( '<a href="%1$s">WooCommerce Product Search</a> is made available to you exclusively through <a href="%2$s">%3$s</a>.', 'woocommerce-product-search' ),
					esc_url( 'https://woo.com/products/woocommerce-product-search/' ),
					esc_url( 'https://woo.com/?utm_source=helptab&utm_medium=product&utm_content=about&utm_campaign=woocommerceplugin' ),
					esc_html( 'Woo' )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'Please always make sure that you obtain or renew this official extension through the only trusted source at <a href="%s">WooCommerce Product Search</a>.', 'woocommerce-product-search' ),
					esc_url( 'https://woo.com/products/woocommerce-product-search/' )
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
					esc_url( WooCommerce_Product_Search_Admin_Navigation::get_report_url( 'searches' ) )
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
			$content .= sprintf(
				/* translators: %s are automatically generated HTML elements which must be present */
				esc_html__(
					'Add live search and filters to your shop, using the %sblocks%s, %swidgets%s and %sshortcodes%s that come exclusively with the search engine.',
					'woocommerce-product-search'
				),
				sprintf( '<a href="%s">', esc_url( 'https://woo.com/document/woocommerce-product-search/blocks/' ) ),
				'</a>',
				sprintf( '<a href="%s">', esc_url( 'https://woo.com/document/woocommerce-product-search/widgets/' ) ),
				'</a>',
				sprintf( '<a href="%s">', esc_url( 'https://woo.com/document/woocommerce-product-search/shortcodes/' ) ),
				'</a>'
			);

			if ( self::uses_classic_widgets() ) {
				$content .= ' ';
				$content .= wp_kses(
					sprintf(
						__( 'You can use the <a href="%s">Assistant</a> to add filter widgets to your sidebars.', 'woocommerce-product-search' ),
						esc_url( self::get_admin_section_url( self::SECTION_ASSISTANT ) )
					),
					array( 'a' => array( 'href' => array() ) )
				);
			}
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
			$content .= esc_html__( 'Product Filter &ndash; Stock', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Filter &ndash; Tags', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '</ul>';

			$content .= '<p>';
			$content .= esc_html__( 'The advanced product search field that can replace the standard search is also available as a block, widget and shortcode:', 'woocommerce-product-search' );
			$content .= '</p>';
			$content .= '<ul>';
			$content .= '<li>';
			$content .= esc_html__( 'Product Search Field', 'woocommerce-product-search' );
			$content .= '</li>';
			$content .= '</ul>';

			$content .= '<p>';
			$content .= esc_html__( 'For ease of integration, these features are available as blocks, widgets, shortcodes and API functions.', 'woocommerce-product-search' );
			$content .= ' ';
			$content .= wp_kses(
				sprintf(
					__( 'Please refer to the <a href="%s">WooCommerce Product Search</a> documentation pages for detailed information.', 'woocommerce-product-search' ),
					esc_url( 'https://woo.com/document/woocommerce-product-search/' )
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
			$links[] = '<a style="font-weight:bold" href="' . esc_url(  WooCommerce_Product_Search_Admin_Navigation::get_report_url( 'searches' ) ) . '">' . esc_html__( 'Reports', 'woocommerce-product-search' ) . '</a>';
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
			$plugin_meta[] = '<a style="font-weight:bold" href="https://woo.com/document/woocommerce-product-search/">' . esc_html__( 'Documentation', 'woocommerce-product-search' ) . '</a>';
			$plugin_meta[] = '<a style="font-weight:bold" href="https://woo.com/my-account/contact-support/">' . esc_html__( 'Support', 'woocommerce-product-search' ) . '</a>';
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
		$settings = Settings::get_instance();
		if ( $plugin_file == plugin_basename( WOO_PS_FILE ) ) {
			$delete_data         = $settings->get( WooCommerce_Product_Search::DELETE_DATA, false );
			$delete_network_data = $settings->get( WooCommerce_Product_Search::NETWORK_DELETE_DATA, false );
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
