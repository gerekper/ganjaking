<?php

/**
 * Post
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'SRP_Post' ) ) {

	/**
	 * SRP_Post Class.
	 */
	abstract class SRP_Post {

		/**
		 * ID
		 */
		protected $id = '' ;

		/**
		 * Post
		 */
		protected $post ;

		/**
		 * Post type
		 */
		protected $post_type = '' ;

		/**
		 * Post status
		 */
		protected $post_status = '' ;

		/**
		 * Meta data
		 */
		protected $meta_data = array() ;

		/**
		 * Meta data keys
		 */
		protected $meta_data_keys = array() ;

		/**
		 * Status
		 */
		protected $status ;

		/**
		 * Class initialization.
		 */
		public function __construct( $_id = '', $populate = true ) {
			$this->id = $_id ;

			if ( $populate && $_id ) {
				$this->populate_data() ;
			}
		}

		/**
		 * Has Status
		 */
		public function has_status( $status ) {
			$current_status = $this->get_status() ;

			if ( is_array( $status ) && in_array( $current_status , $status ) ) {
				return true ;
			}

			if ( $current_status == $status ) {
				return true ;
			}

			return false ;
		}

		/**
		 * Update Status
		 */
		public function update_status( $status ) {
			$post_args = array(
				'ID'          => $this->id,
				'post_type'   => $this->post_type,
				'post_status' => $status,
					) ;

			return wp_update_post( $post_args ) ;
		}

		/**
		 * Id
		 */
		public function get_id() {
			return $this->id ;
		}

		/**
		 * Status
		 */
		public function get_status( $bool = true ) {
			if ( $this->status ) {
				return $this->status ;
			}

			if ( $bool ) {
				return get_post_status( $this->id ) ;
			} else {
				$status_object = get_post_status_object( $this->status ) ;

				return $status_object->label ;
			}
		}

		/**
		 * Post exists
		 */
		public function exists() {
			return isset( $this->post->post_type ) && $this->post->post_type == $this->post_type ;
		}

		/**
		 * Populate Data for this post
		 */
		protected function populate_data() {
			if ( 'auto-draft' == $this->get_status() ) {
				return ;
			}

			$this->load_postdata() ;
			$this->load_metadata() ;
		}

		/**
		 * Prepare Post data
		 */
		protected function load_postdata() {
			$this->post = get_post( $this->id ) ;
			if ( ! $this->post ) {
				return ;
			}

			$this->status = $this->post->post_status ;

			$this->load_extra_postdata() ;
		}

		/**
		 * Prepare extra post data
		 */
		protected function load_extra_postdata() {
		}

		/**
		 * Prepare Post Meta data
		 */
		protected function load_metadata() {

			$meta_data_array = get_post_meta( $this->id ) ;
			if ( ! srp_check_is_array( $meta_data_array ) ) {
				return $meta_data_array ;
			}

			$new_meta_array = array() ;
			foreach ( $this->meta_data_keys as $key => $value ) {
				$this->$key = $value ;

				if ( ! isset( $meta_data_array[ $key ][ 0 ] ) ) {
					continue ;
				}

				$meta_data              = ( is_serialized( $meta_data_array[ $key ][ 0 ] ) ) ? @unserialize( $meta_data_array[ $key ][ 0 ] ) : $meta_data_array[ $key ][ 0 ] ;
				$new_meta_array[ $key ] = $meta_data ;
				$this->$key             = $meta_data ;
			}

			$this->meta_data = ( object ) $new_meta_array ;

			return $this->meta_data ;
		}

		/**
		 * Create a post
		 */
		public function create( $meta_data, $post_args = array() ) {

			$default_post_args = array(
				'post_type'   => $this->post_type,
				'post_status' => $this->post_status,
					) ;

			$post_args = wp_parse_args( $post_args , $default_post_args ) ;

			$this->id = wp_insert_post( $post_args ) ;

			$this->update_metas( $meta_data ) ;

			$this->populate_data() ;

			return $this->id ;
		}

		/**
		 * Update a post
		 */
		public function update( $meta_data, $post_args = array() ) {
			if ( ! $this->id ) {
				return false ;
			}

			$default_post_args = array(
				'ID'          => $this->id,
				'post_type'   => $this->post_type,
				'post_status' => $this->get_status(),
					) ;

			$post_args = wp_parse_args( $post_args , $default_post_args ) ;

			wp_update_post( $post_args ) ;

			$this->update_metas( $meta_data ) ;

			$this->populate_data() ;

			return $this->id ;
		}

		/**
		 * Update post metas
		 */
		public function update_metas( $meta_data ) {
			if ( ! $this->id ) {
				return false ;
			}

			foreach ( $this->meta_data_keys as $meta_key => $default ) {
				if ( ! isset( $meta_data[ $meta_key ] ) ) {
					continue ;
				}

				update_post_meta( $this->id , sanitize_key( $meta_key ) , $meta_data[ $meta_key ] ) ;
			}
		}

		/**
		 * Update post meta
		 */
		public function update_meta( $meta_key, $value ) {
			if ( ! $this->id ) {
				return false ;
			}

			update_post_meta( $this->id , sanitize_key( $meta_key ) , $value ) ;
		}
	}

}
