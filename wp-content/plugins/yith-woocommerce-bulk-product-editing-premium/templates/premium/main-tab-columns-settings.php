<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

?>

<!-- - - - - - - - - - - - - -   C O L U M N S   S E T T I N G S   - - - - - - - - - - - - - -->
<div id="yith-wcbep-columns-settings-wrapper">
    <span class="dashicons dashicons-no yith-wcbep-close"></span>
    <h2><?php _e( 'Show/Hide Columns', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
    <?php
    $cols   = YITH_WCBEP_List_Table_Premium::get_enabled_default_columns();
    $hidden = YITH_WCBEP_List_Table_Premium::get_default_hidden();

    echo '<div class="yith-wcbep-columns-settings-checkboxes">';
    foreach ( $cols as $key => $value ) {
        if ( $key == 'cb' || $key == 'show' )
            continue;

        $checked = 'checked="checked"';
        if ( in_array( $key, $hidden ) )
            $checked = '';
        ?>
        <label><input id="yith-wcbep-col-set-<?php echo $key ?>" data-cols-id="<?php echo $key ?>" type="checkbox" <?php echo $checked ?> >
            <?php echo $value ?></label>
        <?php
    }
    echo '</div>';
    ?>
    <div style="clear: both"></div>
    <div id="yith-wcbep-columns-settings-button-wrap">
        <input id="yith-wcbep-columns-settings-select-all" type="button" value="<?php _e( 'Select All', 'yith-woocommerce-bulk-product-editing' ) ?>">
        <input id="yith-wcbep-columns-settings-unselect-all" type="button" value="<?php _e( 'Unselect', 'yith-woocommerce-bulk-product-editing' ) ?>">
    </div>
</div>