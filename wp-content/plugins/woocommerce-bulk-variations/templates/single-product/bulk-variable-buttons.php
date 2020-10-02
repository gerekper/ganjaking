<?php

if ( ( WC_Bulk_Variations::instance()->is_bulk_variation_form() || WC_Bulk_Variations::instance()->is_quick_view ) && ! WC_Bulk_Variations::instance()->is_only_bulk_variation_form() ) {
	?>
    <input class="button btn-bulk" type="button" value="<?php _e( 'Bulk Order Form', 'woocommerce-bulk-variations' ); ?>"/>
    <input class="button btn-single" type="button" value="<?php _e( 'Singular Order Form', 'woocommerce-bulk-variations' ); ?>"/>
	<?php
} elseif ( ( WC_Bulk_Variations::instance()->is_bulk_variation_form() || WC_Bulk_Variations::instance()->is_quick_view ) && WC_Bulk_Variations::instance()->is_only_bulk_variation_form() ) {
	?>
    <input class="button btn-bulk" type="button" value="<?php _e( 'Bulk Order Form', 'woocommerce-bulk-variations' ); ?>"/>
	<?php
}

