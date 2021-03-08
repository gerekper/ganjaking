<?php
/**
 * Scenario metabox contents
 *
 * @version 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="options_group">
	<input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][dirty]" value="1"/><?php

	/**
	 * Action 'woocommerce_composite_scenario_admin_info_html'.
	 *
	 * @param  string  $scenario_id
	 * @param  array   $scenario_data
	 * @param  array   $composite_data
	 * @param  string  $composite_id
	 */
	do_action( 'woocommerce_composite_scenario_admin_info_html', $id, $scenario_data, $composite_data, $composite_id );

	?><div class="hr-section"><?php echo __( 'Conditions', 'woocommerce-composite-products' ); ?></div><?php

	/**
	 * Action 'woocommerce_composite_scenario_admin_config_html'.
	 *
	 * @param  string  $scenario_id
	 * @param  array   $scenario_data
	 * @param  array   $composite_data
	 * @param  string  $composite_id
	 */
	do_action( 'woocommerce_composite_scenario_admin_config_html', $id, $scenario_data, $composite_data, $composite_id );

	?><div class="hr-section"><?php echo __( 'Actions', 'woocommerce-composite-products' ); ?></div><?php

	/**
	 * Action 'woocommerce_composite_scenario_admin_actions_html'.
	 *
	 * @param  string  $scenario_id
	 * @param  array   $scenario_data
	 * @param  array   $composite_data
	 * @param  string  $composite_id
	 */
	do_action( 'woocommerce_composite_scenario_admin_actions_html', $id, $scenario_data, $composite_data, $composite_id );

?></div>
