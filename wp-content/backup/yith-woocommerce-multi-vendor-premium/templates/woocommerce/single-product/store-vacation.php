<?php
$vendor = isset( $vendor ) ? $vendor : yith_get_vendor( 'current', 'product' );
if( $vendor->is_valid() && $vendor->vacation_message ) : ?>
    <?php
    /* Support for MultiLingua plugins */
    $vendor_vacation_message = call_user_func( '__', $vendor->vacation_message, 'yith-woocommerce-product-vendors' ); ?>
    <div class="vacation woocommerce-info">
        <i class="far fa-calendar-times" aria-hidden="true"></i>
        <?php echo $vendor_vacation_message; ?>
    </div>
<?php endif; ?>