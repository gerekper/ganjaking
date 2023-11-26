<?php
global $wp;

if(isset($_GET['delship']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_ship_method');
	$rtwwdpd_row_no = sanitize_post($_GET['delship']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_ship_method',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_shipping_method';
	header('Location: '.$rtwwdpd_new_url);
    die();
}
$abcd = 'verification_done';
if(isset($_POST['rtwwdpd_save_ship_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_ship']);
	$rtwwdpd_products_option = get_option('rtwwdpd_ship_method');
	if($rtwwdpd_products_option == '')
	{
		$rtwwdpd_products_option = array();
	}
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();

	foreach($rtwwdpd_prod as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option_no != 'save'){
		unset($_REQUEST['edit_ship']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_ship_method',$rtwwdpd_products_option);
	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div>
<?php 
}

if(isset($_POST['rtwwdpd_copy_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_ship_method');
	if($rtwwdpd_products_option == '')
	{
		$rtwwdpd_products_option = array();
	}
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();

	foreach($rtwwdpd_prod as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}

	if(isset($rtwwdpd_products_option[$rtwwdpd_option_no]) && !empty($rtwwdpd_products_option[$rtwwdpd_option_no]))
	{
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_ship_offer_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_ship_offer_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_ship_offer_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_ship_offer_name'],"",-5);
	
	update_option('rtwwdpd_ship_method',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$abcd, array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
?>
<div class="rtwwdpd_right">
<div class="rtwwdpd_add_buttons">
	<input class="rtw-button rtwwdpd_single_prod_rule" id="add_single_product" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
</div> 
<?php
if(isset($_GET['edit_ship']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	$rtwwdpd_products_option = get_option('rtwwdpd_ship_method');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['edit_ship']];
	$key = 'edit_ship';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_shipping_method');
	?>
	<div class="rtwwdpd_add_single_rule rtwwdpd_active rtwwdpd_form_layout_wrapper">
		<form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_rule_tab_combi active">
								<a class="rtwwdpd_link" id="rtwproduct_rule_combi">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab_combi" id="rtwproduct_restrict_combi">
								<a class="rtwwdpd_link">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab_combi" id="rtwproduct_validity_combi">
								<a class="rtwwdpd_link">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
								<input type="hidden" id="edit_ship" name="edit_ship" value="<?php echo esc_attr($_GET['edit_ship']); ?>">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_ship_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_ship_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_ship_offer_name']) : ''; ?>">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<?php

									$rtwwdpd_available_shipping_methods = WC()->shipping()->get_shipping_methods();
									$rtwwdpd_get_title = function($obj){
										return $obj->get_method_title();
									};
									$rtwwdpd_available_shipping_methods=array_map($rtwwdpd_get_title,$rtwwdpd_available_shipping_methods);
									?>
									<tr>
			            				<td>
			            					<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>	
			            				</td>
			            				<td>
			            					<select class="rtwwdpd_payment_method" name="allowed_shipping_methods">
		            						<?php 
		            						if(is_array($rtwwdpd_available_shipping_methods) && !empty($rtwwdpd_available_shipping_methods))
		            						{	
			            						foreach ($rtwwdpd_available_shipping_methods as $key => $value) 
			            						{						
			            							if(isset($rtwwdpd_prev_prod['allowed_shipping_methods']))
						            				{ 		
						            					if($key == $rtwwdpd_prev_prod['allowed_shipping_methods'])
					            						{	
				            								echo '<option value="' . esc_attr($key) . '"' . selected($key, $rtwwdpd_prev_prod['allowed_shipping_methods']) . '>' . esc_html($value) . '</option>'; 
				            							}
				            							else{
				            								echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
				            							}
					            					}
					            					else{
			            								echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
			            							}
				            					}
				            				}
			            					?>
			            					</select>
			            				</td>
			            			</tr>
									<?php 
								$delivery_zones = WC_Shipping_Zones::get_zones();
								$shipping_zone = array();
								$shipping_zone['all'] = esc_html__('Any', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								if(!empty($delivery_zones))
								{
									foreach ($delivery_zones as $key => $the_zone ) {
										$shipping_zone[$the_zone['id']] = $the_zone['zone_name'];
									}
								}
								?>
								<tr>
		            				<td>
		            					<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Zones', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>	
		            				</td>
		            				<td>
		            					<select multiple="multiple" class="rtwwdpd_payment_method" name="allowed_shipping_zones[]">
	            						<?php 
	            						if(is_array($shipping_zone) && !empty($shipping_zone))
	            						{
		            						foreach ($shipping_zone as $key => $value) 
		            						{
												// echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
												if(isset($rtwwdpd_prev_prod['allowed_shipping_zones']))
												{ 			 
													if(in_array($key, $rtwwdpd_prev_prod['allowed_shipping_zones']))
													{
														echo '<option value="' . esc_attr($key) . '" selected="selected" >' . esc_html($value) . '</option>'; 
													}
													else{
														echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
													}
												}
												else{
													echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
												}


			            					}
			            				}
		            					?>
		            					</select>
		            				</td>
		            			</tr>
			            			<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_ship_discount_type">
												<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_ship_discount_type'], 'rtwwdpd_discount_percentage'); ?>>
													<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_ship_discount_type'], 'rtwwdpd_flat_discount_amount'); ?>>
													<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_ship_discount_type'], 'rtwwdpd_fixed_price'); ?>>
													<?php esc_html_e( 'Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Choose discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_ship_discount_value']) ? $rtwwdpd_prev_prod['rtwwdpd_ship_discount_value'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_ship_discount_value">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
		            			</table>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
								<table class="rtwwdpd_table_edit">
		            				<tr>
										<td>
						            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
					            		</td>
					            		<td>
					            			<input type="number" required value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_ship_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_ship_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_ship_max_discount">
											<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
						            		<?php
							            	global $wp_roles;
							            	$rtwwdpd_roles 	= $wp_roles->get_names();
							            	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
							            	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); 
			            					$rtwwdpd_selected_role =  $rtwwdpd_prev_prod['rtwwdpd_select_roles']; ?>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple="multiple">
					            				<?php
					            				foreach ($rtwwdpd_roles as $roles => $role) {
					            					if(is_array($rtwwdpd_selected_role))
					            					{
				            						?>
				            							<option value="<?php echo esc_attr($roles); ?>"<?php
					            						foreach ($rtwwdpd_selected_role as $ids => $roleid) {
					            							selected($roles, $roleid);
					            						}
					            						?>>
															<?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
					            				<?php
				            					}
				            					else{
					            				?>
														<option value="<?php echo esc_attr($roles); ?>">
															<?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
					            				<?php
					            					}
					            				}
				            				?>
			            					</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Select user role for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum amount spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            			</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_spend']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_spend']) : '' ; ?>" min="0" name="rtwwdpd_min_spend">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			            					</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_prod_cont']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_prod_cont']) : '' ; ?>" min="0" name="rtwwdpd_min_prod_cont">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum product need to be purchase by a customer to be eligible for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_ship_exclude_sale" <?php checked( isset( $rtwwdpd_prev_prod['rtwwdpd_ship_exclude_sale'] ) ? $rtwwdpd_prev_prod['rtwwdpd_ship_exclude_sale'] : 'NO', 'yes'); ?>/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
			            		</table>
			            	</div>

							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab_combi">
								<table class="rtwwdpd_table_edit">
		           					<tr>
		           						<td>
					           				<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
						            	</td>
						            	<td>
					           				<input type="date" name="rtwwdpd_ship_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_ship_from_date']); ?>" />
											<i class="rtwwdpd_description"><?php esc_html_e( 'The date from which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
					           				<label class="rtwwdpd_label"><?php esc_html_e('Valid To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
						            	</td>
						            	<td>
			           						<input type="date" name="rtwwdpd_ship_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_ship_to_date']); ?>"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
           						</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="rtwwdpd_prod_combi_save_n_cancel rtwwdpd_btn_save_n_cancel"> 
				<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_shipp_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_shipp_rule" name="rtwwdpd_save_ship_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }else{
?>
<div class="rtwwdpd_add_single_rule rtwwdpd_form_layout_wrapper">
 	<form method="post" action="" enctype="multipart/form-data">
 		<div id="woocommerce-product-data" class="postbox ">
 			<div class="inside">
 				<div class="panel-wrap product_data">
 					<ul class="product_data_tabs wc-tabs">
 						<li class="rtwwdpd_rule_tab_combi active">
 							<a class="rtwwdpd_link" id="rtwproduct_rule_combi">
 								<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
 							</a>
 						</li>
 						<li class="rtwwdpd_restriction_tab_combi" id="rtwproduct_restrict_combi">
 							<a class="rtwwdpd_link">
 								<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
 							</a>
 						</li>
 						<li class="rtwwdpd_time_tab_combi" id="rtwproduct_validity_combi">
 							<a class="rtwwdpd_link">
 								<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
 							</a>
 						</li>
 					</ul>

 					<div class="panel woocommerce_options_panel">
 						<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">

 							<input type="hidden" id="edit_ship" name="edit_ship" value="save">
 							<table class="rtwwdpd_table_edit">
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_ship_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<?php
								$rtwwdpd_available_shipping_methods = WC()->shipping()->get_shipping_methods();

								$rtwwdpd_get_title = function($obj){
									return $obj->get_method_title();
								};
								$rtwwdpd_available_shipping_methods=array_map($rtwwdpd_get_title,$rtwwdpd_available_shipping_methods);
								?>
								<tr>
		            				<td>
		            					<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>	
		            				</td>
		            				<td>
		            					<select class="rtwwdpd_payment_method" name="allowed_shipping_methods">
	            						<?php 
	            						if(is_array($rtwwdpd_available_shipping_methods) && !empty($rtwwdpd_available_shipping_methods))
	            						{
		            						foreach ($rtwwdpd_available_shipping_methods as $key => $value) 
		            						{	
												// if($value == 'Free shipping'){
												// 	continue;
												// }
		            							echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
			            					}
			            				}
		            					?>
		            					</select>
		            				</td>
		            			</tr>
								<?php 
								$delivery_zones = WC_Shipping_Zones::get_zones();
								$shipping_zone = array();
								$shipping_zone['all'] = esc_html__('Any', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								if(!empty($delivery_zones))
								{
									foreach ($delivery_zones as $key => $the_zone ) {
										$shipping_zone[$the_zone['id']] = $the_zone['zone_name'];
									}
								}
								?>
								<tr>
		            				<td>
		            					<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Zones', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>	
		            				</td>
		            				<td>
		            					<select multiple="multiple" class="rtwwdpd_payment_method" name="allowed_shipping_zones[]">
	            						<?php 
	            						if(is_array($shipping_zone) && !empty($shipping_zone))
	            						{
		            						foreach ($shipping_zone as $key => $value) 
		            						{	
												if($key == "all"){
		            							echo '<option selected="selected" value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
												}
												else{													
													echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
												}
			            					}
			            				}
		            					?>
		            					</select>
		            				</td>
		            			</tr>
		            			<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<select name="rtwwdpd_ship_discount_type">
											<option value="rtwwdpd_discount_percentage">
												<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_flat_discount_amount">
												<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_fixed_price">
												<?php esc_html_e( 'Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
										</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'Choose discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
									</td>
									<td>
										<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_ship_discount_value">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
							</table>
 						</div>

 						<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
 							<table class="rtwwdpd_table_edit">
	            				<tr>
									<td>
					            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            		</label>
				            		</td>
				            		<td>
				            			<input type="number" required value="" min="0" name="rtwwdpd_ship_max_discount">
										<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr>
									<td>
					            		<?php
						            	global $wp_roles;
						            	$rtwwdpd_roles 	= $wp_roles->get_names();
						            	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						            	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );  ?>
				            			<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            			</label>
				            		</td>
				            		<td>
				            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple="multiple">
				            				<?php
				            				foreach ($rtwwdpd_roles as $roles => $role) {
				            					?>
													<option value="<?php echo esc_attr($roles); ?>">
															<?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
					            				<?php
					            				}
				            				?>
				            			</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'Select user role for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Minimum amount spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			            				</label>
									</td>
									<td>
										<input type="number" value="" min="0" name="rtwwdpd_min_spend">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			            				</label>
									</td>
									<td>
										<input type="number" value="" min="0" name="rtwwdpd_min_prod_cont">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum product need to be purchase by a customer to be eligible for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
		            				<td>
				            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            			</label>
				            		</td>
				            		<td>
				            			<input type="checkbox" value="yes" name="rtwwdpd_ship_exclude_sale"/>
										<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
	            			</table>
 						</div>

 						<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab_combi">
 							<table class="rtwwdpd_table_edit">
	           					<tr>
	           						<td>
				           				<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            		</label>
					            	</td>
					            	<td>
				           				<input type="date" name="rtwwdpd_ship_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
										<i class="rtwwdpd_description"><?php esc_html_e( 'The date from which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr>
									<td>
				           				<label class="rtwwdpd_label"><?php esc_html_e('Valid To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            		</label>
					            	</td>
					            	<td>
				           				<input type="date" name="rtwwdpd_ship_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
										<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
           					</table>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>
 		<div class="rtwwdpd_prod_combi_save_n_cancel rtwwdpd_btn_save_n_cancel"> 
 			<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_shipp_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			<input id="submit_shipp_rule" name="rtwwdpd_save_ship_rule" type="submit" hidden="hidden"/>
 			<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
 		</div>
 	</form>
</div>
<?php }
	$rtwwdpd_response_html = '';
	$rtwwdpd_response_html = apply_filters('rtwwdpd_shipping_rule_html', $rtwwdpd_response_html);
	echo $rtwwdpd_response_html;
?>
<div class="rtwwdpd_enable_rule">
	<b>
		<?php esc_html_e( 'How to show discount? : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
		$rtwwdpd_enable = get_option('rtwwdpd_apply_shipping_discount_on', 'message');
	?>
	</b>
	<select name="rtwwdpd_apply_shipping_discount_on" class="rtwwdpd_apply_shipping_discount_on">
		<option value="message" <?php selected( $rtwwdpd_enable, 'message'); ?>><?php esc_attr_e( 'Show as Shipping Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="applied" <?php selected( $rtwwdpd_enable, 'applied'); ?>><?php esc_attr_e( 'Directly Apply', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
<div class="rtwwdpd_enable_rules">
	<b>
		<?php esc_html_e( 'Show Shipping Discount On : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); 
		$rtwwdpd_chkout = get_option('rtwwdpd_show_ship_on_chkout', 'both');
	?>
	</b>
	<select name="rtwwdpd_show_ship_on_chkout" class="rtwwdpd_show_ship_on_chkout">
		<option value="checkout" <?php selected( $rtwwdpd_chkout, 'checkout'); ?>><?php esc_attr_e( 'Only on Checkout Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="both" <?php selected( $rtwwdpd_chkout, 'both'); ?>><?php esc_attr_e( 'On Cart & Checkout Pages', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
<div class="rtwwdpd_prod_table">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="shipp_tbl" cellspacing="0">
		<thead>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Shipping Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Shipping Zones', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Product Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</thead>
		<?php $rtwwdpd_products_option = get_option('rtwwdpd_ship_method');

		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

        $rtwwdpd_available_shipping_methods = WC()->shipping()->get_shipping_methods();
        $rtwwdpd_get_title = function($obj){
        	return $obj->get_method_title();
        };
                        
        $rtwwdpd_available_shipping_methods = array_map($rtwwdpd_get_title,$rtwwdpd_available_shipping_methods);

		global $wp_roles;
    	$rtwwdpd_roles 	= $wp_roles->get_names();
    	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );
		
		$delivery_zones = WC_Shipping_Zones::get_zones();
		$shipping_zone = array();
		$shipping_zone['all'] = esc_html__('Any', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
		if(!empty($delivery_zones))
		{
			foreach ($delivery_zones as $key => $the_zone ) {
				$shipping_zone[$the_zone['id']] = $the_zone['zone_name'];
			}
		}

		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)){	?>
		<tbody>
			<?php
			foreach ($rtwwdpd_products_option as $key => $value) {

				echo '<tr data-val="'.$key.'">';
				
				echo '<td class="rtwrow_no">'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
				echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.(isset($value['rtwwdpd_ship_offer_name']) ? esc_html__($value['rtwwdpd_ship_offer_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>'.(isset($rtwwdpd_available_shipping_methods[$value['allowed_shipping_methods']]) ? esc_html__($rtwwdpd_available_shipping_methods[$value['allowed_shipping_methods']], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>';
				if(isset($value['allowed_shipping_zones']) && is_array($value['allowed_shipping_zones']) && !empty($value['allowed_shipping_zones']))
				{
					foreach ($value['allowed_shipping_zones'] as $val) {
						echo esc_html($shipping_zone[$val]).'<br>';
					}
				}
				echo '</td>';

				if($value['rtwwdpd_ship_discount_type'] == 'rtwwdpd_discount_percentage')
				{
					echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				elseif($value['rtwwdpd_ship_discount_type'] == 'rtwwdpd_flat_discount_amount')
				{
					echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else{
					echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				
				echo '<td>'.(isset($value['rtwwdpd_ship_discount_value']) ? esc_html__($value['rtwwdpd_ship_discount_value'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_ship_max_discount']) ? esc_html__($value['rtwwdpd_ship_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) :'').'</td>';

				echo '<td>'.(isset($value['rtwwdpd_min_spend']) ? esc_html__($value['rtwwdpd_min_spend'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>'.(isset($value['rtwwdpd_min_prod_cont']) ? esc_html__($value['rtwwdpd_min_prod_cont'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
				{
					foreach ($value['rtwwdpd_select_roles'] as $val) {
						echo esc_html($rtwwdpd_roles[$val]).'<br>';
					}
				}
				echo '</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_ship_from_date']) ? esc_html($value['rtwwdpd_ship_from_date'] ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_ship_to_date']) ? esc_html($value['rtwwdpd_ship_to_date'] ) : '').'</td>';
				
				echo '<td>';
				if(isset($value['rtwwdpd_ship_exclude_sale'])){
					echo esc_html($value['rtwwdpd_ship_exclude_sale']);
				}else{
					esc_html_e('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
				}
				echo '</td>';
				
				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&edit_ship='.$key ).'"><input type="button" class="rtwwdpd_edit_dt_row" value="'.esc_html__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
						<a href="'.esc_url( $rtwwdpd_absolute_url .'&delship='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_html__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
				echo '</tr>';
			}
			?>		
		</tbody>
		<?php } ?>
		<tfoot>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Shipping Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Shipping Zones', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Product Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
