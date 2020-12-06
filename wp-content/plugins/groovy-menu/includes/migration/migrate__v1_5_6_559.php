<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_5_6_559 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.5.6.559';

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
			foreach ( $posts as $post ) {
				$post_id     = (int) $post->ID;
				$post_parent = $post->post_parent;
				$post_title  = $post->post_title;


				if ( empty( $post_parent ) || '-' !== $post_title ) {
					continue;
				}

				$do_not_show_title_old = get_post_meta( $post_id, 'groovy_menu_do_not_show_title', true );

				update_metadata( 'post', $post_id, 'groovy_menu_do_not_show_title', '1', $do_not_show_title_old );

				$this->add_migrate_debug_log( 'Updated meta for post id#' . $post_id );

			}
		}

		unset( $posts );

		$this->success();

		return true;

	}


}
