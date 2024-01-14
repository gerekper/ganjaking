<?php
/**
 * Account Anniversary Points Rule Based Type.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class="rs-account-anniversary-rule-based-type widefat striped" >
	<thead>
		<tr class="rs-account-anniversary-rule-based-type-row">
			<th><?php esc_html_e( 'Level Name', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Duration', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Points Value', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$account_anniversary_rule = get_option( 'rs_account_anniversary_rules', array() );
		if ( srp_check_is_array( $account_anniversary_rule ) ) :
			foreach ( $account_anniversary_rule as $key => $value ) :
				?>
				<tr class="rs-account-anniversary-rule-based-type-row">
					<td>
						<input type="text" 
							name = "rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][level_name]"
							value="<?php echo esc_attr( isset( $value['level_name'] ) ? $value['level_name'] : '' ); ?>" />
					</td>

					<td>
						<input type="number" 
							name="rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][duration]" 
							value="<?php echo esc_attr( isset( $value['duration'] ) ? $value['duration'] : '' ); ?>" />
						<label><b><?php echo esc_html__( 'Year(s)', 'rewardsystem' ); ?></b></label>
					</td>

					<td>
						<input type="number" 
							name="rs_account_anniversary_rules[<?php echo esc_attr( $key ); ?>][point_value]" 
							value="<?php echo esc_attr( isset( $value['point_value'] ) ? $value['point_value'] : '' ); ?>" />
					</td>

					<td>
						<span class="rs-remove-account-anniversary-rule button-secondary"><?php esc_html_e( 'Remove', 'rewardsystem' ); ?></span>
					</td>
				</tr>
				<?php
			endforeach;
		endif;
		?>
	</tbody>
	<tfoot>
		<tr class="rs-account-anniversary-rule-based-type-row">
			<td></td>
			<td></td>
			<td></td>
			<td><span class="rs-add-account-anniversary-rule button-primary"><?php esc_html_e( 'Add', 'rewardsystem' ); ?></span></td>
		</tr>
		<tr class="rs-account-anniversary-rule-based-type-row">
			<th><?php esc_html_e( 'Level Name', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Duration', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Points Value', 'rewardsystem' ); ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ); ?></th>
		</tr>
	</tfoot>
</table>
