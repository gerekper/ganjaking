<?php
/**
 * Groovy menu Uninstall
 *
 * Remove Groovy menu, Presets and other data when using the "Delete" link on the plugins screen.
 *
 */


// if uninstall.php is not called by WordPress, stop script
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


if ( ! function_exists( 'gm_get_posts_fields' ) ) {

	function gm_get_posts_fields( $args = array() ) {
		$valid_fields = array(
			'ID'             => '%d',
			'post_author'    => '%d',
			'post_type'      => '%s',
			'post_mime_type' => '%s',
			'post_title'     => false,
			'post_name'      => '%s',
			'post_date'      => '%s',
			'post_modified'  => '%s',
			'menu_order'     => '%d',
			'post_parent'    => '%d',
			'post_excerpt'   => false,
			'post_content'   => false,
			'post_status'    => '%s',
			'comment_status' => false,
			'ping_status'    => false,
			'to_ping'        => false,
			'pinged'         => false,
			'comment_count'  => '%d'
		);
		$defaults     = array(
			'post_type'      => 'groovy_menu_preset',
			'post_status'    => 'publish',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'posts_per_page' => - 1,
		);

		$order = $orderby = $posts_per_page = '';

		global $wpdb;
		$args  = wp_parse_args( $args, $defaults );
		$where = "";
		foreach ( $valid_fields as $field => $can_query ) {
			if ( isset( $args[ $field ] ) && $can_query ) {
				if ( '' !== $where ) {
					$where .= ' AND ';
				}
				$where .= $wpdb->prepare( $field . " = " . $can_query, $args[ $field ] );
			}
		}
		if ( isset( $args['search'] ) && is_string( $args['search'] ) ) {
			if ( '' !== $where ) {
				$where .= ' AND ';
			}
			$where .= $wpdb->prepare( "post_title LIKE %s", "%" . $args['search'] . "%" );
		}
		if ( isset( $args['include'] ) ) {
			if ( is_string( $args['include'] ) ) {
				$args['include'] = explode( ',', $args['include'] );
			}
			if ( is_array( $args['include'] ) ) {
				$args['include'] = array_map( 'intval', $args['include'] );
				if ( '' !== $where ) {
					$where .= ' OR ';
				}
				$where .= "ID IN (" . implode( ',', $args['include'] ) . ")";
			}
		}
		if ( isset( $args['exclude'] ) ) {
			if ( is_string( $args['exclude'] ) ) {
				$args['exclude'] = explode( ',', $args['exclude'] );
			}
			if ( is_array( $args['exclude'] ) ) {
				$args['exclude'] = array_map( 'intval', $args['exclude'] );
				if ( '' !== $where ) {
					$where .= ' AND ';
				}
				$where .= "ID NOT IN (" . implode( ',', $args['exclude'] ) . ")";
			}
		}
		extract( $args );
		$iscol = false;
		if ( isset( $fields ) ) {
			if ( is_string( $fields ) ) {
				$fields = explode( ',', $fields );
			}
			if ( is_array( $fields ) ) {
				$fields = array_intersect( $fields, array_keys( $valid_fields ) );
				if ( count( $fields ) === 1 ) {
					$iscol = true;
				}
				$fields = implode( ',', $fields );
			}
		}
		if ( empty( $fields ) ) {
			$fields = '*';
		}
		if ( ! in_array( $orderby, $valid_fields ) ) {
			$orderby = 'post_date';
		}
		if ( ! in_array( strtoupper( $order ), array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}
		if ( ! intval( $posts_per_page ) && $posts_per_page != - 1 ) {
			$posts_per_page = $defaults['posts_per_page'];
		}
		if ( '' === $where ) {
			$where = '1';
		}
		$q = "SELECT $fields FROM $wpdb->posts WHERE " . $where;
		$q .= " ORDER BY $orderby $order";
		if ( $posts_per_page != - 1 ) {
			$q .= " LIMIT $posts_per_page";
		}

		return $iscol ? $wpdb->get_col( $q ) : $wpdb->get_results( $q );
	}

}


if ( ! function_exists( 'gm_get_all_presets' ) ) {
	/**
	 * @param bool $key_value if true return simple array key value.
	 *
	 * @return array|null|object
	 */
	function gm_get_all_presets( $key_value = false ) {

		$presets = array();

		// get posts.
		$args          = array(
			'fields' => array( 'ID', 'post_title' ),
			'order'  => 'ASC',
		);
		$raw_base_data = gm_get_posts_fields( $args );

		if ( empty( $raw_base_data ) ) {
			$raw_base_data = array();
		}

		if ( $key_value ) {
			foreach ( $raw_base_data as $preset ) {
				$presets[ strval( $preset->ID ) ] = $preset->post_title;
			}
		} else {
			foreach ( $raw_base_data as $preset ) {
				$preset_obj       = new stdClass();
				$preset_obj->id   = strval( $preset->ID );
				$preset_obj->name = $preset->post_title;

				$presets[] = $preset_obj;
			}
		}


		return $presets;
	}

}


if ( ! function_exists( 'gm_delete_groovy_uploads_dir' ) ) {
	/**
	 * Remove groovy uploads dir with files.
	 */
	function gm_delete_groovy_uploads_dir() {

		$_cpath       = ABSPATH . 'wp-content/uploads/';
		$_groovy_path = $_cpath . 'groovy/';

		$tmp_dir_i   = new RecursiveDirectoryIterator( $_groovy_path, RecursiveDirectoryIterator::SKIP_DOTS );
		$tmp_files_i = new RecursiveIteratorIterator( $tmp_dir_i, RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $tmp_files_i as $file ) {
			if ( $file->isDir() ) {
				rmdir( $file->getPathname() );
			} elseif ( $file->isFile() ) {
				unlink( $file->getPathname() );
			}
		}
		rmdir( $_groovy_path );
	}
}


// Start uninstall proccess.
// Get global options.
$gr_options = get_option( 'groovy_menu_settings', array() );

if ( isset( $gr_options['tools'] ) && isset( $gr_options['tools']['uninstall_data'] ) ) {

	if ( $gr_options['tools']['uninstall_data'] ) {

		/**
		 * Fires when groovy menu uninstall.
		 *
		 * @since 1.2.20
		 */
		do_action( 'gm_uninstall_action' );


		delete_option( 'groovy_menu_settings' );
		delete_option( 'groovy_menu_settings_fonts' );
		delete_option( 'groovy_menu_db_version__report' );
		delete_option( 'groovy_menu_imported' );
		delete_option( 'groovy_menu_default_preset' );
		delete_option( 'groovy_menu_preset_used_in_storage' );
		delete_option( 'groovy_menu_db_version' );
		delete_option( 'gm_migration_data_1_4_4_400_1' );

		$all_presets = gm_get_all_presets();

		foreach ( $all_presets as $preset ) {
			if ( isset( $preset->id ) && ! empty( $preset->id ) ) {
				$delete_post = wp_delete_post( intval( $preset->id ), true );
			}
		}

		gm_delete_groovy_uploads_dir();


		/**
		 * Fires at the end of groovy menu uninstall process.
		 *
		 * @since 1.5.3
		 */
		do_action( 'gm_uninstall_action_end' );


	}

}

