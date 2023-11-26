<?php 
global $wp;
if(isset($_GET['delplus']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_plus_member');
	$rtwwdpd_row_no = sanitize_post($_GET['delplus']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_plus_member',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_plus_member';
	header('Location: '.$rtwwdpd_new_url);
    die();
}
$sabcd = 'verification_done';
if(isset($_POST['rtwwdpd_plus_mem'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_plus']);

	$rtwwdpd_products_option = get_option('rtwwdpd_plus_member');
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
		unset($_REQUEST['editplus']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_plus_member',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
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
	$rtwwdpd_products_option = get_option('rtwwdpd_plus_member');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_plus_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_plus_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_plus_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_plus_name'],"",-5);
	
	update_option('rtwwdpd_plus_member',$rtwwdpd_products_option);

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
if(isset($_GET['editplus']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	$rtwwdpd_bogo = get_option('rtwwdpd_plus_member');
	$rtwwdpd_prev_prod = $rtwwdpd_bogo[$_GET['editplus']];
	$key = 'editplus';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_plus_member');
?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_combi_prod_rule" id="rtwwdpd_plus_rule" type="button" name="rtwwdpd_plus_mem_rule" value="<?php esc_attr_e( 'Plus Member Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />

		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Discount Rule for Plus Member', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
	<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_plus_member_rule.php' ); ?>
	<div class="rtwwdpd_add_single_rule rtwwdpd_active rtwwdpd_form_layout_wrapper">
		<form action="<?php echo esc_url($rtwwdpd_new_url); ?>" method="POST" accept-charset="utf-8">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_rule_tab active">
								<a class="rtwwdpd_link" id="rtwproduct_rule_combi">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab">
								<a class="rtwwdpd_link" id="rtwproduct_restrict_combi">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab">
								<a class="rtwwdpd_link" id="rtwproduct_validity_combi">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>
						<div class="panel woocommerce_options_panel" >
							<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
								<input type="hidden" name="edit_plus" id="edit_plus" value="<?php echo esc_attr($_GET['editplus']); ?>">
									<table class='rtw_specific_tbl'>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_plus_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_plus_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_plus_name']) : ''; ?>">

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
												<select id="rtwwdpd_rule_for_plus" name="rtwwdpd_rule_for_plus">
													<option value="rtw_select" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'], 'rtw_select') ?>>
														<?php esc_html_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_product" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'], 'rtwwdpd_product') ?>>
														<?php esc_html_e( 'Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_category" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'], 'rtwwdpd_category') ?>>
														<?php esc_html_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Offer should be applied on the selected products or category.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtw_if_prod">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Choose Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwproducts" name="product_ids[]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" multiple="multiple"  >
			                					<?php
												$selected_prods = isset($rtwwdpd_prev_prod['product_ids'])?$rtwwdpd_prev_prod['product_ids']: array();

												if(isset($rtwwdpd_prev_prod['product_ids']) && is_array($rtwwdpd_prev_prod['product_ids']) && !empty($rtwwdpd_prev_prod['product_ids']))
												{
													foreach($rtwwdpd_prev_prod['product_ids'] as $prod => $id)
													{
														$product = wc_get_product($id);
														if (is_object($product)) {
															echo '<option value="' . esc_attr($id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
														}
													}
												}
			                					?>
		                					</select>
											</td>
							        	</tr>
							        	<tr class="rtw_if_cat">
							        		<td>
							        			<label class="rtwwdpd_label"><?php esc_html_e('Choose Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
							        		</td>
											<td>
												<select name="category_ids[]" id="category_ids" class="wc-enhanced-select" multiple data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
											<?php
											$selected_categories = isset($rtwwdpd_prev_prod['category_ids'])? $rtwwdpd_prev_prod['category_ids'] : array();

											$category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array(); 
											if(!is_array($category_ids)) $category_ids=array($category_ids);
											$categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

											if ($categories) {
												foreach ($categories as $cat) {


													echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$selected_categories), true, true) . '>' . esc_html($cat->name) . '</option>';
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
												<select name="rtwwdpd_dsnt_type">
													<option value="rtwwdpd_dis_percent" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_dis_percent') ?>>
														<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_flat_dis_amt" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_flat_dis_amt') ?>>
														<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtwwdpd_fxd_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_fxd_price') ?>>
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
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_dscnt_val']) ? $rtwwdpd_prev_prod['rtwwdpd_dscnt_val'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_val">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwwdpd_rule_on_plus" name="rtwwdpd_rule_on">
													<option value="rtw_amt" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_on'], 'rtw_amt') ?>>
														<?php esc_html_e( 'Minimum Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtw_quant" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_on'], 'rtw_quant') ?>>
														<?php esc_html_e( 'Minimum Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<option value="rtw_both" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_on'], 'rtw_both') ?>>
														<?php esc_html_e( 'Both', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount/quantity of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtw_min_price">
							        		<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_purchase_of']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_purchase_of']) : '' ; ?>" min="1" name="rtwwdpd_min_purchase_of">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
							        	</tr>
										<tr class="rtw_min_price">
							        		<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Maximum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_purchase_of']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_purchase_of']) : '' ; ?>" min="1" name="rtwwdpd_max_purchase_of">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atmost this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
							        	</tr>
							        	<tr class="rtw_min_quant">
							         		<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_purchase_quant']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_purchase_quant']) : '' ; ?>" min="1" name="rtwwdpd_min_purchase_quant">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
							            	</td>
							        	</tr>
										<tr class="rtw_min_quant">
							         		<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Maximum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_purchase_quant']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_purchase_quant']) : '' ; ?>" min="1" name="rtwwdpd_max_purchase_quant">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atmost this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
					            			<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_max_discount">
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

												$rtwwdpd_selected_role =  $rtwwdpd_prev_prod['rtwwdpd_select_roles'];
												 ?>
												<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
					            			<select class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple>
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
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_combi_exclude_sale" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_combi_exclude_sale']) ? $rtwwdpd_prev_prod['rtwwdpd_combi_exclude_sale'] : '', 'yes'); ?>/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
							</div>
						</div>
					</div>
				</div>
				<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
					<input class="rtw-button rtwwdpd_save_rule rtwwdpd_plusm_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
					<input id="submit_plusm_rule" name="rtwwdpd_plus_mem" type="submit" hidden="hidden"/>
					<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				</div>
			</form>
		</div>
<?php }
else{ ?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_combi_prod_rule" id="rtwwdpd_plus_rule" type="button" name="rtwwdpd_plus_mem_rule" value="<?php esc_attr_e( 'Plus Member Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />

		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Discount Rule for Plus Member', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
	<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_tabs/rtwwdpd_plus_member_rule.php' ); ?>
	<div class="rtwwdpd_add_single_rule rtwwdpd_form_layout_wrapper">
		<form action="" method="POST" accept-charset="utf-8">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_rule_tab active">
								<a class="rtwwdpd_link" id="rtwproduct_rule_combi">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab">
								<a class="rtwwdpd_link" id="rtwproduct_restrict_combi">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab">
								<a class="rtwwdpd_link" id="rtwproduct_validity_combi">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
								<input type="hidden" name="edit_plus" id="edit_plus" value="save">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_plus_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

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
											<select id="rtwwdpd_rule_for_plus" name="rtwwdpd_rule_for_plus">
												<option value="rtw_select">
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
									<tr class="rtw_if_prod">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Choose Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select id="rtwproducts" name="product_ids[]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" multiple="multiple"  >
	                					</select>
										</td>
						        	</tr>
						        	<tr class="rtw_if_cat">
						        		<td>
						        			<label class="rtwwdpd_label"><?php esc_html_e('Choose Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
						        		</td>
										<td>
											<select name="category_ids[]" id="category_ids" class="wc-enhanced-select" multiple data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                 <?php
                                 $category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array(); 
                                 if(!is_array($category_ids)) $category_ids=array($category_ids);
                                 $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

                                 if ($categories) {
                                    foreach ($categories as $cat) {
                                       echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
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
											<select name="rtwwdpd_dsnt_type">
												<option value="rtwwdpd_dis_percent">
													<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_flat_dis_amt">
													<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_fxd_price">
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
											<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_val">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select id="rtwwdpd_rule_on_plus" name="rtwwdpd_rule_on">
												<option value="rtw_amt">
													<?php esc_html_e( 'Minimum Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtw_quant">
													<?php esc_html_e( 'Minimum Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtw_both">
													<?php esc_html_e( 'Both', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount/quantity of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr class="rtw_min_price">
						        		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_min_purchase_of">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
						        	</tr>
									<tr class="rtw_min_price">
						        		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Maximum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_max_purchase_of">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atmost this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
						        	</tr>
						        	<tr class="rtw_min_quant">
						         		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_min_purchase_quant">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
						            	</td>
						        	</tr>
									<tr class="rtw_min_quant">
						         		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Maximum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_max_purchase_quant">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atmost this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				            			<input type="number" value="" min="0" name="rtwwdpd_max_discount">
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
				            			<select class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple>
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
				            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            			</label>
				            		</td>
				            		<td>
				            			<input type="checkbox" value="yes" name="rtwwdpd_combi_exclude_sale"/>
										<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
						</div>
					</div>
				</div>
			</div>
			<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_plusm_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_plusm_rule" name="rtwwdpd_plus_mem" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
</div>
<?php } 
$rtwwdpd_enable = get_option('rtwwdpd_plus_enable');
?>	
<div class="rtwwdpd_enable_rule">
	<div>
		<b><?php esc_html_e( 'Rule Permission : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
		</b>
		<select name="rtw_enable_plus" class="rtw_enable_plus">
			<option value="select" <?php selected( $rtwwdpd_enable, 'select'); ?>><?php esc_attr_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
			<option value="enable" <?php selected( $rtwwdpd_enable, 'enable'); ?>><?php esc_attr_e( 'Enable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
			<option value="disable" <?php selected( $rtwwdpd_enable, 'disable'); ?>><?php esc_attr_e( 'Disable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		</select>
	</div>
	<?php
	$htmls = '';

	$htmls = apply_filters('rtwwdpd_show_plus_html', $htmls);	

	if( !empty($htmls) )
	{ 
		print_r($htmls);
	}
	?>
</div>
<div class="rtwwdpd_prod_table">
	<table id="rtw_plus_tbl" class="rtwtable table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
		<caption>
			<h3 class="rtw_plus_class"><?php esc_html_e('Note : These rules is only for the "Plus" or "Prime" members which you have set from the users page.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai')?></h3>
		</caption>
		<thead>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Product/Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</thead>
		<?php
		$rtwwdpd_products_option = get_option('rtwwdpd_plus_member');
		
		$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		$products = array();
		if(is_array($cat) && !empty($cat))
		{
			foreach ($cat as $value) {
				$products[$value->term_id] = $value->name;
			}
		}
		
		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { ?>
		<tbody>
		<?php
			foreach ($rtwwdpd_products_option as $key => $value) {
				
				echo '<tr>';
				echo '<td>'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';

				echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_plus_name'] ).'</td>';
				
				echo '<td>';
				if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product')
				{
					echo 'Products';
				}
				elseif($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category')
				{
					echo 'Category';
				}
				echo '</td>';
				echo '<td>';
				if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product' )
				{
					if( isset($value["product_ids"]) && is_array($value["product_ids"]) && !empty($value["product_ids"]))
					{
						foreach ($value["product_ids"] as $kp => $pid) {
							echo get_the_title( $pid ). ' ';
						}
					}
					
				}else{
					if( isset($value['category_ids']) && is_array($value["category_ids"]) && !empty($value["category_ids"]))
					{
						foreach ($value["category_ids"] as $kps => $pids) {
							echo  $products[$pids]. ' ';
						}
					}
				}
				echo '</td>';

				if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_dis_percent')
				{
					echo '<td>Percent Discount</td>';
				}
				elseif($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_flat_dis_amt')
				{
					echo '<td>Flat Discount</td>';
				}
				else{
					echo '<td>Fixed Price</td>';
				}
				
				echo '<td>'.esc_html($value['rtwwdpd_dscnt_val'] ).'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_rule_on']))
				{	
					if($value['rtwwdpd_rule_on'] == 'rtw_amt'){
						echo 'Min - Max Amount';
					}
					elseif($value['rtwwdpd_rule_on'] == 'rtw_quant'){
						echo 'Min - Max Quantity';
					}
					else{
						echo 'Min - Max Amount & Min - Max Quantity';
					}
				}
				echo '</td>';
				echo '<td>';
				if($value['rtwwdpd_rule_on'] == 'rtw_amt')
				{
					echo esc_html( isset($value['rtwwdpd_min_purchase_of']) ? $value['rtwwdpd_min_purchase_of'] : 0 ). ' - '. esc_html(isset($value['rtwwdpd_max_purchase_of']) ? $value['rtwwdpd_max_purchase_of']: '' );
				}
				elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
				{
					echo esc_html(isset($value['rtwwdpd_min_purchase_quant'])?$value['rtwwdpd_min_purchase_quant']:0). ' - '. esc_html(isset($value['rtwwdpd_max_purchase_quant']) ? $value['rtwwdpd_max_purchase_quant'] : '');
				}
				elseif($value['rtwwdpd_rule_on'] == 'rtw_both')
				{
					echo esc_html( isset($value['rtwwdpd_min_purchase_of'] ) ? $value['rtwwdpd_min_purchase_of'] : 0 ). ' - '. esc_html(isset($value['rtwwdpd_max_purchase_of']) ? $value['rtwwdpd_max_purchase_of']: '' ). ' & ' .esc_html(isset($value['rtwwdpd_min_purchase_quant']) ? $value['rtwwdpd_min_purchase_quant'] : 0). ' - '. esc_html(isset($value['rtwwdpd_max_purchase_quant']) ? $value['rtwwdpd_max_purchase_quant'] : '' );
				}
				echo '</td>';

				echo '<td>'.esc_html($value['rtwwdpd_max_discount'] ).'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
				{
					foreach ($value['rtwwdpd_select_roles'] as $val) {
						echo esc_html($val).'<br>';
					}
				}
				
				echo '</td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_from_date'] ).'</td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_to_date'] ).'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_combi_exclude_sale'])){
					echo esc_html($value['rtwwdpd_combi_exclude_sale'] );
				}
				echo '</td>';
				
				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editplus='.$key ).'"><input type="button" class="rtw_plus_member rtwwdpd_edit_dt_row" value="Edit" /></a>
						<a href="'.esc_url( $rtwwdpd_absolute_url .'&delplus='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="Delete"/></a></td>';
				echo '<input type="hidden" class="rtwrow_no" value="1" name="plus_rul"/>';
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
		    	<th><?php esc_html_e( 'Rule For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Product/Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
