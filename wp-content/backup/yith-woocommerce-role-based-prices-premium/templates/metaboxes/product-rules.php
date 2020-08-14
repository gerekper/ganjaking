<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

$currency_symbol = get_woocommerce_currency_symbol();
$options         = array(
	'discount_perc' => __( 'Discount %', 'yith-woocommerce-role-based-prices' ),
	'discount_val'  => sprintf( '%s %s', __( 'Discount ', 'yith-woocommerce-role-based-prices' ), $currency_symbol ),
	'markup_perc'   => __( 'Markup %', 'yith-woocommerce-role-based-prices' ),
	'markup_val'    => sprintf( '%s %s', __( 'Markup ', 'yith-woocommerce-role-based-prices' ), $currency_symbol ),
);

$all_price_rule = get_post_meta( $post->ID, '_product_rules', true );
$how_apply      = get_post_meta( $post->ID, 'how_apply_product_rule', true );


$how_apply = empty( $how_apply ) || is_array( $how_apply ) ? 'only_this' : $how_apply;


?>
<div class="options_group product_price_rule show_if_simple show_if_ticket-event show_if_external">
    <div class="toolbar toolbar-top">
		<span class="expand-close">
		    <a href="#" class="expand_all_price_rule"><?php _e( 'Expand', 'woocommerce' ); ?></a> / <a href="#"
                                                                                                       class="close_all_price_rule"><?php _e( 'Close', 'woocommerce' ); ?></a>
        </span>
        <label><?php _e( 'Add a role based price rule', 'yith-woocommerce-role-based-prices' ); ?></label>
        <select name="type_price_rule" class="type_price_rule_select">
            <option value=""><?php _e( 'Select an option', 'yith-woocommerce-role-based-prices' ); ?></option>
			<?php foreach ( $options as $key => $value ): ?>
                <option value="<?php esc_attr_e( $key ); ?>"><?php echo $value; ?></option>
			<?php endforeach; ?>
        </select>
        <button type="button"
                class="button add_price_rule"><?php _e( 'Add', 'yith-woocommerce-role-based-prices' ); ?></button>
        <div class="clear"></div>
    </div>
    <div class="options_group how_apply_product_rule">
        <p class="form-field">
      <span class="only_this">
          <label><?php _e( 'Use only the rules below', 'yith-woocommerce-role-based-prices' ); ?></label>
          <input type="radio" value="only_this"
                 name="how_apply_product_rule" <?php checked( 'only_this', $how_apply ); ?> >
      </span>
            <span class="only_user">
           <label><?php _e( 'Override rules created for the same user role', 'yith-woocommerce-role-based-prices' ); ?></label>
            <input type="radio" value="only_user"
                   name="how_apply_product_rule" <?php checked( 'only_user', $how_apply ); ?>>
       </span>
        </p>
    </div>
    <div class="product_price_list">
		<?php if ( ! empty( $all_price_rule ) ): ?>
			<?php
			$i = 0;
			foreach ( $all_price_rule as $rule ):
				$args         = array( 'index' => $i, 'rule' => $rule );
				$args['args'] = $args;
				wc_get_template( 'metaboxes/view/product-single-rule.php', $args, '', YWCRBP_TEMPLATE_PATH );
				$i ++;
			endforeach;
		endif; ?>
    </div>
</div>