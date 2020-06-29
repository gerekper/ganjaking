<?php


require_once ( YITH_COG_PATH . '/includes/admin/reports/stock-reports/class.yith-cog-report-stock-table.php' );



if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//Template of the 'Stock by ... ' links
wc_get_template( 'html/html-admin-report-stock-links.php', array(), '' , YITH_COG_TEMPLATE_PATH );

?>
<div id="poststuff" class="woocommerce-reports-wide">
    <div style="float: right"><?php $this->get_export_button(); ?></div>
    <br>
    <div class="inside"></div>
</div>

