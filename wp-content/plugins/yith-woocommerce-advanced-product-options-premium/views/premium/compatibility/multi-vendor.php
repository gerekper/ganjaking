<?php
/**
 * Multi Vendor compatibility View
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 *
 * @var YITH_WAPO_Block $block
 */

$args = array(
    'taxonomy'   => class_exists( 'YITH_Vendors_Taxonomy' ) ? YITH_Vendors_Taxonomy::TAXONOMY_NAME : YITH_Vendors()->get_taxonomy_name(),
    'hide_empty' => false,
);

$vendors_obj = get_terms( $args );

// translators: [ADMIN] Edit block page - When Multi Vendor is activated
$vendors     = array( '' => __( 'No vendor', 'yith-woocommerce-product-add-ons' ) );

foreach ( $vendors_obj as $vendor ) {
    $vendors[ $vendor->term_id ] = $vendor->name;
}

?>
    <!-- Option field -->
    <div class="field-wrap yith-wapo-block-rule-show-to-vendors" >
        <label><?php
            // translators: [ADMIN] Edit block page - When Multi Vendor is activated
            echo esc_html__( 'Vendor', 'yith-woocommerce-product-add-ons' ); ?>:</label>
        <div class="field block-option">
            <?php
            yith_plugin_fw_get_field(
                array(
                    'id'      => 'yith-wapo-block-rule-show-to-vendors',
                    'name'    => 'block_vendor_id',
                    'type'    => 'select',
                    'class'   => 'wc-enhanced-select',
                    'value'   => isset( $block->vendor_id ) ? $block->vendor_id : '',
                    'options' => $vendors,
                ),
                true
            );
            ?>
            <span class="description">
                <?php
                // translators: description in Block editor for Vendor option (YITH Vendors activated is necessary).
                echo esc_html__( 'Choose for which vendor to show this block.', 'yith-woocommerce-product-add-ons' ); ?>
            </span>
        </div>
    </div>
<!-- End option field -->