<?php
/**
 * Add Rule for Account Anniversary
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<tr class="rs-account-anniversary-rule-based-type-row">
	<td>
		<input type = "text"
			name = "rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][level_name]"/>
	</td>
	<td>
		<input type = "number"
			name = "rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][duration]"/>
		<label><b><?php echo esc_html__( 'Year(s)', 'rewardsystem' ); ?></b></label>
	</td>
	<td>
		<input type = "number"
			name = "rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][point_value]"/>
	</td>

	<td class="num">
		<span class="rs-remove-account-anniversary-rule button-secondary"><?php esc_html_e( 'Remove', 'rewardsystem' ); ?></span>
	</td>
</tr>
<?php
