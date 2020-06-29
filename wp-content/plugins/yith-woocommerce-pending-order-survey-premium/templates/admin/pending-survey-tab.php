<?php
if( !defined( 'ABSPATH' ) )
    exit;
$table = new YITH_WC_Pending_Survey_Table();

?>
<div class="wrap">
    <h2><?php _e('Pending Order Survey', 'yith-woocommerce-pending-order-survey') ?>

        <a href="<?php echo esc_url( add_query_arg( 'post_type', YITH_Pending_Order_Survey_Type()->post_type_name, admin_url('post-new.php') ) ) ?>" class="add-new-h2">Add New</a></h2>

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
    <?php
    $args = array(
        'page' => 'yith_wc_pending_order_survey_panel',
        'tab'   => 'pending-survey'
    );


    $cart_link = esc_url ( wp_nonce_url( admin_url( 'admin.php' ) , 'ywcpos_donwload', '_ywcpos_donwload' ) );
    $admin_url = esc_url( add_query_arg( $args, $cart_link) );
    ?>
    <a href="<?php echo $admin_url;?>" class="button action"><?php _e('Export results','yith-woocommerce-pending-order-survey');?></a>
</div>
