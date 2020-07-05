<?php
if(! function_exists('generate_aliexpress_key')) {

	function generate_aliexpress_key($domain) {
		$passphrase = '107029c9969d644eca7321f9c4df2e6b';
		$key = md5($domain.$passphrase);
		return $key;
		
	}
} // Generate aliexpress api key and send to the admin mailbox.

if ( ! function_exists( 'get_dropship_option' ) ) {
	function get_dropship_option() {
		$dOptions = get_option('opmc_dropshipping_options');
		if( $dOptions !== false && is_array($dOptions) && !empty($dOptions) ) {
			return $dOptions;
		} else {
			return array();
		}
	}
}

if ( ! function_exists( 'update_dropship_option' ) ) {
	function update_dropship_option($dOptions) {
		if(is_array($dOptions)) {
			update_option('opmc_dropshipping_options', $dOptions);
		}		
	}
}

if ( ! function_exists( 'wc_dropshipping_get_dropship_supplier' ) ) {
	function wc_dropshipping_get_dropship_supplier ( $id = '' ) {
		$term = get_term( intval( $id ),'dropship_supplier' );
		$supplier = get_term_meta( intval( $id ), 'meta', true );

		if ( isset( $term->term_id ) ) {
			$supplier['id'] = $term->term_id;
			$supplier['slug'] = $term->slug;
			$supplier['name'] = $term->name;
			$supplier['description'] = $term->description;
		}
		return $supplier;
	}
}

if ( ! function_exists( 'wc_dropshipping_get_dropship_supplier_by_product_id' ) ) {
	function wc_dropshipping_get_dropship_supplier_by_product_id ( $product_id ) {
		$supplier = array();
		$productdata = get_post_meta( $product_id, '_virtual', true );
		if($productdata != 'yes') {
			$terms = get_the_terms( intval( $product_id ), 'dropship_supplier' );
		}
		if ($terms && ! is_wp_error($terms) && 0 < count( $terms ) ) {
			$supplier = wc_dropshipping_get_dropship_supplier( intval( $terms[0]->term_id ) ); // load the term. there can only be one supplier notified per product
		}
		return $supplier;
	}
}

if (! function_exists( 'wc_dropshipping_get_base_path' ) ) {
	function wc_dropshipping_get_base_path () {
		return plugin_dir_path( __FILE__ );
	}
}



add_action('wp_ajax_woocommerce_dropshippers_mark_as_shipped', 'woocommerce_dropshippers_mark_as_shipped_callback');
add_action('wp_ajax_nopriv_woocommerce_dropshippers_mark_as_shipped', 'woocommerce_dropshippers_mark_as_shipped_callback');
function woocommerce_dropshippers_mark_as_shipped_callback(){
	$order_id = $_GET['orderid'];
	$supplier_id = @$_GET['supplierid'];

	$dKey = 'order_'.$order_id;
	$dOptions = get_dropship_option();

	if(!isset($dOptions[$dKey])) {
		echo '<p style="color:red;">Sorry for inconvenience: This link will work only for the newly placed orders!</p>';
		wp_die();
	}

	$shipping_status = $dOptions[$dKey]['shipping_status'];


	$my_wc_order = new WC_Order($order_id);
	$my_wc_order_number = $my_wc_order->get_order_number();

	if($shipping_status == 'completed'){
		$my_wc_order->update_status('completed');
	} else {
		$dOptions[$dKey][$supplier_id] = 'completed';
		$dFlag = true;
        $dFlag1 = "yes";
		foreach($dOptions[$dKey] as $k => $v) {
			//echo '-->'.$k.'<=='.$supplier_id.'==>'.$v; 
			if($k != 'shipping_status' && $v == 'processing') {
				$dFlag = false;
				$dFlag1 = "no";
			}
		}

		if($dFlag === true) {
			$dOptions[$dKey]['shipping_status'] = 'completed';
			$my_wc_order->update_status('completed');
		}

		update_dropship_option($dOptions);   
	}

	//$my_wc_order->update_status('completed');
	echo '<h1>Order #'.$order_id.'</h1>';
	echo '<p style="color:green;">Order has been notified as shipped for this supplier, and Marked as Completed if required.</p>';

	if(isset($_GET['return'])){
		if($dFlag1 == 'no'){
			
			header('Location:'.$_GET['return'].'admin.php?page=dropshipper-order-list&success='.$dFlag1);
		}
        else
        {
              header('Location:'.$_GET['return'].'admin.php?page=dropshipper-order-list&success='.$dFlag1);

        }
	}
	


	die;
}


function dropshipper_order_list() {
	global $wpdb;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;
	$uemail = $current_user->user_email;
	$sid = get_user_meta($uid, 'supplier_id',true);

	$term = get_term_by('id', $sid, 'dropship_supplier'); 

	$post_status = array( 'wc-processing', 'wc-completed', 'wc-on-hold' );
	$args = array(
		'post_type' => 'shop_order',
		'post_status' => $post_status,
		'meta_query' => array( 
			array(
				'key' => 'supplier_'.$term->term_id,
				'value' => $term->name
			)
		)
	);

	$the_query = new WP_Query( $args );

	echo '<div class="wrap">
			<h1>Supplier Orders</h1>
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th scope="col" id="id" class="manage-column column-id column-primary sortable desc">ID</th>
						<th scope="col" id="date" class="manage-column column-date">Date</th>
						<th scope="col" id="product" class="manage-column column-product">Product</th>
						<th scope="col" id="client" class="manage-column column-client-info">Client Info</th>	
						<th scope="col" id="client" class="manage-column column-client-info">Contact Info</th>	
						<th scope="col" id="shipping" class="manage-column column-shipping-info">Shipping Info</th>	
						<th scope="col" id="status" class="manage-column column-status-info">Status</th>	
					</tr>
				</thead>
				<tbody id="the-list">';

				$user_id = get_current_user_id();
				$supplier_id = get_user_meta($user_id, 'supplier_id');
                 

				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) : $the_query->the_post();
	      				$order = wc_get_order( get_the_ID() );
	      				$items = $order->get_items();
	      				$fake_ajax_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_dropshippers_mark_as_shipped&return='.admin_url().'&orderid=' . get_the_ID() . '&supplierid=' . @$supplier_id[0]), 'woocommerce_dropshippers_mark_as_shipped' );
	      				$upload_dir = wp_get_upload_dir();
	      				$pdfpath =$upload_dir['baseurl'].'/'.get_the_ID().'/'.get_the_ID().'_'.$term->slug.'.pdf';
	      				
	      				$dropshipper_shipping_info = get_post_meta(get_the_ID(), 'dropshipper_shipping_info_'.get_current_user_id(), true);
						if(!$dropshipper_shipping_info){
							$dropshipper_shipping_info = array(
								'date' => '',
								'tracking_number' => '',
								'shipping_company' => '',
								'notes' => ''
							);
						}
								
							/*	$ds = wc_dropshipping_get_dropship_supplier_by_product_id( intval( $item['product_id'] ) );
								if ($ds['order_email_addresses'] == $uemail) {*/
									//$product = $order->get_product_from_item( $item );
									
									//$prod_info = WC_Dropshipping_Orders::get_order_product_info($item,$product);

								
							
	      				

					   echo '<tr><td class="id column-id" data-colname="id">'.get_the_ID().'</td>
						<td class="date column-date" data-colname="date">'.get_the_date().'</td>';

						echo '<td class="product column-product" data-colname="product">';
						if ( count( $items ) > 0 ) {
							foreach( $items as $item_id => $item ) {
								$ds = wc_dropshipping_get_dropship_supplier_by_product_id( intval( $item['product_id'] ) );
								if ($ds['order_email_addresses'] == $uemail) {
							echo '<p>'. $product_name = $item->get_name(). '</p>';
									}
								}
						}	
						echo '</td>

						<td class="client column-client" data-colname="client">'. $order->get_formatted_shipping_address() .'</td>
						<td class="client-email column-client-email" data-colname="client-email">'. $order->get_billing_email() .'<br><div class="row-actions"><span><a href="mailto:'.$order->get_billing_email().'">Send an Email</a></span></div></td>
						<td class="shipping column-shipping" data-colname="shipping">
							<p>Date: '.$dropshipper_shipping_info['date'].'</p>
							<p>Tracking number: '.$dropshipper_shipping_info['tracking_number'].'</p>
							<p>Shipping Company: '.$dropshipper_shipping_info['shipping_company'].'</p>
							<p>Notes: '.$dropshipper_shipping_info['notes'].'</p>
							<br>
							<button id="open_dropshipper_dialog_'.get_the_ID().'" class="button button-primary" onclick="open_dropshipper_dialog('.get_the_ID().')" style="margin-top:2px">Edit Shipping Info</button>
						</td>
						<td class="status column-status" data-colname="status">'. $order->get_status().'<br>';
							if($order->get_status() != 'completed') {
							echo '<a id="mark_dropshipped_'.get_the_ID().'" class="button button-primary" href="'.$fake_ajax_url.'" style="margin-top:2px">Mark as Complete</a>
							<br>';
						}
						echo '<a href="'.$pdfpath.'" target="blank" id="print_slip_'.get_the_ID().'" class="button button-primary" style="margin-top:2px">Download packing slip</a>
						</td></tr>';

							/*}*/
						
					endwhile;
					} 
				wp_reset_postdata();

			echo '</tbody>
				<tfoot>
					<tr>
						<th scope="col" id="id" class="manage-column column-id column-primary sortable desc">ID</th>
						<th scope="col" id="date" class="manage-column column-date">Date</th>
						<th scope="col" id="product" class="manage-column column-product">Product</th>
						<th scope="col" id="client" class="manage-column column-client-info">Client Info</th>	
						<th scope="col" id="client" class="manage-column column-client-info">Contact Info</th>	
						<th scope="col" id="shipping" class="manage-column column-shipping-info">Shipping Info</th>	
						<th scope="col" id="status" class="manage-column column-status-info">Status</th>	
					</tr>
				</tfoot>
			</table>
		</div>';

	echo '<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
			

			<div id="input-dialog-template" style="display:none">
				<label for="input-dialog-date"><label for="input-dialog-date">Date</label></label>
				<input type="text" name="input-dialog-date" id="input-dialog-date" style="width:100%">
				<label for="input-dialog-trackingnumber"><label for="input-dialog-trackingnumber">Tracking Number(s)</label></label>
				<textarea name="input-dialog-trackingnumber" id="input-dialog-trackingnumber" style="width:100%"></textarea>
				<label for="input-dialog-shippingcompany"><label for="input-dialog-shippingcompany">Shipping Company</label></label>
				<textarea name="input-dialog-shippingcompany" id="input-dialog-shippingcompany" style="width:100%"></textarea>
				<label for="input-dialog-notes"><label for="input-dialog-notes">Notes</label></label>
				<textarea name="input-dialog-notes" id="input-dialog-notes" style="width:100%"></textarea>
			</div>'	;

}
if(@$_GET['success'] == 'no'){

  	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
		<!-- Modal -->
		<div class="modal" id="complete_order_mark_Modal">
		    <div class="modal-dialog">
		      <div class="modal-content">
		      
		        <!-- Modal Header -->
		        <div class="modal-header">
		          <h4 class="modal-title"></h4>
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		        </div>
		        
		        <!-- Modal body -->
		        <div class="modal-body">
		          Thank you for completing this order. It will be under process untill all other dropshippers mark this order as
		          complete.
		        </div>
		        
		        <!-- Modal footer -->
		        <div class="modal-footer">
		          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		        </div>
		        
		      </div>
		    </div>
		</div>';
}


