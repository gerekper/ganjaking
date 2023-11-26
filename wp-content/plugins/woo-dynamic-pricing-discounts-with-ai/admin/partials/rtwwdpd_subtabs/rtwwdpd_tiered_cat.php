<?php
if(isset($_GET['deltcat']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
	$rtwwdpd_row_no = sanitize_post($_GET['deltcat']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_tiered_cat',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_tiered_rules';
	header('Location: '.$rtwwdpd_new_url);
	die();
}
$abcd = 'verification_done';
if(isset($_POST['rtwwdpd_tiered_cat'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_t_cat']);
	$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
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
		unset($_REQUEST['tier_cat']);
		$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
	}
	else{
		$rtwwdpd_products_option[] = $rtwwdpd_products;
	}

	update_option('rtwwdpd_tiered_cat',$rtwwdpd_products_option);

	?>
<div class="notice notice-success is-dismissible">
    <p><strong><?php esc_html_e('Rule saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
    <button type="button" class="notice-dismiss">
        <span
            class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
    </button>
</div><?php
}

if(isset($_POST['rtwwdpd_copy_combi_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
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
	
	update_option('rtwwdpd_tiered_cat',$rtwwdpd_products_option);

	?>
<div class="notice notice-success is-dismissible">
    <p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
    <button type="button" class="notice-dismiss">
        <span
            class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
    </button>
</div><?php
}

$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$abcd, array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
if(isset($_GET['tier_cat']))
{
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
	$rtwwdpd_prev_prod = $rtwwdpd_products_option[$_GET['tier_cat']];
	$key = 'tier_cat';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_tiered_rules');
	?>
<div class="rtwwdpd_add_tier_cat_rule_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">
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
                        <li class="rtwwdpd_restriction_tab_combi">
                            <a class="rtwwdpd_link" id="rtwproduct_restrict_combi">
                                <span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                            </a>
                        </li>
                        <li class="rtwwdpd_time_tab_combi">
                            <a class="rtwwdpd_link" id="rtwproduct_validity_combi">
                                <span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                            </a>
                        </li>
                    </ul>

                    <div class="panel woocommerce_options_panel">
                        <div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
                            <input type="hidden" id="edit_t_cat" name="edit_t_cat"
                                value="<?php echo esc_attr($_GET['tier_cat']); ?>" />
                            <table class="rtwwdpd_table_edit">
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="text" name="rtwwdpd_offer_name"
                                            placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>"
                                            required="required"
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_name']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_name']) : ''; ?>" />

                                        <i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <select name="rtwwdpd_discount_type">
                                            <option value="rtwwdpd_discount_percentage"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_discount_percentage'); ?>>
                                                <?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </option>
                                            <option value="rtwwdpd_flat_discount_amount"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_flat_discount_amount'); ?>>
                                                <?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </option>
                                            <option value="rtwwdpd_fixed_price"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_discount_type'], 'rtwwdpd_fixed_price'); ?>>
                                                <?php esc_html_e( 'Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </option>
                                        </select>
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Choose discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <select name="rtwwdpd_check_for" id="rtwwdpd_check_for">
                                            <option value="rtwwdpd_quantity"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_quantity');?>>
                                                <?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                            <option value="rtwwdpd_price"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_price');?>>
                                                <?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                            <option value="rtwwdpd_weight"
                                                <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for'], 'rtwwdpd_weight');?>>
                                                <?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                        </select>
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Left column background color', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="color" name="rtwwdpd_offer_header_color"
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_header_color']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_header_color']) : ''; ?>">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Select color for offer message left column color.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Right Column Color', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="color" name="rtwwdpd_offer_right_col_color"
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_offer_right_col_color']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_offer_right_col_color']) : ''; ?>">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Select color for offer message right coulmn.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <!-- ///  -->
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Category on which this rule is apply ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select required name="category_id[]" id="rtwwdpd_category_id"
                                            class="wc-enhanced-select rtwwdpd_prod_class" multiple
                                            data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                            <?php
                               
                                             $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
                                    
                                             if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) 
                                             {
                                                foreach ($rtwwdpd_categories as $cat) 
                                                {
                                                   echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_prev_prod['category_id']),true,true) . '>' . esc_html($cat->name) . '</option>';
                                                }
                                             }
                                             ?>
                                        </select>
                                        <i class="rtwwdpd_description">
                                            <?php esc_html_e('Select Category on which discount apply.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            <b class="rtwwdpd_required">
                                                <?php esc_html_e('Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </b>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <!-- <div>
                                <h4 id="rtw_ab">
                                    <a><?php esc_html_e('Select Category on which this rule is apply ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                    </a>
                                    <select name="category_id[]" id="rtwwdpd_category_id"
                                        class="wc-enhanced-select rtwwdpd_prod_class" multiple
                                        data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                        <?php
                               
                                 $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
                              
                                 if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) 
                                 {
                                 	foreach ($rtwwdpd_categories as $cat) 
                                    {
                                 		echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_prev_prod['category_id']),true,true) . '>' . esc_html($cat->name) . '</option>';
                                 	}
                                 }
                                 ?>
                                    </select>
                                </h4>
                            </div> -->
                            <table id="rtwtiered_tbl_cat">
                                <thead>
                                    <tr>
                                        <th class="rtwtable_header">
                                            <?php esc_attr_e('Tiers', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header"><a
                                                class="rtwtiered_chk_for"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </a><?php esc_html_e('Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header"><a
                                                class="rtwtiered_chk_for"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </a><?php esc_html_e('Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header">
                                            <div id="rtw_header">
                                                <?php esc_html_e('Discount Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </div>
                                        </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="product_cat_tier">
                                    <?php
                              foreach ($rtwwdpd_prev_prod['quant_min'] as $rows => $row) {?>
                                    <tr>
                                        <td id="td_product_name">
                                            <?php esc_html_e('Tier '.($rows+1), 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </td>
                                        <td id="td_quant">
                                            <input type="number" min="1" name="quant_min[]"
                                                value="<?php echo esc_attr($row); ?>" />
                                        </td>
                                        <td id="td_quant">
                                            <input type="number" class="quant_c_max max" min="1" name="quant_max[]"
                                                value="<?php echo esc_attr($rtwwdpd_prev_prod['quant_max'][$rows]); ?>" />
                                        </td>
                                        <td>
                                            <input type="number" min="0.1" step="0.01" name="discount_val[]"
                                                value="<?php echo esc_attr($rtwwdpd_prev_prod['discount_val'][$rows]); ?>" />
                                        </td>
                                        <td id="td_remove">
                                            <?php 
                                    if($rows == 0)
                                    {
                                       esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); 
                                    }
                                    else{
                                       echo '<a class="button insert rtw_remov_tier_cat" name="deletebtn" >Remove</a>';
                                    }?>
                                        </td>
                                    </tr>
                                    <?php }  ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan=3>
                                            <a class="button insert" name="rtwnsertbtn"
                                                id="rtwadd_tiered_cat"><?php esc_html_e('Add Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
                            <table class="rtwwdpd_table_edit">
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
                                    </td>
                                    <td>
                                        <select class="wc-product-search rtwwdpd_prod_class" multiple="multiple"
                                            name="product_exe_id[]"
                                            data-action="woocommerce_json_search_products_and_variations"
                                            placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>">
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number" required
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_discount']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_discount']) : '' ; ?>"
                                            min="0" name="rtwwdpd_max_discount">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                 global $wp_roles;
                                 $rtwwdpd_roles    = $wp_roles->get_names();
                                 $rtwwdpd_role_all    = esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                                 $rtwwdpd_roles    = array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); 
                                 $rtwwdpd_selected_role =  $rtwwdpd_prev_prod['rtwwdpd_select_roles']; ?>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]"
                                            multiple="multiple">
                                            <?php
                                    foreach ($rtwwdpd_roles as $roles => $role) {
                                       if(is_array($rtwwdpd_selected_role))
                                       {
                                          ?>
                                            <option value="<?php echo esc_attr($roles); ?>" <?php
                                             foreach ($rtwwdpd_selected_role as $ids => $roleid) {
                                                selected($roles, $roleid);
                                             }
                                          ?>>
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
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number"
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_orders']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_orders']) : '' ; ?>"
                                            min="0" name="rtwwdpd_min_orders">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Minimum number of orders done by a customer to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Minimum amount spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number"
                                            value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_spend']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_spend']) : '' ; ?>"
                                            min="0" name="rtwwdpd_min_spend">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Minimum amount need to be spent by a customer on previous orders to be eligible for this discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="yes" name="rtwwdpd_exclude_sale"
                                            <?php checked(isset($rtwwdpd_prev_prod['rtwwdpd_exclude_sale']) ? 'yes' : 'no', 'yes'); ?> />
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="date" name="rtwwdpd_frm_date_c" placeholder="YYYY-MM-DD"
                                            required="required"
                                            value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_frm_date_c']); ?>" />
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'The date from which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Valid To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="date" name="rtwwdpd_to_date_c" placeholder="YYYY-MM-DD"
                                            required="required"
                                            value="<?php echo esc_attr( $rtwwdpd_prev_prod['rtwwdpd_to_date_c']); ?>" />
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
            <input class="rtw-button rtwwdpd_save_rule rtwwdpd_tiercat_save" type="button"
                value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
            <input id="submit_tiercat_rule" name="rtwwdpd_tiered_cat" type="submit" hidden="hidden" />
            <input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule"
                value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
        </div>
    </form>
</div>
<?php 
}else{ ?>
<div class="rtwwdpd_add_tier_cat_rule_tab rtwwdpd_inactive rtwwdpd_form_layout_wrapper">
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
                        <li class="rtwwdpd_restriction_tab_combi">
                            <a class="rtwwdpd_link" id="rtwproduct_restrict_combi">
                                <span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                            </a>
                        </li>
                        <li class="rtwwdpd_time_tab_combi">
                            <a class="rtwwdpd_link" id="rtwproduct_validity_combi">
                                <span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                            </a>
                        </li>
                    </ul>

                    <div class="panel woocommerce_options_panel">
                        <div class="options_group rtwwdpd_active" id="rtwwdpd_rule_tab_combi">
                            <input type="hidden" id="edit_t_cat" name="edit_t_cat" value="save" />
                            <table class="rtwwdpd_table_edit">
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Offer Name', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="text" name="rtwwdpd_offer_name"
                                            placeholder="<?php esc_html_e('Enter title for this offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>"
                                            required="required" value="" />

                                        <i class="rtwwdpd_description"><?php esc_html_e( 'This title will be displayed in the Offer listings.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <select name="rtwwdpd_check_for" id="rtwwdpd_check_for">
                                            <option value="rtwwdpd_quantity">
                                                <?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                            <option value="rtwwdpd_price">
                                                <?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                            <option value="rtwwdpd_weight">
                                                <?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </option>
                                        </select>
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Left Column Color', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="color" name="rtwwdpd_offer_header_color" required="required"
                                            value="#FFFFFF">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Select color for offer message left Column Color.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Right Column Color', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                    </td>
                                    <td>
                                        <input type="color" name="rtwwdpd_offer_right_col_color" required="required"
                                            value="#FFFFFF">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Select color for offer message right coulmn.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <!-- //// -->
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Select Category on which this rule is apply', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?>
                                          </label>
                                    </td>
                                    <td>
                                    <select required name="category_id[]" id="rtwwdpd_category_id"
                                        class="wc-enhanced-select rtwwdpd_prod_class" multiple
                                        data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                        <?php
                                       
                                       $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
                                       if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
                                       $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

                                       if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories))  {
                                          foreach ($rtwwdpd_categories as $cat) {
                                             echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_category_ids), true, true) . '>' . esc_html($cat->name) . '</option>';
                                          }
                                       }
                                       ?>
                                    </select>
                                    <i class="rtwwdpd_description">
                                        <?php esc_html_e('Select Category on which discount apply.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        <b class="rtwwdpd_required">
                                            <?php esc_html_e('Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </b>
                                    </i>
                                    </td>
                                </tr>
                            </table>                    
                            <!-- <div>
                                <h4 id="rtw_ab">
                                    <a><?php esc_html_e('Select Category on which this rule is apply : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?></a>
                                    <select name="category_id[]" id="rtwwdpd_category_id"
                                        class="wc-enhanced-select rtwwdpd_prod_class" multiple
                                        data-placeholder="<?php esc_attr_e('Select category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>">
                                        <?php
                              $rtwwdpd_category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : array();  //selected product categorie
                              if(!is_array($rtwwdpd_category_ids)) $rtwwdpd_category_ids=array($rtwwdpd_category_ids);
                              $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

                              if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories))  {
                              	foreach ($rtwwdpd_categories as $cat) {
                              		echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id,$rtwwdpd_category_ids), true, true) . '>' . esc_html($cat->name) . '</option>';
                              	}
                              }
                              ?>
                                    </select>
                                </h4>
                            </div> -->
                            <table id="rtwtiered_tbl_cat">
                                <thead>
                                    <tr>
                                        <th class="rtwtable_header">
                                            <?php esc_attr_e('Tiers', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header"><a
                                                class="rtwtiered_chk_for"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </a><?php esc_html_e('Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header"><a
                                                class="rtwtiered_chk_for"><?php esc_html_e('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </a><?php esc_html_e('Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </th>
                                        <th class="rtwtable_header">
                                            <div id="rtw_header">
                                                <?php esc_html_e('Discount Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                            </div>
                                        </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="product_cat_tier">
                                    <tr>
                                        <td id="td_product_name">
                                            <?php esc_html_e('Tier 1', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </td>
                                        <td id="td_quant">
                                            <input type="number" min="1" name="quant_min[]" value="1" />
                                        </td>
                                        <td id="td_quant">
                                            <input type="number" class="quant_c_max max" min="1" name="quant_max[]"
                                                value="2" />
                                        </td>
                                        <td>
                                            <input type="number" min="0.1" step="0.01" name="discount_val[]"
                                                value="1" />
                                        </td>
                                        <td id="td_remove">
                                            <?php esc_html_e('Min One product.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan=3>
                                            <a class="button insert" name="rtwnsertbtn"
                                                id="rtwadd_tiered_cat"><?php esc_html_e('Add Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="options_group rtwwdpd_inactive" id="rtwwdpd_restriction_tab_combi">
                            <table class="rtwwdpd_table_edit">
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Exclude Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></label>
                                    </td>
                                    <td>
                                        <select class="wc-product-search rtwwdpd_prod_class" multiple="multiple"
                                            name="product_exe_id[]"
                                            data-action="woocommerce_json_search_products_and_variations"
                                            placeholder="<?php esc_html_e('Search for a product','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>">
                                        </select>
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Exclude products form this rule.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Maximum Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number" required value="" min="0" name="rtwwdpd_max_discount">
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'This is used to set a threshold limit on the discount.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                              global $wp_roles;
                              $rtwwdpd_roles    = $wp_roles->get_names();
                              $rtwwdpd_role_all    = esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                              $rtwwdpd_roles    = array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );?>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select required class="rtwwdpd_select_roles" name="rtwwdpd_select_roles[]"
                                            multiple="multiple">
                                            <?php 
                                 foreach ($rtwwdpd_roles as $roles => $role) { ?>
                                            <option value="<?php echo esc_attr($roles); ?>">
                                                <?php esc_html_e( $role, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'Select user role for this offer.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Minimum orders done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Minimum amount spend', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Exclude sale items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="yes" name="rtwwdpd_exclude_sale" />
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
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Valid from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="date" name="rtwwdpd_frm_date_c" placeholder="YYYY-MM-DD"
                                            required="required" value="" />
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'The date from which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                        </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label
                                            class="rtwwdpd_label"><?php esc_html_e('Valid To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="date" name="rtwwdpd_to_date_c" placeholder="YYYY-MM-DD"
                                            required="required" value="" />
                                        <i class="rtwwdpd_description"><?php esc_html_e( 'The date till which the rule would be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            <b
                                                class="rtwwdpd_required"><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
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
            <input class="rtw-button rtwwdpd_save_rule rtwwdpd_tiercat_save" type="button"
                value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
            <input id="submit_tiercat_rule" name="rtwwdpd_tiered_cat" type="submit" hidden="hidden" />
            <input class="rtw-button rtwwdpd_cancel_rule" type="button" name="rtwwdpd_cancel_rule"
                value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
        </div>
    </form>
</div>
<?php } 
if(isset($_GET['tier_cat']))
{
	echo '<div id="rtwwdpd_edit_combi_prod" class="rtwwdpd_prod_c_table_edit rtwwdpd_tier_c_table rtwwdpd_active">';
}
else{
	echo '<div class="rtwwdpd_tier_c_table">';
}
?>
<table class="rtwtables table table-striped table-bordered dt-responsive nowrap" data-value="tier_cat_tbl"
    cellspacing="0">
    <thead>
        <tr>

            <th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Check For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Min - Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'From', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'To', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Min Order Count', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Min Order Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Exclude Sale Items', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
        </tr>
    </thead>
    <?php
   	$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
   	$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
   	if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)) { ?>
    <tbody>
        <?php
   			$cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
   			$products = array();
   			foreach ($cat as $value) {
   				$products[$value->term_id] = $value->name;
   			}
   			foreach ($rtwwdpd_products_option as $key => $value) {
   				echo '<tr data-val="'.$key.'">';

   				echo '<td class="rtwrow_nos">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_combi_rule" value="Copy"></form></td>';
   				echo '<td class="rtw_drags"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';

   				echo '<td>'.esc_html($value['rtwwdpd_offer_name'] ).'</td>';

   				echo'<td>';
   				if(isset($value['category_id']) && is_array($value['category_id']) && !empty($value['category_id']))
   				{
   					foreach ($value['category_id'] as $val) {
   						echo esc_html__($products[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'<br> ';
   					}
   				}
   				echo '</td>';

   				if($value['rtwwdpd_check_for'] == 'rtwwdpd_price')
   				{
   					echo '<td>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				elseif($value['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
   				{
   					echo '<td>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				else{
   					echo '<td>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				echo '<td>';
   				if(isset($value['quant_min']) && is_array($value['quant_min']) && !empty($value['quant_min']))
   				{
   					foreach ($value['quant_min'] as $keys => $val) {
   						echo esc_html($val).' - '. esc_html($value['quant_max'][$keys]).'<br>';
   					}
   				}
   				echo '</td>';

   				if($value['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
   				{
   					echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				elseif($value['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
   				{
   					echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				else{
   					echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
   				}
   				echo '<td>';
               if(isset($value['discount_val']) && is_array($value['discount_val']) && !empty($value['discount_val']))
               {
      				foreach ($value['discount_val'] as $val) {
      					echo esc_html($val).'<br>';
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

   				echo '<td>'.(isset($value['rtwwdpd_max_discount']) ? esc_html($value['rtwwdpd_max_discount'] ) : '').'</td>';

   				echo '<td>';
   				if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
   				{
   					foreach ($value['rtwwdpd_select_roles'] as $keys) {
   						echo esc_html($keys).'<br>';
   					}
   				}
   				echo '</td>';

   				echo '<td>'.(isset($value['rtwwdpd_frm_date_c']) ? esc_html($value['rtwwdpd_frm_date_c'] ) : '').'</td>';

   				echo '<td>'.(isset($value['rtwwdpd_to_date_c']) ? esc_html($value['rtwwdpd_to_date_c'] ) : '').'</td>';

   				echo '<td>'.(isset($value['rtwwdpd_min_orders']) ? esc_html($value['rtwwdpd_min_orders'] ) : '').'</td>';

   				echo '<td>'.(isset($value['rtwwdpd_min_spend']) ? esc_html($value['rtwwdpd_min_spend'] ) : '').'</td>';

   				echo '<td>';
   				if(isset($value['rtwwdpd_exclude_sale']))
   				{
   					echo esc_html($value['rtwwdpd_exclude_sale']);
   				}else
   				{
   					esc_html_e('No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
   				}
   				echo '</td>';

   				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&tier_cat='.$key ).'"><input type="button" class="rtw_edit_tier_cat rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" /></a>
   				<a href="'.esc_url( $rtwwdpd_absolute_url .'&deltcat='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="'.esc_attr__('Delete', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'"/></a></td>';
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
            <th><?php esc_html_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Check For', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Min - Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Excluded Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
            <th><?php esc_html_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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