<?php
/**
 * Multiple Anniversary Points Table Content.
 * */
defined( 'ABSPATH' ) || exit;
?>

<div class="rs-multiple-anniversary-points-table-content-popup-wrapper">
	<div class="rs-multiple-anniversary-details">
		<div>
			<span><b><?php echo esc_html__( 'Username: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_login ); ?></span>
		</div>
		<br/>
		<div>
			<span><b><?php echo esc_html__( 'Email ID: ', 'rewardsystem' ); ?></b></span>
			<span><?php echo esc_html( $user->user_email ); ?></span>
		</div>
		<div>
			<h3><?php echo esc_html__( 'Anniversary Details', 'rewardsystem' ); ?></h3>
			<?php require SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-anniversary-multiple-dates-table-content.php'; ?>
		</div>
	</div>
	<table class="rs-multiple-anniversary-points-table-content-popup striped widefat">
		<h3><?php echo esc_html__( 'Anniversary Points', 'rewardsystem' ); ?></h3>
		<thead>
			<tr>
				<th><b><?php esc_html_e( 'Anniversary Name', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Earned Points', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Earned Date', 'rewardsystem' ); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $multiple_anniv_data as $multiple_anniv_value ) :
				?>
				<tr>
					<td>
						<?php
						$stored_field_names = get_user_meta( $user->ID, 'rs_stored_multiple_anniversary_field_names', true );
						if ( isset( $multiple_anniv_value['earneddate'] ) ) :
							$earned_date = $multiple_anniv_value['earneddate'];
							echo esc_html( isset( $stored_field_names[ $earned_date ] ) ? $stored_field_names[ $earned_date ] : '-' );
						endif;
						?>
					</td>
					<td><?php echo esc_attr( isset( $multiple_anniv_value['earnedpoints'] ) ? $multiple_anniv_value['earnedpoints'] : 0 ); ?></td>
					<td><?php echo esc_attr( ! empty( $multiple_anniv_value['earneddate'] ) ? SRP_Date_Time::get_wp_format_datetime_from_gmt( gmdate( 'Y-m-d H:i:s', $multiple_anniv_value['earneddate'] ) ) : '-' ); ?></td>
				</tr>
				<?php
			endforeach;
			?>
		</tbody>
	</table>
</div>
<?php
