<?php
/**
 * Redeeming percentage rule.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>

<table class="rs-redeeming-percentage-rule widefat fixed" cellspacing="0">
	<thead>
		<tr class="rsdynamicrulecreation_for_redeem">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Redeem Points Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>

	<tbody id="here_redeem">
		<?php
		$redeeming_percentage_rules = get_option( 'rewards_dynamic_rule_for_redeem' ) ;
		if ( srp_check_is_array( $redeeming_percentage_rules ) ) :
			foreach ( $redeeming_percentage_rules as $key => $value ) :
				$level_name    = isset( $value[ 'name' ] ) ? $value[ 'name' ] : '' ;
				$reward_points = isset( $value[ 'rewardpoints' ] ) ? $value[ 'rewardpoints' ] : '' ;
				$percentage    = isset( $value[ 'percentage' ] ) ? $value[ 'percentage' ] : '' ;
				?>
				<tr class="rsdynamicrulecreation_for_redeem">
					<td class="column-columnname">
						<p class="form-field">
							<input type="text" 
								   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $key ) ; ?>][name]" 
								   class="short" value="<?php echo wp_kses_post( $level_name ) ; ?>">
						</p>
					</td>
					<td class="column-columnname">
						<p class="form-field">
							<input type="number" 
								   step="any" min="0" 
								   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $key ) ; ?>][rewardpoints]" 
								   id="rewards_dynamic_rewardpoints<?php echo esc_attr( $key ) ; ?>"
								   class="short" value="<?php echo esc_html( $reward_points ) ; ?>">
						</p>
					</td>
					<td class="column-columnname">
						<p class="form-field">
							<input type ="number"
								   name="rewards_dynamic_rule_for_redeem[<?php echo esc_attr( $key ) ; ?>][percentage]" 
								   id="rewards_dynamic_rule_percentage<?php echo esc_attr($key) ; ?>"
								   class="short" value="<?php echo esc_html( $percentage ) ; ?>">
						</p>
					</td>

					<td class="column-columnname num">
						<span class="rs-remove-redeeming-percentage-rule button-secondary"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></span>
					</td>
				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>

	<tfoot>
		<tr class="rsdynamicrulecreation_for_redeem">
			<td></td>
			<td></td>
			<td></td>
			<td class="manage-column column-columnname num" scope="col"> 
				<span class="rs-add-redeeming-percentage-rule button-primary"><?php esc_html_e( 'Add New Level' , 'rewardsystem' ) ; ?></span>
			</td>
		</tr>
		<tr class="rsdynamicrulecreation_for_redeem">
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Level Name' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Reward Points' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Redeem Points Percentage' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Level' , 'rewardsystem' ) ; ?></th>
		</tr>
	</tfoot>
</table>
