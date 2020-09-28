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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Frontend;

use SkyVerge\WooCommerce\Memberships\Profile_Fields as Profile_Fields_Handler;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Exceptions\Invalid_Field;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The main handler for the profile fields frontend components and UI.
 *
 * This handler is responsible for outputting profile fields form fields in front end areas and allow users to submit them.
 * The profile fields will be shown when linked to products that grant access to a membership listed in the profile field data, o
 * or in sign up forms when they are associated with free membership plans.
 *
 * @since 1.19.0
 */
class Profile_Fields {


	/**
	 * Profile fields frontend handler constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		/** process profile fields showing in product pages @see Profile_Fields_Handler::set_member_profile_fields_from_purchase() */
		add_action( 'woocommerce_before_add_to_cart_button',                [ $this, 'add_product_page_profile_fields' ] );
		add_filter( 'woocommerce_add_to_cart_validation',                   [ $this, 'validate_product_profile_fields_submission' ], 10, 6 );
		add_filter( 'woocommerce_add_cart_item_data',                       [ $this, 'add_product_profile_fields_cart_item_data' ], 10, 3 );
		add_filter( 'woocommerce_get_item_data',                            [ $this, 'display_product_profile_fields_cart_item_data' ], 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item',          [ $this, 'add_product_profile_fields_to_order_item' ], 10, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta',                    [ $this, 'hide_order_item_profile_fields' ] );

		// process profile fields showing in registration forms
		add_action( 'woocommerce_register_form',                                [ $this, 'add_sign_up_form_profile_fields' ], 999 ); // very low priority to attach our fields last before the registration button
		add_filter( 'woocommerce_process_registration_errors',                  [ $this, 'validate_sign_up_profile_fields' ] );
		add_action( 'wc_memberships_grant_free_membership_access_from_sign_up', [ $this, 'set_member_profile_fields_from_sign_up' ], 10, 2 );
	}


	/**
	 * Adds profile fields to the product page.
	 *
	 * This callback method outputs a template in the product page, if the product grants access to a membership plan for which there are profile fields set up for.
	 * The template will provide profile field inputs for the customer to fill as they add the product to cart, with their preferences.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function add_product_page_profile_fields() {
		global $product;

		if ( ! $product instanceof \WC_Product || ! Profile_Fields_Handler::is_using_profile_fields() ) {
			return;
		}

		$membership_plans = $this->get_non_member_plans_for_product( $product );

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => array_keys( $membership_plans ),
			'visibility'          => Profile_Fields_Handler::VISIBILITY_PRODUCT_PAGE
		] );

		if ( ! empty( $profile_fields ) ) {

			wc_get_template( 'single-product/member-profile-fields.php', [
				'membership_plans' => $membership_plans,
				'profile_fields'   => $profile_fields,
			] );
		}
	}


	/**
	 * Adds the profile fields to the sign up form.
	 *
	 * This callback method outputs a template in the registration form, if there are free membership plans that grant access upon registration and there are profile fields for those.
	 * The template will provide profile field inputs for the customer to fill as they add the product to cart, with their preferences.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function add_sign_up_form_profile_fields() {

		if ( get_current_user_id() > 0 || ! Profile_Fields_Handler::is_using_profile_fields() ) {
			return;
		}

		$membership_plans = wc_memberships_get_free_membership_plans();
		$applicable_plans = [];

		foreach ( $membership_plans as $id => $membership_plan ) {
			$applicable_plans[ $membership_plan->get_id() ] = $membership_plan;
		}

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => array_keys( $applicable_plans ),
			'visibility'          => Profile_Fields_Handler::VISIBILITY_REGISTRATION_FORM
		] );

		if ( ! empty( $profile_fields ) ) {

			wc_get_template( 'myaccount/member-profile-fields.php', [
				'membership_plans' => $applicable_plans,
				'profile_fields'   => $profile_fields,
			] );
		}
	}


	/**
	 * Gets profile fields for user submission.
	 *
	 * This private helper method will generate an array of new profile field objects meant to populate form field inputs.
	 * @see \wc_memberships_profile_field_form_field()
	 * @see Profile_Fields::add_product_page_profile_fields()
	 * @see Profile_Fields::add_sign_up_form_profile_fields()
	 *
	 * @since 1.19.0
	 *
	 * @param array $args optional array of arguments
	 * @return Profile_Fields_Handler\Profile_Field[] array of profile fields prepared for user input
	 */
	private function get_profile_fields_for_submission( array $args = [] ) {

		$args = (array) wp_parse_args( $args, [
			'membership_plan_ids' => [],
			'editable_by'         => Profile_Fields_Handler\Profile_Field_Definition::EDITABLE_BY_CUSTOMER,
		] );

		$user_id        = get_current_user_id();
		$profile_fields = [];

		// load profile field definitions if the list of membership_plan_ids is not empty only
		$profile_field_definitions = ! empty( $args['membership_plan_ids'] ) ? Profile_Fields_Handler::get_profile_field_definitions( $args ) : [];

		$existing_choices = ! empty( $profile_field_definitions ) ? $this->get_profile_field_slugs_from_cart() : [];

		// subtract from results any profile fields that may have been already processed by the user for another product or that they already have assigned
		foreach ( array_keys( $profile_field_definitions ) as $slug ) {

			if ( in_array( $slug, $existing_choices, true ) || ( $user_id > 0 && Profile_Fields_Handler::get_profile_field( $user_id, $slug ) ) ) {

				unset( $profile_field_definitions[ $slug ] );
				continue;
			}
		}

		$profile_fields_data = $this->get_profile_fields_user_data();

		foreach ( $profile_field_definitions as $profile_field_definition ) {

			$profile_field = new Profile_Fields_Handler\Profile_Field();
			$profile_field->set_user_id( get_current_user_id() );
			$profile_field->set_slug( $profile_field_definition->get_slug() );

			if ( isset( $profile_fields_data[ $profile_field->get_slug() ] ) ) {
				$profile_field->set_value( $profile_fields_data[ $profile_field->get_slug() ] );
			}

			$profile_fields[ $profile_field_definition->get_slug() ] = $profile_field;
		}

		return $profile_fields;
	}


	/**
	 * Gets profile fields user data from the posted form and the session.
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	private function get_profile_fields_user_data() {

		$posted_data  = Framework\SV_WC_Helper::get_posted_value( 'member_profile_fields', [] );
		$session_data = Profile_Fields_Handler::get_uploaded_profile_field_files_from_session();

		return array_merge( $session_data, is_array( $posted_data ) ? $posted_data : [] );
	}


	/**
	 * Adds the profile fields to the cart item data.
	 *
	 * Profile field entered data won't be added if early user input validation has failed.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param array $cart_item_data extra cart item data
	 * @param int $product_id the ID of the product being added to the cart
	 * @param int $variation_id the ID of the variation being added to the cart
	 * @return array
	 */
	public function add_product_profile_fields_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		$product = wc_get_product( $product_id );

		if ( ! $product instanceof \WC_Product || ! $this->should_process_product_profile_fields_submission( $product ) ) {
			return $cart_item_data;
		}

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => array_keys( $this->get_non_member_plans_for_product( $product ) ),
			'visibility'          => Profile_Fields_Handler::VISIBILITY_PRODUCT_PAGE,
		] );

		$data = $this->validate_profile_fields_data( $profile_fields );

		if ( ! empty( $data ) && ! is_wp_error( $data ) ) {
			$cart_item_data['profile_fields'] = $data;
		}

		return $cart_item_data;
	}


	/**
	 * Determines whether we should process profile fields data submitted from a product page.
	 *
	 * Allows extensions like Teams for Memberships to disable profile fields processing if, for example, the fields were rendered but not presented to the customer.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Product $product product being added to the cart
	 * @return bool
	 */
	private function should_process_product_profile_fields_submission( \WC_Product $product ) {

		/**
		 * Filters whether we should process profile fields data submitted from a product page.
		 *
		 * @since 1.19.0
		 *
		 * @param bool $process_product_profile_fields whether we should process the submitted data or not
	 	 * @param \WC_Product $product product being added to the cart
		 */
		return (bool) apply_filters( 'wc_memberships_should_process_product_profile_fields_submission', true, $product );
	}


	/**
	 * Adds the product profile fields to an order item.
	 *
	 * We won't display profile field data in orders, but we will still record submitted data also as order item meta.
	 * This will allow us to grab this data _after_ the order went through and a user membership has been created because of it, and then finally create profile fields associated with the user.
	 * @see Profile_Fields::set_member_profile_fields_from_purchase()
	 * @see Profile_Fields::hide_order_item_profile_fields()
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Order_Item_Product $order_item order item object
	 * @param $cart_item_key cart item key for the given order item
	 * @param $values cart item data for the given order item
	 */
	public function add_product_profile_fields_to_order_item( $order_item, $cart_item_key, $values ) {

		if ( isset( $values['profile_fields'] ) ) {
			$order_item->update_meta_data( Profile_Fields_Handler::ORDER_ITEM_PROFILE_FIELDS_META, $values['profile_fields'] );
		}
	}


	/**
	 * Shows the product profile field in the cart item data.
	 *
	 * In this way, we will display entered profile field values in the cart page for the user to review.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param array $data cart item data
	 * @param array $item a cart item object
	 * @return array
	 */
	public function display_product_profile_fields_cart_item_data( $data, $item ) {

		if ( isset( $item['profile_fields'] ) && is_array( $item['profile_fields'] ) ) {

			foreach ( $item['profile_fields'] as $slug => $value ) {

				$profile_field = new Profile_Field();
				$profile_field->set_slug( $slug );
				$profile_field->set_value( $value );

				if ( $profile_field_definition = $profile_field->get_definition() ) {

					$value = $profile_field->get_formatted_value();

					if ( $value && $profile_field_definition->is_type( Profile_Fields_Handler::TYPE_FILE ) ) {
						$value = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( $value ), esc_html( get_the_title( $profile_field->get_value() ) ) );
					}

					$data[] = [
						'name'  => $profile_field_definition->get_label() ?: $profile_field_definition->get_name(),
						'value' => $value,
					];
				}
			}
		}

		return $data;
	}


	/**
	 * Gets the profile field slugs from the customer cart.
	 *
	 * This helper method will be used to intersect profile field data that has been already entered with profile fields that should be shown on a product page.
	 * In this way we don't ask the user again to enter profile field information if they already added a product to cart that prompted the same profile field inputs.
	 * This may happen in a scenario where the user has added two different access-granting products to cart which trigger the same or overlapping profile fields.
	 * @see Profile_Fields::add_product_page_profile_fields()
	 * @see Profile_Fields::add_sign_up_form_profile_fields()
	 * @see Profile_Fields::get_profile_fields_for_submission()
	 *
	 * @since 1.19.0
	 *
	 * @return string[] array of profile field slugs
	 */
	private function get_profile_field_slugs_from_cart() {

		$slugs = [ [] ];
		$cart  = wc()->cart ? wc()->cart->get_cart() : [];

		foreach ( $cart as $cart_item ) {

			if ( ! isset( $cart_item['profile_fields'] ) || ! is_array( $cart_item['profile_fields'] ) ) {
				continue;
			}

			$slugs[] = array_keys( $cart_item['profile_fields'] );
		}

		return array_unique( array_merge( ...$slugs ) );
	}


	/**
	 * Adds the member profile fields to the meta keys that should be hidden from the order admin view.
	 *
	 * @see Profile_Fields::add_product_profile_fields_to_order_item()
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param array $hidden_item_meta_keys
	 * @return string[]
	 */
	public function hide_order_item_profile_fields( $hidden_item_meta_keys ) {

		$hidden_item_meta_keys[] = Profile_Fields_Handler::ORDER_ITEM_PROFILE_FIELDS_META;

		return $hidden_item_meta_keys;
	}


	/**
	 * Adds profile fields to a membership granted upon sign up.
	 *
	 * If signing up has determined a new membership, and there were profile fields sent along the user creation, we can set those form inputs as profile fields on the newly created membership.
	 * @see Profile_Fields::add_sign_up_form_profile_fields()
	 * @see \WC_Memberships_Membership_Plans::grant_access_to_free_membership()
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan that the user was granted access to
	 * @param array $args {
	 *     @type int $user_id newly registered user ID
	 *     @type int $user_membership_id the ID of the new user membership
	 * }
	 */
	public function set_member_profile_fields_from_sign_up( $membership_plan, $args ) {

		if ( ! isset( $args['user_membership_id'] ) ) {
			return;
		}

		$user_membership = wc_memberships_get_user_membership( $args['user_membership_id'] );

		if ( ! $user_membership ) {
			return;
		}

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => [ $membership_plan->get_id() ],
			'visibility'          => Profile_Fields_Handler::VISIBILITY_REGISTRATION_FORM,
		] );

		$data = $this->validate_profile_fields_data( $profile_fields );

		if ( is_wp_error( $data ) ) {
			return;
		}

		$file_profile_fields = [];

		foreach ( $data as $slug => $value ) {

			try {
				$profile_field = $user_membership->set_profile_field( $slug, $value );
			} catch ( Framework\SV_WC_Plugin_Exception $e ) {
				continue;
			}

			if ( $profile_field->get_definition()->is_type( Profile_Fields_Handler::TYPE_FILE ) ) {

				$file_profile_fields[] = $profile_field;
			}
		}

		if ( ! empty( $file_profile_fields ) ) {
			Profile_Fields_Handler::move_uploaded_profile_fields_files_to_member_profile_fields_folder( $user_membership, $file_profile_fields );
		}
	}


	/**
	 * Validates the product profile fields while adding an access-granting product to cart.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param bool $valid whether validation passes
	 * @param int $product_id the ID of the product being added to cart
	 * @param int $quantity the quantity of the product being added to cart
	 * @param string $variation_id the product's variation identifier
	 * @param array $variations any product's associated variations
	 * @param array $cart_item_data cart item data
	 * @return bool
	 */
	public function validate_product_profile_fields_submission( $valid, $product_id, $quantity, $variation_id = '', $variations = [], $cart_item_data = [] ) {

		$product = wc_get_product( $product_id );

		if ( ! $product instanceof \WC_Product || ! $this->should_process_product_profile_fields_submission( $product ) ) {
			return $valid;
		}

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => array_keys( $this->get_non_member_plans_for_product( $product ) ),
			'visibility'          => Profile_Fields_Handler::VISIBILITY_PRODUCT_PAGE,
		] );

		$data = $this->validate_profile_fields_data( $profile_fields );

		if ( is_wp_error( $data ) ) {

			foreach ( $data->get_error_messages() as $message ) {

				Framework\SV_WC_Helper::wc_add_notice( $message, 'error' );
			}

			$valid = false;
		}

		return $valid;
	}


	/**
	 * Validates the product profile fields from a sign up.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param \WP_Error $validation_error
	 * @return \WP_Error
	 */
	public function validate_sign_up_profile_fields( $validation_error ) {

		$profile_fields = $this->get_profile_fields_for_submission( [
			'membership_plan_ids' => array_keys( wc_memberships_get_free_membership_plans() ),
			'visibility'          => Profile_Fields_Handler::VISIBILITY_REGISTRATION_FORM,
		] );

		$data = $this->validate_profile_fields_data( $profile_fields );

		if ( is_wp_error( $data ) ) {

			foreach ( $data->get_error_codes() as $code ) {
				$validation_error->add( $code, $data->get_error_message( $code ) );
			}
		}

		return $validation_error;
	}


	/**
	 * Validates the given profile fields values.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Fields_Handler\Profile_Field[] $profile_fields array of objects
	 * @return array|\WP_Error
	 */
	private function validate_profile_fields_data( array $profile_fields ) {

		$errors = new \WP_Error();
		$data   = [];

		foreach ( $profile_fields as $profile_field ) {

			$field_errors = $profile_field->validate();

			if ( $message = $field_errors->get_error_message( Invalid_Field::ERROR_REQUIRED_VALUE ) ) {
				$errors->add( $profile_field->get_slug(), $message );
				continue;
			}

			if ( $message = $field_errors->get_error_message( Invalid_Field::ERROR_INVALID_VALUE ) ) {
				$errors->add( $profile_field->get_slug(), $message );
				continue;
			}

			$data[ $profile_field->get_slug() ] = $profile_field->get_value();
		}

		return $errors->has_errors() ? $errors : $data;
	}


	/**
	 * Gets a list of membership plans that the current user is not a member of and that are accessible from a given product.
	 *
	 * Helper method, do not open to public.
	 * @see \WC_Memberships_Membership_Plans::get_membership_plans_for_product()
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Product|int $product product ID or object
	 * @return \WC_Memberships_Membership_Plan[]
	 */
	private function get_non_member_plans_for_product( $product ) {

		$applicable_plans = [];

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product instanceof \WC_Product || ! Profile_Fields_Handler::is_using_profile_fields() ) {
			return $applicable_plans;
		}

		foreach ( wc_memberships()->get_plans_instance()->get_membership_plans_for_product( $product ) as $membership_plan ) {

			if ( ! wc_memberships_is_user_member( get_current_user_id(), $membership_plan ) ) {

				$applicable_plans[ $membership_plan->get_id() ] = $membership_plan;
			}
		}

		return $applicable_plans;
	}


}
