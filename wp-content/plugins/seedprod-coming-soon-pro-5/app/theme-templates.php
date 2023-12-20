<?php
/**
 * Backend funtions for Theme Template Pat functionality.
 */

/**
 * Get Datatable Info for the Theme Template parts page.
 *
 * @return JSON object.
 */
function seedprod_pro_themetemplate_datatable() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$data         = array( '' );
		$current_page = 1;
		if ( ! empty( absint( $_GET['current_page'] ) ) ) {
			$current_page = absint( $_GET['current_page'] );
		}
		$per_page = 10;

		$filter = null;
		if ( ! empty( $_GET['filter'] ) ) {
			$filter = sanitize_text_field( wp_unslash( $_GET['filter'] ) );
			if ( 'all' === $filter ) {
				$filter = null;
			}
		}

		if ( ! empty( $_GET['s'] ) ) {
			$filter = null;
		}

		// Get records
		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';

		$sql = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

		$sql .= ' WHERE 1 = 1 AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';

		if ( ! empty( $filter ) ) {
			if ( esc_sql( $filter ) === 'published' ) {
				$sql .= ' AND  post_status = "publish" ';
			}
			if ( esc_sql( $filter ) === 'drafts' ) {
				$sql .= ' AND  post_status = "draft" ';
			}
			if ( esc_sql( $filter ) === 'scheduled' ) {
				$sql .= ' AND  post_status = "future" ';
			}
			if ( esc_sql( $filter ) === 'archived' ) {
				$sql .= ' AND  post_status = "trash" ';
			}
		} else {
			$sql .= 'AND post_status != "trash"';
		}

		if ( ! empty( $_GET['s'] ) ) {
			$sql .= ' AND post_title LIKE "%' . esc_sql( trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) ) . '%"';
		}

		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		$order   = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		if ( ! empty( $orderby ) ) {
			if ( 'date' === $orderby ) {
				$orderby = 'post_modified';
			}
			if ( 'name' === $orderby ) {
				$orderby = 'post_title';
			}
			$sql .= ' ORDER BY ' . esc_sql( $orderby );

			if ( 'desc' === $order ) {
				$order = 'DESC';
			} else {
				$order = 'ASC';
			}
			$sql .= ' ' . $order;
		} else {
			$sql .= ' ORDER BY id DESC';
		}

		$sql .= " LIMIT $per_page";
		if ( empty( $_POST['s'] ) ) {
			$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;
		}
		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$login_page_id = get_option( 'seedprod_login_page_id' );
		$data          = array();
		foreach ( $results as $v ) {
			// Skip row to prevent current Login Page post from displaying here
			if ( $v->ID === $login_page_id ) {
				continue;
			}

			$modified_at = gmdate( 'Y/m/d', strtotime( $v->post_modified ) );

			$posted_at = gmdate( 'Y/m/d', strtotime( $v->post_date ) );

			$url = get_permalink( $v->ID );

			if ( 'publish' === $v->post_status ) {
				$status = 'Published';
			}
			if ( 'draft' === $v->post_status ) {
				$status = 'Draft';
			}
			if ( 'future' === $v->post_status ) {
				$status = 'Scheduled';
			}
			if ( 'trash' === $v->post_status ) {
				$status = 'Trash';
			}

			$type              = get_post_meta( $v->ID, '_seedprod_page_template_type', true );
			$conditions_return = '';
			$conditions        = get_post_meta( $v->ID, '_seedprod_theme_template_condition', true );
			if ( ! empty( $conditions ) ) {
				$conditions     = json_decode( $conditions );
				$conditions_map = seedprod_pro_conditions_map();
				if ( is_array( $conditions ) || is_object( $conditions ) ) {
					foreach ( $conditions as $k5 => $v5 ) {
						if ( ! empty( $conditions_return ) ) {
							$conditions_return .= ', ';
						}
						$exclude1 = '';
						$exclude2 = '';
						if ( 'exclude' === $v5->condition ) {
							$exclude1 = '<span style="text-decoration: line-through;">';
							$exclude2 = '</span>';
						}
						if ( empty( $v5->value ) ) {
							$conditions_return .= $exclude1 . $conditions_map[ $v5->type ] . $exclude2;
						} else {
							if ( 'custom' === $v5->condition ) {
								$conditions_return .= $exclude1 . 'Custom : ' . $v5->value . $exclude2;
							} else {
								$conditions_return .= $exclude1 . $conditions_map[ $v5->type ] . ' : ' . $v5->value . $exclude2;
							}
						}
					}
				}
			}

			$is_published = false;
			if ( 'publish' === $v->post_status ) {
				$is_published = true;
			}

			// Load Data

			$data[] = array(
				'id'                => $v->ID,
				'name'              => $v->post_title,
				'status'            => $status,
				'is_published'      => $is_published,
				'post_status'       => $v->post_status,
				'url'               => $url,
				'type'              => $type,
				'conditions'        => $conditions,
				'conditions_return' => $conditions_return,
				'modified_at'       => $modified_at,
				'posted_at'         => $posted_at,
				'priority'          => $v->menu_order,
				'preview_link'      => home_url() . "/?post_type=seedprod&page_id=$v->ID&preview_id=$v->ID&preview_nonce=" . wp_create_nonce( 'post_preview_' . $v->ID ) . '&preview=true',
			);
		}

		$totalitems = seedprod_pro_themetemplate_get_data_total( $filter );
		$views      = seedprod_pro_themetemplate_get_views( $filter );

		$response = array(
			'rows'               => $data,
			'totalitems'         => $totalitems,
			'totathemetemplates' => ceil( $totalitems / 10 ),
			'currentpage'        => $current_page,
			'views'              => $views,
		);

		wp_send_json( $response );
	}
}

/**
 * Get total for Filters on Datatale
 *
 * @param string $filter Filter from Top of Datable.
 * @return JSON object.
 */
function seedprod_pro_themetemplate_get_data_total( $filter = null ) {
	global $wpdb;

	$tablename      = $wpdb->prefix . 'posts';
	$meta_tablename = $wpdb->prefix . 'postmeta';

	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';

	if ( ! empty( $filter ) ) {
		if ( 'published' === esc_sql( $filter ) ) {
			$sql .= ' AND  post_status = "publish" ';
		}
		if ( 'drafts' === esc_sql( $filter ) ) {
			$sql .= ' AND  post_status = "draft" ';
		}
		if ( 'scheduled' === esc_sql( $filter ) ) {
			$sql .= ' AND  post_status = "future" ';
		}
		if ( 'archived' === esc_sql( $filter ) ) {
			$sql .= ' AND  post_status = "trash" ';
		}
	} else {
		$sql .= ' AND post_status != "trash"';
	}

	if ( ! empty( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sql .= ' AND post_name LIKE "%' . esc_sql( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) . '%"'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$results = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $results;
}


/**
 * Return Filter Views and Totals
 *
 * @param string $filter Filter from Top of Datable.
 * @return array
 */
function seedprod_pro_themetemplate_get_views( $filter = null ) {
	$views   = array();
	$current = ( ! empty( $filter ) ? $filter : 'all' );
	$current = sanitize_text_field( $current );

	global $wpdb;
	$tablename      = $wpdb->prefix . 'posts';
	$meta_tablename = $wpdb->prefix . 'postmeta';

	//All link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod" AND post_status != "trash"  AND meta_key = "_seedprod_is_theme_template"';

	$results      = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$class        = ( 'all' === $current ? ' class="current"' : '' );
	$all_url      = remove_query_arg( 'filter' );
	$views['all'] = $results;

	//Published link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod"  AND meta_key = "_seedprod_is_theme_template" AND post_status = "publish" ';

	$results            = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$running_url        = add_query_arg( 'filter', 'publish' );
	$class              = ( 'publish' === $current ? ' class="current"' : '' );
	$views['published'] = $results;

	//Drafts link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod"  AND meta_key = "_seedprod_is_theme_template" AND post_status = "draft" ';

	$results         = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$upcoming_url    = add_query_arg( 'filter', 'drafts' );
	$class           = ( 'drafts' === $current ? ' class="current"' : '' );
	$views['drafts'] = $results;

	//Scheduled link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod"  AND meta_key = "_seedprod_is_theme_template" AND post_status = "future" ';

	$results            = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$ended_url          = add_query_arg( 'filter', 'scheduled' );
	$class              = ( 'scheduled' === $current ? ' class="current"' : '' );
	$views['scheduled'] = $results;

	//Trash link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod"  AND meta_key = "_seedprod_is_theme_template" AND post_status = "trash" ';

	$results           = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$archived_url      = add_query_arg( 'filter', 'archived' );
	$class             = ( 'archived' === $current ? ' class="current"' : '' );
	$views['archived'] = $results;

	return $views;
}

/**
 * Duplicates a Theme Template
 *
 * @return JSON object.
 */
function seedprod_pro_duplicate_themetemplate() {
	if ( check_ajax_referer( 'seedprod_pro_duplicate_themetemplate' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id = '';
		if ( ! empty( $_GET['id'] ) ) {
			$id = absint( $_GET['id'] );
		}

		$post          = get_post( $id );
		$json          = $post->post_content_filtered;
		$template_type = get_post_meta( $id, '_seedprod_page_template_type', true );
		$conditions    = get_post_meta( $id, '_seedprod_theme_template_condition', true );

		$args = array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => $post->post_content,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . '- Copy',
			'post_type'      => 'seedprod',
			'post_name'      => '',
			'meta_input'     => array(
				'_seedprod_page_template_type'       => $template_type,
				'_seedprod_is_theme_template'        => true,
				'_seedprod_page_uuid'                => wp_generate_uuid4(),
				'_seedprod_theme_template_condition' => $conditions,
			),
		);

		$new_post_id = wp_insert_post( $args, true );
		// reinsert json due to slash bug
		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$wpdb->update(
			$tablename,
			array(
				'post_content_filtered' => $json,   // string
			),
			array( 'ID' => $new_post_id ),
			array(
				'%s',   // value1
			),
			array( '%d' )
		);

		wp_send_json( array( 'status' => true ) );
	}
}

/**
 * Archives a Theme Template
 *
 * @return JSON object.
 */
function seedprod_pro_archive_selected_themetemplates() {
	if ( check_ajax_referer( 'seedprod_pro_archive_selected_themetemplates' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_trash_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_trash_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}

/**
 * Unarchives a Theme Template
 *
 * @param  mixed $ids Id or list of ids to archive.
 * @return JSON object.
 */
function seedprod_pro_unarchive_selected_themetemplates( $ids ) {
	if ( check_ajax_referer( 'seedprod_pro_unarchive_selected_themetemplates' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_unarchive_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_untrash_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}

/**
 * Delete a Theme Template
 *
 * @return JSON object.
 */
function seedprod_pro_delete_archived_themetemplates() {
	if ( check_ajax_referer( 'seedprod_pro_delete_archived_themetemplates' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_archive_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_delete_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}


/**
 * Saves a New Theme Template in a temp location.
 *
 * @return JSON object.
 */
function seedprod_pro_temp_save_theme_template() {
	if ( check_ajax_referer( 'seedprod_pro_temp_save_theme_template' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$template_name       = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
		$template_type       = isset( $_POST['template_type'] ) ? sanitize_text_field( wp_unslash( $_POST['template_type'] ) ) : '';
		$template_priority   = isset( $_POST['template_priority'] ) ? sanitize_text_field( wp_unslash( $_POST['template_priority'] ) ) : 0;
		$template_conditions = $_POST['template_conditions']; // phpcs:ignore

		$data                        = array();
		$data['template_name']       = $template_name;
		$data['template_type']       = $template_type;
		$data['template_priority']   = $template_priority;
		$data['template_conditions'] = $template_conditions;
		update_option( 'seedprod_temp_theme_template_data', wp_json_encode( $data ) );
		wp_send_json( true );
	}
}

/**
 * Updates a Theme Templates conditions.
 *
 * @return JSON object.
 */
function seedprod_pro_update_theme_template_conditions() {
	if ( check_ajax_referer( 'seedprod_pro_update_theme_template_conditions' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$template_id         = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : null;
		$template_name       = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
		$template_priority   = isset( $_POST['template_priority'] ) ? sanitize_text_field( wp_unslash( $_POST['template_priority'] ) ) : 0;
		$template_conditions = isset( $_POST['template_conditions'] ) ? wp_json_encode( wp_unslash( $_POST['template_conditions'] ) ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$data                        = array();
		$data['template_id']         = absint( wp_unslash( $_POST['template_id'] ) );
		$data['template_name']       = $template_name;
		$data['template_priority']   = $template_priority;
		$data['template_conditions'] = $template_conditions;
		if ( ! empty( $template_id ) ) {
			// remove action so they don't conflict with the save. Yoast SEO was trying to analytize this content.
			remove_all_actions( 'wp_insert_post' );
			wp_update_post(
				array(
					'ID'         => $data['template_id'],
					'post_title' => $data['template_name'],
					'menu_order' => $data['template_priority'],
				)
			);
			update_post_meta( $data['template_id'], '_seedprod_theme_template_condition', $data['template_conditions'] );
		}
		wp_send_json( true );
	}
}

/**
 * Updates a Theme Templates post status.
 *
 * @return JSON object.
 */
function seedprod_pro_update_theme_template_post_status() {
	if ( check_ajax_referer( 'seedprod_pro_update_theme_template_post_status' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id           = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$is_published = isset( $_POST['is_published'] ) ? sanitize_text_field( wp_unslash( $_POST['is_published'] ) ) : null;

		$post_status = 'draft';
		if ( 'true' === $is_published ) {
			$post_status = 'publish';
		}

		wp_update_post(
			array(
				'ID'          => $id,
				'post_status' => $post_status,
			)
		);
		wp_send_json( true );
	}
}

/**
 * Updates a Theme Templates post status.
 *
 * @return JSON object.
 */
function seedprod_pro_update_theme_template_preview_mode() {
	if ( check_ajax_referer( 'seedprod_pro_update_theme_template_preview_mode' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$theme_preview_mode = isset( $_POST['theme_preview_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_preview_mode'] ) ) : null;

		$theme_preview_mode_update = false;
		if ( 'true' === $theme_preview_mode ) {
			$theme_preview_mode_update = true;
		}

		update_option( 'seedprod_theme_template_preview_mode', $theme_preview_mode_update );
		wp_send_json( true );
	}
}

/**
 * Enable / Disable SeedProd Theme
 *
 * @return JSON object.
 */
function seedprod_pro_update_seedprod_theme_enabled() {
	if ( check_ajax_referer( 'seedprod_pro_update_seedprod_theme_enabled' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_update_seedprod_theme_enabled', 'switch_themes' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		$seedprod_theme_enabled = isset( $_POST['seedprod_theme_enabled'] ) ? sanitize_text_field( wp_unslash( $_POST['seedprod_theme_enabled'] ) ) : null;

		$seedprod_theme_enabled_update = false;
		if ( 'true' === $seedprod_theme_enabled ) {
			$seedprod_theme_enabled_update = true;
		}

		update_option( 'seedprod_theme_enabled', $seedprod_theme_enabled_update );
		if ( true === $seedprod_theme_enabled_update ) {
			wp_send_json( true );
		} else {
			wp_send_json( 'disabled' );
		}
	}
}

/**
 * Map Conditons to an array
 *
 * @return array conditions.
 */
function seedprod_pro_conditions_map() {
	$conditions_map = array();
	$conditions     = seedprod_pro_theme_template_conditons();
	foreach ( $conditions as $k => $v ) {
		foreach ( $v  as $k1 => $v1 ) {
			$conditions_map[ $v1['value'] ] = $v1['text'];
		}
	}
	return $conditions_map;
}

/**
 * Create Global CSS if it does not exist.
 *
 * @return void
 */
function seedprod_pro_create_global_css_post() {
		// see if have a global css post created yet
	if ( ! empty( $_GET['page'] ) && 'seedprod_pro' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$global_css_page_id = get_option( 'seedprod_global_css_page_id' );
		if ( empty( $global_css_page_id ) ) {
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/global-css.php';
			$args = array(
				'comment_status'        => 'closed',
				'ping_status'           => 'closed',
				'post_content_filtered' => $seedprod_global_css,
				'post_status'           => 'publish',
				'post_title'            => __( 'Global CSS', 'seedprod-pro' ),
				'post_type'             => 'seedprod',
				'post_name'             => '',
				'meta_input'            => array(
					'_seedprod_page'               => true,
					'_seedprod_page_uuid'          => wp_generate_uuid4(),
					'_seedprod_page_template_type' => 'css',
					'_seedprod_is_theme_template'  => true,

				),
			);

			$global_css_page_id = wp_insert_post( $args, true );
			update_option( 'seedprod_global_css_page_id', $global_css_page_id );
		}
	}

}
add_action( 'admin_init', 'seedprod_pro_create_global_css_post' );


/**
 * Gray out WP themes to let user know they doesn't matter
 *
 * @author Oxygen Builder
 * @return void
 */
function seedprod_pro_disable_themes_css() {

	$current_screen = get_current_screen();

	// add for Themes screen only
	if ( 'themes' !== $current_screen->id ) {
		return;
	}

	echo '<style>
      .theme-screenshot img {
          filter: grayscale(100%) brightness(0.5);
      }
      .theme-actions .button,
          .theme-actions .button:hover {
          background-color: #F1F1F1;
          color: #DDDDDD;
          text-shadow: none;
          border-color: #ccc;
          box-shadow: none;
      }
      .seedprod-notice {
          border-left: 4px solid #dd4a1f;
          padding: 11px 15px;
      }
    </style>';
}
$seedprod_theme_enabled = get_option( 'seedprod_theme_enabled' );
if ( ! empty( $seedprod_theme_enabled ) ) {
	add_action( 'admin_head', 'seedprod_pro_disable_themes_css' );
}

/**
 * Show admin notice on Themes screen.
 *
 * @author Oxygen Builder
 * @return void
 */
function seedprod_pro_themes_screen_notice() {

	$current_screen = get_current_screen();

	// add for Themes screen only
	if ( 'themes' !== $current_screen->id ) {
		return;
	}
	?>
	<div class="notice notice-warning seedprod-notice">
		<p>
		<?php
		printf(
			__( 'You\'re using the <a href="%s">SeedProd</a> Theme to build your web site, which disables the WordPress theme system.', 'seedprod-pro' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.I18n.MissingTranslatorsComment
			menu_page_url( 'seedprod_pro', false ) . '#/theme-templates' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		?>
				</p>
	</div>
	<?php
}
$seedprod_theme_enabled = get_option( 'seedprod_theme_enabled' );
if ( ! empty( $seedprod_theme_enabled ) ) {
	add_action( 'admin_notices', 'seedprod_pro_themes_screen_notice' );
}

/**
 * Export Theme Templates
 *
 * @return void
 */
function seedprod_pro_theme_export() {

	if ( ! empty( $_REQUEST['action'] ) && 'seedprod_pro_export_theme' === $_REQUEST['action'] && current_user_can( 'export' ) ) {
		if ( ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'seedprod_pro_export_theme' ) ) {

			$export = array();
			// get records
			global $wpdb;
			$tablename      = $wpdb->prefix . 'posts';
			$meta_tablename = $wpdb->prefix . 'postmeta';

			if ( empty( $_REQUEST['id'] ) || ( ! empty( $_REQUEST['a'] ) && 'export_all_themetemplates' === $_REQUEST['a'] ) ) {
				$sql = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

				$sql    .= ' WHERE post_status="publish" AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';
				$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			} else {
				$ids          = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) );
				$how_many     = count( $ids );
				$placeholders = array_fill( 0, $how_many, '%d' );
				$format       = implode( ', ', $placeholders );

				$sql      = "SELECT * FROM $tablename WHERE id IN ($format)";
				$safe_sql = $wpdb->prepare( $sql, $ids ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$results  = $wpdb->get_results( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			}
			foreach ( $results as $k => $v ) {
				// get_post_meta
				$meta     = wp_json_encode( get_post_meta( $v->ID ) );
				$export[] = array(
					'post_content'          => base64_encode( $v->post_content ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'post_content_filtered' => base64_encode( $v->post_content_filtered ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'post_title'            => base64_encode( $v->post_title ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'meta'                  => base64_encode( $meta ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				);

			}

			$export_json = wp_json_encode( $export );
			$domain      = wp_parse_url( get_home_url() )['host'];

			$filename = sprintf( '%1$s-%2$s-%3$s', $domain . '-seedprod-theme', gmdate( 'Ymd' ), gmdate( 'His' ) );

			seedprod_pro_export_theme_parts_json( $export_json, $filename );

		}
	}
}
add_action( 'admin_init', 'seedprod_pro_theme_export' );

/**
 * Creates export JSON
 *
 * @param mixed  $data     Theme data.
 * @param string $filename Export file name.
 * @return void
 */
function seedprod_pro_export_theme_parts_json( $data, $filename ) {
	// No point in creating the export file on the file-system. We'll stream
	// it straight to the browser. Much nicer.

	// Open the output stream
	$fh = fopen( 'php://output', 'w' );

	// Start output buffering (to capture stream contents)
	ob_start();

	echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	// Get the contents of the output buffer
	$string = ob_get_clean();

	// Output CSV-specific headers
	header( 'Pragma: public' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Cache-Control: private', false );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '.json";' );
	header( 'Content-Transfer-Encoding: binary' );

	// Stream the CSV data
	exit( $string ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Imports Theme Templates JSON
 *
 * @return JSON object.
 */
function seedprod_pro_import_theme_request() {  
	if ( check_ajax_referer( 'seedprod_pro_import_theme_request' ) && ! empty( $_REQUEST['id'] ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_import_theme_request', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id = absint( $_REQUEST['id'] );
		seedprod_pro_theme_import( $id );
		update_option( 'seedprod_theme_id', $id );
		wp_send_json( true );
	}
}

/**
 * Process Imports Theme Templates JSON
 * @param integer $id ID of theme template.
 * @return void.
 */
function seedprod_pro_theme_import( $id = null ) {

	// get remote theme
	$code = '';

	$apikey = get_option( 'seedprod_api_token' );

	$url = SEEDPROD_PRO_API_URL . 'themes?plugin_version='.SEEDPROD_PRO_VERSION.'&id=' . $id . '&filter=theme_code_zip&api_token=' . $apikey;

	$response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		$code = $response->get_error_message();
	} else {
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $response_code ) {
			$code = $response['body'];
		} else {
			$code = __( "<br><br>Please enter a valid license key to access the themes. You can still proceed to create a page with the default theme.<br> <a class='seedprod_no_themes' href='?theme=0'>Click to continue &#8594;</a>", 'seedprod-pro' );
		}
	}

	$full_code = json_decode( $code );

	// see if it's a zip file or code, if zip call new import method and bail.
	if ( !empty($full_code->zipfile) ) {
		seedprod_pro_import_theme_by_url($full_code->zipfile);
		return true;
	}

	// else process code if legacy import
	$full_code = $full_code->code;
	$theme = $full_code->theme;

	$shortcode_update = $full_code->mapped;

	$imports = array();
	foreach ( $theme as $k => $v ) {
		$imports[] = array(
			'post_content'          => base64_decode( $v->post_content ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_content_filtered' => base64_decode( $v->post_content_filtered ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_title'            => base64_decode( $v->post_title ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'meta'                  => json_decode( base64_decode( $v->meta ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'order'                 => $v->order,
		);
	}

	$shortcode_array = array();
	foreach ( $shortcode_update as $k => $t ) {
		$shortcode_array[] = array(
			'shortcode'  => base64_decode( $t->shortcode ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'page_title' => $t->page_title,
		);
	}

	$import_page_array = array();

	foreach ( $imports as $k1 => $v1 ) {

		$meta = $v1['meta'];

		$data = array(
			'comment_status' => 'closed',
			'menu_order'     => $v1['order'],
			'ping_status'    => 'closed',
			'post_status'    => 'publish',
			'post_title'     => $v1['post_title'],
			'post_type'      => 'seedprod',
			'meta_input'     => array(
				'_seedprod_page'               => true,
				'_seedprod_is_theme_template'  => true,
				'_seedprod_page_uuid'          => wp_generate_uuid4(),
				'_seedprod_page_template_type' => $meta->_seedprod_page_template_type[0],
			),
		);

		$id = wp_insert_post(
			$data,
			true
		);

		$import_page_array[] = array(
			'id'                    => $id,
			'title'                 => $v1['post_title'],
			'post_content'          => $v1['post_content'],
			'post_content_filtered' => $v1['post_content_filtered'],
		);

		//reinsert settings because wp_insert screws up json.
		$post_content_filtered = $v1['post_content_filtered'];
		$post_content          = $v1['post_content'];
		global $wpdb;
		$tablename     = $wpdb->prefix . 'posts';
		$sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $post_content_filtered, $post_content, $id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$update_result = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// add meta
		if ( 'css' === $meta->_seedprod_page_template_type[0] ) {
			// set css file
			// find and replace url
			$css         = str_replace( 'TO_BE_REPLACED', home_url(), $meta->_seedprod_css[0] );
			$custom_css  = str_replace( 'TO_BE_REPLACED', home_url(), $meta->_seedprod_custom_css[0] );
			$custom_css  = '';
			$builder_css = str_replace( 'TO_BE_REPLACED', home_url(), $meta->_seedprod_builder_css[0] );

			update_post_meta( $id, '_seedprod_css', $css );
			update_post_meta( $id, '_seedprod_custom_css', $custom_css );
			update_post_meta( $id, '_seedprod_builder_css', $builder_css );
			update_option( 'global_css_page_id', $id );
			// generate css
			$css = $css . $custom_css;

			// trash current css file and set css file pointer
			$current_css_file = get_option( 'seedprod_global_css_page_id' );
			if ( ! empty( $current_css_file ) ) {
				wp_trash_post( $current_css_file );
			}

			update_option( 'seedprod_global_css_page_id', $id );
			seedprod_pro_generate_css_file( $id, $css );
		} else {
			$code = seedprod_pro_extract_page_css( $v1['post_content'], $id );
			update_post_meta( $id, '_seedprod_theme_template_condition', $meta->_seedprod_theme_template_condition[0] );
			update_post_meta( $id, '_seedprod_css', $code['css'] );
			update_post_meta( $id, '_seedprod_html', $code['html'] );
			seedprod_pro_generate_css_file( $id, $code['css'] );

			// process conditon to see if we need to create a placeholder page.
			$conditions = $meta->_seedprod_theme_template_condition[0];

			if ( ! empty( $conditions ) ) {

				$conditions = json_decode( $conditions );
				if (is_array($conditions)) {
					if (1 === count($conditions) && 'include' === $conditions[0]->condition && 'is_page(x)' === $conditions[0]->type && ! empty($conditions[0]->value) && ! is_numeric($conditions[0]->value)) {
						// check if slug exists.
						$slug_tablename  = $wpdb->prefix . 'posts';
						$sql             = "SELECT id FROM $slug_tablename WHERE post_name = %s AND post_type = 'page'";
						$safe_sql        = $wpdb->prepare($sql, $conditions[0]->value); // phpcs:ignore
					$this_slug_exist = $wpdb->get_var($safe_sql);// phpcs:ignore
					if (empty($this_slug_exist)) {
						$page_details = array(
							'post_title'   => $v1['post_title'],
							'post_name'    => $conditions[0]->value,
							'post_content' => __('This page was auto-generated and is a placeholder page for the SeedProd theme. To manage the contents of this page please visit SeedProd > Theme Builder in the left menu in WordPress. ', 'seedprod-pro'),
							'post_status'  => 'publish',
							'post_type'    => 'page',
						);
						wp_insert_post($page_details);
					}
					}
				}
			}
		}
	}

	// find and replace shortcodes
	foreach ( $import_page_array as $t => $val ) {
        if ($val['title'] != 'Global CSS') {
            //$theme_page_content =
            $post_content          = $val['post_content'];
            $post_content_filtered = $val['post_content_filtered'];
            $post_id               = $val['id'];

            foreach ($shortcode_array as $k => $t) {
                $shortcode_page_title = $shortcode_array[ $k ]['page_title'];
                $fetch_shortcode_key  = array_search($shortcode_page_title, array_column($import_page_array, 'title'));
                $fetch_shortcode_id   = $import_page_array[ $fetch_shortcode_key ]['id'];

                $shortcode_page_sc = $shortcode_array[ $k ]['shortcode'];
                $shortcode_page_sc = str_replace('[sp_template_part id="', '', $shortcode_page_sc);
                $shortcode_page_sc = str_replace('"]', '', $shortcode_page_sc);

                if ($fetch_shortcode_id) {
                    $shortcode_array[ $k ]['updated_shortcode'] = '[sp_template_part id="' . $fetch_shortcode_id . '"]';
                    $post_content                               = str_replace($shortcode_array[ $k ]['shortcode'], $shortcode_array[ $k ]['updated_shortcode'], $post_content);

                    $shortcode_array[ $k ]['updated_shortcode_filtered'] = '"templateparts":"' . $fetch_shortcode_id . '"';
                    $shortcode_array[ $k ]['shortcode_filtered_id']      = $shortcode_page_sc;
                    $shortcode_array[ $k ]['shortcode_filtered']         = '"templateparts":"' . $shortcode_page_sc . '"';

                    $post_content_filtered = str_replace($shortcode_array[ $k ]['shortcode_filtered'], $shortcode_array[ $k ]['updated_shortcode_filtered'], $post_content_filtered);

                    // update generated html
                    $generate_html = get_post_meta($post_id, '_seedprod_html', true);
                    $generate_html = str_replace($shortcode_array[ $k ]['shortcode'], $shortcode_array[ $k ]['updated_shortcode'], $generate_html);
                    update_post_meta($post_id, '_seedprod_html', $generate_html);
                }
            }

            global $wpdb;
            $tablename     = $wpdb->prefix . 'posts';
            $sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
            $safe_sql      = $wpdb->prepare($sql, $post_content_filtered, $post_content, $post_id); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        	$update_result = $wpdb->get_var($safe_sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        }
	}

}

/**
 * Process Imports Theme Templates JSON
 * @return void.
 */
function seedprod_pro_create_blog_and_home_for_theme() {
	if ( check_ajax_referer( 'seedprod_pro_create_blog_and_home_for_theme' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_themetemplate_create_default_pages_capability', 'manage_options' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		// create front page and blog page
		$posts_page_id = get_page_by_path( 'blog' );
		// Check if the page already exists
		if ( empty( $posts_page_id ) ) {
			$posts_page_id = wp_insert_post(
				array(
					'comment_status' => 'close',
					'ping_status'    => 'close',
					'post_author'    => 1,
					'post_title'     => 'Blog',
					'post_name'      => 'blog',
					'post_status'    => 'publish',
					'post_content'   => '',
					'post_type'      => 'page',
				)
			);
		} else {
			$posts_page_id = $posts_page_id->ID;
		}

		$front_page_id = get_page_by_path( 'home' );
		// Check if the page already exists
		if ( empty( $front_page_id ) ) {
			$front_page_id = wp_insert_post(
				array(
					'comment_status' => 'close',
					'ping_status'    => 'close',
					'post_author'    => 1,
					'post_title'     => 'Home',
					'post_name'      => 'home',
					'post_status'    => 'publish',
					'post_content'   => '',
					'post_type'      => 'page',
				)
			);
		} else {
			$front_page_id = $front_page_id->ID;
		}

		update_option( 'show_on_front', 'page' );
		update_option( 'page_for_posts', $posts_page_id );
		update_option( 'page_on_front', $front_page_id );
	}
}

/**
 * Short circuit new SeedProd theme pages
 *
 * @return void.
 */
function seedprod_pro_new_page_to_seedprod() {
	if ( ! empty( $_GET['page'] ) && 'seedprod_pro_template' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['from'] ) && 'post' === $_GET['from'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$theme_enabled = get_option( 'seedprod_theme_enabled' );
			if ( ! empty( $theme_enabled ) ) {
				$lpage_id    = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$template_id = 71;

				// get template code
				$template_code = seedprod_pro_get_template_code( $template_id );

				// merge in template code to settings
				global $wpdb;
				$tablename               = $wpdb->prefix . 'posts';
				$sql                     = "SELECT * FROM $tablename WHERE id = %d";
				$safe_sql                = $wpdb->prepare( $sql, $lpage_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$lpage                   = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$settings                = json_decode( $lpage->post_content_filtered, true );
				$settings['template_id'] = $template_id;
				if ( 99999 != $template_id ) {
					unset( $settings['document'] );
					$template_code_merge = json_decode( $template_code, true );
					$settings            = $settings + $template_code_merge;
				}

				if ( ! empty( $lpage->post_content ) ) {
					require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
					$current_content                  = $lpage->post_content;
					$settings['document']['sections'] = json_decode( $seedprod_current_content );
					$settings['document']['sections'][0]->rows[0]->cols[0]->blocks[0]->settings->txt = preg_replace( '/<!--(.*?)-->/', '', $current_content );
				}

				$settings['page_type'] = 'post';

				global $wpdb;
				$tablename = $wpdb->prefix . 'posts';
				$r         = $wpdb->update(
					$tablename,
					array(
						'post_content_filtered' => wp_json_encode( $settings ),
					),
					array( 'ID' => $lpage_id ),
					array(
						'%s',
					),
					array( '%d' )
				);

				update_post_meta( $lpage_id, '_seedprod_edited_with_seedprod', '1' );
				delete_post_meta( $lpage_id, '_seedprod_page' );

				// redirect to setup
				$edit_url = admin_url() . 'admin.php?page=seedprod_pro_builder&id=' . $lpage_id . '#/setup/' . $lpage_id;
				wp_safe_redirect( $edit_url );
				exit;
			}
		}
	}
}
add_action( 'admin_init', 'seedprod_pro_new_page_to_seedprod' );



