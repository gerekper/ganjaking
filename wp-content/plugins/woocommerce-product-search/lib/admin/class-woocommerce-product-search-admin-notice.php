<?php
/**
 * class-woocommerce-product-search-admin-notice.php
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
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notices
 */
class WooCommerce_Product_Search_Admin_Notice {

	/**
	 * Time mark.
	 *
	 * @var string
	 */
	const INIT_TIME = 'woocommerce-product-search-init-time';

	/**
	 * Used to show the welcome notice and reset the hide/remind flags.
	 *
	 * @var string
	 */
	const SHOW_WELCOME_NOTICE = 'woocommerce-product-search-show-welcome-notice';

	/**
	 * Used to store user meta and hide the welcome notice.
	 *
	 * @var string
	 */
	const HIDE_WELCOME_NOTICE = 'woocommerce-product-search-hide-welcome-notice';

	/**
	 * Used to store user meta and hide the notice asking to review.
	 *
	 * @var string
	 */
	const HIDE_REVIEW_NOTICE = 'woocommerce-product-search-hide-review-notice';

	/**
	 * Used to check welcome next time.
	 *
	 * @var string
	 */
	const REMIND_WELCOME_NOTICE = 'woocommerce-product-search-remind-welcome-notice';

	/**
	 * Used to check notice next time.
	 *
	 * @var string
	 */
	const REMIND_LATER_NOTICE = 'woocommerce-product-search-remind-later-notice';

	/**
	 * The number of seconds in seven days, since init date to show the notice.
	 *
	 * @var int
	 */
	const SHOW_LAPSE = 604800;

	/**
	 * The number of seconds in one day, used to show the welcome notice later again.
	 *
	 * @var int
	 */
	const REMIND_WELCOME_LAPSE = 86400;

	/**
	 * The number of seconds in three days, used to show notice later again.
	 *
	 * @var int
	 */
	const REMIND_NOTICE_LAPSE = 259200;

	/**
	 * Used to confirm initiating the update process.
	 *
	 * @var string
	 */
	const CONFIRM_UPDATE = 'woocommerce-product-search-confirm-update';

	/**
	 * Adds actions.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__,'admin_init' ) );
	}

	/**
	 * Hooked on the admin_init action.
	 */
	public static function admin_init() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$user_id = get_current_user_id();

			$showing_welcome = false;

			if ( !empty( $_GET[self::SHOW_WELCOME_NOTICE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'show' ) ) {
				delete_user_meta( $user_id, self::HIDE_WELCOME_NOTICE );
				delete_user_meta( $user_id, self::REMIND_WELCOME_NOTICE );
			}

			if ( ! (
				isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' &&
				isset( $_GET['tab'] ) && $_GET['tab'] === 'product-search'
			) ) {

				if ( !empty( $_GET[self::HIDE_WELCOME_NOTICE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'hide' ) ) {
					add_user_meta( $user_id, self::HIDE_WELCOME_NOTICE, true );
				}
				if ( !empty( $_GET[self::REMIND_WELCOME_NOTICE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'later' ) ) {
					update_user_meta( $user_id, self::REMIND_WELCOME_NOTICE, time() + self::REMIND_WELCOME_LAPSE );
				}
				$hide_welcome_notice = get_user_meta( $user_id, self::HIDE_WELCOME_NOTICE, true );
				if ( empty( $hide_welcome_notice ) ) {
					$remind_welcome_notice = get_user_meta( $user_id, self::REMIND_WELCOME_NOTICE, true );
					if ( empty( $remind_welcome_notice ) || ( time() > $remind_welcome_notice ) ) {
						add_action( 'admin_notices', array( __CLASS__, 'admin_notices_welcome' ), 0 );
						$showing_welcome = true;
					}
				}
			}

			if ( !$showing_welcome ) {

				if ( !empty( $_GET[self::HIDE_REVIEW_NOTICE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'hide' ) ) {
					add_user_meta( $user_id, self::HIDE_REVIEW_NOTICE, true );
				}
				if ( !empty( $_GET[self::REMIND_LATER_NOTICE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'later' ) ) {
					update_user_meta( $user_id, self::REMIND_LATER_NOTICE, time() + self::REMIND_NOTICE_LAPSE );
				}
				$hide_review_notice = get_user_meta( $user_id, self::HIDE_REVIEW_NOTICE, true );
				if ( empty( $hide_review_notice ) ) {
					$d = time() - self::get_init_time();
					if ( $d >= self::SHOW_LAPSE ) {
						$remind_later_notice = get_user_meta( $user_id, self::REMIND_LATER_NOTICE, true );
						if ( empty( $remind_later_notice ) || ( time() > $remind_later_notice ) ) {
							add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
						}
					}
				}
			}

			if ( !empty( $_GET[self::CONFIRM_UPDATE] ) && isset( $_GET['wps_notice'] ) && wp_verify_nonce( $_GET['wps_notice'], 'confirm' ) ) {
				WooCommerce_Product_Search::schedule_db_update();
			} else {

				if ( WooCommerce_Product_Search::needs_db_update() && !WooCommerce_Product_Search::is_db_update_scheduled() ) {
					add_action( 'admin_notices', array( __CLASS__, 'update_notice' ), 0 );
				}
			}
		}
	}

	/**
	 * Initializes if necessary and returns the init time.
	 */
	public static function get_init_time() {
		$init_time = get_site_option( self::INIT_TIME, null );
		if ( $init_time === null ) {
			$init_time = time();
			add_site_option( self::INIT_TIME, $init_time );
		}
		return $init_time;
	}

	/**
	 * Adds the welcome notice.
	 */
	public static function admin_notices_welcome( $atts = null ) {

		global $woocommerce_product_search_welcome;

		if ( !isset( $woocommerce_product_search_welcome ) ) {
			$woocommerce_product_search_welcome = true;
		} else {
			return;
		}

		$class = is_array( $atts ) && isset( $atts['class'] ) ? $atts['class'] : null;
		$epilogue = is_array( $atts ) && isset( $atts['epilogue'] ) ? $atts['epilogue'] : true;

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$hide_url    = wp_nonce_url( add_query_arg( self::HIDE_WELCOME_NOTICE, true, $current_url ), 'hide', 'wps_notice' );
		$remind_url  = wp_nonce_url( add_query_arg( self::REMIND_WELCOME_NOTICE, true, $current_url ), 'later', 'wps_notice' );

		$output = '';

		$output .= '<style type="text/css">';
		$output .= 'div.updated.wps-welcome {';
		$output .= sprintf( 'background: url(%s) #fff no-repeat 8px 8px;', WOO_PS_PLUGIN_URL . '/images/woocommerce-product-search.png' );
		$output .= 'padding-left: 76px ! important;';
		$output .= 'background-size: 64px 64px;';
		$output .= 'border-color: #cc99c2 !important;';
		$output .= 'padding-bottom: 1em;';
		$output .= '}';
		$output .= '</style>';

		if ( $class === null ) {
			$output .= '<div class="updated wps-welcome">';
		} else {
			$output .= sprintf( '<div class="%s">', esc_attr( $class ) );
		}

		$output .= '<h2 style="font-size: 2em;">';
		$output .= __( 'Welcome to WooCommerce Product Search', 'woocommerce-product-search' );
		$output .= '</h2>';
		$output .= '<p style="font-size: 1.62em;">';
		$output .= __( 'Congrats on using the best <em>Search Experience</em> for WooCommerce!', 'woocommerce-product-search' );
		$output .= '</p>';

		$output .= '<table>';
		$output .= '<tr>';

		$output .= '<td style="vertical-align:top">';
		$output .= '<h3>';
		$output .= esc_html__( 'Documentation', 'woocommerce-product-search' );
		$output .= '</h3>';

		$output .= '<p>';
		$output .= wp_kses(
			sprintf(
				__( 'Please refer to the <a href="%s">WooCommerce Product Search</a> documentation pages for detailed information.', 'woocommerce-product-search' ),
				esc_url( 'https://docs.woocommerce.com/document/woocommerce-product-search/' )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= '</p>';
		$output .= '</td>';

		$output .= '<td style="vertical-align:top">';
		$output .= '<h3>';
		$output .= esc_html__( 'Support', 'woocommerce-product-search' );
		$output .= '</h3>';

		$output .= '<p>';
		$output .= wp_kses(
			sprintf(
				__( 'For further assistance with <a href="%1$s">WooCommerce Product Search</a>, please use the <a href="%2$s">helpdesk</a>.', 'woocommerce-product-search' ),
				esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' ),
				esc_url( 'https://woocommerce.com/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin' )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= ' ';
		$output .= esc_html__( 'We also welcome your suggestions through this channel.', 'woocommerce-product-search' );
		$output .= '</p>';
		$output .= '</td>';

		$output .= '<td style="vertical-align:top">';
		$output .= '<h3>';
		$output .= esc_html__( 'Security', 'woocommerce-product-search' );
		$output .= '</h3>';

		$output .= '<p>';
		$output .= esc_html__( 'Do not compromise on security!', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= wp_kses(
			sprintf(
				__( '<a href="%1$s">WooCommerce Product Search</a> is made available to you exclusively through <a href="%2$s">WooCommerce</a>.', 'woocommerce-product-search' ),
				esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' ),
				esc_url( 'https://woocommerce.com/?utm_source=helptab&utm_medium=product&utm_content=about&utm_campaign=woocommerceplugin' )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= ' ';
		$output .= wp_kses(
			sprintf(
				__( 'Please always make sure that you obtain or renew this official extension through the only trusted source at <a href="%s">WooCommerce Product Search</a>.', 'woocommerce-product-search' ),
				esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= '</p>';
		$output .= '</td>';
		$output .= '</tr>';
		$output .= '</table>';

		$output .= '<h2>';
		$output .= esc_html__( 'Getting started &hellip;', 'woocommerce-product-search' );
		$output .= '</h2>';

		$output .= '<p>';
		$output .= __( 'Your customers will love your store for this &mdash; it helps them to find the right products quickly.', 'woocommerce-product-search' );
		$output .= '</p>';

		$output .= '<ul style="list-style:outside;padding: 0.24em 1em">';

		$output .= '<li>';
		$output .= '<p>';
		$output .= esc_html__( 'The search engine is already indexing your products to optimize search results for your visitors.', 'woocommerce-product-search' );
		$output .= sprintf(
			'<span style="cursor:help" title="%s">&emsp;[?]</span>',
			esc_attr__( 'Depending on the amount of products in your store and the processing capabilities of your server, this process may take a few minutes or hours to complete.', 'woocommerce-product-search' ) .
			' ' .
			esc_attr__( 'It is designed to provide search results immediately, even while the indexing process continues in the background.', 'woocommerce-product-search' ) .
			' ' .
			esc_attr__( 'New and updated content is normally indexed within seconds.', 'woocommerce-product-search' )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '<li>';
		$output .= '<p>';
		$output .= esc_html__( 'Front-end and back-end searches are now optimized to provide the best search experience by default.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= esc_html__( 'The advanced Product Search Field will replace the standard product search field where possible.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= wp_kses(
			sprintf(
				__( 'You can adjust these settings in the <a href="%s">General</a> section.', 'woocommerce-product-search' ),
				esc_url( WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_GENERAL ) )
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '<li>';
		$output .= '<p>';
		$output .= wp_kses(
			sprintf(
				__( 'Live search statistics are available in the <a href="%s">Search</a> section of the reports.', 'woocommerce-product-search' ),
				esc_url( admin_url( 'admin.php?page=wc-reports&tab=search' ) )
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '<li>';
		$output .= '<p>';
		$output .= wp_kses(
			sprintf(
				__( 'Now you can use <a href="%s">Weights</a> to improve the relevance in product search results.', 'woocommerce-product-search' ),
				esc_url( WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_WEIGHTS ) )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= sprintf(
			'<span style="cursor:help" title="%s">&emsp;[?]</span>',
			esc_attr__( 'Enable the use of Weights to improve the relevance in product search results, based on matches in product titles, descriptions, contents, categories, tags, attributes and their SKU.', 'woocommerce-product-search' ) .
			' ' .
			esc_attr__( 'Weights can also be set for specific products and product categories.', 'woocommerce-product-search' )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '<li>';
		$output .= '<p>';
		$output .= '<strong>';
		$output .= __( 'Shall we make it easy for your customers and add live filters to your shop pages right now?', 'woocommerce-product-search' );
		$output .= '</strong>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			'<a class="button button-primary" href="%s">%s</a>',
			esc_url( WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_ASSISTANT ) ),
			__( 'Run the Assistant', 'woocommerce-product-search' )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '<li>';
		$output .= '<p>';
		$output .= esc_html__( 'There are plenty of useful features and we highly recommend to have a look at the documentation for details.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= wp_kses(
			sprintf(
				__( 'We\'re always here for you if you need help or have suggestions, just <a href="%s">ask</a>.', 'woocommerce-product-search' ),
				esc_url( 'https://woocommerce.com/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin' )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			'<a class="button" href="%s">%s</a>',
			esc_url( WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_GENERAL ) ),
			__( 'Review the Settings', 'woocommerce-product-search' )
		);
		$output .= '</p>';
		$output .= '</li>';

		$output .= '</ul>';

		$output .= '<p>';
		$output .= esc_html__( 'Have fun selling!', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= esc_html__( 'It just got easier &hellip; :)', 'woocommerce-product-search' );
		$output .= '</p>';

		if ( $epilogue ) {
			$output .= '<p>';
			$output .= sprintf(
				'<a class="button" href="%s">%s</a>',
				esc_url( $remind_url ),
				esc_html( __( 'Remind me later', 'woocommerce-product-search' ) )
			);
			$output .= '&emsp;';
			$output .= sprintf(
				'<a style="color:inherit;white-space:nowrap;" href="%s">%s</a>',
				esc_url( $hide_url ),
				esc_html( __( 'I\'ve got it, let\'s hide this', 'woocommerce-product-search' ) )
			);
			$output .= sprintf(
				'<span style="cursor:help" title="%s">&emsp;&hellip;</span>',
				esc_attr__( 'Don\'t worry, you can always get this back by clicking the Welcome link of the WooCommerce Product Search entry in the Plugins section.', 'woocommerce-product-search' )
			);
			$output .= '</p>';
		}

		$output .= '</div>';

		echo $output;
	}

	/**
	 * Adds the admin notice.
	 */
	public static function admin_notices() {

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$hide_url    = wp_nonce_url( add_query_arg( self::HIDE_REVIEW_NOTICE, true, $current_url ), 'hide', 'wps_notice' );
		$remind_url  = wp_nonce_url( add_query_arg( self::REMIND_LATER_NOTICE, true, $current_url ), 'later', 'wps_notice' );

		$output = '';

		$output .= '<style type="text/css">';
		$output .= 'div.wps-rating {';
		$output .= sprintf( 'background: url(%s) #fff no-repeat 8px 8px;', WOO_PS_PLUGIN_URL . '/images/woocommerce-product-search.png' );
		$output .= 'padding-left: 76px ! important;';
		$output .= 'background-size: 64px 64px;';
		$output .= '}';
		$output .= '</style>';

		$output .= '<div class="updated wps-rating">';
		$output .= '<p>';
		$output .= __( 'Many thanks for using <strong>WooCommerce Product Search</strong>!', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= __( 'Could you please take a moment and rate this extension?', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= sprintf(
			'<a style="color:inherit;white-space:nowrap;" href="%s">%s</a>',
			esc_url( $hide_url ),
			esc_html( __( 'I have already rated it', 'woocommerce-product-search' ) )
		);
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			'<a class="button button-primary" href="%s" target="_blank">%s</a>',
			esc_url( 'https://woocommerce.com/products/woocommerce-product-search/' ),
			__( 'Submit a rating', 'woocommerce-product-search' )
		);
		$output .= '&emsp;';
		$output .= sprintf(
			'<a class="button" href="%s">%s</a>',
			esc_url( $remind_url ),
			esc_html( __( 'Remind me later', 'woocommerce-product-search' ) )
		);
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'Your rating helps us to provide you with an excellent product and commit to continuous improvement.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= sprintf(
			__( 'If you would rate any aspect below 5 stars, please <a href="%s">let us know before</a> you submit your rating so we can improve it.', 'woocommerce-product-search' ),
			esc_url( 'https://woocommerce.com/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin' )
		);
		$output .= '</p>';
		$output .= '</div>';

		echo $output;
	}

	public static function update_notice() {
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$confirm_url = wp_nonce_url( add_query_arg( self::CONFIRM_UPDATE, true, $current_url ), 'confirm', 'wps_notice' );

		$output = '';

		$output .= '<style type="text/css">';
		$output .= 'div.wps-update {';
		$output .= sprintf( 'background: url(%s) #fff no-repeat 8px 8px;', WOO_PS_PLUGIN_URL . '/images/woocommerce-product-search.png' );
		$output .= 'padding-left: 76px ! important;';
		$output .= 'background-size: 64px 64px;';
		$output .= 'border-color: #7f54b3 !important;';
		$output .= 'padding-bottom: 1em;';
		$output .= '}';
		$output .= '.wps-update-warning {';
		$output .= 'float: right;';
		$output .= 'font-size: 32px;';
		$output .= 'background-color: #ffcc00;';
		$output .= 'padding: 8px;';
		$output .= 'border-radius: 8px;';
		$output .= 'position: relative;';
		$output .= 'top: 4px;';
		$output .= 'right: 4px;';
		$output .= 'margin: 4px;';
		$output .= '}';
		$output .= '</style>';

		$output .= '<div class="updated wps-update">';
		$output .= '<span class="wps-update-warning">&#9888;</span>';
		$output .= '<h2>';
		$output .= __( 'WooCommerce Product Search requires a Database Update &hellip;', 'woocommerce-product-search' );
		$output .= '</h2>';
		$output .= '<p>';
		$output .= __( 'The database update will run in the background and may take a while.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= __( 'During this process, product searches and filters may produce fewer or no results until the process is completed.', 'woocommerce-product-search' );
		$output .= ' ';
		$output .= __( 'We recommend to run the update during low traffic hours.', 'woocommerce-product-search' );
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			'<a class="button button-primary" href="%s">%s</a>',
			esc_url( $confirm_url ),
			__( 'Update', 'woocommerce-product-search' )
		);
		$output .= '</p>';
		$output .= '</div>';

		echo $output;
	}
}
WooCommerce_Product_Search_Admin_Notice::init();
