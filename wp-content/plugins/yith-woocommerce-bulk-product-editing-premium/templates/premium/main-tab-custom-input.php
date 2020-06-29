<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

$enabled_columns = yith_wcbep_get_enabled_columns();
?>

<!-- - - - - - - - - - - - - -   C   U   S   T   O   M       I   N   P   U   T   - - - - - - - - - - - - - -->
<!-- Custom input Simple Text -->
<div id="yith-wcbep-custom-input">
    <input type="text"/>
</div>

<!-- Custom Input Text Area -->
<div id="yith-wcbep-custom-input-textarea">
    <textarea></textarea>

    <div id="yith-wcbep-custom-input-textarea-button-wrap">
        <input id="yith-wcbep-custom-input-textarea-button-save" type="button" class="button button-primary button-large" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-textarea-button-cancel" type="button" class="button button-secondary button-large" value="<?php _e( 'Cancel', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
</div>

<!-- Custom Input DATE -->
<div id="yith-wcbep-custom-input-date" class="yith-wcbep-custom-input">
    <input type="text"/>
</div>

<!-- Custom Input DOWNLOADABLE FILES -->
<div id="yith-wcbep-custom-input-downloadable-files">
    <table id="yith-wcbep-custom-input-downloadable-files-default-row">
        <tr>
            <td class="sort"></td>
            <td class="file_name"><input type="text" class="input_text" placeholder="<?php _e( 'File Name', 'yith-woocommerce-bulk-product-editing' ); ?>" name="_wc_file_names[]" value=""/></td>
            <td class="file_url"><input type="text" class="input_text" placeholder="<?php _e( "http://", 'yith-woocommerce-bulk-product-editing' ); ?>" name="_wc_file_urls[]" value=""/></td>
            <td class="file_url_choose" width="1%"><input class="yith-wcbep-custom-input-downloadable-files-choose-file button button-secondary button-large" type="button"
                                                          value="<?php _e( 'Choose file', 'yith-woocommerce-bulk-product-editing' ); ?>"/>
            <td width="1%"><span class="delete"><?php _e( 'Delete', 'yith-woocommerce-bulk-product-editing' ); ?></span></td>
        </tr>
    </table>
    <table id="yith-wcbep-custom-input-downloadable-files-table">

    </table>
    <div id="yith-wcbep-custom-input-downloadable-files-button-wrap">
        <input id="yith-wcbep-custom-input-downloadable-files-button-save" type="button" class="button button-primary button-large" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-downloadable-files-button-add" type="button" class="button button-secondary button-large" value="<?php _e( 'Add File', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-downloadable-files-button-cancel" type="button" class="button button-secondary button-large" value="<?php _e( 'Cancel', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
</div>

<!-- Custom Input Image Gallery -->
<div id="yith-wcbep-custom-input-gallery">
    <div id="yith-wcbep-custom-input-gallery-container"></div>
    <div id="yith-wcbep-custom-input-gallery-button-wrap">
        <input id="yith-wcbep-custom-input-gallery-button-save" type="button" class="button button-primary button-large" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-gallery-button-add" type="button" class="button button-secondary button-large" value="<?php _e( 'Add Images to Product Gallery', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-gallery-button-cancel" type="button" class="button button-secondary button-large" value="<?php _e( 'Cancel', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
</div>

<!-- Custom Input IMAGE -->
<div id="yith-wcbep-custom-input-image" class="yith-wcbep-custom-input">
    <div class="yith-wcbep-custom-input-image-container"><img/></div>
    <input type="hidden" class="yith-wcbep-hidden-image-value">

    <div id="yith-wcbep-custom-input-image-button-wrap">
        <input id="yith-wcbep-custom-input-image-button-save" type="button" class="button button-primary button-large" value="<?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-image-button-remove" type="button" class="button button-secondary button-large" value="<?php _e( 'Remove Image', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-custom-input-image-button-cancel" type="button" class="button button-secondary button-large" value="<?php _e( 'Cancel', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
</div>

<!-- Message for not editable fields-->
<div id="yith-wcbep-message-not-editable">
    <?php _e( 'This field is not editable because this is a variation!', 'yith-woocommerce-bulk-product-editing' ) ?>
</div>

<?php
// C A T E G O R I E S   CUSTOM INPUT
if ( yith_wcbep_is_column_enabled( 'categories' ) ) {
    $cat_args   = array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC'
    );
    $categories = yith_wcbep_get_terms( $cat_args );
    if ( !empty( $categories ) ) {
        ?>
        <div id="yith-wcbep-custom-input-categories" class="yith-wcbep-custom-input">
            <select id="yith-wcbep-custom-input-categories-select" class="chosen yith-wcbep-chosen" multiple xmlns="http://www.w3.org/1999/html">
                <?php
                foreach ( $categories as $c ) {
                    ?>
                    <option value="<?php echo $c->term_id; ?>"><?php echo $c->name; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <?php
    }
} ?>

<?php
// A T T R I B U T E S    Custom Input
$attribute_taxonomies = wc_get_attribute_taxonomies();
if ( $attribute_taxonomies ) {
    foreach ( $attribute_taxonomies as $tax ) {
        if ( !yith_wcbep_is_column_enabled( 'attr_pa_' . $tax->attribute_name ) )
            continue;

        $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
        $terms                   = yith_wcbep_get_terms( array( 'taxonomy' => $attribute_taxonomy_name, 'hide_empty' => '0' ) );
        if ( count( $terms ) > 0 ) {
            ?>
            <div id="yith-wcbep-custom-input-attributes-<?php echo $attribute_taxonomy_name; ?>" class="yith-wcbep-custom-input yith-wcbep-custom-input-attributes">
                <div class="yith-wcbep-custom-input-attributes-checkbox-wrap">
                    <label for="yith-wcbep-custom-input-attributes-visible-<?php echo $attribute_taxonomy_name; ?>"><?php _e( 'is visible', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                    <input
                            class="yith-wcbep-custom-input-attributes-visible" id="yith-wcbep-custom-input-attributes-visible-<?php echo $attribute_taxonomy_name; ?>" type="checkbox">
                    <label for="yith-wcbep-custom-input-attributes-variations-<?php echo $attribute_taxonomy_name; ?>"><?php _e( 'used for variations', 'yith-woocommerce-bulk-product-editing' ) ?></label><input
                            class="yith-wcbep-custom-input-attributes-variations" id="yith-wcbep-custom-input-attributes-variations-<?php echo $attribute_taxonomy_name; ?>" type="checkbox">
                </div>
                <select class="chosen yith-wcbep-chosen yith-wcbep-custom-input-attributes-select" multiple xmlns="http://www.w3.org/1999/html">
                    <?php
                    foreach ( $terms as $t ) {
                        ?>
                        <option value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }
    }
}
?>

<?php
do_action( 'yith_wcbep_extra_custom_input' );
?>


