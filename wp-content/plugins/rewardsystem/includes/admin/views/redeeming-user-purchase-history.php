<?php
/**
 * Redeeming User purchase history.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="rs-redeeming-user-purchase-history widefat fixed" cellspacing="0">
	<thead>
		<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Value' , 'rewardsystem' ) ; ?></th>      
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Percentage' , 'rewardsystem' ) ; ?></th>   
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>

	<tbody id="here_product">
		<?php
		$rewards_dynamic_rules = get_option( 'rewards_dynamic_rule_purchase_history_redeem' ) ;
		if ( srp_check_is_array( $rewards_dynamic_rules ) ) :
			foreach ( $rewards_dynamic_rules as $i => $rewards_dynamic_rule ) :

				$reward_type  = isset( $rewards_dynamic_rule[ 'type' ] ) ? $rewards_dynamic_rule[ 'type' ] : '' ;
				$level_name   = isset( $rewards_dynamic_rule[ 'name' ] ) ? $rewards_dynamic_rule[ 'name' ] : '' ;
				$percentage   = isset( $rewards_dynamic_rule[ 'percentage' ] ) ? $rewards_dynamic_rule[ 'percentage' ] : '' ;
				$reward_value = isset( $rewards_dynamic_rule[ 'value' ] ) ? $rewards_dynamic_rule[ 'value' ] : '' ;
				?>

				<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">

					<td class="column-columnname">
						<p class="form-field">
							<input type="text" 
								   name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $i ) ; ?>][name]"
								   class="short" value="<?php echo wp_kses_post( $level_name ) ; ?>">
						</p>
					</td>

					<td class="column-columnname">
						<p class="form-field">
							<select
									name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $i ) ; ?>][type]"
									id="rewards_dynamic_rule_purchase_history_redeem<?php echo esc_attr( $i ) ; ?>"
									class="short"  >

								<option value="1" <?php selected( '1' , $reward_type ) ; ?>>
									<?php esc_html_e( 'Number of Successful Order(s)' , 'rewardsystem' ) ; ?>
								</option>

								<option value="2" <?php selected( '2' , $reward_type ) ; ?>>
									<?php esc_html_e( 'Total Amount Spent in Site' , 'rewardsystem' ) ; ?>
								</option>

							</select> 
						</p>
					</td>

					<td class="column-columnname">
						<p class="form-field">
							<input type ="number" 
								   name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr($i) ; ?>][value]" 
								   id="rewards_dynamic_rule_purchase_history_redeemvalue<?php echo esc_attr( $i ) ; ?>" 
								   class="short" 
								   value="<?php echo esc_html( $reward_value ) ; ?>">
						</p>
					</td>

					<td class="column-columnname">
						<p class="form-field">
							<input type ="number" name="rewards_dynamic_rule_purchase_history_redeem[<?php echo esc_attr( $i ) ; ?>][percentage]" 
								   id="rewards_dynamic_rule_purchase_history_redeempercentage<?php echo esc_attr( $i ) ; ?>" 
								   class="short" 
								   value="<?php echo esc_html( $percentage ) ; ?>">
						</p>
					</td>

					<td class="column-columnname num">
						<span class="rs-remove-redeeming-user-purchase-history-rule button-secondary"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></span>
					</td>

				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>

	<tfoot>
		<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class="manage-column column-columnname num" scope="col"> 
				<span class="rs-add-redeeming-user-purchase-history-rule button-primary"><?php esc_html_e( 'Add New Level' , 'rewardsystem' ) ; ?></span>
			</td>
		</tr>
		<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Value' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</tfoot>
</table>
<?php
