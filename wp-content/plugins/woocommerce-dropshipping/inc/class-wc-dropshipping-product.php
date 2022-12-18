<?php
class WC_Dropshipping_Product {

	public function __construct() {

		// admin for product edit
				add_action( 'init', array( $this, 'check_deleted_supplier' ) );
		add_action( 'add_meta_boxes', array( $this, 'dropship_supplier_meta_box' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_dropshipper_metaboxes_in_orders' ) );

		add_action( 'save_post_product', array( $this, 'save_supplier_name' ), 100, 3 );

		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'supplier_name_order_page' ), 10, 3 );

		add_filter( 'manage_edit-shop_order_columns', array( $this, 'wc_new_supplier_column' ) );

		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'supplier_value' ) );

		// add_filter( 'bulk_actions-edit-product', array($this,'de_bulk_actions_edit_product') );

		add_filter( 'handle_bulk_actions-edit-product', array( $this, 'assign_bulk_supplier' ), 10, 3 );

		// Related to COST OF GOODS

		/*
		  add_action( 'save_post', array($this,'save_services_checkboxes' ));

			add_action( 'add_meta_boxes', array($this,'add_custom_box' ));

		*/

	}

	public function add_dropshipper_metaboxes_in_orders() {

		add_meta_box( 'wpt_dropshipper_list', 'Shipping details', array( $this, 'print_dropshipper_list_metabox_in_orders' ), 'shop_order', 'side', 'default' );

	}

	/* ADD METABOX WITH DROPSHIPPER STATUSES IN ADMIN ORDERS */

	public function print_dropshipper_list_metabox_in_orders() {

		global $post;

		$order = wc_get_order( $post->ID );

		$items = $order->get_items();

		$arrayuser = array();

		$order_data = $order->get_data();

		$order_status = $order_data['status'];

		$suppliers_attached = array();

		foreach ( $items as $item_id => $item ) {

			$product_name = $item->get_name();

			$product_id = $item->get_product_id();

			$quantity = $item['qty'];

			$supplier_id = get_post_meta( $item_id, 'supplierid', true );

			$arg = array(

				'meta_key'    => 'supplier_id',

				'meta_value'    => $supplier_id,

			);

			$user_query = new WP_User_Query( $arg );

			$authors = $user_query->get_results();

			foreach ( $authors as $author ) {

				$arrayuser[] = $author->ID;

			}

			array_push( $suppliers_attached, $supplier_id );

		}

		$uniqe_userid = array_unique( $arrayuser );

		if ( 'on-hold' == $order_status ) {

			echo 'On hold orders are not yet processed to the suppliers';

		} else {

			foreach ( $uniqe_userid as $key => $value ) {

				$dropshipper_shipping_info = get_post_meta( $post->ID, 'dropshipper_shipping_info_' . $value, true );

				$supplier_id = get_user_meta( $value, 'supplier_id', true );

				$term = get_term_by( 'id', $supplier_id, 'dropship_supplier' );

				if ( in_array( $supplier_id, $suppliers_attached ) ) {

					if ( ! empty( $term->name ) ) {

						if ( empty( $dropshipper_shipping_info ) ) {

							echo 'Pending shipping info - <b>' . $term->name . '</b></br></br>';

							echo '<hr>';

						} else {

							echo '<h2><b>' . $term->name . '</b></h2>';

							echo '<strong>' . __( 'Date', 'woocommerce-dropshippers' ) . '</strong>: <span class="dropshipper_date">' . ( empty( $dropshipper_shipping_info['date'] ) ? '-' : $dropshipper_shipping_info['date'] ) . '</span><br/>' . "\n";

							echo '<strong>' . __( 'Tracking Number(s)', 'woocommerce-dropshippers' ) . '</strong>: <span class="dropshipper_tracking_number">' . ( empty( $dropshipper_shipping_info['tracking_number'] ) ? '-' : $dropshipper_shipping_info['tracking_number'] ) . '</span><br/>' . "\n";

							echo '<strong>' . __( 'Shipping Company', 'woocommerce-dropshippers' ) . '</strong>: <span class="dropshipper_shipping_company">' . ( empty( $dropshipper_shipping_info['shipping_company'] ) ? '-' : $dropshipper_shipping_info['shipping_company'] ) . '</span><br/>' . "\n";

							echo '<strong>' . __( 'Notes', 'woocommerce-dropshippers' ) . '</strong>: <span class="dropshipper_notes">' . ( empty( $dropshipper_shipping_info['notes'] ) ? '-' : $dropshipper_shipping_info['notes'] ) . '</span><br/>' . "\n";

							echo "<hr>\n";

						}
					}
				}
			}
		}

	}

	public function de_bulk_actions_edit_product( $bulk_actions ) {

		$terms = get_terms(
			array(

				'taxonomy' => 'dropship_supplier',

				'hide_empty' => false,

			)
		);

		foreach ( $terms as $term ) {

			$bulk_actions[ 'opmc-dropship-suppliers-' . $term->name ] = $term->name;

		}

		return $bulk_actions;

	}

	public function wc_new_supplier_column( $columns ) {

		$columns['supplier'] = 'Dropshipping Supplier';

		return $columns;

	}

	/*Order page listing column */

	function supplier_value( $column ) {

		global $post;

		if ( 'supplier' === $column ) {

			$order = wc_get_order( $post->ID );

			$items = $order->get_items();

			foreach ( $items as $item_id => $item ) {

				$product_name = $item->get_name();

				$product_id = $item->get_product_id();

				$quantity = $item['qty'];

				$s_name = get_post_meta( $product_id, 'supplier', true );

				$suppliername = wc_get_order_item_meta( $item_id, 'supplier', true );

				if ( $suppliername != '' ) {

					/*echo 'Supplier: '.$s_name.'<br><p><a href="'.get_permalink($product_id).'">'.$product_name.'</a> x '.$quantity.'</p>';*/

					echo $suppliername . ' <br>';

				}
			}
		}

	}

	function assign_bulk_supplier( $redirect_to, $action_name, $post_ids, $append = false ) {

		if ( strpos( $action_name, 'opmc-dropship-suppliers-' ) !== false ) {

			$action_name = str_replace( 'opmc-dropship-suppliers-', '', $action_name );

			foreach ( $post_ids as $post_id ) {

				wp_set_object_terms( $post_id, $action_name, 'dropship_supplier', $append );

				$post = get_post( $post_id );

				// if( !isset( $_POST['tax_input']['dropship_supplier'] ) ) return;

				$term = get_term_by( 'slug', $action_name, 'dropship_supplier' );

				$name = $term->name;

				update_post_meta( $post_id, 'supplier', $name );

				update_post_meta( $post_id, 'supplierid', $term->term_id );

			}

			$redirect_to = add_query_arg( 'other_bulk_posts_precessed', count( $post_ids ), $redirect_to );

			return $redirect_to;

		} else {

			return $redirect_to;

		}

	}

	/* Order Detail page */

	public function supplier_name_order_page( $item_id, $item, $_product ) {

		$suppliername = wc_get_order_item_meta( $item_id, 'supplier', true );

		/*
		if($item['product_id']){

			   echo '<p>Supplier : <b>'.$suppliername.'</b></p>';

		}*/

	}

	public function dropship_supplier_metabox( $post ) {

		$taxonomy = 'dropship_supplier';

		$tax = get_taxonomy( $taxonomy );

		$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

		$name = 'tax_input[' . $taxonomy . ']';

		$postterms = get_the_terms( $post->ID, $taxonomy );

		$current = ( $postterms ? array_pop( $postterms ) : false );

		$current = ( $current ? $current->term_id : 0 );

		echo '<div id="taxonomy-' . $taxonomy . '" class="categorydiv">

			<!-- Display tabs-->

			<ul id="' . $taxonomy . '-tabs" class="category-tabs">

				<li class="tabs"><a href="#' . $taxonomy . '-all" tabindex="3">Select a Dropshipping Supplier</a></li>

			</ul>

			<!-- Display taxonomy terms -->

			<div id="' . $taxonomy . '-all" class="tabs-panel">

			<select id="" name="tax_input[dropship_supplier]" class="form-no-clear">';

		if ( $current == 0 ) {

			echo '<option selected value="" />No Supplier</option>';

		}

		foreach ( $terms as $term ) {

			$selected = '';

			if ( $current == $term->term_id ) {

				$selected = 'selected="selected"';

			}

				$id = $taxonomy . '-' . $term->term_id;

				echo '<option ' . $selected . ' value="' . $term->slug . '" />' . $term->name . '</option>';

		}

		if ( $current > 0 ) {

				echo '<option id="wcds-remove-supplier" value="" />Remove Supplier</option>';

		}

		echo '</select>

			   </ul>

			</div>

		</div>';

	}

	public function save_supplier_name( $post_id ) {

		global $post;

		if ( ! empty( $post->post_type ) && $post->post_type == 'product' ) {

			if ( isset( $_POST['tax_input']['dropship_supplier'] ) ) {

				$supplier_name = $_POST['tax_input']['dropship_supplier'];

				if ( isset( $supplier_name ) && '' !== $supplier_name ) {

					$term = get_term_by( 'slug', $supplier_name, 'dropship_supplier' );

					$name = $term->name;

					update_post_meta( $post_id, 'supplier', $name );

					update_post_meta( $post_id, 'supplierid', $term->term_id );

				} else {

					delete_post_meta( $post_id, 'supplier' );

					delete_post_meta( $post_id, 'supplierid' );

				}
			}
		}

	}

	public function dropship_supplier_meta_box() {

		 add_meta_box( 'dropship_supplier', 'Dropshipping Supplier', array( $this, 'dropship_supplier_metabox' ), 'product', 'side', 'core' );

	}

	public function check_deleted_supplier() {
		global $wpdb;
		$supplier = array();
				$suppliers = array();
				$meta_datas = array();
		$result = $wpdb->get_results( "SELECT a.ID,a.post_type, b.post_id  FROM {$wpdb->posts} a, {$wpdb->postmeta} b WHERE a.ID=b.post_id AND a.post_type='product' GROUP BY a.ID" );
		if ( ! empty( $result ) ) {
			foreach ( $result as $products ) {
				if ( ! empty( get_post_meta( $products->ID, 'supplierid' ) ) ) {
					$supplier[] = get_post_meta( $products->ID, 'supplierid' );
				}
			}
			$get_meta_data = $wpdb->get_results( "SELECT a.term_id, b.term_id, b.taxonomy FROM {$wpdb->terms} a, {$wpdb->term_taxonomy} b WHERE a.term_id=b.term_id AND b.taxonomy='dropship_supplier'" );
			foreach ( $get_meta_data as $meta_data ) {
				if ( ! empty( $meta_data ) ) {
					$meta_datas[] = $meta_data->term_id;
				}
			}
			foreach ( $supplier as $data ) {
				$suppliers[] = $data[0];
			}

			$unmatched_result = array_diff( $suppliers, $meta_datas );

			foreach ( $unmatched_result as $rs ) {
				  $results = $wpdb->get_results( "SELECT post_id FROM {$wpdb->postmeta}  WHERE meta_key='supplierid' AND meta_value='" . $rs . "'" );
				  delete_post_meta( $results[0]->post_id, 'supplierid' );
				  delete_post_meta( $results[0]->post_id, 'supplier' );
			}
		}
	}

}
