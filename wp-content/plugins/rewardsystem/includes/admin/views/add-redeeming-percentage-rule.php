<?php
/**
 * Add Redeeming percentage rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
global $woocommerce ;
if ( $woocommerce->version >= ( float ) ( '3.0.0' ) ) :
	?>
	<tr class="rsdynamicrulecreation_for_redeem">
		<td>
			<p class="form-field">
				<input type="text" name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][name]" 
					   class="short" value="">
			</p>
		</td>
		<td>
			<p class="form-field">
				<input type="number" 
					   step="any" min="0" 
					   id="rewards_dynamic_ruleamount<?php echo esc_attr( $random_value ) ; ?>" 
					   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][rewardpoints]"
					   class="short" value="">
			</p>
		</td>
		<td>
			<p class="form-field">
				<input type ="number" id="rewards_dynamic_rule_claimcount<?php echo esc_attr( $random_value ) ; ?>" 
					   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][percentage]" 
					   class="short" value="">
			</p>
		</td>
		<td class="num">
			<span class="rs-remove-redeeming-percentage-rule button-secondary"><?php esc_html_e( 'Remove Rule' , 'rewardsystem' ) ; ?></span>
		</td>
	</tr>

	<?php
else :
	?>
	<tr>
		<td>
			<p class="form-field">
				<input type="text" 
					   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][name]" 
					   class="short" value="">
			</p>
		</td>

		<td>
			<p class="form-field">
				<input type="number" 
					   step="any" min="0" 
					   id="rewards_dynamic_ruleamount<?php echo esc_attr( $random_value ) ; ?>" 
					   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][rewardpoints]" 
					   class="short" value="">
			</p>
		</td>

		<td>
			<p class="form-field">
				<input type ="number"
					   id="rewards_dynamic_rule_claimcount<?php echo esc_attr( $random_value ) ; ?>" 
					   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $random_value ) ; ?>][percentage]" 
					   class="short" value="">
			</p>
		</td>
		<td class="num">
			<span class="rs-remove-redeeming-percentage-rule button-secondary"><?php esc_html_e( 'Remove Rule' , 'rewardsystem' ) ; ?></span>
		</td>
	</tr>

	<?php
endif;
?>

