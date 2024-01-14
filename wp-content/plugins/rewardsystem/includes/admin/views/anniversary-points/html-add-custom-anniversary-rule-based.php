<?php
/**
 * Add Custom Anniversary Points Rule Based Type.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<tr class="rs-custom-anniversary-rule-based-type-row">
	<td>
		<input type = "text"
			   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][field_name]"/>
	</td>

	<td>
		<input type = "number"
			   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][point_value]"/>
	</td>

	<td>
		<textarea name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][desc]"><?php esc_html_e('Select the {anniversary_name} date to earn {anniversary_points}', 'rewardsystem'); ?></textarea>
	</td>

	<td>
		<input type="checkbox"
			   name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][repeat]"/>
	</td>
	
	<td>
		<input type = "checkbox"
			   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][mandatory]"/>
	</td>

	<td>
		<textarea name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][reward_log]"><?php esc_html_e('Points earned for {anniversary_name}', 'rewardsystem'); ?></textarea>
	</td>

	<td class="num">
		<span class="rs-remove-custom-anniversary-rule button-secondary"><?php esc_html_e('Remove', 'rewardsystem'); ?></span>
	</td>
</tr>
