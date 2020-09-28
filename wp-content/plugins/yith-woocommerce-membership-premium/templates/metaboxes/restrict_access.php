<?php
/*
 * Template for Metabox Restrict Access
 */
?>

<select id="yith_wcmbs_restrict_access" name="_yith_wcmbs_restrict_access">
    <option value="none" <?php selected( $restrict_access, 'none', true ) ?> ><?php _e( 'Everyone', 'yith-woocommerce-membership' ) ?></option>
    <option value="all_members" <?php selected( $restrict_access, 'all_members', true ) ?> ><?php _e( 'All Members', 'yith-woocommerce-membership' ) ?></option>
</select>

