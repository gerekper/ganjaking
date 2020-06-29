<?php
$warranty_page_id       = wc_get_page_id('warranty');
$order_status_options   = array();
$warranty_statuses      = warranty_get_statuses();
$warranty_status_options= array();

$saved_rma  = get_option( 'warranty_saved_rma', 0 );
$last_rma   = get_option( 'warranty_last_rma', 0 );

$statuses = wc_get_order_statuses();

foreach ( $statuses as $key => $status ) {
    $key = str_replace( 'wc-', '', $key );
    $order_status_options[ $key ] = $key;
}

foreach ( $warranty_statuses as $warranty_status ) {
    $warranty_status_options[ $warranty_status->slug ] = $warranty_status->name;
}

?>
<div id="warranty_settings_general">

    <?php WC_Admin_Settings::output_fields( $settings['general'] ); ?>

</div>
