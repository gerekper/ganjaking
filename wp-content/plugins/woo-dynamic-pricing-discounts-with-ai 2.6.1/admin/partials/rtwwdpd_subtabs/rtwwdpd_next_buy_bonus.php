<?php
global $wp;
global $woocommerce;
if(isset($_GET['delnxt']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_next_buy_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delnxt']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_next_buy_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_next_buy_bonus';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_save_cat_combi'])){
	$rtwwdpd_cat_option = get_option('rtwwdpd_next_buy_rule');
	$rtwwdpd_cat = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_cat['rtwwdpd_edit_next']);

	if($rtwwdpd_cat_option == '')
	{
		$rtwwdpd_cat_option = array();
	}
	$rtwwdpd_products = array();

	foreach($rtwwdpd_cat as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option_no != 'save'){
		unset($_REQUEST['editnxt']);
		$rtwwdpd_cat_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_cat_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_next_buy_rule',$rtwwdpd_cat_option);

	?><div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
		</div><?php
}

if(isset($_POST['rtwwdpd_copy_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_next_buy_rule');
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
	
	update_option('rtwwdpd_next_buy_rule',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{ ?>
<div class="rtwwdpd_right">
	<div class="rtwwdpd_add_buttons">
		<input class="rtw-button rtwwdpd_next_buy" type="button" name="rtwwdpd_next_buy_rule" value="<?php esc_attr_e( 'Add New Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>

<?php
if(isset($_GET['editnxt']))
{
$rtwwdpd_cats = get_option( 'rtwwdpd_next_buy_rule' );
$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
$rtwwdpd_prev_prod = $rtwwdpd_cats[$_GET['editnxt']];
$edit = 'editnxt';
$filteredURL = preg_replace('~(\?|&)'.$edit.'=[^&]*~', '$1', $rtwwdpd_url);
$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_next_buy_bonus');
?>

<div class="rtwwdpd_active rtwwdpd_form_layout_wrapper">
	<form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
		<div id="woocommerce-product-data" class="postbox ">
			<div class="inside">
				<div class="panel-wrap product_data">
					<ul class="product_data_tabs wc-tabs">
						<li class="rtwcat_com_rule_tab active">
							<a class="rtwwdpd_link" id="rtwcat_com_rule">
								<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_restriction_tab">
							<a class="rtwwdpd_link" id="rtwcat_com_rest">
								<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_time_tab">
							<a class="rtwwdpd_link" id="rtwcat_com_time">
								<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
					</ul>
					<div class="panel woocommerce_options_panel">
						<div class="options_group rtwwdpd_active" id="rtwcat_com_rule_tab">
							<input type="hidden" id="rtwwdpd_edit_next" name="rtwwdpd_edit_next" value="<?php echo esc_attr($_GET['editnxt']); ?>">
							<table class="rtwwdpd_table_edit">
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
				            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple="multiple">
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
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_purchase_prdt']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_purchase_prdt']) : '' ; ?>" min="0" name="rtwwdpd_purchase_prdt">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum number of purchase products in this order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Calculate Total/Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<select name="rtwwdpd_totl_sbtotl">

												<option value="rtw_sbtotl" <?php selected( isset( $rtwwdpd_prev_prod['rtwwdpd_totl_sbtotl'] ) ? $rtwwdpd_prev_prod['rtwwdpd_totl_sbtotl'] : '' ) ?> ><?php esc_html_e( 'Sub-Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>

												<option value="rtw_totl" <?php selected( isset( $rtwwdpd_prev_prod['rtwwdpd_totl_sbtotl'] ) ? $rtwwdpd_prev_prod['rtwwdpd_totl_sbtotl'] : '' ) ?> ><?php esc_html_e( 'Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>

											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Total include taxes whereas subtotal exclude taxes.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Order Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_ordr_totl']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_ordr_totl']) : '' ; ?>" min="0" name="rtwwdpd_ordr_totl">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on this order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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

				        	<div class="options_group rtwwdpd_inactive" id="rtwcat_com_rest_tab">
				        		<table class="rtwwdpd_table_edit">
			            			<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
					            		</td>
					            		<td>
					            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_attr_e('Search product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" >
					            				<?php 
					            				if(isset($rtwwdpd_prev_prod['product_exe_id']) && is_array($rtwwdpd_prev_prod['product_exe_id']) && !empty($rtwwdpd_prev_prod['product_exe_id']))
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
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
					            		</td>
				            			<td class="td_product_name">
											<select name="category_exe_id[]" id="category_exe_id" multiple class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_class " data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
													<?php
			                                    $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
			                                    if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
			                                    $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
			                                    $rtwwdpd_exe_cat_ids = isset( $rtwwdpd_prev_prod['category_exe_id'] ) ? $rtwwdpd_prev_prod['category_exe_id'] : array();
			                                    if ($rtwwdpd_categories) {
			                                    if( is_array( $rtwwdpd_exe_cat_ids ) && !empty( $rtwwdpd_exe_cat_ids ) )
			                                    {
			                                    	foreach ( $rtwwdpd_exe_cat_ids as $key => $val ) {
				                                    	foreach ($rtwwdpd_categories as $cat) {
			                                    		echo '<option value="' . esc_attr($cat->term_id) . '"' . selected( $cat->term_id, $val, false) . '>' . esc_html($cat->name) . '</option>';
				                                    	}
				                                    }
				                                }
				                                else{
				                                	foreach ($rtwwdpd_categories as $cat) {
			                                    		echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
				                                    	}
				                                }
			                                    }
			                                    ?>
			                                </select>
					            		</td>
					            	</tr>
					            	<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Orders Done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_exclude_sale" <?php checked( isset( $rtwwdpd_prev_prod['rtwwdpd_exclude_sale'] ) ? $rtwwdpd_prev_prod['rtwwdpd_exclude_sale'] : 'no' , 'yes'); ?>/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Repeat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_repeat_discnt" <?php checked( isset( $rtwwdpd_prev_prod['rtwwdpd_repeat_discnt'] ) ? $rtwwdpd_prev_prod['rtwwdpd_repeat_discnt'] : 'no', 'yes'); ?>/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Repeat this discount on every next order of same user.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
				        	</div>

				        	<div class="options_group rtwwdpd_inactive" id="rtwcat_com_time_tab">
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
			<div class="rtwwdpd_cat_combi_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_next_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_next_rule" name="rtwwdpd_save_cat_combi" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }else{ ?>
<div class="rtwwdpd_next_buy_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">

	<form method="post" action="" enctype="multipart/form-data">
		<div id="woocommerce-product-data" class="postbox ">
			<div class="inside">
				<div class="panel-wrap product_data">
					<ul class="product_data_tabs wc-tabs">
						<li class="rtwcat_com_rule_tab active">
							<a class="rtwwdpd_link" id="rtwcat_com_rule">
								<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_restriction_tab">
							<a class="rtwwdpd_link" id="rtwcat_com_rest">
								<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
						<li class="rtwwdpd_time_tab">
							<a class="rtwwdpd_link" id="rtwcat_com_time">
								<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
							</a>
						</li>
					</ul>
	
					<div class="panel woocommerce_options_panel">
						<div class="options_group rtwwdpd_active" id="rtwcat_com_rule_tab">
							<input type="hidden" id="rtwwdpd_edit_next" name="rtwwdpd_edit_next" value="save">
							<table class="rtwwdpd_table_edit">
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
				            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple="multiple">
				            				<?php
				            				foreach ( $rtwwdpd_roles as $roles => $role) { ?>
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
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="" min="0" name="rtwwdpd_purchase_prdt">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum number of purchase products in this order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Calculate Total/Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<select name="rtwwdpd_totl_sbtotl">

												<option value="rtw_sbtotl"><?php esc_html_e( 'Sub-Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>

												<option value="rtw_totl"><?php esc_html_e( 'Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
												
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Total include taxes whereas subtotal exclude taxes.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Order Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
				            				</label>
										</td>
										<td>
											<input type="number" value="" min="0" name="rtwwdpd_ordr_totl">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on this order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
											<input type="number" value=" " required="required" min="0" step="0.01" name="rtwwdpd_discount_value">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
				            	</table>
				        	</div>

				        	<div class="options_group rtwwdpd_inactive" id="rtwcat_com_rest_tab">
				        		<table class="rtwwdpd_table_edit">
			            			<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
					            		</td>
					            		<td>
					            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_attr_e('Search product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" >
					            				
				            				</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude products form this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
					            		</td>
				            			<td class="td_product_name">
											<select name="category_exe_id[]" id="category_exe_id" multiple class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_class " data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
													<?php
			                                    $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
			                                    if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
			                                    $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

			                                    if ($rtwwdpd_categories) {
			                                    	foreach ($rtwwdpd_categories as $cat) {
		                                    		echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
			                                    	}
			                                    }
			                                    ?>
			                                </select>
					            		</td>
					            	</tr>
					            	<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Orders Done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_exclude_sale"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Repeat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            			</label>
					            		</td>
					            		<td>
					            			<input type="checkbox" value="yes" name="rtwwdpd_repeat_discnt"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Repeat this discount on every next order of same user.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
				        	</div>

				        	<div class="options_group rtwwdpd_inactive" id="rtwcat_com_time_tab">
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
			<div class="rtwwdpd_cat_combi_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_next_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_next_rule" name="rtwwdpd_save_cat_combi" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php } $rtwwdpd_enable = get_option('rtwwdpd_next_buy');
?>	
<div class="rtwwdpd_enable_rule">
	<b><?php esc_html_e( 'Rule Permission : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
	</b>
	<select name="rtw_enable_next_buy" class="rtw_enable_next_buy">
		<option value="select" <?php selected( $rtwwdpd_enable, 'select'); ?>><?php esc_attr_e( 'Select', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="enable" <?php selected( $rtwwdpd_enable, 'enable'); ?>><?php esc_attr_e( 'Enable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
		<option value="disable" <?php selected( $rtwwdpd_enable, 'disable'); ?>><?php esc_attr_e( 'Disable', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
	</select>
</div>
<div class="rtwwdpd_next_buy_table">
	<h4 class="rtw_plus_class"><?php esc_html_e( 'Note: These discounts are given on the customer\'s next order.' , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></h4>
<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="categor_com" cellspacing="0">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Calculate Total/Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Exclude Sale Items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Repeat', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		</tr>
	</thead>
	<?php $rtwwdpd_products_option = get_option('rtwwdpd_next_buy_rule');
	
	$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	global $wp_roles;
	$rtwwdpd_roles 	= $wp_roles->get_names();
	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

	if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)){	?>
		<tbody>
			<?php
			$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
			$products = array();
			if( is_array( $cat ) && !empty( $cat ) )
			{
				foreach ( $cat as $value ) {
					$products[$value->term_id] = $value->name;
				}
			}
			foreach ( $rtwwdpd_products_option as $key => $value) {
				echo '<tr data-val="'.$key.'">';

				echo '<td class="rtwrow_nos">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
				echo '<td class="rtw_drags"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.( isset( $value['rtwwdpd_offer_name'] ) ? esc_html__( $value['rtwwdpd_offer_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';

				echo '<td>';
				if( isset( $value['rtwwdpd_select_roles_com'] ) && is_array( $value['rtwwdpd_select_roles_com'] ) && !empty( $value['rtwwdpd_select_roles_com'] ) )
				{
					foreach ( $value['rtwwdpd_select_roles_com'] as $val )
					{
						echo esc_html__( $rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
					}
				}
				else{
					echo 'All';
				}
				echo '</td>';

				echo '<td>'.( isset( $value['rtwwdpd_purchase_prdt'] ) ? esc_html__( $value['rtwwdpd_purchase_prdt'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';

				echo '<td>';
				if( isset( $value['rtwwdpd_totl_sbtotl'] ) && $value['rtwwdpd_totl_sbtotl'] == 'rtw_sbtotl' )
				{
					esc_html_e( 'Sub-Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
				}
				else{
					esc_html_e( 'Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
				}
				echo '</td>';

				echo '<td>'.( isset( $value['rtwwdpd_ordr_totl'] ) ? esc_html__( $value['rtwwdpd_ordr_totl'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				if( $value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
				{
					echo '<td>'.esc_html__( 'Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else{
					echo '<td>'.esc_html__( 'Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				
				echo '<td>'.( isset( $value['rtwwdpd_discount_value'] ) ? esc_html__( $value['rtwwdpd_discount_value'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>';
				if( isset( $value['product_exe_id'] ) && is_array( $value['product_exe_id'] ) && !empty( $value['product_exe_id'] ) )
				{
					foreach ( $value['product_exe_id'] as $val )
					{
						echo '<span id="'.esc_attr( $val ).'">';
						echo esc_html__( get_the_title( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) ).'</span><br>';
					}
				}
				else{
					echo '';
				}
				echo '</td>';

				echo'<td>';
				if( isset( $value['category_exe_id'] ) && is_array( $value['category_exe_id'] ) && !empty( $value['category_exe_id'] ) )
				{
					foreach ( $value['category_exe_id'] as $keys => $val ) {
						echo esc_html__( $products[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
					}
				}
				echo '</td>';

				echo '<td>'.( isset( $value['rtwwdpd_max_discount'] ) ? esc_html__( $value['rtwwdpd_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo '<td>'.( isset( $value['rtwwdpd_min_orders'] ) ? esc_html__( $value['rtwwdpd_min_orders'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';

				echo '<td>'.( isset( $value['rtwwdpd_min_spend'] ) ? esc_html__( $value['rtwwdpd_min_spend'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';
				
				if(!isset($value['rtwwdpd_exclude_sale']))
				{
					echo '<td>'.esc_html__('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else
				{
					echo '<td>'.esc_html__('Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}

				if(!isset($value['rtwwdpd_repeat_discnt']))
				{
					echo '<td>'.esc_html__('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else
				{
					echo '<td>'.esc_html__('Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				
				echo '<td>'.( isset( $value['rtwwdpd_combi_from_date'] ) ? esc_html__( $value['rtwwdpd_combi_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';
				
				echo '<td>'.( isset( $value['rtwwdpd_combi_to_date'] ) ? esc_html__( $value['rtwwdpd_combi_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '' ).'</td>';

				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editnxt='.$key ).'"><input type="button" class="rtw_edit_combi_cat rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
				<a href="'.esc_url( $rtwwdpd_absolute_url .'&delnxt='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
			<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Purchase Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Calculate Total/Subtotal', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Total', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Exclude Sale Items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Repeat', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
