<?php
$rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
    if(isset($_GET['editcat']))
    {
        $rtwwdpd_cats = get_option('rtwwdpd_single_cat_rule');
        $rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
        
        $rtwwdpd_prev_prod = $rtwwdpd_cats[$_GET['editcat']];
        $edit = 'editcat';
        $filteredURL = preg_replace('~(\?|&)'.$edit.'=[^&]*~', '$1', $rtwwdpd_url);
        $rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules&rtwwdpd_sub=rtwwdpd_cat_rules');
        
        ?>
        <div class="rtwwdpd_right">
        <div class="rtwwdpd_add_buttons">
            <input class="rtw-button rtwwdpd_single_cat" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add Single Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
        </div>

        <div class="rtwwdpd_single_tag_rule rtwwdpd_active rtwwdpd_form_layout_wrapper">
            <?php 
            $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
            ?>
            <form method="post" action="<?php echo esc_url($rtwwdpd_new_url); ?>" enctype="multipart/form-data">
                <div id="woocommerce-product-data" class="postbox ">
                    <div class="inside">
                        <div class="panel-wrap product_data">
                            <ul class="product_data_tabs wc-tabs">
                                <li class="rtwwdpd_single_tag_rule_tab active">
                                    <a class="rtwwdpd_link" id="rtwtag_rule">
                                        <span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                    </a>
                                </li>
                                <li class="rtwwdpd_restriction_tab">
                                    <a class="rtwwdpd_link" id="rtwtag_restrict">
                                        <span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                    </a>
                                </li>
                                <li class="rtwwdpd_time_tab">
                                    <a class="rtwwdpd_link" id="rtwtag_validity">
                                        <span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                    </a>
                                </li>
                            </ul>

                            <div class="panel woocommerce_options_panel">
                                <div class="options_group rtwwdpd_active" id="rtwtag_rule_tab">
                                    <input type="hidden" id="rtw_save_single_tag" name="rtw_save_single_tag" value="<?php echo esc_attr($_GET['editcat']); ?>">
                                    <table class="rtwwdpd_table_edit">
                                        <?php 
                                        if($active_dayss == 'yes')
                                        {
                                            $daywise_discount = apply_filters('rtwwdpd_category_daywise_discount_edit', $_GET['editcat']);
                                            echo $daywise_discount;
                                        }
                                        ?>
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

                                <!-- extra addition in category  anshuman      -->
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php
                                                esc_html_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                                                ?>
                                                <label>
                                            </td>
                                            <td>
                                                <select id="rtwwdpd_category_on_update" name="rtwwdpd_category_on_update">
                                                    <option value="rtwwdpd_category_update"  <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_category_on_update']) ? $rtwwdpd_prev_prod['rtwwdpd_category_on_update'] : "" ,'rtwwdpd_category_update' )?>>
                                                    <?php esc_html_e( 'Selected category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
                                                    </option>
                                                    <option class="multi" value="rtwwdpd_multiple_cat_update" <?php selected(isset($rtwwdpd_prev_prod['rtwwdpd_category_on_update']) ? $rtwwdpd_prev_prod['rtwwdpd_category_on_update'] : "" ,'rtwwdpd_multiple_cat_update' ) ?> >
                                                    <?php esc_html_e( 'Multiple categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                                    </option>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Select option on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr class="rtwwdpd_cat">
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Product category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <?php
                                                    $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
                                                    $cats = array();
                                                    if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
                                                    foreach ($rtwwdpd_categories as $cat) 
                                                    {
                                                            $cats[$cat->term_id] = $cat->name;
                                                    }
                                                    }
                                        $rtwwdpd_single_select_cat =  isset($rtwwdpd_prev_prod['category_id']) ? $rtwwdpd_prev_prod['category_id'] : '';
                                                ?>
                                                <select name="category_id" id="edit_category_id" class="wc-enhanced-select rtw_class_category " >
                                                    <!-- <option value=""><?php //esc_html_e( 'Select Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option> -->
                                                <?php 
                                                foreach ($cats as $cat_key => $cat_val) {
                                            ?>
                                            <option value="<?php echo esc_attr($cat_key); ?>"<?php
                                                selected($cat_key,  $rtwwdpd_single_select_cat );
                                            ?> >
                                            <?php esc_html_e( $cat_val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </option>
                                        <?php
                                        
                                        
                                        }
                                                ?>
                                                </select>
                                            <i class="rtwwdpd_description"><?php esc_html_e( 'Select category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </i>
                                            </td>
                                        </tr>
                                        
                                        <tr class="prod_cat">
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('product categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?></label>
                                            </td>
                                            <td>
                                    <?php
                                    $rtwwdpd_mul_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

                                    $mul_cats = array();

                                    if (is_array($rtwwdpd_mul_categories) && !empty($rtwwdpd_mul_categories)) {
                                        foreach ($rtwwdpd_mul_categories as $cat) 
                                        {
                                            $mul_cats[$cat->term_id] = $cat->name;
                                        }
                                    }
                                    $rtwwdpd_selected_mul_cat =  isset($rtwwdpd_prev_prod['multiple_cat_ids']) ? $rtwwdpd_prev_prod['multiple_cat_ids'] : '';
                                    ?>
                                                <select id="rtwwdpd_checking_placeholder" class="rtwwdpd_cat_class " multiple="multiple" name="multiple_cat_ids[]" placeholder="<?php esc_html_e('Search for a category','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
                                                <?php 
                                    foreach ($mul_cats as $cat_key => $cat_val) {
                                    
                                    if(is_array($rtwwdpd_selected_mul_cat))
                                    {
                                        ?>
                                        <option value="<?php echo esc_attr($cat_key); ?>"<?php
                                            foreach ($rtwwdpd_selected_mul_cat as $id => $cat_id) {
                                            
                                            selected($cat_key, $cat_id);
                                            }
                                        ?> >
                                            <?php esc_html_e( $cat_val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </option>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <option value="<?php echo esc_attr($cat_key); ?>">
                                            <?php esc_html_e( $cat_val, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                        </option>
                                    <?php
                                        }
                                    }

                                                    // if(is_array($cats) && !empty($cats))
                                                    // {
                                                    // 	foreach ($cats as $key => $value) 
                                                    // 	{	
                                                    // 		echo '<option value="'.esc_attr($key).'">'.esc_html($cats[$key]).'</option>';	
                                                    // 	}
                                                    // }
                                                ?>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>

                                            <!-- extra addition anshuman End     -->
                                        
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <select name="rtwwdpd_check_for_cat" id="rtwwdpd_check_for_cat">
                                                    <option value="rtwwdpd_quantity" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for_cat'], 'rtwwdpd_quantity');?>><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                    <option value="rtwwdpd_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for_cat'], 'rtwwdpd_price');?>><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                    <option value="rtwwdpd_weight" <?php selected($rtwwdpd_prev_prod['rtwwdpd_check_for_cat'], 'rtwwdpd_weight');?>><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Minimum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_min_cat']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_cat']) : '' ; ?>" min="0" name="rtwwdpd_min_cat">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_max_cat']) ? esc_attr($rtwwdpd_prev_prod['rtwwdpd_max_cat']) : '' ; ?>" name="rtwwdpd_max_cat">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <select name="rtwwdpd_dscnt_cat_type">
                                                    <option value="rtwwdpd_discount_percentage" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_type'], 'rtwwdpd_discount_percentage') ?>>
                                                        <?php esc_html_e( 'Percent Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                    </option>
                                                    <option value="rtwwdpd_flat_discount_amount" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_type'], 'rtwwdpd_flat_discount_amount') ?>>
                                                        <?php esc_html_e( 'Flat Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                    </option>
                                                    <option value="rtwwdpd_fixed_price" <?php selected($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_type'], 'rtwwdpd_fixed_price') ?>>
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
                                                <input type="number" value="<?php echo isset($rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_val']) ? $rtwwdpd_prev_prod['rtwwdpd_dscnt_cat_val'] : ''; ?>" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_cat_val">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                <b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                                </i>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="options_group rtwwdpd_inactive" id="rtwtag_restriction_tab">
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
                                                $rtwwdpd_role_guest 	= esc_html__( 'Guest', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                                                $rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); 
                                                $rtwwdpd_roles 	= array_merge( array( 'guest' => $rtwwdpd_role_guest ), $rtwwdpd_roles ); 
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

                                                $rtwwdpd_selected_mail =  isset( $rtwwdpd_prev_prod['rtwwdpd_select_emails']) ? $rtwwdpd_prev_prod['rtwwdpd_select_emails'] : ''; 
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
                                <div class="options_group rtwwdpd_inactive" id="rtwtag_time_tab">
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
                    <input class="rtw-button rtwwdpd_save_tag rtwwdpd_tag_saave" type="button" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
                    <input id="submit_cat_rule" name="rtwwdpd_save_tag" type="submit" hidden="hidden"/>
                    <input class="rtw-button rtwwdpd_cancel_rule" type="submit" name="rtwwdpd_cancel_rule" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
                </div>
            </form>
        </div>
        <?php include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_subtabs/rtwwdpd_cat_combi.php' ); 
    }
    else
    {
        ?>
        <div class="rtwwdpd_right">
            <div class="rtwwdpd_add_buttons">
                <input class="rtw-button rtwwdpd_single_tag" type="button" name="rtwwdpd_single_prod_rule" value="<?php esc_attr_e( 'Add Single Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
            </div>

            <div class="rtwwdpd_single_tag_rule rtwwdpd_form_layout_wrapper">
                <?php 
                $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
                ?>
                <form method="post" action="" enctype="multipart/form-data">
                    <div id="woocommerce-product-data" class="postbox ">
                        <div class="inside">
                            <div class="panel-wrap product_data">
                                <ul class="product_data_tabs wc-tabs">
                                    <li class="rtwwdpd_single_tag_rule_tab active">
                                        <a class="rtwwdpd_link" id="rtwtag_rule">
                                            <span><?php esc_html_e('Rule','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                        </a>
                                    </li>
                                    <li class="rtwwdpd_restriction_tab">
                                        <a class="rtwwdpd_link" id="rtwtag_restrict">
                                            <span><?php esc_html_e('Restrictions','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                        </a>
                                    </li>
                                    <li class="rtwwdpd_time_tab">
                                        <a class="rtwwdpd_link" id="rtwtag_validity">
                                            <span><?php esc_html_e('Validity','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="panel woocommerce_options_panel">
                                <div class="options_group rtwwdpd_active" id="rtwtag_rule_tab">
                                    
                                    <input type="hidden" id="rtw_save_single_tag" name="rtw_save_single_tag" value="save">
                                    <table class="rtwwdpd_table_edit">
                                        <?php 
                                            $daywise_discount = apply_filters('rtwwdpd_category_daywise_discount', '');
                                            echo $daywise_discount;
                                        ?>
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
                                                
                                        <!-- extra addition in category  anshuman      -->
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php
                                                esc_html_e('To be Applied on', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                                                ?>
                                                <label>
                                            </td>
                                            <td>
                                                <select id="rtwwdpd_category_on_update" name="rtwwdpd_category_on_update">
                                                    <option value="rtwwdpd_category_update">
                                                    <?php esc_html_e( 'Selected category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>	
                                                    </option>
                                                    <option value="rtwwdpd_multiple_cat_update">
                                                    <?php esc_html_e( 'Multiple categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
                                                    </option>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Select option on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr class="rtwwdpd_cat">
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Product category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <?php
                                                    $rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
                                                    $cats = array();

                                                    if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
                                                    foreach ($rtwwdpd_categories as $cat) 
                                                    {
                                                            $cats[$cat->term_id] = $cat->name;
                                                    }
                                                    }
                                                ?>
                                                <select name="category_id" id="category_id" class="wc-enhanced-select rtw_class_category " >
                                                    <option value=""><?php esc_html_e( 'Select Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></option>
                                                <?php 
                                                    if(is_array($cats) && !empty($cats))
                                                    {
                                                        foreach ($cats as $key => $value) {
                                                            echo '<option value="' . esc_attr($key) . '">' . esc_html($cats[$key]) . '</option>';
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            <i class="rtwwdpd_description"><?php esc_html_e( 'Select category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                            </i>
                                            </td>
                                        </tr>
                                        
                                        <tr class="extra_field">
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('product categories', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?></label>
                                            </td>
                                            <td>
                                                <select id="rtwwdpd_checking_placeholder" class="rtwwdpd_cat_class " multiple="multiple" name="multiple_cat_ids[]" placeholder="<?php esc_html_e('Search for a category','rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" >
                                                <?php 
                                                    if(is_array($cats) && !empty($cats))
                                                    {
                                                        foreach ($cats as $key => $value) 
                                                        {	
                                                            echo '<option value="'.esc_attr($key).'">'.esc_html($cats[$key]).'</option>';	
                                                        }
                                                    }
                                                ?>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Category on which rule is applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>

                                            <!-- extra addition anshuman End     -->

                                        
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Check for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <select name="rtwwdpd_check_for_cat" id="rtwwdpd_check_for_cat">
                                                    <option value="rtwwdpd_quantity"><?php esc_html_e( 'Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                    <option value="rtwwdpd_price"><?php esc_html_e( 'Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                    <option value="rtwwdpd_weight"><?php esc_html_e( 'Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
                                                </select>
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Rule can be applied for either on Price/ Quantity/ Weight.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Minimum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <input type="number" value="" min="0" name="rtwwdpd_min_cat">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Minimum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Maximum ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <input type="number" value="" name="rtwwdpd_max_cat">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Maximum value to check', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="rtwwdpd_label"><?php esc_html_e('Discount type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label>
                                            </td>
                                            <td>
                                                <select name="rtwwdpd_dscnt_cat_type">
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
                                                <input type="number" value="" required="required" min="0" step="0.01" name="rtwwdpd_dscnt_cat_val">
                                                <i class="rtwwdpd_description"><?php esc_html_e( 'Discount should be given according to discount type.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>
                                                <b class="rtwwdpd_required" ><?php esc_html_e( 'Required', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b>
                                                </i>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="options_group rtwwdpd_inactive" id="rtwtag_restriction_tab">
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
                                                $rtwwdpd_role_guest 	= esc_html__( 'Guest', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                                                $rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles ); 
                                                $rtwwdpd_roles 	= array_merge( array( 'guest' => $rtwwdpd_role_guest ), $rtwwdpd_roles ); 
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
                                    <div class="options_group rtwwdpd_inactive" id="rtwtag_time_tab">
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
    $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no'); ?>
    </div>
    <div class="rtwwdpd_table rtwwdpd_tag_table">
        <table class="rtwtable table table-striped table-bordered dt-responsive nowrap" data-value="categor" cellspacing="0">
            <thead>
                <tr>
                    <th><?php esc_attr_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <?php 
                    if($active_dayss == 'yes')
                    { ?>
                    <th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <?php }?>
                    <th><?php esc_attr_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
            $rtwwdpd_products_option = get_option('rtwwdpd_single_cat_rule');

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
                $cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
                $products = array();
                if(is_array($cat) && !empty($cat))
                {
                    foreach ($cat as $value) 
                {
                        $products[$value->term_id] = $value->name;
                    }
                }

                foreach ($rtwwdpd_products_option as $key => $value) {
                    echo '<tr data-val="'.$key.'">';

                    echo '<td class="rtwrow_no">'.esc_html__( $key+1 , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                    echo '<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_rule" value="Copy"></form></td>';
                    echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
                    
                    echo '<td>'.(isset($value['rtwwdpd_offer_cat_name']) ? esc_html__($value['rtwwdpd_offer_cat_name'], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
                    
                    echo '<td>';
                    if(isset($value['rtwwdpd_category_on_update']) &&  $value['rtwwdpd_category_on_update']=='rtwwdpd_multiple_cat_update')
                    {
                        if(isset($value['multiple_cat_ids']) && !empty( $value['multiple_cat_ids'] ))
                        {
                            foreach($value['multiple_cat_ids'] as $multiple_cat_key=>$multiple_cat_value)
                            {
                                echo " <p> <b>".( isset( $products[$multiple_cat_value] ) ? $products[$multiple_cat_value] : '' )."</p></b>";
                            }
                        }
                    }
                    else
                    {
                    echo ( isset( $products[$value['category_id']] ) ? $products[$value['category_id']] : '' );
                    }
                    echo '</td>';
                    if( isset( $value['rtwwdpd_check_for_cat'] ) && $value['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
                    {
                        echo '<td>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    elseif( isset( $value['rtwwdpd_check_for_cat'] ) && $value['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
                    {
                        echo '<td>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    else{
                        echo '<td>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    
                    echo '<td>'.(isset($value['rtwwdpd_min_cat']) ? esc_html__($value['rtwwdpd_min_cat'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
                    
                    echo '<td>'.(isset($value['rtwwdpd_max_cat']) ? esc_html__($value['rtwwdpd_max_cat'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

                    if( isset( $value['rtwwdpd_dscnt_cat_type'] ) && $value['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
                    {
                        echo '<td>'.esc_html__('Percentage', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    elseif( isset( $value['rtwwdpd_dscnt_cat_type'] ) && $value['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_flat_discount_amount')
                    {
                        echo '<td>'.esc_html__('Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    else{
                        echo '<td>'.esc_html__('Fixed Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'</td>';
                    }
                    
                    echo '<td>'.(isset($value['rtwwdpd_dscnt_cat_val']) ? esc_html__($value['rtwwdpd_dscnt_cat_val'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';

                    
                    if($active_dayss == 'yes')
                    {
                        echo '<td>';
                        if(isset($value['rtwwwdpd_cat_day']) && is_array($value['rtwwwdpd_cat_day']) && !empty($value['rtwwwdpd_cat_day']))
                        {
                            if(in_array(1, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Mon, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(2, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Tue, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(3, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Wed, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(4, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Thu, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(5, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Fri, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(6, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Sat, ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                            if(in_array(7, $value['rtwwwdpd_cat_day']))
                            {
                                esc_html_e('Sun', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
                            }
                        }
                        echo '</td>';
                    }
                    
                    echo '<td>'.(isset($value['rtwwdpd_max_discount']) ? esc_html__($value['rtwwdpd_max_discount'] , 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) : '').'</td>';
                    
                    echo '<td>';
                    if(isset($value['rtwwdpd_select_roles']) && is_array($value['rtwwdpd_select_roles']) && !empty($value['rtwwdpd_select_roles']))
                    {
                        foreach ($value['rtwwdpd_select_roles'] as $keys => $val) {
                            echo esc_html__($rtwwdpd_roles[$val], 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
                        }
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

                    echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&editcat='.$key ).'"><input type="button" class="rtw_edit_cat rtwwdpd_edit_dt_row" value="'.esc_attr__('Edit', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'" />
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
                    <th><?php esc_attr_e( 'Category', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Check On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Min', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Max', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Discount Type', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <?php 
                    if($active_dayss == 'yes')
                    { ?>
                    <th><?php esc_html_e( 'Active Days', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <?php }?>
                    <th><?php esc_attr_e( 'Max Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_attr_e( 'Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
                    <th><?php esc_html_e( 'Restricted Emails', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
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
    <?php 
}
else{
    include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
    }
?>
<div class="rtwwdpd_prod_offer_message">
    <form  action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
        <span class="rtwwdpd_shortcodes"><b><?php esc_html_e( 'Offer Message : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></b></span>
        <textarea class="rtwwdpd_input" value="" name="rtwwdpd_pro_offer">
            <?php echo trim(get_option( 'rtwwdpd_category_offer_msg','Get [discount_value] off on purchase of [from] to [to] on [category_name]' )); ?>
        </textarea>
        <!-- <input type="text" class="rtwwdpd_input" name="rtwwdpd_pro_offer"> -->
        <input id="subpro_rule" name="subpro_rule" class="rtw-button" type="submit" value="<?php esc_attr_e( 'Save Offer Message', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
        <span class="rtwwdpd_shortcodes"><b><?php esc_html_e('Use ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?>[discount_value], [from], [to], [category_name] <?php esc_html_e('as shortcodes to exchange values', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?></b></span>
    </form>
</div>