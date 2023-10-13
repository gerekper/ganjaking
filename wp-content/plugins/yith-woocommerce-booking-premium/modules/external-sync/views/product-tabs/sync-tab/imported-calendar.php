<?php
/**
 * Imported calendar row.
 *
 * @var int    $index
 * @var string $name
 * @var string $url
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$is_empty = ! $name && ! $url;
$classes  = $is_empty ? 'is-empty' : '';
?>
<tr class="<?php echo esc_attr( $classes ); ?>">
	<td>
		<input type="text" name="_yith_booking_external_calendars[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $name ); ?>"/>
	</td>
	<td>
		<input type="text" name="_yith_booking_external_calendars[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $url ); ?>"/>
	</td>
	<td class="yith-wcbk-product-sync-imported-calendars-table__delete-column">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => 'yith-icon',
				'icon'  => 'trash',
				'class' => 'yith-wcbk-trash-icon-action delete',
			)
		)
		?>
	</td>
</tr>
