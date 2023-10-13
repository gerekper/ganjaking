<?php
/**
 * Class YITH_WCBK_Theme
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Theme' ) ) {
	/**
	 * Class YITH_WCBK_Theme
	 * handle the YITH Booking theme install and update
	 */
	class YITH_WCBK_Theme {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Theme constructor.
		 */
		protected function __construct() {
			add_action( 'wp_ajax_yith_wcbk_theme_action', array( $this, 'handle_ajax_actions' ) );
		}

		/**
		 * Handle ajax actions.
		 */
		public function handle_ajax_actions() {
			check_admin_referer( 'yith-wcbk-theme-action', 'security' );

			$type = sanitize_text_field( wp_unslash( $_REQUEST['type'] ?? '' ) );
			$slug = sanitize_text_field( wp_unslash( $_REQUEST['slug'] ?? '' ) );
			$name = sanitize_text_field( wp_unslash( $_REQUEST['name'] ?? '' ) );

			$response = array(
				'success' => false,
				'error'   => __( 'Something went wrong! Please try again.', 'yith-booking-for-woocommerce' ),
			);

			switch ( $type ) {
				case 'network-enable':
					if ( $slug ) {
						$theme = wp_get_theme( $slug );
						if ( $theme->exists() ) {
							WP_Theme::network_enable_theme( $slug );

							$response = array(
								'success'     => true,
								'activateUrl' => $this->get_theme_activation_url( $slug ),
							);
						} else {
							$response = array(
								'success' => false,
								'error'   =>
									sprintf(
									// translators: %s is the theme name.
										__( 'Error: "%s" doesn\'t exist!', 'yith-booking-for-woocommerce' ),
										$name
									),
							);
						}
					}

					break;
			}

			wp_send_json( $response );
		}

		/**
		 * Check if the theme is installed.
		 *
		 * @param string $stylesheet The stylesheet.
		 *
		 * @return bool
		 */
		public function is_installed( $stylesheet ) {
			return ! ! $this->get_theme( $stylesheet );
		}

		/**
		 * Get themes.
		 *
		 * @return WP_Theme[]
		 */
		public function get_themes() {
			static $themes = null;
			if ( is_null( $themes ) ) {
				$themes = wp_get_themes();
			}

			return $themes;
		}

		/**
		 * Get theme by stylesheet.
		 *
		 * @param string $stylesheet The stylesheet.
		 *
		 * @return WP_Theme|false
		 */
		public function get_theme( $stylesheet ) {
			$themes = $this->get_themes();

			return $themes[ $stylesheet ] ?? false;
		}

		/**
		 * Get theme activation URL.
		 *
		 * @param string $stylesheet Theme stylesheet.
		 *
		 * @return string
		 */
		public function get_theme_activation_url( string $stylesheet ): string {
			return add_query_arg(
				array(
					'action'     => 'activate',
					'_wpnonce'   => wp_create_nonce( 'switch-theme_' . $stylesheet ),
					'stylesheet' => $stylesheet,
				),
				admin_url( 'themes.php' )
			);
		}

		/**
		 * Is the theme active?
		 *
		 * @param string $stylesheet Theme stylesheet.
		 *
		 * @return bool
		 */
		public function is_active( $stylesheet ) {
			return in_array( $stylesheet, array( $this->get_current_theme_stylesheet(), $this->get_current_parent_theme_stylesheet() ), true );
		}

		/**
		 * Get the active theme information.
		 *
		 * @return WP_Theme
		 */
		public function get_current_theme() {
			static $theme = null;
			if ( is_null( $theme ) ) {
				$theme = wp_get_theme();
			}

			return $theme;
		}

		/**
		 * Retrieve the current theme name
		 *
		 * @return string
		 */
		public function get_current_theme_stylesheet() {
			return $this->get_current_theme()->get_stylesheet();
		}

		/**
		 * Retrieve the parent theme name.
		 *
		 * @return string
		 */
		private function get_current_parent_theme_stylesheet() {
			$theme = $this->get_current_theme();
			if ( $theme->parent() ) {
				$theme = $theme->parent();
			}

			return $theme->get_stylesheet();
		}
	}
}
