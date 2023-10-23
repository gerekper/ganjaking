<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * General class to manage custom post types
 *
 * @class   YITH_YWPI_Cpt_Object
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder\Abstracts
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWPI_Cpt_Object' ) ) {
	/**
	 * Abstract class
	 */
	abstract class YITH_YWPI_Cpt_Object {

		/**
		 * Array of data
		 *
		 * @var array
		 */
		protected $data = array();

		/**
		 * Post type name
		 *
		 * @var string
		 */
		protected $post_type = '';

		/**
		 * ID of post type
		 *
		 * @var int
		 */
		protected $id;

		/**
		 * Object type
		 *
		 * @var string
		 */
		protected $object_type = 'cpt_object';

		/**
		 * Object read
		 *
		 * @var bool
		 */
		protected $object_read = false;

		/**
		 * YITH_YWPI_Cpt_Object constructor.
		 *
		 * @param mixed $obj Object.
		 */
		public function __construct( $obj ) {
			if ( is_numeric( $obj ) && $obj > 0 ) {
				$this->set_id( $obj );
			} elseif ( $obj instanceof self ) {
				$this->set_id( absint( $obj->get_id() ) );
			} elseif ( ! empty( $obj->ID ) ) {
				$this->set_id( absint( $obj->ID ) );
			}

			if ( $this->get_id() ) {
				if ( ! $this->post_type || get_post_type( $this->get_id() ) === $this->post_type ) {
					$this->populate_props();
					$this->object_read = true;
				} else {
					$this->set_id( 0 );
				}
			}
		}

		/**
		 * Return the data
		 *
		 * @return array
		 */
		public function get_data() {
			return array_merge( $this->data, array( 'id' => $this->get_id() ) );
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'yith-ywpi_' . $this->object_type . '_get_';
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook() {
			return 'yith-ywpi_' . $this->object_type . '_get';
		}

		/**
		 * Get object properties
		 *
		 * @param string $prop Properties.
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return mixed
		 */
		protected function get_prop( $prop, $context = 'view' ) {
			$value = null;

			if ( array_key_exists( $prop, $this->data ) ) {
				$value = $this->data[ $prop ];

				if ( 'view' === $context ) {
					$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
					$value = apply_filters( $this->get_hook(), $value, $prop, $this );
				}
			}

			return $value;
		}

		/**
		 * Return the meta by prop
		 *
		 * @param string $prop Property name.
		 * @return string
		 */
		protected function get_meta_by_prop( $prop ) {
			return '_' . $prop;
		}

		/**
		 * Populate all props
		 */
		protected function populate_props() {
			foreach ( $this->data as $prop => $default_value ) {
				$meta   = $this->get_meta_by_prop( $prop );
				$value  = metadata_exists( 'post', $this->get_id(), $meta ) ? get_post_meta( $this->get_id(), $meta, true ) : $default_value;
				$setter = "set_{$prop}";

				if ( method_exists( $this, $setter ) ) {
					$this->$setter( $value );
				} else {
					$this->set_prop( $prop, $value );
				}
			}
		}

		/**
		 * Set an object property
		 *
		 * @param string $prop Property name.
		 * @param mixed  $value The value of the properties.
		 */
		protected function set_prop( $prop, $value ) {
			if ( array_key_exists( $prop, $this->data ) ) {
				$this->data[ $prop ] = $value;
			}
		}

		/**
		 * Set object properties
		 *
		 * @param array $props Properties.
		 */
		public function set_props( $props ) {
			foreach ( $props as $key => $value ) {
				$setter = 'set_' . $key;

				if ( is_callable( array( $this, $setter ) ) ) {
					$this->$setter( $value );
				} else {
					$this->set_prop( $key, $value );
				}
			}
		}

		/**
		 * Merge changes with data and clear.
		 */
		protected function update_post_meta() {
			$props_to_update = $this->data;

			foreach ( $props_to_update as $prop => $value ) {
				$meta = $this->get_meta_by_prop( $prop );
				update_post_meta( $this->id, $meta, $value );
			}
		}

		/**
		 * Store options in DB
		 *
		 * @return int
		 */
		public function save() {
			$this->update_post_meta();

			return $this->get_id();
		}

		/**
		 * Return the object ID
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Return the WP Post object
		 *
		 * @return int
		 */
		public function get_post() {
			return get_post( $this->id );
		}

		/**
		 * Set the object ID
		 *
		 * @param int $id ID.
		 */
		public function set_id( $id ) {
			$this->id = absint( $id );
		}

		/**
		 * Check if the post type is valid
		 *
		 * @return bool
		 */
		public function is_valid() {
			return ! ! $this->get_id() && ( ! $this->post_type || get_post_type( $this->get_id() ) === $this->post_type );
		}

		/**
		 * Trash the post
		 */
		public function trash() {
			return wp_trash_post( $this->get_id() );
		}

		/**
		 * Delete the post
		 */
		public function delete() {
			return wp_delete_post( $this->get_id() );
		}

		/**
		 * Return the post_status
		 *
		 * @return string
		 */
		public function get_post_status() {
			return get_post_status( $this->get_id() );
		}
	}
}
