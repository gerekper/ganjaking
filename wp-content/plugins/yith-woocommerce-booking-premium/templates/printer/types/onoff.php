<?php
/**
 * On-off field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$enabled = 'yes' === $value;
$value   = $enabled ? 'yes' : 'no';

$classes = array(
	'yith-wcbk-printer-field__on-off',
	$enabled ? 'yith-wcbk-printer-field__on-off--enabled' : '',
	$class ?? '',
);
$class   = implode( ' ', array_filter( $classes ) );

?>
<span
		id="<?php echo esc_attr( $id ?? '' ); ?>"
		class="<?php echo esc_attr( $class ?? '' ); ?>"
	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<input type="hidden"
			class="yith-wcbk-printer-field__on-off__value"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

			value="<?php echo esc_attr( $value ); ?>"
	/>
	<span class="yith-wcbk-printer-field__on-off__handle">
		<svg class="yith-wcbk-printer-field__on-off__icon yith-wcbk-printer-field__on-off__icon--on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
			<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
		</svg>
		<svg class="yith-wcbk-printer-field__on-off__icon yith-wcbk-printer-field__on-off__icon--off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
			<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
		</svg>
	</span>
	<span class="yith-wcbk-printer-field__on-off__zero-width-space notranslate">&#8203;</span>
</span>
