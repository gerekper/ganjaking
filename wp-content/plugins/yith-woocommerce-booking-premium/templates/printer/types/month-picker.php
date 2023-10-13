<?php
/**
 * Month-picker field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;
$class       = implode( ' ', array_filter( array( 'yith-wcbk-month-picker-wrapper', $class ?? '' ) ) );
$value_class = implode( ' ', array_filter( array( 'yith-wcbk-month-picker-value', $value_class ?? '' ) ) );

$default_options      = array(
	'not_available_months' => array(),
	'min_date'             => time(),
	'max_date'             => strtotime( '+2 years' ),
);
$options              = $options ?? array();
$options              = wp_parse_args( $options, $default_options );
$not_available_months = $options['not_available_months'];
$min_date             = $options['min_date'];
$max_date             = $options['max_date'];

$start_year  = gmdate( 'Y', $min_date );
$start_month = gmdate( 'n', $min_date );
$end_year    = gmdate( 'Y', $max_date );
$end_month   = gmdate( 'n', $max_date );

$date_helper = yith_wcbk_date_helper();
?>

<div
		id="<?php echo esc_attr( $id ?? '' ); ?>"
		class="<?php echo esc_attr( $class ?? '' ); ?>"
		data-current-year="<?php echo esc_attr( $start_year ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<input type="hidden"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

			class="<?php echo esc_attr( $value_class ?? '' ); ?>"
			value="<?php echo esc_attr( $value ); ?>">
	<?php
	$current_year = $start_year;
	?>
	<?php while ( $current_year <= $end_year ) : ?>
		<?php
		$display  = $current_year === $start_year ? '' : 'display:none';
		$has_next = $current_year < $end_year;
		$has_prev = $start_year < $current_year;
		?>
		<div class="year year-<?php echo esc_attr( $current_year ); ?>" data-year="<?php echo esc_attr( $current_year ); ?>" style="<?php echo esc_attr( $display ); ?>">
			<div class="top-actions">
				<span class="yith-icon yith-icon-arrow-left-alt prev <?php echo $has_prev ? 'enabled' : ''; ?>"></span>
				<span class="yith-icon yith-icon-arrow-right-alt next <?php echo $has_next ? 'enabled' : ''; ?>"></span>
			</div>
			<table>
				<thead>
				<tr>
					<th colspan="3"><?php echo esc_html( $current_year ); ?> </th>
				</tr>
				</thead>
				<?php foreach ( range( 1, 12 ) as $month ) : ?>
					<?php
					$month_txt = $month < 10 ? '0' . $month : $month;

					$enabled       = ! in_array( $current_year . '-' . $month_txt, $not_available_months, true ) && strtotime( $current_year . '-' . $month_txt . '-01' ) > $min_date && strtotime( $current_year . '-' . $month_txt . '-01' ) < $max_date;
					$enabled_class = $enabled ? 'enabled' : 'disabled';
					$month_name    = date_i18n( 'M', mktime( 0, 0, 0, $month ) );
					$this_value    = $current_year . '-' . $month_txt . '-01';
					?>
					<?php if ( in_array( $month, array( 1, 4, 7, 10 ), true ) ) : ?>
						<tr>
					<?php endif; ?>
					<td class='month <?php echo esc_attr( $enabled_class ); ?>' data-value='<?php echo esc_attr( $this_value ); ?>'><?php echo esc_html( $month_name ); ?></td>
					<?php if ( in_array( $month, array( 3, 6, 9, 12 ), true ) ) : ?>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
		</div>
		<?php
		$current_year ++;
		?>
	<?php endwhile; ?>
</div>
