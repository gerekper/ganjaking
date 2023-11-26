<?php 
global $post;
global $wp;
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );

if(isset($_GET['delatt']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delatt']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_att_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_attribute' ;
	header('Location: '. $rtwwdpd_new_url );
	die();
}
if(isset($_POST['rtwwdpd_save_pay_rule'])){
	$rtwwdpd_prod = $_POST;

	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_att']);

	$rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
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
		unset( $_REQUEST['edit_att'] );
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}

	update_option('rtwwdpd_att_rule',$rtwwdpd_products_option);
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
	$rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_att_offer_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_att_offer_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_att_offer_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_att_offer_name'],"",-5);
	
	update_option('rtwwdpd_att_rule',$rtwwdpd_products_option);

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
?>

<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="add_single_product" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div> 
	<?php
	if(isset($_GET['edit_att']))
	{	
		$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

		$rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
		$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['edit_att']];
		$key = 'edit_att';
		$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
		$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_attribute');
		?>
		<div class="rtwwdpd_add_single_rule rtwwdpd_form_layout_wrapper rtwwdpd_active">
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
									<input type="hidden" id="edit_att" name="edit_att" value="<?php echo esc_attr($_GET['edit_att']); ?>">
									<table class="rtwwdpd_table_edit">
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input class="rtwwdpd_attr_offer_name" type="text" name="rtwwdpd_att_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_att_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_att_offer_name']) : ''; ?>">
												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Select Attribute Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<?php
												$rtwwdpd_attr_array = array();
												$rtwwdpd_attribute_taxonomies = wc_get_attribute_taxonomies();
												$rtwwdpd_attr_array['select'] = 'Select';
												if ( is_array($rtwwdpd_attribute_taxonomies) && !empty( $rtwwdpd_attribute_taxonomies ) ) {
													foreach ( $rtwwdpd_attribute_taxonomies as $tax ) {
														$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
														$label                   = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
														$rtwwdpd_attr_array[$attribute_taxonomy_name] = $label;
													}
												}
												?>
												<select id="rtwwdpd_attributes" name="rtwwdpd_attributes" class="rtwwdpd_payment_method rtw_class_category">
												<?php
												
												
												if(is_array($rtwwdpd_attr_array) && !empty($rtwwdpd_attr_array))
												{
													
													foreach ($rtwwdpd_attr_array as $attn => $attname) {
													?>
													<option value="<?php echo esc_attr($attn); ?>" <?php selected($rtwwdpd_prev_prod['rtwwdpd_attributes'], $attn) ?>>
														<?php esc_html_e($attname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
													</option>
													<?php
												}}
												?>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select attribute type on which discount should be given.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr id="rtwwdpd_attribute_val">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Attribute Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											
										</td>
										<td>
										<?php
											
											$rtwwdpd_pre_col = isset($rtwwdpd_prev_prod['rtwwdpd_attribute_val']) ? $rtwwdpd_prev_prod['rtwwdpd_attribute_val'] : array();
										
											?>
											<select multiple="multiple" class="rtwwdpd_payment_method rtw_class_category" name="rtwwdpd_attribute_val[]">
											<?php
											if(is_array($rtwwdpd_pre_col) && !empty($rtwwdpd_pre_col))
											{
											foreach ($rtwwdpd_pre_col as $colid => $col) { 
												
												$attr = get_term($col);
											
												?>
												<option value="<?php echo esc_attr($col); ?>" <?php
												selected('aa', 'aa');
												?>>
												<?php
												esc_html_e( $attr->name, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
												?>
												</option>
											<?php }}
											?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Select attribute name on which discount should be given.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
										<select name="rtwwdpd_att_discount_type">
											<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_att_discount_type'], 'rtwwdpd_discount_percentage') ?>>
												<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_att_discount_type'], 'rtwwdpd_flat_discount_amount') ?>>
												<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_att_discount_type'], 'rtwwdpd_fixed_price') ?>>
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
										<input type="number" class="rtwwdpd_attr_dis" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_att_discount_value']) ? $rtwwdpd_prev_prod['rtwwdpd_att_discount_value'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_att_discount_value">
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
										<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
									</td>
									<td>

										<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
											<?php 
											if( isset( $rtwwdpd_prev_prod['product_exe_id'] ) && is_array( $rtwwdpd_prev_prod['product_exe_id'] ) && !empty( $rtwwdpd_prev_prod['product_exe_id'] ) )
											{
												foreach ($rtwwdpd_prev_prod['product_exe_id'] as $key => $value) {
													$product = wc_get_product($value);
													if (is_object($product)) {
														echo '<option value="' . esc_attr($value) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
													}
												}
											}
											?>
										</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude products form this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</label>
									</td>
									<td>
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_att_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_att_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_att_max_discount">
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
												if(is_array($rtwwdpd_selected_role) && !empty($rtwwdpd_selected_role))
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
										<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
									</label>
									</td>
									<td>
										<input type="checkbox" value="yes" name="rtwwdpd_att_exclude_sale" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_att_exclude_sale']) ? $rtwwdpd_prev_prod['rtwwdpd_att_exclude_sale']: "no", 'yes'); ?>/>
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
										<input type="date" class="date_from" name="rtwwdpd_att_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_att_from_date']); ?>" />
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
										<input type="date" class="date_to" name="rtwwdpd_att_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_att_to_date']); ?>"/>
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
		<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_attr_clss rtwwdpd_att_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<input id="submit_attr_rule" name="rtwwdpd_save_pay_rule" type="submit" hidden="hidden"/>
		<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
</div>
<?php }
else{
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
								<input type="hidden" id="edit_att" name="edit_att" value="save">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input class="rtwwdpd_attr_offer_name" type="text" name="rtwwdpd_att_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required">
											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Select Attribute Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<?php
												$rtwwdpd_attr_array = array();
												$rtwwdpd_attribute_taxonomies = wc_get_attribute_taxonomies();

												$attribute_taxonomy_name = array();
												$rtwwdpd_attr_array['select'] = 'Select';
												if ( is_array($rtwwdpd_attribute_taxonomies) && ! empty( $rtwwdpd_attribute_taxonomies ) ) {
													foreach ( $rtwwdpd_attribute_taxonomies as $tax ) {
														$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
														$label                   = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
														$rtwwdpd_attr_array[$attribute_taxonomy_name] = $label;
													}
												}
												
												?>
												<select data-placeholder="Select Attribute" id="rtwwdpd_attributes" name="rtwwdpd_attributes " class="rtwwdpd_payment_method rtw_class_category">
												<?php 
												if(is_array($rtwwdpd_attr_array) && !empty($rtwwdpd_attr_array))
												{
													foreach ($rtwwdpd_attr_array as $attn => $attname) {
													?>
														<option value="<?php echo esc_attr($attn); ?>">
															<?php esc_html_e($attname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
														</option>
													<?php
													}}
													?>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select attribute type on which discount should be given.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr id="rtwwdpd_attribute_val">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Attribute Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
												<input type="hidden" class="rtw_attribute_val" value="">
											</td>
											<td>
												
												<select multiple="multiple" class="rtwwdpd_payment_method rtw_class_category" name="rtwwdpd_attribute_val[]">
													
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select attribute name on which discount should be given.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
										<tr class="rtwwdpd_check_maxvalue">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="" name="rtwwdpd_max">
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
												<select name="rtwwdpd_att_discount_type">
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
												<input class="rtwwdpd_attr_dis" type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_att_discount_value">
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
												<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
											</td>
											<td>
												<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude products form this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input type="number" value="" required="required" min="0" name="rtwwdpd_att_max_discount">
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
												$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); ?>

												<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple="multiple">
													<?php
													if(is_array($rtwwdpd_roles) && !empty($rtwwdpd_roles))
													{
													foreach ($rtwwdpd_roles as $roles => $role) {
														?>
														<option value="<?php echo esc_attr($roles); ?>">
															<?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
														<?php
													}}
													?>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Select user role for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input type="checkbox" value="yes" name="rtwwdpd_att_exclude_sale"/>
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
												<input type="date" class="date_from" name="rtwwdpd_att_from_date" placeholder="YYYY-MM-DD" required="required" />
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
												<input type="date" class="date_from" name="rtwwdpd_att_to_date" placeholder="YYYY-MM-DD" required="required" />
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
					<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_attr_clss rtwwdpd_att_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
					<input id="submit_attr_rule" name="rtwwdpd_save_pay_rule" type="submit" hidden="hidden"/>
					<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				</div>
			</form>
		</div>
<?php } ?>
<div class="rtwwdpd_prod_table">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="attr_tbl" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'On Attribue', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Attribue Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php $rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
		
		$absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

		global $wp_roles;
		global $post;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$rtwwdpd_attr_array = array();
		$rtwwdpd_attribute_taxonomies = wc_get_attribute_taxonomies();
		$rtwwdpd_attr_array['select'] = 'Select';
		if ( is_array( $rtwwdpd_attribute_taxonomies ) && ! empty( $rtwwdpd_attribute_taxonomies ) ) {
			foreach ( $rtwwdpd_attribute_taxonomies as $tax ) {
				$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
				$label                   = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
				$rtwwdpd_attr_array[$attribute_taxonomy_name] = $label;
			}
		}

		if( is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option) ){	?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {

					echo '<tr data-val="'.$key.'">';

					echo '<td class="rtwrow_no">'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'. esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';

					echo '<td>'.(isset($value['rtwwdpd_att_offer_name']) ? esc_html($value['rtwwdpd_att_offer_name'] ) : '').'</td>';

					echo '<td>'.(isset($rtwwdpd_attr_array[$value['rtwwdpd_attributes']]) ? esc_html__($rtwwdpd_attr_array[$value['rtwwdpd_attributes']], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>';
					if( isset($value['rtwwdpd_attribute_val']) && is_array($value['rtwwdpd_attribute_val']) && !empty($value['rtwwdpd_attribute_val']))
					{
						foreach ($value['rtwwdpd_attribute_val'] as $val) {
							$obj = get_term($val);
							echo esc_html__(($obj->name), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';

					if($value['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_att_discount_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					else{
						echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}

					echo '<td>'.(isset($value['rtwwdpd_att_discount_value']) ? esc_html($value['rtwwdpd_att_discount_value'] ) : '').'</td>';

					echo '<td>';
					if(isset($value['product_exe_id']) && is_array($value['product_exe_id']) && !empty($value['product_exe_id']))
					{
						foreach ($value['product_exe_id'] as $val)
						{
							echo '<span id="'.esc_attr($val).'">';
							echo esc_html__(get_the_title( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai')).'</span><br>';
						}
					}
					else{
						echo '';
					}
					echo '</td>';

					echo '<td>'.(isset($value['rtwwdpd_att_max_discount']) ? esc_html($value['rtwwdpd_att_max_discount'] ) : '').'</td>';
					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $val) {
							echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					else{
						esc_html_e('All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					echo '</td>';

					echo '<td>'.(isset($value['rtwwdpd_att_from_date']) ? esc_html($value['rtwwdpd_att_from_date'] ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_att_to_date']) ? esc_html($value['rtwwdpd_att_to_date'] ) : '').'</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_att_exclude_sale']))
					{
						echo esc_html($value['rtwwdpd_att_exclude_sale']);
					}else{
						esc_html_e('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}						
					echo '</td>';

					echo '<td><a href="'.esc_url( $absolute_url .'&edit_att='.$key ).'"><input class="rtwwdpd_edit_dt_row" type="button" value="'.esc_html__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
					<a href="'.esc_url( $absolute_url .'&delatt='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_html__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
				<th><?php esc_html_e( 'On Attribue', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Attribue Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
