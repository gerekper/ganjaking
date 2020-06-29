<?php
!defined( 'YITH_WCMBS' ) && exit();
global $post;

$protected_links = get_post_meta( $post->ID, '_yith_wcmbs_protected_links', true );

$plans       = YITH_WCMBS_Manager()->get_plans();
$plans_array = array();
if ( !!$plans && is_array( $plans ) ) {
    foreach ( $plans as $plan ) {
        $plans_array[ $plan->ID ] = $plan->post_title;
    }
}

if ( !$protected_links ) {
    $protected_links = array(
        array(
            'name'       => '',
            'link'       => '',
            'membership' => array(),
        )
    );
}
?>

<div class="yith-wcmbs-admin-protected-links-wrapper yith-wcmbs-admin-settings-table-wrapper">
    <div class="yith-wcmbs-admin-protected-links-content">
        <table class="yith-wcmbs-admin-protected-links-table yith-wcmbs-admin-settings-table">
            <thead>
            <tr>
                <th class="yith-wcmbs-admin-protected-links-name-column"><?php _e( 'File name', 'yith-woocommerce-membership' ) ?></th>
                <th class="yith-wcmbs-admin-protected-links-link-column"><?php _e( 'File URL', 'yith-woocommerce-membership' ) ?></th>
                <th class="yith-wcmbs-admin-protected-links-membership-column"><?php _e( 'Membership', 'yith-woocommerce-membership' ) ?></th>
                <th class="yith-wcmbs-admin-protected-links-actions-column"></th>
            </tr>
            </thead>
            <tbody>
            <tr class="yith-wcmbs-admin-protected-links-row yith-wcmbs-admin-settings-table-default-row yith-wcmbs-admin-settings-table-row">
                <td class="yith-wcmbs-admin-protected-links-name-column">
                    <input type="text" name="_yith_wcmbs_protected_links[YITH_WCMBS_ID][name]" value=""/>
                </td>
                <td class="yith-wcmbs-admin-protected-links-link-column">
                    <input type="text" name="_yith_wcmbs_protected_links[YITH_WCMBS_ID][link]" value=""/>
                </td>
                <td class="yith-wcmbs-admin-protected-links-membership-column">
                    <select style="width:100%" class="yith-wcmbs-select2" name="_yith_wcmbs_protected_links[YITH_WCMBS_ID][membership][]" multiple>
                        <?php foreach ( $plans_array as $id => $title ): ?>
                            <option value="<?php echo $id ?>"><?php echo $title ?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                <td class="yith-wcmbs-admin-protected-links-actions-column">
                    <span class="dashicons dashicons-no-alt yith-wcmbs-delete"></span>
                </td>
            </tr>

            <?php foreach ( $protected_links as $index => $protected_link ): ?>
                <tr class="yith-wcmbs-admin-protected-links-row yith-wcmbs-admin-settings-table-row">
                    <td class="yith-wcmbs-admin-protected-links-name-column">
                        <input type="text" name="_yith_wcmbs_protected_links[<?php echo $index ?>][name]" value="<?php echo $protected_link[ 'name' ] ?>"/>
                    </td>
                    <td class="yith-wcmbs-admin-protected-links-link-column">
                        <input type="text" name="_yith_wcmbs_protected_links[<?php echo $index ?>][link]" value="<?php echo $protected_link[ 'link' ] ?>"/>
                    </td>
                    <td class="yith-wcmbs-admin-protected-links-membership-column">
                        <select style="width:100%" class="yith-wcmbs-select2 wc-enhanced-select" name="_yith_wcmbs_protected_links[<?php echo $index ?>][membership][]" multiple>
                            <?php foreach ( $plans_array as $id => $title ): ?>
                                <option value="<?php echo $id ?>" <?php selected( in_array( $id, (array) $protected_link[ 'membership' ] ) ); ?> ><?php echo $title ?></option>
                            <?php endforeach; ?>

                        </select>
                    </td>
                    <td class="yith-wcmbs-admin-protected-links-actions-column">
                        <span class="dashicons dashicons-no-alt yith-wcmbs-delete"></span>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
    <div class="yith-wcmbs-admin-bottom-actions">
        <span class="description"><?php _e('Use the following shortcode to show the protected links: [membership_protected_links]', 'yith-woocommerce-membership')?></span>
        <input type="button" id="yith-wcmbs-admin-protected-links-add-link" class="button yith-wcmbs-admin-settings-table-add-row"
               value="<?php _e( 'Add Link', 'yith-woocommerce-membership' ); ?>">
    </div>
</div>