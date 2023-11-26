<?php
global $wp;
if(isset($_GET['delspec']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
	$rtwwdpd_row_no = sanitize_post($_GET['delspec']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_specific_c',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_specific_customer';
	header('Location: '.$rtwwdpd_new_url);
    die();
}

if(isset($_POST['rtwwdpd_specific_c'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_chk']);

	$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
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
		unset($_REQUEST['editspec']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_specific_c',$rtwwdpd_products_option);
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
	$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_specific_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_specific_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_specific_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_specific_name'],"",-5);
	
	update_option('rtwwdpd_specific_c',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

if(isset($_GET['editspec']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editspec']];
	$key = 'editspec';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_specific_customer');

?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
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
						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
								<input type="hidden" name="edit_chk" id="edit_chk" value="<?php echo esc_attr($_GET['editspec']);?>">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_specific_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_specific_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_specific_name']) : ''; ?>">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
		                              	<td>
		                                 	<label class="rtwwdpd_label"><?php esc_html_e('Rule for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
		                              	</td>
		                              	<td>
		                                 	<select name="rtwwdpd_rule_for">
			                                    <option value="rtwwdpd_min_purchase" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for'], 'rtwwdpd_min_purchase'); ?>>
			                                       <?php esc_html_e( 'Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                    </option>
			                                    <option value="rtwwdpd_min_prod" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for'], 'rtwwdpd_min_prod'); ?>>
			                                       <?php esc_html_e( 'Purchased Products Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                    </option>
			                                    <option value="rtwwdpd_mntly_pur_pro" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for'], 'rtwwdpd_mntly_pur_pro'); ?>>
			                                       <?php esc_html_e( 'Monthly Purchased Product Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                    </option>
			                                    <option value="rtwwdpd_mntly_pur_amt" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for'], 'rtwwdpd_mntly_pur_amt'); ?>>
			                                       <?php esc_html_e( 'Monthly Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                    </option>
			                                    <option value="rtwwdpd_mntly_visit" <?php selected($rtwwdpd_prev_prod['rtwwdpd_rule_for'], 'rtwwdpd_mntly_visit'); ?>>
			                                       <?php esc_html_e( 'Monthly Visit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                    </option>
			                                </select>
			                                <i class="rtwwdpd_description"><?php esc_html_e( 'Choose the condition on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
			                                </i>
			                            </td>
			                        </tr>
			                        <tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min']) : '' ; ?>" min="0" name="rtwwdpd_min">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_dsnt_type">
												<option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_discount_percentage') ?>>
													<?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_flat_discount_amount') ?>>
													<?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dsnt_type'], 'rtwwdpd_fixed_price') ?>>
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
											<?php
											$rtwwdpd_all_users_e = get_users();
											$rtwwdpd_user_email = isset($rtwwdpd_prev_prod['rtwwdpd_user_emails']) ? $rtwwdpd_prev_prod['rtwwdpd_user_emails'] : array();
											?>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Select User Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
											<!-- <input type="text" value="<?php // echo isset($rtwwdpd_prev_prod['rtwwdpd_user_emails']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_user_emails']) : '' ; ?>" name="rtwwdpd_user_emails"> --->
											<?php
											?>
											<select class="rtwwdpd_user_email_for_spc_c" name="rtwwdpd_user_emails[]" multiple>
												<?php
												foreach ($rtwwdpd_all_users_e as $rtwwdpd_user)
												{
													if(is_array($rtwwdpd_user_email))
													{
														?>
														<option value="<?php echo esc_attr($rtwwdpd_user->user_email); ?>"<?php
															foreach ($rtwwdpd_user_email as $ids => $rtwwdpd_usr_email) {
																selected($rtwwdpd_user->user_email, $rtwwdpd_usr_email);
															}
														?> >
															<?php esc_html_e( $rtwwdpd_user->user_email, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
															?>
														</option>
														<?php
													}
													else
													{
														?>
														<option value="<?php echo esc_attr($rtwwdpd_user->user_email); ?>">
																<?php esc_html_e( $rtwwdpd_user->user_email, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
														</option>
														<?php
													}
												}
												?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'If empty, user is selected according to the rule\'s other conditions. (Separate emails with comma \',\')', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_spec_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_spec_rule" name="rtwwdpd_specific_c" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
</div>
<?php
} 
else{ ?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_single_prod_rule" id="rtw_speci" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
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
								<input type="hidden" name="edit_chk" id="edit_chk" value="save">
								<table class='rtw_specific_tbl'>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_specific_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">
											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Rule for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_rule_for">
												<option value="rtwwdpd_min_purchase">
													<?php esc_html_e( 'Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_min_prod">
													<?php esc_html_e( 'Purchased Products Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_mntly_pur_pro">
													<?php esc_html_e( 'Monthly Purchased Product Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_mntly_pur_amt">
													<?php esc_html_e( 'Monthly Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="rtwwdpd_mntly_visit">
													<?php esc_html_e( 'Monthly Visit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Choose the condition on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="" min="0" name="rtwwdpd_min">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_dsnt_type">
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
							            	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ) ?>
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
												$rtwwdpd_all_users_e = get_users();
												?>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Select User Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
											<!-- <input type="text" value="" name="rtwwdpd_user_emails">
											<i class="rtwwdpd_description"><?php // esc_html_e( 'If empty, user is selected according to the rule\'s other conditions. (Separate emails with comma \',\')', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i> -->
											<select class="rtwwdpd_user_email_for_spc_c" name="rtwwdpd_user_emails[]" multiple>
												<?php
												foreach ($rtwwdpd_all_users_e as $rtwwdpd_user)
												{
												
													?>
													<option value="<?php echo esc_attr($rtwwdpd_user->user_email); ?>">
															<?php esc_html_e( $rtwwdpd_user->user_email, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
													</option>
													<?php
												}
												?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'If empty, user is selected according to the rule\'s other conditions. (Separate emails with comma \',\')', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_spec_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_spec_rule" name="rtwwdpd_specific_c" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
</div>

<?php }
$rtwwdpd_enable = get_option('rtwwdpd_specific_enable');
?>	
<div class="rtwwdpd_enable_rule">
	<b><?php esc_html_e( 'Rule Permission : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
	</b>
	<select name="rtw_enable_specific" class="rtw_enable_specific">
		<option value="select" <?php selected( $rtwwdpd_enable, 'select'); ?>><?php esc_attr_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="enable" <?php selected( $rtwwdpd_enable, 'enable'); ?>><?php esc_attr_e( 'Enable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="disable" <?php selected( $rtwwdpd_enable, 'disable'); ?>><?php esc_attr_e( 'Disable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
<div class="rtwwdpd_prod_table">
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="rtwspecific" cellspacing="0">
		<thead>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Minimum', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</thead>
		<?php
			$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
			$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
			if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { ?>
		<tbody>
			<?php
				foreach ($rtwwdpd_products_option as $key => $value) {
					echo '<tr data-val="'.$key.'">';
					echo '<td>'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
					
					echo '<td>'.esc_html__($value['rtwwdpd_specific_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';

					if($value['rtwwdpd_rule_for'] == 'rtwwdpd_min_prod')
					{
						echo '<td>'.esc_html__('Purchased Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_min_purchase')
					{
						echo '<td>'.esc_html__('Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_mntly_pur_pro')
					{
						echo '<td>'.esc_html__('Monthly Purchased Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_mntly_pur_amt')
					{
						echo '<td>'.esc_html__('Monthly Purchased Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}else
					{
						echo '<td>'.esc_html__('Monthly Visit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					
					echo '<td>'.esc_html__( $value['rtwwdpd_min'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';

					if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_discount_percentage')
					{
						echo '<td>'.esc_html__('Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					elseif($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_flat_discount_amount')
					{
						echo '<td>'.esc_html__('Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					else{
						echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					}
					
					echo '<td>'.esc_html__($value['rtwwdpd_dscnt_val'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					echo '<td>';
					if(isset($value['rtwwdpd_rule_on']))
					{
						if(trim($value['rtwwdpd_rule_on']) == 'rtw_amt')
						{
							esc_html_e('Minimum Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
						elseif(trim($value['rtwwdpd_rule_on']) == 'rtw_quant')
						{
							esc_html_e('Minimum Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
						else{
							esc_html_e('Minimum Quantity &', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
							echo '<br>';
							esc_html_e('Minimum Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_rule_on']))
					{
						if(trim($value['rtwwdpd_rule_on']) == 'rtw_amt')
						{
							echo esc_html__($value['rtwwdpd_min_purchase_of'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
						elseif(trim($value['rtwwdpd_rule_on']) == 'rtw_quant')
						{
							echo esc_html__($value['rtwwdpd_min_purchase_quant'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
						else{
							echo esc_html__($value['rtwwdpd_min_purchase_of'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
							echo esc_html__($value['rtwwdpd_min_purchase_quant'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
						}
					}
					echo '</td>';

					echo '<td>'.esc_html__($value['rtwwdpd_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $val) {
							echo esc_html__($val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';
					
					echo '<td>'(isset($value['rtwwdpd_user_emails']) ? $value['rtwwdpd_user_emails'] : '' , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					
					echo '<td>'($value['rtwwdpd_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
					
					echo '<td>'($value['rtwwdpd_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_combi_exclude_sale'])){
						echo esc_html__($value['rtwwdpd_combi_exclude_sale'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					else{
						esc_html_e('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					echo '</td>';
					
					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editspec='.$key ).'"><input type="button" class="rtw_specific_edit rtwwdpd_edit_dt_row" value="'.esc_html__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
							<a href="'.esc_url( $rtwwdpd_absolute_url .'&delspec='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_html__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
		    	<th><?php esc_html_e( 'Minimum', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Rule On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_attr_e( 'Exclude Sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</tfoot>
	</table>
</div>