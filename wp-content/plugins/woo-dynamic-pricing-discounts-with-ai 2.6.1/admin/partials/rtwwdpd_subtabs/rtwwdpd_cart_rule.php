<?php
$rtwwdpd_payment_gateway_obj = new WC_Payment_Gateways();
$rtwwdpd_available_methods = $rtwwdpd_payment_gateway_obj->get_available_payment_gateways();
$rtwwdpd_get_title = function($obj){
	return $obj->get_method_title();
};
$rtwwdpd_available_methods = array_map($rtwwdpd_get_title,$rtwwdpd_available_methods);

$rtwwdpd_available_methods = array_merge( array( 0 => esc_html__( 'Any', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )), $rtwwdpd_available_methods); 

$rtwwdpd_shipping_obj = new WC_Shipping();
$rtwwdpd_available_shipping_methods = $rtwwdpd_shipping_obj->get_shipping_methods();
$rtwwdpd_get_title = function($obj){
	return $obj->get_method_title();
};
$rtwwdpd_available_shipping_methods = array_map($rtwwdpd_get_title,$rtwwdpd_available_shipping_methods);

$rtwwdpd_available_shipping_methods = array_merge( array( 0 => esc_html__( 'Any', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )), $rtwwdpd_available_shipping_methods); 
$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');

global $wp; 
if(isset($_GET['delcart']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delcart']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_cart_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cart_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_cart_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_chk_cart']);
	$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
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
		unset($_REQUEST['editcart']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_cart_rule',$rtwwdpd_products_option);

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
	$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_cart_offer'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_cart_offer'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_cart_offer'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_cart_offer'],"",-5);
	
	update_option('rtwwdpd_cart_rule',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
if(isset($_GET['editcart']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editcart']];
	$key = 'editcart';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);

	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cart_rules');

	?>
	<div class="rtwwdpd_right">
		<div class="rtwwdpd_add_buttons">
			<input class="rtw-button rtwwdpd_single_prod_rule" id="rtwwdpd_single_cart_rule" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>

		<div class="rtwwdpd_add_single_rule rtwwdpd_active rtwwdpd_form_layout_wrapper">
			<form action="<?php echo esc_url($rtwwdpd_new_url); ?>" method="POST" accept-charset="utf-8">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_rule_tab active">
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
								<li class="rtwwdpd_adv">
									<a class="rtwwdpd_link" id="rtwwdpd_adv">
										<span><?php esc_html_e('Advance Restriction','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab">
									<input type="hidden" id="edit_chk_cart" value="<?php echo esc_attr($_GET['editcart']);?>" name="edit_chk_cart">	
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_cart_offer" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_cart_offer']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_cart_offer']) : ''; ?>">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
													<option value="rtwwdpd_subtotal" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_subtotal');?>><?php esc_html_e( 'Cart Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_cart_count" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_cart_count');?>><?php esc_html_e( 'Total Cart Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
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
												<input type="number"  class="rtwwdpd_check_minvalue"  value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min']) : '' ; ?>" min="0" name="rtwwdpd_min">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_check_maxvalue">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max']) : '' ; ?>" min="0" name="rtwwdpd_max">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab">
									<table class="rtwwdpd_table_edit">
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
												<label class="rtwwdpd_label"><?php esc_html_e('Select Required Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select class="wc-product-search rtwwdpd_prod_class" name="rtwwdpd_select_product[]" data-action="woocommerce_json_search_products_and_variations" multiple="multiple" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
												<?php
												if(isset($rtwwdpd_prev_prod['rtwwdpd_select_product']))
												{
													$products = $rtwwdpd_prev_prod['rtwwdpd_select_product'];
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
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select product for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>

										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input required type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_max_discount">
												<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
											<input type="date" name="rtwwdpd_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_from_date']); ?>" />
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
											<input type="date" name="rtwwdpd_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_to_date']); ?>"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
								</table>
							</div>
							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_adv_tab">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Allowed Payment Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select required id="allowed_payment_method" class="rtwwdpd_payment_method" name="allowed_payment_method[]" >
											<?php
											if(is_array($rtwwdpd_available_methods) && !empty($rtwwdpd_available_methods))
											{
												foreach ($rtwwdpd_available_methods as $method => $pay) {
													?>
													<option value="<?php echo esc_attr($method); ?>">
														<?php esc_html_e( $pay, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<?php
												}
											}
											?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Select Payment Method.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select required id="allowed_shipping_method" class="rtwwdpd_ship_method" name="allowed_shipping_method[]" >
											<?php
											if(is_array($rtwwdpd_available_shipping_methods) && !empty($rtwwdpd_available_shipping_methods))
											{
												foreach ($rtwwdpd_available_shipping_methods as $method => $ship) {
													?>
													<option value="<?php echo esc_attr($method); ?>">
														<?php esc_html_e( $ship, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<?php
												}
											}
											?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Select Shipping Method.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
			<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_crt_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_cart_rule" name="rtwwdpd_cart_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
</div>
<?php
}
else{
	
	?>
	<div class="rtwwdpd_right">
		<div class="rtwwdpd_add_buttons">
			<input class="rtw-button rtwwdpd_single_prod_rule" id="rtwwdpd_single_cart_rule" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>

		<div class="rtwwdpd_add_single_rule rtwwdpd_form_layout_wrapper">
			<form action="" method="POST" accept-charset="utf-8">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_rule_tab active">
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
								<li class="rtwwdpd_adv">
									<a class="rtwwdpd_link" id="rtwwdpd_adv">
										<span><?php esc_html_e('Advance Restriction','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab">
									<input type="hidden" id="edit_chk_cart" value="save" name="edit_chk_cart">	
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_cart_offer" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
													<option value="rtwwdpd_subtotal"><?php esc_html_e( 'Cart Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_cart_count"><?php esc_html_e( 'Total Cart Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
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
												<input type="number"  class="rtwwdpd_check_minvalue"  value="" min="0" name="rtwwdpd_min">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_check_maxvalue">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="" min="0" name="rtwwdpd_max">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab">
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
												<?php
												global $wp_roles;
												$rtwwdpd_roles 	= $wp_roles->get_names();
												$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
												$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); ?>
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
												<label class="rtwwdpd_label"><?php esc_html_e('Select Required Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select class="wc-product-search rtwwdpd_prod_class" name="rtwwdpd_select_product[]" data-action="woocommerce_json_search_products_and_variations" multiple="multiple" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select product for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>

										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input type="number" value="" min="0" name="rtwwdpd_max_discount">
												<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
												<input type="date" name="rtwwdpd_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
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
												<input type="date" name="rtwwdpd_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
									</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_adv_tab">
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Allowed Payment Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select required id="allowed_payment_method" class="rtwwdpd_payment_method" name="allowed_payment_method[]" >
												<?php
												if(is_array($rtwwdpd_available_methods) && !empty($rtwwdpd_available_methods))
												{
													foreach ($rtwwdpd_available_methods as $method => $pay) {
														?>
														<option value="<?php echo esc_attr($method); ?>">
															<?php esc_html_e( $pay, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
														<?php
													}
												}
												?>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select Payment Method.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Allowed Shipping Methods', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select required id="allowed_shipping_method" class="rtwwdpd_ship_method" name="allowed_shipping_method[]" >
												<?php
												if(is_array($rtwwdpd_available_shipping_methods) && !empty($rtwwdpd_available_shipping_methods))
												{
													foreach ($rtwwdpd_available_shipping_methods as $method => $ship) {
														?>
														<option value="<?php echo esc_attr($method); ?>">
															<?php esc_html_e( $ship, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
														<?php
													}
												}
												?>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select Shipping Method.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
					<input class="rtw-button rtwwdpd_save_rule rtwwdpd_crt_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
					<input id="submit_cart_rule" name="rtwwdpd_cart_rule" type="submit" hidden="hidden"/>
					<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				</div>
			</form>
		</div>
	</div>
<?php } 
?><div class="rtwwdpd_cart_table">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="cart_tbl" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Required Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Payment Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Shipping Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php
		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
		
		global $wp_roles;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
		$products = array();

		if ($rtwwdpd_categories) {
			foreach ($rtwwdpd_categories as $cat) {
				$products[$cat->term_id] = $cat->name;
			}
		}
		
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

					echo '<td class="rtwrow_no">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';

					echo '<td>'.(isset($value['rtwwdpd_cart_offer']) ? esc_html__($value['rtwwdpd_cart_offer'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					if($value['rtwwdpd_check_for'] == 'rtwwdpd_price')
					{
						echo '<td>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
					{
						echo '<td>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_check_for'] =='rtwwdpd_subtotal')
					{
						echo '<td>'.esc_html__('Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_check_for'] =='rtwwdpd_cart_count')
					{
						echo '<td>'.esc_html__('Cart Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					else{
						echo '<td>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}

					echo '<td>'.(isset($value['rtwwdpd_min']) ? esc_html__($value['rtwwdpd_min'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_max']) ? esc_html__($value['rtwwdpd_max'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					else{
						echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}

					echo '<td>'.(isset($value['rtwwdpd_discount_value']) ? esc_html__($value['rtwwdpd_discount_value'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_max_discount']) ? esc_html__($value['rtwwdpd_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					//-------------Anoop-----------------//
					echo '<td>';
					if(isset($value['rtwwdpd_select_product']) && is_array($value['rtwwdpd_select_product']) && !empty($value['rtwwdpd_select_product']))
					{
						foreach ($value['rtwwdpd_select_product'] as $keys => $val)
						{
							echo esc_html__(get_the_title($val), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					else
					{
						echo esc_html__('Not Selected', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
					}
					echo "</td>";
					//--------------------------------//
					echo '<td>';
						echo esc_html__($rtwwdpd_available_methods[$value['allowed_payment_method'][0]], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
					echo '</td>';
					echo '<td>';
						esc_html_e($rtwwdpd_available_shipping_methods[$value['allowed_shipping_method'][0]], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $keys => $val) {
							echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
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

					echo '<td>'.(isset($value['rtwwdpd_from_date']) ? esc_html__($value['rtwwdpd_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_to_date']) ? esc_html__($value['rtwwdpd_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editcart='.$key ).'"><input type="button" class="rtw_edit_cart rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" />
					</a><a href="'.esc_url( $rtwwdpd_absolute_url .'&delcart='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
					echo '</tr>';
				}
				?>	
			</tbody>
		<?php } ?>
		<tfoot>
			<tr>

				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Product/ Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Required Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Payment Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Shipping Method', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php }
else{
	include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
}
