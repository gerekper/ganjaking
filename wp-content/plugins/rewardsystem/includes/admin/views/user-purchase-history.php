<?php
/**
 * User purchase history.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="rs-user-purchase-history-rules widefat striped" cellspacing="0">
	<thead>
		<tr class="rsdynamicrulecreationsforuserpurchasehistory">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Value' , 'rewardsystem' ) ; ?></th>      
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Percentage' , 'rewardsystem' ) ; ?></th>   
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>

	<tbody id="rs_table_data_for_user_purchase_history">
		<?php
		$user_purchase_history_rules = get_option( 'rewards_dynamic_rule_purchase_history' ) ;
		if ( srp_check_is_array( $user_purchase_history_rules ) ) :
			foreach ( $user_purchase_history_rules as $key => $value ) :

				$reward_type  = isset( $value[ 'type' ] ) ? $value[ 'type' ] : '' ;
				$level_name   = isset( $value[ 'name' ] ) ? $value[ 'name' ] : '' ;
				$reward_value = isset( $value[ 'value' ] ) ? $value[ 'value' ] : '' ;
				$percentage   = isset( $value[ 'percentage' ] ) ? $value[ 'percentage' ] : '' ;
				?>
				<tr class="rsdynamicrulecreationsforuserpurchasehistory">

					<td class="column-columnname">
						<p class="form-field">
							<input type="text" 
								   name="rewards_dynamic_rule_purchase_history[<?php echo esc_attr( $key ) ; ?>][name]" 
								   value="<?php echo esc_html( $level_name ) ; ?>">
						</p>
					</td>

					<td class="column-columnname">
						<p class="form-field">

							<select
									name="rewards_dynamic_rule_purchase_history[<?php echo esc_attr( $key ) ; ?>][type]" 
									id="rewards_dynamic_rule_purchase_history<?php echo esc_attr( $key ) ; ?>" >

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
								   name="rewards_dynamic_rule_purchase_history[<?php echo esc_attr( $key ) ; ?>][value]" 
								   id="rewards_dynamic_rule_purchase_historyvalue<?php echo esc_attr( $key ) ; ?>" 
								   value="<?php echo esc_html( $reward_value ) ; ?>">
						</p>
					</td>

					<td class="column-columnname">
						<p class="form-field">
							<input type ="number" 
								   name="rewards_dynamic_rule_purchase_history[<?php echo esc_attr( $key ) ; ?>][percentage]" 
								   id="rewards_dynamic_rule_purchase_historypercentage<?php echo esc_attr( $key ) ; ?>"
								   value="<?php echo esc_html( $percentage ) ; ?>">
						</p>
					</td>

					<td class="column-columnname num">
						<span class="rs-remove-purchase-history-rule button-secondary"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></span>
					</td>
				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>

	<tfoot>

		<tr class="rsdynamicrulecreationsforuserpurchasehistory">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class="manage-column column-columnname num" scope="col"> 
				<span class="rs-add-new-purchase-history-rule button-primary">
					<?php esc_html_e( 'Add New Level' , 'rewardsystem' ) ; ?>
				</span>
			</td>
		</tr>

		<tr class="rsdynamicrulecreationsforuserpurchasehistory">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Value' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>

	</tfoot>
	
</table>
<?php
