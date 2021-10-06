<?php
/**
 * Range Based Points Rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="rs-range-based-rules widefat striped" >
	<thead>
		<tr class="rs_range_based_rules_data">
			<th><?php esc_html_e( 'Min Cart Total', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Max Cart Total', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Reward Type', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Reward Points Value/Percentage', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Validity', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$range_data = get_option( 'rs_range_based_points', array() ) ;
		if ( srp_check_is_array( $range_data ) ) :
			foreach ( $range_data as $key => $value ) :
				$reward_type = isset( $value[ 'type' ] ) ? $value[ 'type' ] : 1 ;
				?>
				<tr class="rs_range_based_rules_data">
					<td>
						<input type="text" 
							   name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][min_value]" 
							   value="<?php echo esc_attr( isset( $value[ 'min_value' ] ) ? $value[ 'min_value' ] : ''  ) ; ?>" />
					</td>

					<td>
						<input type="text" 
							   name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][max_value]" 
							   value="<?php echo esc_attr( isset( $value[ 'max_value' ] ) ? $value[ 'max_value' ] : ''  ) ; ?>" />
					</td>

					<td>
						<select name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][type]" id="rs_range_based_rules<?php echo esc_attr( $key ) ; ?>">

							<option value="1" <?php selected( '1', $reward_type ) ; ?>>
								<?php esc_html_e( 'Fixed Points', 'rewardsystem' ) ; ?>
							</option>

							<option value="2" <?php selected( '2', $reward_type ) ; ?>>
								<?php esc_html_e( 'Percentage of Cart Total', 'rewardsystem' ) ; ?>
							</option>

						</select> 
					</td>

					<td>
						<input type="text" 
							   name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][reward_points]" 
							   value="<?php echo esc_attr( isset( $value[ 'reward_points' ] ) ? $value[ 'reward_points' ] : ''  ) ; ?>" />
					</td>

					<td>
						<label><b><?php esc_html_e( 'From', 'rewardsystem' ) ; ?></b></label>
						<input type="text" 
							   class ="rs_range_from_date" name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][from_date]" 
							   value="<?php echo esc_attr( isset( $value[ 'from_date' ] ) ? $value[ 'from_date' ] : ''  ) ; ?>" />  
						<br>
						<label><b><?php esc_html_e( 'To', 'rewardsystem' ) ; ?></b></label>                       
						<br>
						<input type="text" 
							   class = "rs_range_to_date" name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][to_date]" 
							   value="<?php echo esc_attr( isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : ''  ) ; ?>" />
					</td>

					<td>
						<span class="remove_rule button-secondary"><?php esc_html_e( 'Remove Rule', 'rewardsystem' ) ; ?></span>
					</td>
				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>
	<tfoot>
		<tr class="rs_range_based_rules_data">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><span class="add_rule button-primary"><?php esc_html_e( 'Add Rule', 'rewardsystem' ) ; ?></span></td>
		</tr>
		<tr class="rs_range_based_rules_data">
			<th><?php esc_html_e( 'Min Cart Total', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Max Cart Total', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Reward Type', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Reward Points Value/Percentage', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Validity', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ) ; ?></th>
		</tr>
	</tfoot>
</table>
