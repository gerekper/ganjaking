<?php
/**
 * Earning Percentage rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="rs-earning-percentage-rule widefat striped rs_sample" cellspacing="0">
	<thead>
		<tr class="rsdynamicrulecreation">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points Earning Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Free Product(s)/Bonus Points', 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>

	<tbody id="here">
		<?php
		$rewards_dynamic_rulerule = get_option( 'rewards_dynamic_rule' ) ;
		if ( srp_check_is_array( $rewards_dynamic_rulerule ) ) :
			foreach ( $rewards_dynamic_rulerule as $i => $rewards_dynamic_rule ) :
				?>
				<tr class="rsdynamicrulecreation">

					<td class="column-columnname">
						<input type="text" 
							   name="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][name]"
							   class="short" 
							   value="<?php echo wp_kses_post( $rewards_dynamic_rule[ 'name' ] ) ; ?>"/>
					</td>

					<td class="column-columnname">
						<input type="number" 
							   step="any"
							   min="0" 
							   name="rewards_dynamic_rule[<?php echo esc_attr($i) ; ?>][rewardpoints]" 
							   id="rewards_dynamic_rewardpoints<?php echo esc_attr( $i ) ; ?>" 
							   class="short" 
							   value="<?php echo esc_html( $rewards_dynamic_rule[ 'rewardpoints' ] ) ; ?>"/>
					</td>

					<td class="column-columnname">
						<input type ="number" 
							   name="rewards_dynamic_rule[<?php echo esc_attr($i) ; ?>][percentage]" 
							   id="rewards_dynamic_rule_percentage<?php echo esc_attr( $i ) ; ?>" 
							   class="short test" 
							   value="<?php echo esc_html( $rewards_dynamic_rule[ 'percentage' ] ) ; ?>"/>
					</td>
					<?php $earning_type = isset( $rewards_dynamic_rule[ 'type' ] ) ? $rewards_dynamic_rule[ 'type' ] : 1 ; ?>
					<td>
						<select name="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][type]" class="rs-member-level-earning-type" id="rewards_dynamic_rule_type<?php echo esc_attr( $i ) ; ?>">
							<option value="1" <?php selected( '1', $earning_type ) ; ?>><?php esc_html_e( 'Free Product(s)', 'rewardsystem' ) ; ?></option>
							<option value="2" <?php selected( '2', $earning_type ) ; ?>><?php esc_html_e( 'Bonus Points', 'rewardsystem' ) ; ?></option>
						</select> 
					</td>

					<td class="column-columnname">
						 <div class="rs-free-product-data">
						<?php
						if ( ( float ) $woocommerce->version > ( float ) ( '2.2.0' ) ) {
							if ( $woocommerce->version >= ( float ) ( '3.0.0' ) ) {
								?>
																					
								<select class="wc-product-search rs-free-product" 
										multiple="multiple"
										id="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>]['product_list'][]" 
										name="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][product_list][]" 
										data-placeholder="<?php esc_html_e( 'Search for a product' , 'woocommerce' ) ; ?>" 
										data-action="woocommerce_json_search_products_and_variations" data-multiple="true"
										multiple = "multiple" >
											<?php
											$json_ids = array() ;
											if ( isset( $rewards_dynamic_rule[ 'product_list' ] ) && '' != $rewards_dynamic_rule[ 'product_list' ] ) {
												$list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
												if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
													$product_ids = $list_of_produts ;
												} else {
													$product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
												}
												foreach ( $product_ids as $product_id ) {
													$product = srp_product_object( $product_id ) ;
													if ( is_object( $product ) ) {
														$json_ids = wp_kses_post( $product->get_formatted_name() ) ;
														?>
														 <option value="<?php echo esc_html($product_id) ; ?>" 
														selected="selected"><?php echo esc_html( $json_ids ) ; ?></option>
																					   <?php
													}
												}
											}
											?>
								</select>
								<?php
							} else {
								?>
								<input type="hidden" 
									   class="wc-product-search rs-free-product"
									   id="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][product_list][]" 
									   name="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][product_list][]"
									   data-placeholder="<?php esc_html_e( 'Search for a product' , 'woocommerce' ) ; ?>" 
									   data-action="woocommerce_json_search_products_and_variations" 
									   data-multiple="true" 
									   multiple = "multiple"
									   data-selected="
									   <?php
										$json_ids = array() ;
										if ( '' != $rewards_dynamic_rule[ 'product_list' ] ) {
											$list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
											if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
												$product_ids = $list_of_produts ;
											} else {
												$product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
											}
											foreach ( $product_ids as $product_id ) {
												$product = srp_product_object( $product_id ) ;
												if ( is_object( $product ) ) {
													$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() ) ;
												}
											} echo esc_attr( json_encode( $json_ids ) ) ;
										}
										?>
									   " value="<?php echo wp_kses_post(implode( ',' , array_keys( $json_ids )) ) ; ?>" />
									   <?php
							}
						} else {
							?>
							<!-- For Old Version -->
							<select multiple name="rewards_dynamic_rule[<?php echo esc_attr( $i ) ; ?>][product_list][]" 
									class="rs_add_free_product_user_levels rs-product-search">
								 <?php
									if ( '' != $rewards_dynamic_rule[ 'product_list' ] ) {
											$list_of_produts = $rewards_dynamic_rule[ 'product_list' ] ;
										if ( is_array( $list_of_produts ) && ! empty( $list_of_produts ) ) {
											$product_ids = $list_of_produts ;
										} else {
											$product_ids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $list_of_produts ) ) ) ;
										}

										foreach ( $product_ids as $rs_free_id ) {
											echo '<option value="' . esc_html( $rs_free_id ) . '" ' ;
											selected( 1 , 1 ) ;
											$product_id = esc_html( $rs_free_id );
											$product_title = get_the_title( $rs_free_id );
											echo wp_kses_post(">#$product_id&ndash;$product_title");
											?>
											<?php
										}
									} else {
										?>
									<option value=""></option>
										<?php
									}
									?>
							</select>
							<?php
						}
						?>
						</div>
						<div class="rs-bouns-point-data">
						 <input type="number" 
								   step="any"
								   min="1" 
								   name="rewards_dynamic_rule[<?php echo esc_attr($i) ; ?>][bounspoints]" 
								   id="rewards_dynamic_bounspoints<?php echo esc_attr( $i ) ; ?>" 
								   class="short rs-bonus-points" 
								   value="<?php echo esc_html( $rewards_dynamic_rule[ 'bounspoints' ] ) ; ?>"/>
						</div>    
					</td>

					<td class="column-columnname num">
						<span class="rs-remove-earning-percentage-rule button-secondary"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></span>
					</td>

				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>

	<tfoot>
		<tr class="rsdynamicrulecreation">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class="manage-column column-columnname num" scope="col"> <span class="rs-add-earning-percentage-rule add button-primary"><?php esc_html_e( 'Add New Level' , 'rewardsystem' ) ; ?></span></td>
		</tr>
		<tr class="rsdynamicrulecreation">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points Earning Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Free Product(s)/Bonus Points', 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>

		</tr>
	</tfoot>
</table>
<?php
