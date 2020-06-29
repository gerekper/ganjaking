<?php
/**
 * Single component dependence options
 *
 * @author  Yithemes
 * @package YITH Composite Products for WooCommerce Premium
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$base_editor_name = 'ywcp_dependencies_component_data_options';
$item_key_name = $item_index.'_'.$wcp_data_key;
$option_ids = isset( $wcp_data_dependencies_components_list_data_item['option_ids'] ) ? $wcp_data_dependencies_components_list_data_item['option_ids']  : array();
?>

<div class="ywcp_dependencieslist_component_single_item">

    <p class="form-field _ywcp_layout_options_field">
        <label><?php echo $wcp_data_single_item['name'] ?><strong><?php echo $wcp_data_single_item['required'] ? '*' : ''; ?></strong></label>
        <?php YITH_WCP_Admin::printSettingsDropdown( $item_key_name, $base_editor_name, 'action_type', YITH_WCP_Admin::getComponentDependenceActionTypeList( $wcp_data_single_item['required'] ), $wcp_data_dependencies_components_list_data_item['action_type'], 'ywcp-dependecies-action-option' ) ?>
        <?php echo wc_help_tip( __( 'Select the type of action for this component', 'yith-composite-products-for-woocommerce' ) ); ?>
    </p>

    <p class="form-field _ywcp_layout_options_field ywcp_dependecies_condition_container">
        <?php YITH_WCP_Admin::printSettingsDropdown( $item_key_name, $base_editor_name, 'selection_type', YITH_WCP_Admin::getComponentDependenceSelectionTypeList( $wcp_data_single_item['required'] ), $wcp_data_dependencies_components_list_data_item['selection_type'], 'ywcp-dependecies-selection-option' ) ?>
        <?php echo wc_help_tip( __( 'Select the type of dependence for this component', 'yith-composite-products-for-woocommerce' ) ); ?>
    </p>

    <p class="form-field _ywcp_layout_options_field ywcp_dependecies_action_container">
        <?php YITH_WCP_Admin::printSettingsDropdown( $item_key_name, $base_editor_name, 'do_type', YITH_WCP_Admin::getComponentDependenceDoTypeList( $wcp_data_single_item['required'] ), $wcp_data_dependencies_components_list_data_item['do_type'], 'ywcp-dependecies-do-option' ) ?>
        <?php echo wc_help_tip( __( 'Select the type of dependence for this component', 'yith-composite-products-for-woocommerce' ) ); ?>
    </p>

    <p class="form-field ywcp_dependencieslist_component_single_item_products_chosen">
        <select name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_key_name , $base_editor_name , 'option_ids' , true ) ?>" class="ywcp_dependence_selection_product_id-select2" multiple="multiple" placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>" data-placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>"><?php
            YITH_WCP_Admin::echo_product_chosen_list( $post_id , $wcp_data_single_item , $option_ids );
            ?></select>
    </p>
    
    
</div>
