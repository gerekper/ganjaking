<?php
/**
 * WordPress Admin
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

namespace SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers;

use SkyVerge\WordPress\Plugin_Admin\Package;

defined( 'ABSPATH' ) or exit;

/**
 * The Support Requests controller class.
 *
 * @since 1.0.0
 */
class Support_Requests {


	/** @var string route namespace */
	protected $namespace = 'skyverge/v1';

	/** @var string route */
	protected $rest_route = 'support-requests';


	/**
	 * Register’s the controller’s routes.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace, "/{$this->rest_route}", [
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
					'args'                => $this->get_item_schema(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}


	/**
	 * Gets the item schema.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$schema = [
			'replyTo'         => [
				'description' => __( 'The e-mail address the support team will reply to', 'sv-wordpress-plugin-admin' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'plugin'          => [
				'description' => __( 'The plugin name', 'sv-wordpress-plugin-admin' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'subject'         => [
				'description' => __( 'The subject', 'sv-wordpress-plugin-admin' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'message'         => [
				'description' => __( 'The message', 'sv-wordpress-plugin-admin' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'createDebugUser' => [
				'description' => __( 'Whether or not to create a debug user', 'sv-wordpress-plugin-admin' ),
				'type'        => 'bool',
				'context'     => [ 'view', 'edit' ],
			],
		];

		return $schema;
	}


	/**
	 * Checks if the user has permission to update items.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function update_item_permissions_check() {

		return current_user_can( 'manage_woocommerce' );
	}


	/**
	 * Sends the support request.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {

		$request_data = json_decode( $request->get_body(), JSON_OBJECT_AS_ARRAY );

		$required_fields = [
			'replyTo',
			'plugin',
			'subject',
			'message',
		];

		foreach ( $required_fields as $required_field ) {

			if ( empty( $request_data[ $required_field ] ) ) {
				return new \WP_Error( 'required_field_missing', __( 'A required field is missing or empty', 'sv-wordpress-plugin-admin' ), [ 'field' => $required_field ] );
			}
		}

		$reply_to          = sanitize_email( $request_data['replyTo'] );
		$plugin_name       = sanitize_text_field( $request_data['plugin'] );
		$request_subject   = sanitize_text_field( $request_data['subject'] );
		$request_message   = sanitize_textarea_field( $request_data['message'] );
		$create_debug_user = ! empty( $request_data['createDebugUser'] );

		// define template variables
		$customer_name    = $this->get_customer_name( $reply_to );
		$plugin_version   = $this->get_plugin_version( $plugin_name );
		$support_end_date = $this->get_subscription_end_date( $plugin_name );

		$data = [
			'ticket'               => [
				'subject'     => $request_subject,
				'description' => $request_message,
			],
			'customer'             => [
				'name'  => $customer_name,
				'email' => $reply_to,
			],
			'plugin'               => [
				'name'             => $plugin_name,
				'version'          => $plugin_version,
				'support_end_date' => $support_end_date,
			],
			'system_status_report' => WC()->api->get_endpoint_data( '/wc/v3/system_status' ),
		];

		if ( $create_debug_user ) {

			$debug_user = $this->create_or_get_debug_user();

			if ( false !== $debug_user ) {

				$data['support_user'] = [
					'user_id' => $debug_user->ID,
				];

				$password_reset_key = get_password_reset_key( $debug_user );
				$debug_user_login   = $debug_user->user_login;

				if ( ! is_wp_error( $password_reset_key ) ) {

					// need to set this variable because it is used by the template
					$password_reset_url = network_site_url( "wp-login.php?action=rp&key=$password_reset_key&login=" . rawurlencode( $debug_user_login ), 'login' );

					$data['support_user']['password_reset_url'] = $password_reset_url;
				}
			}
		}

		$json_data = json_encode( $data, JSON_PRETTY_PRINT );

		ob_start();

		?>
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<pre>
<?php echo $json_data; ?>
</pre>
</body>
</html>
		<?php

		$email_body = ob_get_clean();

		$headers = [
			'Content-type'   => 'text/html',
			'Request-source' => 'SkyVerge Dashboard',
		];

		add_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

		wp_mail( 'incoming@skyver.ge', 'Support Request from the SkyVerge Dashboard', $email_body, $headers );

		remove_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

		$response = [
			'replyTo'         => $reply_to,
			'plugin'          => $plugin_name,
			'subject'         => $request_subject,
			'message'         => $request_message,
			'createDebugUser' => $create_debug_user,
			'debugUserId'     => ! empty( $debug_user ) ? $debug_user->ID : '',
		];

		return rest_ensure_response( $response );
	}


	/**
	 * Creates a new admin user (or gets the existing one) and returns it.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_User|false
	 */
	private function create_or_get_debug_user() {

		// look for the debug user by login
		$debug_user = get_user_by( 'login', 'skyverge' );

		if ( false === $debug_user ) {

			// look for the debug user by email
			$debug_user = get_user_by( 'email', 'support@skyverge.com' );
		}

		if ( false === $debug_user ) {

			// create the user
			$debug_user_id = wp_create_user( 'skyverge', wp_generate_password(), 'support@skyverge.com' );

			if ( ! empty( $debug_user_id ) ) {

				$debug_user = get_user_by( 'id', $debug_user_id );
			}
		}

		// make sure the user is admin
		if ( false !== $debug_user ) {

			$debug_user->add_role( 'administrator' );
		}

		return $debug_user;
	}


	/**
	 * Tries to get the customer name, first by looking for a user with the same e-mail,
	 * and later by getting the name of the logged in user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $reply_to reply to e-mail address
	 * @return string
	 */
	private function get_customer_name( $reply_to ) {

		$customer_name = '';

		$user = get_user_by_email( $reply_to );

		if ( empty( $user ) ) {

			// try to get name from current WP user
			$user_id = get_current_user_id();
			$user    = get_user_by( 'id', $user_id );
		}

		if ( $user instanceof \WP_User ) {

			$first_name    = $user->get( 'first_name' );
			$last_name     = $user->get( 'last_name' );
			$customer_name = implode( ' ', [ $first_name, $last_name ] );

			if ( empty( $customer_name ) ) {
				// fallback to user name, if first and last name are not set
				$customer_name = $user->user_nicename;
			}
		}

		return $customer_name;
	}


	/**
	 * Gets plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name plugin name
	 * @return string
	 */
	private function get_plugin_version( $plugin_name ) {

		$plugins = wp_list_filter( \WC_Helper::get_local_woo_plugins(), [ 'Name' => $plugin_name ] );
		if ( ! empty( $plugins ) ) {
			$plugin = array_shift( $plugins );
		}

		return ! empty( $plugin['Version'] ) ? $plugin['Version'] : '';
	}


	/**
	 * Gets plugin subscription end date.
	 *
	 * @see https://github.com/skyverge/support-bot/blob/3f35161b724530e7e8143a28995a4c7e8ce451a4/app/services/support_connector/woocommerce_email.rb#L110
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name plugin name
	 * @return string date in Y-m-d format, 'no subscription' or 'lifetime'
	 */
	private function get_subscription_end_date( $plugin_name ) {

		$end_date = 'no subscription';

		$subscriptions = wp_list_filter( \WC_Helper::get_subscriptions(), [ 'product_name' => $plugin_name ] );
		if ( ! empty( $subscriptions ) ) {
			$subscription = array_shift( $subscriptions );
		}

		if ( ! empty( $subscription ) ) {

			if ( ! empty( $subscription['expires'] ) ) {

				try {

					$end_date = new \DateTime( "@{$subscription['expires']}" );
					$end_date = $end_date->format( 'Y-m-d' );

				} catch ( \Exception $exception ) {}

			} elseif ( ! empty( $subscription['lifetime'] ) ) {

				$end_date = 'lifetime';
			}
		}

		return $end_date;
	}


	/**
	 * Gets the email content type.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_content_type() {

		return 'text/html';
	}


}
