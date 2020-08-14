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

// default values

$data = array(
    'name'                          => __( 'New Dependence', 'yith-composite-products-for-woocommerce' ),
    'description'                   => '', );

$item_index = isset( $_REQUEST['ywcp_dependence_index'] ) ? $_REQUEST['ywcp_dependence_index']  : 0;
                  
if ( isset( $wcp_data_dependence_single_item ) && ! empty( $wcp_data_dependence_single_item ) ) {
    $item_index = $wcp_data_key;
    $data = $wcp_data_dependence_single_item;
}

$base_editor_name = 'ywcp_dependencies_data';

?>

<div class="ywcp_dependencieslist_container_single_item ywcp_list_container_single_item wc-metabox" >

    <h3><strong></strong><?php echo $data['name']; ?>
        <button type="button" class="button ywcp_remove_dependence"><?php _e( 'Remove', 'yith-composite-products-for-woocommerce' ); ?></button>
    </h3>

    <div class="ywcp_dependencies_list_container_single_item_form">

        <p class="form-field _ywcp_layout_name">
            <label for="_ywcp_layout_name"><?php _e( 'Name' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="text" class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'name' ) ?>" value="<?php echo $data['name'] ?>" placeholder="">
            <?php echo wc_help_tip( __( 'Dependence title', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_description">
            <label for="_ywcp_layout_description"><?php _e( 'Description' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <textarea class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'description' ) ?>" placeholder="" rows="2" cols="20"><?php echo $data['description'] ?></textarea>
            <?php echo wc_help_tip( __( 'Dependence description', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>
        
        <h4><?php _e( 'List of components' , 'yith-composite-products-for-woocommerce' ) ?></h4>

        <div class="ywcp_depencies_container_component">
            
            <?php


            if( isset( $wcp_data ) && $post_id > 0 ) {

                foreach ( $wcp_data as $key => $wcp_data_item ) {

                    $item_key_name = $item_index.'_'.$key;
                    
                    if( ! empty( $wcp_data_item ) ) {

                        $wcp_data_dependencies_components_list_data_item = null;
                        
                        if ( isset( $wcp_data_dependencies_components_list_data[$item_key_name] ) ) {
                            $wcp_data_dependencies_components_list_data_item = $wcp_data_dependencies_components_list_data[$item_key_name];
                        }
                        
                        YITH_WCP()->admin->dependence_list_options_sigle_item_components_item( $key , $wcp_data_item , $post_id , $item_index , $wcp_data_dependencies_components_list_data_item ) ;
                        
                    }

                }

            }

            ?>
            
        </div>
        

    </div>

</div>
