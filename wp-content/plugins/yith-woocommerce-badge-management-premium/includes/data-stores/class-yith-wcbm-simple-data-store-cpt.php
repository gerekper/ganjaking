<?php
/**
 * Simple Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\DataStores
 */

if ( ! class_exists( 'YITH_WCBM_Simple_Data_Store_CPT' ) ) {
	/**
	 * Simple Data Store CPT Class
	 */
	abstract class YITH_WCBM_Simple_Data_Store_CPT extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array();

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_type = 'simple';

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_post_type = 'post';

		/**
		 * Stores updated props.
		 *
		 * @var array
		 */
		protected $updated_props = array();

		/**
		 * Stores notice/warning/success messages.
		 *
		 * @var array
		 */
		protected $messages = array();

		/*
		|--------------------------------------------------------------------------
		| CRUD Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Create a new Object in the database.
		 *
		 * @param Object $object The object.
		 */
		public function create( &$object ) {
			$id = wp_insert_post(
				apply_filters(
					'yith_wcbm_new_' . $this->object_type . '_data',
					array(
						'post_type'   => $this->object_post_type,
						'post_status' => 'publish',
						'post_title'  => $object->get_title(),
					)
				),
				true
			);

			if ( $id && ! is_wp_error( $id ) ) {
				$object->set_id( $id );

				$this->update_post_meta( $object, true );

				$object->save_meta_data();
				$object->apply_changes();

				do_action( 'yith_wcbm_new_' . $this->object_type, $id, $object );
			}

			return $id;
		}

		/**
		 * Read Object data from the database.
		 *
		 * @param Object $object The Object.
		 */
		public function read( &$object ) {
			$object->set_defaults();
			$post_object = $object->get_id() ? wp_cache_get( 'yith_wcbm_badge_' . $object->get_id(), 'yith_wcbm_badges' ) : false;

			if ( ! $post_object ) {
				$post_object = get_post( $object->get_id() );

				if ( ( ! $post_object || $this->object_post_type !== $post_object->post_type ) && isset( $this->messages['invalid_data'] ) ) {
					return;
				} else {
					$object->set_props(
						array(
							'title'  => $post_object->post_title,
							'status' => $post_object->post_status,
						)
					);
				}

				wp_cache_set( 'yith_wcbm_badge_' . $object->get_id(), $post_object, 'yith_wcbm_badges' );
			}

			$object->set_id( $post_object->ID );

			$this->read_post_meta( $object );
			$object->set_object_read( true );

			do_action( 'yith_wcbm_' . $this->object_type . '_read', $object->get_id() );
		}

		/**
		 * Update Object in the database
		 *
		 * @param Object $object Badge.
		 */
		public function update( &$object ) {
			$object->save_meta_data();
			$changes = $object->get_changes();

			// Only update the post when the post data changes.
			if ( array_intersect( array( 'title', 'status' ), array_keys( $changes ) ) ) {
				$post_data = array(
					'post_title' => $object->get_title( 'edit' ),
					'post_type'  => $this->object_post_type,
				);

				if ( doing_action( 'save_post' ) ) {
					$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $object->get_id() ) );
					clean_post_cache( $object->get_id() );
				} else {
					wp_update_post( array_merge( array( 'ID' => $object->get_id() ), $post_data ) );
				}
				$object->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

			}

			$this->update_post_meta( $object );
		}

		/**
		 * Delete Badge Rule in the database
		 *
		 * @param Object $object Badge.
		 * @param array  $args   Arguments.
		 *
		 * @return bool|void
		 */
		public function delete( &$object, $args = array() ) {
			$id   = $object->get_id();
			$args = wp_parse_args(
				$args,
				array(
					'force_delete' => false,
				)
			);

			if ( ! $id ) {
				return;
			}

			if ( $args['force_delete'] ) {
				do_action( 'yith_wcbm_before_delete_' . $this->object_type, $id );
				wp_delete_post( $id );
				$object->set_id( 0 );
				do_action( 'yith_wcbm_delete_' . $this->object_type, $id );
			} else {
				wp_trash_post( $id );
				$object->set_status( 'trash' );
				do_action( 'yith_wcbm_delete_' . $this->object_type, $id );
			}
		}

		/**
		 * Update Object post meta
		 *
		 * @param WC_Data $object The object.
		 * @param bool    $force  Force update. Used during create.
		 *
		 * @since 2.0
		 */
		public function update_post_meta( &$object, $force = false ) {
			$props_to_update = $force ? $this->meta_key_to_props : $this->get_props_to_update( $object, $this->meta_key_to_props );

			foreach ( $props_to_update as $meta_key => $prop ) {
				$value = $object->{"get_$prop"}( 'edit' );
				$value = is_string( $value ) ? wp_slash( $value ) : $value;
				switch ( $prop ) {
					default:
						$value = wc_clean( $value );
						break;
				}

				$updated = $this->update_or_delete_post_meta( $object, $meta_key, $value );

				if ( $updated ) {
					$this->updated_props[] = $prop;
				}
			}
		}

		/**
		 * Read All post Meta
		 *
		 * @param Object $object Badge Rule.
		 */
		protected function read_post_meta( &$object ) {
			$post_meta = get_post_meta( $object->get_id() );
			$set_props = array();

			foreach ( $this->meta_key_to_props as $meta_key => $prop ) {
				$set_props[ $prop ] = isset( $post_meta[ $meta_key ][0] ) ? maybe_unserialize( $post_meta[ $meta_key ][0] ) : null;
			}

			$object->set_props( $set_props );
		}
	}
}
