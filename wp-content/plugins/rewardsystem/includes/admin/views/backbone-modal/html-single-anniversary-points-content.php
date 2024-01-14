<?php
/**
 * Single Anniversary Points Table Content.
 * */
defined( 'ABSPATH' ) || exit;
?>

<div class="rs-single-anniversary-points-table-content-popup-wrapper">
	<div class="rs-single-anniversary-details">
		<div>
			<span><b><?php echo esc_html__( 'Username: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_login ); ?></span>
		</div>
		<br/>
		<div>
			<span><b><?php echo esc_html__( 'Email ID: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_email ); ?></span>
		</div>
		<br>
		<div>
			<span><b><?php echo esc_html__( 'Anniversary Date: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( ! empty( $single_anniv_date ) ? $single_anniv_date : '-' ); ?></span>
		</div>
		<br>
	</div>
	<table class="rs-single-anniversary-points-table-content-popup striped widefat">
		<thead>
			<tr>
				<th><b><?php esc_html_e( 'Anniversary Name', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Earned Points', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Earned Date', 'rewardsystem' ); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $single_anniv_data as $single_anniv_value ) :
				?>
				<tr>
					<td>
						<?php
						$stored_field_names = get_user_meta( $user->ID, 'rs_stored_single_anniversary_field_names', true );
						if ( isset( $single_anniv_value['earneddate'] ) ) :
							$earned_date = $single_anniv_value['earneddate'];
							echo esc_html( isset( $stored_field_names[ $earned_date ] ) ? $stored_field_names[ $earned_date ] : '-' );
						endif;
						?>
					</td>
					<td><?php echo esc_attr( isset( $single_anniv_value['earnedpoints'] ) ? $single_anniv_value['earnedpoints'] : 0 ); ?></td>
					<td><?php echo esc_attr( ! empty( $single_anniv_value['earneddate'] ) ? SRP_Date_Time::get_wp_format_datetime_from_gmt( gmdate( 'Y-m-d H:i:s', $single_anniv_value['earneddate'] ) ) : '-' ); ?></td>
				</tr>
				<?php
			endforeach;
			?>
		</tbody>
	</table>
</div>
<?php
