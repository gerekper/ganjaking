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

$is_less_than_2_7 = version_compare( WC()->version, '2.7', '<' );

// default values

$data = array(
    'name'                          => __( 'New Component', 'yith-composite-products-for-woocommerce' ),
    'description'                   => '',
    'option_type'                   => 'product',
    'option_type_product_id_values' => array(),
    'option_type_cat_id_values'     => '',
    'option_type_tag_id_values'     => '',
    'option_style'                  => 'dropdown',
    'min_quantity'                  => 1,
    'max_quantity'                  => 1,
    'discount'                      => '',
    'apply_discount_to_sale_price'  => false,
    'thumb'                         => false,
    'required'                      => false,
    'exclusive'                     => false,
);

$base_editor_name = 'ywcp_component_data';

$item_index = isset( $_REQUEST['ywcp_component_index'] ) ? $_REQUEST['ywcp_component_index']  : 0;
                  
if ( isset( $wcp_data_single_item ) && ! empty( $wcp_data_single_item ) ) {
    $item_index = $wcp_data_key;
    $data = $wcp_data_single_item;
}

if ( ! isset( $data['thumb'] ) ) { $data['thumb'] = false; }
if ( ! isset( $data['exclusive'] ) ) { $data['exclusive'] = false; }
if ( ! isset( $data['discount'] ) ) { $data['discount'] = ''; }
if ( ! isset( $data['apply_discount_to_sale_price'] ) ) { $data['apply_discount_to_sale_price'] = false; }
if ( ! isset( $data['sold_individually'] ) ) { $data['sold_individually'] = ''; }
if ( ! isset( $data['product_order'] ) ) { $data['product_order'] = ''; }
if ( ! isset( $data['product_order_direction'] ) ) { $data['product_order_direction'] = ''; }

$list_option_type = array(
    'product' => _x( 'Products' , 'product inclusion type' , 'yith-composite-products-for-woocommerce' ),
    'product_categories' => _x( 'Categories' , 'product inclusion type' , 'yith-composite-products-for-woocommerce' ),
    'product_tags' => _x( 'Tags', 'product inclusion type' , 'yith-composite-products-for-woocommerce' ),
);


$list_option_type_style = array(
    'dropdowns'     => __( 'Dropdown', 'yith-composite-products-for-woocommerce' ),
    'thumbnails' => __( 'Product thumbnails', 'yith-composite-products-for-woocommerce' ),
    'radio' => __( 'Radio button', 'yith-composite-products-for-woocommerce' ),
);

$list_option_products_order = YITH_WCP_Admin::getWooCommerceOrderOptions();

$list_option_products_direction = array(
    'asc'     => __( 'Ascendent', 'yith-composite-products-for-woocommerce' ),
    'desc' => __( 'Descendent', 'yith-composite-products-for-woocommerce' ),
);
?>

<div class="ywcp_components_list_container_single_item ywcp_list_container_single_item wc-metabox" >

    <h3><?php echo $data['name']; ?>  <em><?php echo $data['required'] ? '['._x( 'Required', 'admin list advice' , 'yith-composite-products-for-woocommerce' ).']' : ''; ?></em> <em><?php echo $data['exclusive'] ? '['.__( 'Exclusive', 'admin list advice' , 'yith-composite-products-for-woocommerce' ).']' : ''; ?></em>
        <button type="button" class="button ywcp_remove_component"><?php _e( 'Remove', 'yith-composite-products-for-woocommerce' ); ?></button>
    </h3>

    <div class="ywcp_components_list_container_single_item_form">

        <p class="form-field _ywcp_layout_name">
            <label for="_ywcp_layout_name"><?php _e( 'Name' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="text" class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'name' ) ?>" value="<?php echo $data['name'] ?>" placeholder="">
            <?php echo wc_help_tip( __( 'Component title', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_description">
            <label for="_ywcp_layout_description"><?php _e( 'Description' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <textarea class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'description' ) ?>" placeholder="" rows="2" cols="20"><?php echo $data['description'] ?></textarea>
            <?php echo wc_help_tip( __( 'Component description', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_options_field">
            <label><?php _e( 'Selection type' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <?php YITH_WCP_Admin::printSettingsDropdown( $item_index , $base_editor_name , 'option_type' , $list_option_type , $data['option_type'] , 'ywcp-product-search' ) ?>
            <?php echo wc_help_tip( __( 'Select how to retrieve the list of products for this component', 'yith-composite-products-for-woocommerce' ) )
            ; ?>
        </p>

        <p class="form-field ywcp-product-search-container ywcp_layout_options_container">
            <label></label>

            <?php if( $is_less_than_2_7 ) : ?>

            <input type="hidden" class="wc-product-search" style="width: 50%;" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'option_type_product_id_values' ) ?>" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-exclude="<?php echo intval( $post_id ); ?>" data-selected="<?php
            $product_ids = array_filter( array_map( 'absint', explode( ',' , isset( $data['option_type_product_id_values'] ) ? $data['option_type_product_id_values'] : '' ) ) );
            $json_ids    = array();

            foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                if ( is_object( $product ) ) {
                    $json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
                }
            }

            echo esc_attr( json_encode( $json_ids ) );
            ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />

            <?php else: ?>

            <select class="wc-product-search" multiple="multiple" style="width: 50%;" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'option_type_product_id_values' ) ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products" data-exclude="<?php echo intval( $post_id ); ?>">
                <?php

                if ( isset( $data['option_type_product_id_values'] ) && is_array( $data['option_type_product_id_values'] ) ) {
                    $option_type_product_id_values = $data['option_type_product_id_values'];
                } else {
                    $option_type_product_id_values = array();
                }

                $product_ids = array_filter( array_map( 'absint', $option_type_product_id_values ) );

                foreach ( $product_ids as $product_id ) {
                    $product = wc_get_product( $product_id );
                    if ( is_object( $product ) ) {
                        echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                    }
                }
                ?>
            </select>

            <?php endif ?>

        </p>

        <p class="form-field ywcp-categories-search-container ywcp_layout_options_container">
            <select name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'option_type_cat_id_values' , true ) ?>" class="categories_id-select2" multiple="multiple" placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>" data-placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>"><?php
                $categories_array = isset( $data['option_type_cat_id_values'] ) && is_array( $data['option_type_cat_id_values'] ) ? $data['option_type_cat_id_values'] : array();
                YITH_WCP_Admin::echo_product_categories_childs_of( 0, 0, $categories_array );
             ?></select>
        </p>
        
        <p class="form-field ywcp-tags-search-container ywcp_layout_options_container">
            <select name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'option_type_tag_id_values' , true ) ?>" class="categories_id-select2" multiple="multiple" placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>" data-placeholder="<?php _e( 'Applied to...' , 'yith-composite-products-for-woocommerce' ) ?>"><?php
                $tags_array = isset( $data['option_type_tag_id_values'] ) && is_array( $data['option_type_tag_id_values'] ) ? $data['option_type_tag_id_values'] : array();
                YITH_WCP_Admin::echo_product_tags_childs_of( 0, $tags_array );
                ?></select>
        </p>

        <p class="form-field _ywcp_layout_options_field">
            <label><?php _e( 'Sort products by' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <?php YITH_WCP_Admin::printSettingsDropdown( $item_index , $base_editor_name , 'product_order' , $list_option_products_order , $data['product_order'] ) ?>
            <?php echo wc_help_tip( __( 'Select the products order', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>
        <p class="form-field _ywcp_layout_options_field">
            <?php YITH_WCP_Admin::printSettingsDropdown( $item_index , $base_editor_name , 'product_order_direction' , $list_option_products_direction , $data['product_order_direction'] ) ?>
            <?php echo wc_help_tip( __( 'Select the products order ASC|DESC', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_options_field">
            <label><?php _e( 'Option selection style' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <?php YITH_WCP_Admin::printSettingsDropdown( $item_index , $base_editor_name , 'option_style' , $list_option_type_style , $data['option_style'] ) ?>
            <?php echo wc_help_tip( __( 'Select how to show the list of products', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_apply_discount">
            <label for="_ywcp_layout_required"><?php _e( 'Counted Separately' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="checkbox" class="checkbox" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'sold_individually' ) ?>"  value="open" <?php checked( true , $data['sold_individually'] ) ?>>
            <?php echo wc_help_tip( __( 'Check if you want the component quantity to be calculated as a independent value, no matter how many composite products are added to the cart.', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_min">
            <label for="_ywcp_layout_min"><?php _e( 'Min quantity' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="number" class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'min_quantity' ) ?>" value="<?php echo $data['min_quantity'] ?>" placeholder="" min="0">
            <?php echo wc_help_tip( __( 'The mimimum quantity required', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_max">
            <label for="_ywcp_layout_max"><?php _e( 'Max quantity' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="number" class="short" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'max_quantity' ) ?>" value="<?php echo $data['max_quantity'] ?>" placeholder="" min="0">
            <?php echo wc_help_tip( __( 'The maximum selectable quantity', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_discount">
            <label for="_ywcp_layout_name"><?php _e( 'Discount %' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="text" class="group_discount input-text wc_input_decimal" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'discount' ) ?>" value="<?php echo $data['discount'] ?>" placeholder="">
            <?php echo wc_help_tip( __( 'Discount applied. Note: it works only if "Per-Item Pricing" is checked',
                'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_apply_discount">
            <label for="_ywcp_layout_required"><?php _e( 'Apply discount to sale price' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="checkbox" class="checkbox" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'apply_discount_to_sale_price' ) ?>"  value="open" <?php checked( true , $data['apply_discount_to_sale_price'] ) ?>>
            <?php echo wc_help_tip( __( 'Check if you want to combine this discount to sale price for the selected product',
                'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_thumb">
            <label for="_ywcp_layout_thumb"><?php _e( 'Replace thumbnail' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="checkbox" class="checkbox" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'thumb' ) ?>"  value="open" <?php checked( true , $data['thumb'] ) ?>>
            <?php echo wc_help_tip( __( 'Check if you want replace the composite product thumbnail', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_required">
            <label for="_ywcp_layout_required"><?php _e( 'Required' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="checkbox" class="checkbox" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'required' ) ?>"  value="open" <?php checked( true , $data['required'] ) ?>>
            <?php echo wc_help_tip( __( 'Check if you want to set this component as required', 'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

        <p class="form-field _ywcp_layout_exclusive">
            <label for="_ywcp_layout_exclusive"><?php _e( 'Exclusive selection' , 'yith-composite-products-for-woocommerce' ) ?></label>
            <input type="checkbox" class="checkbox" style="" name="<?php echo YITH_WCP_Admin::getSettingsEditorName( $item_index , $base_editor_name , 'exclusive' ) ?>"  value="open" <?php checked( true , $data['exclusive'] ) ?>>
            <?php echo wc_help_tip( __( 'Check if you want that the current product cannot be selected in other components',
                'yith-composite-products-for-woocommerce' ) ); ?>
        </p>

    </div>

</div>
