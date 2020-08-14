<?php
/*
 * Template for Reports Page
 */
?>
<div id="yith-wcmbs-download-reports-downloads-by-product">
    <div class="yith-wcmbs-reports-filters">
        <select type="hidden" name="user_id" class="yith-wcmbs-reports-filter-user-id yith_wcmbs_ajax_select2_select_customer" style="width: 350px"
                data-placeholder="<?php _e( 'Filters by user', 'yith-woocommerce-membership' ); ?>">
            <option></option>
        </select>
        <input type="button" class="yith-wcmbs-reports-filter-button button primary-button" value="<?php _e( 'Filter', 'yith-woocommerce-membership' ) ?>">
        <input type="button" class="yith-wcmbs-reports-filter-reset button primary-button" value="<?php _e( 'Reset Filters', 'yith-woocommerce-membership' ) ?>">
    </div>

    <div class="yith-wcmbs-reports-download-reports-table">
        <?php include YITH_WCMBS_TEMPLATE_PATH . '/reports/download-reports-table.php'; ?>
    </div>

</div>