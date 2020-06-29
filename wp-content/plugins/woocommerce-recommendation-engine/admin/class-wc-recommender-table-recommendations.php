<?php

if ( ! class_exists( 'WP_List_Table' ) ) :
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
endif;

class WC_Recommender_Table_Recommendations extends WP_List_Table {

	public $is_trash = false;

	public function __construct() {
		parent::__construct( array(
			'singular' => 'Product',
			'plural'   => 'Products',
			'ajax'     => false,
			'screen'   => 'wc_recommender_admin'
		) );
	}

	public function get_bulk_actions() {
		$actions = array();

		$actions['build-recommendations'] = __( 'Rebuild Recommendations', 'wc_recommender' );

		return $actions;
	}

	public function get_table_classes() {
		return array( 'widefat', 'fixed', 'posts' );
	}

	public function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">';
		if ( $which == 'top' ) {

		}
		echo '</div>';
	}

	public function get_columns() {

		$c = array(
			'cb'      => '<input type="checkbox" />',
			'title'   => __( 'Title' ),
			'views'   => __( 'Views', 'wc_recommender' ),
			'orders'  => __( 'Orders', 'wc_recommender' ),
			'related' => __( 'Recommendations', 'wc_recommender' ),
			'actions' => __( 'Rebuild', 'wc_recommender' )
		);

		return $c;
	}

	public function get_sortable_columns() {
		$c = array(
			'title'  => array(
				'post_title',
				true
			),
			'views'  => array(
				'views',
				false
			),
			'orders' => array(
				'orders',
				false
			)
		);

		return $c;
	}

	public function display_rows() {
		// Query the post counts for this page
		//Get the records registered in the prepare_items method
		$records = $this->items;

		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();

		$row_count = 0;
		//Loop for each record
		if ( ! empty( $records ) ) {

			foreach ( $records as $rec ) {

				$row_count ++;
				$row_class = $row_count % 2 ? 'alternate' : '';

				//Open the line
				echo '<tr id="record_' . $rec->ID . '" class="' . $row_class . '">';
				foreach ( $columns as $column_name => $column_display_name ) {

					//Style attributes for each col
					$class = "class='$column_name column-$column_name'";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;
					$editlink   = '';
					//Display the cell
					switch ( $column_name ) {
						case 'cb' :
							echo '<th class="check-column">';
							echo '<input type="checkbox" value="' . esc_attr( $rec->ID ) . '" name="product_ids[]"/>';
							echo '</th>';
							break;
						case "title":
							$actions = array();
							echo '<td ' . $attributes . '><strong><a href="' . admin_url( 'admin.php' ) . '?page=wc_recommender_admin&product-id=' . (int) $rec->ID . '&wc_recommender_admin_view=view-recommendations">' . stripslashes( $rec->post_title ) . '</a></strong>';
							echo '<div class="row-actions">';
							$actions[] = '<span class="edit"><a href="' . get_edit_post_link( $rec->ID ) . '">Edit</a></span>';
							echo implode( ' | ', $actions );
							echo '</div>';
							echo '</td>';

							break;
						case "views":
							echo '<td>' . ( empty( $rec->views ) ? 0 : $rec->views ) . '</td>';
							break;
						case 'orders' :
							echo '<td>' . ( empty( $rec->orders ) ? 0 : $rec->orders ) . '</td>';
							break;
						case 'related':
							echo '<td class="recommendations-cell">';

							echo '<ul class="recommendations-list" style="list-style:none;margin: 0;">';

							echo '<li class="viewed_similar"><span class="label">' . __( 'Viewed:', 'wc_recommender' ) . '</span> <span class="value"> ' . ( empty( $rec->related['viewed_similar'] ) ? 0 : $rec->related['viewed_similar'] ) . '</span></li>';
							echo '<li class="ordered_similar"><span class="label">' . __( 'Also Purchased:', 'wc_recommender' ) . '</span> <span class="value"> ' . ( empty( $rec->related['ordered_similar'] ) ? 0 : $rec->related['ordered_similar'] ) . '</span></li>';
							echo '<li class="purchased_together"><span class="label">' . __( 'Purchased Together:', 'wc_recommender' ) . '</span> <span class="value"> ' . ( empty( $rec->related['purchased_together'] ) ? 0 : $rec->related['purchased_together'] ) . '</span></li>';

							echo '</ul>';

							echo '</td>';
							break;
						case 'actions' :
							echo '<td class="rebuild-cell"><button rel="' . $rec->ID . '" type="button" class="button do_wc_recommender_build_recommendation wc-reload" title="' . __( 'Rebuild', 'wc_recommender' ), '"><span>' . __( 'Rebuild', 'wc_recommender' ) . '</span></button></td>';
							break;
					}
				}

				//Close the line
				echo '</tr>';
			}
		}
	}

	public function prepare_items() {
		global $wpdb, $_wp_column_headers, $woocommerce_recommender;
		$screen = get_current_screen();

		$viewed_sql  = "SELECT product_id, COUNT(DISTINCT session_id) AS activity_count FROM $woocommerce_recommender->db_tbl_session_activity WHERE activity_type = 'viewed' GROUP BY product_id ORDER BY product_id";
		$ordered_sql = "SELECT product_id, COUNT(DISTINCT session_id) AS activity_count FROM $woocommerce_recommender->db_tbl_session_activity WHERE activity_type = 'completed' GROUP BY product_id ORDER BY product_id";


		$items_table_name = $wpdb->posts;
		$items_sql        = "SELECT ID, post_title, cviewed.activity_count as views, cordered.activity_count as orders FROM $items_table_name p ";

		$items_sql .= "LEFT JOIN ( SELECT product_id, COUNT(DISTINCT session_ID) as activity_count FROM $woocommerce_recommender->db_tbl_session_activity WHERE activity_type = 'viewed' GROUP BY product_id) cviewed ON p.ID = cviewed.product_id ";
		$items_sql .= "LEFT JOIN ( SELECT product_id, COUNT(DISTINCT session_ID) as activity_count FROM $woocommerce_recommender->db_tbl_session_activity WHERE activity_type = 'completed' GROUP BY product_id) cordered ON p.ID = cordered.product_id ";
		$where_sql = " WHERE post_type = 'product' AND post_status IN('publish')";

		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$where_sql = $wpdb->prepare( $where_sql . ' AND LOWER( post_title ) LIKE %s', '%' . strtolower( $wpdb->esc_like( $_REQUEST['s'] ) ) . '%' );
		}

		/* -- Ordering parameters -- */

		$items_sql .= $where_sql;


		//Parameters that are going to be used to order the result
		$orderby_sql = " ORDER BY ";
		$orderby     = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'post_title';
		$order       = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : 'ASC';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$orderby_sql .= $orderby . ' ' . $order;
		} else {
			$orderby_sql .= 'post_title' . ' ' . 'ASC';
		}

		$items_sql .= $orderby_sql;


		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = $wpdb->query( $items_sql ); //return the total number of affected rows
		//How many to display per page?
		$perpage = 40;
		//Which page is this?
		$paged = ! empty( $_REQUEST["paged"] ) ? esc_sql( $_REQUEST["paged"] ) : '';
		//Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		//How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		//adjust the query to take pagination into account
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$items_sql .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page"    => $perpage,
		) );
		//The pagination links are automatically built according to those parameters


		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* -- Fetch the items -- */
		$temp = $wpdb->get_results( $items_sql );

		$temp_items = array();
		foreach ( $temp as $data ) {
			$product_id      = $data->ID;
			$recommendations = array();

			$recommendations['viewed_similar']     = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(related_product_id) FROM $woocommerce_recommender->db_tbl_recommendations WHERE product_id = %d AND rkey = %s", $product_id, 'wc_recommender_viewed_' . $product_id ) );
			$recommendations['ordered_similar']    = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(related_product_id) FROM $woocommerce_recommender->db_tbl_recommendations WHERE product_id = %d AND rkey = %s", $product_id, 'wc_recommender_completed_' . $product_id ) );
			$recommendations['purchased_together'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(related_product_id) FROM $woocommerce_recommender->db_tbl_recommendations WHERE product_id = %d AND rkey = %s", $product_id, 'wc_recommender_fpt_completed_' . $product_id ) );

			$data->related = $recommendations;
			$temp_items[]  = $data;
		}

		$this->items = $temp_items;
	}

	public function no_items() {
		_e( 'Empty folder' );
	}

}
