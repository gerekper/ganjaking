<div class="updb-widget-style">
<div class="updb-basic-info"><?php _e( 'Purchases', 'userpro-dashboard' );?></div>
<div class="updb-view-profile-details"><br>
<?php 
		global $wpdb;
		if( $args['template'] == 'view' ){
			if( empty( $upw_default_options ) ){
				$upw_default_options = new UPWDefaultOptions();
			}
			if(is_user_logged_in()){
				$user = get_userdata( get_current_user_id() );
				$customer_data = array( get_current_user_id() );
			}
			else{
				$user = get_userdata( $user_id );
				$customer_data = array( $user_id );
			}
			
			$customer_data[] = $user->user_email;
			$customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );
			$total_products = $upw_default_options->userpro_woocommerce_get_option( 'upw_total_products_show' );
			$result = $wpdb->get_col( "
								SELECT distinct(im.meta_value) FROM {$wpdb->posts} AS p
								INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
								INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
								INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
								WHERE p.post_status IN ( 'wc-completed', 'wc-processing' )
								AND pm.meta_key IN ( '_billing_email', '_customer_user' )
								AND im.meta_key IN ( '_product_id', '_variation_id' )
								AND im.meta_value != 0
								AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . " ' ) limit $total_products" );
			if ( $result ) {
				
				global $post, $product, $userpro;
				?>
				<div class="upw-purchases">
				<div class="userpro-section userpro-column userpro-collapsible-1 userpro-collapsed-0"><?php _e( $upw_default_options->userpro_woocommerce_get_option( 'upw_purchase_tab_text' ), 'userpro-woocommerce');?></div>
				<div class="userpro-field userpro-field-all-media userpro-field-view" style="display:block;">
				<div class="upw-product-wrapper purchases-widget">
					<table class="upw-purchases-table">
						<thead>
							<tr>
								<th class="upw-date-label" style="text-align:center;font-size: 11px;font-weight: 900;"><span class=""><?php _e( 'Title', 'userpro-woocommerce' ); ?></span></th>
								<th class="upw-status-label" style="text-align:center;font-size: 11px;font-weight: 900;"><span class=""><?php _e( 'Price', 'userpro-woocommerce' ); ?></span></th>
								<th class="upw-total-label" style="text-align:center;font-size: 11px;font-weight: 900;"><span class=""><?php _e( 'Availablity', 'userpro-woocommerce' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
				<?php 
				
				for($i=0;$i<count($result);$i++){
				
					$product = wc_get_product( $result[$i] );
				
					$total_sales = (int)get_post_meta( $result[$i], 'total_sales', true );
					$total_sales = number_format( $total_sales );
				
					$stock_state = get_post_meta( $result[$i], '_stock_status', true );
					if ( $stock_state == 'instock' ) {
						$stock_state = __('Instock','userpro-woocommerce');
					}elseif ( $stock_state == 'outofstock' ){
						$stock_state = __('Out of stock','userpro-woocommerce');
					}
					$product_link   = get_permalink( $result[$i] );
				
					?>
							<tr>
								<td class="upw-product-title" style="text-align:center;font-size: 12px;"><a href="<?php echo $product_link; ?>"><span class="upw-product-title upw-db"><?php echo get_the_title($result[$i]); ?></span></a></td>
								<td class="upw-product-price" style="text-align:center;font-size: 12px;"><span class="upw-product-price upw-db"><?php echo $product->get_price_html(); ?></span></td>
								<td class="upw-stock-sate" style="text-align:center;font-size: 12px;"><span class="upw-stock-sate upw-db"><?php echo $stock_state; ?></span></td>
							</tr>
						
						
				
			<?php } ?>
						</tbody>
					</table>
				</div>
				</div>
				</div>
			<?php 
			}
		}	
?>
</div>
</div>