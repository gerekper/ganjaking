<?php
/**
 * Add Rule for Bonus Point Without Repeat.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<tr>
	<td>
		<input type = "text"
			   name = "rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][level_name]"/>
	</td>
	<td>
		<input type = "number"
			   name = "rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][number_of_orders]"/>
	</td>
	<td>
		<input type = "number"
			   name = "rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][bonus_points]"/>
	</td>

	<td>
		<label><b><?php esc_html_e( 'From Date', 'rewardsystem' ) ; ?></b></label>
		<input type="text" 
			   class ="rs-bonus-points-number-of-orders-from-date-without-repeat" name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][from_date]" 
			   value="<?php echo esc_attr( isset( $key[ 'from_date' ] ) ? $key[ 'from_date' ] : ''  ) ; ?>" />  
		<br>
		<label><b><?php esc_html_e( 'To Date', 'rewardsystem' ) ; ?></b></label>  
		<br>
		<input type="text" 
			   class = "rs-bonus-points-number-of-orders-to-date-without-repeat" name="rs_bonus_points_number_of_orders_without_repeat_rules[<?php echo esc_attr( $key ) ; ?>][to_date]" 
			   value="<?php echo esc_attr( isset( $key[ 'to_date' ] ) ? $key[ 'to_date' ] : ''  ) ; ?>" />
	</td>

	<td class="num">
		<span class="rs-remove-bonus-points-rule-for-orders button-secondary"><?php esc_html_e( 'Remove Rule', 'rewardsystem' ) ; ?></span>
		<input type ="hidden" id="rs_bonus_points_rule_id_for_orders_without_repeat" value="<?php echo esc_html( $key ) ; ?>">
	</td>
</tr>
<?php
