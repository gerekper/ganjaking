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

namespace SkyVerge\WooCommerce\Memberships\Profile_Fields;

use SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field\User_Meta;
use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Exceptions\Invalid_Field;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile field object.
 *
 * @since 1.19.0
 */
class Profile_Field extends \WC_Data {


	/** @var string implements parent property: in profile fields this equals to the slug */
	protected $id = '';

	/** @var string used in \WC_Data hook names */
	protected $object_type = 'memberships_profile_field';

	/** @var array the profile field data, with defaults */
	protected $data = [
		'user_id' => 0,
		'slug'    => '',
		'value'   => '',
	];


	/**
	 * Profile field constructor
	 *
	 * @since 1.19.0
	 *
	 * @param array|Profile_Field $data associative array of profile field data, or profile field object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function __construct( $data = [] ) {

		// sets default data
		parent::__construct( $data );

		$this->data_store = new User_Meta();

		if ( is_array( $data ) && ! empty( $data ) ) {

			$this->set_defaults();

			$this->data['user_id'] = isset( $data['user_id'] ) ? (int)    $data['user_id'] : 0;
			$this->data['slug']    = isset( $data['slug'] )    ? (string) $data['slug']    : '';

			$this->data_store->read( $this );

		} elseif ( ! $data instanceof self ) {

			// new object
			$this->set_object_read( true );
		}
	}


	/**
	 * Gets the profile field ID.
	 *
	 * For a profile field, this is an alias of the slug.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_id() {

		$slug    = $this->get_slug();
		$user_id = $this->get_user_id( 'edit' );

		try {
			$exists = $slug && $user_id > 0 ? (bool) Profile_Fields::get_profile_field( $user_id, $slug ) : false;
		} catch ( \Exception $e ) {
			$exists = false;
		}

		/** if the ID is empty when saving with {@see \WC_Data::save()} it will use the {@see User_Meta::create()} method instead of {@see User_Meta::update()} method */
		return $exists ? $this->get_slug() : '';
	}


	/**
	 * Sets the profile field ID.
	 *
	 * For profile fields, the ID is an alias of the slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $id
	 */
	public function set_id( $id ) {

		$this->set_slug( $id );
	}


	/**
	 * Gets the profile field slug.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_slug() {

		// slug shouldn't be made filterable
		return $this->get_prop( 'slug', 'edit' );
	}


	/**
	 * Gets the profile field label.
	 *
	 * @since 1.23.0
	 *
	 * @return string
	 */
	public function get_name() {

		$definition = $this->get_definition();
		return $definition ? $definition->get_name( 'view' ) : '';
	}


	/**
	 * Sets the profile field slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string
	 */
	public function set_slug( $slug ) {

		$this->set_prop( 'slug', $slug );
	}


	/**
	 * Gets the ID of the user the profile field belongs to.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {

		return $this->get_prop( 'user_id', $context );
	}


	/**
	 * Sets the profile field user ID.
	 *
	 * @since 1.19.0
	 *
	 * @param int $user_id user ID
	 */
	public function set_user_id( $user_id ) {

		$this->set_prop( 'user_id', absint( $user_id ) );
	}


	/**
	 * Gets the object of the user the profile field belongs to.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return null|\WP_User
	 */
	public function get_user( $context = 'view' ) {

		$user = get_user_by( 'id', $this->get_user_id( $context ) );

		return $user instanceof \WP_User ? $user : null;
	}


	/**
	 * Gets the value of the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string|int|float|array
	 */
	public function get_value( $context = 'view' ) {

		return $this->get_prop( 'value', $context );
	}


	/**
	 * Sets the value of the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string|bool|int|float|array $value
	 */
	public function set_value( $value ) {

		$sanitized_value = '';

		if ( $profile_field_definition = $this->get_definition() ) {
			$sanitized_value = $this->sanitize_value( $profile_field_definition, wp_unslash( $value ) );
		}

		$this->set_prop( 'value', $sanitized_value );
	}


	/**
	 * Sanitize the profile field value using different rules based on field type.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $definition profile field definition
	 * @param mixed $value profile field value
	 * @return string|int|array
	 */
	private function sanitize_value( Profile_Field_Definition $definition, $value ) {

		// handle a few values differently based on field type
		switch ( $definition->get_type() ) {

			case Profile_Fields::TYPE_TEXT:
			case Profile_Fields::TYPE_RADIO:
				$value = sanitize_text_field( $value );
			break;

			case Profile_Fields::TYPE_CHECKBOX:
				$value = $this->sanitize_checkbox_field( $value );
			break;

			case Profile_Fields::TYPE_TEXTAREA:
				$value = sanitize_textarea_field( $value );
			break;

			case Profile_Fields::TYPE_FILE:
				$value = (int) $value;
			break;

			default:
				$value = wc_clean( $value );
			break;
		}

		// ensure the value is an array if a multiple field type
		if ( $definition->is_multiple() ) {

			// remove empty strings
			$value = array_values( array_filter( (array) $value, static function( $v ) {
				return ! is_string( $v ) || '' !== trim( $v );
			} ) );
		}

		return $value;
	}


	/**
	 * Sanitizes a value for a checkbox field.
	 *
	 * @since 1.19.0
	 *
	 * @param string|bool|int $value profile field value
	 * @return bool
	 */
	private function sanitize_checkbox_field( $value ) {

		if ( is_string( $value ) || is_numeric( $value ) || is_bool( $value ) ) {
			return wc_string_to_bool( $value );
		}

		return false;
	}


	/**
	 * Gets the profile field formatted value.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context
	 * @return string
	 */
	public function get_formatted_value( $context = 'view' ) {

		$value = $this->get_value( $context );

		if ( $profile_field_definition = $this->get_definition() ) {

			switch ( $profile_field_definition->get_type( $context ) ) {

				case Profile_Fields::TYPE_CHECKBOX :
					$value = ! is_array( $value ) && wc_string_to_bool( $value ) ? __( 'Yes', 'woocommerce-memberships' ) : __( 'No', 'woocommerce-memberships' );
				break;

				case Profile_Fields::TYPE_FILE :
					$value = ! is_array( $value ) ? wp_get_attachment_url( $value ) : '';
					$value = is_string( $value ) ? $value : '';
				break;

				default :

					if ( is_array( $value ) || $this->is_multiple() ) {
						$value = implode( ', ', array_map( 'stripslashes', (array) $value ) );
					} else {
						$value = stripslashes( $value );
					}

				break;
			}
		}

		return $value;
	}


	/**
	 * Gets the profile field definition object.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Field_Definition
	 */
	public function get_definition() {

		return Profile_Fields::get_profile_field_definition( $this->get_slug() );
	}


	/**
	 * Determines whether the profile field is editable by the current user.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_user_editable( $context = 'view' ) {

		$is_admin    = current_user_can( 'manage_woocommerce' );
		$is_editable = $is_admin; // admins can always edit any field

		if ( ! $is_admin && ( $definition = $this->get_definition() ) ) {
			$is_editable = Profile_Field_Definition::EDITABLE_BY_CUSTOMER === $definition->get_editable_by( $context ) && $this->is_visible( $context );
		}

		return $is_editable;
	}


	/**
	 * Determines whether the profile field is required (must have a non-empty value)
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_required( $context = 'view' ) {

		$definition = $this->get_definition();

		return $definition && Profile_Field_Definition::EDITABLE_BY_CUSTOMER && wc_string_to_bool( $definition->get_required( $context ) );
	}


	/**
	 * Determines whether the profile field can have multiple values (array).
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_multiple() {

		$definition = $this->get_definition();

		return $definition && $definition->is_multiple();
	}


	/**
	 * Determines whether the profile field is visible.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_visible( $context = 'view' ) {

		$definition = $this->get_definition();

		return $definition && ! empty ( $definition->get_visibility( $context ) );
	}


	/**
	 * Validates the profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @return \WP_Error always returns a WP Error object, but only if there are error messages set it should be considered failed
	 */
	public function validate() {

		$errors = new \WP_Error();

		$user_id    = $this->get_user_id( 'edit' );
		$value      = $this->get_value( 'edit' );
		$definition = $this->get_definition();
		$name       = $definition ? $definition->get_name( 'edit' ) : '';
		$name       = ! empty( $name ) ? '"' . stripslashes( $name ) . '"' : '';

		if ( ! $definition ) {
			$errors->add( Invalid_Field::ERROR_NO_DEFINITION, __( 'The profile field is invalid.', 'woocommerce-memberships' ) );
		}

		if ( ! is_numeric( $user_id ) || $user_id <= 0 ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_INVALID_USER, sprintf( __( 'The profile field %s should be assigned to a user.', 'woocommerce-memberships' ), $name ) );
		}

		if ( ! Profile_Fields::is_profile_field_slug( $this->get_slug() ) ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_INVALID_SLUG, sprintf( __( 'The profile field %s must have a valid slug.', 'woocommerce-memberships' ), $name ) );
		}

		if ( empty ( $value ) && $this->is_required() && $this->is_user_editable() ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_REQUIRED_VALUE, sprintf( __( 'The profile field %s is required.', 'woocommerce-memberships' ), $name ) );
		}

		// option types: value does not match possible option choices
		if ( ! empty ( $value ) && $definition && $definition->has_options() && array_diff( (array) $value, $definition->get_options() ) ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_INVALID_VALUE, sprintf( __( 'The value of the profile field %s does not match one of the possible options.', 'woocommerce-memberships' ), $name ) );
		}

		// checkbox type: the value is not a boolean
		if ( ! is_bool( $value ) && $definition && $definition->is_type( Profile_Fields::TYPE_CHECKBOX ) ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_INVALID_VALUE, sprintf( __( 'The value for the profile field %s is invalid.', 'woocommerce-memberships' ), $name ) );
		}

		// file type: the ID in value is not a valid attachment
		if ( ! empty( $value ) && $definition && $definition->is_type( Profile_Fields::TYPE_FILE ) && ! wp_get_attachment_url( $value ) ) {
			/* translators: Placeholder: %s - profile field name */
			$errors->add( Invalid_Field::ERROR_INVALID_VALUE, sprintf( __( 'The value of the profile field %s does not match an existing uploaded file.', 'woocommerce-memberships' ), $name ) );
		}

		$definition_plan_ids = $definition ? $definition->get_membership_plan_ids( 'edit' ) : [];

		if ( ! empty ( $definition_plan_ids ) ) {

			$user_memberships         = wc_memberships_get_user_memberships( $user_id );
			$user_membership_plan_ids = array_map( static function( $membership ) { return $membership->get_plan_id(); }, $user_memberships );

			if ( empty ( array_intersect( $definition_plan_ids, $user_membership_plan_ids ) ) ) {
				/* translators: Placeholder: %s - profile field name */
				$errors->add( Invalid_Field::ERROR_INVALID_PLAN, sprintf( __( 'The user must be a member of one of the %s profile field plans.', 'woocommerce-memberships' ), $name ) );
			}
		}

		/**
		 * Filters validation errors for a profile field.
		 *
		 * @since 1.19.0
		 *
		 * @param \WP_Error $errors an error object, if there are any errors added to it, the validation will be interpreted as failed, passes otherwise
		 * @param Profile_Field $profile_field current profile field object
		 */
		return apply_filters( 'wc_memberships_profile_field_validation', $errors, $this );
	}


	/**
	 * Saves the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @throws Invalid_Field
	 */
	public function save() {

		$validation = $this->validate();

		foreach ( $validation->get_error_codes() as $code ) {
			throw new Invalid_Field( $validation->get_error_message( $code ), $code );
		}

		parent::save();
	}


	/**
	 * Determines whether the current object is new and does not have a database entry yet.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_new() {

		$user_id = $this->get_user_id( 'edit' );
		$slug    = $this->get_slug();

		return ! $user_id || ! $slug || ! $this->data_store || ! metadata_exists( 'user', $user_id, $this->data_store->get_meta_key( $this ) );
	}


}
