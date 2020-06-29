<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

extract( $args );
$shipping_method = get_post_meta( $post->ID, '_ywcdd_shipping_method', true );

$shipping_zones     = WC_Shipping_Zones::get_zones();
$global_zone        = new WC_Shipping_Zone( 0 );
$shipping_method_av = array();
$url                = admin_url( 'admin.php' );
foreach ( $shipping_zones as $zone ) {

	$shipping_methods = $zone['shipping_methods'];
	$single_zone      = array();
	foreach ( $shipping_methods as $method ) {

		if ( isset( $method->instance_settings['select_process_method'] ) && $post->ID == $method->instance_settings['select_process_method'] ) {

			$required  = isset( $method->instance_settings['set_method_as_mandatory'] ) ? $method->instance_settings['set_method_as_mandatory'] : 'no';
			$params    = array( 'page' => 'wc-settings', 'tab' => 'shipping', 'instance_id' => $method->instance_id );
			$edit_link = esc_url( add_query_arg( $params, $url ) );

			$shipping_method = array(
				'name'      => $method->title,
				'edit_link' => $edit_link,
				'required'  => $required
			);
			$single_zone[]   = $shipping_method;
		}
	}

	if ( count( $single_zone ) > 0 ) {
		$zone_name = $zone['zone_name'];
		$zone_id   = $zone['zone_id'];

		$shipping_method_av[ $zone_id ] = array(
			'zone_name'       => $zone_name,
			'shipping_method' => $single_zone,

		);

	}
}
$global_method = $global_zone->get_shipping_methods();
$single_zone   = array();
foreach ( $global_method as $method ) {

	if ( isset( $method->instance_settings['select_process_method'] ) && $post->ID == $method->instance_settings['select_process_method'] ) {
		$required  = isset( $method->instance_settings['set_method_as_mandatory'] ) ? $method->instance_settings['set_method_as_mandatory'] : 'no';
		$params    = array( 'page' => 'wc-settings', 'tab' => 'shipping', 'instance_id' => $method->instance_id );
		$edit_link = esc_url( add_query_arg( $params, $url ) );

		$shipping_method = array(
			'name'      => $method->title,
			'edit_link' => $edit_link,
			'required'  => $required
		);
		$single_zone[]   = $shipping_method;
	}
}
if ( count( $single_zone ) > 0 ) {
	$zone_name = $global_zone->get_zone_name();
	$zone_id   = version_compare( WC()->version, '3.0.0', '>=' ) ? $global_zone->get_id() : $global_zone->get_zone_id();

	$shipping_method_av[ $zone_id ] = array(
		'zone_name'       => $zone_name,
		'shipping_method' => $single_zone,

	);

}
$deps_html = yith_field_deps_data( $args );
?>
<div id="<?php echo $id ?>-container" <?php echo $deps_html; ?>>
    <div id="ywcdd_shipping_table">
        <label><?php _e( 'Shipping Method', 'yith-woocommerce-delivery-date' ); ?></label>
        <table class="widefat wc-shipping-zone-methods">
            <thead>
            <tr>
                <th><?php _e( 'Shipping Zone', 'yith-woocommerce-delivery-date' ); ?></th>
                <th><?php _e( 'Shipping Method', 'yith-woocommerce-delivery-date' ); ?></th>
                <th><?php _e( 'Required', 'yith-woocommerce-delivery-date' ); ?></th>
            </tr>
            </thead>
            <tbody class="wc-shipping-zone-method-rows">
			<?php if ( count( $shipping_method_av ) > 0 ) {
				foreach ( $shipping_method_av as $key => $method_available ) {

					$zone_name        = $method_available['zone_name'];
					$shipping_methods = $method_available['shipping_method'];
					?>
					<?php
					foreach ( $shipping_methods as $shipping_method ) {
						echo '<tr>';
						echo '<td>' . $zone_name . '</td>';
						echo '<td>' . $shipping_method['name'] . '</td>';
						echo '<td>' . $shipping_method['required'] . '</td>';
						echo '</tr>';
					} ?>

					<?php
				}
				?>

			<?php } else { ?>
                <tr>
                    <td colspan="3" class="ywcdd_no_shipping_method"><?php _e( 'This method is not set in any WooCommerce Shipping Method', 'yith-woocommerce-delivery-date' ); ?></td>

                </tr>
				<?php
			} ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
					<?php
					$url      = admin_url( 'admin.php' );
					$params   = array( 'page' => 'wc-settings', 'tab' => 'shipping' );
					$zone_url = esc_url( add_query_arg( $params, $url ) );
					echo sprintf( '<a href="%s" target="_blank" class="button button-primary button-small">%s</a>', $zone_url, __( 'Manage Shipping Zones', 'yith-woocommerce-delivery-date' ) );
					?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
