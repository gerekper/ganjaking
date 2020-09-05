<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions\Handlers;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Jilt_Promotions\Admin\Emails;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;

/**
 * The base prompt handler.
 *
 * @since 1.1.0
 */
abstract class Prompt {


	/** @var string the source value for the connection arguments */
	const UTM_SOURCE = 'jilt-for-woocommerce';

	/** @var string the medium value for the connection arguments */
	const UTM_MEDIUM = 'oauth';

	/** @var string the campaign value for the connection arguments */
	const UTM_CAMPAIGN = 'wc-plugin-promo';

	/** @var string the content value for the connection arguments */
	const UTM_CONTENT = 'install-jilt-button';


	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		$this->add_hooks();
	}


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 1.1.0
	 */
	private function add_hooks() {

		if ( is_admin() && $this->should_display_prompt() ) {
			$this->add_prompt_hooks();
		}

		// add the connection redirect args if the plugin was installed from this prompt
		add_filter( 'wc_jilt_app_connection_redirect_args', [ $this, 'add_connection_redirect_args' ] );
	}


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * Subclasses can use this method to setup hooks only when the prompt should be displayed.
	 *
	 * @since 1.1.0
	 */
	abstract protected function add_prompt_hooks();


	/**
	 * Adds the connection redirect args if the plugin was installed from this prompt.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 *
	 * @param array $args redirect args
	 * @return array
	 */
	public function add_connection_redirect_args( $args ) {

		// use an empty UTM_TERM if the installed from value matches the default campaign identifier
		if ( self::UTM_CAMPAIGN === Installation::get_jilt_installed_from() ) {
			$new_args = [ 'utm_term' => '' ];
		} else {
			$new_args = $this->get_connection_redirect_args();
		}

		// add the connection redirect args if the utm_term is defined, even if it's empty
		if ( isset( $new_args['utm_term'] ) ) {

			$utm_campaign = isset( $new_args['utm_campaign'] ) ? $new_args['utm_campaign'] : self::UTM_CAMPAIGN;

			$args['utm_source']   = isset( $new_args['utm_source'] )   ? $new_args['utm_source']   : self::UTM_SOURCE;
			$args['utm_medium']   = isset( $new_args['utm_medium'] )   ? $new_args['utm_medium']   : self::UTM_MEDIUM;
			$args['utm_campaign'] = $utm_campaign;
			$args['utm_content']  = isset( $new_args['utm_content' ] ) ? $new_args['utm_content']  : self::UTM_CONTENT;
			$args['utm_term']     = str_replace( '_', '-', wc_clean( $new_args['utm_term'] ) );
			$args['partner']      = '1';
			$args['campaign']     = $utm_campaign;
		}

		return $args;
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * The returned array will be used only if it includes the utm_term arg.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	abstract protected function get_connection_redirect_args();


	/**
	 * Whether the Jilt install prompt should be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	protected function should_display_prompt() {

		$display = current_user_can( 'install_plugins' ) && ! $this->is_jilt_plugin_installed();

		$display = $display && ! wc_string_to_bool( get_user_meta( get_current_user_id(), Emails::META_KEY_HIDE_PROMPT, true ) );

		// do not display the prompt if the there is at least one message dismissed
		$display = $display && ! Messages::get_dismissed_messages();

		/**
		 * Filters whether the Jilt install prompt should be displayed.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $should_display whether the Jilt install prompt should be displayed
		 */
		return (bool) apply_filters( 'sv_wc_jilt_prompt_should_display', $display );
	}


	/**
	 * Whether Jilt is already installed.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	protected function is_jilt_plugin_installed() {

		return function_exists( 'wc_jilt' );
	}


}
