<form name="location_picker" method="post">
	<?php woocommerce_catalog_restrictions_country_input( wc_cvo_restrictions()->get_location_for_current_user() ); ?>

    <input type="hidden" name="woocommerce_catalog_restrictions_location_picker" value="shortcode"/>
    <input type="submit" name="save-location" value="<?php _e( 'Set Location', 'wc_catalog_restrictions' ); ?>"/>
</form>
