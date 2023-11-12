<?php 
$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');

global $wp;
if(isset($_GET['delbr']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');
	$rtwwdpd_row_no = sanitize_post($_GET['delbr']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_bogo_rule',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if(isset($_POST['rtwwdpd_save_rule'])){
	
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_chk_bogo']);
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');
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
		unset($_REQUEST['editbogo']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}
	update_option('rtwwdpd_bogo_rule',$rtwwdpd_products_option);

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
	$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');
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

	if(isset($rtwwdpd_products_option[$rtwwdpd_option_no]) && !empty($rtwwdpd_products_option[$rtwwdpd_option_no]))
	{
		$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_offer_name'] = $rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_offer_name'] . esc_html__('-Copy', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	$rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_offer_name'] = substr_replace($rtwwdpd_products_option[$rtwwdpd_option_no]['rtwwdpd_bogo_offer_name'],"",-5);
	
	update_option('rtwwdpd_bogo_rule',$rtwwdpd_products_option);

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
		<input class="rtw-button rtwwdpd_single_bogo_rule" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add BOGO Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<input class="rtw-button rtwwdpd_cat_bogo_rule" type="button" name="rtwwdpd_combi_prod_rule" value="<?php esc_attr_e( 'Add BOGO Categorie Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		<input class="rtw-button rtwwdpd_tag_bogo_rule" type="button" name="rtwwdpd_tag_rule" value="<?php esc_attr_e( 'Add BOGO Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
	</div>


	<?php	
	if(isset($_GET['editbogo']))
	{	
		$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));

		$rtwwdpd_bogo = get_option('rtwwdpd_bogo_rule');
		$rtwwdpd_prev_prod = $rtwwdpd_bogo[$_GET['editbogo']];
		$key = 'editbogo';
		$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
		$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_bogo_rules');
		
		$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
		?>
		<div class="rtwwdpd_single_bogo_rule_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">
			<form action="<?php echo esc_url($rtwwdpd_new_url); ?>" method="POST" accept-charset="utf-8">
				<div id="woocommerce-product-data" class="postbox ">
					<div class="inside">
						<div class="panel-wrap product_data">
							<ul class="product_data_tabs wc-tabs">
								<li class="rtwwdpd_bogo_rule_tab active">
									<a class="rtwwdpd_link" id="rtwbogo_rule">
										<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_restriction_tab">
									<a class="rtwwdpd_link" id="rtwbogo_restrict">
										<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
								<li class="rtwwdpd_time_tab">
									<a class="rtwwdpd_link" id="rtwbogo_validity">
										<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
									</a>
								</li>
							</ul>

							<div class="panel woocommerce_options_panel">
								<div class="options_group rtwwdpd_active" id="rtwbogo_rule_tab">
									<input type="hidden" id="edit_chk_bogo" name="edit_chk_bogo" value="<?php echo esc_attr($_GET['editbogo']); ?>">
									<table class="rtwwdpd_table_edit">
									<?php 
									if($active_dayss == 'yes')
									{
										$daywise_discount = apply_filters('rtwwdpd_bogo_daywise_discount_edit', $_GET['editbogo']);
										echo $daywise_discount;
									}
									?>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
											</td>
											<td>
												<input type="text" name="rtwwdpd_bogo_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_bogo_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_offer_name']) : ''; ?>">

												<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
												</i>
											</td>
										</tr>
										<tr>
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Rule on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<select class="rtwwdpd_bogo_rule_on" name="rtwwdpd_bogo_rule_on" id="rtwwdpd_bogo_rule_on">
													<option <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_bogo_rule_on']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_rule_on']) : '', 'product' ); ?>  value="product"><?php esc_html_e( 'Selected Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
													<option <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_bogo_rule_on']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_bogo_rule_on']) : '', 'min_purchase' ); ?>  value="min_purchase"><?php esc_html_e( 'Minimum Purchase', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
												</select>
												<i class="rtwwdpd_description"><?php esc_html_e( 'Rule to be applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</i>
											</td>
										</tr>
										<tr id="mini_pur_amount">
											<td>
												<label class="rtwwdpd_label"><?php esc_html_e('Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</label>
											</td>
											<td>
												<input type="number" class="rtwwdpd_min_purchase" name="rtwwdpd_min_purchase" id="rtwwdpd_min_purchase" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_purchase']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_purchase']) : ''; ?>">
												</i>
											</td>
										</tr>
									</table>
		
									<?php 
									// $bogo_purchase_product = apply_filters('rtwwdpd_show_bogo_purchase_product_edit', $_GET['editbogo']);
									// if($bogo_purchase_product != $_GET['editbogo'])
									// {
									// 	echo $bogo_purchase_product;
									// }
									?>
									
									<h3 class="rtw_tbltitle rtwwdpd_show_purchase_prod"><?php esc_html_e('Product Need to be Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
									<table id="rtwproduct_table" class="rtwwdpd_show_purchase_prod">
										<thead>
											<tr>
												<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header"><?php esc_html_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											
												<th class="rtwtable_header"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											</tr>
										</thead>
										<tbody id="product_list_body">
											<?php
											if(isset($rtwwdpd_prev_prod['product_id']) && is_array($rtwwdpd_prev_prod['product_id']) && !empty($rtwwdpd_prev_prod['product_id'])){
											foreach ($rtwwdpd_prev_prod['product_id'] as $key => $val) {
											?>
											<tr>
												<td id="td_row_no"><?php echo ($key +1)?></td>
												<td id="td_product_name">
													<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class"  data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
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
												<input type="number" min="1" name="combi_quant[]" value="<?php echo isset($rtwwdpd_prev_prod['combi_quant'][$key]) ? $rtwwdpd_prev_prod['combi_quant'][$key] : ''; ?>"  />
											</td>
											<td id="td_remove">
												<a class="button insert remove" name="deletebtn" id="deletebtn" ><?php esc_html_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
										</tr><?php } } else{?>
										<tr>
											<td id="td_row_no">1</td>
											<td id="td_product_name">
												<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
												</select>
												<?php
												$rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
	                             
												foreach ($rtwwdpd_product_ids as $product_id) {
													$product = wc_get_product($product_id);
													if (is_object($product)) {
													echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
													}
												}
													?>
												</select>
											</td>
											<td id="td_quant">
												<input type="number" min="0" name="combi_quant[]" value=""  />
											</td>
											<td id="td_remove">
												<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
										</tr>
										<?php } ?>	
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">
												<a  class="button insert" name="rtwnsertbtn" id="rtwinsertbtnbogo" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
										</tr>
									</tfoot>
								</table>

								<h3 class="rtw_tbltitle"><?php esc_html_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
							  <div class="rtw_tbl_edit_free_pro">	
								<table id="rtwbogo_table_pro">
									<thead>
										<tr>
											<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<?php
                                                	$disnt_head_edit='';
													$disnt_head_edit =apply_filters('show_disnt_heading_edit',$disnt_head_edit);
													echo $disnt_head_edit;
												 ?>
											<th class="rtwtable_header"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="rtw_bogo_row">
										<?php
										if(isset($rtwwdpd_prev_prod['rtwbogo']) && is_array($rtwwdpd_prev_prod['rtwbogo']) && !empty($rtwwdpd_prev_prod['rtwbogo']))
										{
									
										foreach ($rtwwdpd_prev_prod['rtwbogo'] as $key => $val) {
											
											?>
											<tr>
												<td id="td_row_no"><?php echo ($key +1)?></td>
												<td id="td_product_name">

													<select id="rtwproduct" name="rtwbogo[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
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
													<input type="number" min="0" name="bogo_quant_free[]" value="<?php echo isset($rtwwdpd_prev_prod['bogo_quant_free'][$key]) ? esc_attr($rtwwdpd_prev_prod['bogo_quant_free'][$key]) : ''; ?>" id="edit_bogo_pro_quant" />
												</td>   
											<!---------- start   extra field for discount type 
								              -->
													<?php									
													$disnt_html_edit="";
													$disnt_html_edit=apply_filters('rtw_add_discnt_type_edit',$disnt_html_edit);
													echo $disnt_html_edit;
													?>
										<!---------- End   extra field for discount type               -->

												<td id="td_remove">
													<a class="button insert remove" name="deletebtn" id="deletebtn" ><?php esc_html_e('Remove', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
												</td>
											</tr><?php }} else { ?>
											<tr>
												<td id="td_row_no">1</td>
												<td id="td_product_name">
													<select id="rtwproduct" name="rtwbogo[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
													</select>
													<?php
													$rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
			                        
													foreach ($rtwwdpd_product_ids as $product_id) {
															$product = wc_get_product($product_id);
															if (is_object($product)) {
																echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
															}
														}
														?>
													</select>
												</td>
												<td id="td_quant">
													<input type="number" min="0" name="bogo_quant_free[]" value=""  />
												</td>
												<td id="td_remove">
													<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</td>
											</tr>
											<?php }?>	
										</tbody>
										<tfoot>
											<tr>
												<td colspan=3>
													<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_bogo_pro" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
												</td>
											</tr>
										</tfoot>
									</table>
								  </div>
								</div>

								<div class="options_group rtwwdpd_inactive" id="rtwbogo_restrict_tab">
									<table class="rtwwdpd_table_edit">
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

											$rtwwdpd_selected_mail =  isset($rtwwdpd_prev_prod['rtwwdpd_select_emails']) ? $rtwwdpd_prev_prod['rtwwdpd_select_emails'] : array(); 
											?>
											<label class="rtwwdpd_label"><?php esc_html_e('Restrict User with Email ID', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select class="rtwwdpd_select_roles" name="rtwwdpd_select_emails[]" multiple>
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
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on the order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwbogo_validity_tab">
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
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e(' Based On Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="checkbox" name="rtwwdpd_enable_day_bogo" value="yes" class="rtwwdpd_day_chkbox" <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_enable_day_bogo']) ? $rtwwdpd_prev_prod['rtwwdpd_enable_day_bogo'] : '', 'yes'); ?>/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Check If You want to Set Discount on Speciifc Day.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											
											</i>
										</td>
									</tr>
									<tr class="rtwwdpd_daywise_rule_row">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select class="rtwwdpd_select_day_bogo" name="rtwwdpd_select_day_bogo">
												
												<option value="">
													<?php esc_html_e( '-- Select --', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="7"  <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 7) ?>>
													<?php esc_html_e( 'Sunday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="1" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 1) ?>>
													<?php esc_html_e( 'Monday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="2" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 2) ?>>
													<?php esc_html_e( 'Tuesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="3" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 3) ?>>
													<?php esc_html_e( 'Wednesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="4" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 4) ?>>
													<?php esc_html_e( 'Thursday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="5" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 5) ?>>
													<?php esc_html_e( 'Friday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="6" <?php selected($rtwwdpd_prev_prod['rtwwdpd_select_day_bogo'], 6) ?>>
													<?php esc_html_e( 'Saturday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_bog_rul" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_bogo_rule" name="rtwwdpd_save_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_bogo_cat.php' );
	include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_bogo_tag.php' ); ?>
</div>
<?php }
else { ?>
	<div class="rtwwdpd_single_bogo_rule_tab rtwwdpd_inactive rtwwdpd_form_layout_wrapper">
		<form action="" method="POST" accept-charset="utf-8">
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_bogo_rule_tab active">
								<a class="rtwwdpd_link" id="rtwbogo_rule">
									<span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_restriction_tab">
								<a class="rtwwdpd_link" id="rtwbogo_restrict">
									<span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li class="rtwwdpd_time_tab">
								<a class="rtwwdpd_link" id="rtwbogo_validity">
									<span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwbogo_rule_tab">
								<input type="hidden" id="edit_chk_bogo" name="edit_chk_bogo" value="save">
								<table class="rtwwdpd_table_edit">
									<?php 
										$daywise_discount = apply_filters('rtwwdpd_bogo_daywise_discount', '');
										echo $daywise_discount;
									?>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
										</td>
										<td>
											<input type="text" name="rtwwdpd_bogo_offer_name" placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>" required="required" value="">

											<i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											<b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
											</i>
										</td>
									</tr>
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Rule on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select class="rtwwdpd_bogo_rule_on" name="rtwwdpd_bogo_rule_on" id="rtwwdpd_bogo_rule_on">
												<option value="product"><?php esc_html_e( 'Selected Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
												<option value="min_purchase"><?php esc_html_e( 'Minimum Purchase', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
											</select>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Rule to be applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
									<tr id="mini_pur_amount">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="number" class="rtwwdpd_min_purchase" name="rtwwdpd_min_purchase" id="rtwwdpd_min_purchase">
											</i>
										</td>
									</tr>
									<?php 
									// $bogo_purchase_product = apply_filters('rtwwdpd_show_bogo_purchase_product', '');
									// echo $bogo_purchase_product;
									?>
									
								</table>
								<h3 class="rtw_tbltitle rtwwdpd_show_purchase_prod"><?php esc_attr_e('Product Need to be Purchased', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
								<table id="rtwproduct_table" class="rtwwdpd_show_purchase_prod">
									<thead>
										<tr>
											<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											<th class="rtwtable_header"><?php esc_html_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
										</tr>
									</thead>
									<tbody id="product_list_body ">
										<tr>
											<td id="td_row_no">1</td>
											<td id="td_product_name">
												<select id="rtwproduct" name="product_id[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
												</select>
												<?php
												////
												$rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
	                             
												foreach ($rtwwdpd_product_ids as $product_id) {
													$product = wc_get_product($product_id);
													if (is_object($product)) {
													echo '<option value="' . esc_attr($product_id) . '">' . wp_kses_post($product->get_formatted_name()) . '</option>';
														}
													}
													?>
												</select>
											</td>
											<td id="td_quant">
												<input type="number" min="1" name="combi_quant[]" value="1" />
											</td>
											
											<td id="td_remove">
												<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</td>
											
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">
												<a  class="button insert" name="rtwnsertbtn" id="rtwinsertbtnbogo" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
											</td>
										</tr>
									</tfoot>
								</table>

								<h3 class="rtw_tbltitle"><?php esc_attr_e('Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></h3>
								<div class="free_product_table">
									<table id="rtwbogo_table_pro">
										<thead>
											<tr>
												<th class="rtwtable_header"><?php esc_attr_e('Row no', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header"><?php esc_attr_e('Product Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
												<th class="rtwtable_header rtw_qty_width"><?php esc_attr_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
                                                 <?php
                                                $disnt_head='';

													$disnt_head =apply_filters('show_disnt_heading',$disnt_head);
													echo $disnt_head;

												 ?>
												<th class="rtwtable_header"><?php esc_attr_e('Remove Item', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
											</tr>
										</thead>
										<tbody id="rtw_bogo_row asdsadsadsadsa">
											<tr>
												<td id="td_row_no">1</td>
												<td id="td_product_name">
													<select id="rtwproduct" name="rtwbogo[]" class="wc-product-search rtwwdpd_prod_tbl_class" data-placeholder="Search for a product" data-action="woocommerce_json_search_products_and_variations" data-multiple="false" >
													</select>
													<?php
													$rtwwdpd_product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array(); 
									
													foreach ($rtwwdpd_product_ids as $product_id) {
															$product = wc_get_product($product_id);
															if (is_object($product)) {
																echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
															}
														}
													// 	?>
													</select>
												</td>
												<td id="td_quant">
													<input type="number" min="1" name="bogo_quant_free[]" value="1" id="free_pro_quant"  />
												</td>

											<!---------- start   extra field for discount type 
								              -->
													<?php
													$disnt_html="";
													$disnt_html=apply_filters('rtw_add_discnt_type',$disnt_html);
													echo $disnt_html;
													?>
										<!---------- End   extra field for discount type               -->
												<td id="td_remove">
													<?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td colspan=3>
													<a  class="button insert" name="rtwnsertbtn" id="rtwinsert_bogo_pro" ><?php esc_html_e('Add Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwbogo_restrict_tab">
								<table class="rtwwdpd_table_edit">
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
											<select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]" multiple="multiple">
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
											<select class="rtwwdpd_select_roles" name="rtwwdpd_select_emails[]" multiple>
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
											<i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on the order to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</i>
										</td>
									</tr>
								</table>
							</div>

							<div class="options_group rtwwdpd_inactive" id="rtwbogo_validity_tab">
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
									<tr>
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e(' Based On Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<input type="checkbox" name="rtwwdpd_enable_day_bogo" value="yes" class="rtwwdpd_day_chkbox"/>
											<i class="rtwwdpd_description"><?php esc_html_e( 'Check If You want to Set Discount on Speciifc Day.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											
											</i>
										</td>
									</tr>
									<tr class="rtwwdpd_daywise_rule_row">
										<td>
											<label class="rtwwdpd_label"><?php esc_html_e('Day', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
											</label>
										</td>
										<td>
											<select class="rtwwdpd_select_day_bogo" name="rtwwdpd_select_day_bogo">
												
												<option value="">
													<?php esc_html_e( '-- Select --', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="7">
													<?php esc_html_e( 'Sunday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="1">
													<?php esc_html_e( 'Monday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="2">
													<?php esc_html_e( 'Tuesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="3">
													<?php esc_html_e( 'Wednesday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="4">
													<?php esc_html_e( 'Thursday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="5">
													<?php esc_html_e( 'Friday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												<option value="6">
													<?php esc_html_e( 'Saturday', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
												</option>
												
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="rtwwdpd_prod_single_save_n_cancel rtwwdpd_btn_save_n_cancel">
				<input class="rtw-button rtwwdpd_save_rule rtwwdpd_bog_rul" type="button" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input id="submit_bogo_rule" name="rtwwdpd_save_rule" type="submit" hidden="hidden"/>
				<input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_bogo_cat.php' );
	include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_bogo_tag.php' ); ?>
</div>
<?php } 
if(isset($_GET['editbogo']) && !isset($_GET['editbcat']))
{
	echo '<div class="rtwwdpd_bogo_edit_table rtwwdpd_active">';
}
elseif(isset($_GET['editbcat']))
{
	echo '<div class="rtwwdpd_bogo_table rtwwdpd_inactive">';
}else{
	echo '<div class="rtwwdpd_bogo_table">';
}
$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no'); ?>
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="bogo_tbl" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Purchased Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Purchased Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				
				<?php 
				if($active_dayss == 'yes')
				{ ?>
				<th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php }?>
				<?php
					$rtwwdpd_percent_html='';	
					$rtwwdpd_percent_html=apply_filters('show_disnt_percent',$rtwwdpd_percent_html);
					echo $rtwwdpd_percent_html;
				?>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
			</tr>
		</thead>
		<?php
		$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');
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

		if(is_array($rtwwdpd_products_option) &&  !empty($rtwwdpd_products_option)) { ?>
			<tbody>
				<?php
				foreach ($rtwwdpd_products_option as $key => $value) {
					echo '<tr data-val="'.$key.'">';

					echo '<td class="rtwrow_no">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
					echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ).'"/></td>';

					echo '<td>'.(isset($value['rtwwdpd_bogo_offer_name']) ? esc_html__($value['rtwwdpd_bogo_offer_name'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>';
					// $product_spend = apply_filters('rtwwdpd_either_product_or_spend', $key);
					if(isset($value['rtwwdpd_bogo_rule_on']) && $value['rtwwdpd_bogo_rule_on'] == 'product')
					// if( $product_spend === $key )
					{
						if(isset($value['product_id']) && is_array($value['product_id']) && !empty($value['product_id']))
						{
							foreach ($value['product_id'] as $val) {
								
								echo esc_html__( get_the_title( $val ), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
							}
						}
					}elseif(!isset($value['rtwwdpd_bogo_rule_on']))
					{
						if(isset($value['product_id']) && is_array($value['product_id']) && !empty($value['product_id']))
						{
							foreach ($value['product_id'] as $val) {
								
								echo esc_html__( get_the_title( $val ), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
							}
						}
					}elseif(isset($value['rtwwdpd_bogo_rule_on']) && $value['rtwwdpd_bogo_rule_on'] == 'min_purchase'){
						esc_html_e( 'On minimum purchase amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_bogo_rule_on']) && $value['rtwwdpd_bogo_rule_on'] == 'product')
					// if( $product_spend === $key )
					{
						if(isset($value['combi_quant']) && is_array($value['combi_quant']) && !empty($value['combi_quant']))
						{
							foreach ($value['combi_quant'] as $val) {
								echo esc_html__( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
							}
						}
					}elseif(!isset($value['rtwwdpd_bogo_rule_on']))
					{
						if(isset($value['combi_quant']) && is_array($value['combi_quant']) && !empty($value['combi_quant']))
						{
							foreach ($value['combi_quant'] as $val) {
								echo esc_html__( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
							}
						}
					}elseif(isset($value['rtwwdpd_bogo_rule_on']) && $value['rtwwdpd_bogo_rule_on'] == 'min_purchase'){
						esc_html_e( $value['rtwwdpd_min_purchase'].' (Amount)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwbogo']) && is_array($value['rtwbogo']) && !empty($value['rtwbogo']))
					{
						foreach ($value['rtwbogo'] as $val) {
							echo esc_html__(get_the_title( $val ), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['bogo_quant_free']) && is_array($value['bogo_quant_free']) && !empty($value['bogo_quant_free']))
					{
						foreach ($value['bogo_quant_free'] as $val) {
							echo esc_html__( $val , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					
					echo '</td>';
                    
					////////////////// start percent discount value in edit datatable ansh  //////////////////////
					
						$disnt_html_val="";
						$disnt_html_val=apply_filters('show_disnt_value',$disnt_html_val,$key);
						echo $disnt_html_val;
					   
					////////////////////  end percent discount value in edit datatable ansh  //////////////////////////////////////////////////////
					if($active_dayss == 'yes')
					{
						echo '<td>';
						if(isset($value['rtwwwdpd_bogo_day']) && is_array($value['rtwwwdpd_bogo_day']) && !empty($value['rtwwwdpd_bogo_day']))
						{
							if(in_array(1, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Mon, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(2, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Tue, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(3, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Wed, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(4, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Thu, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(5, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Fri, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(6, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Sat, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
							if(in_array(7, $value['rtwwwdpd_bogo_day']))
							{
								esc_html_e('Sun', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
							}
						}
						echo '</td>';
					}

					echo '<td>';
					if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
					{
						foreach ($value['rtwwdpd_select_roles'] as $val) {
							echo esc_html__( $rtwwdpd_roles[$val] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br>';
						}
					}
					else{
						esc_html_e('All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
					}
					echo '</td>';

					echo '<td>';
					if(isset($value['rtwwdpd_select_emails']) && is_array($value['rtwwdpd_select_emails']) && !empty($value['rtwwdpd_select_emails']))
					{
						foreach ($value['rtwwdpd_select_emails'] as $val) {
							echo esc_html__($all_users_mail[$val], "rtwwdpd-woo-dynamic-pricing-discounts-with-ai").', ';
						}
					}
					else{
						echo '';
					}
					echo '</td>';
					
					echo '<td>'.(isset($value['rtwwdpd_bogo_min_orders']) ? esc_html__($value['rtwwdpd_bogo_min_orders'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_bogo_min_spend']) ? esc_html__($value['rtwwdpd_bogo_min_spend'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_bogo_from_date']) ? esc_html__($value['rtwwdpd_bogo_from_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td>'.(isset($value['rtwwdpd_bogo_to_date']) ? esc_html__($value['rtwwdpd_bogo_to_date'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

					echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editbogo='.$key ).'"><input type="button" class="rtw_edit_bogo rtwwdpd_edit_dt_row" value="'.esc_html__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
					<a href="'.esc_url( $rtwwdpd_absolute_url .'&delbr='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_html__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
				<th><?php esc_html_e( 'Purchased Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Purchased Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Free Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				
				<?php 
				if($active_dayss == 'yes')
				{ ?>
				<th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<?php }?>
				<?php
					$rtwwdpd_percent_html='';	
					$rtwwdpd_percent_html=apply_filters('show_disnt_percent',$rtwwdpd_percent_html);
					echo $rtwwdpd_percent_html;
				?>
				<th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
				<th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
