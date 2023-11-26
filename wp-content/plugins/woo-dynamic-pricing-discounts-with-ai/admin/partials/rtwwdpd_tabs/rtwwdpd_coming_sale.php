<?php
global $wp; 
if(isset($_GET['rtwdsale']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_coming_sale');
	$rtwwdpd_row_no = sanitize_post($_GET['rtwdsale']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_coming_sale',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_coming_sale';
	header('Location: '.$rtwwdpd_new_url);
    die();
}
$sabcd = 'verification_done';
if(isset($_POST['rtwwdpd_cmng_sale'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['editsale']);
	$rtwwdpd_products_option = get_option('rtwwdpd_coming_sale');
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
		unset($_REQUEST['editsale']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_coming_sale',$rtwwdpd_products_option);
?><div class="notice notice-success is-dismissible">
	<p><strong><?php esc_html_e('Settings saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
	</button>
	</div>
<?php
}

if(isset($_POST['rtwwdpd_copy_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_coming_sale');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_sale_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_sale_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_sale_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_sale_name'],"",-5);
	
	update_option('rtwwdpd_coming_sale',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
if(isset($_GET['editsale']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	$rtwwdpd_products_option = get_option('rtwwdpd_coming_sale');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editsale']];
	$key = 'editsale';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_coming_sale');
	?>
	<div class="rtwwdpd_right">
		<div class="rtwwdpd_add_buttons">
			<h1 class="rtwcenter"><b><?php esc_attr_e('Create Upcoming Sale','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></h1>
		</div>
		<div class="rtwwdpd_form_layout_wrapper">
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

							<div class="panel woocommerce_options_panel rtwwdpd_woocommerce_pannel_option">
								<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">

									<input type="hidden" id="editsale" name="editsale" value="<?php echo esc_attr($_GET['editsale']); ?>">
									<table class='rtw_specific_tbl'>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_sale_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_sale_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_sale_name']) : ''; ?>">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Sale of', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwwdpd_sale_of" name="rtwwdpd_sale_of">
													<option value="rtwwdpd_select" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_of'], 'rtwwdpd_select') ?>>
														<?php esc_html_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_product" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_of'], 'rtwwdpd_product') ?>>
														<?php esc_html_e( 'Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_category" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_of'], 'rtwwdpd_category') ?>>
														<?php esc_html_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Offer should be applied on the selected products or category.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_sale_check_for" id="rtwwdpd_sale_check_for">
													<option value="rtwwdpd_quantity" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_check_for'], 'rtwwdpd_quantity');?>><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_check_for'], 'rtwwdpd_price');?>><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
													<option value="rtwwdpd_weight" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_check_for'], 'rtwwdpd_weight');?>><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
									</table>
									<?php
									if(isset($rtwwdpd_prev_prod['product_id']) && $rtwwdpd_prev_prod['product_id'] != '')
									{ ?>
									<div class="rtwwdpd_active">
									<h3 class="rtw_tbltitle"><?php esc_attr_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
									<table id="rtw_for_product">
											<thead>
												<tr>
													<th class="rtwtable_header" ><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header" ><div class="rtw_sale_quant"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></div></th>
													<th class="rtwtable_header" ><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												</tr>
											</thead>
											<tbody id="rtw_product_body">
												<?php
												$rtwwdpd_pro_ids =	isset($rtwwdpd_prev_prod['product_id']) ? $rtwwdpd_prev_prod['product_id'] : array();
													foreach ($rtwwdpd_pro_ids as $key => $val) {
													?>
												<tr>
													<td id="td_row_no" class=""><?php echo esc_html($key+1); ?></td>
													<td id="td_product_name">
														<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class"  data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
														<?php
														$product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 

														$product = wc_get_product($val);
														if (is_object($product)) {
															echo '<option value="' . esc_attr($val) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
															}
														?>
														</select>
													</td>
													<td id="td_quant">
														<input type="number" min="0" name="quant_pro[]" value="<?php echo isset($rtwwdpd_prev_prod['quant_pro'][$key]) ? $rtwwdpd_prev_prod['quant_pro'][$key] : '' ?>"  />
													</td>
													<td id="td_remove">
														<a class="button insert remove" name="deletebtn" ><?php esc_attr_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
													</td>
												</tr>
												<?php } ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan=3>
														<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_product" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<?php }
									if(isset($rtwwdpd_prev_prod['category_id']))
									{ ?>
										<div class="rtwwdpd_active">
											<table id="rtw_for_category" class="rtwwdpd_active">
												<caption><b><?php esc_attr_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></caption>
												<thead>
													<tr>
													<th class="rtwtable_header rtwten"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header rtwforty"><?php esc_attr_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header rtwtwenty"><div class="rtw_sale_quant"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></div></th>
													<th class="rtwtable_header rtwthirty"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													</tr>
												</thead>
												<tbody id="rtw_category_body">
							
													<?php
													foreach ($rtwwdpd_prev_prod['category_id'] as $key => $val) {
													?>	
													<tr id="rtw_tbltr">
														<td id="td_row_no"><?php echo esc_html($key+1); ?></td>
														<td class="td_product_name">
															<select name="category_id[]" id="category_id<?php echo esc_attr($key);?>" class="wc-enhanced-select rtwwdpd_prod_tbl_class" multiple data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
															<?php
															$rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
															if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
															$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

															if ($rtwwdpd_categories) {
																foreach ($rtwwdpd_categories as $cat) {
																echo '<option value="' . esc_attr($cat->term_id) . '"' . selected($cat->term_id,$val, false) . '>' . esc_html($cat->name) . '</option>';
																}
																}
																?>
															</select>
														</td>
														<td class="td_quant">
															<input type="number" min="0" class="rtwtd_quant"name="quant_cat[]" value="<?php echo esc_attr($rtwwdpd_prev_prod['quant_cat'][$key]); ?>"  />
														</td>
														<td id="td_remove">
															<a class="button insert remove_cat" name="deletebtn" ><?php esc_html_e('Remove','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
														</td>
													</tr>
												<?php } ?>
												</tbody>
												<tfoot>
											<tr>
												<td colspan=3>
													<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_category" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
											</tr>
										</tfoot>
								</table>
								</div>
								<?php } ?>
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_sale_discount_type">
												<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_discount_type'], 'rtwwdpd_discount_percentage') ?>>
													<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_discount_type'], 'rtwwdpd_flat_discount_amount') ?>>
													<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_sale_discount_type'], 'rtwwdpd_fixed_price') ?>>
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
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_sale_discount_value']) ? $rtwwdpd_prev_prod['rtwwdpd_sale_discount_value'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_sale_discount_value">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
								</table>
							</div>
							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</label>
									</td>
									<td>
										<input required type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_sale_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_sale_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_sale_max_discount">
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
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_sale_min_orders']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_sale_min_orders']) : '' ; ?>" min="0" name="rtwwdpd_sale_min_orders">
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
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_sale_min_spend']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_sale_min_spend']) : '' ; ?>" min="0" name="rtwwdpd_sale_min_spend">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab_combi">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</label>
									</td>
									<td>
										<input type="date" name="rtwwdpd_sale_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_sale_from_date']); ?>" />
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
										<input type="date" name="rtwwdpd_sale_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_sale_to_date']); ?>"/>
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

			<div class="rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_sale_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_come_rule" name="rtwwdpd_cmng_sale" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
</div>
<?php }else{ ?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<h1 class="rtwcenter"><b><?php esc_attr_e('Create Upcoming Sale','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></h1>
	</div>
	<div class="rtwwdpd_form_layout_wrapper">
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

						<div class="panel woocommerce_options_panel rtwwdpd_woocommerce_pannel_option" >
							<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
								<input type="hidden" id="editsale" name="editsale" value="save">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_sale_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Sale of', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select id="rtwwdpd_sale_of" name="rtwwdpd_sale_of">
												<option value="rtwwdpd_select">
													<?php esc_html_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_product">
													<?php esc_html_e( 'Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_category">
													<?php esc_html_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Offer should be applied on the selected products or category.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_sale_check_for" id="rtwwdpd_sale_check_for">
												<option value="rtwwdpd_quantity"><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option value="rtwwdpd_price"><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option value="rtwwdpd_weight"><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
								<div>
									<h3 class="rtw_tbltitle"><?php esc_attr_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
									<table id="rtw_for_product">
										<thead>
											<tr>
												<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header">
													<div class="rtw_sale_quant"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></div></th>
													<th class="rtwtable_header"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												</tr>
											</thead>

											<tbody id="rtw_product_body">
												<tr>
													<td id="td_row_no">1</td>
													<td id="td_product_name">
														<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
															<?php
															$product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
	                                         // selected product ids
															foreach ($product_ids as $product_id) {
																$product = wc_get_product($product_id);
																if (is_object($product)) {
																	echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
																}
															}
															?>
														</select>
													</td>
													<td id="td_quant">
														<input type="number" min="0" name="quant_pro[]" value=""  />
													</td>
													<td id="td_remove">
														<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td colspan=3>
														<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_product" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<div>
										<table id="rtw_for_category">
											<caption><b><?php esc_attr_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></caption>
											<thead>
												<tr>
													<th class="rtwtable_header rtwten"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header rtwforty"><?php esc_attr_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
													<th class="rtwtable_header rtwtwenty"><div class="rtw_sale_quant"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></div></th>
													<th class="rtwtable_header rtwthirty"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												</tr>
											</thead>
											<tbody id="rtw_category_body">
												<tr id="rtw_tbltr">
													<td id="td_row_no">1</td>
													<td class="td_product_name">
														<select name="category_id[]" id="category_id" class="wc-enhanced-select rtwwdpd_prod_tbl_class" multiple data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
															<?php
															$rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array(); 
															if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
															$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

															if ($rtwwdpd_categories) {
																foreach ($rtwwdpd_categories as $cat) {
																	echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_category_ids), true, true) . '>' . esc_html($cat->name) . '</option>';
																}
															}
															?>
														</select>
													</td>
													<td class="td_quant">
														<input type="number" min="0" class="rtwtd_quant"name="quant_cat[]" value=""  />
													</td>
													<td id="td_remove">
														<?php esc_html_e('Minimum One Category Required.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td colspan=3>
														<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_category" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<table class="rtwwdpd_table_edit">
					            		<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select name="rtwwdpd_sale_discount_type">
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
												<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_sale_discount_value">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
					            	</table>
								</div>
								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
									<table class='rtw_specific_tbl'>
										<tr>
											<td>
						            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
					            		</td>
					            		<td>
					            			<input required type="number" value="" min="0" name="rtwwdpd_sale_max_discount">
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
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
											</td>
											<td>
												<input type="number" value="" min="0" name="rtwwdpd_sale_min_orders">
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
												<input type="number" value="" min="0" name="rtwwdpd_sale_min_spend">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
									</table>
								</div>

								<div class="options_group rtwwdpd_inactive" id="rtwwdpd_time_tab_combi">
									<table class='rtw_specific_tbl'>
			        					<tr>
			        						<td>
					           				<label class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
						            		</label>
						            	</td>
						            	<td>
					           				<input type="date" name="rtwwdpd_sale_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
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
					           				<input type="date" name="rtwwdpd_sale_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
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
			</div>
		<div class="rtwwdpd_btn_save_n_cancel">
			<input class="rtw-button rtwwdpd_sale_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			<input id="submit_come_rule" name="rtwwdpd_cmng_sale" type="submit" hidden="hidden"/>
			<input id="rtwwdpd_clr_dta_cancel" class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>
	</form>
</div>	<?php } ?>
<div class="">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
		<thead>
			<tr>
				
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Sale Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Applied On.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Products/Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php $rtwwdpd_products_option = get_option('rtwwdpd_coming_sale');

		global $wp_roles;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		$products = array();
		if(is_array($cat) && !empty($cat))
		{
			foreach ($cat as $value) {
				$products[$value->term_id] = $value->name;
			}
		}
		
		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)){	?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {

					echo '<tr>';
					echo '<td>'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_name'] ).'</td>';
					echo '<td>';
					if(isset($value['rtwwdpd_sale_of']) && $value['rtwwdpd_sale_of'] == 'rtwwdpd_product')
					{
						echo 'Products';
					}
					elseif (isset($value['rtwwdpd_sale_of']) && $value['rtwwdpd_sale_of'] == 'rtwwdpd_category') {
						echo 'Category';
					}
					else{
						echo 'Products';
					}
					echo'</td>';
					echo'<td>';
					if(isset($value['product_id']) && !empty($value['product_id']))
					{
						foreach ($value['product_id'] as $val) {
							echo '<span id="'.$val.'">';
							echo get_the_title( $val ).'</span><br>';
						}
						echo '</td>';
					}
					if(isset($value['quant_pro']) && !empty($value['quant_pro']))
					{
						echo '<td>';

						foreach ($value['quant_pro'] as $val) {
							echo esc_html($val). '<br>';
						}
						echo'</td>';
					}
					elseif (isset($value['category_id']) && !empty($value['category_id'])) {
						foreach ($value['category_id'] as $val) {
							echo '<span id="'.$val.'">';
							echo esc_html($products[$val]).'</span><br>';
						}
						echo '</td><td>';
						foreach ($value['quant_cat'] as $val) {
							echo esc_html($val). '<br>';
						}
						echo'</td>';
					}
					else {
						echo '<td></td>';
					}

					if($value['rtwwdpd_sale_discount_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>Percentage</td>';
					}
					elseif($value['rtwwdpd_sale_discount_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>Amount</td>';
					}
					else{
						echo '<td>Fixed Price</td>';
					}

					echo '<td>'.esc_html($value['rtwwdpd_sale_discount_value'] ).'</td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_max_discount'] ).'</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $val) {
							echo esc_html($rtwwdpd_roles[$val]).'<br>';
						}
					}else{
						echo 'All';
					}
					echo '</td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_from_date'] ).'</td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_to_date'] ).'</td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_min_orders'] ).'</td>';

					echo '<td>'.esc_html($value['rtwwdpd_sale_min_spend'] ).'</td>';

					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editsale='.$key ).'"><input type="button" class="rtw_combi_prod_edit rtwwdpd_edit_dt_row" name="rtw_combi_prod_edit"  value="Edit" /></a>
					<a href="'.esc_url( $rtwwdpd_absolute_url .'&rtwdsale='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="Delete"/></a></td>';
					echo '</tr>';
				}
				?>		
			</tbody>
		<?php } ?>
		<tfoot>
			<tr>
				
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Sale Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Applied On.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Products/Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}

