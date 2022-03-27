<?php
/**
 * My Reward Menu Sorting.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="form-table rs_myrewards_menu_sorting">
		<thead>
			<tr class="rs-my-reward-menu-sorting-content">
				<th style="width:auto;font-size:18px;">
					<b><?php esc_html_e( 'My Reward Points Menu Sorting' , 'rewardsystem' ) ; ?></b>
				</th>
				<td></td>
			</tr>
		</thead>
	<tbody class="sortable_menu">
		<?php
		$defaultcolumn = array(
			'rs_myrewards_table'        => esc_html__( 'My Rewards Table' , 'rewardsystem' ) ,
			'rs_nominee_field'          => esc_html__( 'Nominee Field' , 'rewardsystem' ) ,
			'rs_gift_voucher_field'     => esc_html__( 'Gift Voucher Field' , 'rewardsystem' ) ,
			'rs_referral_table'         => esc_html__( 'Referral Table' , 'rewardsystem' ) ,
			'rs_generate_referral_link' => esc_html__( 'Generate Referral Link' , 'rewardsystem' ) ,
			'rs_refer_a_friend_form'    => esc_html__( 'Refer a Friend Form' , 'rewardsystem' ) ,
			'rs_my_cashback_form'       => esc_html__( 'Cashback Form' , 'rewardsystem' ) ,
			'rs_my_cashback_table'      => esc_html__( 'My Cashback Table' , 'rewardsystem' ) ,
			'rs_email_subscribe_link'   => esc_html__( 'Email - Subscribe Link' , 'rewardsystem' ) ,
				) ;

		$sortedcolumn = srp_check_is_array( get_option( 'rs_sorted_menu_settings_list' ) ) ? get_option( 'rs_sorted_menu_settings_list' ) : $defaultcolumn ;
		if ( ! isset( $sortedcolumn[ 'rs_my_cashback_form' ] ) ) {
			$sortedcolumn = array_slice( $sortedcolumn , 0 , 4 , true ) +
					array( 'rs_my_cashback_form' => esc_html__( 'Cashback Form' , 'rewardsystem' ) ) +
					array_slice( $sortedcolumn , 3 , count( $sortedcolumn ) - 4 , true ) ;
		}
		
		foreach ( $sortedcolumn as $column_key => $column_value ) {
			?>
			<tr class="rs-my-reward-menu-sorting-content">
				<th class="myrewards_sortable_menu_header"><?php echo esc_attr( $defaultcolumn[ $column_key ] ) ; ?></th>                 
				<td class="myrewards_sortable_menu_data">
					<input type="hidden" name="rs_sorted_reward_menu_list[<?php echo esc_attr( $column_key ) ; ?>]" value="<?php echo esc_attr( $column_value ); ?>">
					<img src="<?php echo esc_url( SRP_PLUGIN_DIR_URL ) ; ?>/assets/images/drag-icon.png"/>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?php
