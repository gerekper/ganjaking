<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_5_5_529 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.5.5.529';

		$migration_change_id = get_option( 'gm_migration_data_v1_5_4_529' );
		if ( empty( $migration_change_id ) ) {
			$migration_change_id = array();
		}

		$post_fields_array = array(
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'post_name', // only for custom type.
			'post_content_filtered',
			'post_parent',
			'menu_order',
			'post_mime_type',
			'filter',
		);

		$args = array(
			'post_type'        => 'nav_menu_item',
			'numberposts'      => - 1,
			'category'         => 0,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'include'          => array(),
			'exclude'          => array(),
			'suppress_filters' => true,
		);

		$this->add_migrate_debug_log( 'Get all nav_menu_item. Part 1' );

		$posts = get_posts( $args );

		if ( $posts ) {

			foreach ( $posts as $post ) {
				$post_id = (int) $post->ID;

				$meta           = get_post_custom( $post_id );
				$post_collected = array();
				$meta_collected = array();
				$menu_terms     = array();

				foreach ( $meta as $meta_field => $meta_field_data ) {
					$meta_collected[ $meta_field ] = array_shift( $meta_field_data );
				}

				// Skip menus without selected gm_menu_block.
				if ( empty( $meta_collected['groovy_menu_megamenu_post'] ) ) {
					continue;
				}

				// Skip menus with gm_menu_block object.
				if ( 'gm_menu_block' === $meta_collected['_menu_item_object'] ) {
					continue;
				}

				$menu_terms = wp_get_post_terms( $post_id, 'nav_menu', array( 'fields' => 'ids' ) );

				foreach ( $post_fields_array as $post_field ) {
					if ( isset( $post->$post_field ) ) {
						$post_collected[ $post_field ] = $post->$post_field;
					}
				}

				$menu_block_id = $meta_collected['groovy_menu_megamenu_post'];

				$post_collected['post_type'] = 'nav_menu_item';
				$post_collected['post_name'] = $menu_block_id;

				if ( empty( $post_collected['post_title'] ) ) {
					$menu_item_object_id = intval( $meta_collected['_menu_item_object_id'] );

					if ( $menu_item_object_id ) {
						if ( 'page' === $meta_collected['_menu_item_object'] || 'post' === $meta_collected['_menu_item_object'] ) {
							$post_collected['post_title']            = get_the_title( $menu_item_object_id );
							$meta_collected['groovy_menu_block_url'] = get_permalink( $menu_item_object_id );
						}
						if ( 'category' === $meta_collected['_menu_item_object'] ) {
							$post_collected['post_title']            = get_the_category_by_ID( $menu_item_object_id );
							$meta_collected['groovy_menu_block_url'] = get_category_link( $menu_item_object_id );
						}
					}
				}

				$meta_collected['_menu_item_type']      = 'post_type';
				$meta_collected['_menu_item_object']    = 'gm_menu_block';
				$meta_collected['_menu_item_object_id'] = $menu_block_id;


				// set meta for post array.
				$post_collected['meta_input'] = $meta_collected;


				// Inset post.
				$new_post_id = wp_insert_post( $post_collected );

				$this->add_migrate_debug_log( 'Inset post. New id#' . $new_post_id );

				if ( ! $new_post_id ) {
					continue;
				}

				$add_term_result = wp_set_post_terms( $new_post_id, $menu_terms, 'nav_menu' );

				// Delete old post.
				$deleted_obj = wp_delete_post( $post_id );

				$migration_change_id[ $post_id ] = $new_post_id;
				update_option( 'gm_migration_data_v1_5_4_529', $migration_change_id, false );
			}
		}


		$migration_change_id = get_option( 'gm_migration_data_v1_5_4_529' );
		if ( empty( $migration_change_id ) ) {
			$migration_change_id = array();
		}

		if ( ! empty( $migration_change_id ) ) {
			$args = array(
				'post_type'        => 'nav_menu_item',
				'numberposts'      => - 1,
				'category'         => 0,
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'include'          => array(),
				'exclude'          => array(),
				'suppress_filters' => true,
			);

			$this->add_migrate_debug_log( 'Get all nav_menu_item. Part 2' );

			$posts = get_posts( $args );

			if ( $posts ) {

				foreach ( $posts as $post ) {
					$post_id              = (int) $post->ID;
					$menu_item_parent_old = get_post_meta( $post_id, '_menu_item_menu_item_parent', true );

					if ( empty( $menu_item_parent_old ) ) {
						continue;
					}

					$_menu_item_parent = intval( $menu_item_parent_old );

					if ( ! empty( $migration_change_id[ $_menu_item_parent ] ) ) {
						$new_id = strval( $migration_change_id[ $_menu_item_parent ] );
						update_metadata( 'post', $post_id, '_menu_item_menu_item_parent', $new_id, $menu_item_parent_old );
						$this->add_migrate_debug_log( 'Updated meta for post id#' . $post_id );
					}
				}
			}
		}

		unset( $posts );

		delete_option( 'gm_migration_data_v1_5_4_529' );


		$this->success();

		return true;

	}


}
