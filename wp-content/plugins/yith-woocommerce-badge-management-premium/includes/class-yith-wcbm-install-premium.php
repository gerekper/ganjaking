<?php
/**
 * Class YITH_WCBM_Install
 * Installation related functions and actions.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 * @since   2.0.0
 */

if ( ! class_exists( 'YITH_WCBM_Install_Premium' ) ) {
	/**
	 * YITH_WCBM_Install class.
	 *
	 * @since 2.0
	 */
	class YITH_WCBM_Install_Premium extends YITH_WCBM_Install {

		/**
		 * The updates to fire.
		 *
		 * @var array
		 */
		protected $db_updates = array(
			'2.0.0' => array(
				'yith_wcbm_update_200_badges_meta_premium',
				'yith_wcbm_update_200_badge_rules',
				'yith_wcbm_update_200_badges_settings',
				'yith_wcbm_update_200_products_badge_meta_premium',
			),
		);

		/**
		 * The version option.
		 */
		const VERSION_OPTION = 'yith_woocommerce_badge_version_premium';

		/**
		 * The version option.
		 */
		const DB_VERSION_OPTION = 'yith_wcbm_db_version_premium';

		/**
		 * Install WC.
		 */
		public function install() {

			// Check if we are not already running this routine.
			if ( 'yes' === get_transient( 'yith_wcbm_installing' ) ) {
				return;
			}

			set_transient( 'yith_wcbm_installing', 'yes', MINUTE_IN_SECONDS * 10 );
			if ( ! defined( 'YITH_WCBM_INSTALLING' ) ) {
				define( 'YITH_WCBM_INSTALLING', true );
			}
			$this->create_tables();
			$this->update_version();
			$this->maybe_update_db_version();

			delete_transient( 'yith_wcbm_installing' );

			do_action( 'yith_wcbm_installed' );
		}

		/**
		 * Create tables
		 */
		private function create_tables() {
			YITH_WCBM_DB::create_db_tables();
		}

		/**
		 * Update version to current.
		 */
		protected function update_version() {
			delete_option( static::VERSION_OPTION );
			add_option( static::VERSION_OPTION, YITH_WCBM_VERSION );
		}

		/**
		 * Run an update callback when triggered by ActionScheduler.
		 *
		 * @param string $callback Callback name.
		 */
		public function run_update_callback( $callback ) {
			include_once YITH_WCBM_INCLUDES_PATH . '/functions.yith-wcbm-update-premium.php';

			if ( is_callable( $callback ) ) {
				static::run_update_callback_start( $callback );
				$result = (bool) call_user_func( $callback );
				static::run_update_callback_end( $callback, $result );
			}
		}

	}
}

if ( ! function_exists( 'yith_wcbm_install' ) ) {
	/**
	 * Return the Install Class Instance
	 *
	 * @return YITH_WCBM_Install
	 */
	function yith_wcbm_install() {
		return YITH_WCBM_Install::get_instance();
	}
}
