<?php
/*
 * Template for Metabox Restrict Access
 */

$plans = YITH_WCMBS_Manager()->plans;
?>

<?php if ( !empty( $plans ) ) : ?>
    <input name="_yith_wcmbs_restrict_access_edit_post" type="hidden" value="1">
    <div class="">
        <label for="yith_wcmbs_restrict_access_plan"><?php _e( 'Include this item in a membership', 'yith-woocommerce-membership' ) ?>:</label>
        <?php $loop = 0; ?>
        <?php foreach ( $plans as $p ) : ?>
            <p>
                <input id="yith-wcmbs-rap-<?php echo $loop ?>" type="checkbox" name="_yith_wcmbs_restrict_access_plan[<?php echo $loop ?>]" value="<?php echo $p->ID ?>"
                    <?php checked( true, in_array( $p->ID, (array)$restrict_access_plan ), true ) ?> >
                <label for="yith-wcmbs-rap-<?php echo $loop ?>"><?php echo $p->post_title ?></label>
            </p>
            <?php $loop++; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>