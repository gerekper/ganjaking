<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Discount List Table
 *
 * @class   YWDPD_Discount_List_Table
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
class YWDPD_Discount_List_Table extends WP_List_Table {

	/**
	 * @var string
	 */
	private $post_type;
	private $type;
	private $valid_status_to_trash;

	/**
	 * YWDPD_Discount_List_Table constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array(
			                     'singular' => __( 'discount', 'ywdpd' ),
			                     'plural'   => __( 'discounts', 'ywdpd' ),
			                     'ajax'     => false
		                     ) );

		$this->type = $args['type'];
		//   parent::__construct( array() );
		$this->post_type = 'ywdpd_discount';

		$this->process_bulk_action();


	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'post_title'    => __( 'Name', 'ywdpd' ),
			'discount_mode' => __( 'Discount Mode', 'ywdpd' ),
			'status'        => __( 'Status', 'ywdpd' )
		);

		return apply_filters( 'ywsbs_subscription_table_list_columns', $columns );
	}

	/**
	 * Returns column to be sortable in table
	 *
	 * @return array Array of sortable columns
	 * @since 1.0.0
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title'    => array( 'ywdpd_p.post_title', false )
		);

		return $sortable_columns;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 1.0.0
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$orderby = isset( $_REQUEST['orderby'] ) ? esc_sql( $_REQUEST['orderby'] ) : 'ywdpd_pm3.meta_value+0';
		$order = isset( $_REQUEST['order'] ) ? esc_sql( $_REQUEST['order'] ) : 'ASC';
		$s = isset( $_REQUEST['s'] ) ? strtolower(esc_sql( $_REQUEST['s'] )) : false;

		$query = $wpdb->prepare( "SELECT ywdpd_p.* FROM $wpdb->posts as ywdpd_p 
        LEFT JOIN ".$wpdb->prefix."postmeta as ywdpd_pm ON ( ywdpd_p.ID = ywdpd_pm.post_id ) 
        LEFT JOIN ".$wpdb->prefix."postmeta as ywdpd_pm2 ON  ywdpd_p.ID = ywdpd_pm2.post_id 
        LEFT JOIN ".$wpdb->prefix."postmeta as ywdpd_pm3 ON  ywdpd_p.ID = ywdpd_pm3.post_id 
        WHERE ywdpd_p.post_type = %s
        AND ( ywdpd_pm3.meta_key = '_priority' )
        AND ( ywdpd_pm2.meta_key = '_discount_type' AND ywdpd_pm2.meta_value = %s )
        AND (ywdpd_p.post_status = 'publish' OR ywdpd_p.post_status = 'future' OR ywdpd_p.post_status = 'draft' OR ywdpd_p.post_status = 'pending' OR ywdpd_p.post_status = 'private')
        AND LOWER(ywdpd_p.post_title) LIKE %s
        GROUP BY ywdpd_p.ID ORDER BY {$orderby} {$order}",  $this->post_type, $this->type, $s ? '%'.$s.'%' : '%'
		);

		$totalitems = $wpdb->query($query);
		$perpage = 50;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//How many pages do we have in total?
		$totalpages = ceil($totalitems/$perpage);
		//adjust the query to take pagination into account
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page"    => $perpage,
		) );
		//The pagination links are automatically built according to those parameters

		$_wp_column_headers[$screen->id]=$columns;
		$this->items = $wpdb->get_results($query);


	}


	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed|string|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_title':
				$return =  $item->$column_name;
				break;
            case 'status':
                $is_active = get_post_meta( $item->ID, '_active', true );
	            $class = ywdpd_is_true( $is_active )? 'active' : 'deactive';
	            $return = '<span class="ywdpd-discount-status ' . $class . '">' . $class . '</span>';
	            break;
            case 'discount_mode':
                $modes = ywdpd_discount_pricing_mode();
                $mode_option = get_post_meta( $item->ID, '_discount_mode', true );
	            $return = isset( $modes[ $mode_option ] ) ?  $modes[ $mode_option ] : '';
	            break;
			default:
				$return = apply_filters( 'ywsbs_column_default', '', $item, $column_name ); //Show the whole array for troubleshooting purposes
		}

		return $return;
	}



	/**
	 * Handles the checkbox column output.
	 *
	 * @since 1.0.0
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID );
	}

	function column_post_title($item) {
		admin_url( 'post.php?post=' . $item->ID . 'action=edit' );
		$actions = array(
			'edit'   => '<a href="' . admin_url( 'post.php?post=' . $item->ID . '&action=edit' ) . '">' . __( 'Edit', 'ywdpd' ) . '</a>',
			'duplicate'   => '<a href="' . wp_nonce_url( admin_url( 'post.php?post_type=ywdpd_discount&action=duplicate_discount&post=' . $item->ID ), 'ywdpd-duplicate-rule_' . $item->ID ) . '">' . __( 'Duplicate', 'ywdpd' ) . '</a>',
			'delete' => '<a href="' . YITH_WC_Dynamic_Pricing_Admin()->get_delete_post_link( '', $item->ID, $this->type ) . '">' . __( 'Delete', 'ywdpd' ) . '</a>',
		);



		return sprintf( '%1$s %2$s', $item->post_title, $this->row_actions( $actions ) );
	}

	public function extra_tablenav( $which ) {

    }


	/**
	 * Get bulk action
	 *
	 * @since  1.0.0
	 * @return array|false|string
	 */
	function get_bulk_actions() {

		return array(
			'activate'   => __( 'Activate', 'ywdpd' ),
			'deactivate' => __( 'Deactivate', 'ywdpd' ),
			'delete'     => __( 'Delete Permanently', 'ywdpd' )
		);

	}

	public function process_bulk_action() {

		$actions = $this->current_action();

		if ( ! empty( $actions ) && isset( $_REQUEST[ $this->_args['singular'] ] ) ) {

			$discounts = (array) $_REQUEST[ $this->_args['singular'] ];

			foreach ( $discounts as $discount_id ) {

				$post = get_post( $discount_id );

				if ( ! ( $post && $post->post_type == $this->post_type ) ) {
					continue;
				}

				$post_type_object = get_post_type_object( $post->post_type );

				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {

					switch ( $actions ) {
						case 'delete':
							wp_delete_post( $discount_id, true );
							break;
						case 'activate':
							update_post_meta( $discount_id, '_active', 1 );
							break;
						case 'deactivate':
							update_post_meta( $discount_id, '_active', false );
							break;
						default:

					}
				}
			}
		}
	}



}
