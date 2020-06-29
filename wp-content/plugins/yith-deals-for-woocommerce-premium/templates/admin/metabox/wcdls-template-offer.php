<?php

$offer_accepted            = get_post_meta( $post->ID, 'yith_wcdls_offer', true );
$rule_conditions           = get_post_meta( $post->ID, 'yith_wcdls_rule', true );
$enable_disable            = get_post_meta( $post->ID, 'yith_wcdls_enable_disable', true );

$deals_from = ( $datetime = get_post_meta( $post->ID, '_yith_wcdls_for', true )) ? absint( $datetime ) : '';

$deals_from = $deals_from ? date( 'Y-m-d', $deals_from ) : '';

$deals_to   = ( $datetime = get_post_meta( $post->ID, '_yith_wcdls_to', true )) ? absint( $datetime ) : '';
$deals_to   = $deals_to ? date( 'Y-m-d', $deals_to ) : '';

$automatic_deal = get_post_meta( $post->ID, 'yith_wcdls_automatic_deal', true );


if (is_array($offer_accepted)) {
    extract($offer_accepted);
}
?>

<div class="yith-wcdls-template-offer wrap">
    <div class="yith-wcdls-conditions-displayed">
        <div class="yith-wcdls-enable-disable-payment-restriction">
            <table class="form-table">
                <tbody>
                <tr>
                    <th>
                        <label for="yith-wcdls-rule-enable-disable"><?php _e( 'Disable:', 'yith-deals-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="yith-wcdls-rule-enable-disable" name="enable_disable" value="1" <?php checked( $enable_disable, 1 ) ?> />
                        <span class="desc inline"><?php echo esc_html__( 'Check this option if you want to disable this offer',
                                'yith-deals-for-woocommerce' ) ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <h3><?php esc_html_e('When will your offer be displayed?','yith-deals-for-woocommerce') ?></h3>
        <div class="yith-wcdls-div">
            <p><?php esc_html_e('Use these rules to decide when your offer is going to be displayed','yith-deals-for-woocommerce'); ?></p>

            <div class="yith-wcdls-condition-rules">
                <input id="yith-wcdls-new-condition" type="button" class="button-secondary yith-wcdls-new-condition" value="<?php esc_html_e( '+ Add new condition', 'yith-deals-for-woocommerce' ); ?>">
                <div id="yith-wcdls-list-conditions" class="yith-wcdls-list-conditions">
                    <?php
                    if ( !empty( $rule_conditions[ 'conditions' ] ) ) {
                        $i = 0;
                        foreach ( $rule_conditions[ 'conditions' ] as $conditions ) {
                            $default_args = array(
                            'i' => $i
                            );
                            $args         = wp_parse_args( $conditions, $default_args );
                            wc_get_template( 'wcdls-conditions-row.php', $args, '', YITH_WCDLS_TEMPLATE_PATH . 'admin/metabox/' );
                            $i++;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="yith-wcdls-offers-actions">
        <div>
            <h3><?php esc_html_e('What to do if the offer is accepted','yith-deals-for-woocommerce') ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="yith-wcdls-remove-products"><?php esc_html_e('Remove product(s)','yith-deals-for-woocommerce'); ?></label>
                        </th>
                        <td>
                            <div class="yith-wcdls-div">
                                <br>
                                <label>
                                    <input type="radio" class="yith-wcdls-remove-product-option"
                                           name="yith-wcdls-offer[remove_product_check_product_radio]" value="none" <?php checked(empty
                                    ($remove_product_check_product_radio) ? 'none': $remove_product_check_product_radio,'none') ?>> <?php esc_html_e('Do
                                    nothing','yith-deals-for-woocommerce'); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="radio" class="yith-wcdls-remove-product-option"
                                           name="yith-wcdls-offer[remove_product_check_product_radio]" value="remove_all_products" <?php isset
                                    ($remove_product_check_product_radio) ? checked( $remove_product_check_product_radio,'remove_all_products'): ''
                                    ?>> <?php esc_html_e('Remove all products from the cart','yith-deals-for-woocommerce'); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="radio" class="yith-wcdls-remove-product-option"
                                           name="yith-wcdls-offer[remove_product_check_product_radio]" value="remove_some_products" <?php isset
                                    ($remove_product_check_product_radio) ? checked( $remove_product_check_product_radio,'remove_some_products') :
                                        '' ?>> <?php esc_html_e('Remove these products from the cart','yith-deals-for-woocommerce'); ?>
                                </label>
                                <div data-type="yith-products" class="yith-wcdls-products-offer-accepted yith-wcdls-offer-accepted">
                                    <?php
                                    $data_selected = array();

                                    if (!empty($remove_product_check_product_select)) {
                                        $products = is_array($remove_product_check_product_select) ? $remove_product_check_product_select : explode(',', $remove_product_check_product_select);
                                        if ($products) {
                                            foreach ($products as $product_id) {
                                                $product = wc_get_product($product_id);
                                                $data_selected[$product_id] = $product->get_formatted_name();
                                            }
                                        }
                                    }
                                    $class = version_compare(WC()->version, '3.0.0', '>=') ? 'wc-product-search yith-wcdls-information yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li' : 'wc-product-search yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li';
                                    $class = isset($remove_product_check_product_select) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class.' yith-wcdls-hide-rule-set';
                                    $search_products_array = array(
                                        'type' => '',
                                        'class' => $class,
                                        'id' => 'yith_wcdls_product_selector_remove',
                                        'name' => 'yith-wcdls-offer[remove_product_check_product_select]',
                                        'data-placeholder' => esc_attr__('Search for a product&hellip;', 'yith-deals-for-woocommerce'),
                                        'data-allow_clear' => false,
                                        'data-selected' => $data_selected,
                                        'data-multiple' => true,
                                        'data-action' => 'woocommerce_json_search_products',
                                        'value' => empty($remove_product_check_product_select) ? '' : $remove_product_check_product_select,
                                        'style' => '',
                                        'custom-attributes' => array(
                                            'data-type' => 'product'
                                        ),
                                    );
                                    yit_add_select2_fields($search_products_array);


                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="yith-wcdls-add-products"><?php esc_html_e('Add product(s)','yith-deals-for-woocommerce'); ?></label>
                        </th>
                        <td>
                            <div class="yith-wcdls-div">
                                <label for="yith-wcdls-add-products"><?php esc_html_e('Add these products to the cart','yith-deals-for-woocommerce');
                                    ?></label>
                                <div data-type="yith-products" class="yith-wcdls-products-offer-accepted yith-wcdls-offer-accepted">
                                    <?php
                                    $data_selected = array();

                                    if (!empty($add_product_check_product_select)) {
                                        $products = is_array($add_product_check_product_select) ? $add_product_check_product_select : explode(',', $add_product_check_product_select);
                                        if ($products) {
                                            foreach ($products as $product_id) {
                                                $product = wc_get_product($product_id);
                                                $data_selected[$product_id] = $product->get_formatted_name();
                                            }
                                        }
                                    }
                                    $class = version_compare(WC()->version, '3.0.0', '>=') ? 'wc-product-search yith-wcdls-information yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li' : 'wc-product-search yith-wcdls-product-search yith-wcdls-select yith-wcdls yith-wcdls-li';
                                    $class = isset($add_product_check_product_select) ? $class.' yith-wcdls-rule-set yith-wcdls-selector2' : $class.' yith-wcdls-hide-rule-set';
                                    $search_products_array = array(
                                        'type' => '',
                                        'class' => $class,
                                        'id' => 'yith_wcdls_product_selector_added',
                                        'name' => 'yith-wcdls-offer[add_product_check_product_select]',
                                        'data-placeholder' => esc_attr__('Search for a product&hellip;', 'yith-deals-for-woocommerce'),
                                        'data-allow_clear' => false,
                                        'data-selected' => $data_selected,
                                        'data-multiple' => true,
                                        'data-action' => 'woocommerce_json_search_products_and_variations',
                                        'value' => empty($add_product_check_product_select) ? '' : $add_product_check_product_select,
                                        'style' => '',
                                        'custom-attributes' => array(
                                            'data-type' => 'product'
                                        ),
                                    );
                                    yit_add_select2_fields($search_products_array);


                                    ?>
                                </div>
                                <div class="yith-wcdls-div yith-wcdls-apply-type-offer">
                                    <label for="yith-wcdls-apply-offer"><?php esc_html_e('Type of discount','yith-deals-for-woocommerce');
                                        ?></label>
                                    <div class="yith-wcdls-offer-accepted">
                                        <select name="yith-wcdls-offer[type_offer_selected]" id="yith-wcdls-type-offer-selected"
                                                class="yith-wcdls-offer-select yith-wcdls-select-woo">
                                            <?php echo isset($type_offer_selected) ?  get_offer_options($type_offer_selected) : get_offer_options() ;  ?>
                                        </select>
                                    </div>
                                    <div class="yith-wcdls-offer-accepted ">
                                        <input type="text" name="yith-wcdls-offer[type_offer_value]" value="<?php echo isset($type_offer_value) ?  $type_offer_value : "" ?>"/>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h3><?php esc_html_e('What to do if the offer is not accepted','yith-deals-for-woocommerce') ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="yith-wcdls-no-acceoted-options"><?php esc_html_e('Repeat offer?','yith-deals-for-woocommerce'); ?></label>
                        </th>
                        <td>
                            <div class="yith-wcdls-div">
                                <div class="yith-wcdls-div-radio-not-accepted">
                                    <div class="yith-wcdls-radio-not-accepted">
                                        <label>
                                            <input type="radio" class="yith-wcdls-offer-no-accepted-option" name="yith-wcdls-offer[no_accepted_option]" value="hide_offer" <?php checked(empty($no_accepted_option) ? 'hide_offer': $no_accepted_option,'hide_offer') ?>> <?php esc_html_e('Hide offer: ','yith-deals-for-woocommerce'); ?>
                                        </label>
                                        <div class="yith-wcdls-offer-accepted">
                                            <?php echo yith_wcdls_get_dropdown(array(
                                                'name' => 'yith-wcdls-offer[hide_offer_select]',
                                                'id' => 'yith-wcdls-no-accepted-option',
                                                'class' => 'yith-wcdls-select-woo',
                                                'options' => yith_wcdls_get_hide_offer(),
                                                'value' => isset($hide_offer_select) ? $hide_offer_select : '',
                                            )); ?>
                                        </div><br/>
                                    </div>
                                    <div class="yith-wcdls-radio-not-accepted">
                                        <label>
                                            <input type="radio" class="yith-wcdls-offer-no-accepted-option" name="yith-wcdls-offer[no_accepted_option]" value="show_another_offer" <?php isset($no_accepted_option) ? checked( $no_accepted_option,'show_another_offer') : '' ?>> <?php esc_html_e('Show another offer: ','yith-deals-for-woocommerce'); ?>
                                        </label>
                                        <br>
                                        <div class="yith-wcdls-offer-accepted yith-wcdls-select-offers">
                                            <?php
                                            $offers = yith_wcdls_show_another_offer($post->ID);

                                            if(empty($offers)) {
                                                ?>
                                                <label for="yith-wcdls-no-offers"><?php esc_html_e('You didn\'t create any other offer. Please create another offer to use this option' ,'yith-deals-for-woocommerce'); ?></label>
                                                <?php
                                            } else {
                                                echo yith_wcdls_get_dropdown(array(
                                                    'name' => 'yith-wcdls-offer[show_another_offer]',
                                                    'id' => 'yith-wcdls-show-another-offer',
                                                    'class' => 'yith-wcdls-select-woo',
                                                    'options' => $offers,
                                                    'value' => isset($show_another_offer) ? $show_another_offer : '',
                                                ));
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h3><?php esc_html_e('Layout','yith-deals-for-woocommerce') ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="yith-wcdls-popup-text"><?php esc_html_e('Select layout','yith-deals-for-woocommerce'); ?></label>

                        </th>
                        <td>
                            <div class="yith-wcdls-div">
                                <?php echo yith_wcdls_get_dropdown(array(
                                    'name' => 'yith-wcdls-offer[type_layout]',
                                    'id' => '',
                                    'class' => 'yith-wcdls-select-woo',
                                    'options' => yith_wcdls_get_type_layout(),
                                    'value' => isset($type_layout) ? $type_layout : '',
                                    'custom-attributes' => array(
                                        'aria-describedby' => 'yith-wcdls-popup-description'
                                    )
                                )); ?>
                                <p class="description" id="yith-wcdls-popup-description"><?php esc_html_e('Choose the offer\'s layout that will be seen by your customer','yith-deals-for-woocommerce');?></p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h3><?php esc_html_e('Time-based offer','yith-deals-for-woocommerce') ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <?php esc_html_e('Select the offer availability dates','yith-deals-for-woocommerce'); ?>
                        </th>
                        <td>
                            <label>
                                <?php esc_html_e( 'From: ', 'yith-deals-for-woocommerce' ); ?>
                                <input type="text" name="_yith_wcdls_for" class="wcdls_datepicker yith-wcdls-select" id="_yith_wcdls_from" value="<?php echo $deals_from ?>"
                                       title="YYYY-MM-DD" data-related-to="#_yith_wcdls_to">
                            </label>
                            <label>
                                <?php esc_html_e( 'To: ', 'yith-deals-for-woocommerce' ); ?>
                                <input type="text" name="_yith_wcdls_to" class="wcdls_datepicker yith-wcdls-select" id="_yith_wcdls_to" value="<?php echo $deals_to ?>"
                                       title="YYYY-MM-DD">
                            </label>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="yith-wcdls-automatic-deals">
            <table class="form-table">
                <tbody>
                <tr>
                    <th>
                        <label for="yith-wcdls-automatic-deal"><?php esc_html_e( 'Automatic deal:', 'yith-deals-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="yith-wcdls-yith-wcdls-automatic-deal" name="automatic_deal" value="1" <?php checked( $automatic_deal, 1 ) ?> />
                        <span class="desc inline"><?php echo esc_html__( 'Check this option if you want to apply the deal without accept it',
                                'yith-deals-for-woocommerce' ) ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

