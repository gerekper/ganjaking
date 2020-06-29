<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$function_product = YITH_Google_Product_Feed()->product_function;
$product = yit_get_prop($variation,'yith_wcgpf_product_feed_configuration',true);
$shipping = get_post_meta($variation->ID,'yith_wcgpf_shipping_feed_configuration',true);

?>
<div class="form-row form-row-full">
    <div>
        <h4><?php echo esc_html__( 'Google feed fields for variations', 'yith-google-product-feed-for-woocommerce' ); ?> </h4>
    </div>
    <div>
        <p style="font-weight: bold;"><?php esc_html_e('Basic product settings:','yith-google-product-feed-for-woocommerce')?></p>

        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-brand-label"><?php esc_html_e('Brand:', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-brand-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][brand]',
                'class' => 'yith-wcgpf-brand-product yith-wcgpf-information',
                'value' => isset($product['brand']) ? $product['brand'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-gtin-label"><?php esc_html_e('Global Trade Item Number (GTIN):', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-gtin-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][gtin]',
                'class' => 'yith-wcgpf-gtin-product yith-wcgpf-information',
                'value' => isset($product['gtin']) ? $product['gtin'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-mpn-label"><?php esc_html_e('Manufacturer Part Number (MPN):', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-mpn-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][mpn]',
                'class' => 'yith-wcgpf-mpn-product yith-wcgpf-information',
                'value' => isset($product['mpn']) ? $product['mpn'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-condition-label"><?php esc_html_e('Condition:', 'yith-google-product-feed-for-woocommerce'); ?></label>
            <?php echo yith_wcgpf_get_dropdown(array(
                'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][condition]',
                'id'        =>  'yith-wcgpf-condition-product',
                'class'     =>  'yith-wcgpf-condition-product yith-wcgpf-select yith-wcgpf-information',
                'options'   =>  $function_product->condition(),
                'value'     =>  isset($product['condition']) ? $product['condition'] : '',
            )); ?>
        </div>
        <div class=yith-wcgpf-product-feed-information>
            <label for="yith-wcgpf-google-category-label"><?php esc_html_e('Google Category:', 'yith-google-product-feed-for-woocommerce'); ?></label>
            <?php echo yith_wcgpf_get_dropdown(array(
                'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][google_product_category]',
                'id'        =>  'yith-wcgpf-google-category',
                'class'     =>  'yith-wcgpf-google-category  yith-wcgpf-select yith-wcgpf-information',
                'options'   =>  $function_product->google_category('local'),
                'value'     =>  isset($product['google_product_category']) ? $product['google_product_category'] : '',
            )); ?>
        </div>

        <?php if( apply_filters('yith_wcgpf_show_product_information_on_product_page',true) ) { ?>

            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-adult-label"><?php esc_html_e('Adult:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][adult]',
                    'id'        =>  'yith-wcgpf-adult-product',
                    'class'     =>  'yith-wcgpf-adult-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->adult(),
                    'value'     =>  isset($product['adult']) ? $product['adult'] : '',
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-energy-label"><?php esc_html_e('Energy efficiency class:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][energy_efficiency_class]',
                    'id'        =>  'yith-wcgpf-adult-product',
                    'class'     =>  'yith-wcgpf-adult-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->energy_efficiency(),
                    'value'     =>  isset($product['energy_efficiency_class']) ? $product['energy_efficiency_class'] : '',
                )); ?>
            </div>

        <?php } ?>

            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-gender-label"><?php esc_html_e('Gender:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][gender]',
                    'id'        =>  'yith-wcgpf-gender-product',
                    'class'     =>  'yith-wcgpf-gender-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->gender(),
                    'value'     =>  isset($product['gender']) ? $product['gender'] : '',
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-age-group-label"><?php esc_html_e('Age group:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][age_group]',
                    'id'        =>  'yith-wcgpf-age-group-product',
                    'class'     =>  'yith-wcgpf-age-group-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->age_group(),
                    'value'     =>  isset($product['age_group']) ? $product['age_group'] : '',
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-material-label"><?php esc_html_e('Material:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-material-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][material]',
                    'class' => 'yith-wcgpf-material-product  yith-wcgpf-information',
                    'value' => isset($product['material']) ? $product['material'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-pattern-label"><?php esc_html_e('Pattern:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-pattern-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][pattern]',
                    'class' => 'yith-wcgpf-pattern-product  yith-wcgpf-information',
                    'value' => isset($product['pattern']) ? $product['pattern'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-size-label"><?php esc_html_e('Size:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-size-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][size]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['size']) ? $product['size'] : ''
                )); ?>
            </div>

        <?php if( apply_filters('yith_wcgpf_show_product_information_on_product_page',true) ) { ?>

            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-size-type-label"><?php esc_html_e('Size type:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][size_type]',
                    'id'        =>  'yith-wcgpf-size-type-product',
                    'class'     =>  'yith-wcgpf-size-type-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->size_type(),
                    'value'     =>  isset($product['size_type']) ? $product['size_type'] : '',
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-size-system-label"><?php esc_html_e('Size system:', 'yith-google-product-feed-for-woocommerce'); ?></label>
                <?php echo yith_wcgpf_get_dropdown(array(
                    'name'      =>  'yith-wcgpf-product-feed-configuration['.$variation->ID.'][size_system]',
                    'id'        =>  'yith-wcgpf-size-system-product',
                    'class'     =>  'yith-wcgpf-size-system-product yith-wcgpf-select yith-wcgpf-information',
                    'options'   =>  $function_product->size_system(),
                    'value'     =>  isset($product['size_system']) ? $product['size_system'] : '',
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-custom-label-0"><?php esc_html_e('Custom label 0:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-custom-label-0-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][custom_label_0]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['custom_label_0']) ? $product['custom_label_0'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-custom-label-1"><?php esc_html_e('Custom label 1:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-custom-label-1-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][custom_label_1]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['custom_label_1']) ? $product['custom_label_1'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-custom-label-2"><?php esc_html_e('Custom label 2:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-custom-label-2-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][custom_label_2]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['custom_label_2']) ? $product['custom_label_2'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-custom-label-3"><?php esc_html_e('Custom label 3:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-custom-label-3-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][custom_label_3]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['custom_label_3']) ? $product['custom_label_3'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-custom-label-4"><?php esc_html_e('Custom label 4:', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-custom-label-4-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][custom_label_4]',
                    'class' => 'yith-wcgpf-size-product  yith-wcgpf-information',
                    'value' => isset($product['custom_label_4']) ? $product['custom_label_4'] : ''
                )); ?>
            </div>

        <?php } ?>

    </div>
    <?php if (apply_filters( 'yith_wcgpf_show_shipping_information_on_product_page',true ) ) { ?>
        <div>
        <p style="font-weight: bold;"><?php esc_html_e('Shipping settings:','yith-google-product-feed-for-woocommerce')?></p>
        <div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-shiping-price"><?php esc_html_e('Shipping price', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-shipping-price-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-shipping-feed-configuration['.$variation->ID.'][price]',
                    'class' => 'yith-wcgpf-shipping-price-product  yith-wcgpf-information',
                    'value' => isset($shipping['price']) ? $shipping['price'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-shiping-country"><?php esc_html_e('Shipping country', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-shipping-country-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-shippping-feed-configuration['.$variation->ID.'][country]',
                    'class' => 'yith-wcgpf-shipping-country-product  yith-wcgpf-information',
                    'value' => isset($shipping['country']) ? $shipping['country'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-shiping-region"><?php esc_html_e('Shipping region', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-shipping-price-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-shipping-feed-configuration['.$variation->ID.'][region]',
                    'class' => 'yith-wcgpf-shipping-region-product  yith-wcgpf-information',
                    'value' => isset($shipping['region']) ? $shipping['region'] : ''
                )); ?>
            </div>
            <div class="yith-wcgpf-product-feed-information">
                <label for="yith-wcgpf-shiping-service"><?php esc_html_e('Shipping service', 'yith-google-product-feed-for-woocommerce');?></label>
                <?php echo yith_wcgpf_get_input(array(
                    'id' => 'yith-wcgpf-shipping-price-product',
                    'type' => 'text',
                    'name' => 'yith-wcgpf-shipping-feed-configuration['.$variation->ID.'][service]',
                    'class' => 'yith-wcgpf-shipping-price-product  yith-wcgpf-information',
                    'value' => isset($shipping['service']) ? $shipping['service'] : ''
                )); ?>
            </div>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-shiping-label"><?php esc_html_e('Shipping label', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-shipping-label-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][shipping_label]',
                'class' => 'yith-wcgpf-shipping-label-product  yith-wcgpf-information',
                'value' => isset($product['shipping_label']) ? $product['shipping_label'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-shiping-weight"><?php esc_html_e('Shipping weight', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-shipping-weight-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][shipping_weight]',
                'class' => 'yith-wcgpf-shipping-weight-product  yith-wcgpf-information',
                'value' => isset($product['shipping_weight']) ? $product['shipping_weight'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-shiping-length"><?php esc_html_e('Shipping length', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-shipping-length-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][shipping_length]',
                'class' => 'yith-wcgpf-shipping-length-product  yith-wcgpf-information',
                'value' => isset($product['shipping_length']) ? $product['shipping_length'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-shiping-width"><?php esc_html_e('Shipping width', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-shipping-width-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][shipping_width]',
                'class' => 'yith-wcgpf-shipping-width-product  yith-wcgpf-information',
                'value' => isset($product['shipping_width']) ? $product['shipping_width'] : ''
            )); ?>
        </div>
        <div class="yith-wcgpf-product-feed-information">
            <label for="yith-wcgpf-shiping-height"><?php esc_html_e('Shipping height', 'yith-google-product-feed-for-woocommerce');?></label>
            <?php echo yith_wcgpf_get_input(array(
                'id' => 'yith-wcgpf-shipping-height-product',
                'type' => 'text',
                'name' => 'yith-wcgpf-product-feed-configuration['.$variation->ID.'][shipping_height]',
                'class' => 'yith-wcgpf-shipping-height-product  yith-wcgpf-information',
                'value' => isset($product['shipping_height']) ? $product['shipping_height'] : ''
            )); ?>
        </div>
    </div>
    <?php } ?>

    <?php do_action('yith_wcgpf_template_variation_information',$product,$shipping,$function_product,$variation) ?>

</div>