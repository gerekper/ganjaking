<?php

defined( 'ABSPATH' ) or exit;

$edit = isset( $_REQUEST['edit'] ) ? $_REQUEST['edit'] : '';

$attribute = NULL;
$attribute_taxonomies = wc_get_attribute_taxonomies();

if ( $attribute_taxonomies ) {
    foreach ( $attribute_taxonomies as $tax ) {
        if ( $tax->attribute_name == $edit ) {
            $attribute = $tax;
        }
    }
}

$attribute_id           = isset( $attribute->attribute_id )          ? $attribute->attribute_id : '';
$attribute_name         = isset( $attribute->attribute_name )        ? $attribute->attribute_name : '';
$attribute_label        = isset( $attribute->attribute_label )       ? $attribute->attribute_label : '';
$attribute_public       = isset( $attribute->attribute_public )      ? $attribute->attribute_public : '';
$attribute_type         = isset( $attribute->attribute_type )        ? $attribute->attribute_type : '';
$attribute_orderby      = isset( $attribute->attribute_orderby )     ? $attribute->attribute_orderby : '';

?>

<h2><?php echo $attribute ? __('Edit Product Attribute', 'yith-frontend-manager-for-woocommerce') : __('Add New Product Attribute', 'yith-frontend-manager-for-woocommerce'); ?></h2>

<form name="post" action="<?php echo $section_uri; ?>" method="post" id="post">

    <?php if ( $attribute_label != '' ) : ?>

        <input type="hidden" name="id" value="<?php echo $attribute_id; ?>">
        <input type="hidden" name="act" value="edit">
        <input type="hidden" name="edit" value="<?php echo $attribute_label; ?>">

    <?php else : ?>

        <input type="hidden" name="act" value="new">

    <?php endif; ?>

    <div class="options_group">

        <p class="form-field">
            <label for="attribute_name"><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="attribute_label" value="<?php echo $attribute_label; ?>" id="attribute_label"><br />
            <i><?php echo __('Attribute name (shown on the frontend).', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <p class="form-field">
            <label for="attribute_label"><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="hidden" name="attribute_name_old" value="<?php echo $attribute_name; ?>" id="attribute_name_old">
            <input type="text" name="attribute_name" value="<?php echo $attribute_name; ?>" id="attribute_name"><br />
            <i><?php echo __('Unique slug/reference to the attribute; it must be shorter than 28 characters.', 'yith-frontend-manager-for-woocommerce');
                ?></i>
        </p>

        <p class="form-field">
            <label for="attribute_public"><?php echo __('Enable Archives?', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="checkbox" name="attribute_public" value="1" <?php checked( $attribute_public, 1, true ); ?> id="attribute_public"><br />
            <i><?php echo __('Enable this option, if you want this attribute to have product archives in your store.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>
    
        <p class="form-field">
            <label for="attribute_type">
                <?php echo __('Type', 'yith-frontend-manager-for-woocommerce'); ?>
            </label>

            <select name="attribute_type" id="attribute_type">
                <?php foreach ( wc_get_attribute_types() as $key => $value ) : ?>
                    <?php $current_attribute_type = ! empty( $tax ) ? $tax->attribute_type : ''; ?>
                    <option <?php selected( $key, $current_attribute_type, true )?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
                <?php endforeach; ?>

                <?php

                /**
                 * Deprecated action in favor of product_attributes_type_selector filter.
                 *
                 * @deprecated 2.4.0
                 */
                do_action( 'woocommerce_admin_attribute_types' );
                ?>
            </select>
            <br />
        </p>
    
        <p class="form-field">
            <label><?php echo __('Default sort order', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <select name="attribute_orderby">
                <?php
                $yith_wcfm_attribute_orderby = apply_filters( 'yith_wcfm_attribute_orderby', array(
                    'menu_order' => __('Custom ordering', 'yith-frontend-manager-for-woocommerce'),
                    'name' => __('Name', 'yith-frontend-manager-for-woocommerce'),
                    'name_num' => __('Name (numeric)', 'yith-frontend-manager-for-woocommerce'),
                    'id' => __('Term ID', 'yith-frontend-manager-for-woocommerce'),
                ));
                foreach ( $yith_wcfm_attribute_orderby as $key => $value) { echo '<option value="' . $key . '" ' . selected( $key, $attribute_orderby ) . '>' . $value . '</option>'; }
            ?></select><br />
            <i><?php echo __('Determines the sort order of the terms on the frontend shop product pages. If using custom ordering, you can drag and drop the terms in this attribute.', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>
    </div>

    <input type="submit" value="Save" />

</form>