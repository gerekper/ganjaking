<?php
if( !defined( 'ABSPATH' ) )
    exit;

$table = new YITH_WC_Pending_Survey_Email_Table();

?>
<div class="wrap">
    <h2><?php _e('Email templates', 'yith-woocommerce-pending-order-survey') ?>

        <a href="<?php echo esc_url( add_query_arg( 'post_type', YITH_Pending_Email_Type()->post_type_name, admin_url('post-new.php') ) ) ?>" class="add-new-h2">Add New</a></h2>

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
