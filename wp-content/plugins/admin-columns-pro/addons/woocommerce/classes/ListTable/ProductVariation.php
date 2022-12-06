<?php

namespace ACA\WC\ListTable;

use AC;
use AC\Form\Element\Select;
use ACA\WC\Asset\Script\TableVariation;
use WC_Admin_List_Table;
use WC_Product_Variation;
use WP_Post;

if ( ! class_exists( '\WC_Admin_List_Table', false ) && defined( 'WC_ABSPATH' ) ) {
	include_once( WC_ABSPATH . 'includes/admin/list-tables/abstract-class-wc-admin-list-table.php' );
}

class ProductVariation extends WC_Admin_List_Table {

	/**
	 * Post type.
	 * @var string
	 */
	protected $list_table_type = 'product_variation';

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	/**
	 * Constructor.
	 */
	public function __construct( AC\Asset\Location\Absolute $location ) {
		parent::__construct();

		add_filter( 'disable_months_dropdown', '__return_true' );
		add_filter( 'query_vars', [ $this, 'add_custom_query_var' ] );
		add_filter( 'views_edit-' . $this->list_table_type, [ $this, 'get_views' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'woocommerce_scripts' ], 11 );
		add_action( 'ac/table_scripts', [ $this, 'admin_scripts' ] );

		$this->location = $location;
	}

	public function define_bulk_actions( $actions ) {
		return [];
	}

	public function admin_scripts() {
		$script = new TableVariation( 'aca-wc-table-' . $this->list_table_type, $this->location );
		$script->enqueue();
	}

	/**
	 * @param array $views
	 *
	 * @return array
	 */
	public function get_views( $views ) {
		$num_posts = wp_count_posts( $this->list_table_type, 'readable' );

		$statuses = [
			'publish' => __( 'Enabled' ),
			'private' => __( 'Disabled' ),
		];

		foreach ( $statuses as $status => $label ) {
			if ( $num_posts->$status > 0 ) {
				$views[ $status ] = sprintf( '<a href="%s" class="%s">%s</a>(%s)', add_query_arg( [ 'post_status' => $status ] ), ( $status === get_query_var( 'post_status' ) ? 'current' : '' ), $label, $num_posts->$status );
			}
		}

		return $views;
	}

	public function woocommerce_scripts() {
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );

		wp_enqueue_style( 'jquery-ui-style' );
		wp_enqueue_style( 'woocommerce_admin_styles' );
	}

	/**
	 * Get row actions to show in the list table.
	 *
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	protected function get_row_actions( $actions, $post ) {
		unset( $actions['inline hide-if-no-js'] );

		if ( isset( $actions['edit'] ) ) {
			$actions['edit'] = ac_helper()->html->link( get_edit_post_link( get_post_field( 'post_parent', $post->ID ) ) . '#variation_' . $post->ID, __( 'Edit' ) );
		}

		return $actions;
	}

	/**
	 * Define primary column.
	 * @return string
	 */
	protected function get_primary_column() {
		return 'variation_product';
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		$columns['variation_product'] = 'variation_product';

		return $columns;
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function define_columns( $columns ) {
		return [
			'cb'                   => $columns['cb'],
			'variation_product'    => __( 'Product', 'woocommerce' ),
			'variation_image'      => '<span class="dashicons dashicons-format-image"></span>',
			'variation_id'         => '#',
			'variation_attributes' => __( 'Variation', 'woocommerce' ),
			'variation_sku'        => __( 'SKU', 'woocommerce' ),
			'variation_stock'      => __( 'Stock', 'woocommerce' ),
			'variation_price'      => __( 'Price', 'woocommerce' ),
		];
	}

	/**
	 * Query vars for custom searches.
	 *
	 * @param mixed $public_query_vars Array of query vars.
	 *
	 * @return array
	 */
	public function add_custom_query_var( $public_query_vars ) {
		$public_query_vars[] = 'post_parent';

		return $public_query_vars;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it. the_product global is there for bw compat.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_product;

		if ( empty( $this->object ) || $this->object->get_id() !== $post_id ) {
			$this->object = $the_product = new WC_Product_Variation( $post_id );
		}
	}

	/**
	 * @param array $query_vars
	 *
	 * @return array
	 */
	protected function query_filters( $query_vars ) {

		// Correct default sorting
		if ( empty( $query_vars['orderby'] ) || 'date' === $query_vars['orderby'] ) {
			$query_vars['orderby'] = 'ProductParent';
		}

		return $query_vars;
	}

	/**
	 * @return array
	 */
	private function get_variable_product_options() {
		$options = [];

		$variations = get_posts( [
			'post_type'      => $this->list_table_type,
			'fields'         => 'id=>parent',
			'posts_per_page' => -1,
			'post_status'    => get_query_var( 'post_status' ),
		] );

		$variations = array_unique( $variations );

		foreach ( $variations as $parent_id ) {
			$options[ $parent_id ] = get_the_title( $parent_id );
		}

		natcasesort( $options );

		return $options;
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 */
	protected function render_filters() {
		$options = $this->get_variable_product_options();

		if ( ! $options ) {
			return;
		}

		$select = new Select( 'post_parent', [ '' => '' ] + $options );

		$value = get_query_var( 'post_parent' );

		$select->set_value( $value )
		       ->set_attribute( 'class', 'product_search' )
		       ->set_attribute( 'data-allow_clear', 'true' )
		       ->set_attribute( 'data-placeholder', __( 'Search Variable Product', 'codepress-admin-columns' ) );

		if ( $value ) {
			$select->add_class( 'active' );
		}

		?>
		<div class="acp-select2-filter">
			<?php echo $select->render(); ?>
		</div>
		<?php
	}

	protected function render_blank_state() {
		?>
		<div class="woocommerce-BlankState">
			<h2 class="woocommerce-BlankState-message"><?php echo esc_html__( 'When you create a product variation, it will appear here.', 'codepress-admin-columns' ); ?></h2>
		</div>
		<?php
	}

}