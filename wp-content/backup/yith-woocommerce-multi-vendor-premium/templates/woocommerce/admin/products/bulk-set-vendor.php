<fieldset class="inline-edit-col-center">
    <div class="inline-edit-col">
        <span class="title inline-edit-charts-label"><?php echo YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ) ?></span>
        <ul class="vendors-list product_vendors-list cat-checklist product_cat-checklist">
            <?php
            $vendors = YITH_Vendors()->get_vendors();
            $vendor_taxonomy_name = YITH_Vendors()->get_taxonomy_name();
            $no_vendor = sprintf( __( 'No %s' ), strtolower( YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ) ) );
            echo "<li id='vendor-0'>
                                <label class='selectit'>
                                    <input  value='0' 
                                            name='tax_input[{$vendor_taxonomy_name}]'
                                            id='in-vendor-store-0'
                                            type='radio'>
                                            {$no_vendor}
                                </label>";
            if ( ! empty( $vendors ) ) {
                foreach ( $vendors as $vendor ) {
                    echo "<li id='vendor-{$vendor->id}'>
                                <label class='selectit'>
                                    <input  value='{$vendor->slug}' 
                                            name='tax_input[{$vendor_taxonomy_name}]'
                                            id='in-vendor-store-{$vendor->id}'
                                            type='radio'>
                                            {$vendor->name}
                                </label>";
                }
            }
            ?>
        </ul>
    </div>
</fieldset>