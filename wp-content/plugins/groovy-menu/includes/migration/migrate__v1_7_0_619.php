<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_7_0_619 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.7.0.619';

		$meta_params = array(
			'groovy_menu_is_megamenu',
			'groovy_menu_do_not_show_title',
			'groovy_menu_megamenu_cols',
			'groovy_menu_block_url',
			'groovy_menu_megamenu_post',
			'groovy_menu_megamenu_post_not_mobile',
			'groovy_menu_is_show_featured_image',
			'groovy_menu_icon_class',
			'groovy_menu_megamenu_background',
			'groovy_menu_megamenu_background_position',
			'groovy_menu_megamenu_background_repeat',
			'groovy_menu_megamenu_background_size',
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

		$this->add_migrate_debug_log( 'Get all nav_menu_item.' );

		$posts = get_posts( $args );

		if ( $posts ) {

			$this->add_migrate_debug_log( 'Work with nav_menu_item meta.' );


			foreach ( $posts as $post ) {
				$post_id               = (int) $post->ID;
				$current_mass_meta     = get_post_meta( $post_id, 'groovy_menu_nav_menu_meta', true );
				$current_separate_meta = array();

				if ( is_string( $current_mass_meta ) ) {
					$current_mass_meta = json_decode( $current_mass_meta, true );
				}

				if ( empty( $current_mass_meta ) || ! is_array( $current_mass_meta ) ) {
					$current_mass_meta = array();
				}

				foreach ( $meta_params as $param ) {
					$data = get_post_meta( $post_id, $param, true );
					if ( empty( $data ) ) {
						continue;
					}
					$current_separate_meta[ $param ] = get_post_meta( $post_id, $param, true );
				}

				if ( empty( $current_separate_meta ) ) {
					continue;
				}

				$new_mass_meta = array_merge( $current_separate_meta, $current_mass_meta );

				update_metadata( 'post', $post_id, 'groovy_menu_nav_menu_meta', wp_json_encode( $new_mass_meta ) );

				$this->add_migrate_debug_log( 'Updated meta for post id#' . $post_id );

			}
		}

		unset( $posts );

		$this->success();

		return true;

	}


}
