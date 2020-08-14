<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly
?>
<div class="yith-plugin-fw-panel-custom-tab-container">
    <h2><?php _e( 'Enabled Columns', 'yith-woocommerce-bulk-product-editing' ) ?></h2>
    <div id="yith-wcbep-enabled-columns-tab-wrapper">
        <?php
        $cols         = YITH_WCBEP_List_Table_Premium::get_default_columns();
        $enabled      = YITH_WCBEP_List_Table_Premium::get_enabled_columns();
        $ever_enabled = array( 'cb', 'ID', 'show' );

        foreach ( $ever_enabled as $id ) {
            if ( isset( $cols[ $id ] ) )
                unset( $cols[ $id ] );
        }

        echo '<table>';
        foreach ( $cols as $key => $value ) {

            $icon_class = 'no';
            if ( in_array( $key, $enabled ) )
                $icon_class = 'yes';
            ?>
            <tr style="display: inline-block; width: 33%;">
                <td>
                    <span class="yith-wcbep-enabled-column-icon dashicons dashicons-<?php echo $icon_class ?>" data-cols-id="<?php echo $key ?>"></span>
                </td>
                <td>
                    <label><?php echo $value ?></label>
                </td>
            </tr>
            <?php
        }
        echo '</table>';
        ?>
    </div>
</div>
<div id="yith-wcbep-enabled-columns-tab-actions">
    <span id="yith-wcbep-enabled-columns-tab-actions-save" class="button button-primary"><?php _e( 'Save', 'yith-woocommerce-bulk-product-editing' ) ?></span>
    <span id="yith-wcbep-enabled-columns-tab-actions-enable-all" class="button"><?php _e( 'Enable All', 'yith-woocommerce-bulk-product-editing' ) ?></span>
    <span id="yith-wcbep-enabled-columns-tab-actions-disable-all" class="button"><?php _e( 'Disable All', 'yith-woocommerce-bulk-product-editing' ) ?></span>

    <div id="yith-wcbep-enabled-columns-tab-actions-saving">
        <!--<div class="yith-wcbep-enabled-columns-tab-actions-saving-container">
            <span class="spinner"></span>
            <span class="text"><?php /*_e( 'Saving...', 'yith-woocommerce-bulk-product-editing' ) */ ?></span>
        </div>-->
        <span class="yith-wcbep-enabled-columns-tab-actions-saving-container loading">
            <?php _e( 'Saving...', 'yith-woocommerce-bulk-product-editing' ) ?>
        </span>
    </div>
</div>