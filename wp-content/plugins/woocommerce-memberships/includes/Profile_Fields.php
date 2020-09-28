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

namespace SkyVerge\WooCommerce\Memberships;

use SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field\User_Meta;
use SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field_Definition\Option;
use SkyVerge\WooCommerce\Memberships\Frontend\Profile_Fields as Frontend_Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile fields handler.
 *
 * @since 1.19.0
 */
class Profile_Fields {


	/** @var string profile field type checkbox input */
	const TYPE_CHECKBOX = 'checkbox';

	/** @var string profile field type file upload */
	const TYPE_FILE = 'file';

	/** @var string profile field type multi-checkbox */
	const TYPE_MULTICHECKBOX = 'multicheckbox';

	/** @var string profile field type multi-dropdown */
	const TYPE_MULTISELECT = 'multiselect';

	/** @var string profile field type radio input */
	const TYPE_RADIO = 'radio';

	/** @var string profile field type dropdown */
	const TYPE_SELECT = 'select';

	/** @var string profile field type text input */
	const TYPE_TEXT = 'text';

	/** @var string profile field type textarea */
	const TYPE_TEXTAREA = 'textarea';

	/** @var string product page identifier */
	const VISIBILITY_PRODUCT_PAGE = 'product-page';

	/** @var string registration forms identifier */
	const VISIBILITY_REGISTRATION_FORM = 'registration-form';

	/** @var string profile fields area identifier */
	const VISIBILITY_PROFILE_FIELDS_AREA = 'profile-fields-area';

	/** @var string the meta key used to store submitted values for member profile fields during checkout or sign up */
	const ORDER_ITEM_PROFILE_FIELDS_META = '_wc_memberships_member_profile_fields';


	/** @var array memoized profile fields, by user ID */
	private static $profile_fields = [];

	/** @var array memoized profile field definitions */
	private static $profile_field_definitions = [];

	/** @var string session key used to store profile fields data */
	private static $session_key = 'member_profile_fields';


	/**
	 * Adds profile fields to a membership upon granted access from purchase.
	 *
	 * We grab the submitted values from an order which granted access to the membership which has thus been created.
	 * @see Frontend_Profile_Fields::add_product_profile_fields_to_order_item()
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan
	 * @param array $args
	 */
	public static function set_member_profile_fields_from_purchase( $plan, $args ) {

		if ( ! isset( $args['order_id'], $args['user_membership_id'] ) || ! $order = wc_get_order( $args['order_id'] ) ) {
			return;
		}

		$user_membership = wc_memberships_get_user_membership( $args['user_membership_id'] );

		if ( ! $user_membership ) {
			return;
		}

		$file_profile_fields = [];

		foreach ( $order->get_items() as $order_item ) {

			if ( ! $profile_fields_data = $order_item->get_meta( self::ORDER_ITEM_PROFILE_FIELDS_META ) ) {
				continue;
			}

			foreach ( $profile_fields_data as $slug => $value ) {

				try {
					$profile_field = $user_membership->set_profile_field( $slug, $value );
				} catch ( Framework\SV_WC_Plugin_Exception $e ) {
					continue;
				}

				if ( $profile_field->get_definition()->is_type( self::TYPE_FILE ) ) {
					$file_profile_fields[] = $profile_field;
				}
			}
		}

		if ( ! empty( $file_profile_fields ) ) {
			self::move_uploaded_profile_fields_files_to_member_profile_fields_folder( $user_membership, $file_profile_fields );
		}
	}


	/**
	 * Gets the field types and the corresponding localized names.
	 *
	 * @since 1.19.0
	 *
	 * @param bool $with_labels if false, will return only the field types
	 * @return string[]|array list of types, or key-value pairs of field types and localized names
	 */
	public static function get_profile_field_types( $with_labels = true ) {

		/**
		 * Filters the profile field types.
		 *
		 * @since 1.19.0
		 *
		 * @param array $types associative array of types and labels
		 */
		$types = (array) apply_filters( 'wc_memberships_get_profile_field_types', [
			self::TYPE_CHECKBOX       => __( 'Checkbox', 'woocommerce-memberships' ),
			self::TYPE_FILE           => __( 'File', 'woocommerce-memberships' ),
			self::TYPE_MULTICHECKBOX  => __( 'Multi Checkbox', 'woocommerce-memberships' ),
			self::TYPE_MULTISELECT    => __( 'Multi Select', 'woocommerce-memberships' ),
			self::TYPE_RADIO          => __( 'Radio', 'woocommerce-memberships' ),
			self::TYPE_SELECT         => __( 'Select', 'woocommerce-memberships' ),
			self::TYPE_TEXT           => __( 'Text', 'woocommerce-memberships' ),
			self::TYPE_TEXTAREA       => __( 'Text Area', 'woocommerce-memberships' ),
		] );

		return $with_labels ? $types : array_keys( $types );
	}


	/**
	 * Gets profile fields compatible types.
	 *
	 * @since 1.19.0
	 *
	 * @param string $field_type the field type to fetch its compatible types
	 * @param bool $with_labels whether to return the resulting field types with their labels, or not
	 * @return string[]|array list of types, or key-value pairs of field types and localized names
	 */
	public static function get_compatible_profile_field_types( $field_type, $with_labels = true ) {

		if ( ! in_array( $field_type, self::get_profile_field_types( false ), true ) ) {
			return [];
		}

		$field_types      = self::get_profile_field_types( true );
		$compatible_types = [
			$field_type => $field_types[ $field_type ]
		];

		switch ( $field_type ) {

			case self::TYPE_MULTICHECKBOX:
				$compatible_types[ self::TYPE_MULTISELECT ] = $field_types[ self::TYPE_MULTISELECT ];
			break;

			case self::TYPE_MULTISELECT:
				$compatible_types[ self::TYPE_MULTICHECKBOX ] = $field_types[ self::TYPE_MULTICHECKBOX ];
			break;

			case self::TYPE_RADIO:
				$compatible_types[ self::TYPE_SELECT ] = $field_types[ self::TYPE_SELECT ];
			break;

			case self::TYPE_SELECT:
				$compatible_types[ self::TYPE_RADIO ] = $field_types[ self::TYPE_RADIO ];
			break;

			case self::TYPE_TEXT:
				$compatible_types[ self::TYPE_TEXTAREA ] = $field_types[ self::TYPE_TEXTAREA ];
			break;

			case self::TYPE_TEXTAREA:
				$compatible_types[ self::TYPE_TEXT ] = $field_types[ self::TYPE_TEXT ];
			break;
		}

		/**
		 * Filters the compatible field types.
		 *
		 * @since 1.19.0
		 *
		 * @param array $compatible_types associative array of field types and labels
		 * @param string $field_type the field type to return compatible types for
		 */
		$compatible_types = (array) apply_filters( 'wc_memberships_profile_fields_compatible_types', $compatible_types, $field_type );

		return $with_labels ? $compatible_types : array_keys( $compatible_types );
	}


	/**
	 * Gets a list of identifiers where profile fields may appear.
	 *
	 * @since 1.19.0
	 *
	 * @param false $with_labels whether to output an associative array with labels
	 * @return string[]|array
	 */
	public static function get_profile_fields_visibility_options( $with_labels = false ) {

		/**
		 * Filters the visibility options of profile fields.
		 *
		 * @since 1.19.0
		 *
		 * @param array $visibility_options associative array of identifiers of front end areas and their labels
		 */
		$visibility_options = (array) apply_filters( 'wc_memberships_profile_fields_visibility_options', [
			self::VISIBILITY_PROFILE_FIELDS_AREA => __( 'My account', 'woocommerce-memberships' ),
			self::VISIBILITY_PRODUCT_PAGE        => __( 'Product page', 'woocommerce-memberships' ),
			self::VISIBILITY_REGISTRATION_FORM   => __( 'Registration form', 'woocommerce-memberships' ),
		] );

		return $with_labels ? $visibility_options : array_keys( $visibility_options );
	}


	/**
	 * Gets a profile field object.
	 *
	 * @since 1.19.0
	 *
	 * @param int $user_id a WP User ID
	 * @param string $slug profile field slug
	 * @return null|Profile_Field
	 */
	public static function get_profile_field( $user_id, $slug ) {

		try {
			$profile_field = new Profile_Field( [
				'user_id' => $user_id,
				'slug'    => $slug,
			] );
		} catch ( \Exception $e ) {
			$profile_field = null;
		}

		/**
		 * Filters a profile field.
		 *
		 * @since 1.19.0.dev-1
		 *
		 * @param null|Profile_Field $profile_field a profile field object, or null
		 * @param int $user_id ID of the user the profile field belongs to
		 * @param string $slug a slug matched to the profile field
		 */
		$profile_field = apply_filters( 'wc_memberships_get_profile_field', $profile_field, $user_id, $slug );

		return $profile_field instanceof Profile_Field ? $profile_field : null;
	}


	/**
	 * Gets a profile field definition from a profile field slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $profile_field_slug slug
	 * @return null|Profile_Field_Definition
	 */
	public static function get_profile_field_definition( $profile_field_slug ) {

		try {
			$profile_field_definition = new Profile_Field_Definition( $profile_field_slug );
		} catch ( \Exception $e ) {
			$profile_field_definition = null;
		}

		/**
		 * Filters a profile field definition.
		 *
		 * @since 1.19.0
		 *
		 * @param null|Profile_Field_Definition $profile_field_definition a profile field definition object, or null
		 * @param string $profile_field_slug a slug matching to a profile field definition
		 */
		$profile_field_definition = apply_filters( 'wc_membership_get_profile_field_definition', $profile_field_definition, $profile_field_slug );

		return $profile_field_definition instanceof Profile_Field_Definition ? $profile_field_definition : null;
	}


	/**
	 * Gets profile field definitions.
	 *
	 * @since 1.19.0
	 *
	 * @param array $args {
	 *   @type int[] $membership_plan_ids: profile fields that apply to specific plans only
	 *   @type bool $required: whether to include or exclude required fields
	 *   @type string $editable_by: admin|customer to filter profile fields by user editability
	 *   @type string|string[] $visibility: limit to fields of specific visibility
	 *   @type string|string[] $type: limit to fields of specific type(s)
	 * }
	 * @return Profile_Field_Definition[] array sorted by profile field slugs
	 */
	public static function get_profile_field_definitions( array $args = [] ) {

		$query_args = wp_parse_args( $args, [
			'membership_plan_ids' => [],
			'required'            => null,
			'visibility'          => [],
			'editable_by'         => '',
			'type'                => '',
		] );

		$cache_key = http_build_query( $query_args );

		if ( isset( self::$profile_field_definitions[ $cache_key ] ) ) {
			return self::$profile_field_definitions[ $cache_key ];
		}

		$definitions_store = new Option();
		$raw_definitions   = $definitions_store->get_profile_field_definitions_data();

		$profile_field_definitions = [];

		foreach ( $raw_definitions as $raw_definition ) {

			try {
				$profile_field_definition = new Profile_Field_Definition( $raw_definition );
			} catch ( \Exception $e ) {
				continue;
			}

			if ( ! empty( $query_args['editable_by'] ) && $query_args['editable_by'] !== $profile_field_definition->get_editable_by() ) {
				continue;
			}

			if ( ! empty( $query_args['type'] ) && ! $profile_field_definition->is_type( $query_args['type'] ) ) {
				continue;
			}

			if ( ! empty( $query_args['membership_plan_ids'] ) && ! empty( $profile_field_definition->get_membership_plan_ids() ) && empty( array_intersect( array_map( 'intval', (array) $query_args['membership_plan_ids'] ), $profile_field_definition->get_membership_plan_ids() ) ) ) {
				continue;
			}

			if ( ! empty( $query_args['visibility'] ) && empty( array_intersect( array_map( 'strval', (array) $query_args['visibility'] ), $profile_field_definition->get_visibility() ) ) ) {
				continue;
			}

			if ( is_bool( $query_args['required'] ) && wc_bool_to_string( $query_args['required'] ) !== $profile_field_definition->get_required() ) {
				continue;
			}

			$profile_field_definitions[ $profile_field_definition->get_slug() ] = $profile_field_definition;
		}

		self::$profile_field_definitions[ $cache_key ] = $profile_field_definitions;

		/**
		 * Filters the queried profile field definitions.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field_Definition[] array of found profile field definition objects
		 * @param array $query_args associative array of query arguments
		 */
		return (array) apply_filters( 'wc_memberships_get_profile_field_definitions', $profile_field_definitions, $query_args );
	}


	/**
	 * Gets profile fields.
	 *
	 * Optionally some arguments can be defined to return specific fields only:
	 *
	 * @since 1.19.0
	 *
	 * @param int $user_id a WP User ID
	 * @param array $args optional array of arguments {
	 *   @type int[] $membership_plan_ids: limit only for fields matching plans
	 *   @type bool $required: whether to include or exclude required fields
	 *   @type string $editable_by: whether to return only fields editable by a type of user
	 *   @type string|string[] $visibility: limit to fields of specific visibility
	 *   @type string|string[] $type: limit to fields of specific type(s)
	 * }
	 * @return Profile_Field[] array of profile fields indexed by slugs
	 */
	public static function get_profile_fields( $user_id, array $args = [] ) {

		$user_id        = absint( $user_id );
		$profile_fields = [];

		if ( 0 === $user_id ) {
			return $profile_fields;
		}

		$query_args = wp_parse_args( $args, [
			'membership_plan_ids' => [],
			'required'            => null,
			'visibility'          => [],
			'editable_by'         => '',
		] );

		$cache_key = http_build_query( $query_args );

		if ( isset( self::$profile_fields[ $user_id ][ $cache_key ] ) ) {
			return self::$profile_fields[ $user_id ][ $cache_key ];
		}

		foreach ( self::get_profile_field_definitions( $query_args ) as $profile_field_definition ) {

			$profile_field = self::get_profile_field( $user_id, $profile_field_definition->get_slug() );

			if ( ! $profile_field ) {
				continue;
			}

			$profile_fields[ $profile_field->get_slug() ] = $profile_field;
		}

		self::$profile_fields[ $user_id ][ $cache_key ] = $profile_fields;

		/**
		 * Filters the queried profile filters.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field[] array of found profile field objects
		 * @param int $user_id the user ID to retrieve profile fields for
		 * @param array $query_args array of query arguments
		 */
		return (array) apply_filters( 'wc_memberships_get_profile_fields', $profile_fields, $user_id, $query_args );
	}


	/**
	 * Determines whether a slug is a valid profile field slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $profile_field_slug slug
	 * @return bool
	 */
	public static function is_profile_field_slug( $profile_field_slug ) {

		return array_key_exists( $profile_field_slug, self::get_profile_field_definitions() );
	}


	/**
	 * Determines whether a field type is a valid one.
	 *
	 * @since 1.19.0
	 *
	 * @param string $type profile field definition type
	 * @return bool
	 */
	public static function is_valid_field_type( $type ) {

		return in_array( $type, self::get_profile_field_types( false ), true );
	}


	/**
	 * Determines if there’s at least one profile definition in database.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public static function is_using_profile_fields() {

		return ! empty( self::get_profile_field_definitions() );
	}


	/**
	 * Gets a storage profile field user meta key from a slug.
	 *
	 * @since 1.19.0
	 *
	 * @param string $profile_field_slug a profile field slug
	 * @return string the formatted profile field meta key
	 */
	public static function get_profile_field_user_meta_key( $profile_field_slug ) {

		if ( empty( $profile_field_slug ) || ! is_string( $profile_field_slug ) ) {
			return '';
		}

		$user_meta     = new User_Meta();
		$profile_field = new Profile_Field();

		$profile_field->set_slug( $profile_field_slug );

		return $user_meta->get_meta_key( $profile_field );
	}


	/**
	 * Maybe initializes the WooCommerce session.
	 *
	 * To handle file uploads, we may have to store the uploaded files references into session, which only comes available if the current user has items in cart.
	 *
	 * @since 1.19.0
	 */
	private static function maybe_init_session() {

		if ( ! WC()->session ) {
			WC()->initialize_session();
		}

		if ( ! WC()->customer || ! WC()->cart ) {
			WC()->initialize_cart();
		}

		if ( ! WC()->session->has_session() || ! WC()->session->get_session_cookie() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}


	/**
	 * Determines whether the session has started for the current customer.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	private static function customer_has_session() {

		return WC()->session && WC()->session->has_session();
	}


	/**
	 * Stores a reference to the uploaded file in the customer's session.
	 *
	 * @see \WC_Memberships_AJAX::upload_member_profile_field_file()
	 *
	 * @since 1.19.0
	 *
	 * @param string $slug profile field slug identifier
	 * @param string|int $file attachment identifier
	 */
	public static function store_uploaded_profile_field_file_in_session( $slug, $file ) {

		self::maybe_init_session();

		$session_data = (array) WC()->session->get( self::$session_key, [] );
		$session_data['files'][ $slug ] = $file;

		WC()->session->set( self::$session_key, $session_data );
	}


	/**
	 * Gets the uploaded profile field files from session.
	 *
	 * @since 1.19.0
	 *
	 * @return array associative array of file identifiers indexed by the corresponding profile field slug
	 */
	public static function get_uploaded_profile_field_files_from_session() {

		$files = [];

		// do not bother if the session hasn't even started yet
		if ( ! self::customer_has_session() ) {
			return $files;
		}

		$session_key = self::$session_key;

		if ( isset( WC()->session->{$session_key}['files'] ) ) {
			$files = WC()->session->{$session_key}['files'];
		}

		return is_array( $files ) ? $files : [];
	}


	/**
	 * Gets an uploaded profile field file from session.
	 *
	 * @since 1.19.0
	 *
	 * @param string $slug profile field slug
	 * @return string|int file identifier
	 */
	public static function get_uploaded_profile_field_file_from_session( $slug ) {

		$files = self::get_uploaded_profile_field_files_from_session();

		return isset( $files[ $slug ] ) ? $files[ $slug ] : '';
	}


	/**
	 * Removes references to a profile field's uploaded file from the customer's session.
	 *
	 * Verifies that the attachment identifier matches the one in session to prevent removal of another file.
	 *
	 * @since 1.19.0
	 *
	 * @param string $slug profile field slug
	 * @param string|int $file attachment identifier
	 * @param bool $delete_attachment whether to also delete the attachment (default false)
	 * @return bool success
	 */
	public static function remove_uploaded_profile_field_file_from_session( $slug, $file, $delete_attachment = false ) {

		// do not bother if the session hasn't even started yet
		if ( ! self::customer_has_session() ) {
			return false;
		}

		$session_data = (array) WC()->session->get( self::$session_key, [] );
		$success      = isset( $session_data['files'][ $slug ] ) && (string) $file === (string) $session_data['files'][ $slug ];

		if ( $success ) {

			unset( $session_data['files'][ $slug ] );

			if ( $delete_attachment ) {
				wp_delete_attachment( $file, true );
			}
		}

		WC()->session->set( self::$session_key, $session_data );

		return $success;
	}


	/**
	 * Removes references to profile fields uploaded files from the customer's session.
	 *
	 * Also used as callback when the session is wiped periodically.
	 * @see \WC_Memberships::__construct()
	 *
	 * @since 1.19.0
	 *
	 * @param bool $delete_attachments whether to also delete attachments from database (default true)
	 */
	public static function remove_uploaded_profile_field_files_from_session( $delete_attachments = true ) {

		// do not bother if the session hasn't even started yet
		if ( ! self::customer_has_session() ) {
			return;
		}

		$session_data = (array) WC()->session->get( self::$session_key, [] );
		$files        = isset( $session_data['files'] ) ? $session_data['files'] : [];

		if ( ! empty( $files ) ) {

			if ( $delete_attachments ) {

				$user_id = WC()->session->get_customer_id();

				foreach ( $files as $slug => $file ) {

					// sanity check: skip if by any chance the profile field was saved matching with the attachment identifier
					if ( is_numeric( $user_id ) && $user_id > 0 ) {

						$profile_field = self::get_profile_field( $user_id, $slug );
						$saved_file_id = $profile_field ? $profile_field->get_value() : '';

						if ( ! empty( $saved_file_id ) && (string) $file === (string) $saved_file_id ) {
							continue;
						}
					}

					wp_delete_attachment( $file, true );
				}
			}

			unset( $session_data['files'] );
		}

		WC()->session->set( self::$session_key, $session_data );
	}


	/**
	 * Clears memberships profile fields data from the customer's session.
	 *
	 * Also used as callback when the session is cleared upon checkout.
	 * @see \WC_Memberships::__construct()
	 *
	 * @since 1.19.0
	 */
	public static function clear_profile_fields_session_data() {

		unset( WC()->session->{self::$session_key} );
	}


	/**
	 * Moves any files uploaded via profile fields form fields submission from the WordPress general uploads folder to the member private uploads folder.
	 *
	 * This is necessary because a user may not be registered or have a membership at the time when a file has been uploaded and an attachment created in WordPress.
	 * Therefore, we move these files to keep them distinguished from regular WordPress uploads and to add some extra security measure in those folders.
	 *
	 * The folder where all the member files will be stored is `<wp-content/uploads/>memberships_profile_fields/<user_id>`, where:
	 * - <wp-content/uploads> is the WordPress media upload directory (defaults to `wp-content/uploads`)
	 * - <user_id> is the ID of the user the membership belongs to (profile fields are per member-user)
	 *
	 * TODO: consider moving all file related profile fields methods to a separate class {WV 2020-09-11}
	 *
	 * @see \WC_Memberships_Upgrade::upgrade_to_1_19_0()
	 * @see \WC_Memberships_Upgrade::create_access_protected_uploads_dir()
	 *
	 * Note: The method will also set the ownership of file attachments to the user of the membership.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership
	 * @param Profile_Field[] $profile_fields array of profile fields of file type
	 */
	public static function move_uploaded_profile_fields_files_to_member_profile_fields_folder( \WC_Memberships_User_Membership $user_membership, array $profile_fields ) {

		$user_id     = $user_membership->get_user_id();
		$upload_dir  = wp_upload_dir();
		$upload_path = trailingslashit( $upload_dir['basedir'] );
		$user_dir    = "memberships_profile_fields/{$user_id}";
		$user_path   = $upload_path . trailingslashit( $user_dir );

		\WC_Memberships_Upgrade::create_access_protected_uploads_dir( $user_dir );

		foreach ( $profile_fields as $profile_field ) {

			$attachment_id   = $profile_field->get_value();
			$attachment_data = wp_get_attachment_metadata( $attachment_id );

			// non-image files such as PDF documents may not have attachment metadata
			if ( ! is_array( $attachment_data ) ) {
				$attachment_data = [];
			}

			// grab the file name without path (for YYYY/MM uploads folder structure)
			$file_path     = get_attached_file( $attachment_id );
			$file_array    = $file_path ? explode( '/', $file_path ) : [];
			$size_name     = array_pop( $file_array );
			$relative_path = trailingslashit( implode( '/', $file_array ) );

			// move the main file (we don't use a YYYY/MM structure in profile fields)
			$old_file_path = $upload_path . $file_path;
			$new_file_path = $user_path   . $size_name;

			// sanity checks: bail if attachment file doesn't exist
			if ( ! $size_name || ! file_exists( $old_file_path ) ) {
				continue;
			}

			$success = rename( $old_file_path, $new_file_path );

			if ( ! $success ) {
				continue;
			}

			// update file path in attachment metadata
			if ( isset( $attachment_data['file'] ) ) {
				$attachment_data['file'] = $new_file_path;
			}

			// move any thumbnails (images only)
			if ( ! empty( $attachment_data['sizes'] ) ) {

				foreach ( $attachment_data['sizes'] as $size => $sizes_data ) {

					$size_name     = $sizes_data['file'];
					$old_size_path = $upload_path . $relative_path . $size_name;
					$new_size_path = $user_path   . $size_name;

					if ( ! file_exists( $old_size_path ) ) {
						continue;
					}

					// move thumbnail
					rename( $old_size_path, $new_size_path );
				}
			}

			// set the ownership of the file to the member user and make it private
			wp_update_post( [
				'ID'          => $attachment_id,
				'post_author' => $user_id,
				'post_status' => 'private',
			] );

			// update main attachment data (main file including path)
			update_attached_file( $attachment_id, $new_file_path );
			// update attachment meta data (sizes, file paths...)
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			// for sanity, remove this file from the session, if found, as at this point is no longer temporary
			self::remove_uploaded_profile_field_file_from_session( $profile_field->get_slug(), $attachment_id );
		}
	}


}
