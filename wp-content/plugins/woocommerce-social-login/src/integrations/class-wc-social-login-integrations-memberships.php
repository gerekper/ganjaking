<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Memberships integration handler.
 *
 * @since 2.5.0
 */
class WC_Social_Login_Integrations_Memberships {


	/** @var array helper to keep track of shown buttons per post */
	private $shown_buttons = array();

	/** @var bool whether buttons should be shown by setting option */
	private $showing_buttons;


	/**
	 * Integration handler constructor.
	 *
	 * @since 2.5.0
	 */
	public function __construct() {

		// add hooks to filter restriction messages and append Social Login buttons
		add_action( 'wp', array( $this, 'init') );

		// add Social Login settings to Memberships settings
		add_filter( 'wc_memberships_messages_settings', array( $this, 'add_restriction_messages_settings' ) );
	}


	/**
	 * Checks whether Social Login buttons should be appended to restriction messages.
	 *
	 * @since 2.5.0
	 *
	 * @return bool
	 */
	public function showing_social_login_buttons() {

		if ( null === $this->showing_buttons )  {
			$this->showing_buttons = 'yes' === get_option( 'wc_social_login_append_buttons_memberships_restriction_messages' );
		}

		return $this->showing_buttons;
	}


	/**
	 * Initializes integration.
	 *
	 * @internal
	 *
	 * @since 2.5.0
	 */
	public function init() {

		// only output buttons for logged out users and if the admin opted in
		if ( ! is_user_logged_in() && $this->showing_social_login_buttons() ) {

			$restriction_message_ids = \WC_Memberships_User_Messages::get_default_messages( false );

			foreach ( $restriction_message_ids as $restriction_message_id ) {

				/**
				 * Filters whether to output buttons for a specific Memberships restriction message.
				 *
				 * @since 2.5.0
				 *
				 * @param bool $output_social_login_buttons whether to output the buttons or not (default true)
				 * @param string $restriction_message_id the current message code
				 */
				$output_social_login_buttons = apply_filters( 'wc_social_login_add_buttons_memberships_restriction_message', true, $restriction_message_id );

				if ( (bool) $output_social_login_buttons ) {

					add_filter( "wc_memberships_{$restriction_message_id}", array( $this, 'add_social_login_buttons' ), 100, 2 );
				}
			}
		}
	}


	/**
	 * Adds Social Login settings to Memberships messages settings.
	 *
	 * @internal
	 *
	 * @since 2.5.0
	 *
	 * @param array $memberships_messages_settings associative array of settings
	 * @return array new settings
	 */
	public function add_restriction_messages_settings( array $memberships_messages_settings ) {

		$social_login_settings = array( array(
			'type'    => 'checkbox',
			'id'      => 'wc_social_login_append_buttons_memberships_restriction_messages',
			'name'    => __( 'Social Login Buttons', 'woocommerce-social-login' ),
			'desc'    => __( 'Add Social Login buttons to restriction messages for logged out users', 'woocommerce-social-login' ),
			'class'   => 'wc-social-login-setting js-select-edit-message-group',
			'default' => 'yes',
		) );

		array_splice( $memberships_messages_settings, 1, 0, $social_login_settings );

		return $memberships_messages_settings;
	}


	/**
	 * Adds social login buttons to a restriction message.
	 *
	 * Outputs buttons by appending them to the restriction message prompting for login.
	 * The return URL included in the buttons will redirect members that successfully log in to the content that was restricted.
	 *
	 * @internal
	 *
	 * @since 2.5.0
	 *
	 * @param string $message the original restriction message from Memberships
	 * @param array $args additional arguments used by the restriction message
	 * @return string HTML updated message
	 */
	public function add_social_login_buttons( $message, $args = array() ) {

		add_filter( 'pre_option_wc_social_login_text_non_checkout', '__return_empty_string' );

		$social_login_buttons = '';

		if ( ! empty( $args['post_id'] ) && is_numeric( $args['post_id'] ) ) {

			$post_id = (int) $args['post_id'];

			if ( $post_id > 0 && ! in_array( $post_id, $this->shown_buttons, true ) ) {

				$return_url = get_post_permalink( $args['post_id'] );

				ob_start();

				woocommerce_social_login_buttons( is_string( $return_url ) ? $return_url : null );

				$social_login_buttons  = ob_get_clean();
				$this->shown_buttons[] = $post_id;
			}
		}

		return $message . $social_login_buttons;
	}


}
