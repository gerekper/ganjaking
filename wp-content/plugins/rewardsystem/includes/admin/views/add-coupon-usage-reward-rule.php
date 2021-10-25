<?php
/**
 * Add Coupon Rule.
 * */
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
?>
<tr>
    <td>
        <select multiple="multiple" 
                id = "coupon_code_points"
                name="rewards_dynamic_rule_coupon_usage[<?php echo esc_attr( $key ) ; ?>][coupon_codes][]" 
                class="short coupon_points coupon_code_points srp_select2">
                    <?php
                    foreach( $coupons as $coupon ) :
                        ?>
                <option value="<?php echo wp_kses_post( strtolower( $coupon->post_title ) ) ; ?>"><?php echo wp_kses_post( $coupon->post_title ) ; ?>
                <?php endforeach ; ?>          
            </option>
        </select>
    </td>

    <td>
        <input type = "text"
               name="rewards_dynamic_rule_coupon_usage[<?php echo esc_attr( $key ) ; ?>][reward_points]"
               class="short" />
    </td>

    <td class="num">
        <span class="remove rs-remove-coupon-usage-rule button-secondary"><?php esc_html_e( 'Remove Rule' , SRP_LOCALE ) ; ?></span>
        <input type ="hidden" id="rs_rule_id_for_coupon_usage_reward" value="<?php echo esc_html( $key ) ; ?>">
    </td>
</tr>
<?php
