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
 * Provider Profile class
 *
 * Parses & normalizes HybridAuth user profile
 *
 * @link https://github.com/hybridauth/hybridauth/blob/master/hybridauth/Hybrid/User_Profile.php
 *
 * @since 1.0.0
 */
class WC_Social_Login_Provider_Profile {


	/** @var string provider ID */
	private $provider_id;

	/** @var array user profile data */
	private $profile;


	/**
	 * Sets up a profile.
	 *
	 * In 2.0.0 added the provider_id as the first param.
	 *
	 * @since 1.0.0
	 *
	 * @param string $provider_id provider id
	 * @param array $profile user profile data
	 */
	public function __construct( $provider_id, $profile ) {

		$this->provider_id = $provider_id;

		/**
		 * Filter provider's profile.
		 *
		 * Allows providers to normalize the profile before any processing.
		 *
		 * @since 1.0.0
		 * @param array $profile User's profile data from HybridAuth
		 * @param string $provider_id provider ID
		 */
		$this->profile = apply_filters( 'wc_social_login_' . $provider_id . '_profile', $profile, $provider_id );
	}


	/**
	 * Get the provider ID for this profile
	 *
	 * @since 1.0.0
	 * @return string provider ID, e.g. 'facebook'
	 */
	public function get_provider_id() {

		return $this->provider_id;
	}


	/**
	 * Get the full profile returned by HybridAuth, transformed into associative
	 * array with snake_case keys (HA uses camelCase).
	 *
	 * @since 1.1.0
	 * @return array transformed profile
	 */
	public function get_full_profile() {

		return $this->profile;
	}


	/**
	 * Meta-method for returning profile data, currently:
	 *
	 * + identifier
	 * + web_site_url
	 * + profile_url
	 * + photo_url
	 * + display_name
	 * + description
	 * + first_name
	 * + last_name
	 * + gender
	 * + language
	 * + age
	 * + birth_day
	 * + birth_year
	 * + email
	 * + email_verified
	 * + phone
	 * + address
	 * + country
	 * + region
	 * + zip
	 *
	 * Providers may also provide additional properties, such as `username` (Facebook).
	 *
	 * sample usage:
	 *
	 * `$email = $profile->get_email()`
	 *
	 * @since 1.0.0
	 * @param string $method called method
	 * @param array $args method arguments
	 * @return string|bool
	 */
	public function __call( $method, $args ) {

		// get_* method
		if ( 0 === strpos( $method, 'get_' ) ) {

			$property = str_replace( 'get_', '', $method );

			return $this->get_profile_value( $property );
		}

		// has_* method
		if ( 0 === strpos( $method, 'has_' ) ) {

			$property = str_replace( 'has_', '', $method );

			return (bool) $this->get_profile_value( $property );
		}

		return null;
	}


	/**
	 * Get the specified profile info value or return an empty string if
	 * the specified info does not exist
	 *
	 * @since 1.0.0
	 * @param string $key key for profile info, e.g. `email`
	 * @return mixed property value or null if not defined
	 */
	private function get_profile_value( $key ) {

		if ( isset( $this->profile[ $key ] ) ) {

			return $this->profile[ $key ];

		} else {

			return null;
		}
	}


	/**
	 * Store user profile for the current provider on user meta
	 *
	 * Will only store the details if they are new or updated
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 * @param bool $new_customer
	 */
	public function update_customer_profile( $user_id, $new_customer ) {

		$profile_sha    = sha1( serialize( $this->get_full_profile() ) );
		$stored_profile = get_user_meta( $user_id, '_wc_social_login_' . $this->get_provider_id() . '_profile', true );

		// do not update profile if it's already up do date
		if ( $stored_profile && sha1( serialize( $stored_profile ) ) === $profile_sha ) {
			return;
		}

		update_user_meta( $user_id, '_wc_social_login_' . $this->get_provider_id() . '_profile',    $this->get_full_profile() );
		update_user_meta( $user_id, '_wc_social_login_' . $this->get_provider_id() . '_identifier', $this->get_identifier() );

		// update avatar if provided
		$this->update_customer_profile_image( $user_id );

		// Only update user profile if this is not a new user
		if ( ! $new_customer ) {
			$this->update_customer_user_profile( $user_id );
		}

		// always update billing details
		$this->update_customer_billing_details( $user_id );

		// allow plugins to know when a user account is linked to a new provider
		if ( ! $stored_profile ) {

			/**
			 * Social login linked to user account.
			 *
			 * This hook is called when a social login is first linked
			 * to a user account.
			 *
			 * @since 1.0.0
			 *
			 * @param int $user_id ID of the user
			 * @param string $provider_ID Social Login provider ID
			 */
			do_action( 'wc_social_login_user_account_linked', $user_id, $this->get_provider_id() );
		}
	}


	/**
	 * Update a customer's profile based on the social profile
	 *
	 * @since 1.0.0
	 * @param int $customer_id
	 */
	public function update_customer_user_profile( $customer_id ) {

		// Bail out if no customer ID or profile
		if ( ! $customer_id ) {
			return;
		}

		$customer_data = get_userdata( $customer_id );

		// Bail out if no customer data was found
		if ( ! $customer_data ) {
			return;
		}

		// Only update data that is not already present
		$update_data = array();

		if ( $this->has_first_name() && ! $customer_data->first_name ) {
			$update_data['first_name'] = $this->get_first_name();
		}

		if ( $this->has_last_name() && ! $customer_data->last_name ) {
			$update_data['last_name'] = $this->get_last_name();
		}

		if ( $this->has_email() && ! $customer_data->email ) {
			$update_data['email'] = $this->get_email();
		}

		// Bail out if no data to update
		if ( empty( $update_data ) ) {
			return;
		}

		$update_data['ID'] = $customer_id;

		wp_update_user( $update_data );
	}


	/**
	 * Update customer's billing details based on the providers profile
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 */
	public function update_customer_billing_details( $user_id ) {

		/**
		 * Filter billing fields.
		 *
		 * Array
		 *
		 * @since 1.0.0
		 * @param array $mapping Array of fields that should be copied/mapped
		 *        to the customer's billing address
		 */
		$fields = apply_filters( 'wc_social_login_billing_profile_mapping', array(
			'first_name' => 'first_name',
			'last_name'  => 'last_name',
			'country'    => 'country',
			'address'    => 'address_1',
			'city'       => 'city',
			'zip'        => 'postcode',
			'email'      => 'email',
			'phone'      => 'phone',
		) );

		// Loop over fields and update billing fields accordingly
		foreach ( $fields as $profile_field => $billing_field ) {

			$has_profile_field = "has_{$profile_field}";
			$get_profile_field = "get_{$profile_field}";

			// Skip if data for field is not provided or the billing field is already populated
			if ( ! $this->$has_profile_field() || get_user_meta( $user_id, 'billing_' . $billing_field, true ) ) {
				continue;
			}

			// Update billing profile field
			update_user_meta( $user_id, 'billing_' . $billing_field, $this->$get_profile_field() );
		}

		/**
		 * Update customer billing profile.
		 *
		 * @since 1.0.0
		 * @param int $customer_id
		 * @param object $profile
		 */
		do_action( 'wc_social_login_update_customer_billing_profile', $user_id, $this );
		do_action( 'wc_social_login_' . $this->get_provider_id() . '_update_customer_billing_profile', $user_id, $this );
	}


	/**
	 * Update user's profile image (avatar)
	 *
	 * @since 1.1.0
	 * @param int $user_id
	 */
	public function update_customer_profile_image( $user_id ) {

		if ( $image = $this->get_photo_url() ) {
			update_user_meta( $user_id, '_wc_social_login_' . $this->get_provider_id() . '_profile_image', esc_url( $image ) );
			update_user_meta( $user_id, '_wc_social_login_profile_image', esc_url( $image ) );
		}
	}


}
