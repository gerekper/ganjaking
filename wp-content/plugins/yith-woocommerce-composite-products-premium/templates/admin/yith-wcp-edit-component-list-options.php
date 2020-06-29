<?php
/**
 * Add description field to add/edit products attribute
 *
 * @author  Yithemes
 * @package YITH Composite Products for WooCommerce Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

?>

<div class="ywcp_components_list_container ywcp_list_container" >

    <p class="toolbar">

        <span class="ywcp_component_list_title">
            <?php _e( 'Components', 'yith-composite-products-for-woocommerce' ); ?>
            <?php echo wc_help_tip( __( 'List of components', 'yith-composite-products-for-woocommerce' ) ); ?>
        </span>

        <span class="expand-close">

            <a href="#" class="ywcp_expand_all"><?php _e( 'Expand all', 'yith-composite-products-for-woocommerce' ); ?></a> /
            <a href="#" class="ywcp_close_all"><?php _e( 'Collapse all', 'yith-composite-products-for-woocommerce' ); ?></a>

        </span>

    </p>

    <div id="ywcp_components_list_container_items" class="sortable">

        <?php if ( ! empty ( $wcp_data ) ) : ?>

           <?php YITH_WCP()->admin->load_component_list( $post->ID , $wcp_data[0] ); ?>

        <?php endif; ?>

    </div>

</div>

<div class="clearfix"></div>

<?php add_thickbox(); ?>

<p class="toolbar no-border">
    <button type="button" class="button ywcp_save_components"><?php _e( 'Save Components', 'yith-composite-products-for-woocommerce' ); ?></button>
    <button type="button" class="button button-primary ywcp_add_component"><?php _e( 'Add Component', 'yith-composite-products-for-woocommerce' ); ?></button>
    <a href="edit.php?page=ywcp_copy_components&post_id=<?php echo $post->ID; ?>&KeepThis=true&TB_iframe=true&modal=false" class="thickbox button button-primary ywcp_copy_components" onclick="return false;"><?php _e( 'Copy Components', 'yith-composite-products-for-woocommerce' ); ?></a>
    <div class="clearfix"></div>
</p>
