<?php
/**
 * Plugin Name: WooCommerce Splash Popup
 * Plugin URI: https://woocommerce.com/products/woocommerce-splash-popup/
 * Description: Allows store owners to display a lightbox popup on their web site containing page content based on whether the user is logged in or not, and whether the user is a customer or not. Once hidden the popup remains hidden via cookie.
 * Version: 1.5.0
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Text Domain: woocommerce-splash-popup
 * Domain Path: /languages
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.2
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.7
 * Woo: 187449:fa19ddbd06f96ba55e651d56418259be
 *
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-splash-popup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Splash_Popup\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_SPLASH_POPUP_FILE' ) ) {
	define( 'WC_SPLASH_POPUP_FILE', __FILE__ );
}

/**
 * Initialize plugin.
 */
function wc_splash_popup_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_splash_popup_woocommerce_deactivated' );
		return;
	}

	WC_Splash::instance();
}
add_action( 'plugins_loaded', 'wc_splash_popup_init' );

/**
 * WooCommerce Deactivated Notice.
 */
function wc_splash_popup_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Splash Popup requires %s to be installed and active.', 'woocommerce-splash-popup' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

if ( ! class_exists( 'WC_Splash' ) ) {

	/**
	 * WC_Splash class
	 */
	class WC_Splash extends \Themesquad\WC_Splash_Popup\Plugin {

		/**
		 * Constructor.
		 */
		protected function __construct() {
			parent::__construct();
			// Hooks.
			add_action( 'wp', array( $this, 'setup_wc_splash' ), 20 );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->current_tab = isset( $_GET['tab'] ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : 'general';

			// Use a high priority, to make sure this happens after the default tabs are registered.
			add_action( 'woocommerce_settings_tabs_array', array( $this, 'register_settings_tab' ), 30 );

			// Add the settings fields to each tab.
			add_action( 'woocommerce_splash_options_settings', array( $this, 'add_settings_fields' ), 10 );

			// Run these actions when generating the settings tabs.
			add_action( 'woocommerce_settings_tabs_wc_splash', array( $this, 'settings_tab_action' ), 10 );
			add_action( 'woocommerce_update_options_wc_splash', array( $this, 'save_settings' ), 10 );

			// Default options.
			add_option( 'wc_splash_force_display', 'no' );

			include_once __DIR__ . '/woocommerce-splash-popup-privacy.php';
		}

		/**
		 * Add settings tab.
		 *
		 * @param array $tabs List of tabs.
		 * @return array New list of tabs.
		 */
		public function register_settings_tab( $tabs ) {
			$tabs['wc_splash'] = esc_html__( 'Splash Popup', 'woocommerce-splash-popup' );
			return $tabs;
		}

		/**
		 * Splash popup settings tab handler.
		 *
		 * Do this when viewing our custom settings tab(s). One function for all tabs.
		 */
		public function settings_tab_action() {
			global $woocommerce_settings;

			// Determine the current tab in effect.
			$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_settings_tabs_' );

			do_action( 'woocommerce_splash_options_settings' );

			// Display settings for this tab (make sure to add the settings to the tab).
			woocommerce_admin_fields( $woocommerce_settings[ $current_tab ] );
		}

		/**
		 * Add settings fields for each tab.
		 */
		public function add_settings_fields() {
			global $woocommerce_settings;

			// Load the prepared form fields.
			$this->init_form_fields();

			if ( is_array( $this->fields ) ) {
				foreach ( $this->fields as $k => $v ) {
					$woocommerce_settings[ $k ] = $v;
				}
			}
		}

		/**
		 * Get the tab currently in view/processing.
		 *
		 * @param string $current_filter Current tab being ran.
		 * @param string $filter_base This text will be removed from the filter name.
		 * @return string
		 */
		public function get_tab_in_view( $current_filter, $filter_base ) {
			return str_replace( $filter_base, '', $current_filter );
		}

		/**
		 * Prepare form fields to be used in the various tabs.
		 */
		public function init_form_fields() {

			// Define settings.
			$this->fields['wc_splash'] = apply_filters(
				'woocommerce_wc_splash_settings_fields',
				array(
					array(
						'name' => esc_html__( 'Splash Popup Options', 'woocommerce-splash-popup' ),
						'type' => 'title',
						'id'   => 'wc_splash_options',
					),
					array(
						'title'    => esc_html__( 'Logged Out Users See', 'woocommerce-splash-popup' ),
						'desc'     => esc_html__( 'The content of this page will be displayed in your splash popup to logged out users.', 'woocommerce-splash-popup' ),
						'id'       => 'wc_splash_page_content_logged_out',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Logged In Users See', 'woocommerce-splash-popup' ),
						'desc'     => esc_html__( 'The content of this page will be displayed in your splash popup to logged in users.', 'woocommerce-splash-popup' ),
						'id'       => 'wc_splash_page_content_logged_in',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Logged In Customers See', 'woocommerce-splash-popup' ),
						'desc'     => esc_html__( 'The content of this page will be displayed in your splash popup to logged in customers.', 'woocommerce-splash-popup' ),
						'id'       => 'wc_splash_page_content_logged_in_customer',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),
					array(
						'name'    => esc_html__( 'Cookie Expiration (days)', 'woocommerce-splash-popup' ),
						'desc'    => esc_html__( 'Define how many consecutive days the popup will stay hidden for once closed.', 'woocommerce-splash-popup' ),
						'id'      => 'wc_splash_expiration',
						'default' => '30',
						'type'    => 'number',
					),
					array(
						'name' => esc_html__( 'Force Display', 'woocommerce-splash-popup' ),
						'desc' => esc_html__( 'Force the pop up to display regardless of the cookie (only recommended for testing purposes).', 'woocommerce-splash-popup' ),
						'id'   => 'wc_splash_force_display',
						'type' => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'wc_splash_options',
					),
				)
			);
		}

		/**
		 * Save settings in a single field in the database for each tab's fields (one field per tab).
		 */
		public function save_settings() {
			global $woocommerce_settings;

			// Make sure our settings fields are recognised.
			$this->add_settings_fields();

			$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_update_options_' );

			woocommerce_update_options( $woocommerce_settings[ $current_tab ] );
		}

		/**
		 * Initialize hooks.
		 */
		public function setup_wc_splash() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wc_splash_scripts' ) );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$is_xhr = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! wp_doing_ajax() && empty( $_GET['wc-ajax'] ) && ! $is_xhr ) {
				add_action( 'wp_footer', array( $this, 'wc_splash_content' ) );
			}
		}

		/**
		 * Get the id of the page to display in the popup.
		 *
		 * @return string ID of the page to display. Or empty string if not selected.
		 */
		public function get_content_id() {
			$content_id = '';
			// Customer orders query.
			$customer_orders = wc_get_orders(
				array(
					'customer_id'  => get_current_user_id(),
					'status' => array( 'wc-processing', 'wc-completed' ),
				)
			);

			$logged_out_content         = get_option( 'wc_splash_page_content_logged_out' );
			$logged_in_content          = get_option( 'wc_splash_page_content_logged_in' );
			$logged_in_customer_content = get_option( 'wc_splash_page_content_logged_in_customer' );

			// Define the splash content.
			if ( ! is_user_logged_in() ) {
				// If the user is not logged in.
				$content_id = get_option( 'wc_splash_page_content_logged_out' );
			} elseif ( is_user_logged_in() && ! $customer_orders && isset( $logged_in_content ) ) {
				// If the user is logged in but has no orders.
				$content_id = get_option( 'wc_splash_page_content_logged_in' );
			} elseif ( is_user_logged_in() && $customer_orders && isset( $logged_in_customer_content ) ) {
				// If the user is logged in and has orders.
				$content_id = get_option( 'wc_splash_page_content_logged_in_customer' );
			}
			return $content_id;
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @return void
		 */
		public function wc_splash_scripts() {
			$content_id = $this->get_content_id();
			if ( empty( $content_id ) ) {
				// Content is not set, nothing to do here.
				return;
			}

			wp_enqueue_script( 'jquery-cookie' );
			wp_enqueue_script( 'prettyPhoto' );
			wp_enqueue_style( 'woocommerce_prettyPhoto_css' );
			wp_enqueue_style( 'splash-styles', WC_SPLASH_POPUP_URL . 'assets/css/style.css', array(), WC_SPLASH_POPUP_VERSION );

			$expiration = get_option( 'wc_splash_expiration' );

			if ( ! isset( $expiration ) || '' === $expiration ) {
				$expiration = 30;
			}

			$js = 'jQuery(document).ready(function(){
					// Set the splash cookie as open by default
					if (jQuery.cookie( "splash" ) == null) {
						jQuery.cookie( "splash", "open", { expires: ' . $expiration . ', path: "/" } );
					}

					// Hide the splash content
					jQuery( "#splash-content, .reveal-splash" ).hide();

					// Open splash window via prettyPhoto
					jQuery( "a.reveal-splash, a.force-reveal-splash" ).prettyPhoto({
						social_tools: 	false,
						modal: 			true,
						theme: 			"pp_woocommerce pp_splash_popup",
						opacity: 		0.8,
						default_width: 	800,
						default_height: 600,
						horizontal_padding: 40,
						show_title: 	false,
						callback: 		function(){ jQuery.cookie( "splash", "closed", { expires: ' . $expiration . ', path: "/" } ); }, // Set the cookie when closed
					});

					// Set the cookie to hidden when a link is clicked.
					jQuery( "a" ).click( function() {
						jQuery.cookie( "splash", "closed", { expires: ' . $expiration . ', path: "/" } );
					});

					// Open the splash window automatically if cookie dicates it
					if (jQuery.cookie("splash") == "open") {
						jQuery(".reveal-splash").trigger("click");
					}
					// Or force it to open if specified
					jQuery(".force-reveal-splash").trigger("click");
				});';

			wc_enqueue_js( $js );
		}

		/**
		 * Filter dollar sign unicode and change it to a double unicode dollar sign.
		 * Pass all other currency symbols through.
		 *
		 * We need this filter beacause the post content is passed through
		 * String.protype.replace function, inside the PrettyPhoto plugin.
		 * The singular dollar sign which is often part of the price component
		 * for product shortcodes and blocks encodes an escape sequence which causes
		 * the replace function to fail on Safari. The filter detects the dollar sign
		 * and substitutes it as two dollar signs which are encoded by the
		 * replace function as a singular ( what we want ) dollar sign - correctly on
		 * all browsers.
		 *
		 * @param string $currency_symbol currency unicode symbol.
		 * @param string $currency        currency code.
		 *
		 * @return string Currency symobl unicode character.
		 */
		public function maybe_replace_currency_symbol( $currency_symbol, $currency ) {
			return '&#36;' === $currency_symbol ? $currency_symbol . $currency_symbol : $currency_symbol;
		}

		/**
		 * Content of the popup.
		 */
		public function wc_splash_content() {
			$current_user  = wp_get_current_user();
			$content_id    = $this->get_content_id();
			$splash_cookie = isset( $_COOKIE['splash'] ) ? wc_clean( wp_unslash( $_COOKIE['splash'] ) ) : 'open';
			$forcecookie   = get_option( 'wc_splash_force_display' );

			if ( ( 'open' === $splash_cookie || 'yes' === $forcecookie ) && ! empty( $content_id ) ) {
				$post = get_page( $content_id );
				// Only display the content if the cookie is set to 'open' or force display is enabled.
				?>
				<section id="splash-content" class="splash-content">
					<?php
					if ( ! is_user_logged_in() ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<h1 class="splash-title">' . apply_filters( 'the_title', get_the_title( $content_id ) ) . '</h1>';
					} else {
						echo '<h1 class="splash-title">' . esc_html__( 'Welcome back ', 'woocommerce-splash-popup' ) . esc_html( $current_user->display_name ) . '</h1>';
					}
						add_filter( 'woocommerce_currency_symbol', array( $this, 'maybe_replace_currency_symbol' ), 10, 2 );
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<div class="splash-content">' . apply_filters( 'the_content', $post->post_content ) . '</div>';
						remove_filter( 'woocommerce_currency_symbol', array( $this, 'maybe_replace_currency_symbol' ), 10 );
					?>
				</section>
				<a href="#splash-content" title="" class="
				<?php
				if ( 'yes' === $forcecookie ) {
					?>
					force-reveal-splash
					<?php
				} else {
					?>
					reveal-splash <?php } ?>"></a>
				<?php
			}
		}
	}
}
