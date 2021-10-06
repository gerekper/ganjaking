<?php
/**
 * Add Range Based Rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<tr>
	<td>
		<input type = "text"
			   name = "rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][min_value]"/>
	</td>
	<td>
		<input type = "text"
			   name = "rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][max_value]"/>
	</td>
	<?php
	$reward_type = isset( $key[ 'type' ] ) ? $key[ 'type' ] : 1 ;
	?>
	<td class="column-columnname">
		<p class="form-field">
			<select name="rs_range_based_rules[<?php echo esc_attr($key) ; ?>][type]" id="rs_range_based_rules<?php echo esc_attr($key) ; ?>">
				<option value="1" <?php selected( '1', $reward_type ) ; ?>><?php esc_html_e( 'Fixed', 'rewardsystem' ) ; ?></option>
				<option value="2" <?php selected( '2', $reward_type ) ; ?>><?php esc_html_e( 'Percentage', 'rewardsystem' ) ; ?></option>
			</select> 
		</p>
	</td>
	<td>
		<input type = "text"
			   name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][reward_points]"/>
	</td>

	<td>
		<label><b><?php esc_html_e( 'From Date', 'rewardsystem' ) ; ?></b></label>
		<input type="text" 
			   class ="rs_range_from_date" name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][from_date]" 
			   value="<?php echo esc_attr( isset( $key[ 'from_date' ] ) ? $key[ 'from_date' ] : ''  ) ; ?>" />  
		<br>
		<label><b><?php esc_html_e( 'To Date', 'rewardsystem' ) ; ?></b></label>  
		<br>
		<input type="text" 
			   class = "rs_range_to_date" name="rs_range_based_rules[<?php echo esc_attr( $key ) ; ?>][to_date]" 
			   value="<?php echo esc_attr( isset( $key[ 'to_date' ] ) ? $key[ 'to_date' ] : ''  ) ; ?>" />
	</td>

	<td class="num">
		<span class="remove_rule rs-remove-range-based-usage-rule button-secondary"><?php esc_html_e( 'Remove Rule', 'rewardsystem' ) ; ?></span>
		<input type ="hidden" id="rs_range_based_rule_id" value="<?php echo esc_html( $key ) ; ?>">
	</td>
</tr>
<?php
