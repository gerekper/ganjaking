<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

use Automattic\WooCommerce\BulkStockManagement\RequestUtil as Request;

/**
 * WC_Stock_Management_List_Table class.
 *
 * @extends WP_List_Table
 */
class WC_Stock_Management_List_Table extends WP_List_Table {

	/** @var integer Index of product being output */
	private $index = 0;

	/** @var int Product ID being output */
	private $last_product_id;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'product',     //singular name of the listed records
			'plural'   => 'products',    //plural name of the listed records
			'ajax'     => false,         //does this table support ajax?
		) );
	}

	/**
	 * Output column data
	 * @param  object $product
	 * @param  string $column_name
	 * @return string
	 */
	public function column_default( $product, $column_name ) {
		switch ( $column_name ) {
			case 'thumb' :
				return $product->get_image();
			break;
			case 'title' :
				$title     = $product->get_title();
				$bwc = version_compare( WC_VERSION, '3.0', '<' );

				if ( $product->is_type( 'variation' ) ) {
					$attributes = $product->get_variation_attributes();
					$extra_data = implode( ', ', $attributes ) . ' &ndash; ' . wc_price( $product->get_price() );

					if ( $this->last_product_id !== $product->get_id() ) {
						$title = $title . ' &mdash; ' . $extra_data;
					} else {
						$title = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash; ' . $extra_data;
					}

					$parent_id = $bwc ? $product->id : $product->get_parent_id();
				} else {
					$parent_id = $bwc ? $product->id : $product->get_id();
				}

				$edit_link = admin_url( 'post.php?post=' . $parent_id . '&action=edit' );

				return '<a href="' . esc_url( $edit_link ) . '">' . esc_html( strip_tags( $title ) ) . '</a>';
			break;
			case 'id' :
				if ( ! $product->is_type( 'variation' ) ) {
					$this->last_product_id = $product->get_id();
				}
				return $product->get_id();
			break;
			case 'sku':
				return $product->get_sku() ? $product->get_sku() : '<span class="na">&ndash;</span>';
			break;
			case 'manage_stock' :
				if ( ! $product->is_type( 'variation' ) && $product->managing_stock() ) {
					return '<mark class="yes">' . esc_html__( 'Parent', 'woocommerce-bulk-stock-management' ) . '</mark>';
				} else {
					return ( $product->managing_stock() ) ? '<mark class="yes">' . esc_html__( 'Yes', 'woocommerce-bulk-stock-management' ) . '</mark>' : '<span class="na">&ndash;</span>';
				}
			break;
			case 'stock' :
				$this->index++;
				?>
				<input type="text" class="input-text wc_bulk_stock_quantity_value" tabindex="<?php echo esc_attr( $this->index ); ?>" data-name="stock_quantity[<?php echo esc_attr( $product->get_id() ); ?>]" placeholder="<?php
				if ( $product->managing_stock() ) {
					echo esc_html( wc_stock_amount( $product->get_stock_quantity() ) );
				} else {
					esc_html_e( 'N/A', 'woocommerce-bulk-stock-management' );
				}
				?>" />

				<input type="hidden" class="input-text" data-name="current_stock_quantity[<?php echo esc_attr( $product->get_id() ); ?>]" value="<?php if ( ! $product->is_type( 'variation' ) || $product->managing_stock() ) { echo esc_attr( $product->get_stock_quantity() ); } ?>" />
				<?php

			break;
			case 'stock_status' :
				return ( $product->is_in_stock() ) ? '<mark class="instock">' . esc_html__( 'In stock', 'woocommerce-bulk-stock-management' ) . '</mark>' : '<mark class="outofstock">' . esc_html__( 'Out of stock', 'woocommerce-bulk-stock-management' ) . '</mark>';
			break;
			case 'backorders' :
				if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
					echo '<mark class="yes">' . esc_html__( 'Notify', 'woocommerce-bulk-stock-management' ) . '</mark>';
				} elseif ( $product->backorders_allowed() ) {
					echo '<mark class="yes">' . esc_html__( 'Yes', 'woocommerce-bulk-stock-management' ) . '</mark>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
			break;
		} // End switch().
	}

	/**
	 * Checkbox column
	 */
	public function column_cb( $item ) {
		$id = $item->get_id();

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			esc_html( $this->_args['singular'] ),
			esc_html( $id )
		);
	}

	/**
	 * Get columns
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'thumb'        => esc_html__( 'Image', 'woocommerce-bulk-stock-management' ),
			'id'           => esc_html__( 'ID', 'woocommerce-bulk-stock-management' ),
			'title'        => esc_html__( 'Name', 'woocommerce-bulk-stock-management' ),
			'sku'          => esc_html__( 'SKU', 'woocommerce-bulk-stock-management' ),
			'manage_stock' => esc_html__( 'Manage Stock', 'woocommerce-bulk-stock-management' ),
			'stock_status' => esc_html__( 'Stock Status', 'woocommerce-bulk-stock-management' ),
			'backorders'   => esc_html__( 'Backorders', 'woocommerce-bulk-stock-management' ),
			'stock'        => esc_html__( 'Quantity', 'woocommerce-bulk-stock-management' ),
		);

		if ( ! wc_product_sku_enabled() ) {
			unset( $columns['sku'] );
		}

		return $columns;
	}

	/**
	 * If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
			'id'    => array( 'ID', false ),
			'sku'   => array( 'sku', false ),
			'stock' => array( 'stock', false ),
		);
		return $sortable_columns;
	}

	 /**
	 * Get bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'save'                    => esc_html__( 'Save stock quantities', 'woocommerce-bulk-stock-management' ),
			'manage_stock'            => esc_html__( 'Selected: Turn on stock management', 'woocommerce-bulk-stock-management' ),
			'do_not_manage_stock'     => esc_html__( 'Selected: Turn off stock management', 'woocommerce-bulk-stock-management' ),
			'in_stock'                => esc_html__( 'Selected: Mark "In stock"', 'woocommerce-bulk-stock-management' ),
			'out_of_stock'            => esc_html__( 'Selected: Mark "Out of stock"', 'woocommerce-bulk-stock-management' ),
			'allow_backorders'        => esc_html__( 'Selected: Allow backorders', 'woocommerce-bulk-stock-management' ),
			'allow_backorders_notify' => esc_html__( 'Selected: Allow backorders, but notify customer', 'woocommerce-bulk-stock-management' ),
			'do_not_allow_backorders' => esc_html__( 'Selected: Do not allow backorders', 'woocommerce-bulk-stock-management' ),
		);
		return $actions;
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backwards-compatibility.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo "<label for='bulk-action-selector-" . esc_attr( $which ) . "' class='screen-reader-text'>" . esc_html__( 'Select bulk action' ) . '</label>';
		echo "<select name='action" . esc_attr( $two ) . "' id='bulk-action-selector-" . esc_attr( $which ) . "'>\n";
		echo "<option value='-1' selected='selected'>" . esc_html__( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			echo "\t<option value='" . esc_attr( $name ) . "'>" . esc_html( $title ) . "</option>\n";
		}

		echo "</select>\n";

		submit_button( esc_html__( 'Apply' ), 'primary', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	public function display_tablenav( $which ) {
		include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

		$product_type = Request::get_request_variable( 'filter_product_type' );
		$manage_stock = Request::get_request_variable( 'filter_manage_stock' );
		$stock_status = Request::get_request_variable( 'filter_stock_status' );
		$product_cat  = Request::get_request_variable( 'filter_product_cat' );

		if ( 'top' == $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			?>

			<ul class="subsubsub">
				<li class="all"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=woocommerce-bulk-stock-management' ) ) ?>" class="<?php if ( empty( $product_type ) ) { echo 'current'; } ?>"><?php esc_html_e( 'All', 'woocommerce-bulk-stock-management' ); ?></a> |</li>
				<li class="product"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=woocommerce-bulk-stock-management&filter_product_type=product' ) ) ?>" class="<?php if ( 'product' == $product_type ) { echo 'current'; } ?>"><?php esc_html_e( 'Products', 'woocommerce-bulk-stock-management' ); ?></a> |</li>
				<li class="variation"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=woocommerce-bulk-stock-management&filter_product_type=product_variation' ) ) ?>" class="<?php if ( 'product_variation' == $product_type ) { echo 'current'; } ?>"><?php esc_html_e( 'Variations', 'woocommerce-bulk-stock-management' ); ?></a></li>
			</ul>

			<?php $this->search_box( esc_html__( 'Search', 'woocommerce-bulk-stock-management' ), 'search-products' );
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( 'top' == $which ) : ?>
				<div class="alignleft actions">
					<?php $this->bulk_actions( $which ); ?>
				</div>
				<div class="alignleft actions">
					<input type="hidden" name="filter_product_type" value="<?php if ( ! empty( $product_type ) ) { echo esc_attr( $product_type ); } ?>" />
					<select name="filter_manage_stock">
						<option value=""><?php esc_html_e( 'All Products', 'woocommerce-bulk-stock-management' ); ?></option>
						<option value="yes" <?php if ( 'yes' == $manage_stock ) { esc_attr( selected( 1 ) ); } ?>><?php esc_html_e( 'Managing stock', 'woocommerce-bulk-stock-management' ); ?></option>
						<option value="no" <?php if ( 'no' == $manage_stock ) { esc_attr( selected( 1 ) ); } ?>><?php esc_html_e( 'Not managing stock', 'woocommerce-bulk-stock-management' ); ?></option>
					</select>
					<select name="filter_stock_status">
						<option value=""><?php esc_html_e( 'Any stock status', 'woocommerce-bulk-stock-management' ); ?></option>
						<option value="instock" <?php if ( 'instock' == $stock_status ) { esc_attr( selected( 1 ) ); } ?>><?php esc_html_e( 'In stock', 'woocommerce-bulk-stock-management' ); ?></option>
						<option value="outofstock" <?php if ( 'outofstock' == $stock_status ) { esc_attr( selected( 1 ) ); } ?>><?php esc_html_e( 'Out of stock', 'woocommerce-bulk-stock-management' ); ?></option>
					</select>
					<?php
						global $wp_query;

						$r               = array();
						$r['pad_counts'] = 0;
						$r['hierarchal'] = 1;
						$r['hide_empty'] = 1;
						$r['show_count'] = 0;
						$r['selected']   = $product_cat;

						$terms = get_terms( 'product_cat', $r );

					if ( $terms && 'product' === $product_type ) {
							?>
							<select name='filter_product_cat' id='dropdown_product_cat'>
								<option value=""><?php esc_html_e( 'Any category', 'woocommerce-bulk-stock-management' ); ?></option>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wc_walk_category_dropdown_tree( $terms, 0, $r );

						echo '<option value="0" ' . esc_attr( selected( $product_cat, '0', false ) ) . '>' . esc_html__( 'Uncategorized', 'woocommerce-bulk-stock-management' ) . '</option>';
						?>
							</select>
							<?php
					}
					?>
					<input type="hidden" name="paged" value="<?php echo esc_attr( $this->get_pagenum() ); ?>" />
					<input type="submit" name="filter" value="<?php esc_html_e( 'Filter', 'woocommerce-bulk-stock-management' ); ?>" class="button" />
				</div>
			<?php else : ?>
				<div class="alignleft actions">
					<?php $this->bulk_actions( $which ); ?>
				</div>
				<?php $this->extra_tablenav( $which ); ?>
			<?php endif; ?>
			<?php $this->pagination( 'bottom' ); ?>
			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Get the page number
	 * @return int
	 */
	public function get_pagenum() {
		return absint( Request::get_request_variable( 'paged', '1' ) );
	}

	/**
	 * Defines the hidden columns
	 *
	 * @access public
	 * @since 2.0.2
	 * @version 2.0.2
	 * @return array $columns
	 */
	public function get_hidden_columns() {
		// get user hidden columns
		$hidden = get_hidden_columns( $this->screen );

		$new_hidden = array();

		foreach ( $hidden as $k => $v ) {
			if ( ! empty( $v ) ) {
				$new_hidden[] = $v;
			}
		}

		return $new_hidden;
	}

	/**
	 * Get items to display
	 */
	public function prepare_items() {
		global $wpdb;

		$current_page = $this->get_pagenum();
		$post_type    = Request::get_request_variable( 'filter_product_type' );
		$orderby      = Request::get_request_variable( 'orderby', 'ID' );
		$order        = Request::get_request_variable( 'order', 'ASC' );
		$manage_stock = Request::get_request_variable( 'filter_manage_stock' );
		$stock_status = Request::get_request_variable( 'filter_stock_status' );
		$stock_status = 'instock' !== $stock_status && 'outofstock' !== $stock_status ? '' : $stock_status;
		$product_cat  = Request::get_request_variable( 'filter_product_cat' );
		$per_page     = $this->get_items_per_page( 'wc_bulk_stock_products_per_page', apply_filters( 'wc_bulk_stock_default_items_per_page', 50 ) );

		/**
		 * Init column headers
		 */
		$this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

		/**
		 * Prepare ordering args
		 */
		switch ( $orderby ) {
			case 'sku' :
				$meta_key 	= '_sku';
				$orderby 	= 'meta_value';
			break;
			case 'stock' :
				$meta_key = '_stock';
				$orderby 	= 'meta_value_num';
			break;
			default :
				$meta_key = '';
			break;
		}

		$tax_query = array();

		if ( $product_cat ) {
			$tax_query[] = array(
				'taxonomy'	=> 'product_cat',
				'field'		=> 'slug',
				'terms'	 	=> array( $product_cat ),
			);
		} elseif ( '0' === $product_cat ) {
			$tax_query[] = array(
				'taxonomy'	=> 'product_cat',
				'field'		=> 'id',
				'terms' 	=> get_terms( 'product_cat', array( 'fields' => 'ids' ) ),
				'operator' 	=> 'NOT IN',
			);
		}

		$meta_query = array();

		if ( ! empty( $manage_stock ) ) {
			$meta_query[] = array(
				'key'	=> '_manage_stock',
				'value'	=> ( 'yes' == $manage_stock ) ? 'yes' : 'no',
			);
		}

		if ( $stock_status ) {
			$meta_query[] = array(
				'key'	=> '_stock_status',
				'value'	=> $stock_status,
			);
		}

		if ( $post_type ) {
			$post_types = 'product' === $post_type ? array( 'product' ) : array( 'product_variation' );

		} else {
			$post_types = array( 'product', 'product_variation' );
		}

		$products = new WP_Query( array(
			'post_type'      => $post_types,
			'posts_per_page' => $per_page,
			'offset'         => ( $current_page - 1 ) * $per_page,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => $meta_query,
			'tax_query'      => 'product' === $post_type ? $tax_query : array(),
			'meta_key'       => $meta_key,
			's'              => Request::get_request_variable( 's' ),
			'orderby'        => array( $orderby => $order ),
		) );

		/*
		 * We have to do another query for the meta values since there is no easy way
		 * in WordPress to do a query like: post_title LIKE '%str%' OR (_sku.meta_value LIKE '%str%' ...)
		 */
		$s = Request::get_request_variable( 's' );
		if ( ! empty( $s ) ) {
			$sku_meta_query = array( array(
				'key'     => '_sku',
				'value'   => $s,
				'compare' => 'LIKE',
			) );

			$sku_products = new WP_Query( array(
				'post_type'      => $post_types,
				'posts_per_page' => $per_page,
				'offset'         => ( $current_page - 1 ) * $per_page,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'meta_query'     => array_merge( $meta_query, $sku_meta_query ),
				'tax_query'      => 'product' === $post_type ? $tax_query : array(),
				'meta_key'       => $meta_key,
				'orderby'        => array( $orderby => $order ),
			) );

			$products->posts = array_merge( $products->posts, $sku_products->posts );
		}

		$this->items = array();

		if ( $products->posts ) {
			foreach ( $products->posts as $id ) {
				$product = wc_get_product( $id );

				if ( ! $product || isset( $this->items[ $id ] ) ) {
					continue;
				}

				$product_id = $product->get_id();

				if ( $product->is_type( 'variation' ) ) {
					$product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $product->parent->id : $product->get_parent_id();
				}

				// if product or variation's parent was deleted, skip
				if ( 'publish' !== get_post_status( $product_id ) ) {
					continue;
				}

				if ( ! empty( $product_cat ) ) {
					// get the terms of either the product or variation's parent
					$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );

					// if product or variation's parent does not have term, skip
					if ( empty( $terms ) || ! in_array( $product_cat, $terms ) ) {
						continue;
					}
				}

				$this->items[ $id ] = $product;

				$s = Request::get_request_variable( 's' );
				if ( ! $post_type && $product->is_type( 'variable' ) ) {
					$variations = get_posts( array(
						'post_type'      => 'product_variation',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
						'fields'         => 'ids',
						'meta_query'     => $meta_query,
						'meta_key'       => $meta_key,
						's'              => $s,
						'post_parent'    => $id,
					) );

					if ( ! empty( $s ) ) {
						$sku_meta_query = array( array(
							'key'     => '_sku',
							'value'   => $s,
							'compare' => 'LIKE',
						) );

						$sku_variations = new WP_Query( array(
							'post_type'      => 'product_variation',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
							'fields'         => 'ids',
							'meta_query'     => array_merge( $meta_query, $sku_meta_query ),
							'meta_key'       => $meta_key,
							'post_parent'    => $id,
						) );

						$variations = array_merge( $variations, $sku_variations->posts );
					}

					foreach ( $variations as $variation_id ) {
						$variation = wc_get_product( $variation_id );
						if ( $variation ) {
							$this->items[ $variation_id ] = $variation;
						}
					}
				}
			} // End foreach().
		} // End if().

		// Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $products->found_posts,
			'per_page'    => $per_page,
			'total_pages' => ceil( $products->found_posts / $per_page ),
		) );
	}
}
