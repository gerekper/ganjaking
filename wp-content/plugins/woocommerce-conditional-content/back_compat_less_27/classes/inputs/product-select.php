<?php

class WC_Conditional_Content_Input_Product_Select {

    public function __construct() {
        // vars
        $this->type = 'Product_Select';

        $this->defaults = array(
            'multiple' => 0,
            'allow_null' => 0,
            'choices' => array(),
            'default_value' => '',
            'class' => 'ajax_chosen_select_products'
        );
    }

    public function render($field, $value = null) {

        $field = array_merge($this->defaults, $field);
        if (!isset($field['id'])) {
            $field['id'] = sanitize_title($field['id']);
        }
        
        ?>

        <select id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>[]" class="ajax_chosen_select_products" multiple="multiple" data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>">
            <?php
            $current = $value ? $value : array();
            $product_ids = !empty($current) ? array_map('absint', $current) : null;
            if ($product_ids) {
                foreach ($product_ids as $product_id) {

                    $product = wc_get_product($product_id);
                    $product_name = WC_Conditional_Content_Compatibility::woocommerce_get_formatted_product_name($product);

                    echo '<option value="' . esc_attr($product_id) . '" selected="selected">' . esc_html($product_name) . '</option>';
                }
            }
            ?>
        </select> 

        <?php
    }

}
?>