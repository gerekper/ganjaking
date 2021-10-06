<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$intervals = array();

$intervals['months'] = array(
	'1'  => __( 'January', 'woocommerce-bookings' ),
	'2'  => __( 'February', 'woocommerce-bookings' ),
	'3'  => __( 'March', 'woocommerce-bookings' ),
	'4'  => __( 'April', 'woocommerce-bookings' ),
	'5'  => __( 'May', 'woocommerce-bookings' ),
	'6'  => __( 'June', 'woocommerce-bookings' ),
	'7'  => __( 'July', 'woocommerce-bookings' ),
	'8'  => __( 'August', 'woocommerce-bookings' ),
	'9'  => __( 'September', 'woocommerce-bookings' ),
	'10' => __( 'October', 'woocommerce-bookings' ),
	'11' => __( 'November', 'woocommerce-bookings' ),
	'12' => __( 'December', 'woocommerce-bookings' ),
);

$intervals['days'] = array(
	'1' => __( 'Monday', 'woocommerce-bookings' ),
	'2' => __( 'Tuesday', 'woocommerce-bookings' ),
	'3' => __( 'Wednesday', 'woocommerce-bookings' ),
	'4' => __( 'Thursday', 'woocommerce-bookings' ),
	'5' => __( 'Friday', 'woocommerce-bookings' ),
	'6' => __( 'Saturday', 'woocommerce-bookings' ),
	'7' => __( 'Sunday', 'woocommerce-bookings' ),
);

for ( $i = 1; $i <= 52; $i ++ ) {
	/* translators: 1: week number */
	$intervals['weeks'][ $i ] = sprintf( __( 'Week %s', 'woocommerce-bookings' ), $i );
}

if ( ! isset( $pricing['type'] ) ) {
	$pricing['type'] = 'custom';
}
if ( ! isset( $pricing['modifier'] ) ) {
	$pricing['modifier'] = '';
}
if ( ! isset( $pricing['base_modifier'] ) ) {
	$pricing['base_modifier'] = '';
}
if ( ! isset( $pricing['base_cost'] ) ) {
	$pricing['base_cost'] = '';
}

// In the loop of saved items an index is supplied, but we need one for the
// add new cost range button so we can replace it when adding and index on the front end.
$index = isset( $index ) ? $index : 'bookings_cost_js_index_replace';
?>
<tr>
	<td class="sort">&nbsp;</td>
	<td>
		<div class="select wc_booking_pricing_type">
			<select name="wc_booking_pricing_type[<?php echo esc_attr( $index ); ?>]">
				<option value="custom" <?php selected( $pricing['type'], 'custom' ); ?>><?php esc_html_e( 'Date range', 'woocommerce-bookings' ); ?></option>
				<option value="months" <?php selected( $pricing['type'], 'months' ); ?>><?php esc_html_e( 'Range of months', 'woocommerce-bookings' ); ?></option>
				<option value="weeks" <?php selected( $pricing['type'], 'weeks' ); ?>><?php esc_html_e( 'Range of weeks', 'woocommerce-bookings' ); ?></option>
				<option value="days" <?php selected( $pricing['type'], 'days' ); ?>><?php esc_html_e( 'Range of days', 'woocommerce-bookings' ); ?></option>
				<option value="time" <?php selected( $pricing['type'], 'time' ); ?>><?php esc_html_e( 'Time Range', 'woocommerce-bookings' ); ?></option>
				<option value="persons" <?php selected( $pricing['type'], 'persons' ); ?>><?php esc_html_e( 'Person count', 'woocommerce-bookings' ); ?></option>
				<option value="blocks" <?php selected( $pricing['type'], 'blocks' ); ?>><?php esc_html_e( 'Block count', 'woocommerce-bookings' ); ?></option>
				<optgroup label="<?php esc_html_e( 'Time Ranges', 'woocommerce-bookings' ); ?>">
					<option value="time" <?php selected( $pricing['type'], 'time' ); ?>><?php esc_html_e( 'Time Range (all week)', 'woocommerce-bookings' ); ?></option>
					<option value="time:range" <?php selected( $pricing['type'], 'time:range' ); ?>><?php esc_html_e( 'Date Range with time', 'woocommerce-bookings' ); ?></option>
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="time:<?php echo esc_attr( $key ); ?>" <?php selected( $pricing['type'], 'time:' . $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</div>
	</td>
	<td style="border-right:0;">
	<div class="bookings-datetime-select-from">
		<div class="select from_day_of_week">
			<select name="wc_booking_pricing_from_day_of_week[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_month">
			<select name="wc_booking_pricing_from_month[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_week">
			<select name="wc_booking_pricing_from_week[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="from_date">
			<?php
			$from_date = '';
			if ( 'custom' === $pricing['type'] && ! empty( $pricing['from'] ) ) {
				$from_date = $pricing['from'];
			} elseif ( 'time:range' === $pricing['type'] && ! empty( $pricing['from_date'] ) ) {
				$from_date = $pricing['from_date'];
			}
			?>
			<input type="text" class="date-picker" name="wc_booking_pricing_from_date[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $from_date ); ?>" />
		</div>

		<div class="from_time">
			<input type="time" class="time-picker" name="wc_booking_pricing_from_time[<?php echo esc_attr( $index ); ?>]" value="<?php
			if ( strrpos( $pricing['type'], 'time' ) === 0 && ! empty( $pricing['from'] ) ) {
				echo esc_attr( $pricing['from'] );
			}
			?>" placeholder="HH:MM" />
		</div>

		<div class="from">
			<input type="number" step="1" name="wc_booking_pricing_from[<?php echo esc_attr( $index ); ?>]" value="<?php
			if ( ! empty( $pricing['from'] ) && is_numeric( $pricing['from'] ) ) {
				echo esc_attr( $pricing['from'] );
			}
			?>" />
		</div>
	</div>
	</td>
	<td style="border-right:0;" width="25px;" class="bookings-to-label-row">
		<p><?php esc_html_e( 'to', 'woocommerce-bookings' ); ?></p>
		<p class="bookings-datetimerange-second-label"><?php esc_html_e( 'to', 'woocommerce-bookings' ); ?></p>
	</td>
	<td>
	<div class="bookings-datetime-select-to">
		<div class="select to_day_of_week">
			<select name="wc_booking_pricing_to_day_of_week[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_month">
			<select name="wc_booking_pricing_to_month[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_week">
			<select name="wc_booking_pricing_to_week[<?php echo esc_attr( $index ); ?>]">
				<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="to_date">
			<?php
			$to_date = '';
			if ( 'custom' === $pricing['type'] && ! empty( $pricing['to'] ) ) {
				$to_date = $pricing['to'];
			} elseif ( 'time:range' === $pricing['type'] && ! empty( $pricing['to_date'] ) ) {
				$to_date = $pricing['to_date'];
			}
			?>
			<input type="text" class="date-picker" name="wc_booking_pricing_to_date[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $to_date ); ?>" />
		</div>

		<div class="to_time">
			<input type="time" class="time-picker" name="wc_booking_pricing_to_time[<?php echo esc_attr( $index ); ?>]" value="<?php
			if ( strrpos( $pricing['type'], 'time' ) === 0 && ! empty( $pricing['to'] ) ) {
				echo esc_attr( $pricing['to'] );
			}
			?>" placeholder="HH:MM" />
		</div>

		<div class="to">
			<input type="number" step="1" name="wc_booking_pricing_to[<?php echo esc_attr( $index ); ?>]" value="<?php
			if ( ! empty( $pricing['to'] ) && is_numeric( $pricing['to'] ) ) {
				echo esc_attr( $pricing['to'] );
			}
			?>" />
		</div>
	</div>
	</td>
	<td>
		<div class="select">
			<select name="wc_booking_pricing_base_cost_modifier[<?php echo esc_attr( $index ); ?>]">
				<option <?php selected( $pricing['base_modifier'], '' ); ?> value="">+</option>
				<option <?php selected( $pricing['base_modifier'], 'minus' ); ?> value="minus">-</option>
				<option <?php selected( $pricing['base_modifier'], 'times' ); ?> value="times">&times;</option>
				<option <?php selected( $pricing['base_modifier'], 'divide' ); ?> value="divide">&divide;</option>
				<option <?php selected( $pricing['base_modifier'], 'equals' ); ?> value="equals">=</option>
			</select>
		</div>
		<input type="number" step="0.01" name="wc_booking_pricing_base_cost[<?php echo esc_attr( $index ); ?>]" value="<?php
		if ( ! empty( $pricing['base_cost'] ) ) {
			echo esc_attr( $pricing['base_cost'] );
		}
		?>" placeholder="0" />
	<?php do_action( 'woocommerce_bookings_after_booking_pricing_base_cost', $pricing, $post->ID ); ?>
	</td>
	<td>
		<div class="select">
			<select name="wc_booking_pricing_cost_modifier[<?php echo esc_attr( $index ); ?>]">
				<option <?php selected( $pricing['modifier'], '' ); ?> value="">+</option>
				<option <?php selected( $pricing['modifier'], 'minus' ); ?> value="minus">-</option>
				<option <?php selected( $pricing['modifier'], 'times' ); ?> value="times">&times;</option>
				<option <?php selected( $pricing['modifier'], 'divide' ); ?> value="divide">&divide;</option>
				<option <?php selected( $pricing['modifier'], 'equals' ); ?> value="equals">=</option>
			</select>
		</div>
		<input type="number" step="0.01" name="wc_booking_pricing_cost[<?php echo esc_attr( $index ); ?>]" value="<?php
		if ( ! empty( $pricing['cost'] ) ) {
			echo esc_attr( $pricing['cost'] );
		}
		?>" placeholder="0" />
	<?php do_action( 'woocommerce_bookings_after_booking_pricing_cost', $pricing, $post->ID ); ?>
	</td>
	<td class="remove">&nbsp;</td>
</tr>
