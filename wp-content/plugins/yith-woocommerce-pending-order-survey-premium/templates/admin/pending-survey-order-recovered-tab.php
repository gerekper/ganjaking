<?php
if( !defined( 'ABSPATH' ) )
    exit;
$table = new YITH_WC_Recovered_Order_Table();

?>
<div class="wrap">
    <h2>
        <?php _e('Recovered orders', 'yith-woocommerce-pending-order-survey') ?>
    </h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $table->prepare_items();
                        $table->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
