<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

$custom_fields = YITH_WCBEP_Custom_Fields_Manager::get_custom_fields();

$custom_fields = empty( $custom_fields ) || !is_array( $custom_fields ) ? array( '' ) : $custom_fields;
?>

<form method="post">
    <div class="yith-plugin-fw-panel-custom-tab-container">
        <h2><?php _e( 'Custom Fields', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
        <div id="yith-wcbep-custom-fields-tab-wrapper">

            <?php foreach ( $custom_fields as $custom_field ) : ?>
                <div class="yith-wcbep-custom-field-wrap">
                    <input type="text" name="yith-wcbep-custom-field[]" value="<?php echo $custom_field; ?>">
                    <span class="dashicons dashicons-no yith-wcbep-custom-field-delete"></span>
                    <span class="dashicons dashicons-plus yith-wcbep-custom-field-add"></span>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div id="yith-wcbep-custom-fields-tab-actions">
        <input type="submit" id="yith-wcbep-custom-fields-tab-actions-save"
               class="button button-primary" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
    <?php
    wp_nonce_field( 'yith_wcbep_save_custom_fields', 'yith_wcbep_nonce', false );
    ?>
</form>