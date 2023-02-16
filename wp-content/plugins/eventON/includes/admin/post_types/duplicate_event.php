<?php
/**
 * Functions used to duplicate event
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin
 * @version     2.4.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Duplicate event action.
	function eventon_duplicate_event() {
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_page' == $_REQUEST['action'] ) ) ) {
			wp_die(__( 'No event to duplicate has been supplied!', 'eventon' ));
		}

		if( $_REQUEST['action'] != 'duplicate_event') return;

		// Get the original page
		$id = (isset($_GET['post']) ? sanitize_text_field($_GET['post']) : $_POST['post']);
		check_admin_referer( 'eventon-duplicate-event_' . $id );
		$post = eventon_get_event_to_duplicate($id);

		// Copy the page and insert it
		if (isset($post) && $post!=null) {
			$new_id = eventon_create_duplicate_from_event($post);

			if($new_id){
				$EVENT = new EVO_Event( $new_id);
				// hook after duplicate event created
				do_action( 'eventon_duplicate_product', $new_id, $post );

				do_action('evo_after_duplicate_event', $EVENT, $post);

				// Redirect to the edit screen for the new draft page
				wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
				exit;
			}else{
				wp_die(__( 'Event creation failed, could not create duplicate event:', 'eventon' ) . ' ' . $id);
			}
			
		} else {
			wp_die(__( 'Event creation failed, could not find original event:', 'eventon' ) . ' ' . $id);
		}
	}


// Duplication of the post
	function eventon_create_duplicate_from_event( $post, $parent = 0, $post_status = '' ) {
		global $wpdb;

		$new_post_author 	= wp_get_current_user();
		$new_post_date 		= current_time('mysql');
		$new_post_date_gmt 	= get_gmt_from_date($new_post_date);

		if ( $parent > 0 ) {
			$post_parent		= $parent;
			$post_status 		= $post_status ? $post_status : 'publish';
			$suffix 			= '';
		} else {
			$post_parent		= $post->post_parent;
			$post_status 		= $post_status ? $post_status : 'draft';
			$suffix 			= apply_filters('evo_duplicate_eventname_suffix', ' ' . __( '(Copy)', 'eventon' ) );
		}

		// Insert the new template in the post table
		$wpdb->insert(
			$wpdb->posts,
			array(
				'post_author'               => $new_post_author->ID,
				'post_date'                 => $new_post_date,
				'post_date_gmt'             => $new_post_date_gmt,
				'post_content'              => $post->post_content,
				'post_content_filtered'     => $post->post_content_filtered,
				'post_title'                => $post->post_title . $suffix,
				'post_excerpt'              => $post->post_excerpt,
				'post_status'               => $post_status,
				'post_type'                 => $post->post_type,
				'comment_status'            => $post->comment_status,
				'ping_status'               => $post->ping_status,
				'post_password'             => $post->post_password,
				'to_ping'                   => $post->to_ping,
				'pinged'                    => $post->pinged,
				'post_modified'             => $new_post_date,
				'post_modified_gmt'         => $new_post_date_gmt,
				'post_parent'               => $post_parent,
				'menu_order'                => $post->menu_order,
				'post_mime_type'            => $post->post_mime_type
			)
		);

		$new_post_id = $wpdb->insert_id;

		// Copy the taxonomies
		eventon_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

		// Copy the meta information
		eventon_duplicate_post_meta( $post->ID, $new_post_id );

		// ONLY for ticket addon event duplication
			if($post->post_type =='product'){

				$exclude = apply_filters( 'woocommerce_duplicate_product_exclude_children', false );

				if ( ! $exclude && ( $children_products = get_children( 'post_parent=' . $post->ID . '&post_type=product_variation' ) ) ) {
					foreach ( $children_products as $child ) {
						eventon_create_duplicate_from_event( eventon_get_event_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
					}
				}
			}

		return $new_post_id;
	}

/** Get a event from the database to duplicate */
	function eventon_get_event_to_duplicate($id) {
		global $wpdb;

		$id = absint( $id );
		if ( ! $id ) return false;

		$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
		if (isset($post->post_type) && $post->post_type == "revision"){
			$id = $post->post_parent;
			$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
		}
		return $post[0];
	}

// duplicate event taxonomies
	function eventon_duplicate_post_taxonomies($id, $new_id, $post_type) {
		global $wpdb;
		$taxonomies = get_object_taxonomies($post_type); //array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($id, $taxonomy);
			$post_terms_count = sizeof( $post_terms );
			for ($i=0; $i<$post_terms_count; $i++) {
				wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
			}
		}
	}

// Duplicate post meta for the event
	function eventon_duplicate_post_meta($id, $new_id) {
		global $wpdb;

		$sql     = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", absint( $id ) );

		// Fields to exclude during event replication
		$exclude = array_map( 'esc_sql', array_filter( apply_filters( 'eventon_duplicate_event_exclude_meta', array('tx_woocommerce_product_id') ) ) );

		if ( sizeof( $exclude ) ) {
			$sql .= " AND meta_key NOT IN ( '" . implode( "','", $exclude ) . "' )";
		}
		
		$post_meta = $wpdb->get_results( $sql );

		if ( sizeof($post_meta)) {
			$sql_query_sel = array();
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			$event_exlink = false;
			foreach ( $post_meta as $post_meta_row ) {
				
				// post value
				$post_value = $post_meta_row->meta_value;

				$sql_query_sel[] = $wpdb->prepare( "SELECT %d, %s, %s", $new_id, $post_meta_row->meta_key,  $post_value);
			}

			$sql_query .= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );

		}
	}
?>