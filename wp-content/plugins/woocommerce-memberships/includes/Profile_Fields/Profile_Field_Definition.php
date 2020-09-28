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

namespace SkyVerge\WooCommerce\Memberships\Profile_Fields;

use SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field\User_Meta;
use SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field_Definition\Option;
use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile field definition object
 *
 * @since 1.19.0
 */
class Profile_Field_Definition extends \WC_Data {


	/** @var string flag used when the profile field should only be editable by admins */
	const EDITABLE_BY_ADMIN = 'admin';

	/** @var string flag used when the profile field should be editable by admins AND the customer the profile field belongs to */
	const EDITABLE_BY_CUSTOMER = 'customer';


	/** @var string overrides parent property
	protected $id = '';

	/** @var string used in \WC_Data hook names */
	protected $object_type = 'memberships_profile_field_definition';

	/** @var array the profile field data, with defaults */
	protected $data = [
		'slug'                => '',
		'name'                => '',
		'type'                => Profile_Fields::TYPE_TEXT,
		'label'               => '',
		'description'         => '',
		'editable_by'         => self::EDITABLE_BY_ADMIN,
		'visibility'          => [],
		'required'            => 'no',
		'default_value'       => '',
		'options'             => [],
		'membership_plan_ids' => [],
	];


	/**
	 * Profile field definition constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param array|string|Profile_Field_Definition $data array of profile field definition data, slug, or instance of self
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function __construct( $data = [] ) {

		parent::__construct( $data );

		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} elseif ( is_string( $data ) ) {
			$this->set_slug( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
			$this->set_slug( $data->get_slug( 'edit' ) );
		} else {
			// new object
			$this->set_object_read( true );
		}

		$this->data_store = new Option();

		if ( ! $this->get_object_read() ) {
			$this->data_store->read( $this );
		}
	}


	/**
	 * Gets the profile field definition ID.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_id() {

		$id = parent::get_id();

		return ! empty( $id ) ? (string) $id : '';
	}


	/**
	 * Sets the profile field definition ID.
	 *
	 * @since 1.19.0
	 *
	 * @param string $id
	 */
	public function set_id( $id = '' ) {

		if ( '' === $id ) {
			$id = uniqid( '', false );
		} elseif ( 0 === $id || ! is_string( $id ) ) {
			$id = '';
		} else {
			$id = sanitize_text_field( $id );
		}

		$this->id = $id;
	}


	/**
	 * Gets the profile field unique slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {

		return $this->get_prop( 'slug', $context );
	}


	/**
	 * Sets the profile field unique slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $slug
	 */
	public function set_slug( $slug ) {

		$this->set_prop( 'slug', sanitize_title( $slug ) );
	}


	/**
	 * Determines if the profile field is of a given type.
	 *
	 * @since 1.19.0
	 *
	 * @param string|string[] $type the field type or field types to check
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_type( $type, $context = 'view' ) {

		return is_array( $type ) ? in_array( $this->get_type( $context ), $type, true ) : $type === $this->get_type( $context );
	}


	/**
	 * Gets the profile field's input type.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_type( $context = 'view' ) {

		return $this->get_prop( 'type', $context );
	}


	/**
	 * Sets the profile field's input type.
	 *
	 * @since 1.19.0
	 *
	 * @param string a valid field type
	 */
	public function set_type( $type ) {

		$this->set_prop( 'type', $type );
	}


	/**
	 * Gets the profile field's input name.
	 *
	 * This is the common name of the input, e.g. Text, Text Area, Checkbox, etc.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)@param $
	 * @return string
	 */
	public function get_type_name( $context = 'view' ) {

		$profile_field_type  = $this->get_prop( 'type', $context );
		$profile_field_types = Profile_Fields::get_profile_field_types();

		return isset( $profile_field_types[ $profile_field_type ] ) ? $profile_field_types[ $profile_field_type ] : '';
	}


	/**
	 * Gets the profile field's name.
	 *
	 * This is an internal name used to identify the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_name( $context = 'view' ) {

		return $this->get_prop( 'name', $context );
	}


	/**
	 * Sets the profile field's name.
	 *
	 * This is an internal name used to identify the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string $name the profile field name
	 */
	public function set_name( $name ) {

		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}


	/**
	 * Gets the profile field's label.
	 *
	 * This is the user-facing label that may be shown in front end next to the profile field input.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_label( $context = 'view' ) {

		return $this->get_prop( 'label', $context );
	}


	/**
	 * Sets the profile field's label.
	 *
	 * This is the user-facing label that may be shown in front end next to the profile field input.
	 *
	 * @since 1.19.0
	 *
	 * @param string $label
	 */
	public function set_label( $label ) {

		$this->set_prop( 'label', sanitize_text_field( $label ) );
	}


	/**
	 * Gets the profile field's description.
	 *
	 * This is the user-facing description that may be shown in front end next to the profile field input.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_description( $context = 'view' ) {

		return $this->get_prop( 'description', $context );
	}


	/**
	 * Sets the profile field's description.
	 *
	 * This is the user-facing description that may be shown in front end next to the profile field input.
	 *
	 * @since 1.19.0
	 *
	 * @param string $description
	 */
	public function set_description( $description ) {

		$this->set_prop( 'description', wp_kses_post( $description ) );
	}


	/**
	 * Determines whether the profile field type allows for multiple choices.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_multiple( $context = 'view' ) {

		return in_array( $this->get_type( $context ), [ Profile_Fields::TYPE_MULTICHECKBOX, Profile_Fields::TYPE_MULTISELECT ], true );
	}


	/**
	 * Determines whether the field type accepts options and the are options for the profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function has_options( $context = 'view' ) {

		$options_field_type = in_array( $this->get_type( $context ), [ Profile_Fields::TYPE_RADIO, Profile_Fields::TYPE_SELECT ], true ) || $this->is_multiple( $context );

		return $options_field_type && ! empty( $this->get_options( $context ) );
	}


	/**
	 * Gets the profile field options (if type allows).
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string[] array of options
	 */
	public function get_options( $context = 'view' ) {

		return $this->get_prop( 'options', $context );
	}


	/**
	 * Sets the profile field options (if type allows).
	 *
	 * @since 1.19.0
	 *
	 * @param string[] $options array of options
	 */
	public function set_options( array $options ) {

		$sanitized_options = [];

		foreach ( $options as $option ) {

			if ( ! is_string( $option ) || '' === trim( $option ) ) {
				continue;
			}

			$option = sanitize_text_field( $option );

			$sanitized_options[ $option ] = $option;
		}

		$this->set_prop( 'options', $sanitized_options );
	}


	/**
	 * Gets the related profile field's default value.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string|string[] returns array if the field can have multiple choices
	 */
	public function get_default_value( $context = 'view' ) {

		$default_value = $this->get_prop( 'default_value', $context );

		if ( $this->is_multiple() ) {
			$default_value = ! empty( $default_value ) ? (array) $default_value : [];
		} elseif ( is_array( $default_value ) ) {
			$default_value = '';
		}

		return $default_value;
	}


	/**
	 * Sets the related profile field's default value.
	 *
	 * @since 1.19.0
	 *
	 * @param string|bool|int|float|array $value according to field type
	 */
	public function set_default_value( $value ) {

		// normalize boolean values to yes|no
		if ( is_bool( $value ) ) {

			$value = wc_bool_to_string( $value );

		} elseif ( $this->is_multiple( 'edit' ) ) {

			$default_options = [];

			foreach ( (array) $value as $default_option ) {

				if ( ! is_string( $default_option ) || '' === $default_option ) {
					continue;
				}

				$default_option = sanitize_text_field( $default_option );

				$default_options[ $default_option ] = $default_option;
			}

			$value = $default_options;

		} else {

			$value = wc_clean( $value );
		}

		$this->set_prop( 'default_value', $value );
	}


	/**
	 * Determines whether the profile field is editable by a given type of user.
	 *
	 * @since 1.19.0
	 *
	 * @param string $user_type type of user, one of "admin" or "customer"
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return bool
	 */
	public function is_editable_by( $user_type, $context = 'view' ) {

		return $user_type === $this->get_editable_by( $context );
	}


	/**
	 * Gets the user type the related profile field can be edited by.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string
	 */
	public function get_editable_by( $context = 'view' ) {

		return $this->get_prop( 'editable_by', $context );
	}


	/**
	 * Sets the user type the related profile field should be editable by.
	 *
	 * @since 1.19.0
	 *
	 * @param string $user_type a valid user type, one of admin or customer
	 */
	public function set_editable_by( $user_type ) {

		$this->set_prop( 'editable_by', $user_type );
	}


	/**
	 * Gets the IDs of the membership plans the related field is associated to.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return int[] empty array means any plan
	 */
	public function get_membership_plan_ids( $context = 'view' ) {

		return $this->get_prop( 'membership_plan_ids', $context );
	}


	/**
	 * Sets the IDs of the membership plans the related profile field should be associated with.
	 *
	 * @since 1.19.0
	 *
	 * @param int[] $plan_ids empty array will imply that the field is associated with all plans
	 */
	public function set_membership_plan_ids( array $plan_ids = [] ) {

		$this->set_prop( 'membership_plan_ids', $plan_ids );
	}


	/**
	 * Gets the visibility value of the related profile field.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string[] identifiers
	 */
	public function get_visibility( $context = 'view' ) {

		return $this->get_prop( 'visibility', $context );
	}


	/**
	 * Sets the related profile field visibility (where the field should appear).
	 *
	 * Empty visibility is allowed only when the field is admin-only.
	 *
	 * @since 1.19.0
	 *
	 * @param string[] $visibility identifiers
	 */
	public function set_visibility( array $visibility = [] ) {

		$this->set_prop( 'visibility', $visibility );
	}


	/**
	 * Gets the required value.
	 *
	 * @since 1.19.0
	 *
	 * @param string $context the requested context: "edit" or "view" (default)
	 * @return string 'yes' or 'no'
	 */
	public function get_required( $context = 'view' ) {

		return $this->get_prop( 'required', $context );
	}


	/**
	 * Sets the related profile field as required.
	 *
	 * @since 1.19.0
	 *
	 * @param bool $required whether the profile field should be required (have non-empty value)
	 */
	public function set_required( $required ) {

		// normalizes the value to a yes|no string value
		$this->set_prop( 'required', wc_bool_to_string( $required ) );
	}


	/**
	 * Validates the profile field definition data.
	 *
	 * @since 1.19.0
	 *
	 * @return \WP_Error always returns a WP Error object, but only if there are error messages set it should be considered failed
	 */
	public function validate() {

		$errors = new \WP_Error();

		$name = $this->get_name( 'edit' );

		// the profile field must have a non empty name
		if ( ! is_string( $name ) || '' === trim( $name ) ) {
			$errors->add( Invalid_Field::ERROR_INVALID_NAME, __( 'The profile field should have a name.', 'woocommerce-memberships' ) );
		}

		$slug = $this->get_slug( 'edit' );

		// the slug should be a non-numerical string
		if ( empty( $slug ) || ! is_string( $slug ) || is_numeric( $slug ) ) {

			$errors->add( Invalid_Field::ERROR_INVALID_SLUG, __( 'The profile field slug should be a non-numeric string.', 'woocommerce-memberships' ) );

		// check that the slug doesn't belong already to a different field
		} elseif ( '' !== $slug && is_string( $slug ) && ! is_numeric( $slug ) ) {

			foreach ( Profile_Fields::get_profile_field_definitions() as $existing_definition ) {

				if ( $slug === $existing_definition->get_slug( 'edit' ) && $this->get_id() !== $existing_definition->get_id() ) {

					$errors->add( Invalid_Field::ERROR_EXISTING_SLUG, __( 'A profile field with this slug already exists. Please use a unique slug for this profile field.', 'woocommerce-memberships' ) );
					break;
				}
			}
		}

		$field_type = $this->get_type( 'edit' );

		// unrecognized field type
		if ( ! Profile_Fields::is_valid_field_type( $field_type ) ) {
			$errors->add( Invalid_Field::ERROR_INVALID_TYPE, __( 'The profile field should be of a valid type.', 'woocommerce-memberships' ) );
		}

		// multi-choice fields need to have options set
		if ( in_array( $field_type, [ Profile_Fields::TYPE_SELECT, Profile_Fields::TYPE_RADIO, Profile_Fields::TYPE_MULTISELECT, Profile_Fields::TYPE_MULTICHECKBOX ], true ) && ! $this->has_options( 'edit' ) ) {
			$errors->add( Invalid_Field::ERROR_NO_OPTIONS, __( 'Please add one or more field options before saving this profile field.', 'woocommerce-memberships' ) );
		}

		$user_type = $this->get_editable_by( 'edit' );

		// editability must be of a known kind
		if ( ! in_array( $user_type, [ self::EDITABLE_BY_ADMIN, self::EDITABLE_BY_CUSTOMER ], true ) ) {
			$errors->add( Invalid_Field::ERROR_INVALID_USER_TYPE, __( 'The profile field can be only made editable by a valid user type.', 'woocommerce-memberships' ) );
		}

		$visibility = $this->get_visibility( 'edit' );

		// if the field is customer-editable, then it should have set at least one visibility option
		if ( empty( $visibility ) && self::EDITABLE_BY_CUSTOMER === $user_type ) {
			$errors->add( Invalid_Field::ERROR_NO_VISIBILITY, __( 'The profile field should have visibility preferences if editable by a customer.', 'woocommerce-memberships' ) );
		}

		/**
		 * Filters validation errors for a profile field definition.
		 *
		 * @since 1.19.0
		 *
		 * @param \WP_Error $errors a error object, if there are any errors added to it, the validation will be interpreted as failed, passes otherwise
		 * @param Profile_Field_Definition $profile_field_definition current profile field definition object
		 */
		return apply_filters( 'wc_memberships_profile_field_definition_validation', $errors, $this );
	}


	/**
	 * Saves the profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function save() {

		// options are removed if not an options-enabled field type
		if ( ! $this->is_type( [ Profile_Fields::TYPE_SELECT, Profile_Fields::TYPE_RADIO, Profile_Fields::TYPE_MULTISELECT, Profile_Fields::TYPE_MULTICHECKBOX ] ) ) {

			$this->set_options( [] );

			// if it's not an options type and neither a checkbox type, there shouldn't be default values to be set
			if ( ! $this->is_type( Profile_Fields::TYPE_CHECKBOX ) ) {
				$this->set_default_value( '' );
			}
		}

		$name = $this->get_name( 'edit' );
		$slug = $this->get_slug( 'edit' );

		// if no slug has been specified and this is a new profile field, convert the name into a slug
		if ( ! empty( $name ) && is_string( $name ) && ( ! is_string( $slug ) || is_numeric( $slug ) || '' === trim( $slug ) ) && $this->is_new() ) {
			$slug = $this->get_slug_from_name( $name );
			$this->set_slug( $slug );
		}

		// ensures that a new profile field definition doesn't accidentally set the same slug as an existing one
		if ( is_string( $slug ) && ! is_numeric( $slug ) && '' !== trim( $slug ) && $this->is_new() ) {

			foreach ( Profile_Fields::get_profile_field_definitions() as $existing_definition ) {

				if ( $slug === $existing_definition->get_slug( 'edit' ) ) {

					$slug = $this->get_slug_from_name( $slug );
					$this->set_slug( $slug );
					break;
				}
			}
		}

		$validation = $this->validate();

		if ( $errors = $validation->get_error_messages() ) {
			foreach ( $errors as $error_message ) {
				throw new Framework\SV_WC_Plugin_Exception( $error_message );
			}
		}

		parent::save();
	}


	/**
	 * Converts a name into a slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $name optional, defaults to the profile field name
	 * @return string
	 */
	private function get_slug_from_name( $name = '' ) {

		if ( '' === $name ) {
			$name = $this->get_name( 'edit' );
		}

		$slug = sanitize_title( $name );

		// append -<n> to slug where <n> is an incremental number in case two fields with the same slug exist
		if ( Profile_Fields::get_profile_field_definition( $slug ) ) {

			$slug .= '-';
			$counter = 1;

			do {

				$slug = rtrim( $slug, (string) $counter - 1 );

				$slug .= $counter;

				$counter++;

			} while ( Profile_Fields::get_profile_field_definition( $slug ) );
		}

		return $slug;
	}


	/**
	 * Determines whether any profile field matching this definition exists in database.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_in_use() {
		global $wpdb;

		try {

			$slug     = $this->get_slug( 'edit' );
			$meta_key = Profile_Fields::get_profile_field_user_meta_key( $slug );

			if ( empty( $meta_key ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Profile field slug invalid or empty.' );
			}

			// checks if the meta_key is already in use by selecting at least 1 row in the user meta table
			$umeta_id = $wpdb->get_var( $wpdb->prepare( "SELECT umeta_id FROM $wpdb->usermeta WHERE meta_key = %s LIMIT 1", $meta_key ) );

			$meta_key_found = ! empty( $umeta_id );

		} catch ( \Exception $e ) {

			return false;
		}

		/**
		 * Filters whether a profile field related to a definition exists in database.
		 *
		 * @since 1.19.0
		 *
		 * @param bool $meta_key_found true if any profile field matching this definition exists in database
		 * @param Profile_Field_Definition $profile_field_definition the profile field definition instance
		 */
		return (bool) apply_filters( 'wc_memberships_profile_field_in_use', $meta_key_found, $this );
	}


	/**
	 * Determines if the current instance is new (has not been saved to database yet).
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_new() {

		return empty( $this->get_id() ) || ! array_key_exists( $this->get_slug(), $this->data_store->get_profile_field_definitions_data() );
	}


}
