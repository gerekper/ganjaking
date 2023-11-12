<?php
global $wp;
if(isset($_POST['subpro_rule']))
{
	update_option('rtwwdpd_product_offer_msg', $_POST['rtwwdpd_pro_offer']);
}
if(isset($_GET['delprod']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delprod']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_single_prod_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$abc = 'verification_done'; 
if(isset($_POST['rtwwdpd_save_rule'])){

	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_chk']);
	$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');
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
		unset($_REQUEST['editpid']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}

	update_option('rtwwdpd_single_prod_rule',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}
if(isset($_POST['rtwwdpd_copy_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_name'],"",-5);
	
	update_option('rtwwdpd_single_prod_rule',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$abc, array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{ 
if(isset($_GET['editpid']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');

	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editpid']];
	$key = 'editpid';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_rules');
?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="add_single_product" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add Single Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<input class="rtw-button rtwwdpd_combi_prod_rule" id="add_combi_product" type="button" name="rtwwdpd_combi_prod_rule" value="<?php esc_attr_e( 'Add Combi Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
	<div class="rtwwdpd_add_main_wrapper rtwwdpd_form_layout_wrapper">
		<div class="rtwwdpd_add_single_rule rtwwdpd_active">
		<?php 
		$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
		?>
			<form action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_prod_rule_tab rtwwdpd_active">
									<a class="rtwwdpd_link rtwwdpd_active" id="rtwproduct_rule">
										<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_restriction_tab">
									<a class="rtwwdpd_link" id="rtwproduct_restrict">
										<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_time_tab">
									<a class="rtwwdpd_link" id="rtwproduct_validity">
										<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab">
									<input type="hidden" id="edit_chk_single" value="<?php echo esc_attr($_GET['editpid']);?>" name="edit_chk">
									<table class="rtwwdpd_table_edit">
										<?php 
										if($active_dayss == 'yes')
										{
											$daywise_discount = apply_filters('rtwwdpd_product_daywise_discount_edit', $_GET['editpid']);
											echo $daywise_discount;
										}
										?>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_name']) : ''; ?>">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwwdpd_rule_on" name="rtwwdpd_rule_on">
													<option <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_rule_on']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_rule_on']) : '', 'rtwwdpd_products' ); ?> value="rtwwdpd_products">
														<?php esc_html_e( 'Selected Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
													</option>
													<option <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_rule_on']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_rule_on']) : '', 'rtwwdpd_multiple_products' ); ?> value="rtwwdpd_multiple_products">
														<?php esc_html_e( 'Multiple Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
													</option>
													<option <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_rule_on']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_rule_on']) : '', 'rtwwdpd_cart' ); ?> value="rtwwdpd_cart">
														<?php esc_html_e( 'All Products in Cart', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
													</option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select option on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr id="product_id">
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Select Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
						            		</td>
						            		<td>
						            			<select class="wc-product-search rtwwdpd_prod_class" name="product_id" data-action="woocommerce_json_search_products_and_variations" data-placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
						            				<?php 
						            				if(isset($rtwwdpd_prev_prod['product_id']))
						            				{
						            					$product = wc_get_product($rtwwdpd_prev_prod['product_id']);
														if (is_object($product)) {
															echo '<option value="' . esc_attr($rtwwdpd_prev_prod['product_id']) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
														}
						            				}
						            				?>
					            				</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Product on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="multiple_product_ids">
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Select Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
						            		</td>
						            		<td>
						            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="multiple_product_ids[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
												<?php
												if(isset($rtwwdpd_prev_prod['multiple_product_ids']))
												{
													$products = $rtwwdpd_prev_prod['multiple_product_ids'];
													if(is_array($products) && !empty($products))
													{
														foreach($products as $key => $val)
														{
															$product = wc_get_product($val);
															if (is_object($product)) {
															echo '<option value="' . esc_attr($val) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
															}
														}
													}
												}
												?>
					            				</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Products on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="multiple_product_ids">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Condition', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_condition" id="rtwwdpd_condition">
													<option value="rtwwdpd_and" <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_condition'])?$rtwwdpd_prev_prod['rtwwdpd_condition']:'' , 'rtwwdpd_and');?>><?php esc_html_e( 'And', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_or" <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_condition'])?$rtwwdpd_prev_prod['rtwwdpd_condition']:'' , 'rtwwdpd_or');?>><?php esc_html_e( 'Or', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'If you select \'And\', all the selected products must be present in the cart or if you select \'Or\' any of the selected product in the cart get the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_check_for" id="rtwwdpd_check_for">
													<option value="rtwwdpd_quantity" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_quantity');?>><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_price');?>><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_weight" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_weight');?>><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<?php $rtwwdpd_check_var = 'Quantity';?>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" class="rtwwdpd_check_minvalue" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min']) : '' ; ?>" min="0" name="rtwwdpd_min">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_check_maxvalue">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max']) : '' ; ?>" name="rtwwdpd_max">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_discount_type">
													<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_discount_percentage') ?>>
														<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_flat_discount_amount') ?>>
														<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_fixed_price') ?>>
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
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_discount_value']) ? $rtwwdpd_prev_prod['rtwwdpd_discount_value'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_discount_value">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<?php
										$new_msg_foreach_rule = apply_filters('rtwwdpd_product_rule_edit_changes', $_GET['editpid']);
										if( $_GET['editpid'] != $new_msg_foreach_rule )
										{
											echo $new_msg_foreach_rule;
										}
										?>
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab">
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
							            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
							            		</label>
						            		</td>
						            		<td>
						            			<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>" min="0" required="required" name="rtwwdpd_max_discount">
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
						            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple>
					            				<?php
					            					foreach ($rtwwdpd_roles as $roles => $role) {
														
					            					if(is_array($rtwwdpd_selected_role))
					            					{
					            						?>
					            						<option value="<?php echo esc_attr($roles); ?>"<?php
						            						foreach ($rtwwdpd_selected_role as $ids => $roleid) {
						            							selected($roles, $roleid);
						            						}
						            					?> >
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
							            		<?php
												$args = array('orderby' => 'display_name');
												$wp_user_query = new WP_User_Query($args);
												$authors = $wp_user_query->get_results();
												$all_users_mail = array();
												if (!empty($authors)) {
													foreach ($authors as $author) {
														$all_users_mail[$author->ID] = $author->data->user_email;
													}
												}

												$rtwwdpd_selected_mail =  isset($rtwwdpd_prev_prod['rtwwdpd_select_emails']) ? $rtwwdpd_prev_prod['rtwwdpd_select_emails'] : array(); 
												?>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Restrict User with Email ID', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            			</label>
						            		</td>
						            		<td>
						            			<select class="rtwwdpd_select_roles" name="rtwwdpd_select_emails[]" multiple>
					            				<?php
					            					foreach ($all_users_mail as $roles => $role) {
														if(is_array($rtwwdpd_selected_mail))
														{
															?>
															<option value="<?php echo esc_attr($roles); ?>"<?php
																foreach ($rtwwdpd_selected_mail as $ids => $roleid) {
																	selected($roles, $roleid);
																}
															?> >
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
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select emails for which this discount is not available.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            				</label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_orders']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_orders']) : '' ; ?>" min="0" name="rtwwdpd_min_orders">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum number of orders done by a customer to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            			</label>
						            		</td>
						            		<td>
				            					<input type="checkbox" value="yes" name="rtwwdpd_exclude_sale" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_exclude_sale']) ? $rtwwdpd_prev_prod['rtwwdpd_exclude_sale'] : '', 'yes'); ?>/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab">
									<table class="rtwwdpd_table_edit">
		           					<tr>
		           						<td>
					           				<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
						            	</td>
						            	<td>
				           					<input type="date" name="rtwwdpd_single_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_single_from_date']); ?>" />
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
				           					<input type="date" name="rtwwdpd_single_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_single_to_date']); ?>"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e(' Based On Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
							            	<td>
						           				<input type="checkbox" name="rtwwdpd_enable_day" value="yes" class="rtwwdpd_day_chkbox" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_enable_day']) ? $rtwwdpd_prev_prod['rtwwdpd_enable_day'] : '', 'yes'); ?>/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Check If You want to Set Discount on Speciifc Day.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												
												</i>
											</td>
									</tr>
									<tr class="rtwwdpd_daywise_rule_row">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select class="rtwwdpd_select_day" name="rtwwdpd_select_day">
												
												<option value="">
													<?php esc_html_e( '-- Select --', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="7" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 7) ?> >
													<?php esc_html_e( 'Sunday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="1" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 1) ?> >
													<?php esc_html_e( 'Monday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="2" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 2) ?> >
													<?php esc_html_e( 'Tuesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="3" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 3) ?> >
													<?php esc_html_e( 'Wednesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="4" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 4) ?> >
													<?php esc_html_e( 'Thursday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="5" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 5) ?> >
													<?php esc_html_e( 'Friday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="6" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day'], 6) ?> >
													<?php esc_html_e( 'Saturday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												
											</select>
										</td>
									</tr>
	           					</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_pro_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_pro_rule" name="rtwwdpd_save_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
	<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_prod_combi.php' ); ?>
	</div>
</div>
<?php }else {
	
?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="add_single_product" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add Single Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<input class="rtw-button rtwwdpd_combi_prod_rule" id="add_combi_product" type="button" name="rtwwdpd_combi_prod_rule" value="<?php esc_attr_e( 'Add Combi Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
	<div class="rtwwdpd_add_main_wrapper rtwwdpd_form_layout_wrapper">
		<div class="rtwwdpd_add_single_rule">
		<?php 
		// $daywise_discount = apply_filters('rtwwdpd_product_daywise_discount', '');
		// echo $daywise_discount;
		?>
			<form  action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_prod_rule_tab active">
									<a class="rtwwdpd_link" id="rtwproduct_rule">
										<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_restriction_tab">
									<a class="rtwwdpd_link" id="rtwproduct_restrict">
										<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_time_tab">
									<a class="rtwwdpd_link" id="rtwproduct_validity">
										<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab">
									<input type="hidden" id="edit_chk_single" value="save" name="edit_chk">
									<table class="rtwwdpd_table_edit">
										<?php 
											$daywise_discount = apply_filters('rtwwdpd_product_daywise_discount', '');
											echo $daywise_discount;
										?>
										<tr>	
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwwdpd_rule_on" name="rtwwdpd_rule_on">
													<option value="rtwwdpd_products">
														<?php esc_html_e( 'Selected Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
													</option>
													<option value="rtwwdpd_multiple_products">
														<?php esc_html_e( 'On Multiple Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
													</option>
													<option value="rtwwdpd_cart">
														<?php esc_html_e( 'All Products in Cart', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
													</option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select option on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr id="product_id">
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Select Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
						            		</td>
						            		<td>
						            			<select class="wc-product-search rtwwdpd_prod_class" name="product_id" data-action="woocommerce_json_search_products_and_variations" data-placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
					            				</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Product on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="multiple_product_ids">
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Select Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
						            		</td>
						            		<td>
						            			<select id="rtwwdpd_checking_placeholder" class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="multiple_product_ids[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
					            				</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Products on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="multiple_product_ids">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Condition', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_condition" id="rtwwdpd_condition">
													<option value="rtwwdpd_and"><?php esc_html_e( 'And', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_or"><?php esc_html_e( 'Or', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'If you select \'And\', all the selected products must be present in the cart or if you select \'Or\' any of the selected product in the cart get the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_check_for" id="rtwwdpd_check_for">
													<option value="rtwwdpd_quantity"><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_price"><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_weight"><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<?php $rtwwdpd_check_var = 'Quantity';?>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" class="rtwwdpd_check_minvalue" value="" min="0" name="rtwwdpd_min">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_check_maxvalue" >
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number"  value="" name="rtwwdpd_max">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_discount_type">
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
												<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_discount_value">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<?php
										$new_msg_foreach_rule = apply_filters('rtwwdpd_product_rule_changes', '');

										echo $new_msg_foreach_rule;
										?>
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab">
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
							            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
							            		</label>
						            		</td>
						            		<td>
						            			<input type="number" value="" min="0" required="required" name="rtwwdpd_max_discount">
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
												?>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            			</label>
						            		</td>
						            		<td>
						            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple>
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
							            		<?php
												$args = array('orderby' => 'display_name');
												$wp_user_query = new WP_User_Query($args);
												$authors = $wp_user_query->get_results();
												$all_users_mail = array();
												if (!empty($authors)) {
													foreach ($authors as $author) {
														$all_users_mail[$author->ID] = $author->data->user_email;
													}
												}
												?>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Restrict User with Email ID', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            			</label>
						            		</td>
						            		<td>
						            			<select class="rtwwdpd_select_roles" name="rtwwdpd_select_emails[]" multiple>
					            				<?php
					            					foreach ($all_users_mail as $roles => $role) {
														?>
														<option value="<?php echo esc_attr($roles); ?>">
															<?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
														<?php
													}
							            				?>
						            			</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select emails for which this discount is not available.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            				</label>
											</td>
											<td>
												<input type="number" value="" min="0" name="rtwwdpd_min_orders">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum number of orders done by a customer to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
				            				<td>
						            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            			</label>
						            		</td>
						            		<td>
				            					<input type="checkbox" value="yes" name="rtwwdpd_exclude_sale"/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab">
									<table class="rtwwdpd_table_edit">
			           					<tr>
			           						<td>
						           				<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
							            		</label>
							            	</td>
							            	<td>
						           				<input type="date" name="rtwwdpd_single_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
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
						           				<input type="date" name="rtwwdpd_single_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
						           				<label class="rtwwdpd_label"><?php esc_html_e(' Based On Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
							            		</label>
							            	</td>
							            	<td>
						           				<input type="checkbox" name="rtwwdpd_enable_day" value="yes" class="rtwwdpd_day_chkbox"/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Check If You want to Set Discount on Speciifc Day.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_daywise_rule_row">
											<td>
						           				<label class="rtwwdpd_label"><?php esc_html_e('Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
							            		</label>
							            	</td>
							            	<td>
												<select class="rtwwdpd_select_day" name="rtwwdpd_select_day">
					            				   
													<option value="">
														<?php esc_html_e( '-- Select --', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="7">
														<?php esc_html_e( 'Sunday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="1">
														<?php esc_html_e( 'Monday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="2">
														<?php esc_html_e( 'Tuesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="3">
														<?php esc_html_e( 'Wednesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="4">
														<?php esc_html_e( 'Thursday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="5">
														<?php esc_html_e( 'Friday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="6">
														<?php esc_html_e( 'Saturday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													
						            			</select>
											</td>
										</tr>
		           					</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
					<input class="rtw-button rtwwdpd_save_rule rtwwdpd_pro_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
					<input id="submit_pro_rule" name="rtwwdpd_save_rule" type="submit" hidden="hidden"/>
					<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				</div>
			</form>
		</div>
		<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_prod_combi.php' ); ?>
	</div>
</div>
<?php }

if(isset($_GET['editid']))
{
	echo '<div class="rtwwdpd_prod_table_edit">';
}
else{
	echo '<div class="rtwwdpd_prod_table">';
}
$new_msg_foreach_rule = apply_filters('rtwwdpd_product_rule_changes', 'no');
$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
?>
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="prodct" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Product/Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Condition', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php 
				if($active_dayss == 'yes')
				{ ?>
				<th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php }?>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php 
				$html = apply_filters('rtwwdpd_table_html_product_rule_changes', '');
				echo $html;
				?>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php
		$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');
		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

		global $wp_roles;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$args = array('orderby' => 'display_name');
		$wp_user_query = new WP_User_Query($args);
		$authors = $wp_user_query->get_results();
		$all_users_mail = array();
		if (!empty($authors)) {
			foreach ($authors as $author) {
				$all_users_mail[$author->ID] = $author->data->user_email;
			}
		}

		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { ?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {
					echo '<tr data-val="'.$key.'">';
					
					echo '<td class="rtwrow_no">'.($key+1).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
					echo '<td>'.(isset($value['rtwwdpd_offer_name']) ? esc_html__($value['rtwwdpd_offer_name'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
					if($value['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
					{
						echo '<td>'.esc_html__('All Products in Cart', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
						echo '<td>-----</td>';
					}
					elseif($value['rtwwdpd_rule_on'] == 'rtwwdpd_products')
					{
						echo '<td>'.esc_html__('Selected Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
						if(isset($value['product_id']) && $value['product_id'] != '')
						{
							echo '<td>'.get_the_title( $value["product_id"] ).'</td>';
						}
						else{
							echo '<td>-----</td>';
						}
					}
					elseif($value['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
					{
						echo '<td>'.esc_html__('Multiple Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';

						if(isset($value['multiple_product_ids']) && is_array($value['multiple_product_ids']) && !empty($value['multiple_product_ids']))
						{
							echo '<td>';
							foreach ( $value['multiple_product_ids'] as $pp => $iid) {
								echo get_the_title( $iid ). ', ';
							}
							echo '</td>';
						}
						else{
							echo '<td>-----</td>';
						}
					}

					if($value['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
					{
						if($value['rtwwdpd_condition'] == 'rtwwdpd_and')
						{
							echo '<td>'.esc_html__('And', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
						}else{
							echo '<td>'.esc_html__('Or', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
						}
					}else{
						echo '<td>-----</td>';
					}

					if($value['rtwwdpd_check_for'] == 'rtwwdpd_price')
					{
						echo '<td>'.esc_html__("Price", "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'</td>';
					}
					elseif($value['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
					{
						echo '<td>Quantity</td>';
					}
					else{
						echo '<td>Weight</td>';
					}
					
					echo '<td>'.(isset($value['rtwwdpd_min']) ? esc_html__($value['rtwwdpd_min'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';
					
					echo '<td>'.(isset($value['rtwwdpd_max']) ? esc_html__($value['rtwwdpd_max'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';

					if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>'.esc_html__('Percentage', "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'</td>';
					}
					elseif($value['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>'.esc_html__('Amount', "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'</td>';
					}
					else{
						echo '<td>'.esc_html__('Fixed Price', "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'</td>';
					}
					
					echo '<td>'.(isset($value['rtwwdpd_discount_value']) ? esc_html__($value['rtwwdpd_discount_value'] , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';
					 
					if($active_dayss == 'yes')
					{
						echo '<td>';
						if(isset($value['rtwwwdpd_prod_day']) && is_array($value['rtwwwdpd_prod_day']) && !empty($value['rtwwwdpd_prod_day']))
						{
							if(in_array(1, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Mon, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(2, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Tue, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(3, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Wed, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(4, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Thu, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(5, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Fri, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(6, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Sat, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(7, $value['rtwwwdpd_prod_day']))
							{
								esc_html_e('Sun', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
						}
						echo '</td>';
					}
					
					echo '<td>'.(isset($value['rtwwdpd_max_discount']) ? esc_html__($value['rtwwdpd_max_discount'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai" ) : '').'</td>';
					
					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $val) {
							echo esc_html__($rtwwdpd_roles[$val], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").', ';
						}
					}
					else{
						esc_html_e($rtwwdpd_roles[$val], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai");
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_select_emails']) && is_array($value['rtwwdpd_select_emails']) && !empty($value['rtwwdpd_select_emails']))
					{
						foreach ($value['rtwwdpd_select_emails'] as $val) {
							echo esc_html__($all_users_mail[$val], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").', ';
						}
					}
					else{
						echo '';
					}
					echo '</td>';
					
					echo '<td>'.(isset($value['rtwwdpd_single_from_date']) ? esc_html__($value['rtwwdpd_single_from_date'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai" ) : '' ).'</td>';
					
					echo '<td>'.(isset($value['rtwwdpd_single_to_date']) ? esc_html__($value['rtwwdpd_single_to_date'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';

					echo '<td>'.( isset($value['rtwwdpd_min_orders']) ? esc_html__($value['rtwwdpd_min_orders'], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';

					echo '<td>'.( isset($value['rtwwdpd_min_spend']) ? esc_html__($value['rtwwdpd_min_spend'] , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai") : '').'</td>';

					if(!isset($value['rtwwdpd_exclude_sale']))
					{
						echo '<td>'.esc_html__('No' , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'</td>';
					}
					else
					{
						echo '<td>Yes</td>';
					}
					$html_msg = apply_filters('rtwwdpd_table_offer_text_product_rule', $key);
					if($key != $html_msg)
					{
						echo $html_msg;
					}
					 
					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editpid='.$key ).'"><input type="button" class="rtw_single_prod_edit rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit' , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'" /></a>
					<a href="'.esc_url( $rtwwdpd_absolute_url .'&delprod='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete' , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").'"/></a></td>';
					echo '</tr>';
				}
				?>		
			</tbody>
		<?php } ?>
		<tfoot>
			<tr>
				
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Product/Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Condition', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php 
				if($active_dayss == 'yes')
				{ ?>
				<th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php }?>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php 
				$html = apply_filters('rtwwdpd_table_html_product_rule_changes', '');
				echo $html;
				?>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
?>
<div class="rtwwdpd_prod_offer_message">
	<form  action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
		<span  class="rtwwdpd_shortcodes"><b><?php esc_html_e( 'Offer Message : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b></span>
		<textarea class="rtwwdpd_input" value="" name="rtwwdpd_pro_offer">
			<?php echo trim(get_option( 'rtwwdpd_product_offer_msg','Get [discount_value] off on purchase of [from] to [to] on [product_name]' )); ?>
		</textarea>
		<!-- <input type="text" class="rtwwdpd_input" name="rtwwdpd_pro_offer"> -->
		<input id="subpro_rule" name="subpro_rule" class="rtw-button" type="submit" value="<?php esc_attr_e( 'Save Offer Message', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<span class="rtwwdpd_shortcodes"><b><?php esc_html_e('Use ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?>[discount_value], [from], [to], [product_name], [minimum_spend] <?php esc_html_e('as shortcodes to exchange values', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?></b></span>
	</form>
</div>