<?php
/**
 * State metabox contents
 *
 * @version 8.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="options_group">
	<input type="hidden" name="bto_state_data[<?php echo $id; ?>][dirty]" value="1"/><?php

	/**
	 * Action 'woocommerce_composite_state_admin_info_html'.
	 *
	 * @param  string  $scenario_id
	 * @param  array   $state_data
	 * @param  array   $composite_data
	 * @param  string  $composite_id
	 */
	do_action( 'woocommerce_composite_state_admin_info_html', $id, $state_data, $composite_data, $composite_id );

	?><div class="hr-section"><?php echo __( 'State Configuration', 'woocommerce-composite-products' ); ?></div><?php

	/**
	 * Action 'woocommerce_composite_state_admin_config_html'.
	 *
	 * @param  string  $scenario_id
	 * @param  array   $state_data
	 * @param  array   $composite_data
	 * @param  string  $composite_id
	 */
	do_action( 'woocommerce_composite_state_admin_config_html', $id, $state_data, $composite_data, $composite_id );
?></div>
