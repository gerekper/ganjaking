<?php
?>

<div class="yith-wcdls-conditions-row">
    <div class="yith-wcdls-restriction-by">
        <?php do_action('yith_wcdls_before_conditions_row'); ?>
        <!--<div class="yith-wcdls-row yith-wcdls-check-product">
            <input id="yith-wcdls-add-products"  name="yith-wcdls-rule[conditions]['<?php echo $i; ?>'][checkbox]" type="checkbox" value="1" <?php isset($checkbox) ? checked( $checkbox,'1') : ''?>>
            <label for="yith-wcdls-add-products"><?php esc_html_e('- Add product options','yith-deals-for-woocommerce'); ?></label>
        </div>-->
        <!-- Type Restriction -->
        <div class="yith-wcdls-type-restriction yith-wcdls-row">
            <?php echo yith_wcdls_get_dropdown(array(
                'name'      =>  'yith-wcdls-rule[conditions]['.$i.'][type_restriction]',
                'id'        =>  '',
                'class'     =>  isset($type_restriction) ? ' yith-wcdls-rule yith-wcdls-get-type-restriction yith-wcdls-select yith-wcdls-li yith-wcdls-rule-set' : 'yith-wcdls-rule yith-wcdls-get-type-restriction yith-wcdls-select',
                'options'   =>  yith_wcdls_get_type_of_restrictions(),
                'value'     =>  isset($type_restriction) ? $type_restriction : '',
            )); ?>
        </div>
        <!-- Restriction by -->
        <div class="yith-wcdls-restriction-by yith-wcdls-select2 yith-wcdls-row">
            <?php echo yith_wcdls_get_dropdown(array(
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][restriction_by]',
                'id' => '',
                'style' => isset($restriction_by) ? '' : 'display: none;',
                'class' => isset($restriction_by) ? ' yith-wcdls-rule yith-wcdls-restriction-type yith-wcdls-select yith-wcdls yith-wcdls-li yith-wcdls-rule-set' : 'yith-wcdls-rule yith-wcdls-restriction-type yith-wcdls-select yith-wcdls',
                'options' => yith_wcdls_restriction_type(),
                'disabled' => 'default',
                'value' => isset($restriction_by) ? $restriction_by : '',
            )); ?>
        </div>
        <!-- Restriction by Product -->

        <div data-type="yith-products" class="yith-wcdls-select2 yith-wcdls-select2-product yith-wcdls-row">
            <?php
            $data_selected = array();

            if (!empty($products_selected)) {
                $products = is_array($products_selected) ? $products_selected : explode(',', $products_selected);
                if ($products) {
                    foreach ($products as $product_id) {
                        $product = wc_get_product($product_id);
                        $data_selected[$product_id] = $product->get_formatted_name();
                    }
                }
            }
            $class = version_compare(WC()->version, '3.0.0', '>=') ? 'wc-product-search yith-wcdls-information yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li' : 'wc-product-search yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li';
            $class = isset($products_selected) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class.' yith-wcdls-hide-rule-set';
            $search_products_array = array(
                'type' => '',
                'class' => $class,
                'id' => 'yith_wcdls_product_selector',
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][products_selected]',
                'data-placeholder' => esc_attr__('Search for a product&hellip;', 'yith-deals-for-woocommerce'),
                'data-allow_clear' => false,
                'data-selected' => $data_selected,
                'data-multiple' => true,
                'data-action' => 'woocommerce_json_search_products_and_variations',
                'value' => empty($products_selected) ? '' : $products_selected,
                'style' => 'display:none;',
                'custom-attributes' => array(
                    'data-type' => 'product'
                ),
            );
            yit_add_select2_fields($search_products_array,$i);


            ?>
        </div>
        <!-- Restriction by Price -->

        <div class="yith-wcdls-restriction-by-price yith-wcdls-select2 yith-wcdls-row">
            <?php echo yith_wcdls_get_dropdown(array(
                'name'      =>  'yith-wcdls-rule[conditions]['.$i.'][restriction_by_price]',
                'id'        =>  '',
                'style'     =>  isset($restriction_by_price) ? '' : 'display: none;',
                'class'     =>  isset($restriction_by_price) ? 'yith-wcdls-rule yith-wcdls-rule-price yith-wcdls-select yith-wcdls yith-wcdls-li yith-wcdls-rule-set' : 'yith-wcdls-rule yith-wcdls-rule-price yith-wcdls-select yith-wcdls yith-wcdls-li',
                'options'   =>   yith_wcdls_price_order(),
                'value'     =>   isset($restriction_by_price) ? $restriction_by_price : '',
            )); ?>
        </div>

        <div class="yith-wcdls-input-price yith-wcdls-row">
            <?php
            $class = 'yith-wcdls-input-price yith-wcdls-price yith-wcdls yith-wcdls-li';
            $class = isset($price) ? $class . ' yith-wcdls-rule-set' : $class ;
            ?>
            <input type="text" class="<?php echo $class ?> " name="yith-wcdls-rule[conditions][<?php echo $i ?>][price]" value="<?php echo isset($price) ? $price : '' ?>" style="display:none" >
        </div>

        <!-- Restriction by Categories -->

        <div data-type="yith-categories" class="yith-wcdls-select2 yith-wcdls-select2-categories yith-wcdls-row">
            <?php

            $data_selected = array();
            if (!empty($categories_selected)) {
                $categories = is_array($categories_selected) ? $categories_selected : explode(',', $categories_selected);
                if ($categories) {
                    foreach ($categories as $category_id) {
                        $term = get_term_by('id', $category_id, 'product_cat', 'ARRAY_A');
                        $data_selected[$category_id] = $term['name'];
                    }
                }
            }
            $class = version_compare(WC()->version, '3.0.0', '>=') ? 'yith-wcdls-category-search yith-wcdls-information yith-wcdls-categories yith-wcdls-select yith-wcdls yith-wcdls-li' : 'yith-wcdls-category-search yith-wcdls-categories yith-wcdls-select yith-wcdls yith-wcdls-li';

            $class = isset($categories_selected) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class;

            $search_cat_array = array(
                'type' => '',
                'class' => $class,
                'id' => 'yith_wcdls_category_selector',
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][categories_selected]',
                'data-placeholder' => esc_attr__('Search for a category&hellip;', 'yith-deals-for-woocommerce'),
                'data-allow_clear' => false,
                'data-selected' => $data_selected,
                'data-multiple' => true,
                'data-action' => '',
                'value' => empty($categories_selected) ? '' : $categories_selected,
                'style' => 'display: none;',
                'custom-attributes' => array(
                    'data-type' => 'category'
                ),
            );
            yit_add_select2_fields($search_cat_array);

            ?>
        </div>
        <!-- Restriction by Tags -->

        <div data-type="yith-tags" class="yith-wcdls-select2 yith-wcdls-select2-tags yith-wcdls-row">
            <?php

            $data_selected = array();
            if (!empty($tags_selected)) {
                $tags = is_array($tags_selected) ? $tags_selected : explode(',', $feed['tags_selected']);
                if ($tags) {
                    foreach ($tags as $tag_id) {
                        $term = get_term_by('id', $tag_id, 'product_tag', 'ARRAY_A');
                        $data_selected[$tag_id] = $term['name'];
                    }
                }
            }

            $class = version_compare(WC()->version, '3.0.0', '>=') ? ' yith-wcdls-tags-search yith-wcdls-information yith-wcdls-tags yith-wcdls-select yith-wcdls yith-wcdls-li' : 'yith-wcmr-tags-search yith-wcdls-tags yith-wcdls-select yith-wcdls yith-wcdls-li';
            $class = isset($tags_selected) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class;

            $search_tag_array = array(
                'type' => '',
                'class' => $class,
                'id' => 'yith_wcdls_tags_selector',
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][tags_selected]',
                'data-placeholder' => esc_attr__('Search for a tag&hellip;', 'yith-deals-for-woocommerce'),
                'data-allow_clear' => false,
                'data-selected' => $data_selected,
                'data-multiple' => true,
                'data-action' => '',
                'value' => empty($tags_selected) ? '' : $tags_selected,
                'style' => 'display: none;',
                'custom-attributes' => array(
                    'data-type' => 'tag'
                ),
            );

            yit_add_select2_fields($search_tag_array);
            ?>
        </div>


        <!-- Restriction by Geolocalization -->

        <div class="yith-wcdls-select2 yith-wcdls-select2-geolocalization yith-wcdls-row">
            <?php
            $country = WC()->countries->countries;
            $class = 'yith-wcdls-select yith-wcdls yith-wcdls-geolocalization-search yith-wcdls-li';
            $class = isset ($geolocalization) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class;
            echo yith_wcdls_get_dropdown_multiple(array(
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][geolocalization][]',
                'id' => '',
                'style' => isset($geolocalization) ? '' : 'display: none;',
                'class' => $class,
                'options' => $country,
                'multiple' => 'multiple',
                'value' => isset($geolocalization) ? $geolocalization : '',
                'custom-attributes' => array(
                    'data-type' => 'geolocalization',
                ),
            ));
            ?>
        </div>

        <!-- Restriction by role -->
        <div class="yith-wcdls-select2 yith-wcdls-select2-role yith-wcdls-row">
            <?php
            $role_option = yith_wcdls_get_user_roles();
            $class = 'yith-wcdls-select yith-wcdls yith-wcdls-role-search yith-wcdls-li';
            $class = isset ($roles) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class;
            echo yith_wcdls_get_dropdown_multiple(array(
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][roles][]',
                'id' => '',
                'style' => isset($roles) ? '' : 'display: none;',
                'class' => $class,
                'options' => $role_option,
                'multiple' => 'multiple',
                'value' => isset($roles) ? $roles : '',
                'custom-attributes' => array(
                    'data-type' => 'role',
                ),
            ));
            ?>
        </div>

        <!-- Restriction by Users -->

        <div data-type="yith-users" class="yith-wcdls-select2 yith-wcdls-select2-users yith-wcdls-row">
            <?php

            $data_selected = array();
            if (!empty($users_selected)) {
                $users = is_array($users_selected) ? $users_selected : explode(',', $feed['users_selected']);
                if ($users) {
                    foreach ($users as $user_id) {
                        $user = get_userdata( $user_id );
                        $data_selected[$user_id] = $user->user_login;
                    }
                }
            }

            $class = version_compare(WC()->version, '3.0.0', '>=') ? 'wc-customer-search yith-wcdls-select yith-wcdls yith-wcdls-li' : ' wc-customer-search yith-wcdls-tags yith-wcdls-select yith-wcdls yith-wcdls-li';
            $class = isset($users_selected) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class.' yith-wcdls-hide-rule-set';

            $search_tag_array = array(
                'type' => '',
                'class' => $class,
                'id' => 'yith_wcdls_users_selector',
                'name' => 'yith-wcdls-rule[conditions][' . $i . '][users_selected]',
                'data-placeholder' => esc_attr__('Search for a user&hellip;', 'yith-deals-for-woocommerce'),
                'data-allow_clear' => false,
                'data-selected' => $data_selected,
                'data-multiple' => true,
                'data-action' => '',
                'value' => empty($users_selected) ? '' : $users_selected,
                'style' => 'display: none;',
                'custom-attributes' => array(
                    'data-type' => 'user'
                ),
            );

            yit_add_select2_fields($search_tag_array);
            ?>
        </div>

        <div class="yith-wcdls-delete-condition yith-wcdls-row">
            <input type="button" class="button-secondary yith-wcdls-delete-condition" value="<?php esc_html_e('Delete', 'yith-deals-for-woocommerce');?>">
        </div>
    </div>
</div>