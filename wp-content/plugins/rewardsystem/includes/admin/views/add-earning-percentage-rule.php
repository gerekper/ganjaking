<?php
/**
 * Add Earning percentage rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $woocommerce;
if ( $woocommerce->version >= (float) ( '3.0.0' ) ) :
	?>
	<tr>
		<td>
			<input type="text" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][name]"
				   class="short" value=""/>
		</td>
		<td>
			<input type="number" 
				   step="any" 
				   min="0" 
				   id="rewards_dynamic_ruleamount[<?php echo esc_attr( $random_value ); ?>]" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][rewardpoints]" 
				   class="short" value=""/></td>

		<td><input type ="number"
				   id="rewards_dynamic_rule_claimcount[<?php echo esc_attr( $random_value ); ?>]" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][percentage]" 
				   class="short"  value=""/>
		</td>
		<?php $earning_type = isset( $random_value['type'] ) ? $random_value['type'] : 1; ?>
		<td>
			<select name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][type]" class="rs-member-level-earning-type" id="rewards_dynamic_rule<?php echo esc_attr( $random_value ); ?>">

				<option value="1" <?php selected( '1', $earning_type ); ?>>
					<?php esc_html_e( 'Free Product(s)', 'rewardsystem' ); ?>
				</option>

				<option value="2" <?php selected( '2', $earning_type ); ?>>
					<?php esc_html_e( 'Bonus Points ', 'rewardsystem' ); ?>
				</option>

			</select> 
		</td>       
		<td>
			<div class="rs-free-product-data">
				<select
						name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][product_list][]" 
						class="wc-product-search rs-free-product" data-placeholder="<?php echo esc_html_e( 'Search for a product', 'rewardsystem' ); ?>" 
						data-action="woocommerce_json_search_products_and_variations" data-multiple="true"
						multiple = "multiple">
				</select>
			</div>

			<div class="rs-bonus-point-data">             
				<input type="number" 
					   step="any" 
					   min="1" 
					   id="rewards_dynamic_rule_bouns[<?php echo esc_attr( $random_value ); ?>]" 
					   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][bounspoints]" 
					   class="short rs-bonus-points" />
			</div>         
		</td>    

		<td class="num">
			<span class="rs-remove-earning-percentage-rule button-secondary"><?php echo esc_html_e( 'Remove Level', 'rewardsystem' ); ?></span>
		</td>
	</tr>
	<?php
else :
	?>
	<tr>
		<td>
			<input type="text" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][name]" 
				   class="short" value=""/>
		</td>

		<td>
			<input type="number" 
				   step="any"
				   min="0"
				   id="rewards_dynamic_ruleamount[<?php echo esc_attr( $random_value ); ?>]" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][rewardpoints]" 
				   class="short" value=""/>
		</td>

		<td>
			<input type ="number" 
				   id="rewards_dynamic_rule_claimcount[<?php echo esc_attr( $random_value ); ?>]" 
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][percentage]" 
				   class="short" value=""/>
		</td>

		<?php $earning_type = isset( $random_value['type'] ) ? $random_value['type'] : 1; ?>
		<td>
			<select name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][type]" class="rs-member-level-earning-type" id="rewards_dynamic_rule<?php echo esc_attr( $random_value ); ?>">
				<option value="1" <?php selected( '1', $earning_type ); ?>>
					<?php esc_html_e( 'Free Product(s)', 'rewardsystem' ); ?>
				</option>
				<option value="2" <?php selected( '2', $earning_type ); ?>>
					<?php esc_html_e( 'Bonus Points', 'rewardsystem' ); ?>
				</option>
			</select> 
		</td>

		<td>
			<div class="rs-free-product-data">
				<select
						name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][product_list][]" 
						class="wc-product-search rs-free-product" data-placeholder="<?php echo esc_html_e( 'Search for a product', 'rewardsystem' ); ?>" 
						data-action="woocommerce_json_search_products_and_variations" data-multiple="true"
						multiple = "multiple">
				</select>
			</div>

			<div class="rs-bonus-point-data">
				<input type="number" 
					   step="any" 
					   min="1" 
					   id="rewards_dynamic_rule_bouns[<?php echo esc_attr( $random_value ); ?>]" 
					   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][bounspoints]" 
					   class="short rs-bonus-points" />
			</div>           
		</td>

	  <td>

			<input type=hidden
				   name="rewards_dynamic_rule[<?php echo esc_attr( $random_value ); ?>][product_list][]" 
				   class="wc-product-search" data-placeholder="<?php esc_html_e( 'Search for a product', 'rewardsystem' ); ?>" 
				   data-action="woocommerce_json_search_products_and_variations"
				   data-multiple="true"/>
		  </td>        

		<td class="num">
			<span class="rs-remove-earning-percentage-rule button-secondary"><?php esc_html_e( 'Remove Level', 'rewardsystem' ); ?></span>
		</td>
	</tr>
				 <?php
endif;
