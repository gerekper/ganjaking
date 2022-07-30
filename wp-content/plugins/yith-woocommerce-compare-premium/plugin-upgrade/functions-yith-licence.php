<?php
/**
 * This file belongs to the YIT Plugin Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	/**
	 * Register the plugin when activated for onboarding process.
	 * Please note: use this function through register_activation_hook.
	 *
	 * @use activate_PLUGINNAME hook
	 */
	function yith_plugin_onboarding_registration_hook() {
		$plugin = str_replace( 'activate_', '', current_filter() );
		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) || apply_filters( "yith_licence_onboarding_registration_{$plugin}", true ) ) {

			$onboarding_queue = get_transient( 'yith_plugin_licence_onboarding_queue' );
			if ( empty( $onboarding_queue ) || ! is_array( $onboarding_queue ) ) {
				$onboarding_queue = array();
			}

			$onboarding_queue[] = $plugin;
			set_transient( 'yith_plugin_licence_onboarding_queue', $onboarding_queue, 30 * MINUTE_IN_SECONDS );
		}
	}
}
