<?php
/**
 * Anniversary Multiple Dates Table Content.
 * */
defined( 'ABSPATH' ) || exit;
?>

<div class="rs-anniversary-multiple-dates-table-popup-wrapper">
	<table class="rs-anniversary-multiple-dates-table-popup striped widefat">
		<thead>
			<tr>
				<th><b><?php esc_html_e( 'Anniversary Name', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Date', 'rewardsystem' ); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$rules                = get_option( 'rs_custom_anniversary_rules' );
			$multiple_anniv_dates = get_user_meta( $user_id, 'rs_multiple_anniversary_dates', true );
			if ( srp_check_is_array( $multiple_anniv_dates ) && srp_check_is_array( $rules ) ) :
				foreach ( $multiple_anniv_dates as $rule_key => $anniv_date ) :
					$rule_data = isset( $rules[ $rule_key ] ) ? $rules[ $rule_key ] : array();
					if ( ! srp_check_is_array( $rule_data ) || ! $anniv_date ) :
						continue;
					endif;

					$field_name = isset( $rule_data['field_name'] ) ? $rule_data['field_name'] : '-';
					?>
					<tr>
						<td><?php echo esc_html( $field_name ); ?></td>
						<td>
						<?php
						echo ! empty( $anniv_date ) ? esc_html( $anniv_date ) : '-';
						?>
						</td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
		</tbody>
	</table>
</div>
<?php
