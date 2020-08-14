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

$wcp_data = !empty ( $wcp_data ) ? $wcp_data[0] : null;
$wcp_data_dependencies = !empty ( $wcp_data_dependencies ) ? $wcp_data_dependencies[0] : null;
$wcp_data_dependencies_component_list_data = !empty ( $wcp_data_dependencies_component_list_data ) ? $wcp_data_dependencies_component_list_data[0] : null;

?>

<div class="ywcp_dependencies_list_container ywcp_list_container" >

    <p class="toolbar">

        <span class="ywcp_dependencies_list_title">
            <?php _e( 'Dependencies', 'yith-composite-products-for-woocommerce' ); ?>
            <?php echo wc_help_tip( __( 'List of dependencies', 'yith-composite-products-for-woocommerce' ) ); ?>
        </span>

        <span class="expand-close">
            <a href="#" class="ywcp_expand_all"><?php _e( 'Expand all', 'yith-composite-products-for-woocommerce' ); ?></a> /
            <a href="#" class="ywcp_close_all"><?php _e( 'Collapse all', 'yith-composite-products-for-woocommerce' ); ?></a>
        </span>

    </p>

    <div id="ywcp_dependencies_list_container_items" class="sortable">

        <?php if ( ! empty ( $wcp_data_dependencies ) ) : ?>
            <?php YITH_WCP()->admin->load_dependence_list( $post_id, $wcp_data_dependencies, $wcp_data, $wcp_data_dependencies_component_list_data ); ?>
        <?php endif; ?>

    </div>

</div>

<div class="clearfix"></div>

<p class="toolbar no-border">
    <button type="button" class="button ywcp_save_dependencies"><?php _e( 'Save Dependencies', 'yith-composite-products-for-woocommerce' ); ?></button>
    <button type="button" class="button button-primary ywcp_add_dependencies"><?php _e( 'Add Dependence', 'yith-composite-products-for-woocommerce' ); ?></button>
    <div class="clearfix"></div>
</p>
