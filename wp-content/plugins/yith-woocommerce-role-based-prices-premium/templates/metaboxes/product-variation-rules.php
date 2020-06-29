<?php
if (!defined('ABSPATH'))
    exit;

$variation_id = $variation->ID;
$currency_symbol = get_woocommerce_currency_symbol();
$options = array(
    'discount_perc' => __('Discount %', 'yith-woocommerce-role-based-prices'),
    'discount_val' => sprintf('%s %s', __('Discount ', 'yith-woocommerce-role-based-prices'), $currency_symbol),
    'markup_perc' => __('Markup %', 'yith-woocommerce-role-based-prices'),
    'markup_val' => sprintf('%s %s', __('Markup ', 'yith-woocommerce-role-based-prices'), $currency_symbol),
);

$all_price_rule = get_post_meta($variation_id, '_product_rules', true);
$how_apply = get_post_meta($variation_id, 'how_apply_product_rule', true );
$how_apply = empty( $how_apply ) || is_array( $how_apply )? 'only_this' : $how_apply;
?>
<div class="variation_price_rule">
    <p class="form-row form-row-first">
        <label><?php _e( 'Add role-based price rule','yith-woocommerce-role-based-prices' );?></label>
        <select name="type_price_rule" class="type_price_rule_select">
            <option value=""><?php _e('Select an option', 'yith-woocommerce-role-based-prices'); ?></option>
            <?php foreach ($options as $key => $value): ?>
                <option value="<?php esc_attr_e($key); ?>"><?php echo $value; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button add_variation_price_rule"><?php _e('Add', 'yith-woocommerce-role-based-prices'); ?></button>
        <span class="clear"></span>
    </p>
    <p class="form-row form-row-full">
         <span class="only_this" style="float:left;margin-right: 25px;">
          <label><?php _e('Use only the rules below','yith-woocommerce-role-based-prices');?></label>
          <input type="radio" value="only_this" name="how_apply_product_rule[<?php echo $loop;?>]" <?php checked( 'only_this',$how_apply );?> >
      </span>
       <span class="only_user">
           <label><?php _e('Override rules created for the same user role','yith-woocommerce-role-based-prices');?></label>
            <input type="radio" value="only_user" name="how_apply_product_rule[<?php echo $loop;?>]" <?php checked( 'only_user',$how_apply );?>>
       </span>
    </p>
    <p class="form-row form-row-last expand_close">
            <span class="expand-close">
                <a href="#" class="expand_all_variation_price_rule"><?php _e('Expand', 'woocommerce'); ?></a> / <a href="#" class="close_all_variation_price_rule"><?php _e('Close', 'woocommerce'); ?></a>
            </span>
    </p>

    <div class="product_variation_price_list" data-variation_loop="<?php echo $loop; ?>">
        <?php if (!empty($all_price_rule)): ?>
            <?php
            $i = 0;
            foreach ($all_price_rule as $rule):
                $args = array('index' => $i, 'rule' => $rule, 'loop' => $loop);
                $args['args'] = $args;
                wc_get_template('metaboxes/view/product-variation-single-rule.php', $args, '', YWCRBP_TEMPLATE_PATH);
                $i++;
            endforeach;
        endif; ?>
    </div>
</div>