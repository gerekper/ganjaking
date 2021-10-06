<?php
/**
 * Add manual referral link rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
global $woocommerce ;
if ( ( float ) $woocommerce->version >= ( float ) ( '3.0.0' ) ) :
	?>
	<tr>
		<td>
			<select class="wc-customer-search" 
					name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][referer]" 
					data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
					data-allow_clear="true">
				<option value=""></option>
			</select>
		</td>

		<td>
			<select class="wc-customer-search"
					name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][refferal]" 
					data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
					data-allow_clear="true">
				<option value=""></option>
			</select>
		</td>
		<td class="column-columnname-link" >
			<span><input type="hidden" 
						 name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][type]" 
						 value="" class="short "/>
				<b><?php esc_html_e( 'Manual' , 'rewardsystem' ) ; ?></b>
			</span>
		</td>

		<td class="num">
			<span class="remove button-secondary"><?php esc_html_e( 'Remove linking' , 'rewardsystem' ) ; ?></span>
		</td>
	</tr>
	<?php
else :
	?>
	  
	<tr>
		<td>
			<input type="hidden" 
				   class="wc-customer-search"
				   name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][referer]" 
				   data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
				   data-selected="" value="" data-allow_clear="true">
		</td>

		<td>
			<input type="hidden" 
				   class="wc-customer-search" 
				   name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][refferal]" 
				   data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
				   data-selected="" value="" data-allow_clear="true">
		</td>

		<td class="column-columnname-link">
			<span>
				<input type="hidden" name="rewards_dynamic_rule_manual[<?php echo esc_attr( $rule_count ) ; ?>][type]" 
					   value="" class="short"/>
				<b><?php esc_html_e( 'Manual' , 'rewardsystem' ) ; ?></b>
			</span>
		</td>

		<td class="num">
			<span class="remove button-secondary"><?php esc_html_e( 'Remove linking' , 'rewardsystem' ) ; ?></span>
		</td>
	</tr>

	<?php

endif;
