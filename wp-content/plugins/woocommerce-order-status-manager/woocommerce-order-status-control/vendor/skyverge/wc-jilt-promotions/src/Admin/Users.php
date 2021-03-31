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

namespace SkyVerge\WooCommerce\Jilt_Promotions\Admin;

use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Notices\Notice;

defined( 'ABSPATH' ) or exit;

/**
 * Handler for the notice shown when the merchant filters users by the Customer role.
 *
 * @since 1.1.0
 */
class Users extends Prompt {


	/** @var string the id associated with the message */
	private $customer_role_message_id = 'users-customers-role';

	/** @var string user page identifier */
	private $users_screen_id = 'users';

	/** @var string customer role to match with role parameter */
	private $customer_role = 'customer';


	/**
	 * Renders a Notice object if the users customer role message is enabled.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function add_admin_notices() {

		if ( Messages::is_message_enabled( $this->customer_role_message_id ) ) {

			$notice = new Notice();
			$notice->set_message_id( $this->customer_role_message_id );
			$notice->set_actions( [
				[
					'name'    => 'email-my-customers-learn-more',
					'label'   => __( 'Learn more', 'sv-wc-jilt-promotions' ),
					'url'     => 'https://www.skyverge.com/go/email-customers',
					'type'    => Notice::ACTION_TYPE_LINK
				],
				[
					'name'    => 'email-my-customers-cta',
					'label'   => __( 'Email my customers', 'sv-wc-jilt-promotions' ),
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON
				],
			] );
			$notice->set_title( __( 'Show your customers you care by keeping in touch!', 'sv-wc-jilt-promotions' ) );
			$notice->set_content( __( 'Use Jilt to send welcome emails, thank customers for purchases, and encourage lapsed customers to shop again.', 'sv-wc-jilt-promotions' ) );

			$notice->render();
		}
	}


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 1.1.0
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->customer_role_message_id ) ) {

			add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enable_users_customer_role_message' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ $this, 'add_admin_notices' ], 15 );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function enqueue_assets() {

		if ( Messages::is_message_enabled( $this->customer_role_message_id ) ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( Installation::INSTALL_SCRIPT_HANDLE );
		}
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @since 1.1.0
	 */
	protected function get_connection_redirect_args() {

		$redirect_args = [];

		if( Installation::get_jilt_installed_from() === $this->customer_role_message_id ) {
			$redirect_args['utm_term'] = $this->customer_role_message_id;
		}

		return $redirect_args;
	}


	/**
	 * A callback for the admin_enqueue_scripts action.
	 *
	 * Enables the customer role message, if the ID of the current screen is the Users screen ID and the role parameter is set to customer.
	 *
	 * @since 1.1.0
	 */
	public function maybe_enable_users_customer_role_message() {

		$current_screen = get_current_screen();

		// determines if the ID of the current screen is the Users screen ID
		$is_user_current_screen = isset( $current_screen->id ) && $current_screen->id === $this->users_screen_id;

		// determines if the role parameter is set to customer
		$is_role_parameter_customer = isset( $_GET['role'] ) && $_GET['role'] === $this->customer_role;

		if ( $is_user_current_screen && $is_role_parameter_customer ) {
			Messages::enable_message( $this->customer_role_message_id );
		}
	}


}
