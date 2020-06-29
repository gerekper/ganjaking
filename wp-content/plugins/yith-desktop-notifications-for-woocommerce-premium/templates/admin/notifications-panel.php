<?php
/**
 *
 * @package YITH Desktop Notifications for WooCommerce
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div id="wrap" class="plugin-option yit-admin-panel-container">
    <div class="yit-admin-panel-content-wrap yit-admin-panel-content-wrap-full yit-admin-panel-content-wrap-full">
        <table class="form-table">
            <tbody>
            <tr>
                <td>
                    <?php do_action( 'yith_wcdn_print_notifications', $type ); ?>

                </td>
            </tr>
            </tbody>
        </table>

    </div>
</div>