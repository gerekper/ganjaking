<?php

class WC_Wishlists_Wishlist_Admin {

	public static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			$instance = new WC_Wishlists_Wishlist_Admin();
		}

		return $instance;
	}

	public function __construct() {


		add_filter( 'manage_edit-wishlist_columns', array( &$this, 'add_columns' ) );
		add_filter( 'manage_edit-wishlist_sortable_columns', array( &$this, 'sortable_columns' ) );
		add_action( 'manage_wishlist_posts_custom_column', array( &$this, 'render_columns' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( $this, 'custom_filters' ) );

		add_action( 'load-edit.php', array( &$this, 'edit_wishlist_load' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 99 );

		add_action( 'delete_post', array( &$this, 'on_delete_post' ) );
		add_action( 'save_post', array( &$this, 'on_save_post' ), 10, 2 );

		add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );

		add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

		add_filter( 'post_row_actions', array( $this, 'custom_post_row_actions' ), 10, 2 );

		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );
	}

	public function disable_gutenberg( $is_enabled, $post_type ) {
		if ( $post_type === 'wishlist' ) {
			return false;
		}

		return $is_enabled;
	}

	public function custom_post_row_actions( $actions, $post ) {

		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'edit-wishlist', 'wishlist' ) ) ) {
			$wishlist = new WC_Wishlists_Wishlist( $post->ID );

			$title = _draft_or_post_title();
			if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( current_user_can( 'edit_post', $post->ID ) ) {
					$preview_link = set_url_scheme( $wishlist->get_the_url_view( $post->ID ) );
					/** This filter is documented in wp-admin/includes/meta-boxes.php */
					$preview_link    = apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ), $post );
					$actions['view'] = '<a href="' . esc_url( $preview_link ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
				}
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . $wishlist->get_the_url_view( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
			}
		}

		return $actions;
	}

	public function edit_wishlist_load() {
		add_filter( 'request', array( &$this, 'filter_sort_request' ) );
		add_filter( 'pre_get_posts', array( $this, 'custom_filters_parse_query' ), 99 );
		add_filter( 'posts_where', array( $this, 'custom_search_where' ), 99, 2 );
		add_filter( 'posts_join', array( $this, 'custom_search_join' ), 99, 2 );
		add_filter( 'posts_groupby', array( $this, 'custom_search_group' ), 99, 2 );
	}

	public function enqueue_scripts() {
		global $post;

		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'edit-wishlist', 'wishlist' ) ) ) {
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'woocommerce-wishlists-admin', WC_Wishlists_Plugin::plugin_url() . '/assets/css/woocommerce-wishlists-admin.css' );

			if ( $post ) {

				wp_enqueue_script( 'jquery-blockui' );
				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_script( 'woocommerce-wishlists-admin', WC_Wishlists_Plugin::plugin_url() . '/assets/js/woocommerce-wishlists-admin.js', array( 'jquery' ) );

				$params = array(
					'wishlist_item_nonce' => wp_create_nonce( "wishlist-item" ),
					'remove_item_notice'  => __( 'Are you sure you want to remove the selected items?', 'wc_wishlist' ),
					'i18n_select_items'   => __( 'Please select some items.', 'woocommerce' ),
					'wc_plugin_url'       => WC()->plugin_url(),
					'plugin_url'          => WC_Wishlists_Plugin::plugin_url(),
					'ajax_url'            => admin_url( 'admin-ajax.php' ),
					'post_id'             => $post->ID
				);

				wp_localize_script( 'woocommerce-wishlists-admin', 'woocommerce_wishlist_writepanel_params', $params );
			}
		}
	}

	public function filter_sort_request( $vars ) {
		if ( isset( $vars['post_type'] ) && $vars['post_type'] == 'wishlist' ) {

			if ( isset( $vars['orderby'] ) && $vars['orderby'] == '_wishlist_sharing' ) {

				$vars = array_merge(
					$vars, array( 'meta_key' => '_wishlist_sharing', 'orderby' => 'meta_value' )
				);
			}
		}

		return $vars;
	}

	public function custom_filters() {
		global $typenow, $wp_query, $wpdb;

		if ( $typenow == 'wishlist' ) {
			$wishlist_type    = isset( $_REQUEST['wishlist_status'] ) ? $_REQUEST['wishlist_status'] : '';
			$wishlist_sharing = isset( $_REQUEST['wishlist_sharing'] ) ? $_REQUEST['wishlist_sharing'] : '';
			?>

            <select name="wishlist_status" id="wishlist_type">
                <option value=""><?php _e( 'Show All Types', 'wc_wishlist' ); ?></option>
                <option <?php selected( $wishlist_type, 'active' ); ?>
                        value="active"><?php _e( 'Permanent', 'wc_wishlist' ); ?></option>
                <option <?php selected( $wishlist_type, 'temporary' ); ?>
                        value="temporary"><?php _e( 'Temporary List', 'wc_wishlist' ); ?></option>
            </select>

            <select name="wishlist_sharing" id="wishlist_sharing">
                <option value=""><?php _e( 'Show All Sharing Types', 'wc_wishlist' ); ?></option>
                <option <?php selected( $wishlist_sharing, 'Public' ); ?>
                        value="Public"><?php _e( 'Public', 'wc_wishlist' ); ?></option>
                <option <?php selected( $wishlist_sharing, 'Shared' ); ?>
                        value="Shared"><?php _e( 'Shared', 'wc_wishlist' ); ?></option>
                <option <?php selected( $wishlist_sharing, 'Private' ); ?>
                        value="Private"><?php _e( 'Private', 'wc_wishlist' ); ?></option>
            </select>

			<?php
		}
	}

	public function custom_filters_parse_query( $query ) {
		global $pagenow, $wpdb;

		$q_vars = &$query->query_vars;
		if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == 'wishlist' ) {

			$include_filter = false;
			$sharing_search = false;
			if ( isset( $_REQUEST['wishlist_sharing'] ) && ! empty( $_REQUEST['wishlist_sharing'] ) ) {
				$wishlist_sharing = $_REQUEST['wishlist_sharing'];

				$include1 = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p 
						INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id 
							WHERE post_type = 'wishlist' AND meta_key = '_wishlist_sharing' AND meta_value = %s", $wishlist_sharing ) );

				$include_filter = empty( $include1 ) ? array( - 1 ) : $include1;
				$sharing_search = true;
			}

			if ( isset( $_REQUEST['wishlist_status'] ) && ! empty( $_REQUEST['wishlist_status'] ) ) {
				$wishlist_status = $_REQUEST['wishlist_status'];

				$include2 = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p 
						INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id 
							WHERE post_type = 'wishlist' AND meta_key = '_wishlist_status' AND meta_value = %s", $wishlist_status ) );


				if ( $sharing_search ) {
					$include2       = empty( $include2 ) ? array( - 1 ) : $include2;
					$include_filter = array_intersect( $include_filter, $include2 );
				} else {
					$include_filter = empty( $include2 ) ? array( - 1 ) : $include2;
				}
			}

			if ( $include_filter !== false ) {

				if ( empty( $include_filter ) ) {
					$include_filter = array( - 1 );
				}

				$query->query_vars['post__in'] = array_map( 'intval', $include_filter );
			}
		}

		return $query;
	}

	public function custom_search_where( $where, $query ) {
		global $wpdb;

		if ( $query ) {
			if ( is_search() && isset( $_GET['s'] ) && ! empty( $_GET['s'] ) && $query->get( 'post_type' ) == 'wishlist' ) {
				// put the custom fields into an array
				$customs = array( '_wishlist_first_name', '_wishlist_last_name', '_wishlist_email' );
				$term    = get_search_query();

				$terms   = explode( ' ', $term );
				$filters = array();
				foreach ( $terms as $term ) {
					$filters[] = 'LOWER(wlm.meta_value) LIKE ' . strtolower( $wpdb->prepare( "'%%%s%%'", $wpdb->esc_like( $term ) ) );
				}

				if ( $filters ) {
					$filter = implode( ' OR ', $filters );
					$q      = '1 = 0';
					foreach ( $customs as $custom ) {
						$q .= " OR (";
						$q .= "(wlm.meta_key = '$custom')";
						$q .= " AND ( $filter )";
						$q .= ")";
					}

					$where .= " OR ({$q})";
				}
			}
		}

		return ( $where );
	}

	public function custom_search_join( $join, $query ) {
		global $wpdb;

		if ( $query ) {
			if ( is_search() && isset( $_GET['s'] ) && ! empty( $_GET['s'] ) && $query->get( 'post_type' ) == 'wishlist' ) {
				$join .= " INNER JOIN $wpdb->postmeta wlm ON {$wpdb->posts}.ID = wlm.post_id";
			}
		}

		return $join;
	}

	public function custom_search_group( $groupby, $query ) {
		global $wpdb;

		if ( $query ) {
			if ( is_search() && $query->get( 'post_type' ) == 'wishlist' ) {

				$mygroupby = "{$wpdb->posts}.ID";

				if ( preg_match( "/$mygroupby/", $groupby ) ) {
					// grouping we need is already there
					return $groupby;
				}

				if ( ! strlen( trim( $groupby ) ) ) {
					// groupby was empty, use ours
					return $mygroupby;
				}

				// wasn't empty, append ours
				return $groupby . ", " . $mygroupby;
			}
		}

		return $groupby;
	}

	public function add_columns( $columns ) {

		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'title'    => __( 'Title', 'wc_wishlist' ),
			'status'   => __( 'Type', 'wc_wishlist' ),
			'sharing'  => __( 'Sharing Status', 'wc_wishlist' ),
			'user'     => __( 'User', 'wc_wishlist' ),
			'email'    => __( 'Email on List', 'wc_wishlist' ),
			'name'     => __( 'Name on List', 'wc_wishlist' ),
			'products' => __( 'Products', 'wc_wishlist' ),
			'date'     => __( 'Date', 'wc_wishlist' ),
		);


		return $columns;
	}

	public function sortable_columns( $columns ) {
		$columns['status']  = '_wishlist_status';
		$columns['sharing'] = '_wishlist_sharing';

		return $columns;
	}

	public function render_columns( $column, $post_id ) {
		global $post;

		$data = array(
			'wishlist_title'               => get_the_title( $post_id ),
			'wishlist_description'         => $post->post_content,
			'wishlist_type'                => get_post_meta( $post_id, '_wishlist_type', true ),
			'wishlist_sharing'             => get_post_meta( $post_id, '_wishlist_sharing', true ),
			'wishlist_status'              => get_post_meta( $post_id, '_wishlist_status', true ),
			'wishlist_owner'               => get_post_meta( $post_id, '_wishlist_owner', true ),
			'wishlist_owner_email'         => get_post_meta( $post_id, '_wishlist_email', true ),
			'wishlist_owner_notifications' => get_post_meta( $post_id, '_wishlist_owner_notifications', true ),
			'wishlist_first_name'          => get_post_meta( $post_id, '_wishlist_first_name', true ),
			'wishlist_last_name'           => get_post_meta( $post_id, '_wishlist_last_name', true ),
			'wishlist_items'               => get_post_meta( $post_id, '_wishlist_items', true ),
			'wishlist_subscribers'         => get_post_meta( $post_id, '_wishlist_subscribers', true ),
		);

		switch ( $column ) {
			case 'title' :
				echo get_the_title( $post_id );
				break;
			case 'sharing' :
				echo $data['wishlist_sharing'];
				break;
			case 'status' :
				echo( $data['wishlist_status'] == 'temporary' ? 'Temporary List' : 'Permanent' );
				break;
			case 'email' :
				echo '<a href="mailto:' . $data['wishlist_owner_email'] . '">' . $data['wishlist_owner_email'] . '</a>';
				break;
			case 'name' :
				echo $data['wishlist_first_name'] . ' ' . $data['wishlist_last_name'];
				break;
			case 'products' :
				echo count( $data['wishlist_items'] );
				break;
			case 'user' :
				if ( $data['wishlist_status'] == 'active' ) {
					$user_id   = (int) $data['wishlist_owner'];
					$user_info = get_userdata( $user_id );
					if ( $user_info ) {
						printf( '<a href="%s">%s</a>', get_edit_user_link( $user_id ), $user_info->display_name );
					} else {
						echo ' - ';
					}
				} else {
					echo ' - ';
				}
				break;
			default:
				break;
		}
	}

	public function updated_messages( $messages ) {
		global $post;
		$post_ID = $post->ID;

		$messages['wishlist'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Wishlist updated. <a href="%s">View Wishlist</a>' ), esc_url( WC_Wishlists_Wishlist::get_the_url_view( $post_ID ) ) ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Wishlist updated.' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Wishlist restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Wishlist published. <a href="%s">View Wishlist</a>' ), esc_url( WC_Wishlists_Wishlist::get_the_url_view( $post_ID ) ) ),
			7  => __( 'Wishlist saved.' ),
			8  => sprintf( __( 'Wishlist submitted. <a target="_blank" href="%s">Preview Wishlist</a>' ), esc_url( add_query_arg( 'preview', 'true', WC_Wishlists_Wishlist::get_the_url_view( $post_ID ) ) ) ),
			9  => sprintf( __( 'Wishlist scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Wishlist</a>' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( WC_Wishlists_Wishlist::get_the_url_view( $post_ID ) ) ),
			10 => sprintf( __( 'Wishlist draft updated. <a target="_blank" href="%s">Preview Wishlist</a>' ), esc_url( add_query_arg( 'preview', 'true', WC_Wishlists_Wishlist::get_the_url_view( $post_ID ) ) ) ),
		);

		return $messages;
	}

	public function on_delete_post( $id ) {
		if ( get_post_type( $id ) == 'wishlist' ) {
			$key = WC_Wishlists_User::get_wishlist_key() . '_wishlist_products';

			//Fix undefined $_SESSION notice. 1.9.0
			if ( $_SESSION && isset( $_SESSION[ $key ] ) ) {
				unset( $_SESSION[ $key ] );
			}

			do_action( 'wc_wishlists_deleted', $id );
		}
	}

	public function on_save_post( $post_id, $post ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( $post->post_type != 'wishlist' ) {
			return $post_id;
		}

		if ( isset( $_REQUEST['wishlist-action'] ) && ! WC_Wishlists_Plugin::verify_nonce( 'wishlist-action' ) ) {
			return $post_id;
		}


		$args = $_POST;

		$defaults = array(
			'wishlist_title'               => get_the_title( $post_id ),
			'wishlist_description'         => $post->post_content,
			'wishlist_type'                => get_post_meta( $post_id, '_wishlist_type', true ),
			'wishlist_sharing'             => get_post_meta( $post_id, '_wishlist_sharing', true ),
			'wishlist_status'              => get_post_meta( $post_id, '_wishlist_status', true ),
			'wishlist_owner'               => get_post_meta( $post_id, '_wishlist_owner', true ),
			'wishlist_owner_email'         => get_post_meta( $post_id, '_wishlist_email', true ),
			'wishlist_owner_notifications' => get_post_meta( $post_id, '_wishlist_owner_notifications', true ),
			'wishlist_first_name'          => get_post_meta( $post_id, '_wishlist_first_name', true ),
			'wishlist_last_name'           => get_post_meta( $post_id, '_wishlist_last_name', true ),
			'wishlist_items'               => get_post_meta( $post_id, '_wishlist_items', true ),
			'wishlist_subscribers'         => get_post_meta( $post_id, '_wishlist_subscribers', true ),
		);


		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'wc_wishlists_update_list_args', $args );

		if ( $defaults['wishlist_status'] == 'temporary' && $args['wishlist_owner'] != $defaults['wishlist_owner'] ) {
			//Admin is assigning this list to a user.
			update_post_meta( $post_id, '_wishlist_status', 'permanent' );
			$user = get_user_by( 'id', $args['wishlist_owner'] );
			if ( $user ) {
				if ( empty( $args['wishlist_owner_email'] ) ) {
					$args['wishlist_owner_email'] = $user->user_email;
				}

				if ( empty( $args['wishlist_first_name'] ) ) {
					$args['wishlist_first_name'] = $user->first_name;
				}

				if ( empty( $args['wishlist_last_name'] ) ) {
					$args['wishlist_last_name'] = $user->last_name;
				}
			}
		}

		update_post_meta( $post_id, '_wishlist_sharing', $args['wishlist_sharing'] );
		update_post_meta( $post_id, '_wishlist_type', $args['wishlist_type'] );

		update_post_meta( $post_id, '_wishlist_owner', $args['wishlist_owner'] );
		update_post_meta( $post_id, '_wishlist_email', $args['wishlist_owner_email'] );
		update_post_meta( $post_id, '_wishlist_owner_notifications', $args['wishlist_owner_notifications'] );

		update_post_meta( $post_id, '_wishlist_first_name', $args['wishlist_first_name'] );
		update_post_meta( $post_id, '_wishlist_last_name', $args['wishlist_last_name'] );

		update_post_meta( $post_id, '_wishlist_subscribers', apply_filters( 'wc_wishlists_update_subscribers', $args['wishlist_subscribers'], $post_id ) );
		update_post_meta( $post_id, '_wishlist_items', apply_filters( 'wc_wishlists_update_items', $args['wishlist_items'], $post_id ) );

		do_action( 'wc_wishlists_updated', $post_id, $args );


		if ( isset( $args['wishlist_item_quantity'] ) && count( $args['wishlist_item_quantity'] ) ) {

			foreach ( $args['wishlist_item_quantity'] as $item_id => $quantity ) {
				WC_Wishlists_Wishlist_Item_Collection::update_item_quantity( $post_id, $item_id, $quantity );
			}
		}

	}

	function add_metaboxes() {

		add_meta_box( 'wc_wishlists_items', __( 'Items', 'wc_wishlist' ), array(
			&$this,
			'items_metabox'
		), 'wishlist', 'advanced', 'default' );


		add_meta_box( 'wc_wishlists_viewing', __( 'View Wishlist', 'wc_wishlist' ), array(
			&$this,
			'viewing_metabox'
		), 'wishlist', 'side', 'high' );


		add_meta_box( 'wc_wishlists_nameinfo', __( 'Name and Information', 'wc_wishlist' ), array(
			&$this,
			'nameinfo_metabox'
		), 'wishlist', 'advanced', 'default' );

		add_meta_box( 'wc_wishlists_sharing', __( 'Sharing', 'wc_wishlist' ), array(
			&$this,
			'sharing_metabox'
		), 'wishlist', 'advanced', 'default' );
		add_meta_box( 'wc_wishlists_notifications', __( 'Notifications', 'wc_wishlist' ), array(
			&$this,
			'notifications_metabox'
		), 'wishlist', 'advanced', 'default' );


	}

	public function viewing_metabox( $post ) {
		$wishlist = new WC_Wishlists_Wishlist( $post->ID );
		$sharing  = $wishlist->get_wishlist_sharing();
		?>

        <div class="wl-admin-wrapper">

            <ul>
                <li>
                    <a href="<?php echo $wishlist->get_the_url_view( $post->ID ); ?>"
                       target="_blank"><?php _e( 'Preview', 'wc_wishlist' ); ?></a>
                </li>
                <li>
                    <a href="<?php echo $wishlist->get_the_url_edit( $post->ID ); ?>"
                       target="_blank"><?php _e( 'Manage List', 'wc_wishlist' ); ?></a>
                </li>
            </ul>

        </div>

		<?php
	}

	public function sharing_metabox( $post ) {
		$wishlist = new WC_Wishlists_Wishlist( $post->ID );
		$sharing  = $wishlist->get_wishlist_sharing();
		?>
		<?php echo WC_Wishlists_Plugin::nonce_field( 'wishlist-action' ); ?>

        <div class="wl-admin-wrapper">
            <p class="form-row">
                <strong><?php _e( 'Privacy Settings', 'wc_wishlist' ); ?></strong>
            <table class="wl-rad-table">
                <tr>
                    <td><input type="radio" name="wishlist_sharing" id="rad_pub"
                               value="Public" <?php checked( 'Public', $sharing ); ?>></td>
                    <td><label for="rad_pub"><?php _e( 'Public', 'wc_wishlist' ); ?> <span
                                    class="wl-small">- <?php _e( 'Anyone can search for and see this list. You can also share using a link.', 'wc_wishlist' ); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <td><input type="radio" name="wishlist_sharing" id="rad_shared"
                               value="Shared" <?php checked( 'Shared', $sharing ); ?>></td>
                    <td><label for="rad_shared"><?php _e( 'Shared', 'wc_wishlist' ); ?> <span
                                    class="wl-small">- <?php _e( 'Only people with the link can see this list. It will not appear in public search results.', 'wc_wishlist' ); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <td><input type="radio" name="wishlist_sharing" id="rad_priv"
                               value="Private" <?php checked( 'Private', $sharing ); ?>></td>
                    <td><label for="rad_priv"><?php _e( 'Private', 'wc_wishlist' ); ?> <span
                                    class="wl-small">- <?php _e( 'Only you can see this list.', 'wc_wishlist' ); ?></span></label>
                    </td>
                </tr>
            </table>
            </p>
        </div>
		<?php
	}

	public function nameinfo_metabox( $post ) {
		$wishlist = new WC_Wishlists_Wishlist( $post->ID );

		$wishlist_owner = '';
		if ( get_post_meta( $post->ID, '_wishlist_status', true ) != 'temporary' ) {
			$wishlist_owner = get_post_meta( $wishlist->id, '_wishlist_owner', true );
		}

		?>
        <div class="wl-admin-wrapper">
            <p class="no-marg"><?php _e( 'Enter a name you would like associated with this list.  If your list is public, users can find it by searching for this name.', 'wc_wishlist' ); ?></p>
            <p class="form-row form-row-half">
                <label for="wishlist_first_name"><?php _e( 'First Name', 'wc_wishlist' ); ?></label>
                <input type="text" name="wishlist_first_name" id="wishlist_first_name"
                       value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_first_name', true ) ); ?>"/>
            </p>
            <p class="form-row form-row-half">
                <label for="wishlist_first_name"><?php _e( 'Last Name', 'wc_wishlist' ); ?></label>
                <input type="text" name="wishlist_last_name" id="wishlist_last_name"
                       value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_last_name', true ) ); ?>"/>
            </p>
            <p class="form-row form-row-full">
                <label for="wishlist_owner_email"><?php _e( 'Email Assoicated with the List', 'wc_wishlist' ); ?></label>
                <input type="text" name="wishlist_owner_email" id="wishlist_owner_email"
                       value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_email', true ) ); ?>"/>
            </p>

			<?php if ( get_post_meta( $post->ID, '_wishlist_status', true ) == 'temporary' ): ?>
                <br/><br/>
                <hr/>
                <p>
                    <strong><?php _e( 'Attach to a User', 'wc_wishlist' ); ?></strong><br/>
                    Enter an user ID to assign this list to a specific user.
                </p>
			<?php endif; ?>
            <p class="form-row form-row-full">
                <label for="wishlist_owner"><?php _e( 'User ID Assoicated with the List', 'wc_wishlist' ); ?></label>
                <input type="text" name="wishlist_owner" id="wishlist_owner"
                       value="<?php echo esc_attr( $wishlist_owner ); ?>"/>
            </p>

        </div>
		<?php
	}

	public function notifications_metabox( $post ) {
		$wishlist      = new WC_Wishlists_Wishlist( $post->ID );
		$notifications = get_post_meta( $wishlist->id, '_wishlist_owner_notifications', true );
		if ( empty( $notifications ) ) {
			$notifications = 'yes';
		}
		?>
		<?php echo WC_Wishlists_Plugin::nonce_field( 'wishlist-action' ); ?>

        <div class="wl-admin-wrapper">
            <p class="form-row">
                <strong><?php _e( 'Notification Settings', 'wc_wishlist' ); ?></strong>
            <table class="wl-rad-table">
                <tr>
                    <td><input type="radio" id="rad_notification_yes" name="wishlist_owner_notifications"
                               value="yes" <?php checked( 'yes', $notifications ); ?>></td>
                    <td><label for="rad_notification_yes"><?php _e( 'Yes', 'wc_wishlist' ); ?> <span
                                    class="wl-small">- <?php _e( 'Send an email if a price reduction occurs.', 'wc_wishlist' ); ?></span></label>
                    </td>
                </tr>
                <tr>
                    <td><input type="radio" id="rad_notification_no" name="wishlist_owner_notifications"
                               value="no" <?php checked( 'no', $notifications ); ?>></td>
                    <td><label for="rad_notification_no"><?php _e( 'No', 'wc_wishlist' ); ?> <span
                                    class="wl-small">- <?php _e( 'Do not send an email if a price reduction occurs.', 'wc_wishlist' ); ?></span></label>
                    </td>
                </tr>
            </table>
            </p>
        </div>
		<?php
	}

	public function items_metabox( $post ) {
		if ( ! WC_Wishlist_Compatibility::is_wc_version_gte_2_1() ) {
			include_once WC()->plugin_path() . '/classes/class-wc-cart.php';
		}

		/*
		$cart = new WC_Cart();

		remove_action( 'wp_loaded', array( $cart, 'init' ) ); // Get cart after WP and plugins are loaded.
		remove_action( 'wp', array( $cart, 'maybe_set_cart_cookies' ), 99 ); // Set cookies

		remove_all_actions('shutdown');

		remove_all_actions( 'woocommerce_add_to_cart' );
		remove_all_actions( 'woocommerce_applied_coupon' );
		*/

		$wishlist = new WC_Wishlists_Wishlist( $post->ID );


		$current_owner_key = WC_Wishlists_User::get_wishlist_key();
		$sharing           = $wishlist->get_wishlist_sharing();
		$sharing_key       = $wishlist->get_wishlist_sharing_key();
		$wl_owner          = $wishlist->get_wishlist_owner();

		$wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items( $post->ID );

		$treat_as_registry = false;
		?>
        <div class="wl-admin-wrapper">
            <div id="woocommerce-wishlist-items" class="woocommerce_order_items_wrapper">
                <table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
                    <thead>
                    <tr>
                        <th><input type="checkbox" class="check-column" style="width:auto;"/></th>
                        <th class="item" colspan="2" style=""><?php _e( 'Item', 'woocommerce' ); ?></th>
                        <th class="quantity"><?php _e( 'Qty', 'woocommerce' ); ?></th>
                    </tr>
                    </thead>
                    <tbody id="order_items_list">
					<?php
					foreach ( $wishlist_items as $item_id => $item ) {
						$_product = wc_get_product( $item['data'] );
						if ( $_product->exists() && $item['quantity'] > 0 ) {
							?>
                            <tr class="item <?php if ( ! empty( $class ) ) {
								echo $class;
							} ?>" data-order_item_id="<?php echo $item_id; ?>">
                                <td class="check-column">
                                    <input type="checkbox" name="wlitem[]" value="<?php echo $item_id; ?>"
                                           style="width:auto;"/>
                                </td>
                                <td class="thumb" style="text-align:left;">
                                    <a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_product->get_id() ) . '&action=edit' ) ); ?>"
                                       class="tips" data-tip="<?php
									echo '<strong>' . __( 'Product ID:', 'woocommerce' ) . '</strong> ' . absint( $item['product_id'] );

									if ( $item['variation_id'] ) :
										echo '<br/><strong>' . __( 'Variation ID:', 'woocommerce' ) . '</strong> ' . absint( $item['variation_id'] );
									endif;

									if ( $_product->get_sku() ) :
										echo '<br/><strong>' . __( 'Product SKU:', 'woocommerce' ) . '</strong> ' . esc_html( $_product->get_sku() );
									endif;
									?>"><?php echo $_product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a>
                                </td>
                                <td class="name" style="text-align:left;">

									<?php if ( $_product->get_sku() ) {
										echo esc_html( $_product->get_sku() ) . ' &ndash; ';
									} ?>

                                    <a target="_blank"
                                       href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_product->get_id() ) . '&action=edit' ) ); ?>"><?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product_id', $item['product_id'] ) ) ), $_product->get_title() ); ?></a>
                                    <input type="hidden" class="order_item_id" name="order_item_id[]"
                                           value="<?php echo esc_attr( $item_id ); ?>"/>

									<?php

									if ( isset( $item['variation'] ) ) :
										echo $this->get_item_data( $item );
									endif;

									?>

									<?php do_action( 'woocommerce_wishlist_after_list_item_name', $item, $wishlist ); ?>
                                </td>
                                <!-- Quantity inputs -->
                                <td class="quantity" width="1%">
									<?php $product_quantity_value = apply_filters( 'woocommerce_wishlist_list_item_quantity_value', $item['quantity'], $item, $wishlist ); ?>

                                    <input type="number"
                                           step="<?php echo apply_filters( 'woocommerce_quantity_input_step', '1', $_product ); ?>"
                                           min="0" autocomplete="off"
                                           name="wishlist_item_quantity[<?php echo $item_id; ?>]" placeholder="0"
                                           value="<?php echo esc_attr( $product_quantity_value ); ?>" size="4"
                                           class="quantity"/>
                                </td>

                            </tr>
							<?php
						}
					}
					?>
                    </tbody>
                </table>
            </div>

            <p class="wl_bulk_actions">
                <select>
                    <option value=""><?php _e( 'Actions', 'woocommerce' ); ?></option>
                    <optgroup label="<?php _e( 'Edit', 'woocommerce' ); ?>">
                        <option value="delete"><?php _e( 'Delete Lines', 'woocommerce' ); ?></option>
                    </optgroup>
                </select>

                <button type="button" class="button do_wl_bulk_action wc-reload"
                        title="<?php _e( 'Apply', 'woocommerce' ); ?>">
                    <span><?php _e( 'Apply', 'woocommerce' ); ?></span></button>
            </p>
        </div>
		<?php
	}


	private function get_item_data( $cart_item, $flat = false ) {
		$item_data = array();

		// Variation values are shown only if they are not found in the title as of 2.7.
		// This is because variation titles display the attributes.
		if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
			foreach ( $cart_item['variation'] as $name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				if ( taxonomy_exists( $taxonomy ) ) {
					// If this is a term slug, get the term's nice name.
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );
				} else {
					// If this is a custom option slug, get the options name.
					$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
					$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
				}


				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}

		// Filter item data to allow 3rd parties to add more to the array
		$item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );

		// Format item data ready to display
		foreach ( $item_data as $key => $data ) {
			// Set hidden to true to not display meta on cart.
			if ( ! empty( $data['hidden'] ) ) {
				unset( $item_data[ $key ] );
				continue;
			}
			$item_data[ $key ]['key']     = ! empty( $data['key'] ) ? $data['key'] : $data['name'];
			$item_data[ $key ]['display'] = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
		}

		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {
			ob_start();
			echo '<table class="display_meta"><tbody>';
			foreach ( $item_data as $d ) {
				echo '<tr>';
				echo '<th>' . $d['key'] . '</th>';
				echo '<td>' . $d['display'] . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';

			return ob_get_clean();
		}

		return '';
	}


}
