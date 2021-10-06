<?php
/**
 * Redeeming add user purchase history rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">

	<td>
		<p class="form-field">
			<input type="text" 
				   name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $random_value ) ; ?>][name]" 
				   class="short" value=""/>
		</p>
	</td>

	<td>
		<p class="form-field">
			<select
					id="rewards_dynamic_rule_purchase_history_redeem<?php echo esc_attr( $random_value ) ; ?>"
					name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $random_value ) ; ?>][type]" 
					class="short">
				<option value="1"><?php esc_html_e( 'Number of Successful Order(s)' , 'rewardsystem' ) ; ?></option>
				<option value="2"><?php esc_html_e( 'Total Amount Spent in Site' , 'rewardsystem' ) ; ?></select>
		</p>
	</td>

	<td>
		<p class="form-field">
			<input type ="number"
				   id="rewards_dynamic_rule_purchase_history_redeem<?php echo esc_attr( $random_value ) ; ?>"
				   name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $random_value ) ; ?>][value]" 
				   class="short" value=""/>
		</p>
	</td>

	<td>
		<p class="form-field">
			<input type ="number" 
				   id="rewards_dynamic_rule_purchase_history_redeem<?php echo esc_attr( $random_value ) ; ?>" 
				   name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $random_value ) ; ?>][percentage]"
				   class="short"  value=""/>
		</p>
	</td>

	<td class="num">
		<span class="rs-remove-redeeming-user-purchase-history-rule button-secondary">
			<?php esc_html_e( 'Remove Rule' , 'rewardsystem' ) ; ?>
		</span>
	</td>
</tr>
<?php
