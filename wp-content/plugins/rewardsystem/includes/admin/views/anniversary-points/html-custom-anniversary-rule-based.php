<?php
/**
 * Account Anniversary Points Rule Based Type.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<table class="rs-custom-anniversary-rule-based-type widefat striped" >
	<thead>
		<tr class="rs-custom-anniversary-rule-based-type-row">
			<th><?php esc_html_e('Anniversary Name', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Points Value', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Description', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Repeat', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Mandatory Field', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Reward Log', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Remove Rule', 'rewardsystem'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$custom_anniversary_rule = get_option('rs_custom_anniversary_rules', array());
		if (srp_check_is_array($custom_anniversary_rule)) :
			foreach ($custom_anniversary_rule as $key => $value) :
				?>
				<tr class="rs-custom-anniversary-rule-based-type-row">
					<td>
						<input type="text" 
							   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][field_name]"
							   value="<?php echo esc_attr(isset($value['field_name']) ? $value['field_name'] : '' ); ?>" />
					</td>

					<td>
						<input type="number" 
							   name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][point_value]" 
							   value="<?php echo esc_attr(isset($value['point_value']) ? $value['point_value'] : '' ); ?>" />
					</td>

					<td>
						<textarea name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][desc]"><?php echo esc_attr(isset($value['desc']) ? $value['desc'] : '' ); ?></textarea>
					</td>

					<td>
						<input type = "checkbox"
							   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][repeat]"
							   <?php 
								if (isset($value['repeat']) && 'on' == $value['repeat']) {
									?>
									 checked="checked" <?php } ?>/>
					</td>
					
					<td>
						<input type = "checkbox"
							   name = "rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][mandatory]"
							   <?php 
								if (isset($value['mandatory']) && 'on' == $value['mandatory']) {
									?>
									 checked="checked" <?php } ?>/>
					</td>

					<td>
						<textarea name="rs_custom_anniversary_rules[<?php echo esc_attr($key); ?>][reward_log]"><?php echo esc_attr(isset($value['reward_log']) ? $value['reward_log'] : '' ); ?></textarea>
					</td>

					<td>
						<span class="rs-remove-custom-anniversary-rule button-secondary"><?php esc_html_e('Remove', 'rewardsystem'); ?></span>
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
	<tfoot>
		<tr class="rs-custom-anniversary-rule-based-type-row">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><span class="rs-add-custom-anniversary-rule button-primary"><?php esc_html_e('Add', 'rewardsystem'); ?></span></td>
		</tr>
		<tr class="rs-custom-anniversary-rule-based-type-row">
			<th><?php esc_html_e('Anniversary Name', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Points Value', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Description', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Repeat', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Mandatory Field', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Reward Log', 'rewardsystem'); ?></th>
			<th><?php esc_html_e('Remove Rule', 'rewardsystem'); ?></th>
		</tr>
	</tfoot>
</table>
