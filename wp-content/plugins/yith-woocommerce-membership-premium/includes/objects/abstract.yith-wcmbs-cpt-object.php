<?php

// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

if ( ! class_exists( 'YITH_WCMBS_CPT_Object' ) ) {
	abstract class YITH_WCMBS_CPT_Object {
		/** @var array */
		protected $object_data = array(
			'version' => '',
		);

		/**
		 * The version of the object.
		 * This is useful to force saving all metas when changing version.
		 *
		 * @var string
		 */
		protected $object_version = YITH_WCMBS_VERSION;

		/** @var array */
		protected $data = array();

		/** @var array */
		protected $props_to_meta_keys = array();

		/** @var array */
		protected $extra_data = array();

		/** @var array */
		protected $default_data = array();

		/** @var array */
		protected $changes = array();

		/** @var array */
		protected $int_to_string_array_props = array();

		/** @var array */
		protected $boolean_props = array();

		/** @var string */
		protected $post_type = '';

		/** @var int */
		protected $id;

		/** @var string */
		protected $object_type = 'cpt_object';

		/** @var bool */
		protected $object_read = false;

		/** @var WP_Post */
		protected $post_object;

		/**
		 * YITH_WCMBS_CPT_Object constructor.
		 */
		public function __construct( $obj ) {
			$this->data         = array_merge( $this->object_data, $this->data, $this->extra_data );
			$this->default_data = $this->data;

			if ( is_numeric( $obj ) && $obj > 0 ) {
				$this->set_id( $obj );
			} elseif ( $obj instanceof self ) {
				$this->set_id( absint( $obj->get_id() ) );
			} elseif ( ! empty( $obj->ID ) ) {
				$this->set_id( absint( $obj->ID ) );
			}

			if ( $this->get_id() ) {
				if ( ! $this->post_type || $this->post_type === get_post_type( $this->get_id() ) ) {
					$this->populate_props();
					$this->object_read = true;
				} else {
					$this->set_id( 0 );
				}
			}
		}

		/**
		 * __get function for backward compatibility.
		 * Allows to retrieve WP_Post params
		 *
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( is_null( $this->post_object ) ) {
				$this->post_object = get_post( $this->get_id() );
			}
			$value      = ! ! $this->post_object && isset( $this->post_object->$key ) ? $this->post_object->$key : false;
			$this->$key = $value;

			return $value;
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'yith_wcmbs_' . $this->object_type . '_get_';
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook() {
			return 'yith_wcmbs_' . $this->object_type . '_get';
		}

		/**
		 * Return data changes only
		 *
		 * @return array
		 */
		public function get_changes() {
			return $this->changes;
		}

		/**
		 * get object properties
		 *
		 * @param string $prop
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return mixed
		 */
		protected function get_prop( $prop, $context = 'view' ) {
			$value = null;

			if ( array_key_exists( $prop, $this->data ) ) {
				$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

				if ( 'view' === $context ) {
					$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
					$value = apply_filters( $this->get_hook(), $value, $prop, $this );
				}
			}

			return $value;
		}

		/**
		 * @param $prop
		 *
		 * @return mixed|string
		 */
		protected function get_meta_by_prop( $prop ) {
			return array_key_exists( $prop, $this->props_to_meta_keys ) ? $this->props_to_meta_keys[ $prop ] : '_' . $prop;
		}

		/**
		 * Allows to validate props if overridden
		 */
		protected function validate_props() {

		}

		/**
		 * Populate all props
		 */
		protected function populate_props() {
			foreach ( $this->data as $prop => $default_value ) {
				if ( 'name' === $prop ) {
					// not use get_the_title to prevent issues with 'the_title' filters
					$post  = get_post( $this->get_id() );
					$value = isset( $post->post_title ) ? $post->post_title : '';
				} elseif ( $this->is_extra_prop( $prop ) ) {
					$value = $this->get_extra_data_from_db( $prop, $default_value );
				} else {
					$value = $this->get_data_from_db( $prop, $default_value );
				}
				$setter = "set_{$prop}";
				if ( method_exists( $this, $setter ) ) {
					$this->$setter( $value );
				} else {
					$this->set_prop( $prop, $value );
				}
			}
			$this->validate_props();
			$this->apply_changes();
		}

		/**
		 * Is this an extra prop?
		 *
		 * @param string $prop
		 *
		 * @return bool
		 */
		protected function is_extra_prop( $prop ) {
			return in_array( $prop, array_keys( $this->extra_data ) );
		}

		/**
		 * Retrieve data from DB
		 *
		 * @param string $prop
		 * @param mixed  $default
		 *
		 * @return mixed
		 */
		protected function get_data_from_db( $prop, $default ) {
			$meta = $this->get_meta_by_prop( $prop );

			return metadata_exists( 'post', $this->get_id(), $meta ) ? get_post_meta( $this->get_id(), $meta, true ) : $default;
		}

		/**
		 * Retrieve extra data from DB.
		 * It should be overridden in the specific class
		 *
		 * @param string $prop
		 * @param mixed  $default
		 *
		 * @return mixed
		 */
		protected function get_extra_data_from_db( $prop, $default ) {
			return $this->get_data_from_db( $prop, $default );
		}

		/**
		 * set an object property
		 *
		 * @param string $prop
		 * @param mixed  $value the value
		 */
		protected function set_prop( $prop, $value ) {
			if ( array_key_exists( $prop, $this->data ) ) {
				if ( true === $this->object_read ) {
					if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
						$this->changes[ $prop ] = $value;
					}
				} else {
					$this->data[ $prop ] = $value;
				}
			}
		}

		/**
		 * set object properties
		 *
		 * @param array $props
		 */
		public function set_props( $props ) {
			foreach ( $props as $key => $value ) {
				$setter = 'set_' . $key;
				if ( is_callable( array( $this, $setter ) ) ) {
					$this->$setter( $value );
				}
			}
		}

		/**
		 * Merge changes with data and clear.
		 */
		public function apply_changes() {
			$this->data    = array_replace_recursive( $this->data, $this->changes );
			$this->changes = array();
		}

		/**
		 * Is this a prop defined as "int_to_string_array"
		 *
		 * @param string $prop
		 *
		 * @return bool
		 */
		protected function is_int_to_string_array_prop( $prop ) {
			return in_array( $prop, $this->int_to_string_array_props, true );
		}

		/**
		 * Is this a boolean prop?
		 *
		 * @param string $prop
		 *
		 * @return bool
		 */
		protected function is_boolean_prop( $prop ) {
			return in_array( $prop, $this->boolean_props, true );
		}

		/**
		 * Merge changes with data and clear.
		 *
		 * @param bool $force
		 */
		protected function update_post_meta( $force = false ) {
			$props_to_update = ! $force ? $this->get_changes() : $this->data;
			foreach ( $props_to_update as $prop => $value ) {

				if ( $this->is_int_to_string_array_prop( $prop ) && is_array( $value ) ) {
					// "int_to_string_array" props are useful to store the valued as a serialized array of strings (instead of int).
					// This will prevent issues with queries to search through the serialize.
					$value = array_map( 'strval', $value );
				}

				if ( 'name' === $prop ) {
					$post_data = array(
						'post_title' => $value,
					);
					/**
					 * When updating this object, to prevent infinite loops, use $wpdb
					 * to update data, since wp_update_post spawns more calls to the
					 * save_post action.
					 * This ensures hooks are fired by either WP itself (admin screen save),
					 * or an update purely from CRUD.
					 */
					if ( doing_action( 'save_post' ) ) {
						$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $this->get_id() ) );
					} else {
						wp_update_post( array_merge( array( 'ID' => $this->get_id() ), $post_data ) );
					}
				} elseif ( $this->is_extra_prop( $prop ) ) {
					$this->update_extra_prop( $prop, $value );
				} else {
					$meta = $this->get_meta_by_prop( $prop );
					update_post_meta( $this->get_id(), $meta, $value );
				}
			}
		}

		/**
		 * Update extra data to DB.
		 * It should be overridden in the specific class
		 *
		 * @param string $prop
		 * @param mixed  $value
		 */
		protected function update_extra_prop( $prop, $value ) {
			$meta = $this->get_meta_by_prop( $prop );
			update_post_meta( $this->get_id(), $meta, $value );
		}

		/**
		 * Update changes before saving when we're saving a new version of the object.
		 * This will ensure all fields will be forced to be saved!
		 */
		protected function maybe_update_changes_before_saving() {
			if ( ! $this->check_version() ) {
				$this->set_version();
				foreach ( $this->data as $prop => $default ) {
					if ( ! array_key_exists( $prop, $this->changes ) && $this->is_prop_to_force_updating_for_new_versions( $prop ) ) {
						$this->changes[ $prop ] = $default;
					}
				}
			}
		}

		/**
		 * Is this a prop we need to force-saving for new versions of the object?
		 *
		 * @param string $prop
		 *
		 * @return bool
		 */
		protected function is_prop_to_force_updating_for_new_versions( $prop ) {
			return ! $this->is_extra_prop( $prop );
		}

		/**
		 * Store options in DB
		 *
		 * @param bool $force
		 *
		 * @return int
		 */
		public function save( $force = false ) {
			$this->validate_props();

			do_action( 'yith_wcmbs_before_' . $this->object_type . '_object_save', $this );

			if ( $force ) {
				$this->set_version();
				$this->apply_changes();
				$this->update_post_meta( true );
			} else {
				$this->maybe_update_changes_before_saving();
				$this->update_post_meta();
				$this->apply_changes();
			}

			do_action( 'yith_wcmbs_after_' . $this->object_type . '_object_save', $this );

			return $this->get_id();
		}

		/**
		 * get the object ID
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * set the object ID
		 *
		 * @param $id
		 */
		public function set_id( $id ) {
			$this->id = absint( $id );
		}

		/**
		 * Return the version
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_version( $context = 'view' ) {
			return $this->get_prop( 'version', $context );
		}

		/**
		 * set the object version
		 *
		 * @param string|false $value
		 */
		protected function set_version( $value = false ) {
			$value = false !== $value ? $value : $this->object_version;
			$this->set_prop( 'version', $value );
		}

		/**
		 * @return bool
		 */
		public function is_valid() {
			return ! ! $this->get_id() && ( ! $this->post_type || $this->post_type === get_post_type( $this->get_id() ) );
		}

		/**
		 * trash the related Post
		 */
		public function trash() {
			return wp_trash_post( $this->get_id() );
		}

		/**
		 * delete the related Post
		 */
		public function delete() {
			return wp_delete_post( $this->get_id() );
		}

		/**
		 * Return the post_status of the Store
		 *
		 * @return string
		 */
		public function get_post_status() {
			return get_post_status( $this->get_id() );
		}

		/**
		 * return the data
		 *
		 * @return array
		 */
		public function get_data() {
			return array_merge( $this->data, array( 'id' => $this->get_id() ) );
		}

		/**
		 * return the current data
		 *
		 * @return array
		 */
		public function get_current_data() {
			$current_data = array_replace_recursive( $this->data, $this->changes );

			return array_merge( $current_data, array( 'id' => $this->get_id() ) );
		}

		/**
		 * return default data
		 *
		 * @return array
		 */
		public function get_default_data() {
			return $this->default_data;

		}

		public function is_internal_meta_key( $key ) {
			$internal_meta_key = ! empty( $key ) && in_array( $key, array_keys( $this->data ), true );
			if ( ! $internal_meta_key ) {
				return false;
			}

			$has_setter_or_getter = is_callable( array( $this, 'set_' . $key ) ) || is_callable( array( $this, 'get_' . $key ) );

			if ( ! $has_setter_or_getter ) {
				return false;
			}

			return true;
		}

		/**
		 * Return true if the object was created with the latest version
		 *
		 * @return bool|int
		 */
		public function check_version() {
			return version_compare( $this->get_version(), $this->object_version, '>=' );
		}

		/**
		 * Filter an array only for internal keys
		 *
		 * @param array $request The request.
		 *
		 * @return array
		 */
		public function get_props_by_request( $request = false ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = ! ! $request ? $request : wc_clean( wp_unslash( $_REQUEST ) );
			if ( isset( $request['post_title'] ) ) {
				$request['name'] = $request['post_title'];
			}
			$internal_keys = array_keys( $this->data );
			foreach ( $this->boolean_props as $boolean_prop ) {
				$request[ $boolean_prop ] = $request[ $boolean_prop ] ?? 'no';
			}

			return array_filter(
				   $request,
				function ( $key ) use ( $internal_keys ) {
					return in_array( $key, $internal_keys );
				}, ARRAY_FILTER_USE_KEY
			);
		}

		/*
		|--------------------------------------------------------------------------
		| Meta Data handlers
		|--------------------------------------------------------------------------
		*/
		/**
		 * Retrieve a meta data
		 *
		 * @param string $key The key of the meta.
		 *
		 * @return mixed
		 */
		public function get_meta_data( $key ) {
			$getter = 'get_' . $key;
			if ( $this->is_internal_meta_key( $key ) && is_callable( array( $this, $getter ) ) ) {
				return $this->{$getter}();
			}

			return get_post_meta( $this->get_id(), $key, true );
		}

		/**
		 * Update a specific meta data
		 *
		 * @param string $key   The key of the meta.
		 * @param mixed  $value The value to set.
		 */
		public function update_meta_data( $key, $value ) {
			update_post_meta( $this->get_id(), $key, $value );
		}
	}
}