<?php

defined( 'ABSPATH' ) or exit;

$post_type = 'commissions';
set_current_screen( $post_type );
$GLOBALS['hook_suffix'] = 'commissions';

if ( isset( $_GET['id'] ) && $_GET['id'] > 0 ) : ?>

    <div id="yith-wcfm-commisssions">

        <h1><?php echo __('Commission', 'yith-frontend-manager-for-woocommerce'); ?></h1>

        <?php

        $commission = YITH_Commission( absint( $_GET['id'] ) );
        $args = apply_filters( 'yith_vendors_commission_view_template', array( 'commission' => $commission, 'back_url' => yith_wcfm_get_section_url() ) );
        yith_wcpv_get_template( 'commission-view', $args, 'admin' );

        ?>

    </div>

<?php

else :

    $class = apply_filters( 'yith_wcmv_commissions_list_table_class','YITH_WCFM_Commissions_List_Table');
    $commissions_table = new $class( array( 'screen' => $post_type, 'section_obj' => $section_obj ) );
    $commissions_table->prepare_items();

    $args = apply_filters( 'yith_vendors_commissions_template', array(
            'commissions_table' => $commissions_table,
            'page_title'        => __( 'Vendor Commissions', 'yith-frontend-manager-for-woocommerce' )
        )
    );

    ?>

    <div id="yith-wcfm-commisssions">

        <h1><?php echo __('Commissions', 'yith-frontend-manager-for-woocommerce'); ?></h1>

        <?php $commissions_table->views(); ?>

        <form id="commissions-filter" method="get">
            <input type="hidden" name="page" value="<?php echo ! empty( $_REQUEST['page'] ) ?  $_REQUEST['page'] : $section_obj->get_url() ?>" />
            <?php $commissions_table->add_search_box( __( 'Search commissions', 'yith-frontend-manager-for-woocommerce' ), 's' ); ?>
            <?php $commissions_table->display(); ?>
        </form>

    </div>

<?php endif; ?>
