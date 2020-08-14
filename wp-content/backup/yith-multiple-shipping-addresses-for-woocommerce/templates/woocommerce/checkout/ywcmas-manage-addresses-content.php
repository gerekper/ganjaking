<?php

/**
 * The main content of the Addresses Manager.
 */

?>
<div class="ywcmas_multiple_addresses_manager" style="display: none;">
    <h3><?php esc_html_e( 'Manage addresses', 'yith-multiple-shipping-addresses-for-woocommerce' );?></h3>
    <div>
        <div class="ywcmas_manage_addresses_viewer_container">
			<?php

			ob_start();

			wc_get_template( 'checkout/ywcmas-manage-addresses-viewer.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH );

			echo ob_get_clean();

			?>
        </div>
        <div class="ywcmas_manage_addresses_tables_container"></div>
    </div>
</div>