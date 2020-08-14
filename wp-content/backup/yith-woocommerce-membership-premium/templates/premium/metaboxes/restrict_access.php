<?php
/*
 * Template for Metabox Restrict Access
 */

$plans = YITH_WCMBS_Manager()->get_plans();
?>

<?php if ( !empty( $plans ) ) : ?>
    <input name="_yith_wcmbs_restrict_access_edit_post" type="hidden" value="1">
    <div>
        <label for="yith_wcmbs_restrict_access_plan"><?php _e( 'Include this item in a membership', 'yith-woocommerce-membership' ) ?>:</label>
        <select id="yith_wcmbs_restrict_access_plan" class="yith-wcmbs-select2" multiple="multiple" name="_yith_wcmbs_restrict_access_plan[]">
            <?php foreach ( $plans as $p ) : ?>
                <option value="<?php echo $p->ID ?>" <?php selected( true, in_array( $p->ID, (array)$restrict_access_plan ), true ) ?> ><?php echo $p->post_title ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <table id="yith-wcmbs-delay-time-for-plans-container" style="display:none">
            <tr>
                <th><?php _e( 'Plan Name', 'yith-woocommerce-membership' ); ?></th>
                <th><?php _e( 'Delay', 'yith-woocommerce-membership' ); ?></th>
            </tr>
            <?php
            foreach ( $plans as $p ) {
                if ( in_array( $p->ID, (array)$restrict_access_plan ) ) {
                    $plan_name = $p->post_title;
                    $plan_id   = $p->ID;
                    $value     = isset( $plan_delay[ $plan_id ] ) ? $plan_delay[ $plan_id ] : 0;
                    echo "<tr class='yith-wcmbs-delay-row' data-plan-id='{$plan_id}'>
                    <td><label for='yith-wcmbs-delay-{$plan_id}'>{$plan_name}</label></td>
                    <td><input class='yith-wcmbs-delay-number-input' data-plan-id='{$plan_id}' id=yith-wcmbs-delay-{$plan_id}' name='_yith_wcmbs_plan_delay[{$plan_id}]' type='number' value='{$value}' min='0'></td>
                    </tr>";
                }
            }
            ?>
        </table>
    </div>

<?php endif; ?>
