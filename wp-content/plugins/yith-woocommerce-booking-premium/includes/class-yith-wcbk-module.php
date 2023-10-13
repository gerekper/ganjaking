<?php
/**
 * Class YITH_WCBK_Module
 * Handle a single module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Module' ) ) {
	/**
	 * YITH_WCBK_Module class.
	 *
	 * @since   4.0
	 */
	abstract class YITH_WCBK_Module {
		use YITH_WCBK_Multiple_Singleton_Trait;

		const KEY = '';

		/**
		 * YITH_WCBK_Module constructor.
		 */
		private function __construct() {
			if ( empty( static::KEY ) ) {
				$error = sprintf( 'Error: The class "%s" must define the constant KEY.', get_called_class() );
				yith_wcbk_error( $error );
				wp_die( esc_html( $error ) );
			}

			$this->maybe_add_action( 'yith_wcbk_loaded', array( $this, 'on_load' ), 0 );

			$this->maybe_add_action( "yith_wcbk_modules_module_{$this->get_key()}_activated", array( $this, 'on_activate' ) );
			$this->maybe_add_action( "yith_wcbk_modules_module_{$this->get_key()}_deactivated", array( $this, 'on_deactivate' ) );

			$this->maybe_add_action( 'yith_wcbk_register_post_type', array( $this, 'on_register_post_types' ) );
			$this->maybe_add_action( 'yith_wcbk_admin_post_type_handlers_loaded', array( $this, 'on_post_type_handlers_loaded' ) );

			$this->maybe_add_filter( 'yith_wcbk_styles', array( $this, 'filter_styles' ), 10, 2 );
			$this->maybe_add_filter( 'yith_wcbk_scripts', array( $this, 'filter_scripts' ), 10, 2 );
		}

		/**
		 * Maybe add action if the callback exists.
		 *
		 * @param string   $action        The action.
		 * @param callable $callback      The callback.
		 * @param int      $priority      Optional. The priority. Default 10.
		 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
		 */
		private function maybe_add_action( $action, $callback, $priority = 10, $accepted_args = 1 ) {
			is_callable( $callback ) && add_action( $action, $callback, $priority, $accepted_args );
		}

		/**
		 * Maybe add filter if the callback exists.
		 *
		 * @param string   $filter        The filter.
		 * @param callable $callback      The callback.
		 * @param int      $priority      Optional. The priority. Default 10.
		 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
		 */
		private function maybe_add_filter( $filter, $callback, $priority = 10, $accepted_args = 1 ) {
			is_callable( $callback ) && add_filter( $filter, $callback, $priority, $accepted_args );
		}

		/**
		 * Get the key.
		 *
		 * @return string
		 */
		public function get_key(): string {
			return static::KEY;
		}

		/**
		 * Get the active option.
		 *
		 * @return string
		 */
		protected function get_active_option(): string {
			return YITH_WCBK_Modules::get_module_active_option( $this->get_key() );
		}

		/**
		 * Get a path related to the module.
		 *
		 * @param string $path The path.
		 *
		 * @return string
		 */
		protected function get_path( string $path = '' ): string {
			return YITH_WCBK_Modules::get_module_path( $this->get_key(), $path );
		}

		/**
		 * Get a URL related to the module.
		 *
		 * @param string $url The URL.
		 *
		 * @return string
		 */
		protected function get_url( string $url = '' ): string {
			return YITH_WCBK_Modules::get_module_url( $this->get_key(), $url );
		}
	}
}
