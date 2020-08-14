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
?>
<div class="options_group">
     <?php
    // Layout Options

    woocommerce_wp_select(
        array(
            'id'       => '_ywcp_layout_options', 'label' => __( 'Composite product layout', 'yith-composite-products-for-woocommerce' ),
            'options'  => array(
                'list'      => __( 'Simple list', 'yith-composite-products-for-woocommerce' ),
                'accordion' => __( 'Accordion list', 'yith-composite-products-for-woocommerce' ),
                'step'      => __( 'Steps', 'yith-composite-products-for-woocommerce' ),

            ),
            'desc_tip' => true, 'description' => __( 'This option modifies the way the plugin list of components is shown',
            'yith-composite-products-for-woocommerce' ),
        ) );
     
//    echo '<div class="ywcp_layout_options_show_type">';
//
//        woocommerce_wp_select(
//            array(
//                'id' => '_ywcp_layout_options_product_list_position', 'label' => __( 'Position of the list of options',
// 'yith-composite-products-for-woocommerce' ),
//                'options' => array(
//                    'cascading'     => __( 'Cascading', 'yith-composite-products-for-woocommerce' ),
//                    'popup' => __( 'Modal Window', 'yith-composite-products-for-woocommerce' ),
//                ),
//                'desc_tip' => true, 'description' => __( 'This option modifies where the product list is located (work only if the
// option "Option selection style" in the component is set to "Product Thumbnails"', 'yith-composite-products-for-woocommerce' ),
//            ) );
//
//    echo '</div>';

    ?>

 </div>
