<?php
/**
 * Radio fields.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 * @var array  $options
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$options = $options ?? array();
?>

<span class="yith-wcbk-printer-field__radios"
		id="<?php echo esc_attr( $id ?? '' ); ?>"
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<?php foreach ( $options as $option_value => $option_title ) : ?>
		<?php
		$radio_id = '';
		if ( ! empty( $id ) ) {
			$radio_id = $id;
		} elseif ( ! empty( $name ) ) {
			$radio_id = $name;

		}
		$radio_id .= '-' . sanitize_key( $option_value );
		?>
		<input type="radio"
				id="<?php echo esc_attr( $radio_id ); ?>"

				<?php if ( ! ! $name ) : ?>
					name="<?php echo esc_attr( $name ); ?>"
				<?php endif; ?>

				class="<?php echo esc_attr( $class ?? '' ); ?>"
				value="<?php echo esc_attr( $option_value ); ?>"

			<?php checked( $value === $option_value, true, true ); ?>

			<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>

		/>
		<label for="<?php echo esc_attr( $radio_id ); ?>"><?php echo wp_kses_post( $option_title ); ?></label>
	<?php endforeach; ?>
</span>
