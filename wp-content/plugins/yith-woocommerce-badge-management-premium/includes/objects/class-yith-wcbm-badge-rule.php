<?php
/**
 * Badge Rule Object class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Objects
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule' ) ) {
	/**
	 * Badge Rule Class
	 */
	abstract class YITH_WCBM_Badge_Rule extends WC_Data {

		/**
		 * Stores Badge Rule data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'               => '',
			'status'              => 'publish',
			'enabled'             => 'yes',
			'type'                => '',
			'schedule'            => 'no',
			'schedule_dates_from' => 0,
			'schedule_dates_to'   => 0,
			'exclude_products'    => 'no',
			'excluded_products'   => array(),
			'show_badge_to'       => 'all-users',
			'users'               => array(),
			'user_roles'          => array(),
		);

		/**
		 * Meta to props.
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'             => 'enabled',
			'_type'                => 'type',
			'_schedule'            => 'schedule',
			'_schedule_dates_from' => 'schedule_dates_from',
			'_schedule_dates_to'   => 'schedule_dates_to',
			'_exclude_products'    => 'exclude_products',
			'_excluded_products'   => 'excluded_products',
			'_show_badge_to'       => 'show_badge_to',
			'_users'               => 'users',
			'_user_roles'          => 'user_roles',
		);

		/**
		 * Badge rule object type.
		 *
		 * @var string
		 */
		protected $badge_rule_type = 'badge_rule';

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $object_type = 'badge_rule';

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'badge_rule';

		/**
		 * YITH_WCBM_Badge_Rule constructor
		 *
		 * @param int|YITH_WCBM_Badge_Rule|WP_Post $rule Rule, Rule ID or Rule Post.
		 */
		public function __construct( $rule = 0 ) {
			parent::__construct();

			$this->init_default_data();

			if ( $rule instanceof WP_Post ) {
				$this->set_id( $rule->ID );
			} elseif ( is_numeric( $rule ) && $rule > 0 ) {
				$this->set_id( $rule );
			} elseif ( $rule instanceof self ) {
				$this->set_id( $rule->get_id() );
			} else {
				$this->set_object_read( true );
			}

			$this->load_data_store();

			if ( $this->get_id() > 0 && $this->data_store && get_post_type( $this->get_id() ) === YITH_WCBM_Post_Types_Premium::$badge_rule ) {
				$this->data_store->read( $this );
			} else {
				$this->set_id( 0 );
			}
		}

		/**
		 * Load Data Store
		 */
		protected function load_data_store() {
			try {
				$this->data_store = WC_Data_Store::load( $this->data_store_object_type );
			} catch ( Exception $e ) {
				$this->data_store = false;
			}
		}

		/**
		 * Init default Data
		 */
		protected function init_default_data() {
			$now                               = time();
			$this->data['schedule_dates_from'] = $now;
			$this->data['schedule_dates_to']   = $now;
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get badge rule type
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_type( $context = 'view' ) {
			return 'edit' === $context ? $this->get_prop( 'type', $context ) : $this->badge_rule_type;
		}

		/**
		 * Get title
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_title( $context = 'view' ) {
			return $this->get_prop( 'title', $context );
		}

		/**
		 * Get status
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_status( $context = 'view' ) {
			return $this->get_prop( 'status', $context );
		}

		/**
		 * Get enabled
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_enabled( $context = 'view' ) {
			return $this->get_prop( 'enabled', $context );
		}

		/**
		 * Get schedule
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_schedule( $context = 'view' ) {
			return $this->get_prop( 'schedule', $context );
		}

		/**
		 * Get schedule_dates_from
		 *
		 * @param string $context Context.
		 *
		 * @return int
		 */
		public function get_schedule_dates_from( $context = 'view' ) {
			return $this->get_prop( 'schedule_dates_from', $context );
		}

		/**
		 * Get schedule_dates_to
		 *
		 * @param int $context Context.
		 *
		 * @return int
		 */
		public function get_schedule_dates_to( $context = 'view' ) {
			return $this->get_prop( 'schedule_dates_to', $context );
		}

		/**
		 * Get exclude_products
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_exclude_products( $context = 'view' ) {
			return $this->get_prop( 'exclude_products', $context );
		}

		/**
		 * Get excluded_products
		 *
		 * @param string $context Context.
		 *
		 * @return array
		 */
		public function get_excluded_products( $context = 'view' ) {
			return $this->get_prop( 'excluded_products', $context );
		}

		/**
		 * Get show_badge_to
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_show_badge_to( $context = 'view' ) {
			return $this->get_prop( 'show_badge_to', $context );
		}

		/**
		 * Get users
		 *
		 * @param string $context Context.
		 *
		 * @return array
		 */
		public function get_users( $context = 'view' ) {
			return $this->get_prop( 'users', $context );
		}

		/**
		 * Get user_roles
		 *
		 * @param string $context Context.
		 *
		 * @return array
		 */
		public function get_user_roles( $context = 'view' ) {
			return $this->get_prop( 'user_roles', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Methods for setting data from object.
		|
		*/

		/**
		 * Set the title property value
		 *
		 * @param string $value title value.
		 */
		public function set_title( $value ) {
			$this->set_prop( 'title', sanitize_text_field( $value ) );
		}

		/**
		 * Set the status property value
		 *
		 * @param string $value enabled value.
		 */
		public function set_status( $value ) {
			$this->set_prop( 'status', 'publish' === $value ? $value : 'trash' );
		}

		/**
		 * Set the enabled property value
		 *
		 * @param string $value enabled value.
		 */
		public function set_enabled( $value ) {
			$this->set_prop( 'enabled', wc_bool_to_string( 'yes' === $value ) );
		}

		/**
		 * Set the type property value
		 *
		 * @param string $value type value.
		 */
		public function set_type( $value ) {
			$this->set_prop( 'type', sanitize_text_field( $value ) );
		}

		/**
		 * Set the schedule property value
		 *
		 * @param string $value schedule value.
		 */
		public function set_schedule( $value ) {
			$this->set_prop( 'schedule', wc_bool_to_string( 'yes' === $value ) );
		}

		/**
		 * Set the schedule_dates_from property value
		 *
		 * @param int|string $value schedule_dates_from value.
		 */
		public function set_schedule_dates_from( $value ) {
			if ( ! ( is_numeric( $value ) || ( (string) (int) $value ) === $value ) ) {
				$value = strtotime( $value );
			}
			if ( ! $value ) {
				$value = time();
			}
			$this->set_prop( 'schedule_dates_from', strtotime( '00:00:01', absint( $value ) ) );
		}

		/**
		 * Set the schedule_dates_to property value
		 *
		 * @param int|string $value schedule_dates_to value.
		 */
		public function set_schedule_dates_to( $value ) {
			if ( ! ( is_numeric( $value ) || ( (string) (int) $value ) === $value ) ) {
				$value = strtotime( $value );
			}

			if ( $value < $this->get_schedule_dates_from() ) {
				$value = $this->get_schedule_dates_from();
			}

			$this->set_prop( 'schedule_dates_to', strtotime( '23:59:59', absint( $value ) ) );
		}

		/**
		 * Set the exclude_products property value
		 *
		 * @param string $value exclude_products value.
		 */
		public function set_exclude_products( $value ) {
			$this->set_prop( 'exclude_products', wc_bool_to_string( 'yes' === $value ) );
		}

		/**
		 * Set the excluded_products property value
		 *
		 * @param array $value Excluded products.
		 */
		public function set_excluded_products( $value ) {
			$this->set_prop( 'excluded_products', array_filter( array_map( 'absint', $value ) ) );
		}

		/**
		 * Set the show_badge_to property value
		 *
		 * @param string $value weather show badge to all users or to specific ones.
		 */
		public function set_show_badge_to( $value ) {
			$this->set_prop( 'show_badge_to', sanitize_text_field( $value ) );
		}

		/**
		 * Set the users property value
		 *
		 * @param array $value users value.
		 */
		public function set_users( $value ) {
			$this->set_prop( 'users', array_filter( array_map( 'absint', $value ) ) );
		}

		/**
		 * Set the user_roles property value
		 *
		 * @param array $value user_roles value.
		 */
		public function set_user_roles( $value ) {
			$this->set_prop( 'user_roles', array_filter( $value ) );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Methods
		|--------------------------------------------------------------------------
		|
		| Methods that interacts with the CRUD.
		|
		*/

		/**
		 * Save data to the database.
		 *
		 * @return int Badge Rule ID
		 */
		public function save() {
			if ( $this->data_store ) {
				// Trigger action before saving to the DB. Allows you to adjust object props before save.
				do_action( 'yith_wcbm_before_' . $this->object_type . '_object_save', $this, $this->data_store );

				if ( $this->get_id() ) {
					$this->data_store->update( $this );
				} else {
					$this->data_store->create( $this );
				}
				do_action( 'yith_wcbm_' . $this->object_type . '_object_save', $this, $this->data_store );
			}

			return $this->get_id();
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		| Checks if a condition is true or false.
		|
		*/

		/**
		 * Check if the badge Rule is Scheduled
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return 'yes' === $this->get_enabled();
		}

		/**
		 * Check if the badge Rule is Scheduled
		 *
		 * @return bool
		 */
		public function is_scheduled() {
			return 'yes' === $this->get_schedule();
		}

		/**
		 * Check if the rule is valid according to schedule options
		 *
		 * @return bool
		 */
		public function validate_schedule() {
			$validate = true;
			if ( $this->is_scheduled() ) {
				$now      = time();
				$validate = $this->get_schedule_dates_from() <= $now && $this->get_schedule_dates_to() >= $now;
			}

			return $validate;
		}

		/**
		 * Check if the rule is valid for all users
		 *
		 * @return bool
		 */
		protected function is_valid_for_all_users() {
			return 'all-users' === $this->get_show_badge_to();
		}

		/**
		 * Check if the user is in allowed users array.
		 * If the rule is valid for all user is always true
		 *
		 * @param int $user_id User ID.
		 *
		 * @return bool
		 */
		public function in_allowed_users( $user_id = 0 ) {
			$allowed = $this->is_valid_for_all_users();
			$user_id = absint( ! ! $user_id ? $user_id : get_current_user_id() );
			if ( ! $allowed && $user_id && in_array( $user_id, $this->get_users(), true ) ) {
				$allowed = true;
			}

			return $allowed;
		}

		/**
		 * Check if the User roles are in allowed user roles array.
		 * If the rule is valid for all user is always true
		 *
		 * @param array $user_roles User roles.
		 *
		 * @return bool
		 */
		public function in_allowed_user_roles( $user_roles ) {
			$allowed_user_roles = $this->get_user_roles();
			$is_guest_allowed   = in_array( 'yith-wcbm-guest', $allowed_user_roles, true );
			$allowed            = $this->is_valid_for_all_users() || ( ! is_user_logged_in() && $is_guest_allowed );

			if ( ! $allowed ) {
				$user_roles = ! is_array( $user_roles ) || ! $user_roles ? yith_wcbm_get_user_roles() : $user_roles;
				if ( array_intersect( $user_roles, $allowed_user_roles ) ) {
					$allowed = true;
				}
			}

			return $allowed;
		}

		/**
		 * Check if the badge rule is valid for the User
		 *
		 * @param int $user_id User ID.
		 *
		 * @return bool
		 */
		public function is_valid_for_user( $user_id = 0 ) {
			$valid = $this->is_valid_for_all_users();
			if ( ! $valid && $this->get_show_badge_to() === 'specific-users' ) {
				$user_id = absint( ! $user_id ? $user_id : get_current_user_id() );
				$valid   = ( $user_id && $this->in_allowed_users( $user_id ) ) || $this->in_allowed_user_roles( yith_wcbm_get_user_roles( $user_id ) );
			}

			return apply_filters( 'yith_wcbm_badge_rule_is_valid_for_user', $valid, $user_id, $this );
		}

		/**
		 * Check if the rule has excluded products
		 *
		 * @return bool
		 */
		public function has_excluded_products() {
			return 'yes' === $this->get_exclude_products() && $this->get_excluded_products();
		}

		/**
		 * Check if the badge rule is valid for the Product
		 *
		 * @param int $product_id User ID.
		 *
		 * @return bool
		 */
		public function is_valid_for_product( $product_id = 0 ) {
			$valid = ! $this->has_excluded_products();
			if ( ! $valid ) {
				$product_id = absint( ! $product_id ? $product_id : wc_get_product( get_post() )->get_id() ?? false );
				$product    = $product_id ? wc_get_product( $product_id ) : false;
				if ( $product && $product->is_type( 'variation' ) ) {
					$product_id = $product->get_parent_id();
				}
				$valid = $product_id && ! in_array( $product_id, $this->get_excluded_products(), true );
			}

			return apply_filters( 'yith_wcbm_badge_rule_is_valid_for_product', $valid, $this, $product_id );
		}

		/**
		 * Check if badge rule is valid for the current user and product
		 *
		 * @param int $product_id Product ID.
		 * @param int $user_id    User ID.
		 *
		 * @return bool
		 */
		public function is_valid( $product_id = false, $user_id = false ) {
			static $results = array();

			$product    = wc_get_product( $product_id );
			$product_id = $product ? $product->get_id() : false;

			if ( ! $user_id ) {
				$user_id = is_user_logged_in() ? get_current_user_id() : 'guest';
			}

			$validation_key = md5( $this->get_id() . (string) $product_id . (string) $user_id );

			if ( ! array_key_exists( $validation_key, $results ) ) {
				$results[ $validation_key ] = $this->is_enabled() && $this->validate_schedule() && $this->is_valid_for_user( $user_id ) && $this->is_valid_for_product( $product_id );
			}

			return $results[ $validation_key ];
		}

		/**
		 * Check if the product is excluded according to the rule options
		 *
		 * @param int|false $product_id The product ID.
		 *
		 * @return bool
		 */
		public function is_product_excluded( $product_id = false ) {
			$excluded = false;
			if ( $this->has_excluded_products() ) {
				$product = wc_get_product( $product_id );
				if ( $product && $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}
				$excluded = in_array( $product->get_id(), $this->get_excluded_products(), true );
			}

			return $excluded;
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'yith_wcbm_badge_rule_get_';
		}

		/**
		 * Type for action and filter hooks.
		 *
		 * @return string
		 */
		public function get_type_for_hooks() {
			return preg_replace( '/[^a-zA-Z_0-9]/m', '_', $this->get_type() );
		}

		/**
		 * Get internal props from request array
		 *
		 * @return array
		 */
		public function get_internal_props_from_request() {
			if ( isset( $_REQUEST['yith_wcbm_badge_rule_security'], $_REQUEST['yith_wcbm_badge_rule'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith_wcbm_badge_rule_security'] ) ), 'yith_wcbm_save_badge_rule' ) ) {
				$changes            = $this->changes;
				$this->changes      = array();
				$defaults           = array(
					'_enabled'          => 'no',
					'_exclude_products' => 'no',
					'_schedule'         => 'no',
				);
				$props_from_request = wp_parse_args( wp_unslash( $_REQUEST['yith_wcbm_badge_rule'] ), $defaults ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$meta_to_props      = $this->meta_key_to_props;
				foreach ( $props_from_request as $prop_name => $value ) {
					if ( array_key_exists( $prop_name, $meta_to_props ) ) {
						$setter = 'set_' . $meta_to_props[ $prop_name ];
						if ( method_exists( $this, $setter ) ) {
							$this->$setter( $value );
						}
					}
				}
				$props         = $this->get_changes();
				$this->changes = $changes;
			}

			return $props;
		}

		/**
		 * Get Data to store in Lookup table
		 *
		 * @return array
		 */
		public function get_associations_rows() {
			return array();
		}

	}
}
