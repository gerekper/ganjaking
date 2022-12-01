<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\Memberships\Frontend\My_Account;
use SkyVerge\WooCommerce\Memberships\Frontend\Profile_Fields;
use \SkyVerge\WooCommerce\Memberships\Profile_Fields as Profile_Fields_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Frontend class, handles general frontend functionality.
 *
 * @since 1.0.0
 */
class WC_Memberships_Frontend {


	/** @var \WC_Memberships_Checkout instance */
	protected $checkout;

	/** @var My_Account instance */
	protected $my_account;

	/** @var Profile_Fields instance */
	private $profile_fields;

	/** @var array associative array for caching membership content classes */
	private $membership_content_classes = array();


	/**
	 * Initializes the frontend classes and hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load classes
		$this->profile_fields = wc_memberships()->load_class( '/src/frontend/Profile_Fields.php',                'SkyVerge\WooCommerce\Memberships\Frontend\Profile_Fields' );
		$this->my_account     = wc_memberships()->load_class( '/src/frontend/My_Account.php',                    My_Account::class );
		$this->checkout       = wc_memberships()->load_class( '/src/frontend/class-wc-memberships-checkout.php', 'WC_Memberships_Checkout' );

		// enqueue JS and styles
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );

		// show a notice to admins on new installs about restricted content
		add_action( 'wp_footer', array( $this, 'output_admin_message_html' ) );

		// handle frontend actions
		add_action( 'template_redirect', array( $this, 'cancel_membership' ) );
		add_action( 'template_redirect', array( $this, 'renew_membership' ) );

		// add CSS classes for content that is part of a membership
		add_filter( 'body_class', array( $this, 'add_membership_content_body_class' ), 10, 1 );
		add_filter( 'post_class', array( $this, 'add_membership_content_post_class' ), 10, 3 );

		// optionally redirect members upon login (setting)
		add_filter( 'login_redirect', [ $this, 'redirect_to_page_upon_wordpress_login' ], 999, 3 );
		add_action( 'woocommerce_login_redirect', [ $this, 'redirect_to_page_upon_woocommerce_login' ], 30, 2 );

		// display a thank you message when a membership is granted upon order received
		add_action( 'woocommerce_thankyou', array( $this, 'maybe_render_thank_you_content' ), 9 );
	}


	/**
	 * Gets the Checkout instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Checkout
	 */
	public function get_checkout_instance() {
		return $this->checkout;
	}


	/**
	 * Gets the My Account handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return My_Account
	 */
	public function get_my_account_instance() {

		return $this->my_account;
	}


	/**
	 * Gets the Members Area handler instance.
	 *
	 * TODO: remove this method by 2.0.0 or by 2022-03-04 {WV 2020-09-04}
	 *
	 * @since 1.7.4
	 * @deprecated 1.19.0
	 *
	 * @return \WC_Memberships_Members_Area
	 */
	public function get_members_area_instance() {

		wc_deprecated_function( __METHOD__, '1.19.0', __CLASS__ . '::get_my_account_instance()->get_members_area_instance()' );

		return $this->get_my_account_instance()->get_members_area_instance();
	}


	/**
	 * Gets the Profile Fields handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Fields
	 */
	public function get_profile_fields_instance() {

		return $this->profile_fields;
	}


	/**
	 * Enqueues frontend scripts & styles.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts_and_styles() {

		$this->enqueue_styles();
		$this->enqueue_scripts();
	}


	/**
	 * Enqueues front end styles.
	 *
	 * @since 1.19.0
	 */
	private function enqueue_styles() {
		global $post;

		$dependencies = [];

		wp_register_style( 'wc-memberships-profile-fields', wc_memberships()->get_plugin_url() . '/assets/css/frontend/wc-memberships-profile-fields.min.css', [ 'select2' ], \WC_Memberships::VERSION );
		wp_register_style( 'wc-memberships-member-directory', wc_memberships()->get_plugin_url() . '/assets/css/frontend/wc-memberships-directory.min.css', [], WC_Memberships::VERSION );

		if ( $post && is_singular( $post ) && has_shortcode( $post->post_content, 'wcm_directory' ) ) {
			$dependencies[] = 'wc-memberships-member-directory';
		}

		// TODO improve conditional load of front end assets for profile fields {FN 2020-09-03}
		if ( Profile_Fields_Handler::is_using_profile_fields() && ! empty( Profile_Fields_Handler::get_profile_field_definitions( [ 'editable_by' => Profile_Fields_Handler\Profile_Field_Definition::EDITABLE_BY_CUSTOMER, 'type' => [ Profile_Fields_Handler::TYPE_FILE, Profile_Fields_Handler::TYPE_SELECT, Profile_Fields_Handler::TYPE_MULTISELECT ] ] ) ) ) {
			$dependencies[] = 'wc-memberships-profile-fields';
		}

		wp_enqueue_style( 'wc-memberships-frontend', wc_memberships()->get_plugin_url() . '/assets/css/frontend/wc-memberships-frontend.min.css', $dependencies, \WC_Memberships::VERSION );
	}


	/**
	 * Enqueues front end scripts.
	 *
	 * @since 1.19.0
	 */
	private function enqueue_scripts() {

		// TODO improve conditional load of front end assets {FN 2020-09-03}
		if ( Profile_Fields_Handler::is_using_profile_fields() ) {

			$dependencies =[ 'jquery' ];

			// if there are file inputs, require plupload
			if ( ! empty( Profile_Fields_Handler::get_profile_field_definitions( [ 'editable_by' => Profile_Fields_Handler\Profile_Field_Definition::EDITABLE_BY_CUSTOMER, 'type' => Profile_Fields_Handler::TYPE_FILE ] ) ) ) {
				$dependencies[] = 'plupload-all';
			}

			// if there are dropdown inputs, ensure Select2 is available to enhanced them
			if ( ! empty( Profile_Fields_Handler::get_profile_field_definitions( [ 'editable_by' => Profile_Fields_Handler\Profile_Field_Definition::EDITABLE_BY_CUSTOMER, 'type' => [ Profile_Fields_Handler::TYPE_SELECT, Profile_Fields_Handler::TYPE_MULTISELECT ] ] ) ) ) {
				$dependencies[] = 'selectWoo';
			}

			wp_register_script( 'wc-memberships-frontend', wc_memberships()->get_plugin_url() . '/assets/js/frontend/wc-memberships-frontend.min.js', $dependencies, \WC_Memberships::VERSION, true );

			wp_localize_script( 'wc-memberships-frontend', 'wc_memberships_frontend', [

				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'max_file_size' => wp_max_upload_size(),
				'max_files'     => 1,
				'mime_types'    => $this->get_supported_mime_types(),

				'nonces'        => [
					'profile_field_upload_file'  => wp_create_nonce( 'member-profile-field-upload-file' ),
					'profile_field_remove_file'  => wp_create_nonce( 'member-profile-field-remove-file' ),
					'get_product_profile_fields' => wp_create_nonce( Profile_Fields::GET_PRODUCT_PROFILE_FIELDS_ACTION ),
				],

				'i18n'          => [
					/* translators: Placeholder: %1$s - error code, %2$s - error message */
					'upload_error' => __( 'Error %1$s: %2$s', 'woocommerce-memberships' ) // the placeholders content will be handled in JS
				],

			] );

			wp_enqueue_script( 'wc-memberships-frontend' );
		}

		if ( \WC_Memberships_User_Messages::show_admin_message() ) {

			wc_enqueue_js( "
				$( 'div.wc-memberships.admin-restricted-content-notice a.dismiss-link' ).on( 'click', function ( e ) {
					e.preventDefault();
					$.post( '" . esc_js( admin_url( 'admin-ajax.php' ) ) . "', { action: 'wc_memberships_dismiss_admin_restricted_content_notice' } ).done( function() {
						location.reload();
					} );
				} );
			" );
		}
	}


	/**
	 * Gets supported MIME types for file uploads (helper method).
	 *
	 * @see \WC_Memberships_Frontend::enqueue_scripts()
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	private function get_supported_mime_types() {

		$extensions = $types = [];

		$allowed_types = get_allowed_mime_types();

		// break the allowed extensions into their respective types
		foreach ( $allowed_types as $allowed_extensions => $type ) {

			$type = substr( $type, 0, strpos( $type, '/' ) );

			$extensions[ $type ][] = str_replace( '|', ',', $allowed_extensions );
		}

		// format the extensions for plupload
		foreach ( $extensions as $type => $file_extensions ) {

			$types[] = [
				'title'      => $type,
				'extensions' => implode( ',', $file_extensions ),
			];
		}

		/**
		 * Filters the allowed upload mime types.
		 *
		 * @since 1.19.0
		 *
		 * @param array $types the allowed types and their extensions, each an array of {
		 *     @type string $name the mime type name
		 *     @type string $extensions the supported extensions, comma separated
		 * }
		 */
		return (array) apply_filters( 'wc_memberships_allowed_mime_types', $types );
	}


	/**
	 * Outputs the admin content restriction notice html.
	 *
	 * @internal
	 *
	 * @since 1.10.4
	 */
	public function output_admin_message_html() {

		echo \WC_Memberships_User_Messages::get_admin_message_html();
	}


	/**
	 * Cancels a user membership.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function cancel_membership() {

		if ( ! isset( $_REQUEST['cancel_membership'] ) ) {
			return;
		}

		$user_membership_id = (int) $_REQUEST['cancel_membership'];
		$user_membership    = wc_memberships_get_user_membership( $user_membership_id );

		if ( ! $user_membership ) {

			$notice_message = __( 'Invalid membership.', 'woocommerce-memberships' );
			$notice_type    = 'error';

		} else {

			if (     current_user_can( 'wc_memberships_cancel_membership', $user_membership_id )
			      && $user_membership->can_be_cancelled()
			      && isset( $_REQUEST['_wpnonce'] )
			      && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc_memberships-cancel_membership_' . $user_membership_id ) ) {

				$user_membership->cancel_membership( __( 'Membership cancelled by customer.', 'woocommerce-memberships' ) );

				/**
				 * Filters the user cancelled membership message on frontend.
				 *
				 * @since 1.0.0
				 *
				 * @param string $notice the user membership cancelled notice
				 */
				$notice_message =  apply_filters( 'wc_memberships_user_membership_cancelled_notice', __( 'Your membership was cancelled.', 'woocommerce-memberships' ) );
				$notice_type    = 'notice';

				/**
				 * Fires right after a membership has been cancelled by a customer.
				 *
				 * @since 1.0.0
				 *
				 * @param int $user_membership_id a user membership ID
				 */
				do_action( 'wc_memberships_cancelled_user_membership', $user_membership_id );

			} else {

				$notice_message = __( 'Cannot cancel this membership.', 'woocommerce-memberships' );
				$notice_type    = 'error';
			}
		}

		if ( isset( $notice_message, $notice_type ) ) {
			wc_add_notice( $notice_message, $notice_type );
		}

		$redirect_to = wc_memberships_get_members_area_url();

		wp_safe_redirect( '' !== $redirect_to ? $redirect_to : wc_get_page_permalink( 'my-account' ) );
		exit;
	}


	/**
	 * Logs in a member.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership Membership the member to log in belongs to
	 * @throws Framework\SV_WC_Plugin_Exception if log in fails
	 */
	private function log_member_in( $user_membership ) {

		// we're not really concerned with roles since membership / subscription sites probably use custom roles
		// instead, just be sure we don't log anyone in with high permissions
		$log_in_user_id  = $user_membership->get_user_id();
		$current_user_id = get_current_user_id();
		$user_is_admin   = user_can( $log_in_user_id, 'edit_others_posts' ) || user_can( $log_in_user_id, 'edit_users' );
		$error_message   = __( 'Cannot automatically log in. Please log into your account and renew this membership manually.' , 'woocommerce-memberships' );

		/**
		 * Lets third party code to toggle whether a user can be logged in automatically.
		 *
		 * @since 1.8.9
		 *
		 * @param bool $allow_login true if the user should be automatically logged in (default true if not an admin)
		 * @param int $log_in_user_id the user ID of the user to log in
		 */
		$allow_login = (bool) apply_filters( 'wc_memberships_allow_renewal_auto_user_login', ! $user_is_admin, $log_in_user_id );

		/**
		 * Fires before logging a member in for renewal.
		 *
		 * Third party actors can throw SV_WC_Plugin_Exception to halt the login completely.
		 *
		 * @since 1.9.0
		 *
		 * @param int $log_in_user_id the user ID of the member to log in
		 * @param \WC_Memberships_User_Membership $user_membership the user membership instance
		 * @param bool $allow_login whether automatic log in is allowed
		 */
		do_action( 'wc_memberships_before_renewal_auto_login', $log_in_user_id, $user_membership, $allow_login );

		// another user is logged in, but it's a different user: log them out
		if ( $current_user_id > 0 && $log_in_user_id !== $current_user_id ) {
			wp_logout();
		}

		// proceed with auto login, unless membership user is already logged in (including admins)
		if ( ! ( $current_user_id > 0 && $log_in_user_id === $current_user_id ) ) {

			// bail out if we cannot login the user that owns the membership for renewal
			if ( ! $allow_login ) {
				throw new Framework\SV_WC_Plugin_Exception( $error_message );
			}

			// finally log in the user
			wp_set_current_user( $log_in_user_id );
			wp_set_auth_cookie( $log_in_user_id );
		}

		/**
		 * Fires after logging a member in for renewal.
		 *
		 * @since 1.9.0
		 *
		 * @param int $log_in_user_id the user ID of the member to log in
		 * @param \WC_Memberships_User_Membership $user_membership the user membership instance
		 * @param bool $allow_login whether automatic log in is allowed
		 */
		do_action( 'wc_memberships_after_renewal_auto_login', $log_in_user_id, $user_membership, $allow_login );
	}


	/**
	 * Renews a user membership.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function renew_membership() {

		if ( ! isset( $_REQUEST['renew_membership'] ) ) {
			return;
		}

		$user_membership_id = (int) $_REQUEST['renew_membership'];
		$error_display      = isset( $_REQUEST['display'] ) ? $_REQUEST['display'] : 'all';

		if ( isset( $_REQUEST['error'] ) ) {

			$error_message = $_REQUEST['error'];

		} elseif ( isset( $_REQUEST['success'] ) && $user_membership_id > 0 ) {

			if ( '' !== $_REQUEST['success'] ) {
				wc_add_notice( $_REQUEST['success'], 'success' );
			}

		} else {

			$user_membership = wc_memberships_get_user_membership( $user_membership_id );
			$user_token      = isset( $_REQUEST['user_token'] ) ? wc_clean( $_REQUEST['user_token'] ) : '';

			// we only need to redirect upon success; we should already be on the account page
			// based on how we generate this renewal URL so no need to redirect there
			try {

				$result       = $this->process_membership_renewal( $user_membership, $user_token );
				$redirect_url = $result['redirect'];

				/**
				 * Filters the message shown in front end when renewing a membership.
				 *
				 * @since 1.10.6
				 *
				 * @param string $success_message the message body (may be an empty string if an error message is being shown instead)
				 * @param null|\WC_Memberships_User_Membership $user_membership membership being renewed, will be null on errors
				 */
				$success_message = (string) apply_filters( 'wc_memberships_renew_membership_message', $result['message'], $user_membership );

				// generally redirect to checkout and print a success notice
				wp_safe_redirect( add_query_arg( array( 'renew_membership' => $user_membership_id, 'success' => urlencode( trim( $success_message ) ) ), $redirect_url ) );
				exit;

			} catch ( Framework\SV_WC_Plugin_Exception $e ) {

				/**
				 * Filters the error message that may triggered when renewing a membership.
				 *
				 * @since 1.10.6
				 *
				 * @param string $error_message error message body, an empty message will not trigger an error
				 * @param null|\WC_Memberships_User_Membership $user_membership possibly a membership being renewed
				 */
				$error_message = (string) apply_filters( 'wc_memberships_renew_membership_error_message', $e->getMessage(), $user_membership );

				// this allows to understand the context of the notice thrown:
				// - 0: user was not logged in (an error occurred on auto login before renewal)
				// - 1: user was logged in (an issue with renewal)
				$error_display = 1 === $e->getCode() ? 'all' : 'guests';

				if ( $my_account_url = wc_get_page_permalink( 'myaccount' ) ) {

					// redirects to the my account page and tells to print an error message
					wp_safe_redirect( add_query_arg( array( 'renew_membership' => $user_membership_id, 'error' => urlencode( $error_message ), 'display' => $error_display ), $my_account_url ) );
					exit;
				}
			}
		}

		// maybe print an error message as notice
		if ( ! empty( $error_message ) ) {

			// since both the WooCommerce login screen and the dashboard belong to the myaccount route,
			// this avoids printing logged out guests notices again once the member logs in manually,
			// as we cannot remove query strings from the url, and neither WooCommerce does
			$error_display = 'guests' === $error_display ? ! is_user_logged_in() : true;

			if ( $error_display ) {
				wc_add_notice( $error_message, 'error' );
			}
		}
	}


	/**
	 * Processes user membership renewals with a valid renewal link.
	 *
	 * @since 1.8.9
	 *
	 * @param \WC_Memberships_User_Membership $user_membership user membership instance
	 * @param string $token user membership renewal token
	 * @return array updated redirect URL with a success message
	 * @throws Framework\SV_WC_Plugin_Exception if user cannot be logged in or membership cannot be renewed
	 */
	protected function process_membership_renewal( $user_membership, $token ) {

		$redirect_url = $message = $error_message = '';

		$default_error_message = __( 'Cannot renew this membership. Please contact us if you need assistance.', 'woocommerce-memberships' );

		if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {

			$error_message = __( 'Invalid membership.', 'woocommerce-memberships' );

		} elseif ( $user_membership->can_be_renewed() ) {

			$renewal_token = $user_membership->get_renewal_login_token();

			// renewal token empty / not sent
			if ( empty( $token ) ) {

				$error_message = __( 'Invalid renewal URL.', 'woocommerce-memberships' );

			// renewal token in request does not match user membership's stored token
			} elseif ( ! isset( $renewal_token['token'] ) || $token !== $renewal_token['token'] ) {

				$error_message = __( 'Invalid renewal token.', 'woocommerce-memberships' );

			// renewal token in request has expired
			} elseif ( ! isset( $renewal_token['expires'] ) || (int) $renewal_token['expires'] < time() ) {

				// wipe expired renewal token meta
				$user_membership->delete_renewal_login_token();

				$error_message = __( 'Your renewal token has expired.', 'woocommerce-memberships' );
			}

			if ( '' !== $error_message ) {

				if ( ! is_user_logged_in() ) {
					$error_message .= ' ' . __( 'Please log in to renew this membership from your account page.', 'woocommerce-memberships' );
				} else {
					$error_message .= ' ' . __( 'Please renew this membership from your account page.', 'woocommerce-memberships' );
				}

			} else {

				try {

					// makes sure the member is logged in (may be logged in already)
					$this->log_member_in( $user_membership );

					// get the renewal product to be added to cart
					$product_for_renewal = $user_membership->get_product_for_renewal();

					/* this filter is documented in /src/class-wc-memberships-membership-plan.php */
					$renew = apply_filters( 'wc_memberships_renew_membership', (bool) $product_for_renewal, $user_membership->get_plan(), array(
						'user_id'    => $user_membership->get_user_id(),
						'product_id' => $product_for_renewal ? $product_for_renewal->get_id() : 0,
						'order_id'   => $user_membership->get_order_id(),
					) );

					if ( true === $renew && current_user_can( 'wc_memberships_renew_membership', $user_membership->get_id() ) ) {

						/**
						 * Filters whether to add to cart the renewal product and redirect to checkout, or redirect to the product page without adding it to cart.
						 *
						 * @since 1.7.4
						 *
						 * @param bool $add_to_cart whether to add to cart the product and redirect to checkout (default true unless a parent of an unspecified variation) or redirect to product page instead (false)
						 * @param \WC_Product $product_for_renewal the product that would renew access if purchased again
						 * @param int $user_membership_id the membership being renewed upon purchase
						 */
						if ( true === (bool) apply_filters( 'wc_memberships_add_to_cart_renewal_product', ! $product_for_renewal->is_type( 'variable' ), $product_for_renewal, $user_membership->get_id() ) ) {

							// empty the cart and add the one product to renew this membership
							wc_empty_cart();

							// set up variation data (if needed) before adding to the cart
							$product_id           = $product_for_renewal->is_type( 'variation' ) ? $product_for_renewal->get_parent_id() : $product_for_renewal->get_id();
							$variation_id         = $product_for_renewal->is_type( 'variation' ) ? $product_for_renewal->get_id() : 0;
							$variation_attributes = $product_for_renewal->is_type( 'variation' ) ? wc_get_product_variation_attributes( $variation_id ) : [];

							// add the product to the cart
							$show_message = WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation_attributes );

							// then redirect to checkout instead of my account page
							$redirect_url = wc_get_checkout_url();

						} else {

							$show_message = true;
							$redirect_url = get_permalink( $product_for_renewal->is_type( 'variation' ) ? $product_for_renewal->get_parent_id() : $product_for_renewal->get_id() );
						}

						/* translators: Placeholder: %s - a product to purchase to renew a membership */
						$message  = false === (bool) $show_message ? '' : sprintf( __( 'Renew your membership by purchasing %s.', 'woocommerce-memberships' ) . ' ', $product_for_renewal->get_title() );
						$message .= is_user_logged_in() ? ' ' : __( 'You must be logged to renew your membership.', 'woocommerce-memberships' );

					} else {

						$error_message = $default_error_message;
					}

				// login process may produce a more generic Exception
				} catch ( \Exception $e ) {

					$error_message = $e->getMessage();
				}
			}

		} else {

			$error_message = $default_error_message;
		}

		if ( is_string( $error_message ) && '' !== trim( $error_message ) ) {
			throw new Framework\SV_WC_Plugin_Exception( $error_message, is_user_logged_in() ? 1 : 0 );
		}

		return array( 'redirect' => $redirect_url, 'message' => $message );
	}


	/**
	 * Redirects a user upon login when they used the standard WordPress login form.
	 *
	 * The redirection destination is determined based on Memberships settings.
	 *
	 * @internal
	 *
	 * @since 1.21.2
	 *
	 * @param string $redirect_to URL the user is being redirected to
	 * @param string $requested_redirect_to URL (the presence of a non empty string here means that the user is being redirected already to a different page)
	 * @param \WP_User|\WP_Error $user user being logged in
	 * @return string modified URL
	 */
	public function redirect_to_page_upon_wordpress_login( $redirect_to, $requested_redirect_to, $user ) {

		// bail if there's a URL already overriding the standard redirect
		if ( ! $user instanceof \WP_User || ( ! empty( $requested_redirect_to ) && $redirect_to !== $requested_redirect_to ) ) {
			return $redirect_to;
		}

		return $this->redirect_to_page_upon_woocommerce_login( $redirect_to, $user );
	}


	/**
	 * Redirects a user upon login when they used the WooCommerce login form.
	 *
	 * The redirection destination is determined based on Memberships settings.
	 *
	 * This callback must have a lower priority to allow for restricted content access redirects:
	 * @see \WC_Memberships_Posts_Restrictions::redirect_to_member_content_upon_login()
	 * @see \WC_Memberships_Posts_Restrictions::redirect_restricted_content()
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @param string $original_redirect_url URL which WooCommerce is redirecting to
	 * @param \WP_User $user member user object
	 * @return string
	 */
	public function redirect_to_page_upon_woocommerce_login( $original_redirect_url, $user ) {

		// skip for admins & shop managers
		if ( user_can( $user, 'manage_woocommerce' ) || user_can( $user, 'install_plugins' ) ) {
			return $original_redirect_url;
		}

		$redirect_setting = get_option( 'wc_memberships_redirect_upon_member_login', 'no_redirect' );

		if ( 'no_redirect' !== $redirect_setting ) {

			$no_query_var_redirect_url = preg_replace( '/\?.*/', '', $original_redirect_url );

			// retain default behavior if customer is logging in at checkout
			if ( in_array( wc_get_checkout_url(), [ $original_redirect_url, $no_query_var_redirect_url ], true ) || in_array( wc_get_cart_url(), [ $original_redirect_url, $no_query_var_redirect_url ], true ) ) {
				$redirect_setting = 'no_redirect';
			}
		}

		if ( $user && wc_memberships_is_user_active_member( $user ) ) {

			$new_redirect_url = $original_redirect_url;

			switch ( $redirect_setting ) {

				case 'site_page' :

					$redirect_to_page_id = get_option( 'wc_memberships_member_login_redirect_page_id', 0 );

					// the member must be able to access to this page, otherwise retain default behavior
					if ( is_numeric( $redirect_to_page_id ) && $redirect_to_page_id > 0 && wc_memberships_user_can( $user->ID, 'view', [ 'page' => $redirect_to_page_id ] ) ) {
						$new_redirect_url = get_permalink( $redirect_to_page_id );
					}

				break;

				case 'members_area' :

					$plans = 0;

					foreach ( wc_memberships_get_user_active_memberships( $user ) as $user_membership ) {

						$plan = $user_membership->get_plan();

						if ( $plan && count( $plan->get_members_area_sections() ) > 0 ) {

							$new_redirect_url = wc_memberships_get_members_area_url( $plan->get_id() );

							$plans++;

							// if there are two or more plans with a members area, just use the members area plans directory
							if ( 2 === $plans ) {

								$new_redirect_url = wc_memberships_get_members_area_url();
								break;
							}
						}
					}

				break;
			}

			if ( ! is_string( $new_redirect_url ) || '' === trim( $new_redirect_url ) ) {
				$new_redirect_url = $original_redirect_url;
			}

			/**
			 * Filters the URL to redirect the logged in member to.
			 *
			 * @since 1.16.0
			 *
			 * @param string $new_redirect_url URL to redirect member to
			 * @param string $original_redirect_url URL where WooCommerce originally intended to redirect the member to
			 * @param \WP_User $user the member user object
			 */
			$original_redirect_url = (string) apply_filters( 'wc_memberships_member_login_redirect_url', $new_redirect_url, $original_redirect_url, $user );
		}

		return $original_redirect_url;
	}


	/**
	 * Redirects a member who just logged in according to Memberships setting.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 * @deprecated 1.21.2
	 *
	 * @TODO remove this deprecated method by version 2.0.0 or by May 2022 {FN 2020-01-20}
	 *
	 * @param string $original_redirect_url URL which WooCommerce is redirecting to
	 * @param \WP_User $user member user object
	 * @return string
	 */
	public function redirect_to_page_upon_login( $original_redirect_url, $user ) {

		wc_deprecated_function( __METHOD__, '1.21.2', __CLASS__ . '::redirect_to_page_upon_woocommerce_login()' );

		return $this->redirect_to_page_upon_woocommerce_login( $original_redirect_url, $user );
	}


	/**
	 * Prints a thank you message on the "Order Received" page when a membership is purchased.
	 *
	 * @since 1.8.4
	 *
	 * @param int $order_id the order ID
	 */
	public function maybe_render_thank_you_content( $order_id ) {

		echo wp_kses_post( wc_memberships_get_order_thank_you_links( $order_id ) );
	}


	/**
	 * Returns membership content CSS classes.
	 *
	 * @since 1.9.5
	 *
	 * @param \WP_Post|int $post post object or ID
	 * @return string[]
	 */
	private function get_membership_content_classes( $post ) {

		$post_id             = 0;
		$memberships_classes = array();

		if ( is_numeric( $post ) ) {
			$post_id = (int) $post;
		} elseif ( $post instanceof \WP_Post ) {
			$post_id = $post->ID;
		}

		if ( $post_id > 0 ) {

			if ( isset( $this->membership_content_classes[ $post_id ] ) ) {

				$memberships_classes = $this->membership_content_classes[ $post_id ];

			} else {

				$is_user_logged_in = is_user_logged_in();

				if ( 'product' === get_post_type( $post ) ) {

					if ( wc_memberships_is_product_viewing_restricted( $post ) ) {

						$memberships_classes[] = 'membership-content';

						if ( $is_user_logged_in && current_user_can( 'wc_memberships_view_restricted_product', $post_id ) ) {
							if ( ! current_user_can( 'wc_memberships_view_delayed_product', $post_id ) ) {
								$memberships_classes[] = 'access-delayed';
							} else {
								$memberships_classes[] = 'access-granted';
							}
						} else {
							$memberships_classes[] = 'access-restricted';
						}
					}

					if ( wc_memberships_is_product_purchasing_restricted( $post ) ) {

						$memberships_classes[] = 'membership-content';

						if ( $is_user_logged_in && current_user_can( 'wc_memberships_purchase_restricted_product', $post_id ) ) {
							if ( ! current_user_can( 'wc_memberships_purchase_delayed_product', $post_id ) ) {
								$memberships_classes[] = 'purchase-delayed';
							} else {
								$memberships_classes[] = 'purchase-granted';
							}
						} else {
							$memberships_classes[] = 'purchase-restricted';
						}
					}

					if ( wc_memberships_product_has_member_discount( $post ) ) {

						$memberships_classes[] = 'member-discount';

						if ( $is_user_logged_in && wc_memberships_user_has_member_discount( $post ) ) {
							$memberships_classes[] = 'discount-granted';
						} else {
							$memberships_classes[] = 'discount-restricted';
						}
					}

				} elseif ( wc_memberships_is_post_content_restricted( $post ) ) {

					$memberships_classes[] = 'membership-content';

					if ( $is_user_logged_in && current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
						if ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
							$memberships_classes[] = 'access-delayed';
						} else {
							$memberships_classes[] = 'access-granted';
						}
					} else {
						$memberships_classes[] = 'access-restricted';
					}
				}

				$this->membership_content_classes[ $post_id ] = $memberships_classes;
			}
		}

		return $memberships_classes;
	}


	/**
	 * Adds CSS classes to the <body> HTML tag when viewing memberships content.
	 *
	 * @internal
	 *
	 * @since 1.9.5
	 *
	 * @param string[] $classes an array of CSS classes
	 * @return string[]
	 */
	public function add_membership_content_body_class( $classes ) {
		global $post;

		if ( is_array( $classes ) ) {

			$current_user_id     = get_current_user_id();
			$memberships_classes = array();
			$is_member           = $current_user_id > 0 && current_user_can( 'wc_memberships_access_all_restricted_content' );

			if ( ! $is_member && $current_user_id > 0 ) {
				$user_memberships = wc_memberships_get_user_memberships( $current_user_id, array( 'fields' => 'ids' ) );
				$is_member        = ! empty( $user_memberships );
			}

			if ( is_singular() ) {
				if ( $is_members_area = wc_memberships_is_members_area() ) {
					$memberships_classes = array( 'members-area' );
				} elseif ( $post instanceof \WP_Post ) {
					$memberships_classes = $this->get_membership_content_classes( $post );
				}
			}

			if ( $is_member ) {
				$memberships_classes[] = 'member-logged-in';
			}

			$classes = array_unique( array_merge( $classes, $memberships_classes ) );
		}

		return $classes;
	}


	/**
	 * Adds CSS classes to the post classes when viewing memberships content.
	 *
	 * @internal
	 *
	 * @since 1.9.5
	 *
	 * @param string[] $classes array of post classes
	 * @param string[] $additional_classes an array of additional classes added to the post
	 * @param int $post_id the current WP_Post ID
	 * @return string[]
	 */
	public function add_membership_content_post_class( $classes, $additional_classes, $post_id ) {

		return array_merge( $classes, $this->get_membership_content_classes( $post_id ) );
	}



}
