<?php
/**
 * Bonus Points Without Repeat Rule for orders.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="rs-bonus-points-without-repeat-rules-for-orders widefat striped" >
	<thead>
		<tr class="rs-bonus-points-rules-data-for-orders">
			<th><?php esc_html_e( 'Level Name', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Number of Orders', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Points', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Date Range', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$bonus_points_rule = get_option( 'rs_bonus_points_number_of_orders_without_repeat_rules', array() ) ;
		if ( srp_check_is_array( $bonus_points_rule ) ) :
			foreach ( $bonus_points_rule as $key => $value ) :
				?>
				<tr class="rs-bonus-points-rules-data-for-orders">
					<td>
						<input type="text" 
							   name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][level_name]" 
							   value="<?php echo esc_attr( isset( $value[ 'level_name' ] ) ? $value[ 'level_name' ] : ''  ) ; ?>" />
					</td>

					<td>
						<input type="number" 
							   name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][number_of_orders]" 
							   value="<?php echo esc_attr( isset( $value[ 'number_of_orders' ] ) ? $value[ 'number_of_orders' ] : ''  ) ; ?>" />
					</td>
					
					<td>
						<input type="number" 
							   name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][bonus_points]" 
							   value="<?php echo esc_attr( isset( $value[ 'bonus_points' ] ) ? $value[ 'bonus_points' ] : ''  ) ; ?>" />
					</td>
					
					<td>
						<label><b><?php esc_html_e( 'From Date', 'rewardsystem' ) ; ?></b></label>
						<input type="text" 
							   class ="rs-bonus-points-number-of-orders-from-date-without-repeat" name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][from_date]" 
							   value="<?php echo esc_attr( isset( $value[ 'from_date' ] ) ? $value[ 'from_date' ] : ''  ) ; ?>" />  
						<br>
						<label><b><?php esc_html_e( 'To Date', 'rewardsystem' ) ; ?></b></label>                       
						<br>
						<input type="text" 
							   class = "rs-bonus-points-number-of-orders-to-date-without-repeat" name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][to_date]" 
							   value="<?php echo esc_attr( isset( $value[ 'to_date' ] ) ? $value[ 'to_date' ] : ''  ) ; ?>" />
					</td>

					<td>
						<span class="rs-remove-bonus-points-rule-for-orders button-secondary"><?php esc_html_e( 'Remove Rule', 'rewardsystem' ) ; ?></span>
					</td>
				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>
	<tfoot>
		<tr class="rs-bonus-points-rules-data-for-orders">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><span class="rs-add-bonus-points-rule-for-orders button-primary"><?php esc_html_e( 'Add Rule', 'rewardsystem' ) ; ?></span></td>
		</tr>
		<tr class="rs-bonus-points-rules-data-for-orders">
			<th><?php esc_html_e( 'Level Name', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Number of Orders', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Points', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Date Range', 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Remove Level', 'rewardsystem' ) ; ?></th>
		</tr>
	</tfoot>
</table>
