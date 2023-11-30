<?php

/**
 * This class defines all core code for the plugin.
 */

if (!class_exists('WCCS_Core')) {
	
	class WCCS_Core {
	
		
		public static $instance = false;
		
		public static function getInstance() {
			if (!self::$instance) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}

		private function __construct() {
			add_action('plugins_loaded', array( $this, 'plugin_dependencies' ));
			add_action('wp_footer', array( $this, 'wccs_call_refresh_cart_fragment' ));
		}
		
		public function plugin_dependencies() {
			/**
			 * Filter
			 * 
			 * @since 1.0.0
			 */
			if (function_exists('WC') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				$this->includes();
			} else {
				// show notice if WooCommerce plugin is not active
				add_action('admin_notices', array( $this, 'wccs_inactive_plugin_notice' ));
			}
		}

		/**
		 * Add Plugin Include Files
		 */
		private function includes() {
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-storage.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-switcher-widget.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-settings.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-ajax.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-cron.php';
			include_once WCCS_PLUGIN_PATH . '/includes/wccs-functions.php';
		}

		public function wccs_call_refresh_cart_fragment() {
			?>
			<script>
			setTimeout(() => {
				jQuery( document.body ).trigger( 'wc_fragment_refresh' );                
			}, 300);                
			</script>
			<?php
		}
		
		public function wccs_inactive_plugin_notice() {
			?>
			<div id="message" class="error"><p><?php printf(esc_html(__('WC Currency Switcher requires WooCommerce to be installed and active!', 'wccs'))); ?></p></div>
			<?php
		}
	}
	
}
