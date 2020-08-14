<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

$custom_taxonomies = YITH_WCBEP_Custom_Taxonomies_Manager::get_custom_taxonomies();

$product_taxonomies          = array();
$product_taxonomies_excluded = array( 'product_type', 'product_cat', 'product_tag', 'yith_product_brand', 'product_shipping_class', 'product_visibility' );
$product_taxonomy_names      = get_object_taxonomies( 'product', 'names' );
$product_taxonomy_names      = array_diff( $product_taxonomy_names, $product_taxonomies_excluded );
// remove attributes
foreach ( $product_taxonomy_names as $key => $taxonomy ) {
    if ( strpos( $taxonomy, 'pa_' ) !== 0 ) {
        if ( $tax = get_taxonomy( $taxonomy ) ) {
            $labels = get_taxonomy_labels( $tax );
            $name   = isset( $labels->name ) ? $labels->name : $taxonomy;

            $product_taxonomies[ $taxonomy ] = $name;
        }
    }
}
?>

<form method="post">
    <div class="yith-plugin-fw-panel-custom-tab-container">
        <h2><?php _e( 'Custom Taxonomies', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
        <div id="yith-wcbep-custom-taxonomies-tab-wrapper">

            <select name="yith-wcbep-custom-taxonomies[]" class="wc-enhanced-select" multiple
                    data-placeholder="<?php _e( 'Choose Taxonomies', 'yith-woocommerce-bulk-product-editing' ); ?>"
                    style="width:100%">
                <?php
                foreach ( $product_taxonomies as $slug => $name ) {
                    ?>
                    <option value="<?php echo $slug; ?>" <?php selected( in_array( $slug, $custom_taxonomies ), true ) ?> ><?php echo $name; ?></option>
                    <?php
                }
                ?>
            </select>

        </div>
    </div>

    <div id="yith-wcbep-custom-fields-tab-actions">
        <input type="submit" id="yith-wcbep-custom-fields-tab-actions-save"
               class="button button-primary" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
    <?php
    wp_nonce_field( 'yith_wcbep_save_custom_taxonomies', 'yith_wcbep_nonce', false );
    ?>
</form>