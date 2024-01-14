<?php
/**
 * Account Anniversary Points Table Content.
 * */
defined( 'ABSPATH' ) || exit;
?>

<div class="rs-account-anniversary-points-table-content-popup-wrapper">
	<div class="rs-account-anniversary-details">
		<div>
			<span><b><?php echo esc_html__( 'Username: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_login ); ?></span>
		</div>
		<br>
				<div>
			<span><b><?php echo esc_html__( 'Email ID: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_email ); ?></span>
		</div>
		<br>
		<div>
			<span><b><?php echo esc_html__( 'User Registration Date: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( SRP_Date_Time::get_wp_format_datetime_from_gmt($user->user_registered) ); ?></span>
		</div>
		<br>
	</div>
	<table class="rs-account-anniversary-points-table-content-popup striped widefat">
		<thead>
			<tr>
				<th><b><?php esc_html_e( 'Earned Points', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Earned Date', 'rewardsystem' ); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $account_anniv_data as $account_anniv_value ) :
				?>
				<tr>
					<td><?php echo esc_attr( isset( $account_anniv_value[ 'earnedpoints' ] ) ? $account_anniv_value[ 'earnedpoints' ] : 0  ); ?></td>
					<td><?php echo esc_attr(  ! empty( $account_anniv_value[ 'earneddate' ] ) ? SRP_Date_Time::get_wp_format_datetime_from_gmt( gmdate( 'Y-m-d H:i:s', $account_anniv_value[ 'earneddate' ] ) ) : '-'  ); ?></td>
				</tr>
				<?php
			endforeach;
			?>
		</tbody>
	</table>
</div>
<?php
