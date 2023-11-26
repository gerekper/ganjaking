<?php
if(isset($_GET['delcat']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_cat_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delcat']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_combi_cat_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cat_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_save_cat_combi'])){
	$rtwwdpd_cat_option = get_option('rtwwdpd_combi_cat_rule');
	$rtwwdpd_cat = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_cat['rtw_save_combi_cat']);

	if($rtwwdpd_cat_option == '')
	{
		$rtwwdpd_cat_option = array();
	}
	$rtwwdpd_products = array();

	foreach($rtwwdpd_cat as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option_no != 'save'){
		unset($_REQUEST['editcatc']);
		$rtwwdpd_cat_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_cat_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_combi_cat_rule',$rtwwdpd_cat_option);

	?><div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
		</div><?php
}

if(isset($_POST['rtwwdpd_copy_combi_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_combi_cat_rule');
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
	
	update_option('rtwwdpd_combi_cat_rule',$rtwwdpd_products_option);

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
	if(isset($_GET['editcatc']))
	{
		$rtwwdpd_cats = get_option('rtwwdpd_combi_cat_rule');
		$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		
		$rtwwdpd_prev_prod = $rtwwdpd_cats[$_GET['editcatc']];
		$edit = 'editcatc';
		$filteredURL = preg_replace('~(\?|&)'.$edit.'=[^&]*~', '$1', $rtwwdpd_url);
		$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cat_rules');
		?>

		<div class="rtwwdpd_combi_cat_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">
			<form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_cat_rule_tab_combi active">
									<a class="rtwwdpd_link" id="rtwcat_com_rule">
										<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_restriction_tab_combi">
									<a class="rtwwdpd_link" id="rtwcat_com_rest">
										<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_time_tab_combi">
									<a class="rtwwdpd_link" id="rtwcat_com_time">
										<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwcat_com_rule_tab">
									<input type="hidden" id="rtw_save_combi_cat" name="rtw_save_combi_cat" value="<?php echo esc_attr($_GET['editcatc']); ?>">
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
									</table>
									<table id="rtwcat_table">
										<thead>
											<tr>
												<th class="rtwtable_header rtwten"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header rtwforty"><?php esc_attr_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header rtwtwenty"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header rtwthirty"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											</tr>
										</thead>
										<tbody id="product_list_body">
											<?php
											if(isset($rtwwdpd_prev_prod['category_id']) && is_array($rtwwdpd_prev_prod['category_id']) && !empty($rtwwdpd_prev_prod['category_id']))
											{
											foreach ($rtwwdpd_prev_prod['category_id'] as $key => $val) {
												?>
												<tr id="rtw_tbltr">
													<td id="td_row_no"><?php echo esc_html($key+1); ?></td>
													<td class="td_product_name">
														<select name="category_id[]" id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_tbl_class" data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
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
				                            	<input type="number" class="rtwtd_quant"name="combi_quant[]" value="<?php echo esc_attr($rtwwdpd_prev_prod['combi_quant'][$key]); ?>"  />
				                            </td>
				                            <td id="td_remove">
				                            	<a class="button insert remove_cat" name="deletebtn" id="deletebtn" >Remove</a>
				                            </td>
				                        </tr>
				                    <?php }} else { ?>
				                    	<tr id="rtw_tbltr">
											<td id="td_row_no">1</td>
											<td class="td_product_name">
												<select name="category_id[]" id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_tbl_class"  data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
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
												<input min="1" type="number" class="rtwtd_quant"name="combi_quant[]" value="1"  />
											</td>
											<td id="td_remove">
												<?php esc_attr_e('Minimum One Category Required.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
										</tr>
									<?php } ?>
				                </tbody>
				                <tfoot>
				                	<tr>
				                		<td colspan=3>
				                			<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_cat" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
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

				        	<div class="options_group rtwwdpd_inactive" id="rtwcat_com_rest_tab">
				        		<table class="rtwwdpd_table_edit">
			            			<tr>
			            				<td>
					            			<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
					            		</td>
					            		<td>
					            			<select class="wc-product-search rtwwdpd_prod_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
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
											<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude product having tags from this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr>
										<td>
					            		<label class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
					            		</label>
				            		</td>
				            		<td>
				            			<input type="number" required value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>" min="0" name="rtwwdpd_max_discount">
											<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				            			<select class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple="multiple">
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
				            			<input type="checkbox" value="yes" name="rtwwdpd_exclude_sale" <?php checked( isset( $rtwwdpd_prev_prod['rtwwdpd_exclude_sale'] ) ? $rtwwdpd_prev_prod['rtwwdpd_exclude_sale'] : 'no', 'yes'); ?>/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'This will exclude the discount from the products that are on sale.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_catcc_save" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_catco_rule" name="rtwwdpd_save_cat_combi" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }else{ ?>
	<div class="rtwwdpd_combi_cat_tab rtwwdpd_form_layout_wrapper">
		<form method="post" action="" enctype="multipart/form-data">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_cat_rule_tab_combi active">
								<a class="rtwwdpd_link" id="rtwcat_com_rule">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab_combi">
								<a class="rtwwdpd_link" id="rtwcat_com_rest">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab_combi">
								<a class="rtwwdpd_link" id="rtwcat_com_time">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwcat_com_rule_tab">
								<input type="hidden" id="rtw_save_combi_cat" name="rtw_save_combi_cat" value="save"/>
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
								</table>
								<table id="rtwcat_table">
									<thead>
										<tr>
											<th class="rtwtable_header rtwten"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwforty"><?php esc_attr_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwtwenty"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwthirty"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="product_list_body">
										<tr id="rtw_tbltr">
											<td id="td_row_no">1</td>
											<td class="td_product_name">
												<select name="category_id[]" id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_tbl_class"  data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
													<?php
													$rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array(); 
													if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
													$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

													if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
														foreach ($rtwwdpd_categories as $cat) {
															echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_category_ids), true, true) . '>' . esc_html($cat->name) . '</option>';
														}
													}
													?>
												</select>
											</td>
											<td class="td_quant">
												<input min="1" type="number" class="rtwtd_quant"name="combi_quant[]" value="1"  />
											</td>
											<td id="td_remove">
												<?php esc_attr_e('Minimum One Category Required.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan=3>
												<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_cat" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
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

							<div class="options_group rtwwdpd_inactive" id="rtwcat_com_rest_tab">
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
				            			<input type="number" required value="" min="0" name="rtwwdpd_max_discount">
											<i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
													}
												}
												?>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude product having tags from this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
				            			<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles_com[]" multiple="multiple">
				            				<?php
				            				foreach ($rtwwdpd_roles as $roles => $role) { ?>
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
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_catcc_save" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_catco_rule" name="rtwwdpd_save_cat_combi" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }	
if(isset($_GET['editcatc']))
{
				echo '<div id="rtwwdpd_edit_combi_prod" class="rtwwdpd_prod_c_table_edit rtwwdpd_cat_c_table rtwwdpd_active">';
}else{
	echo '<div class="rtwwdpd_cat_c_table">';
}
?>
<table class="rtwtables table table-striped table-bordered dt-responsive nowrap" data-value="categor_com" cellspacing="0">
	<thead>
		<tr>
			
			<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products having Tags', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Exclude Sale Items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		</tr>
	</thead>
	<?php $rtwwdpd_products_option = get_option('rtwwdpd_combi_cat_rule');
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
			$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
			$products = array();
			if(is_array($cat) && !empty($cat))
			{
				foreach ($cat as $value) {
					$products[$value->term_id] = $value->name;
				}
			}
			foreach ($rtwwdpd_products_option as $key => $value) {
				echo '<tr data-val="'.$key.'">';

				echo '<td class="rtwrow_nos">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_combi_rule" value="Copy"></form></td>';
				echo '<td class="rtw_drags"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.(isset($value['rtwwdpd_offer_name']) ? esc_html__($value['rtwwdpd_offer_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

				echo'<td>';
				if(isset($value['category_id']) && is_array($value['category_id']) && !empty($value['category_id']))
				{
					foreach ($value['category_id'] as $keys => $val) {
						if(isset($products[$val]))
						{
							echo esc_html__($products[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
						}
					}
				}
				echo '</td>';

				echo '<td>';
				if(isset($value['combi_quant']) && is_array($value['combi_quant']) && !empty($value['combi_quant']))
				{
					foreach ($value['combi_quant'] as $val) {
						echo esc_html__($val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
					}
				}

				echo '</td>';

				if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
				{
					echo '<td>'.esc_html__( 'Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				elseif($value['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
				{
					echo '<td>'.esc_html__( 'Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				else{
					echo '<td>'.esc_html__( 'Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
				}
				
				echo '<td>'.(isset($value['rtwwdpd_discount_value']) ? esc_html__($value['rtwwdpd_discount_value'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

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

				echo '<td>'.(isset($value['rtwwdpd_max_discount']) ? esc_html__($value['rtwwdpd_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				echo '<td>';
				if(isset($value['rtwwdpd_select_roles_com']) && is_array($value['rtwwdpd_select_roles_com']) && !empty($value['rtwwdpd_select_roles_com']))
				{
					foreach ($value['rtwwdpd_select_roles_com'] as $val)
					{
						echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
					}
				}
				else{
					echo 'All';
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

				echo '<td>'.(isset($value['rtwwdpd_combi_from_date']) ? esc_html__($value['rtwwdpd_combi_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
				
				echo '<td>'.(isset($value['rtwwdpd_combi_to_date']) ? esc_html__($value['rtwwdpd_combi_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

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
				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editcatc='.$key ).'"><input type="button" class="rtw_edit_combi_cat rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
				<a href="'.esc_url( $rtwwdpd_absolute_url .'&delcat='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
			<th><?php esc_html_e( 'Categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Excluded Products having Tags', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Exclude Sale Items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		</tr>
	</tfoot>
</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
