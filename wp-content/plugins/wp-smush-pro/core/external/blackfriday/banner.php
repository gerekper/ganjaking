<?php
/**
 * WPMUDEV Black Friday common module
 *
 * Used by wordpress.org free plugins only to show Black Friday deal on admin dashboard.
 *
 * @since   1.0
 * @author  WPMUDEV
 * @package WPMUDEV\BlackFriday
 */

namespace WPMUDEV\BlackFriday;

if ( ! class_exists( __NAMESPACE__ . '\\Banner' ) ) {
	/**
	 * Class Load.
	 *
	 * @since    1.0
	 * @package  WPMUDEV\BlackFriday\Banner
	 */
	class Banner {

		/**
		 * Version number.
		 *
		 * @since 1.0
		 * @var string $version
		 */
		private $version = '1.0.0';

		/**
		 * SUI version number (in class format).
		 *
		 * @since 1.0
		 * @var string $sui_version
		 */
		private $sui_version = '2-12-13';

		/**
		 * Option name to store data.
		 *
		 * @since 1.0
		 * @var string $option_name
		 */
		protected $option_name = 'wpmudev_black_friday_flag';

		/**
		 * Nonce.
		 *
		 * @since 1.0
		 * @var string $nonce
		 */
		protected $nonce = 'wpmudev-bf-common';

		/**
		 * Constants to be used by plugins.
		 */
		const SMUSH = 0;
		const FORMINATOR = 10;
		const HUMMIGNBIRD = 20;
		const HUSTLE = 30;
		const DEFENDER = 40;
		const SMARTCRAWL = 50;
		const BRANDA = 60;
		const BEEHIVE = 70;

		/**
		 * Boolean that holds a notification printed state.
		 *
		 * @since 1.0
		 * @var bool
		 */
		private static $printed = false;

		/**
		 * The plugin id. Passed via data attributes to js.
		 *
		 * @since 1.0
		 * @var array
		 */
		private $labels;

		/**
		 * The plugin utm link. Passed via data attributes to js.
		 *
		 * @since 1.0
		 * @var string
		 */
		private $utm;

		/**
		 * Plugin priority to be used as admin_notices hook priority.
		 *
		 * @since 1.0
		 * @var int
		 */
		private $priority = 0;

		/**
		 * Construct handler class.
		 *
		 * @since 1.0
		 *
		 * @param array  $labels   Banner labels.
		 * @param string $utm_link UTM link.
		 * @param int    $priority Priority.
		 *
		 * @return void
		 */
		public function __construct( $labels, $utm_link, $priority = 10 ) {
			if ( empty( $labels ) || empty( $utm_link ) ) {
				return;
			}

			$this->labels = wp_parse_args(
				$labels,
				array(
					'close'       => 'Close',
					'get_deal'    => 'Get deal',
					'intro'       => 'Black Friday offer for WP businesses and agencies',
					'off'         => 'Off',
					'title'       => 'Everything you need to run your WP business for',
					'discount'    => '83.5',
					'price'       => '3000',
					'description' => 'WPMU DEV’s all-in-one platform gives you all the Pro tools and support you need to run and grow a web development business. Trusted by over 50,000 web developers. Limited deals available.',
				)
			);

			$this->utm      = $utm_link;
			$this->priority = $priority;

			// Current screen actions.
			add_action( 'current_screen', array( $this, 'current_screen_actions' ) );

			// Ajax request to dismiss.
			add_action( 'wp_ajax_wpmudev_bf_dismiss', array( $this, 'dismiss_banner' ) );
		}

		/**
		 * Perform actions for current screen.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function current_screen_actions() {
			if ( ! $this->can_load() ) {
				return;
			}

			// Enqueue assets.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			// Render dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'dashboard_notice' ), $this->priority );
		}

		/**
		 * Enqueues scripts and styles for the banner.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			wp_enqueue_style(
				'wpmudev-bf-common',
				plugin_dir_url( __FILE__ ) . 'assets/css/banner.min.css',
				array(),
				$this->version
			);
		}

		/**
		 * Loads the Dashboard Notice
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function dashboard_notice() {
			if ( self::$printed ) {
				return;
			}

			self::$printed = true;

			wp_enqueue_script(
				'wpmudev-bf-common',
				plugin_dir_url( __FILE__ ) . '/assets/js/banner.min.js',
				array( 'wp-dom-ready' ),
				$this->version,
				true
			);

			wp_localize_script(
				'wpmudev-bf-common',
				'wpmudevBFBanner',
				array(
					'nonce'  => wp_create_nonce( $this->nonce ),
					'labels' => $this->labels,
					'utm'    => $this->utm,
				)
			);

			echo '<div class="sui-' . esc_html( $this->sui_version ) . '">
					<div class="sui-wrap">
						<div id="wpmudev-bf-common-notice"></div>
					</div>
				</div>';
		}

		/**
		 * Checks if plugin's Black Friday deal can be loaded.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function can_load() {
			if (
				! $this->is_admin_dash() ||
				! current_user_can( 'delete_users' ) ||
				$this->event_expired() ||
				$this->dashboard_plugin_installed() ||
				$this->banner_shown_previously()
			) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if current page is admin dashboard.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function is_admin_dash() {
			if ( is_network_admin() || is_main_site() ) {
				return function_exists( 'get_current_screen' ) && in_array( get_current_screen()->id, array( 'dashboard', 'dashboard-network' ), true );
			}

			return false;
		}

		/**
		 * Checks if offer has expired.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function event_expired() {
			$current_date = apply_filters( 'wpmudev_blackfriday_current_date', 'd-m-Y' );
			$start_date   = apply_filters( 'wpmudev_blackfriday_start_date', '21-11-2022' );
			$expire_date  = apply_filters( 'wpmudev_blackfriday_expire_date', '29-11-2022' );

			// Expires on 29 Nov 2022.
			return (
				date_create( date_i18n( $current_date ) )->getTimestamp() < date_create( date_i18n( $start_date ) )->getTimestamp() ||
				date_create( date_i18n( $current_date ) )->getTimestamp() >= date_create( date_i18n( $expire_date ) )->getTimestamp()
			);
		}

		/**
		 * Checks if Dashboard plugin is installed.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function dashboard_plugin_installed() {
			return class_exists( 'WPMUDEV_Dashboard' );
		}

		/**
		 * Checks if notice has been already shown.
		 *
		 * @since 1.0
		 *
		 * @return bool
		 */
		public function banner_shown_previously() {
			return get_site_option( $this->option_name );
		}

		/**
		 * Ajax request handler for banner dismissal.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function dismiss_banner() {
			// Verify nonce.
			check_ajax_referer( $this->nonce, 'nonce' );

			// Dismiss notice.
			update_site_option( $this->option_name, true );

			wp_send_json_success( array( 'success' => true ) );
		}
	}
}
