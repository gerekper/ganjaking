<?php
/**
 * Deprecated hooks and filters
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Legacy
 * @version 3.0.24
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Deprecated_Hooks' ) ) {
	/**
	 * Class that manages deprecated hooks and filters
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Deprecated_Hooks {

		/**
		 * List of deprecated hooks
		 *
		 * @var array
		 */
		private $deprecated_hooks = array(
			'yith_wcwl_browse_wishlist_label' => 'yith-wcwl-browse-wishlist-label',
			'yith_wcwl_share_title'           => 'plugin_text',
		);

		/**
		 * List of deprecated hooks
		 *
		 * @var array
		 */
		private $deprecated_version = array(
			'yith-wcwl-browse-wishlist-label' => '3.0.24',
			'plugin_text'                     => '3.9.0',
		);

		/**
		 * Constructor.
		 */
		public function __construct() {
			$new_hooks = array_keys( $this->deprecated_hooks );
			array_walk( $new_hooks, array( $this, 'hook_in' ) );
		}

		/**
		 * Hook into the new hook so we can handle deprecated hooks once fired.
		 *
		 * @param string $hook_name Hook name.
		 */
		public function hook_in( $hook_name ) {
			add_filter( $hook_name, array( $this, 'maybe_handle_deprecated_hook' ), -1000, 8 );
		}

		/**
		 * If the hook is Deprecated, call the old hooks here.
		 */
		public function maybe_handle_deprecated_hook() {
			$new_hook          = current_filter();
			$old_hook          = isset( $this->deprecated_hooks[ $new_hook ] ) ? $this->deprecated_hooks[ $new_hook ] : false;
			$new_callback_args = func_get_args();
			$return_value      = $new_callback_args[0];

			if ( $old_hook && has_action( $old_hook ) ) {
				$this->display_notice( $old_hook, $new_hook );
				$return_value = apply_filters_ref_array( $old_hook, $new_callback_args );
			}

			return $return_value;
		}

		/**
		 * Display a deprecated notice for old hooks.
		 *
		 * @param string $old_hook Old hook.
		 * @param string $new_hook New hook.
		 */
		protected function display_notice( $old_hook, $new_hook ) {
			$deprecated_version = isset( $this->deprecated_version[ $old_hook ] ) ? $this->deprecated_version[ $old_hook ] : YITH_WCWL_Frontend()->version;

			wc_deprecated_hook( esc_html( $old_hook ), esc_html( $deprecated_version ), esc_html( $new_hook ) );
		}
	}
}

return new YITH_WCWL_Deprecated_Hooks();
