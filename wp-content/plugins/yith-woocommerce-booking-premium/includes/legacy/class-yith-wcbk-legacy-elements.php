<?php
/**
 * Class for showing backward switch menu since 3.0.0
 * This will be removed in 4.0.0
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Legacy_Elements' ) ) {
	/**
	 * YITH_WCBK_Legacy_Elements
	 *
	 * @since    3.0.0
	 */
	class YITH_WCBK_Legacy_Elements {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Constructor
		 */
		protected function __construct() {
			if ( current_user_can( 'manage_options' ) ) {
				$show_bookings_menu = 'yes' === get_option( 'yith-wcbk-legacy-show-bookings-menu-in-wp-menu', 'no' );
				if ( $show_bookings_menu ) {
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
					add_action( 'admin_menu', array( $this, 'show_legacy_menu' ) );
					add_action( 'admin_init', array( $this, 'remove_menu_handler' ) );
				}
			}
		}

		/**
		 * Maybe show the legacy menu
		 */
		public function show_legacy_menu() {
			add_menu_page(
				_x( 'Bookings', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				_x( 'Bookings', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'manage_options',
				'yith-wcbk-bookings-legacy-menu',
				array( $this, 'print_legacy_menu' ),
				'dashicons-calendar',
				30
			);
		}

		/**
		 * Print the legacy menu page
		 */
		public function print_legacy_menu() {
			$remove_url = add_query_arg( array( 'yith-wcbk-legacy-bookings-menu-remove' => wp_create_nonce( 'remove-legacy-bookings-menu' ) ) );
			?>
			<div class="yith-plugin-ui yith-wcbk-legacy-menu-notice">
				<div class="yith-wcbk-legacy-menu-notice__content">
					<?php
					yith_plugin_fw_get_component(
						array(
							'type'     => 'list-table-blank-state',
							'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
							'message'  => sprintf(
							// translators: 1. plugin version; 2. plugin name; 3. menu name with link.
								esc_html__( 'Since version %1$s of %2$s we moved all booking settings to a new panel that you can find in %3$s, so you can access to all plugin settings from there.', 'yith-booking-for-woocommerce' ),
								'<strong>3.0.0</strong>',
								'<strong>YITH Booking and Appointment for WooCommerce</strong>',
								'<strong>YITH > Booking</strong>'
							),
							'cta'      => array(
								'title' => __( 'Go to the new panel', 'yith-booking-for-woocommerce' ),
								'url'   => $remove_url,
							),
						),
						true
					);
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Remove menu and redirect to the new panel
		 */
		public function remove_menu_handler() {
			if ( ! empty( $_REQUEST['yith-wcbk-legacy-bookings-menu-remove'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['yith-wcbk-legacy-bookings-menu-remove'] ) ), 'remove-legacy-bookings-menu' ) ) {
				update_option( 'yith-wcbk-legacy-show-bookings-menu-in-wp-menu', 'no' );
				wp_safe_redirect( admin_url( 'admin.php?page=yith_wcbk_panel' ) );
				exit;
			}
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = ! ! $screen ? $screen->id : false;

			$css = '
				#toplevel_page_yith-wcbk-bookings-legacy-menu .dashicons-calendar:before {
					content: "\e00e";
					font-family: WooCommerce;
				}';

			if ( 'toplevel_page_yith-wcbk-bookings-legacy-menu' === $screen_id ) {
				wp_enqueue_style( 'yith-plugin-ui' );

				$css .= '
				.yith-wcbk-legacy-menu-notice {
					padding         : 60px 25px;
					margin          : 20px 20px 20px 0;
					background      : #fff;
					text-align      : center;
					display         : flex;
					align-items     : center;
					justify-content : center;
				}

				.yith-wcbk-legacy-menu-notice__content {
					max-width : 700px;
				}';
			}

			wp_add_inline_style( 'admin-menu', $css );
		}
	}
}
