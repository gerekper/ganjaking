<?php
if(isset($_GET['delpro']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delpro']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_combi_prod_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}

$abc = 'verification_done';
if(isset($_POST['rtwwdpd_save_combi_rule'])){
	$rtwwdpd_prod = $_POST;

	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_chk_comb']);
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
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
		unset($_REQUEST['editid']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_combi_prod_rule',$rtwwdpd_products_option);
	?>
<div class="notice notice-success is-dismissible">
	<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
	</button>
</div>
<?php
}

if(isset($_POST['rtwwdpd_copy_combi_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_combi_offer_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_combi_offer_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_combi_offer_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_combi_offer_name'],"",-5);
	
	update_option('rtwwdpd_combi_prod_rule',$rtwwdpd_products_option);

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
if(isset($_GET['editid']))
{	global $wp;
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editid']];
	$key = 'editid';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_prod_rules');
	?>
<div class="rtwwdpd_add_combi_rule_tab rtwwdpd_active">
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

							<input type="hidden" id="edit_chk_combi" name="edit_chk_comb" value="<?php echo esc_attr($_GET['editid']); ?>">
							<table class="rtwwdpd_table_edit">
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<input type="text" name="rtwwdpd_combi_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_combi_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_combi_offer_name']) : ''; ?>">

										<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
										</i>
									</td>
								</tr>
							</table>
							<h3 class="rtw_tbltitle"><?php esc_attr_e('To be Applied on' , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai");?></h3>
							<table id="rtwproduct_table">
								<thead>
									<tr>
										<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										<th class="rtwtable_header"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
									</tr>
								</thead>
								<tbody id="product_list_body">
									<?php
									if(isset($rtwwdpd_prev_prod) && $rtwwdpd_prev_prod != '')
									{
										foreach ($rtwwdpd_prev_prod['product_id'] as $key => $val) {
											?>
											<tr>
												<td id="td_row_no"><?php echo ($key +1);?></td>
												<td id="td_product_name">
													<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
														<?php
														$rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
                            
														$product = wc_get_product($val);
														if (is_object($product)) {
															echo '<option value="' . esc_attr($val) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
														}
														?>
													</select>
												</td>
												<td id="td_quant">
													<input type="number" min="1"  name="combi_quant[]" value="<?php echo esc_attr($rtwwdpd_prev_prod['combi_quant'][$key]) ?>"  />
												</td>
												<td id="td_remove">
													<a class="button insert remove" name="deletebtn" id="deletebtn" ><?php esc_attr_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
												</td>
											</tr>
											<?php
											}
										}
									?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5">
											<a  class="button insert" name="rtwnsertbtn" id="rtwinsertbtn" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
										</td>
									</tr>
								</tfoot>
							</table>
							<table class="rtwwdpd_table_edit">
								<tr>
									<td>
										<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
									</td>
									<td>
										<select name="rtwwdpd_combi_discount_type">
											<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_combi_discount_type'], 'rtwwdpd_discount_percentage') ?>>
												<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_combi_discount_type'], 'rtwwdpd_flat_discount_amount') ?>>
												<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</option>
											<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_combi_discount_type'], 'rtwwdpd_fixed_price') ?>>
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
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_combi_discount_value']) ? $rtwwdpd_prev_prod['rtwwdpd_combi_discount_value'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_combi_discount_value">
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
				            			<input required type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_combi_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_combi_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_combi_max_discount">
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
		            					$rtwwdpd_selected_role =  $rtwwdpd_prev_prod['rtwwdpd_select_roles_com']; ?>
				            			<label class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            			</label>
				            		</td>
				            		<td>
				            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple>
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

										$rtwwdpd_selected_mail =  isset($rtwwdpd_prev_prod['rtwwdpd_select_com_emails']) ? $rtwwdpd_prev_prod['rtwwdpd_select_com_emails'] : array();  
										?>
										<label class="rtwwdpd_label"><?php esc_html_e('Restrict User with Email ID', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
										</label>
									</td>
									<td>
										<select class="rtwwdpd_select_roles" name="rtwwdpd_select_com_emails[]" multiple>
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
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_combi_min_orders']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_combi_min_orders']) : '' ; ?>" min="0" name="rtwwdpd_combi_min_orders">
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
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_combi_min_spend']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_combi_min_spend']) : '' ; ?>" min="0" name="rtwwdpd_combi_min_spend">
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
				            			<input type="checkbox" value="yes" name="rtwwdpd_combi_exclude_sale" <?php checked(isset( $rtwwdpd_prev_prod['rtwwdpd_combi_exclude_sale'] ) ? $rtwwdpd_prev_prod['rtwwdpd_combi_exclude_sale'] : " "); ?>/>
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
				           				<input type="date" name="rtwwdpd_combi_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_combi_from_date']); ?>" />
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
				           				<input type="date" name="rtwwdpd_combi_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_combi_to_date']); ?>"/>
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
			<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_pro_com_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			<input id="submit_procom_rule" name="rtwwdpd_save_combi_rule" type="submit" hidden="hidden"/>
			<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>
	</form>
</div>
<?php
}
else{?>
	<div class="rtwwdpd_add_combi_rule_tab">
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
								<input type="hidden" id="edit_chk_combi" name="edit_chk_comb" value="save">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_combi_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
								</table>
								<h3 class="rtw_tbltitle"><?php esc_attr_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
								<table id="rtwproduct_table">
									<thead>
										<tr>
											<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="product_list_body">
										<tr>
											<td id="td_row_no">1</td>
											<td id="td_product_name">
												<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
													
												</select>
											</td>
											<td id="td_quant">
												<input type="number" min="1"  name="combi_quant[]" value="1"  />
											</td>
											<td id="td_remove">
												<?php esc_attr_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">
												<a  class="button insert" name="rtwnsertbtn" id="rtwinsertbtn" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
										</tr>
									</tfoot>
								</table>
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_combi_discount_type">
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
											<input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_combi_discount_value">
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
					            			<input required type="number" value="" min="0" name="rtwwdpd_combi_max_discount">
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
					            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple>
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
											<select class="rtwwdpd_select_roles" name="rtwwdpd_select_com_emails[]" multiple>
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
											<input type="number" value="" min="0" name="rtwwdpd_combi_min_orders">
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
											<input type="number" value="" min="0" name="rtwwdpd_combi_min_spend">
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
				            				<input type="checkbox" value="yes" name="rtwwdpd_combi_exclude_sale"/>
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
				           					<input type="date" name="rtwwdpd_combi_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
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
				           					<input type="date" name="rtwwdpd_combi_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
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
				<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_pro_com_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_procom_rule" name="rtwwdpd_save_combi_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php 
} 
if(isset($_GET['editid']))
{
	echo '<div id="rtwwdpd_edit_combi_prod" class="rtwwdpd_prod_c_table_edit">';
}
else{
	echo '<div class="rtwwdpd_prod_c_table">';
}
?>
	<table class="rtwtables table table-striped table-bordered dt-responsive nowrap" data-value="prodct_com" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php $rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
		global $wp;
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

		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)){	?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {

					echo '<tr data-val="'.$key.'">';
					
					echo '<td class="rtwrow_nos">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_combi_rule" value="Copy"></form></td>';

					echo '<td class="rtw_drags"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
					
					echo '<td>'.(isset($value['rtwwdpd_combi_offer_name']) ? esc_html__($value['rtwwdpd_combi_offer_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';
					echo'<td>';
					if(isset($value['product_id']) && is_array($value['product_id']) && !empty($value['product_id']))
					{
						foreach ($value['product_id'] as $val) {
							echo '<span id="'.esc_attr($val).'">';
							echo esc_html__(get_the_title( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai')).'</span><br>';
						}
					}
					echo '</td><td>';
					if(isset($value['combi_quant']) && is_array($value['combi_quant']) && !empty($value['combi_quant']))
					{
						foreach ($value['combi_quant'] as $val) {
							echo esc_html__($val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '<br>';
						}
					}
					echo'</td>';

					if($value['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
					}
					elseif($value['rtwwdpd_combi_discount_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
					}
					else{
						echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';
					}
					
					echo '<td>'.(isset($value['rtwwdpd_combi_discount_value']) ? esc_html__($value['rtwwdpd_combi_discount_value'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '' ).'</td>';
					
					echo '<td>'.( isset($value['rtwwdpd_combi_max_discount']) ? esc_html__($value['rtwwdpd_combi_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';

					echo '<td>';

					if(isset($value['rtwwdpd_select_roles_com']) && is_array($value['rtwwdpd_select_roles_com']) && !empty($value['rtwwdpd_select_roles_com']))
					{
						foreach ($value['rtwwdpd_select_roles_com'] as $val)
						{
							echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'<br>';
						}
					}
					echo '</td>';
					
					echo '<td>';
					if(isset($value['rtwwdpd_select_com_emails']) && is_array($value['rtwwdpd_select_com_emails']) && !empty($value['rtwwdpd_select_com_emails']))
					{
						foreach ($value['rtwwdpd_select_com_emails'] as $val) {
							echo esc_html__($all_users_mail[$val], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").', ';
						}
					}
					else{
						echo '';
					}
					echo '</td>';

					echo '<td>'.(isset($value['rtwwdpd_combi_from_date']) ? esc_html__($value['rtwwdpd_combi_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';
					
					echo '<td>'.(isset($value['rtwwdpd_combi_to_date']) ? esc_html__($value['rtwwdpd_combi_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_combi_min_orders']) ? esc_html__($value['rtwwdpd_combi_min_orders'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_combi_min_spend']) ? esc_html__($value['rtwwdpd_combi_min_spend'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') : '').'</td>';
					
					echo '<td>';
					if(isset($value['rtwwdpd_combi_exclude_sale']))
					{
						echo esc_html__($value['rtwwdpd_combi_exclude_sale'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
					}
					echo '</td>';
					
					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editid='.$key ).'"><input type="button" class="rtw_combi_prod_edit rtwwdpd_edit_dt_row" name="rtw_combi_prod_edit"  value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'" /></a>
					<a href="'.esc_url( $rtwwdpd_absolute_url .'&delpro='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'"/></a></td>';
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
				<th><?php esc_html_e( 'Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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

