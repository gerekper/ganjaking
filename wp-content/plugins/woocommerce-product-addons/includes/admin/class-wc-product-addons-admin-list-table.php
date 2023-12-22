<?php
/**
 * WC_PAO_List_Table class
 *
 * @package  Woo Product Add-ons
 * @since    6.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Adds a custom global add-ons list table.
 *
 * @class    WC_PAO_List_Table
 * @version  6.5.1
 */
class WC_PAO_List_Table extends WP_List_Table {

	/**
	 * Page home URL.
	 *
	 * @const PAGE_URL
	 */
	const PAGE_URL = 'edit.php?post_type=product&page=addons';

	/**
	 * Total view records.
	 *
	 * @var int
	 */
	public $total_items = 0;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $status, $page;

		$this->total_items = count( WC_Product_Addons_Groups::get_all_global_groups() );

		parent::__construct( array(
			'singular' => 'addon',
			'plural'   => 'addons'
		) );
	}

	/**
	 * Handles the title column output.
	 *
	 * @param array $item
	 */
	public function column_name( $item ) {

		$actions = array(
			'edit'  => sprintf( '<a href="' . esc_url( add_query_arg( 'edit', $item['id'], admin_url( 'edit.php?post_type=product&page=addons' ) ) ) . '">%s</a>', __( 'Edit', 'woocommerce-product-addons' ) ),
			'delete' => sprintf( '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'delete', $item['id'], admin_url( 'edit.php?post_type=product&page=addons' ) ), 'delete_addon' ) ) . '">%s</a>', __( 'Delete', 'woocommerce-product-addons' ) ),
		);

		$title = $item[ 'name' ];

		printf(
			'<a class="row-title" href="%s" aria-label="%s">%s</a>%s',
			esc_url( add_query_arg( 'edit', $item['id'], admin_url( 'edit.php?post_type=product&page=addons' ) ) ),
			/* translators: %s: Global add-on group name */
			sprintf( esc_attr__( '%s (Edit)', 'woocommerce-product-addons' ), esc_attr( $title ) ),
			esc_html( $title ),
			wp_kses_post( $this->row_actions( $actions ) )
		);
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @param array $item
	 */
	public function column_cb( $item ) {
		?><label class="screen-reader-text" for="cb-select-<?php echo (int) $item[ 'id' ]; ?>">
		<?php
			$title = $item[ 'name' ];

			/* translators: %s: Add-on title */
			printf( esc_html__( 'Select %s', 'woocommerce-product-addons' ), esc_html( $title ) );
		?>
		</label>
		<input id="cb-select-<?php echo (int) $item[ 'id' ]; ?>" type="checkbox" name="addon[]" value="<?php echo (int) $item[ 'id' ]; ?>" />
		<?php
	}

	/**
	 * Handles the Priority column output.
	 *
	 * @param array $item
	 */
	public function column_priority( $item ) {
		echo esc_html( (int) $item[ 'priority' ] );
	}

	/**
	 * Handles the Product Categories column output.
	 *
	 * @param array $item
	 */
	public function column_product_categories( $item ) {
		$all_products           = '1' === get_post_meta( $item['id'], '_all_products', true ) ? true : false;
		$restrict_to_categories = $item['restrict_to_categories'];

		if ( $all_products ) {
			esc_html_e( 'All Products', 'woocommerce-product-addons' );
		} elseif ( 0 === count( $restrict_to_categories ) ) {
			esc_html_e( 'No Products Assigned', 'woocommerce-product-addons' );
		} else {
			$objects    = array_keys( $restrict_to_categories );
			$term_names = array_values( $restrict_to_categories );
			$term_names = apply_filters( 'woocommerce_product_addons_global_display_term_names', $term_names, $objects );
			echo wp_kses_post( implode( ', ', $term_names ) );
		}
	}

	/**
	 * Handles the Number of fields column output.
	 *
	 * @param array $item
	 */
	public function column_fields( $item ) {
		echo count( $item['fields'] );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 */
	public function get_columns() {

		$columns                         = array();
		$columns[ 'cb' ]                 = '<input type="checkbox" />';
		$columns[ 'name' ]               = _x( 'Name', 'column_name', 'woocommerce-product-addons' );
		$columns[ 'priority' ]           = _x( 'Display Order', 'column_name', 'woocommerce-product-addons' );
		$columns[ 'product_categories' ] = _x( 'Product Categories', 'column_name', 'woocommerce-product-addons' );
		$columns[ 'fields' ]             = _x( 'Number of fields', 'column_name', 'woocommerce-product-addons' );

		return $columns;
	}

	/**
	 * Return sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'priority' => array( 'priority', true ),
			'fields'   => array( 'fields', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {

		$actions = array(
			'delete'  => __( 'Delete', 'woocommerce-product-addons' )
		);

		return $actions;
	}

	/**
	 * Process bulk actions.
	 *
	 * @return void
	 */
	private function process_bulk_action() {

		if ( $this->current_action() ) {

			check_admin_referer( 'bulk-addons' );

			$addons = isset( $_GET[ 'addon' ] ) && is_array( $_GET[ 'addon' ] ) ? array_map( 'absint', $_GET[ 'addon' ] ) : array();

			if ( empty( $addons ) ) {
				return;
			}

			if ( 'delete' === $this->current_action() ) {

				foreach ( $addons as $id ) {
					WC_Product_Addons_Groups::delete_group( $id );
				}

				WC_PAO_Admin_Notices::add_notice( __( 'Add-ons deleted.', 'woocommerce-product-addons' ), 'success', true );
			}

			wp_safe_redirect( admin_url( self::PAGE_URL ) );
			exit();
		}
	}

	/**
	 * Query the DB and attach items.
	 *
	 * @return void
	 */
	public function prepare_items() {

		/**
		 * `woocommerce_pao_admin_edit_add_ons_per_page` filter.
		 *
		 * Control how many global add-on groups are displayed per page in admin list table.
		 *
		 * @since  6.5.0
		 *
		 * @param  int
		 * @return int
		 */
		$per_page = (int) apply_filters( 'woocommerce_pao_admin_edit_add_ons_per_page', 10 );

		// Table columns.
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Process actions.
		$this->process_bulk_action();

		// Setup params.
		$paged   = isset( $_REQUEST[ 'paged' ] ) ? max( 0, intval( $_REQUEST[ 'paged' ] ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST[ 'orderby' ] ) && in_array( $_REQUEST[ 'orderby' ], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'orderby' ] ) ) : 'priority';
		$order   = ( isset( $_REQUEST[ 'order' ] ) && in_array( $_REQUEST[ 'order' ], array( 'asc', 'desc' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'order' ] ) ) : 'asc';

		// Fetch the items.
		$this->items    = WC_Product_Addons_Groups::get_all_global_groups();

		// Count total items.
		$total_items = count( $this->items );

		// Paginate items.
		$offset      = $paged * $per_page;
		$this->items = array_slice($this->items, $offset, $per_page );

		// Order items.
		$orderby_column = array_column( $this->items, $orderby );

		if ( 'desc' === $order ) {
			array_multisort($orderby_column, SORT_DESC, $this->items );
		} else {
			array_multisort($orderby_column, SORT_ASC, $this->items );
		}

		// Configure pagination.
		$this->set_pagination_args( array(
			'total_items' => $total_items, // total items defined above
			'per_page'    => $per_page, // per page constant defined at top of method
			'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
		) );
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @return void
	 */
	public function no_items() {
		?>
		<p class="main">
			<?php esc_html_e( 'No add-ons found', 'woocommerce-product-addons' ); ?>
		</p>
		<?php
	}
}
