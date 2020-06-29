<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Coupon_Campaigns {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $post_type;
	public  $tax;

	public function __construct( $file ) {
		$this->dir        = dirname( $file );
		$this->file       = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->post_type  = 'shop_coupon';
		$this->tax        = 'coupon_campaign';

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_filter( 'comments_clauses', array( $this, 'exclude_coupon_comments' ), 10, 1 );

		// Regsiter taxonomy
		add_action('init', array( $this , 'register_taxonomy' ) );

		// Save coupon usage data on order processing/complete (data will only be saved once)
		add_action( 'woocommerce_order_status_processing', array( $this, 'save_usage_data' ), 20, 1 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'save_usage_data' ), 20, 1 );

		// Register shortcode
		add_shortcode( 'coupon_info', array( $this, 'coupon_info' ) );

		if( is_admin() ) {

			// Add menu items
			add_action( 'admin_menu', array( $this, 'admin_menu_items' ) );
			add_action( 'admin_head', array( $this, 'admin_menu_highlight' ) );

			// Add meta boxes
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );

			// Add campaign column
			add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 20, 1 );
			add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'register_custom_columns' ), 20, 2 );

			// Handle coupons table filtering
			add_action( 'restrict_manage_posts', array( $this, 'campaign_filter_option' ) );
			add_filter( 'request', array( $this, 'campaign_filter_action' ) );

			// Display campaigns on order data
			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_order_campaigns' ), 10, 1 );

			// Exclude campaign notes from the admin comment screen.
			add_filter( 'pre_get_comments', array( $this, 'hide_coupon_note_comments' ) );
		}

	}

	/**
	 * Register coupon campaigns taxonomy
	 * @return void
	 */
	public function register_taxonomy() {

        $labels = array(
            'name'              => __( 'Coupon Campaigns' , 'wc_coupon_campaigns' ),
            'singular_name'     => __( 'Campaign', 'wc_coupon_campaigns' ),
            'search_items'      => __( 'Search Campaigns' , 'wc_coupon_campaigns' ),
            'all_items'         => __( 'All Campaigns' , 'wc_coupon_campaigns' ),
            'parent_item'       => __( 'Parent Campaign' , 'wc_coupon_campaigns' ),
            'parent_item_colon' => __( 'Parent Campaign:' , 'wc_coupon_campaigns' ),
            'edit_item'         => __( 'Edit Campaign' , 'wc_coupon_campaigns' ),
            'update_item'       => __( 'Update Campaign' , 'wc_coupon_campaigns' ),
            'add_new_item'      => __( 'Add New Campaign' , 'wc_coupon_campaigns' ),
            'new_item_name'     => __( 'New Term Campaign' , 'wc_coupon_campaigns' ),
            'menu_name'         => __( 'Coupon Campaigns' , 'wc_coupon_campaigns' ),
        );

        $args = array(
            'public'            => true,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'hierarchical'      => true,
            'rewrite'           => true,
            'labels'            => $labels
        );

        register_taxonomy( $this->tax , $this->post_type , $args );
		add_filter( 'manage_edit-' . $this->tax . '_columns', array( $this, 'edit_tax_columns' ) );
		add_filter( 'manage_' . $this->tax . '_custom_column', array( $this, 'edit_tax_column_data' ), 10, 3 );
    }

    /**
     * Edit the taxonomy term column headers
     *
     * @param array $columns
     */
    public function edit_tax_columns( $columns ) {

    	unset( $columns['posts'] );

		$columns['count'] = __( 'Count', 'wc_coupon_campaigns' );
		$columns['reports'] = __( 'Reports', 'wc_coupon_campaigns' );

    	return $columns;
    }

    /**
     * Edit the taxonomy term column data
     *
     * @param string $value
     * @param string $column_name
     * @param int $term_id
     */
    public function edit_tax_column_data( $value, $column_name, $term_id ) {
		switch ( $column_name ) {
			// Count column content
			case 'count':
				// get the term
				$term = get_term_by( 'id', $term_id, $this->tax );

				$url = add_query_arg( array(
					'post_type' => 'shop_coupon',
					$this->tax  => $term->slug ),
					admin_url( 'edit.php' )
				);

				$value = '<a href="' . esc_url( $url ) . '">' . esc_html( $term->count ) . '</a>';
			break;

			// Reports column content
			case 'reports':
				$url = add_query_arg( array(
					'campaign' => intval( $term_id ),
					'page' => 'wc-reports',
					'tab' => 'coupons',
					),
					admin_url( 'admin.php' )
				);

				$value = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'View Reports', 'wc_coupon_campaigns' ) . '</a>';
			break;

			default:
			break;
		}

    	return $value;
    }

    /**
     * Register 'Campaigns' column heading for coupons list table
     * @param  arr $columns existing columns
     * @return arr          Updated columns
     */
    public function register_custom_column_headings( $columns ) {

		$columns[ $this->tax ] = __( 'Campaigns', 'wc_coupon_campaigns' );

		return $columns;
	}

	/**
	 * Register 'Campaigns' column for coupons list table
	 * @param  str $column_name Name of column
	 * @param  int $id          ID of column
	 * @return void
	 */
	public function register_custom_columns( $column_name, $id ) {
		global $post;

		switch ( $column_name ) {

			case $this->tax:
				$campaigns = wp_get_post_terms( $post->ID, $this->tax );

				$data = '';
				$i = 0;
				foreach( $campaigns as $campaign ) {
					if ( $i > 0 ) {
						$data .= ', ';
					} else {
						++$i;
					}
					$data .= '<a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '&' . $this->tax . '=' . $campaign->slug ) . '">' . $campaign->name . '</a>';
				}
				echo $data;
			break;

		}

	}

	/**
	 * Add option to filter coupons by campaign
	 * @return void
	 */
	public function campaign_filter_option() {
		global $typenow;

		if( $typenow == $this->post_type ) {

			$selected = isset( $_GET[ $this->tax ] ) ? $_GET[ $this->tax ] : '';

			$output = '<select name="' . $this->tax . '" id="dropdown_' . $this->tax . '">';

			$campaigns = get_terms( $this->tax );
			$campaign_options = '<option value="">'.__( 'Show all campaigns', 'wc_coupon_campaigns' ).'</option>';
			foreach( $campaigns as $campaign ) {
				$campaign_options .= '<option value="' . esc_attr( $campaign->slug ) . '"' . selected( $campaign->slug, $selected, false ) . '>' . esc_html( $campaign->name ) . '</option>';
			}
			$output .= $campaign_options;

			$output .= '</select>';

			echo $output;
		}
	}

	/**
	 * Filter coupons by campaign
	 * @param  arr $request Current request
	 * @return arr          Modified request
	 */
	public function campaign_filter_action( $request ) {
		global $typenow;

		if( $typenow == $this->post_type ) {
			$selected_campaign = isset( $_GET[ $this->tax ] ) ? $_GET[ $this->tax ] : '';

			if( $selected_campaign ) {

				$query_array = array(
					'taxonomy' => $this->tax,
					'field'    => 'slug',
					'terms'    => $selected_campaign
				);

				// Add new query to existing tax_query if it exists, otherwise add tax_query to request
				if( isset( $request['tax_query'] ) ) {
					array_push( $request['tax_query'], $query_array );
				} else {
					$request['tax_query'] = $query_array;
				}
			}
		}

		return $request;
	}

	/**
	 * Register meta boxes for coupon edit screen
	 * @return void
	 */
	public function register_meta_boxes() {
		global $post;

		if( 'auto-draft' != $post->post_status ) {
			add_meta_box( 'coupon_notes', __( 'Coupon Notes', 'wc_coupon_campaigns' ), array( $this, 'display_usage_notes' ), $this->post_type, 'side', 'default' );
		}
	}

	/**
	 * Add menu items
	 * @return void
	 */
	public function admin_menu_items() {
		add_submenu_page( 'woocommerce', __( 'Coupon Campaigns', 'wc_coupon_campaigns' ), __( 'Coupon Campaigns', 'wc_coupon_campaigns' ), 'manage_woocommerce', 'edit-tags.php?taxonomy=coupon_campaign', '' );
	}

	/**
	 * Highlight the correct menu item when handling campaigns
	 * @return void
	 */
	public function admin_menu_highlight() {
		global $parent_file, $submenu_file, $taxonomy;

		if ( isset( $taxonomy ) ) {
			if ( $taxonomy == $this->tax ) {
				$submenu_file = 'edit-tags.php?taxonomy=' . esc_attr( $this->tax );
				$parent_file  = 'woocommerce';
			}
		}
	}

	/**
	 * Display coupon usage notes on coupon edit screen
	 * @param  obj $post Coupon post object
	 * @return void
	 */
	public function display_usage_notes( $post ) {

		$usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
		$usage_limit = esc_html( get_post_meta( $post->ID, 'usage_limit', true) );

		$data = '<p>' . __( 'Usage:', 'wc_coupon_campaigns' ) . ' ';

		if ( $usage_limit ) {
			$data .= sprintf( __( '%s / %s', 'woocommerce' ), $usage_count, $usage_limit );
		} else {
			$data .= sprintf( __( '%s / &infin;', 'woocommerce' ), $usage_count );
		}

		if( $usage_count > 0 ) {
			$total_discount = absint( get_post_meta( $post->ID, '_total_discount', true ) );
			$total_revenue = absint( get_post_meta( $post->ID, '_total_revenue', true ) );

			$data .= '<br/>' . __( 'Total discount:', 'wc_coupon_campaigns' ) . ' ' . wc_price( $total_discount ) . '<br/>
						' . __( 'Total revenue:', 'wc_coupon_campaigns' ) . ' ' . wc_price( $total_revenue );
		}

		$data .= '</p>';

		echo $data;

		$this->display_coupon_notes();

	}

	/**
	 * Save coupon usage data
	 * @param  int $order_id ID of order
	 * @return void
	 */
	public function save_usage_data( $order_id ) {

		// Check if coupons have already been processed for this order
		$processed = get_post_meta( $order_id, '_coupons_processed', true );
		if ( $processed && 1 == $processed ) {
			return;
		}

		$order = new WC_Order( $order_id );

		$coupons        = $this->get_order_coupons( $order );

		$user_id        = version_compare( WC_VERSION, '3.0', '<' ) ? $order->customer_user : $order->get_customer_id();
		$user           = get_userdata( $user_id );
		$total_discount = $order->get_total_discount();
		$time           = current_time( 'mysql' );
		$order_total    = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_total : $order->get_total();

		foreach( $coupons as $c ) {

			$coupon   = new WC_Coupon( $c['name'] );

			$discount = $c['discount_amount'];

			$this->save_coupon_data( $coupon, $order_id, $order_total, $discount, $user, $time );

			$this->save_order_data( $coupon, $order_id );

		}

		add_post_meta( $order_id, '_coupons_processed', '1' );
	}

	/**
	 * Get coupons from order
	 * @param  obj $order Order object
	 * @return arr        Array of coupons applied to order
	 */
	public function get_order_coupons( $order ) {

		$coupons = $order->get_items( 'coupon' );

		return $coupons;
	}

	/**
	 * Assign order to appropriate campaigns
	 * @param  obj $coupon   Coupon post object
	 * @param  int $order_id Order ID
	 * @return void
	 */
	private function save_order_data( $coupon, $order_id ) {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			$coupon_id = $coupon->get_id();
		} else {
			$coupon_id = $coupon->id;
		}

		$campaigns = wp_get_post_terms( $coupon_id, $this->tax );

		foreach( $campaigns as $campaign ) {
			$campaign_id = (int) $campaign->term_id;
			wp_set_object_terms( $order_id, $campaign_id, $this->tax, true );
		}

	}

	/**
	 * Save coupon usage data to coupon post
	 * @param  obj $coupon      Coupon post object
	 * @param  int $order_id    ID of order
	 * @param  int $order_total Total value of order
	 * @param  int $discount    Total discount provided by coupon
	 * @param  obj $user        Customer's user object
	 * @param  str $time        Time & date of order
	 * @return void
	 */
	private function save_coupon_data( $coupon, $order_id, $order_total, $discount, $user, $time ) {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			$coupon_id = $coupon->get_id();
		} else {
			$coupon_id = $coupon->id;
		}

		// Track orders in which this coupon was used
		$orders = (array)get_post_meta( $coupon_id, '_coupon_orders', true );
		$orders[ $order_id ] = $order_id;
		update_post_meta( $coupon_id, '_coupon_orders', $orders );

		// Track all discount amounts given by this coupon
		$discounts = (array)get_post_meta( $coupon_id, '_coupon_discounts', true );
		$discounts[ $order_id ] = $discount;
		update_post_meta( $coupon_id, '_coupon_discounts', $discounts );

		// Track all revenue amounts generated by this coupon
		$revenue = (array)get_post_meta( $coupon_id, '_coupon_revenue', true );
		$revenue[ $order_id ] = $order_total;
		update_post_meta( $coupon_id, '_coupon_revenue', $revenue );

		// Track the total discount this coupon has given
		$total_discount = absint( get_post_meta( $coupon_id, '_total_discount', true ) );
		$total_discount += $discount;
		update_post_meta( $coupon_id, '_total_discount', $total_discount );

		// Track total revenue generated by this coupon
		$total_revenue = absint( get_post_meta( $coupon_id, '_total_revenue', true ) );
		$total_revenue += $order_total;
		update_post_meta( $coupon_id, '_total_revenue', $total_revenue );

		// Save a human-readable note for this coupon
		$this->save_coupon_note( $coupon, $discount, $order_id, $order_total, $user, $time );
	}

	/**
	 * Add note to post containing usage data
	 * @param  obj $coupon      Coupon post object
	 * @param  int $discount    Total discount provided by coupon
	 * @param  int $order_id    ID of order
	 * @param  int $order_total Total value of order
	 * @param  obj $user        Customer's user object
	 * @param  str $time        Time & date of order
	 * @return void
	 */
	private function save_coupon_note( $coupon, $discount, $order_id, $order_total, $user, $time ) {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			$coupon_id = $coupon->get_id();
		} else {
			$coupon_id = $coupon->id;
		}

		$order_url = admin_url( 'post.php?post=' . intval( $order_id ) . '&action=edit' );

		$note = '<a href="' . esc_url( $order_url ) . '"><b>' . __( 'Order', 'wc_coupon_campaigns' ) . ' #' . $order_id . '</b></a><br/>
				 ' . __( 'Customer:', 'wc_coupon_campaigns' ) . ' <a href="' . admin_url( 'user-edit.php?user_id=' . intval( $user->ID ) ) . '">' . esc_html( $user->data->user_login ) . '</a><br/>
				 ' . __( 'Discount:', 'wc_coupon_campaigns' ) . ' ' . wc_price( $discount ) . '<br/>
				 ' . __( 'Revenue:', 'wc_coupon_campaigns' ) . ' ' . wc_price( $order_total );

		$comment_data = array(
			'comment_post_ID'      => $coupon_id,
			'comment_author'       => $user->data->user_login,
			'comment_author_email' => $user->data->user_email,
			'comment_content'      => $note,
			'comment_type'         => 'coupon_note',
			'comment_parent'       => 0,
			'user_id'              => $user->ID,
			'comment_author_IP'    => $_SERVER['REMOTE_ADDR'],
			'comment_agent'        => $_SERVER['HTTP_USER_AGENT'],
			'comment_date'         => $time,
			'comment_approved'     => 1
		);

		wp_insert_comment( $comment_data );

	}

	/**
	 * Exclude coupon comments from queries and RSS, similarly to how WooCommerce does it for order notes.
	 *
	 * @param  array $clauses A compacted array of comment query clauses.
	 * @return array
	 */
	public function exclude_coupon_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'coupon_note' ";
		return $clauses;
	}

	/**
	 * Display coupone notes on coupon edit screen
	 * @return void
	 */
	private function display_coupon_notes() {
		global $post;

		$args = array(
			'post_id' 	=> $post->ID,
			'approve' 	=> 'approve',
			'type' 		=> 'coupon_note',
			'number'	=> '5'
		);

		$notes = get_comments( $args );

		echo '<ul class="order_notes">';

		if ( $notes ) {
			foreach( $notes as $note ) {
				?>
				<li rel="<?php echo absint( $note->comment_ID ) ; ?>" class="note">
					<div class="note_content">
						<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
					</div>
					<p class="meta">
						<?php printf( __( 'added %s ago', 'woocommerce' ), human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', 1 ) ) ); ?>
					</p>
				</li>
				<?php
			}
		}

		echo '</ul>';
	}

	/**
	 * Display copoun campaigns on order edit screen
	 * @param  obj $order Order post object
	 * @return void
	 */
	public function display_order_campaigns( $order ) {
		global $woocommerce;

		$campaigns = wp_get_post_terms( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), $this->tax );

		if ( $campaigns && count( $campaigns ) > 0 ) {

			$html = '<p class="form-field form-field-wide">
					<b>Coupon Campaigns:</b><br/>';

			$i = 0;
			foreach( $campaigns as $campaign ) {
				if ( $i > 0 ) {
					$data .= ', ';
				} else {
					++$i;
				}
				$html .= '<a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '&' . $this->tax . '=' . $campaign->slug ) . '">' . $campaign->name . '</a>';
			}

			$html .= '</p>';

			echo $html;
		}
	}

	/**
	 * Coupon info form for use on front-end
	 * @return str HTML of form
	 */
	public function coupon_info() {
		global $woocommerce;

		$coupon_code = '';
		$html = '';

		$html .= '<div id="coupon_info_form">
						<h2>Look up coupon code</h2>
						<form class="woo-sc-box" name="coupon_info" action="" method="get">
							<input type="text" name="coupon" value="' . esc_attr( $coupon_code ) . '" placeholder="' . __( 'Coupon code', 'wc_coupon_campaigns' ) . '" />
							<input type="submit" value="Submit" />
						</form>
					</div>';

		if( isset( $_GET['coupon'] ) ) {

			$coupon_code = esc_attr( $_GET['coupon'] );

			$coupon = new WC_Coupon( $coupon_code );

			// Get coupon type
			$types = $woocommerce->get_coupon_discount_types();
			$type = $types[ $coupon->discount_type ];

			// Get amount display
			switch( $coupon->discount_type ) {
				case 'fixed_cart': $amount = wc_price( $coupon->amount ); break;
				case 'percent': $amount = $coupon->amount . '%'; break;
				case 'fixed_product': $amount = wc_price( $coupon->amount ); break;
				case 'percent_product': $amount = $coupon->amount . '%'; break;
				case 'sign_up_fee': $amount = wc_price( $coupon->amount ); break;
				case 'recurring_fee': $amount = wc_price( $coupon->amount ); break;
				default: $amount = $coupon->amount; break;
			}

			// Included products
			$products = '';
			if( is_array( $coupon->product_ids ) ) {
				$c = 0;
				foreach( $coupon->product_ids as $product_id ) {
					if( $c > 0 ) { $products .= ', '; }
					else { ++$c; }
					$_product = wc_get_product( $product_id );
					$title = $_product->get_title();
					$products .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
				}
			}

			if( ! $products ) {
				$products = 'All products';
			}

			// Excluded products
			$ex_products = '';
			if( $coupon->coupon_custom_fields['exclude_product_ids'][0] && strlen( $coupon->coupon_custom_fields['exclude_product_ids'][0] ) ) {
				$c = 0;
				$ex_product_ids = explode( ',', $coupon->coupon_custom_fields['exclude_product_ids'][0] );
				foreach( $ex_product_ids as $product_id ) {
					if( $c > 0 ) { $ex_products .= ', '; }
					else { ++$c; }
					$_product = wc_get_product( $product_id );
					$title = $_product->get_title();
					$ex_products .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
				}
			}

			// Included categories
			$cats = '';
			if( is_array( $coupon->product_categories ) ) {
				$c = 0;
				foreach( $coupon->product_categories as $cat_id ) {
					if( $c > 0 ) { $cats .= ', '; }
					else { ++$c; }
					$cat = get_term( $cat_id, 'product_cat' );
					$cat_link = get_term_link( $cat, 'product_cat' );
					$cats .= '<a href="' . esc_url( $cat_link ) . '" title="' . esc_attr( $cat->name ) . '">' . $cat->name . '</a>';
				}
			}

			// Excluded categories
			$ex_cats = '';
			if( is_array( $coupon->exclude_product_categories ) ) {
				$c = 0;
				foreach( $coupon->exclude_product_categories as $cat_id ) {
					if( $c > 0 ) { $ex_cats .= ', '; }
					else { ++$c; }
					$cat = get_term( $cat_id, 'product_cat' );
					$cat_link = get_term_link( $cat, 'product_cat' );
					$ex_cats .= '<a href="' . esc_url( $cat_link ) . '" title="' . esc_attr( $cat->name ) . '">' . $cat->name . '</a>';
				}
			}

			$html .= '<div id="coupon_info">
						<h2>"' . $coupon_code . '" details</h2>
						<ul>
							<li><strong>Coupon amount:</strong> ' . $amount . '</li>';

			if( $products )
				$html .= '<li><strong>Applies to:</strong> ' . $products . '</li>';

			if( $ex_products )
				$html .= '<li><strong>Excludes:</strong> ' . $ex_products . '</li>';

			if( $cats )
				$html .= '<li><strong>Applies to categories:</strong> ' . $cats . '</li>';

			if( $ex_cats )
				$html .= '<li><strong>Excludes categories:</strong> ' . $ex_cats . '</li>';

			$html .= '</ul>
					 </div>';

		}

		return $html;

	}

	/**
	 * Load plugin localisation files
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wc_coupon_campaigns' , false , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin text domain
	 * @return void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wc_coupon_campaigns';

	    $locale = apply_filters( 'plugin_locale' , get_locale() , $domain );

	    load_textdomain( $domain , WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain , FALSE , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * @since 1.0.3 introduced.
	 *
	 * @param WP_Comment_Query $comments_query
	 * @return WP_Comment_Query
	 */
	public function hide_coupon_note_comments( $comments_query ){
		$screen = get_current_screen();
		if ( ! is_admin() || ! is_object( $screen ) || 'edit-comments' !== $screen->id ) {
			return $comments_query;
		}

		$current_excluded_types = (array) $comments_query->query_vars['type__not_in'];
		$comments_query->query_vars['type__not_in'] = array_merge( array( 'coupon_note' ), $current_excluded_types );

		return $comments_query;
	}
}
