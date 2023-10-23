<?php
/**
 * Filter Preset data store
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\DataStore
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Preset_Data_Store' ) ) {
	/**
	 * This class implements CRUD methods for filter preset
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Preset_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {
		/**
		 * Map that relates meta keys to properties for YITH_WCAN_Preset object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_selector' => 'selector',
			'_enabled'  => 'enabled',
			'_filters'  => 'filters',
			'_layout'   => 'layout',
		);

		/**
		 * Stores updated props.
		 *
		 * @var array
		 */
		protected $updated_props = array();

		/* === CRUD METHODS === */

		/**
		 * Method to create a new preset in the database.
		 *
		 * @param YITH_WCAN_Preset $data Filter preset object.
		 *
		 */
		public function create( &$data ) {
			$id = wp_insert_post(
				apply_filters(
					'yith_wcan_new_filter_preset_data',
					array(
						'post_type'    => YITH_WCAN_Presets()->get_post_type(),
						'post_status'  => 'publish',
						'post_author'  => get_current_user_id(),
						'post_title'   => $data->get_title() ? $data->get_title() : __( 'Draft Preset', 'woocommerce' ),
						'post_content' => '',
						'post_excerpt' => '',
						'ping_status'  => 'closed',
						'post_name'    => $data->get_slug( 'edit' ),
					)
				),
				true
			);

			if ( $id && ! is_wp_error( $id ) ) {
				$data->set_id( $id );

				$this->update_post_meta( $data, true );
				$this->clear_caches( $data );

				$data->save_meta_data();
				$data->apply_changes();

				do_action( 'yith_wcan_new_filter_preset', $id, $data );
			}
		}

		/**
		 * Method to read a preset from the database.
		 *
		 * @param YITH_WCAN_Preset $data Filter preset object.
		 *
		 * @throws Exception If invalid product.
		 *
		 */
		public function read( &$data ) {

			$data->set_defaults();
			$post_object = $data->get_id() ? wp_cache_get( 'filter-preset-id-' . $data->get_id(), 'filter-presets' ) : wp_cache_get( 'filter-preset-slug-' . $data->get_slug(), 'filter-presets' );

			if ( ! $post_object ) {
				$posts_object = get_posts(
					array_merge(
						array(
							'post_type'   => YITH_WCAN_Presets()->get_post_type(),
							'post_status' => 'publish',
							'numberposts' => 1,
						),
						$data->get_id() ? array( 'p' => $data->get_id() ) : array( 'name' => $data->get_slug() )
					)
				);
				$post_object  = array_shift( $posts_object );

				if ( ! $post_object || YITH_WCAN_Presets()->get_post_type() !== $post_object->post_type ) {
					throw new Exception( _x( 'Invalid preset.', '[Generic] Error that happens when trying to read a filter preset that does not exist', 'yith-woocommerce-ajax-navigation' ) );
				}

				wp_cache_set( 'filter-preset-id-' . $data->get_id(), $post_object, 'filter-presets' );
				wp_cache_set( 'filter-presets-slug-' . $data->get_slug(), $post_object, 'filter-presets' );
			}

			$data->set_id( $post_object->ID );
			$data->set_props(
				array(
					'title' => $post_object->post_title,
					'slug'  => $post_object->post_name,
				)
			);

			$this->read_post_meta( $data );
			$data->set_object_read( true );

			do_action( 'yith_wcan_filter_preset_read', $data->get_id() );
		}

		/**
		 * Method to update a preset in the database.
		 *
		 * @param YITH_WCAN_Preset $data Filter preset object.
		 *
		 */
		public function update( &$data ) {
			$data->save_meta_data();
			$changes = $data->get_changes();

			// Only update the post when the post data changes.
			if ( array_intersect( array( 'title', 'slug' ), array_keys( $changes ) ) ) {
				$post_data = array(
					'post_title' => $data->get_title( 'edit' ),
					'post_name'  => $data->get_slug( 'edit' ),
					'post_type'  => YITH_WCAN_Presets()->get_post_type(),
				);

				/**
				 * When updating this object, to prevent infinite loops, use $wpdb
				 * to update data, since wp_update_post spawns more calls to the
				 * save_post action.
				 *
				 * This ensures hooks are fired by either WP itself (admin screen save),
				 * or an update purely from CRUD.
				 */
				if ( doing_action( 'save_post' ) ) {
					$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $data->get_id() ) );
					clean_post_cache( $data->get_id() );
				} else {
					wp_update_post( array_merge( array( 'ID' => $data->get_id() ), $post_data ) );
				}
				$data->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

			} else { // Only update post modified time to record this save event.
				$GLOBALS['wpdb']->update(
					$GLOBALS['wpdb']->posts,
					array(
						'post_modified'     => current_time( 'mysql' ),
						'post_modified_gmt' => current_time( 'mysql', 1 ),
					),
					array(
						'ID' => $data->get_id(),
					)
				);
				clean_post_cache( $data->get_id() );
			}

			$this->update_post_meta( $data );
			$this->clear_caches( $data );

			$data->apply_changes();

			do_action( 'yith_wcan_update_filter_preset', $data->get_id(), $data );
		}

		/**
		 * Method to delete a preset from the database.
		 *
		 * @param YITH_WCAN_Preset $data Filter preset object.
		 * @param array            $args Array of args to pass to the delete method.
		 *
		 */
		public function delete( &$data, $args = array() ) {
			$id = $data->get_id();

			$args = wp_parse_args(
				$args,
				array(
					'force_delete' => false,
				)
			);

			if ( ! $id ) {
				return;
			}

			$post_type = YITH_WCAN_Presets()->get_post_type();

			if ( $args['force_delete'] ) {
				do_action( 'yith_wcan_before_delete_' . $post_type, $id );

				wp_delete_post( $id );
				$data->set_id( 0 );

				do_action( 'yith_wcan_delete_' . $post_type, $id );
			} else {
				wp_trash_post( $id );
				do_action( 'yith_wcan_trash_' . $post_type, $id );
			}
		}

		/**
		 * Method to clone a preset.
		 *
		 * @param YITH_WCAN_Preset $preset Filter preset object.
		 *
		 */
		public function clone( &$preset ) {
			$new_preset = clone( $preset );

			// set new title
			// translators: 1. Title of original preset.
			$new_preset->set_title( sprintf( _x( '%s - Copy', '[ADMIN] Title of the cloned preset', 'yith-woocommerce-ajax-navigation' ), $new_preset->get_title() ) );

			$this->create( $new_preset );
		}

		/* === META METHODS === */

		/**
		 * Helper method that updates all the post meta for a preset based on it's settings in the YITH_WCAN_Preset class.
		 *
		 * @param YITH_WCAN_Preset $preset Filter preset object.
		 * @param bool             $force  Force update. Used during create.
		 *
		 * @since 4.0.0
		 */
		protected function read_post_meta( &$preset, $force = false ) {
			$id                = $preset->get_id();
			$post_meta_values  = get_post_meta( $id );
			$meta_key_to_props = $this->meta_key_to_props;

			$set_props = array();

			foreach ( $meta_key_to_props as $meta_key => $prop ) {
				$meta_value         = isset( $post_meta_values[ $meta_key ][0] ) ? $post_meta_values[ $meta_key ][0] : null;
				$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only unserializes single values.
			}

			$preset->set_props( $set_props );
		}

		/**
		 * Helper method that updates all the post meta for a preset based on it's settings in the YITH_WCAN_Preset class.
		 *
		 * @param YITH_WCAN_Preset $preset Filter preset object.
		 * @param bool             $force  Force update. Used during create.
		 *
		 * @since 4.0.0
		 */
		protected function update_post_meta( &$preset, $force = false ) {
			$meta_key_to_props = $this->meta_key_to_props;

			$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $preset, $meta_key_to_props );

			foreach ( $props_to_update as $meta_key => $prop ) {
				switch ( $prop ) {
					case 'enabled':
						$value = $preset->is_enabled() ? 'yes' : 'no';
						break;
					case 'filters':
						$value = $preset->get_raw_filters();
						break;
					default:
						$value = $preset->{"get_$prop"}( 'edit' );
						$value = is_string( $value ) ? wp_slash( $value ) : $value;
						break;
				}
				$updated = $this->update_or_delete_post_meta( $preset, $meta_key, $value );

				if ( $updated ) {
					$this->updated_props[] = $prop;
				}
			}
		}

		/* === HELPER METHODS === */

		/**
		 * Query database to search
		 *
		 * @param array $args       Array of arguments
		 * [
		 *     'id'      => false,
		 *     'slug'    => false,
		 *     's'       => false,
		 *     'orderby' => 'ID',
		 *     'order'   => 'DESC',
		 *     'limit'   => false,
		 *     'offset'  => 0,
		 * ].
		 * @param bool  $count_only Return count of presets, instead or array of objects.
		 *
		 * @return YITH_WCAN_Preset[]|int Array of matched presets, or count of items when {@see $count_only}.
		 */
		public function query( $args = array(), $count_only = false ) {
			$default = array(
				'id'      => false,
				'slug'    => false,
				's'       => false,
				'orderby' => 'ID',
				'order'   => 'DESC',
				'limit'   => false,
				'offset'  => 0,
			);

			$args_to_query = array(
				'id'    => 'p',
				'slug'  => 'name',
				'limit' => 'posts_per_page',
			);

			$args = wp_parse_args( $args, $default );

			$query_args = array(
				'post_type'        => YITH_WCAN_Presets()->get_post_type(),
				'fields'           => 'ids',
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'suppress_filters' => false,
			);

			foreach ( $args as $key => $value ) {
				if ( ! $value ) {
					continue;
				}

				if ( isset( $args_to_query[ $key ] ) ) {
					$query_args[ $args_to_query[ $key ] ] = $value;
				} else {
					$query_args[ $key ] = $value;
				}
			}

			$presets = get_posts( $query_args );

			if ( $count_only ) {
				return count( $presets );
			} elseif ( ! empty( $presets ) ) {
				$presets = array_map( array( 'YITH_WCAN_Preset_Factory', 'get_preset' ), $presets );
			} else {
				$presets = array();
			}

			return apply_filters( 'yith_wcan_get_presets', $presets, $args );
		}

		/**
		 * Query database to search
		 *
		 * @return string[] Array of presets, in the format slug => name.
		 */
		public function items() {
			global $wpdb;

			$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					"SELECT post_name, post_title FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
					YITH_WCAN_Presets()->get_post_type(),
					'publish'
				),
				ARRAY_A
			);

			if ( empty( $results ) ) {
				return array();
			}

			$presets = array_combine( wp_list_pluck( $results, 'post_name' ), wp_list_pluck( $results, 'post_title' ) );

			return apply_filters( 'yith_wcan_list_presets', $presets );
		}

		/**
		 * Counts items that matches
		 *
		 * @param array $args Same parameters allowed for {@see query} method.
		 * @return int Count of items
		 */
		public function count( $args = array() ) {
			// retrieve number of items found.
			return $this->query( $args, true );
		}

		/**
		 * Clear any caches.
		 *
		 * @param YITH_WCAN_Preset $preset Filter preset object.
		 *
		 * @since 4.0.0
		 */
		protected function clear_caches( &$preset ) {
			wp_cache_delete( 'filter-preset-id-' . $preset->get_id(), 'filter-presets' );
			wp_cache_delete( 'filter-preset-slug-' . $preset->get_slug(), 'filter-presets' );
		}
	}
}
