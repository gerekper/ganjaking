<?php global $thepostid;
global $wp;
if(isset($_GET['delnth']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_nth_order');
	$rtwwdpd_row_no = sanitize_post($_GET['delnth']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_nth_order',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_nth_order_active';
	header('Location: '.$rtwwdpd_new_url);
    die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_nth_orders'])){
	$rtwwdpd_prod = $_POST;

	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_nth']);

	$rtwwdpd_products_option = get_option('rtwwdpd_nth_order', array());
	
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();

	foreach($rtwwdpd_prod as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option_no != 'save'){
		unset($_REQUEST['editnth']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_nth_order',$rtwwdpd_products_option);

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
	$rtwwdpd_products_option = get_option('rtwwdpd_nth_order');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_nth_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_nth_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_nth_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_nth_name'],"",-5);
	
	update_option('rtwwdpd_nth_order',$rtwwdpd_products_option);

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
if(isset($_GET['editnth']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	$rtwwdpd_bogo = get_option('rtwwdpd_nth_order');
	$rtwwdpd_prev_prod = $rtwwdpd_bogo[$_GET['editnth']];
	$key = 'editnth';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_nth_order_active';
?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Create Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
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
								<input type="hidden" name="edit_nth" id="edit_nth" value="<?php echo esc_attr($_GET['editnth']); ?>">
									<table class='rtw_specific_tbl'>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_nth_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_nth_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_nth_name']) : ''; ?>">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer on Order', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select class="rtwwdpd_order_no" name="rtwwdpd_order_no">
													<option <?php selected( $rtwwdpd_prev_prod['rtwwdpd_order_no'], 1 ); ?> value="1"><?php esc_html_e( 'First', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
													<option <?php selected($rtwwdpd_prev_prod['rtwwdpd_order_no'], 2); ?>  value="2"><?php esc_html_e( 'Nth', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
												</select>
												<input type="number" class="rtwwdpd_nth_value" name="rtwwdpd_nth_value" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_nth_value']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_nth_value']) : ''; ?>" min="1" placeholder="value of n">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_nth_value">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Repeat Discount on Next Orders', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input class="rtwwdpd_repeat_discount" type="checkbox" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_repeat_discount']) ? $rtwwdpd_prev_prod['rtwwdpd_repeat_discount'] : '', 'yes'); ?> value="yes" name="rtwwdpd_repeat_discount"/>
												<i class="rtwwdpd_description"><?php esc_html_e( 'The discount will be applied on all orders after the value of N.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr class="rtwwdpd_nth_value rtwwdpd_nth_val">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Discount on Order Upto', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input type="number" class="rtwwdpd_nth_upto_value" name="rtwwdpd_nth_upto_value" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_nth_upto_value']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_nth_upto_value']) : ''; ?>" min="1">
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
										<?php
										if( isset( $rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'] ) && $rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product' )
							        	{
							        		echo '<tr class="rtw_if_prod">';
							        	}
							        	else
										{
							        		echo '<tr class="rtw_if_prod">';
							        	} ?>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Choose Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<select id="rtwproducts" name="product_ids[]" class="wc-product-search rtwwdpd_payment_method" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" multiple="multiple"  >
			                					<?php
			                					// $rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
												$rtwwdpd_product_ids =!empty($rtwwdpd_prev_prod['product_ids']) ? $rtwwdpd_prev_prod['product_ids'] : array(); 
											
		                                         // selected product ids
												 
			                					if(is_array($rtwwdpd_product_ids) && !empty($rtwwdpd_product_ids))
			                					{
				                					foreach ($rtwwdpd_product_ids as $product_id) {
				                						$product = wc_get_product($product_id);
				                						if (is_object($product)) {
				                							echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
				                						}
				                					}
				                				}
			                					?>
		                					</select>
											</td>
							        	</tr>
							        	<?php 
							        	if( isset( $rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'] ) && $rtwwdpd_prev_prod['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category' )
							        	{
							        		echo '<tr class="rtw_if_cat">';
							        	}
							        	else{
							        		echo '<tr class="rtw_if_cat">';
							        	}
							        	?>
							        		<td>
							        			<label class="rtwwdpd_label"><?php esc_html_e('Choose Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
							        		</td>
											<td>
												<select name="category_ids[]" id="category_ids" class="wc-enhanced-select" multiple data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                    <?php
                                    $category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array(); 
                                    $cat_ids = isset( $rtwwdpd_prev_prod['category_ids'] ) ? $rtwwdpd_prev_prod['category_ids'] : array();
                                    if(!is_array($category_ids)) $category_ids=array($category_ids);
                                    $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

                                    if ($categories) {
                                        foreach ($categories as $cat) {
                                            echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id, $cat_ids), true, true) . '>' . esc_html($cat->name) . '</option>';
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
										<tr id="rtw_min_price">
							        		<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_purchase_of']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_purchase_of']) : '' ; ?>" min="1" name="rtwwdpd_min_purchase_of">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
							        	</tr>
							        	<tr id="rtw_min_quant">
							         	<td>
												<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_purchase_quant']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_purchase_quant']) : '' ; ?>" min="1" name="rtwwdpd_min_purchase_quant">
												<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
					            			<input required type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_max_discount">
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
					<input class="rtw-button rtwwdpd_save_rule rtwwdpd_nth_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
					<input id="submit_nth_rule" name="rtwwdpd_nth_orders" type="submit" hidden="hidden"/>
					<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				</div>
			</form>
		</div>
<?php }
else{ ?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">

		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Create Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
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
								<input type="hidden" name="edit_nth" id="edit_nth" value="save">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_nth_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer on Order', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select class="rtwwdpd_order_no" name="rtwwdpd_order_no">
												<option value="1"><?php esc_html_e( 'First', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
												<option value="2"><?php esc_html_e( 'Nth', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
											</select>
											<input type="number" class="rtwwdpd_nth_value" name="rtwwdpd_nth_value" value="" min="1" placeholder="value of n">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr class="rtwwdpd_nth_value">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Repeat Discount on Next Orders', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="checkbox" value="yes" name="rtwwdpd_repeat_discount"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'The discount will be applied on all orders after the value of N.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr class="rtwwdpd_nth_value rtwwdpd_nth_val">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount on Order Upto', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="number" class="rtwwdpd_nth_upto_value" name="rtwwdpd_nth_upto_value" value="" min="1">
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
											<select id="rtwproducts" name="product_ids[]" class="wc-product-search rtwwdpd_payment_method" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" multiple="multiple"  >
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
									<tr id="rtw_min_price">
						        		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_min_purchase_of">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this amount of product to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
						        	</tr>
						        	<tr id="rtw_min_quant">
						         		<td>
											<label class="rtwwdpd_label"><?php esc_html_e('On Minimum Purchase Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="number" value="" min="1" name="rtwwdpd_min_purchase_quant">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Customer have to buy atlest this number of products to get this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				            			<input required type="number" value="" min="0" name="rtwwdpd_max_discount">
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
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_nth_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_nth_rule" name="rtwwdpd_nth_orders" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
</div>
<?php } 
$rtwwdpd_enable = get_option('rtwwdpd_enable_nth_order', 'select');
?>	
<div class="rtwwdpd_enable_rule">
	<b><?php esc_html_e( 'Rule Permission : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
	</b>
	<select name="rtw_enable_nth_order" class="rtw_enable_nth_order">
		<option value="select" <?php selected( $rtwwdpd_enable, 'select'); ?>><?php esc_attr_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="enable" <?php selected( $rtwwdpd_enable, 'enable'); ?>><?php esc_attr_e( 'Enable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="disable" <?php selected( $rtwwdpd_enable, 'disable'); ?>><?php esc_attr_e( 'Disable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
<div class="rtwwdpd_prod_table">
	<table id="rtw_plus_tbl" class="rtwtable table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
		
		<thead>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'On Order No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Repeat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>

		    	<th><?php esc_html_e( 'Discount Upto Order', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>

		    	<th><?php esc_html_e( 'Rule For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Product / Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
		$rtwwdpd_products_option = get_option('rtwwdpd_nth_order');

		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		$products = array();
		if(is_array($cat) && !empty($cat))
		{
			foreach ($cat as $value) {
				$products[$value->term_id] = $value->name;
			}
		}

		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { ?>
		<tbody>
		<?php
			foreach ($rtwwdpd_products_option as $key => $value) {
				echo '<tr>';
				echo '<td>'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';

				echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_nth_name'] ).'</td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_nth_value'] ).'</td>';

				echo '<td>';
				if( isset( $value['rtwwdpd_repeat_discount'] ) && $value['rtwwdpd_repeat_discount'] == 'yes' )
				{
					echo 'Yes';
				}else{
					echo 'No';
				}
				echo '</td>';

				echo '<td>';
				if( isset( $value['rtwwdpd_repeat_discount'] ) && $value['rtwwdpd_repeat_discount'] == 'yes' )
				{
					echo $value['rtwwdpd_nth_upto_value'];
				}else{
					echo '--';
				}
				echo '</td>';

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
				if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product')
				{
					foreach ($value['product_ids'] as $pro => $pro_id) {
						echo get_the_title( $pro_id ) . ', ';
					}
				}
				elseif($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category')
				{
					foreach ($value['category_ids'] as $cat => $cat_id) {
						echo $products[$cat_id] . ', ';
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
						echo 'Minimum Amount';
					}
					elseif($value['rtwwdpd_rule_on'] == 'rtw_quant'){
						echo 'Minimum Quantity';
					}
					else{
						echo 'Minimum Amount & Minimum Quantity';
					}
				}
				echo '</td>';
				echo '<td>';
				if($value['rtwwdpd_rule_on'] == 'rtw_amt')
				{
					echo esc_html($value['rtwwdpd_min_purchase_of']);
				}
				elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
				{
					echo esc_html($value['rtwwdpd_min_purchase_quant']);
				}
				elseif($value['rtwwdpd_rule_on'] == 'rtw_both')
				{
					echo esc_html($value['rtwwdpd_min_purchase_of']). ' & ' .esc_html($value['rtwwdpd_min_purchase_quant']);
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
				
				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editnth='.$key ).'"><input type="button" class="rtw_plus_member rtwwdpd_edit_dt_row" value="Edit" /></a>
						<a href="'.esc_url( $rtwwdpd_absolute_url .'&delnth='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="Delete"/></a></td>';
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
		    	<th><?php esc_html_e( 'On Order No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Repeat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Upto Order', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Product / Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
