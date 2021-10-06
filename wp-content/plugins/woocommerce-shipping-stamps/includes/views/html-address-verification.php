<?php
/**
 * View template to display verification confirmation.
 *
 * @package WC_Stamps_Integration/View
 */

?>

<p><?php _e( 'Before printing a label the shipping address needs to be verified with USPS.', 'woocommerce-shipping-stamps' ); ?></p>
<p><button type="submit" class="button stamps-action" data-stamps_action="verify_address"><?php _e( 'Continue', 'woocommerce-shipping-stamps' ); ?></button></p>
