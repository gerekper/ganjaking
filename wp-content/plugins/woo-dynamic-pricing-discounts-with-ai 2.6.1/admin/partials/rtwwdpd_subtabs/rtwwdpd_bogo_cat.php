<?php

if(isset($_GET['delbogo']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delbogo']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_bogo_cat_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_save_catbogo_rule'])){
	
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_cat_bogo']);
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
	
	if($rtwwdpd_products_option == '')
	{
		$rtwwdpd_products_option = array();
	}
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();
	foreach($rtwwdpd_prod as $key => $val)
	{
		$rtwwdpd_products[$key] = $val;
	}
	if($rtwwdpd_option_no != 'save'){
		unset($_REQUEST['editbcat']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else
	{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_bogo_cat_rule',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php

}

if(isset($_POST['rtwwdpd_copy_combi_rule'])){
	$rtwwdpd_prod = $_POST;

	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
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
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_cat_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_cat_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_cat_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_cat_name'],"",-5);
	
	update_option('rtwwdpd_bogo_cat_rule',$rtwwdpd_products_option);

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
$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
if(isset($_GET['editbcat']))
{	
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['editbcat']];
	$key = 'editbcat';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_rules' );
	?>
	<div class="rtwwdpd_bogo_combi_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">
		<form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_bogo_c_rule_tab active">
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
								<input type="hidden" id="edit_cat_bogo" name="edit_cat_bogo" value="<?php echo esc_attr($_GET['editbcat']); ?>">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_bogo_cat_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_bogo_cat_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_cat_name']) : ''; ?>">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
								</table>
								<h3 class="rtw_tbltitle"><?php esc_html_e('Category Need to be Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
								<table id="rtwcat_table_bogo">
									<thead>
										<tr>
											<th class="rtwtable_header rtwten"><?php esc_html_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwforty"><?php esc_html_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwtwenty rtw_qty_width"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwthirty"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="product_list_body_bogo">
										<?php
										foreach ($rtwwdpd_prev_prod['category_id'] as $key => $val) {
											?>
											<tr id="rtw_tbltr">
												<td id="td_row_no"><?php echo ($key +1)?></td>
												<td class="td_product_name">
													<select name="category_id[]" id="category_id" class="wc-enhanced-select rtw_clsscategory rtwwdpd_prod_tbl_class"  data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
														<?php
				                                    $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  

				                                    if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
				                                    $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

				                                    if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
				                                    	foreach ($rtwwdpd_categories as $cat) {
			                                    		echo '<option value="' . esc_attr($cat->term_id) . '"' . selected($cat->term_id, $val, false) . '>' . esc_html($cat->name) . '</option>';
					                                    	}
					                                    }
					                                    ?>
					                                </select>
					                            </td>
					                            <td class="td_quant">
					                            	<input type="number" min="1" class="rtwtd_quant"name="combi_quant[]" value="<?php echo esc_attr($rtwwdpd_prev_prod['combi_quant'][$key]); ?>"  />
					                            </td>
					                            <td id="td_remove">
					                            	<a class="button insert remove_cat" name="deletebtn" id="deletebtn" ><?php esc_html_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
					                            </td>
					                        </tr>
					                    <?php } ?>
					                </tbody>
					                <tfoot>
					                	<tr>
					                		<td colspan=3>
					                			<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_cat_bogo" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
					                		</td>
					                	</tr>
					                </tfoot>
					            </table>
								<!-- <table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php //esc_html_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_select_free_product" class="rtwwdpd_select_free_product">
												<option <?php //selected( isset($rtwwdpd_prev_prod['rtwwdpd_select_free_product']) ? $rtwwdpd_prev_prod['rtwwdpd_select_free_product'] : '', 'same'); ?> value="same"><?php //esc_html_e('Set Same Product Free', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>		
												<option <?php //selected( isset($rtwwdpd_prev_prod['rtwwdpd_select_free_product']) ? $rtwwdpd_prev_prod['rtwwdpd_select_free_product'] : '', 'diff'); ?>  value="diff"><?php //esc_html_e('Select Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>		
											</select>
										</td>
									</tr>
								</table> -->
					            <h3 class="rtw_tbltitle"><?php esc_html_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
							<div class="free_product_table_cat">	
					            <table id="rtwbogo_table_cat_pro">
					            	<thead>
					            		<tr>
					            			<th class="rtwtable_header"><?php esc_html_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					            			<th class="rtwtable_header rtwforty"><?php esc_html_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					            			<th class="rtwtable_header rtw_qty_width"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<?php
                                                	$disnt_head_edit='';
													$disnt_head_edit =apply_filters('show_disnt_heading_edit',$disnt_head_edit);
													echo $disnt_head_edit;
												 ?>
												<!----          Update 2.1.0 --------->
											<?php
												if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                        						{
												 ?>
					             			<th class="rtwtable_header"><?php esc_html_e('Discount value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<?php
												}
												?>

												<!--------------end ---->
					            			<th class="rtwtable_header"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					            		</tr>
					            	</thead>
					            	<tbody id="rtw_bogo_cat_pro">
					            		<?php
	            						foreach ($rtwwdpd_prev_prod['rtwbogo'] as $key => $val) {
										
            							?>
				            			<tr>
				            				<td id="td_row_no"><?php echo esc_html(($key +1))?></td>
				            				<td id="td_product_name">
				            					<select id="rtwproductfree" name="rtwbogo[]" class="wc-product-search" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
				            						<?php
				            						$product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
					                                 // selected product ids
				            						$product = wc_get_product($val);
				            						if (is_object($product)) {
				            							echo '<option value="' . esc_attr($val) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
				            						}
				            						?>
				            					</select>
				            				</td>
				            				<td id="td_quant">
				            					<input type="number" min="1" name="bogo_quant_free[]" value="<?php echo isset($rtwwdpd_prev_prod['bogo_quant_free'][$key]) ? $rtwwdpd_prev_prod['bogo_quant_free'][$key] :''; ?>" id="free_cat_discnt_quant" />
				            				</td>
											<!---------- start   extra field for discount type 
								              -->
												<?php									
													$disnt_html_edit_cat="";
													$disnt_html_edit_cat=apply_filters('rtw_add_discnt_type_edit_cat',$disnt_html_edit_cat);
													echo $disnt_html_edit_cat;
												?>
											<!---------- End   extra field for discount type               -->
											<!---  update 2.0.1 --->
											<?php
											if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
											{
												$rtwwdpd_cat_val = isset($rtwwdpd_prev_prod ['rtwwdpd_dscnt_cat_val'][$key]) ? $rtwwdpd_prev_prod ['rtwwdpd_dscnt_cat_val'] : 100;
												 ?>
												<td><input type="number" value="<?php echo $rtwwdpd_cat_val ?>" min="0" step="0.01" name="rtwwdpd_dscnt_cat_val" ><span style="display:block"><i>Discount should be given in "%".</i></span></td>
												<?php
														
											}
											?>
											<!---  end -->
				            				<td id="td_remove">
				            					<a class="button insert remove" name="deletebtn" id="deletebtn" ><?php esc_html_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
				            				</td>
			            				</tr>
										<?php
									}
									
									?>
				            		</tbody>
				            		<tfoot>
				            			<tr>
				            				<td colspan=3>
				            					<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_bogo_cat_p" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
				            				</td>
				            			</tr>
				            		</tfoot>
				            	</table>
							</div>	
				            </div>
				            <div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
				            	<table class="rtwwdpd_table_edit">
				            		<tr>
				            			<td>
				            				<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
				            			</td>
				            			<td>
				            				<select class="wc-product-search rtwwdpd_prod_tbl_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
			            					<?php 
			            					if(isset($rtwwdpd_prev_prod['product_exe_id']) && is_array($rtwwdpd_prev_prod['product_exe_id']) && !empty($rtwwdpd_prev_prod['product_exe_id']))
			            					{
			            						foreach ($rtwwdpd_prev_prod['product_exe_id'] as $key => $value) 
												{
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
							    		<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
								    	</label>
								    </td>
								    <td>
								    	<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_bogo_min_orders']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_min_orders']) : '' ; ?>" min="0" name="rtwwdpd_bogo_min_orders">
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
										<input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_bogo_min_spend']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_min_spend']) : '' ; ?>" min="0" name="rtwwdpd_bogo_min_spend">
										<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
										<input type="date" name="rtwwdpd_bogo_from_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_bogo_from_date']); ?>" />
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
										<input type="date" name="rtwwdpd_bogo_to_date" placeholder="YYYY-MM-DD" required="required" value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_bogo_to_date']); ?>"/>
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
			<div class="rtwwdpd_prod_combi_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_save_bcat" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_bogocat_rule" name="rtwwdpd_save_catbogo_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }
else{ ?>
	<div class="rtwwdpd_bogo_combi_tab rtwwdpd_inactive rtwwdpd_form_layout_wrapper">
		<form method="post" action="" enctype="multipart/form-data">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_bogo_c_rule_tab active">
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
								<input type="hidden" id="edit_cat_bogo" name="edit_cat_bogo" value="save">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_bogo_cat_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
								</table>
								<h3 class="rtw_tbltitle"><?php esc_html_e('Category Need to be Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
								<table id="rtwcat_table_bogo">
									<thead>
										<tr>
											<th class="rtwtable_header rtwten"><?php esc_html_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwforty"><?php esc_html_e('Categorie', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwtwenty rtw_qty_width"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtwthirty"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="product_list_body_bogo">
										<tr id="rtw_tbltr">
											<td id="td_row_no">1</td>
											<td class="td_product_name">
												<select name="category_id[]" id="category_id" class="wc-enhanced-select rtw_clsscategory" data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
												<?php
				                                 $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
				                                 if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
				                                 $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

				                                 if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
				                                 	foreach ($rtwwdpd_categories as $cat) {
				                                 		echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_category_ids), true, false) . '>' . esc_html($cat->name) . '</option>';
					                                 	}
					                                }
					                                ?>
					                            </select>
					                        </td>
					                        <td class="td_quant">
					                        	<input type="number" min="1" class="rtwtd_quant"name="combi_quant[]" value="1"  />
					                        </td>
					                        <td id="td_remove">
					                        	<?php esc_html_e('Minimum One Required.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
					                        </td>
					                    </tr>
					                </tbody>
					                <tfoot>
					                	<tr>
					                 		<td colspan=3>
					                 			<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_cat_bogo" ><?php esc_html_e('Add Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
					                 		</td>
					                 	</tr>
									</tfoot>
								</table>
								<!-- <table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php //esc_html_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<select name="rtwwdpd_select_free_product" class="rtwwdpd_select_free_product">
												<option value="same"><?php //esc_html_e('Set Same Product Free', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>		
												<option value="diff"><?php //esc_html_e('Select Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>		
											</select>
										</td>
									</tr>
								</table> -->
								
								<h3 class="rtw_tbltitle"><?php esc_html_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
							<div class="free_product_table_cat">
								<table id="rtwbogo_table_cat_pro">
					             	<thead>
					             		<tr>
					             			<th class="rtwtable_header"><?php esc_html_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					             			<th class="rtwtable_header rtwforty"><?php esc_html_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					             			<th class="rtwtable_header rtw_qty_width"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											 <?php
                                                $disnt_head='';

													$disnt_head =apply_filters('show_disnt_heading',$disnt_head);
													echo $disnt_head;
												if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                        						{
												 ?>
					             			<th class="rtwtable_header"><?php esc_html_e('Discount value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<?php
												}
												?>
					             			<th class="rtwtable_header"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					             		</tr>
					             	</thead>
					             	<tbody id="rtw_bogo_cat_pro">
					             		<tr>
				             			<td id="td_row_no">1</td>
											<td id="td_product_name">
												<select id="rtwproductfree" name="rtwbogo[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
													<?php
													$product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
													if(is_array($product_ids) && !empty($product_ids))
													{
														foreach ($product_ids as $product_id) {
															$product = wc_get_product($product_id);
															if (is_object($product)) {
																echo '<option value="' . esc_attr($product_id) . '"' . selected(true, false, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
															}
														}
													}
													?>
												</select>
											</td>
											<td id="td_quant">
												<input type="number" min="1" name="bogo_quant_free[]" value="1" id="bogo_cat_quant" />
											</td>
												<!---------- start   extra field for discount type 
								              -->
												<?php
												$disnt_html="";
												$disnt_html=apply_filters('rtw_add_discnt_type',$disnt_html);
												echo $disnt_html;
											
												if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                        						{
												 ?>
					             			<td><input type="number" value=""  min="0" step="0.01" name="rtwwdpd_dscnt_cat_val" ><span style="display:block"><i>Discount should be given in "%".</i></span></td>
												<?php
												}
												?>
										<!---------- End   extra field for discount type -->
											<td id="td_remove">
												<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan=3>
												<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_bogo_cat_p" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
										</tr>
									</tfoot>
								</table>
								</div>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
										</td>
										<td>
											<select class="wc-product-search rtwwdpd_prod_tbl_class" multiple="multiple" name="product_exe_id[]" data-action="woocommerce_json_search_products_and_variations" placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Exclude products form this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="number" value="" min="0" name="rtwwdpd_bogo_min_orders">
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
											<input type="number" value="" min="0" name="rtwwdpd_bogo_min_spend">
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
											<input type="date" name="rtwwdpd_bogo_from_date" placeholder="YYYY-MM-DD" required="required" value="" />
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
											<input type="date" name="rtwwdpd_bogo_to_date" placeholder="YYYY-MM-DD" required="required" value=""/>
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
				<input class="rtw-button rtwwdpd_save_combi_rule rtwwdpd_save_bcat" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_bogocat_rule" name="rtwwdpd_save_catbogo_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php } 
if(isset($_GET['editbcat']) && !isset($_GET['editbogo']))
{
	echo '<div id="rtwwdpd_edit_combi_prod" class="rtwwdpd_prod_c_table_edit rtwwdpd_bogo_c_edit_table rtwwdpd_active">';
}
elseif(isset($_GET['editbogo']))
{
	echo '<div class="rtwwdpd_bogo_c_table rtwwdpd_inactive">';
}
else{
	echo '<div class="rtwwdpd_bogo_c_table">';
}
?>
	<table class="rtwtables table table-striped table-bordered dt-responsive nowrap" data-value="bogo_cat_tbl" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Category Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Purchased Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>

				<?php
					$rtwwdpd_percent_html='';	
					$rtwwdpd_percent_html=apply_filters('show_disnt_percent',$rtwwdpd_percent_html);
					echo $rtwwdpd_percent_html;
				?>
				<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Repeat', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php
		$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
		
		global $wp_roles;
		$rtwwdpd_roles 	= $wp_roles->get_names();
		$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
		$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );

		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { 
			$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
			$products = array();
			if(is_array($cat) && !empty($cat))
			{
				foreach ($cat as $value) {
					$products[$value->term_id] = $value->name;
				}
			}
			?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {
					echo '<tr data-val="'.$key.'">';


					echo '<td class="rtwrow_nos">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_combi_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drags"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ).'"/></td>';

					echo '<td>'.( isset($value['rtwwdpd_bogo_cat_name']) ? esc_html__($value['rtwwdpd_bogo_cat_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>';
					if(isset($value['category_id']) && is_array($value['category_id']) && !empty($value['category_id']))
					{
						foreach ($value['category_id'] as $val) {
							echo esc_html__($products[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
						}
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['combi_quant']) && is_array($value['combi_quant']) && !empty($value['combi_quant']))
					{
						foreach ($value['combi_quant'] as $val) {
							echo esc_html__( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwbogo']) && is_array($value['rtwbogo']) && !empty($value['rtwbogo']))
					{
						foreach ($value['rtwbogo'] as $val) {
							esc_html_e(get_the_title( $val ), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['bogo_quant_free']) && is_array($value['bogo_quant_free']) && !empty($value['bogo_quant_free']))
					{
						foreach ($value['bogo_quant_free'] as $val) {
							esc_html_e( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';
                   ////////////////////////
				   $disnt_html_val="";
				   $disnt_html_val=apply_filters('show_disnt_value_cat',$disnt_html_val,$key);
				   echo $disnt_html_val;
				   ///////////////////////
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
					if(isset($value['rtwwdpd_select_roles_com']) && is_array($value['rtwwdpd_select_roles_com']) && !empty($value['rtwwdpd_select_roles_com']))
					{
						foreach ($value['rtwwdpd_select_roles_com'] as $val) {
							echo esc_html__( $rtwwdpd_roles[$val] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}	
					}
					else{
						echo 'All';
					}
					echo '</td>';

					echo '<td>' .( isset( $value["rtwwdpd_bogo_min_orders"]) ? esc_html__($value["rtwwdpd_bogo_min_orders"] , "rtwwdpd-woo-dynamic-pricing-discounts-with-ai" ) : '').'</td>';

					echo '<td>'.( isset($value['rtwwdpd_bogo_min_spend']) ? esc_html__($value['rtwwdpd_bogo_min_spend'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.( isset($value['rtwwdpd_bogo_from_date']) ? esc_html__($value['rtwwdpd_bogo_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.( isset($value['rtwwdpd_bogo_to_date']) ? esc_html__($value['rtwwdpd_bogo_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_repeat_bogo']))
					{
						echo esc_html__($value['rtwwdpd_repeat_bogo'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					else{
						esc_html_e('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					echo '</td>';

					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editbcat='.$key ).'"><input type="button" class="rtw_edit_row rtwwdpd_edit_dt_row" value="'.esc_html__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
					<a href="'.esc_url( $rtwwdpd_absolute_url .'&delbogo='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_html__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
				<th><?php esc_html_e( 'Category Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Purchased Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php
					$rtwwdpd_percent_html='';	
					$rtwwdpd_percent_html=apply_filters('show_disnt_percent',$rtwwdpd_percent_html);
					echo $rtwwdpd_percent_html;
				?>
				<th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Repeat', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
