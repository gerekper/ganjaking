<?php
/**
 * Get lpage Lists
 */
function seedprod_pro_get_lpage_list() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		global $wpdb;

		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';

		$sql = "SELECT id,post_title as name,meta_value as uuid FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

		$sql     .= ' WHERE post_status != "trash" AND post_type = "page" AND meta_key = "_seedprod_page_uuid"';
		$response = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		wp_send_json( $response );
	}
}

/**
 * Check Slug.
 */
function seedprod_pro_slug_exists() {
	if ( check_ajax_referer( 'seedprod_pro_slug_exists' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$post_name = isset( $_POST['post_name'] ) ? sanitize_text_field( wp_unslash( $_POST['post_name'] ) ) : '';
		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$sql       = "SELECT post_name FROM $tablename";
		$sql      .= ' WHERE post_name = %s';
		$safe_sql  = $wpdb->prepare( $sql, $post_name ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result    = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( empty( $result ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}

/**
 * New lpage.
 */
function seedprod_pro_new_lpage() {
	$get_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$get_id   = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( null !== $get_page && 'seedprod_pro_template' == $get_page && null !== $get_id && '0' == $get_id ) {
		// get theme code
		$id = absint( $get_id );

		$from     = '&from=';
		$get_from = ! empty( $_GET['from'] ) ? sanitize_text_field( wp_unslash( $_GET['from'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null !== $get_from ) {
			$from = '&from=' . $get_from;
		}

		$type     = 'lp';
		$get_type = ! empty( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null !== $get_type ) {
			$type = $get_type;
		} elseif ( null !== $get_from && ( 'page' == $get_from || 'post' == $get_from ) ) {
			// if we are coming from a page or post set the page type as post
			$theme_enabled = get_option( 'seedprod_theme_enabled' );
			$theme_builder = seedprod_pro_cu( 'themebuilder' );
			if ( ! empty( $theme_builder ) && ! empty( $theme_enabled ) ) {
				$type = 'post';
			}
		}

		// base page settings
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
		$settings            = json_decode( $seedprod_basic_lpage );
		$settings->is_new    = true;
		$settings->page_type = $type;

		$cpt = 'page';
		// seedprod ctp types
		$cpt_types = array(
			'cs',
			'mm',
			'p404',
			'header',
			'footer',
			'part',
			'page',
		);

		// if is a template part set to true.
		$template_parts = array(
			'header',
			'footer',
			'part',
			'page',
		);

		if ( in_array( $type, $cpt_types ) ) {
			$cpt = 'seedprod';
		}

		$slug       = '';
		$lpage_name = '';
		$menu_order = null;
		$conditions = null;

		// get temp themeplate data if this is a theme_template
		$get_theme_template = ! empty( $_GET['theme_template'] ) ? sanitize_text_field( wp_unslash( $_GET['theme_template'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( null !== $get_theme_template && null !== $get_id && '0' == $get_id ) {
			$temp_theme_template_data = get_option( 'seedprod_temp_theme_template_data' );
			if ( ! empty( $temp_theme_template_data ) ) {
				$temp_theme_template_data = json_decode( $temp_theme_template_data, true );
				$menu_order               = $temp_theme_template_data['template_priority'];
				$lpage_name               = $temp_theme_template_data['template_name'];
				$conditions               = $temp_theme_template_data['template_conditions'];
				// reset temp data
				update_option( 'seedprod_temp_theme_template_data', null );
			}
		}

		if ( 'cs' == $type ) {
			$slug                       = 'sp-cs';
			$lpage_name                 = $slug;
			$settings->no_conflict_mode = true;
		}
		if ( 'mm' == $type ) {
			$slug                       = 'sp-mm';
			$lpage_name                 = $slug;
			$settings->no_conflict_mode = true;
		}
		if ( 'p404' == $type ) {
			$slug                       = 'sp-p404';
			$lpage_name                 = $slug;
			$settings->no_conflict_mode = true;
		}
		if ( 'loginp' == $type ) {
			$slug                       = 'sp-login';
			$lpage_name                 = $slug;
			$settings->no_conflict_mode = true;
		}

		// if is a template part set the template as blank
		if ( in_array( $type, $template_parts ) ) {
			$settings->template_id = 71;
		}

		$settings = wp_json_encode( $settings );

		// Insert
		$id = wp_insert_post(
			array(
				'menu_order'            => $menu_order,
				'comment_status'        => 'closed',
				'ping_status'           => 'closed',
				'post_content'          => '',
				'post_status'           => 'draft',
				'post_title'            => 'seedprod',
				'post_type'             => $cpt,
				'post_name'             => $slug,
				'post_content_filtered' => $settings,
				'meta_input'            => array(
					'_seedprod_page'               => true,
					'_seedprod_page_uuid'          => wp_generate_uuid4(),
					'_seedprod_page_template_type' => $type,
				),
			),
			true
		);

		// record coming soon page_id
		if ( 'cs' == $type ) {
			update_option( 'seedprod_coming_soon_page_id', $id );
		}
		if ( 'mm' == $type ) {
			update_option( 'seedprod_maintenance_mode_page_id', $id );
		}
		if ( 'p404' == $type ) {
			update_option( 'seedprod_404_page_id', $id );
		}
		if ( 'loginp' == $type ) {
			update_option( 'seedprod_login_page_id', $id );
		}

		// If landing page set a temp name

		if ( 'lp' == $type ) {
			if ( is_numeric( $id ) ) {
				$lpage_name = esc_html__( 'New Page', 'seedprod-pro' ) . " (ID #$id)";
			} else {
				$lpage_name = esc_html__( 'New Page', 'seedprod-pro' );
			}
		}

		if ( in_array( $type, $template_parts ) ) {
			update_post_meta( $id, '_seedprod_is_theme_template', true );
			update_post_meta( $id, '_seedprod_theme_template_condition', wp_json_encode( $conditions ) );
		}

		wp_update_post(
			array(
				'ID'         => $id,
				'post_title' => $lpage_name,
			)
		);

		// got straight to builder for template parts, other wise go to templates
		if ( in_array( $type, $template_parts ) ) {
			wp_safe_redirect( 'admin.php?page=seedprod_pro_builder&id=' . $id . '#/setup/' . $id . '/block-options' );
			exit();
		} else {
			wp_safe_redirect( 'admin.php?page=seedprod_pro_template&id=' . $id . $from . '#/template/' . $id );
			exit();
		}

		exit();
	}
}

/**
 * lpage Datatable
 */
function seedprod_pro_lpage_datatable() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
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
			if ( 'all' == $filter ) {
				$filter = null;
			}
		}

		if ( ! empty( $_GET['s'] ) ) {
			$filter = null;
		}

		if ( ! empty( $filter ) ) {
			$post_status_compare = '=';
			if ( 'published' == $filter ) {
				$post_status = 'publish';
			}
			if ( 'drafts' == $filter ) {
				$post_status = 'draft';
			}
			if ( 'scheduled' == $filter ) {
				$post_status = 'future';
			}
			if ( 'archived' == $filter ) {
				$post_status = 'trash';
			}
		} else {
			$post_status_compare = '!=';
			$post_status         = 'trash';
		}
		$post_status_statement = ' post_status ' . $post_status_compare . ' %s ';

		if ( ! empty( $_GET['s'] ) ) {
			$search_term = '%' . trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) . '%';
		}

		$order_by           = 'id';
		$order_by_direction = 'DESC';
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			if ( 'date' == $orderby ) {
				$order_by = 'post_modified';
			}

			if ( 'name' == $orderby ) {
				$order_by = 'post_title';
			}

			$direction = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : null;
			if ( 'desc' == $direction ) {
				$order_by_direction = 'DESC';
			} else {
				$order_by_direction = 'ASC';
			}
		}
		$order_by_statement = 'ORDER BY ' . $order_by . ' ' . $order_by_direction;

		$offset = 0;
		if ( empty( $_POST['s'] ) ) {
			$offset = ( $current_page - 1 ) * $per_page;
		}

		// Get records
		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';

		if ( empty( $_GET['s'] ) ) {
			$sql      = 'SELECT * FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement . ' ' . $order_by_statement . ' LIMIT %d OFFSET %d';
			$safe_sql = $wpdb->prepare( $sql, $post_status, $per_page, $offset ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$sql      = 'SELECT * FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement . ' AND post_title LIKE %s ' . $order_by_statement . ' LIMIT %d OFFSET %d';
			$safe_sql = $wpdb->prepare( $sql, $post_status, $search_term, $per_page, $offset ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$results = $wpdb->get_results( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$login_page_id = get_option( 'seedprod_login_page_id' );
		$data          = array();
		foreach ( $results as $v ) {
			// Skip row to prevent current Login Page post from displaying here
			if ( $v->ID === $login_page_id ) {
				continue; }

			// Format Date
			//$modified_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_modified));

			$modified_at = gmdate( 'Y/m/d', strtotime( $v->post_modified ) );

			$posted_at = gmdate( 'Y/m/d', strtotime( $v->post_date ) );

			$url = get_permalink( $v->ID );

			if ( 'publish' == $v->post_status ) {
				$status = 'Published';
			}
			if ( 'draft' == $v->post_status ) {
				$status = 'Draft';
			}
			if ( 'future' == $v->post_status ) {
				$status = 'Scheduled';
			}
			if ( 'trash' == $v->post_status ) {
				$status = 'Trash';
			}

			// Load Data
			$data[] = array(
				'id'          => $v->ID,
				'name'        => $v->post_title,
				'status'      => $status,
				'post_status' => $v->post_status,
				'url'         => $url,
				'modified_at' => $modified_at,
				'posted_at'   => $posted_at,
			);
		}

		$totalitems = seedprod_pro_lpage_get_data_total( $filter );
		$views      = seedprod_pro_lpage_get_views( $filter );

		$response = array(
			'rows'        => $data,
			'totalitems'  => $totalitems,
			'totalpages'  => ceil( $totalitems / 10 ),
			'currentpage' => $current_page,
			'views'       => $views,
		);

		wp_send_json( $response );
	}
}

/**
 * Get data total.
 *
 * @param string $filter Filter(post status).
 * @return string $results Posts count.
 */
function seedprod_pro_lpage_get_data_total( $filter = null ) {
	if ( ! empty( $filter ) ) {
		$post_status_compare = '=';
		if ( 'published' == $filter ) {
			$post_status = 'publish';
		}
		if ( 'drafts' == $filter ) {
			$post_status = 'draft';
		}
		if ( 'scheduled' == $filter ) {
			$post_status = 'future';
		}
		if ( 'archived' == $filter ) {
			$post_status = 'trash';
		}
	} else {
		$post_status_compare = '!=';
		$post_status         = 'trash';
	}
	$post_status_statement = ' post_status ' . $post_status_compare . ' %s ';

	if ( ! empty( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search_term = '%' . trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) . '%'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	global $wpdb;

	$tablename      = $wpdb->prefix . 'posts';
	$meta_tablename = $wpdb->prefix . 'postmeta';

	if ( empty( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sql      = 'SELECT count(*) FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement;
		$safe_sql = $wpdb->prepare( $sql, $post_status ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	} else {
		$sql      = 'SELECT * FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement . ' AND post_title LIKE %s ';
		$safe_sql = $wpdb->prepare( $sql, $post_status, $search_term ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	$results = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $results;
}

/**
 * Get Views.
 *
 * @param string $filter Post filter.
 * @return string $return Posts count.
 */
function seedprod_pro_lpage_get_views( $filter = null ) {
	$views   = array();
	$current = ( ! empty( $filter ) ? $filter : 'all' );
	$current = sanitize_text_field( $current );

	global $wpdb;
	$tablename      = $wpdb->prefix . 'posts';
	$meta_tablename = $wpdb->prefix . 'postmeta';

	//All link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "page" AND post_status != "trash"  AND meta_key = "_seedprod_page"';

	$results      = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$class        = ( 'all' == $current ? ' class="current"' : '' );
	$all_url      = remove_query_arg( 'filter' );
	$views['all'] = $results;

	//Published link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "publish" ';

	$results            = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$running_url        = add_query_arg( 'filter', 'publish' );
	$class              = ( 'publish' == $current ? ' class="current"' : '' );
	$views['published'] = $results;

	//Drafts link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "draft" ';

	$results         = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$upcoming_url    = add_query_arg( 'filter', 'drafts' );
	$class           = ( 'drafts' == $current ? ' class="current"' : '' );
	$views['drafts'] = $results;

	//Scheduled link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "future" ';

	$results            = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$ended_url          = add_query_arg( 'filter', 'scheduled' );
	$class              = ( 'scheduled' == $current ? ' class="current"' : '' );
	$views['scheduled'] = $results;

	//Trash link
	$sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

	$sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "trash" ';

	$results           = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$archived_url      = add_query_arg( 'filter', 'archived' );
	$class             = ( 'archived' == $current ? ' class="current"' : '' );
	$views['archived'] = $results;

	return $views;
}

/**
 * Duplicate lpage
 */
function seedprod_pro_duplicate_lpage() {
	if ( check_ajax_referer( 'seedprod_pro_duplicate_lpage' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id = '';
		if ( ! empty( $_GET['id'] ) ) {
			$id = absint( $_GET['id'] );
		}

		$post = get_post( $id );
		$json = $post->post_content_filtered;

		$args = array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => $post->post_content,
			//'post_content_filtered' => $post->post_content_filtered,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . '- Copy',
			'post_type'      => 'page',
			'post_name'      => '',
			'meta_input'     => array(
				'_seedprod_page'      => true,
				'_seedprod_page_uuid' => wp_generate_uuid4(),
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
* Archive Selected lpage.
*/
function seedprod_pro_archive_selected_lpages() {
	if ( check_ajax_referer( 'seedprod_pro_archive_selected_lpages' ) ) {
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
* Unarchive Selected lpage.
*/
function seedprod_pro_unarchive_selected_lpages( $ids ) {
	if ( check_ajax_referer( 'seedprod_pro_unarchive_selected_lpages' ) ) {
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
* Delete Archived lpage
*/
function seedprod_pro_delete_archived_lpages() {
	if ( check_ajax_referer( 'seedprod_pro_delete_archived_lpages' ) ) {
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
 * Save/Update lpage
 */
function seedprod_pro_save_lpage() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		$has_permission = false;
		if ( current_user_can( apply_filters( 'seedprod_save_lpage_capability', 'edit_others_posts' ) ) ) {
			$has_permission = true;
		}

		$sp_post         = $_POST;
		$sp_current_user = wp_get_current_user();

		// Save personalization preferences.
		update_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', $sp_post['personalization_preferences'] );

		if ( false === $has_permission ) {
			header( 'Content-Type: application/json' );
			header( 'Status: 400 Bad Request' );
			echo '0';
			exit();
		}

		// clean slashes post
		$sp_post['lpage_html'] = stripslashes_deep( $sp_post['lpage_html'] );

		// remove unneeded code
		$html = $sp_post['lpage_html'];
		if ( ! empty( $html ) ) {
			$html = preg_replace( "'<span class=\"sp-hidden\">START-REMOVE</span>[\s\S]+?<span class=\"sp-hidden\">END-REMOVE</span>'", '', $html );
			$html = preg_replace( "'<span class=\"sp-hidden\">START-COUNTDOWN-REMOVE</span>[\s\S]+?<span class=\"sp-hidden\">END-COUNTDOWN-REMOVE</span>'", '', $html );
			$html = preg_replace( "'seedprod-jscode'", 'script', $html );
			$html = preg_replace( "'<!---->'", '', $html );
			$html = preg_replace( "'<!--'", '', $html );
			$html = preg_replace( "'-->'", '', $html );
			// html custom comment
			$html = preg_replace( "'--!'", '-->', $html );
			$html = preg_replace( "'!--'", '<!--', $html );
			// end html custom comment
			$html = preg_replace( "'contenteditable=\"true\"'", '', $html );
			$html = preg_replace( "'spellcheck=\"false\"'", '', $html );
			$html = str_replace( 'function(e,n,r,i){return fn(t,e,n,r,i,!0)}', '', $html );
			// remove preview animation
			$html = str_replace( 'animate__', '', $html );
			// remove sp-theme-template id
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/simple_html_dom.php';
			$phtml                   = seedprod_str_get_html( $html );
			$sp_theme_templates_divs = $phtml->find( '#sp-theme-template' );
			foreach ( $sp_theme_templates_divs as $k => $v ) {
				$html = $v->innertext;
				break;
			}
		}

		// sanitize post
		$lpage_id          = absint( $sp_post['lpage_id'] );
		$lpage_name        = sanitize_text_field( $sp_post['lpage_name'] );
		$lpage_slug        = sanitize_title( $sp_post['lpage_slug'] );
		$lpage_post_status = sanitize_title( $sp_post['lpage_post_status'] );
		$settings          = $sp_post['settings'];
		//$settings = wp_json_encode(json_decode( stripslashes($sp_post['settings'])));

		// set update array
		$update       = array();
		$update['ID'] = $lpage_id;
		if ( ! empty( $lpage_name ) ) {
			$update['post_title'] = $lpage_name;
		}
		if ( ! empty( $lpage_slug ) ) {
			$update['post_name'] = $lpage_slug;
		}
		if ( ! empty( $lpage_post_status ) ) {
			$update['post_status'] = $lpage_post_status;
		}
		if ( ! empty( $html ) ) {
			$update['post_content'] = $html;
		}
		if ( ! empty( $settings ) ) {
			$update['post_content_filtered'] = $settings;
		}

		$status = '';
		if ( empty( $lpage_id ) ) {
			wp_die();
		} else {
			$check_post_type = json_decode( stripslashes( $settings ) );
			if ( 'post' == $check_post_type->page_type ) {
				update_post_meta( $lpage_id, '_seedprod_edited_with_seedprod', '1' );
				delete_post_meta( $lpage_id, '_seedprod_page' );
			} else {
				update_post_meta( $lpage_id, '_seedprod_page', '1' );
			}
			if ( ! empty( $sp_post['save_type'] ) && 'autosave' == $sp_post['save_type'] ) {
				$update['post_ID'] = $lpage_id;
				$id                = @wp_create_post_autosave( $update ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$status            = 'autosave';
			} else {
				
				// publish if is theme template and first save
				$template_parts = array(
					'header',
					'footer',
					'part',
					'page',
				);
				if ( in_array( $check_post_type->page_type, $template_parts ) ) {
					// see if current content is empty
					$has_content = get_post_field( 'post_content', $lpage_id );
					if ( empty( $has_content ) && ! empty( $update['post_content'] ) ) {
						$update['post_status'] = 'publish';
					}
				}
				

				// remove action so they don't conflict with the save. Yoast SEO was trying to analytize this content.
				remove_all_actions( 'wp_insert_post' );
				if ( is_multisite() ) {
					kses_remove_filters();
					wp_update_post( $update );
					kses_init_filters();
				} else {
					wp_update_post( $update );
				}
				$status = 'updated';
			}

			if ( class_exists( 'SeedProd_Tracking' ) ) {
				$tracking = new SeedProd_Tracking();
				if ( ! empty( $check_post_type->document->sections ) ) {
					$block_usage_data = $tracking->get_block_data( $check_post_type->document->sections );
					update_post_meta( $lpage_id, '_seedprod_block_usage', $block_usage_data );
				}
			}
		}

		
		// if is page theme template extract and save css
		$seedprod_is_theme_template = get_post_meta( $lpage_id, '_seedprod_is_theme_template', true );
		if ( empty( $seedprod_is_theme_template ) ) {
			$seedprod_is_theme_template = get_post_meta( $lpage_id, '_seedprod_edited_with_seedprod', true );
		}
		if ( empty( $seedprod_is_theme_template ) ) {
			$css_settings = json_decode( $settings );
			if ( isset( $css_settings['page_type'] ) && 'post' == $css_settings['page_type'] ) {
				$seedprod_is_theme_template = 1;
			}
		}
		// extract css and save in post meta
		if ( ! empty( $seedprod_is_theme_template ) ) {
			if ( 'css' == $check_post_type->page_type ) {
				update_post_meta( $lpage_id, '_seedprod_css', $check_post_type->document->settings->globalHeadCss );
				update_post_meta( $lpage_id, '_seedprod_builder_css', $check_post_type->document->settings->globalHeadCssBuilder );
				//update_post_meta( $lpage_id, '_seedprod_custom_css', $check_post_type->document->settings->customCss );
				//$css = $check_post_type->document->settings->globalHeadCss . $check_post_type->document->settings->customCss;
				update_post_meta( $lpage_id, '_seedprod_custom_css', '' );
				$css = $check_post_type->document->settings->globalHeadCss;
				seedprod_pro_generate_css_file( $lpage_id, $css );
			} else {
				if ( ! empty( $html ) ) {
					$code = seedprod_pro_extract_page_css( $html, $lpage_id );
					update_post_meta( $lpage_id, '_seedprod_css', $code['css'] );
					update_post_meta( $lpage_id, '_seedprod_html', $code['html'] );
					seedprod_pro_generate_css_file( $lpage_id, $code['css'] );
				}
			}
		}
		

		$response = array(
			'status' => $status,
			'id'     => $lpage_id,
			//'revisions' => $revisions,
		);

		// clear any migration flags
		$i = get_option( 'seedprod_csp4_imported' );
		if ( 1 == $i ) {
			delete_option( 'seedprod_csp4_imported' );
			delete_option( 'seedprod_show_csp4' );
			update_option( 'seedprod_csp4_migrated', true );
		}

		$i = get_option( 'seedprod_cspv5_imported' );
		if ( 1 == $i ) {
			delete_option( 'seedprod_cspv5_imported' );
			delete_option( 'seedprod_show_cspv5' );
			update_option( 'seedprod_cspv5_migrated', true );
		}

		// migrate landing page if id exists
		$settings = json_decode( stripslashes_deep( $sp_post['settings'] ) );
		if ( ! empty( $settings->cspv5_id ) ) {
			$cspv5_id = $settings->cspv5_id;
			global $wpdb;
			$tablename = $wpdb->prefix . 'cspv5_pages';
			$r         = $wpdb->update(
				$tablename,
				array(
					'meta' => 'migrated',
				),
				array( 'id' => $cspv5_id ),
				array(
					'%s',
				),
				array( '%d' )
			);
		}

		
		$domain_mapping_status = ! empty( $settings->domain_mapping_status ) ?
			$settings->domain_mapping_status :
			false;

		$domain_mapping = ! empty( $settings->domain_mapping ) ?
			$settings->domain_mapping :
			'';

		$domain_mapping_force_https = ! empty( $settings->domain_mapping_force_https ) ?
			$settings->domain_mapping_force_https :
			false;

		$domain_mapping_error = seedprod_pro_domain_mapping_db_update(
			$lpage_id,
			$domain_mapping_status,
			$domain_mapping,
			$domain_mapping_force_https
		);

		if ( ! empty( $domain_mapping_error ) ) {
			$response['domain_mapping_error'] = $domain_mapping_error; }
		

		wp_send_json( $response );
	}
}

/**
 * Get revisions.
 */
function seedprod_pro_get_revisisons() {
	$lpage_id  = isset( $_POST['lpage_id'] ) ? absint( wp_unslash( $_POST['lpage_id'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$revisions = wp_get_post_revisions( $lpage_id, array( 'numberposts' => 50 ) );
	foreach ( $revisions as $k => $v ) {
		$v->time_ago           = human_time_diff( strtotime( $v->post_date_gmt ) );
		$v->post_date_formated = gmdate( 'M j \a\t ' . get_option( 'time_format' ), strtotime( $v->post_date ) );
		$authordata            = get_userdata( $v->post_author );
		$v->author_name        = $authordata->data->user_nicename;
		$v->author_email       = md5( $authordata->data->user_email );
		unset( $v->post_content );
		if ( empty( $v->post_content_filtered ) ) {
			unset( $revisions[ $k ] );
		}

		// $created_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_date));
	}
	$revisions = array_values( $revisions );

	$response = array(
		'id'        => $lpage_id,
		'revisions' => $revisions,
	);

	wp_send_json( $response );
}




/**
 * Backgrounds sideload.
 *
 * @return void
 */
function seedprod_pro_backgrounds_sideload() {
	if ( check_ajax_referer( 'seedprod_pro_backgrounds_sideload' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$image = '';
		if ( isset( $_REQUEST['image'] ) ) {
			$image     = urldecode( sanitize_text_field( wp_unslash( $_REQUEST['image'] ) ) );
			$file      = media_sideload_image( $image . '&type=.jpg', 0, null, 'src' );
			$media_id  = attachment_url_to_postid( $file );
			$imgsrcset = wp_get_attachment_image_srcset( $media_id );

			$data_result = array(
				'file'      => $file,
				'media_id'  => $media_id,
				'imgsrcset' => $imgsrcset,
			);

			if ( is_wp_error( $file ) ) {
				$error_message = $file->get_error_message();
				wp_send_json( 0 );
			} else {
				wp_send_json( $data_result );
			}
		}

		exit();
	}
}

/**
 * Backgrounds download.
 *
 * @return void
 */
function seedprod_pro_backgrounds_download() {
	if ( check_ajax_referer( 'seedprod_pro_backgrounds_download' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$image = '';
		if ( isset( $_REQUEST['image'] ) ) {
			$image    = urldecode( sanitize_text_field( wp_unslash( $_REQUEST['image'] ) ) );
			$response = wp_remote_get( SEEDPROD_PRO_BACKGROUND_DOWNLOAD_API_URL . '?image=' . $image );
			if ( ! is_wp_error( $response ) ) {
				wp_send_json( 1 );
			}
		}

		exit();
	}
}


/**
 * Get UTC Offset.
 *
 * @return void
 */
function seedprod_pro_get_utc_offset() {
	if ( check_ajax_referer( 'seedprod_pro_get_utc_offset' ) ) {
		$_POST = stripslashes_deep( $_POST );

		$timezone  = isset( $_POST['timezone'] ) ? sanitize_text_field( wp_unslash( $_POST['timezone'] ) ) : null;
		$ends      = isset( $_POST['ends'] ) ? sanitize_text_field( wp_unslash( $_POST['ends'] ) ) : null;
		$ends_time = isset( $_POST['ends_time'] ) ? sanitize_text_field( wp_unslash( $_POST['ends_time'] ) ) : null;

		//$ends = substr($ends, 0, strpos($ends, 'T'));
		$ends           = $ends . ' ' . $ends_time;
		$ends_timestamp = strtotime( $ends . ' ' . $timezone );
		$ends_utc       = gmdate( 'Y-m-d H:i:s', $ends_timestamp );

		// countdown status
		$countdown_status = '';
		if ( ! empty( $starts_utc ) && time() < strtotime( $starts_utc . ' UTC' ) ) {
			$countdown_status = __( 'Starts in', 'seedprod-pro' ) . ' ' . human_time_diff( time(), $starts_timestamp );
		} elseif ( ! empty( $ends_utc ) && time() > strtotime( $ends_utc . ' UTC' ) ) {
			$countdown_status = __( 'Ended', 'seedprod-pro' ) . ' ' . human_time_diff( time(), $ends_timestamp ) . ' ago';
		}

		$response = array(
			'ends_timestamp'   => $ends_timestamp,
			'countdown_status' => $countdown_status,
		);

		wp_send_json( $response );
	}
}

/**
 * Template subscribe.
 *
 * @return void
 */
function seedprod_pro_template_subscribe() {
	update_option( 'seedprod_free_templates_subscribed', true );
	exit();
}

/**
 * Save/Update lpages Template.
 */
function seedprod_pro_save_template() {
	// get template code and set name and slug
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$_POST = stripslashes_deep( $_POST );

		$status   = false;
		$lpage_id = null;

		if ( empty( absint( $_POST['lpage_id'] ) ) ) {
			// shouldn't get here
			$response = array(
				'status' => $status,
				'id'     => $lpage_id,
				'code'   => '',
			);

			wp_send_json( $response, 403 );
		} else {
			$lpage_id    = absint( $_POST['lpage_id'] );
			$template_id = isset( $_POST['lpage_template_id'] ) ? absint( $_POST['lpage_template_id'] ) : null;

			if ( 99999 != $template_id ) {
				$template_code = seedprod_pro_get_template_code( $template_id );
			}

			// merge in template code to settings
			global $wpdb;
			$tablename               = $wpdb->prefix . 'posts';
			$sql                     = "SELECT * FROM $tablename WHERE id = %d"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$safe_sql                = $wpdb->prepare( $sql, $lpage_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$lpage                   = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$settings                = json_decode( $lpage->post_content_filtered, true );
			$settings['template_id'] = $template_id;
			if ( 99999 != $template_id ) {
				unset( $settings['document'] );
				$template_code_merge = json_decode( $template_code, true );
				if ( is_array( $template_code_merge ) ) {
					$settings = $settings + $template_code_merge;
				}
			}
			// TODO pull in current pages content if any exists, make sure sections is empty before adding
			if ( ! empty( $_POST['lpage_type'] ) && 'post' == $_POST['lpage_type'] ) {
				if ( ! empty( $lpage->post_content ) ) {
					require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
					$current_content = $lpage->post_content;
					//if(empty($settings['document']['sections'])){
						$settings['document']['sections'] = json_decode( $seedprod_current_content );
						$settings['document']['sections'][0]->rows[0]->cols[0]->blocks[0]->settings->txt = preg_replace( '/<!--(.*?)-->/', '', $current_content );
					//}
				}
			}

			$settings['page_type'] = sanitize_text_field( wp_unslash( $_POST['lpage_type'] ) );

			// set post type to landong page if they do not have the theme builder
			$theme_enabled = get_option( 'seedprod_theme_enabled' );
			$theme_builder = seedprod_pro_cu( 'themebuilder' );
			if ( 'post' == $settings['page_type'] && empty( $theme_builder ) ) {
				$settings['page_type'] = 'lp';
			}
			if ( 'post' == $settings['page_type'] && ! empty( $theme_builder ) && empty( $theme_enabled ) ) {
				$settings['page_type'] = 'lp';
			}

			// save settings
			// $r = wp_update_post(
			//     array(
			//         'ID' => $lpage_id,
			//         'post_title'=>sanitize_text_field($_POST['lpage_name']),
			//         'post_content_filtered'=> json_encode($settings),
			//         'post_name' => sanitize_title($_POST['lpage_slug']),
			//       )
			// );

			global $wpdb;
			$tablename = $wpdb->prefix . 'posts';
			$r         = $wpdb->update(
				$tablename,
				array(
					'post_title'            => isset( $_POST['lpage_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lpage_name'] ) ) : '',
					'post_content_filtered' => wp_json_encode( $settings ),
					'post_name'             => isset( $_POST['lpage_slug'] ) ? sanitize_title( wp_unslash( $_POST['lpage_slug'] ) ) : '',
				),
				array( 'ID' => $lpage_id ),
				array(
					'%s',
					'%s',
					'%s',
				),
				array( '%d' )
			);

			$status = 'updated';
		}

		$response = array(
			'status' => $status,
			'id'     => $lpage_id,
			'code'   => $template_code,
		);

		wp_send_json( $response );
	}
}

/**
 * Get template code.
 *
 * @param string $id Post ID.
 * @return string $code Response message/Error message.
 */
function seedprod_pro_get_template_code( $id ) {
	// Get themes
	$code = '';

	$apikey = get_option( 'seedprod_api_token' );
	if ( empty( $apikey ) ) {
		$url = SEEDPROD_PRO_API_URL . 'templates-preview?id=' . $id . '&filter=template_code&api_token=' . $apikey;
	} else {
		$url = SEEDPROD_PRO_API_URL . 'templates?id=' . $id . '&filter=template_code&api_token=' . $apikey;
	}

	$response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		$code = $response->get_error_message();
	} else {
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( '200' == $response_code ) {
			//set_transient('seedprod_template_code_'.$id,$response['body'],86400);
			$code = $response['body'];
			//error_log($code);
		} else {
			$code = __( "<br><br>Please enter a valid license key to access the themes. You can still proceed to create a page with the default theme.<br> <a class='seedprod_no_themes' href='?theme=0'>Click to continue &#8594;</a>", 'seedprod-pro' );
		}
	}

	return $code;
}

/**
 * Get namespaced custom CSS.
 *
 * @return void
 */
function seedprod_pro_get_namespaced_custom_css() {
	if ( check_ajax_referer( 'seedprod_pro_get_namespaced_custom_css' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		if ( ! empty( $_POST['css'] ) ) {
			$css = sanitize_text_field( wp_unslash( $_POST['css'] ) );
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/seedprod_lessc.inc.php';
			$less  = new seedprod_lessc();
			$style = $less->parse( '.sp-html {' . $css . '}' );
			//echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '';
			exit();
		}
	}
}


/**
 * Update Domain Mapping Database Table
 * Returns nothing if success, string if error.
 */
function seedprod_pro_domain_mapping_db_update( $id, $status, $url, $force_https ) {

	global $wpdb;
	$tablename = $wpdb->prefix . 'sp_domain_mapping';

	$where        = array( 'mapped_page_id' => $id );
	$where_format = array( '%d' );

	if ( false === $status || empty( $url ) ) {
		$delete = $wpdb->delete( $tablename, $where, $where_format );
		return ( false === $delete ?
					'Delete Error: Not able to remove Domain Mapping entries on save.' : '' );

	} else {

		$url_no_whitespace = preg_replace( '/\s+/', '', $url );
		$url_no_scheme     = preg_replace( '(^.*:\/\/)', '', $url_no_whitespace );
		$url_add_scheme    = ( $force_https ? 'https://' . $url_no_scheme : 'http://' . $url_no_scheme );

		$url_parsed = wp_parse_url( $url_add_scheme );

		if ( is_array( $url_parsed ) ) {

			$host = array_key_exists( 'host', $url_parsed ) ? $url_parsed['host'] : '';
			$path = array_key_exists( 'path', $url_parsed ) ? trim( $url_parsed['path'], '/' ) : '';

			$data        = array(
				'domain'         => sanitize_text_field( $host ),
				'path'           => sanitize_text_field( $path ),
				'mapped_page_id' => absint( $id ),
				'force_https'    => rest_sanitize_boolean( ( $force_https ? 1 : 0 ) ),
			);
			$data_format = array( '%s', '%s', '%d', '%d' );

			$updated = $wpdb->update( $tablename, $data, $where, $data_format, $where_format );

			if ( $updated > 0 ) {
				return;
			} else {
				$delete = $wpdb->delete( $tablename, $where, $where_format );
				$insert = $wpdb->insert( $tablename, $data, $data_format );
				return ( false === $delete || false === $insert ?
					'Error: Domain Mapping table not updated upon save.' : '' );
			}
		} else {
			return 'Invalid URL: Domain Mapping table not updated upon save.';
		}
	}
}

/**
 * Get domain_mapping row by lpage_id
 */
function seedprod_pro_get_domain_mapping_domain() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$lpage_id = isset( $_POST['lpage_id'] ) ? absint( $_POST['lpage_id'] ) : null;

		if ( empty( $lpage_id ) ) {
			$response = false;
			wp_send_json( $response, 400 );
		} else {
			global $wpdb;
			$tablename = $wpdb->prefix . 'sp_domain_mapping';
			$sql       = "SELECT * FROM $tablename WHERE mapped_page_id = %d LIMIT 1";
			$safe_sql  = $wpdb->prepare( $sql, absint( $lpage_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$response  = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			wp_send_json( $response );
		}
	}
}

