<?php
/*
 * Template for Reports Page
 */
?>
<div id="yith-wcmbs-download-reports-downloads-by-product">
    <div class="yith-wcmbs-reports-filters">
        <select type="hidden" name="user_id" class="yith-wcmbs-reports-filter-user-id yith_wcmbs_ajax_select2_select_customer" style="width: 350px"
                data-placeholder="<?php esc_attr_e( 'Filter by user', 'yith-woocommerce-membership' ); ?>">
        </select>
        <input type="button" class="yith-wcmbs-reports-filter-button button button-primary" value="<?php esc_html_e( 'Filter', 'yith-woocommerce-membership' ) ?>">
        <input type="button" class="yith-wcmbs-reports-filter-reset button button-secondary" value="<?php esc_html_e( 'Reset Filters', 'yith-woocommerce-membership' ) ?>">
    </div>

    <div class="yith-wcmbs-reports-download-reports-table">
        <?php yith_wcmbs_get_view( '/reports/download-reports-table.php' ); ?>
    </div>

</div>