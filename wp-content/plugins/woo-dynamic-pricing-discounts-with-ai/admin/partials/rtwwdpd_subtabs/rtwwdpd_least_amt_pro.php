<?php
$rtwwdpd_products_option = get_option('rtwwdpd_get_least_free');
global $wp;
if(isset($_GET['delcate']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_get_least_free');
	$rtwwdpd_row_no = sanitize_post($_GET['delcate']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_get_least_free',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_least_active';
	header( 'Location: '.$rtwwdpd_new_url );
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_save_cat'])){
    $rtwwdpd_cat = $_POST; 
	$rtwwdpd_option = sanitize_post($_POST['rtw_save_single_cat']);
	$rtwwdpd_cat_option = get_option('rtwwdpd_get_least_free');

	if($rtwwdpd_cat_option == '')
	{
		$rtwwdpd_cat_option = array();
	}
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();

	foreach($rtwwdpd_cat as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option != 'save')
	{
		unset($_REQUEST['editleast']);
		$rtwwdpd_cat_option[$rtwwdpd_option] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_cat_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_get_least_free',$rtwwdpd_cat_option);
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
	$rtwwdpd_products_option = get_option('rtwwdpd_get_least_free');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_cat_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_cat_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_cat_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_offer_cat_name'],"",-5);
	
	update_option('rtwwdpd_get_least_free',$rtwwdpd_products_option);

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
if(isset($_GET['editleast']))
{
	$rtwwdpd_cats = get_option('rtwwdpd_get_least_free');
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_prev_prod = $rtwwdpd_cats[$_GET['editleast']];
	$edit = 'editleast';
	$filteredURL = preg_replace('~(\?|&)'.$edit.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_least_active');
	
	?>
<div class="rtwwdpd_right">
<div class="rtwwdpd_add_buttons">
	<input class="rtw-button rtwwdpd_single_cat" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
</div>

<div class="rtwwdpd_single_cat_rule rtwwdpd_active rtwwdpd_form_layout_wrapper">
	<form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
		<div id="woocommerce-product-data" class="postbox ">
			<div class="inside">
				<div class="panel-wrap product_data">
					<ul class="product_data_tabs wc-tabs">
						<li class="rtwwdpd_single_cat_rule_tab active">
							<a class="rtwwdpd_link" id="rtwcat_rule">
								<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_restriction_tab">
							<a class="rtwwdpd_link" id="rtwcat_restrict">
								<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_time_tab">
							<a class="rtwwdpd_link" id="rtwcat_validity">
								<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
					</ul>

					<div class="panel woocommerce_options_panel">
						<div class="options_group rtwwdpd_active" id="rtwcat_rule_tab">
							<input type="hidden" id="rtw_save_single_cat" name="rtw_save_single_cat" value="<?php echo esc_attr($_GET['editleast']); ?>">
							<table class="rtwwdpd_table_edit">
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_offer_cat_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_cat_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_cat_name']) : ''; ?>">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
                                </tr>
                                <tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<select name="rtwwdpd_discount_on" class="rtwwdpd_discount_on">
                                            <option <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_on'], '1');?> value="1"><?php esc_html_e( 'Any Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
                                            <option <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_on'], '2');?> value="2"><?php esc_html_e( 'Selected Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
                                        </select>

										<i class="rtwwdpd_description"><?php esc_html_e( 'Select Product/Category on which this discount should be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr class="rtwwdpd_category_sel">
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Select Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<?php
											$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
											$cats = array();
											
											if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
											   foreach ($rtwwdpd_categories as $cat) {
											        $cats[$cat->term_id] = $cat->name;
											   }
											}
										?>
										<select name="category_id[]" multiple id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_class" data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
										<?php 
										if(is_array($cats) && !empty($cats))
										{
				            				foreach ($cats as $key => $value) {
												
					            				if(isset($rtwwdpd_prev_prod['category_id']))
					            				{ 
														            					
					            					if(in_array($key,$rtwwdpd_prev_prod['category_id']))
				            						{
			            								echo '<option selected="selected" value="' . esc_attr($key) . '">' . esc_html($cats[$key]) . '</option>'; 
			            							}
			            							else{
			            								echo '<option value="' . esc_attr($key) . '">' . esc_html($cats[$key]) . '</option>';
			            							}
				            					}
				            					else{
		            								echo '<option value="' . esc_attr($key) . '">' . esc_html($cats[$key]) . '</option>';
		            							}
				            				}
				            			}
			            				?>
			            			</select>
			            			<i class="rtwwdpd_description"><?php esc_html_e( 'Select category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			            			</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Minimum Quantity to Purchase ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_cat']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_cat']) : ''; ?>" min="0" name="rtwwdpd_min_cat">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount Value(%)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
									</td>
									<td>
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_val']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_val']) : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_cat_val">%
										<i class="rtwwdpd_description"><?php esc_html_e( 'This discount should be given on the least amount product (Enter 100 to make it free).', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Notice', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_offer_msg" placeholder="<?php esc_html_e('Buy 2 + 1...','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_msg']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_msg']) : ''; ?>">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This notice can be seen on cart when this discount is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
                                </tr>
							</table>
						</div>
						<div class="options_group rtwwdpd_inactive" id="rtwcat_restriction_tab">
							<table class="rtwwdpd_table_edit">
								<tr>
	            				<td>
			            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
			            		</td>
			            		<td>
			            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
			            				<?php 
			            				if(isset($rtwwdpd_prev_prod['product_exe_id']) && is_array($rtwwdpd_prev_prod['product_exe_id']))
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
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'These product are excluded if they have least price amoung the products in cart.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<?php
								$rtwwdpd_termss = get_terms( 'product_tag' );
								$rtwwdpd_term_array = array();
								if ( ! empty( $rtwwdpd_termss ) && ! is_wp_error( $rtwwdpd_termss ) ){
									foreach ( $rtwwdpd_termss as $term ) {
										$rtwwdpd_term_array[$term->term_id] = $term->name;
									}
								}
								?>
								<tr>
		            				<td>
				            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products having Tag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
				            		</td>
				            		<td>
				            			<select class="rtwwdpd_payment_method rtwwdpd_prod_class" name="rtw_exe_product_tags[]" multiple>
											<?php  
											if(is_array($rtwwdpd_term_array) && !empty($rtwwdpd_term_array))
											{
												$i = 0;
												foreach ($rtwwdpd_term_array as $key => $value) {

													if($rtwwdpd_prev_prod['rtw_product_tags'][$i] == $key)
													{
														
														echo '<option value="'.esc_attr($key).'" selected="selected" >'.esc_html($value).'</option>';
													}
													else{
														echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
													}
													$i++;
												}
											}
											?>
										</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'These product are excluded if they have least price amoung the products in cart.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
	            					$rtwwdpd_selected_role =  isset($rtwwdpd_prev_prod['rtwwdpd_select_roles']) ? $rtwwdpd_prev_prod['rtwwdpd_select_roles'] : ''; ?>
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
						<div class="options_group rtwwdpd_inactive" id="rtwcat_time_tab">
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
					</div>
				</div>
			</div>
		</div>
		<div class="rtwwdpd_cat_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
			<input class="rtw-button rtwwdpd_save_cat rtwwdpd_cat_saave" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			<input id="submit_cat_rule" name="rtwwdpd_save_cat" type="submit" hidden="hidden"/>
			<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>
	</form>
</div>
<?php
	}
else{
?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_cat" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>
	<div class="rtwwdpd_single_cat_rule rtwwdpd_form_layout_wrapper">
		<form method="post" action="" enctype="multipart/form-data">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_single_cat_rule_tab active">
								<a class="rtwwdpd_link" id="rtwcat_rule">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab">
								<a class="rtwwdpd_link" id="rtwcat_restrict">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab">
								<a class="rtwwdpd_link" id="rtwcat_validity">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
						<div class="options_group rtwwdpd_active" id="rtwcat_rule_tab">
							<input type="hidden" id="rtw_save_single_cat" name="rtw_save_single_cat" value="save">
							<table class="rtwwdpd_table_edit">
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_offer_cat_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
                                </tr>
                                <tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<select name="rtwwdpd_discount_on" class="rtwwdpd_discount_on">
                                            <option value="1"><?php esc_html_e( 'Any Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
                                            <option value="2"><?php esc_html_e( 'Selected Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
                                        </select>

										<i class="rtwwdpd_description"><?php esc_html_e( 'Select Product/Category on which this discount should be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr class="rtwwdpd_category_sel">
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Select Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<?php
											$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
											$cats = array();

											if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
											   foreach ($rtwwdpd_categories as $cat) {
											        $cats[$cat->term_id] = $cat->name;
											   }
											}
										?>
										<select name="category_id[]" multiple="multiple" id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_class" data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
										<?php 
										if(is_array($cats) && !empty($cats))
										{
				            				foreach ($cats as $key => $value) {
					            				
		            							echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
		            							
				            				}
				            			}
			            				?>
			            			</select>
			            			<i class="rtwwdpd_description"><?php esc_html_e( 'Select category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			            			</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Minimum Quantity to Purchase ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="number" value="" min="0" name="rtwwdpd_min_cat">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount Value(%)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
									</td>
									<td>
										<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_cat_val">%
										<i class="rtwwdpd_description"><?php esc_html_e( 'This discount should be given on the least amount product (Enter 100 to make it free).', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Notice', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_offer_msg" placeholder="<?php esc_html_e('Buy 2 + 1...','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" value="">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This notice can be seen on cart when this discount is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
                                </tr>
							</table>
						</div>
						<div class="options_group rtwwdpd_inactive" id="rtwcat_restriction_tab">
							<table class="rtwwdpd_table_edit">
								<tr>
	            				<td>
			            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
			            		</td>
			            		<td>
			            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
			            				<?php 
			            				if(isset($rtwwdpd_prev_prod['product_exe_id']) && is_array($rtwwdpd_prev_prod['product_exe_id']))
			            				{
				            				foreach ($rtwwdpd_prev_prod['product_exe_id'] as $key => $value) {
				            					$product = wc_get_product($value);
													if (is_object($product)) {
														echo '<option value="' . esc_attr($value) . '">' . wp_kses_post($product->get_formatted_name()) . '</option>';
													}
				            				}
			            				}
			            				?>
			            			</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'These product are excluded if they have least price amoung the products in cart.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</i>
									</td>
								</tr>
								<?php
								$rtwwdpd_termss = get_terms( 'product_tag' );
								$rtwwdpd_term_array = array();
								if ( ! empty( $rtwwdpd_termss ) && ! is_wp_error( $rtwwdpd_termss ) ){
									foreach ( $rtwwdpd_termss as $term ) {
										$rtwwdpd_term_array[$term->term_id] = $term->name;
									}
								}
								?>
								<tr>
		            				<td>
				            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products having Tag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
				            		</td>
				            		<td>
				            			<select class="rtwwdpd_payment_method rtwwdpd_prod_class" name="rtw_exe_product_tags[]" multiple>
											<?php  
											if(is_array($rtwwdpd_term_array) && !empty($rtwwdpd_term_array))
											{
												$i = 0;
												foreach ($rtwwdpd_term_array as $key => $value) {

													echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
													
													$i++;
												}
											}
											?>
										</select>
										<i class="rtwwdpd_description"><?php esc_html_e( 'These product are excluded if they have least price amoung the products in cart.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
	            					$rtwwdpd_selected_role =  isset($rtwwdpd_prev_prod['rtwwdpd_select_roles']) ? $rtwwdpd_prev_prod['rtwwdpd_select_roles'] : ''; ?>
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
						<div class="options_group rtwwdpd_inactive" id="rtwcat_time_tab">
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
					</div>
                </div>
            </div>
        </div>
        <div class="rtwwdpd_cat_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
            <input class="rtw-button rtwwdpd_save_cat rtwwdpd_cat_saave" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
            <input id="submit_cat_rule" name="rtwwdpd_save_cat" type="submit" hidden="hidden"/>
            <input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
        </div>
    </form>
</div>
<?php 
} 
$rtwwdpd_enable = get_option( 'rtwwdpd_enable_least_free', 'select' );

?>	
<div class="rtwwdpd_enable_rule">
	<b><?php esc_html_e( 'Rule Permission : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
	</b>
	<select name="rtwwdpd_enable_least_free" class="rtwwdpd_enable_least_free">
		<option value="select" <?php selected( $rtwwdpd_enable, 'select'); ?>><?php esc_attr_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="enable" <?php selected( $rtwwdpd_enable, 'enable'); ?>><?php esc_attr_e( 'Enable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="disable" <?php selected( $rtwwdpd_enable, 'disable'); ?>><?php esc_attr_e( 'Disable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
</div>
<div class="rtwwdpd_table rtwwdpd_cat_table">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="categor" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_attr_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Discount On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Product Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Discount Value(%)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Notice', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Excluded Products having Tags', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php
		$rtwwdpd_products_option = get_option('rtwwdpd_get_least_free');
		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

		global $wp_roles;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$rtwwdpd_termss = get_terms( 'product_tag' );
		$rtwwdpd_term_array = array();
		if ( ! empty( $rtwwdpd_termss ) && ! is_wp_error( $rtwwdpd_termss ) ){
		    foreach ( $rtwwdpd_termss as $term ) {
		        $rtwwdpd_term_array[$term->term_id] = $term->name;
		    }
		}

		if(is_array($rtwwdpd_products_option) &&  !empty($rtwwdpd_products_option)) { ?>
			<tbody>
			<?php
			$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
			$products = array();
			if(is_array($cat) && !empty($cat))
			{
				foreach ( $cat as $value ) {
					$products[$value->term_id] = $value->name;
				}
			}
			
			foreach ($rtwwdpd_products_option as $key => $value) {
				echo '<tr data-val="'.$key.'">';

				echo '<td class="rtwrow_no">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
				echo '<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
				echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.(isset($value['rtwwdpd_offer_cat_name']) ? esc_html__($value['rtwwdpd_offer_cat_name'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
                
                if( $value['rtwwdpd_discount_on'] == 1 )
                {
                    echo '<td>'.esc_html__( 'Any Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                }else{
                    echo '<td>';
                    foreach( $value['category_id'] as $cats => $cat_ids )
                    {
                        esc_html_e( $products[$cat_ids] . ', ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                    }
                    echo '</td>';
                }
				
				echo '<td>'.(isset($value['rtwwdpd_min_cat']) ? esc_html__($value['rtwwdpd_min_cat'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_dscnt_cat_val']) ? esc_html__($value['rtwwdpd_dscnt_cat_val'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_offer_msg']) ? esc_html__($value['rtwwdpd_offer_msg'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
				{
					foreach ($value['rtwwdpd_select_roles'] as $keys => $val) {
						echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
				}
				echo '</td>';

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

				echo '<td>';
				if(isset($value['rtw_exe_product_tags']) && is_array($value['rtw_exe_product_tags']) && !empty($value['rtw_exe_product_tags'])){
					foreach ($value['rtw_exe_product_tags'] as $keys => $val)
					{
						echo esc_html__( $rtwwdpd_term_array[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
					}
				}
				echo '</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_from_date']) ? esc_html__($value['rtwwdpd_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_to_date']) ? esc_html__($value['rtwwdpd_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>'.(isset($value['rtwwdpd_min_orders']) ? esc_html__($value['rtwwdpd_min_orders'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>'.(isset($value['rtwwdpd_min_spend']) ? esc_html__($value['rtwwdpd_min_spend'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				if(!isset($value['rtwwdpd_exclude_sale']))
				{
					echo '<td>'.esc_html__('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else
				{
					echo '<td>'.esc_html__('Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}

				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editleast='.$key ).'"><input type="button" class="rtw_edit_cat rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" />
				</a>
				<a href="'.esc_url( $rtwwdpd_absolute_url .'&delcate='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
				
				echo '</tr>';
			}
			?>		
			</tbody>
		<?php } ?>

		<tfoot>
			<tr>
				
            <th><?php esc_attr_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Discount On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Product Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Discount Value(%)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Notice', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Excluded Products having Tags', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
