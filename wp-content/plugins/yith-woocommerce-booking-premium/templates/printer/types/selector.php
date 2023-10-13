<?php
/**
 * Selector field.
 *
 * @var string $id                The ID.
 * @var string $name              The name.
 * @var string $class             The class.
 * @var string $field_class       The field class.
 * @var array  $value             The value.
 * @var string $placeholder       The placeholder.
 * @var array  $options           The options.
 * @var bool   $multiple          Multiple flag.
 * @var array  $data              The data.
 * @var array  $custom_attributes Custom attributes.
 * @var bool   $use_images        Use images flag.
 * @var bool   $allow_clear       Allow clear flag.
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$multiple      = $multiple ?? false;
$layout        = $layout ?? 'dropdown';
$layout        = in_array( $layout, array( 'dropdown', 'list' ), true ) ? $layout : 'dropdown';
$default_value = ! $multiple ? '' : array();
$placeholder   = $placeholder ?? '';
$field_class   = $field_class ?? '';
$use_images    = $use_images ?? false;
$options       = $options ?? array();
$allow_clear   = $allow_clear ?? false;
$keyed_options = $options;

if ( ! empty( $value ) && $multiple ) {
	$value = (array) $value;
}

if ( empty( $value ) ) {
	$value = $default_value;
}

if ( ! $allow_clear && ! $value && ! $multiple ) {
	$first_key = current( array_keys( $options ) );
	if ( $first_key ) {
		$value = $first_key;
	}
}

// Convert values to string to prevent issues with strict comparisons.
$value       = ! ! $multiple ? array_map( 'strval', $value ) : strval( $value );
$option_keys = array_map( 'strval', array_keys( $keyed_options ) );

$options = array_map(
	function ( $key, $option ) {
		$option_array = $option;
		if ( is_string( $option ) ) {
			$option_array = array( 'label' => $option );
		}

		$option_image     = $option['image'] ?? '';
		$option_image_id  = $option['image_id'] ?? '';
		$option_image_url = $option['image_url'] ?? '';

		if ( ! $option_image ) {
			if ( $option_image_url ) {
				$option_image = '<img src="' . esc_url( $option_image_url ) . '" />';
			} elseif ( $option_image_id ) {
				$option_image = wp_get_attachment_image( $option_image_id );
			}
		}

		$option_array['image'] = $option_image;

		$option_array['key'] = $key;

		return $option_array;
	},
	array_keys( $options ),
	$options
);

$keyed_options = array_combine( $option_keys, $options );

$selected_names = '';
if ( $value ) {
	$selected_names = implode(
		', ',
		array_filter(
			array_map(
				function ( $option ) use ( $value, $multiple ) {
					$option_key   = strval( $option['key'] ?? '' );
					$option_label = $option['label'] ?? '';
					$is_selected  = ! ! $multiple ? in_array( $option_key, $value, true ) : $option_key === $value;

					return ! ! $is_selected ? $option_label : '';
				},
				$options
			)
		)
	);
}

$selected_image = '';
if ( ! $multiple && $value ) {
	$selected_option = $keyed_options[ $value ] ?? false;
	if ( $selected_option ) {
		$selected_image = $selected_option['image'] ?? '';
	}
}

$classes     = array(
	'yith-wcbk-selector',
	"yith-wcbk-selector--{$layout}-layout",
	$class,
	$multiple ? 'yith-wcbk-selector--multiple' : '',
	$use_images ? 'yith-wcbk-selector--use-images' : '',
);
$classes     = implode( ' ', array_filter( $classes ) );
$label_class = ! ! $value ? 'yith-wcbk-selector__label--selected' : 'yith-wcbk-selector__label--placeholder';
$head_class  = ! ! $selected_image ? '' : 'yith-wcbk-selector__head--no-image';

$options_container_class = "yith-wcbk-selector__{$layout}";
?>

<div id="<?php echo esc_attr( $id ); ?>"
		class="<?php echo esc_attr( $classes ); ?>"
		data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
		data-options="<?php echo esc_attr( wp_json_encode( $options ) ); ?>"
		data-selected="<?php echo esc_attr( wp_json_encode( $value ) ); ?>"
		data-multiple="<?php echo esc_attr( $multiple ? '1' : '0' ); ?>"
		data-allow-clear="<?php echo esc_attr( $allow_clear ? '1' : '0' ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<select
			class="yith-wcbk-selector__field <?php echo esc_attr( $field_class ); ?>"
			name="<?php echo esc_attr( $name ); ?>"

		<?php if ( $multiple ) : ?>
			multiple
		<?php endif; ?>
	>
		<?php if ( $allow_clear && ! $multiple ) : ?>
			<option value=""></option>
		<?php endif; ?>

		<?php foreach ( $options as $option ) : ?>
			<?php
			$option_key   = strval( $option['key'] ?? '' );
			$option_label = $option['label'] ?? '';
			$is_selected  = ! ! $multiple ? in_array( $option_key, $value, true ) : $option_key === $value;
			?>
			<option
					value="<?php echo esc_attr( $option_key ); ?>"
				<?php selected( $is_selected, true, true ); ?>
			><?php echo esc_html( $option_label ); ?></option>

		<?php endforeach; ?>
	</select>

	<?php if ( 'list' !== $layout ) : ?>
		<div class="yith-wcbk-selector__head <?php echo esc_attr( $head_class ); ?>">
			<?php if ( ! $multiple && $use_images ) : ?>
				<div class="yith-wcbk-selector__label__image">
					<?php if ( $selected_image ) : ?>
						<?php echo wp_kses_post( $selected_image ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="yith-wcbk-selector__label <?php echo esc_attr( $label_class ); ?>">
				<?php echo esc_html( ! ! $value ? $selected_names : $placeholder ); ?>
			</div>
			<?php if ( ! $multiple && $allow_clear ) : ?>
				<div class="yith-wcbk-selector__head__clear yith-wcbk-selector__clear">
					<i class="yith-icon yith-icon-close-alt"></i>
				</div>
			<?php endif; ?>
			<i class="yith-wcbk-selector__head__arrow yith-icon yith-icon-arrow-down-alt"></i>
		</div>
	<?php endif; ?>
	<div class="<?php echo esc_attr( $options_container_class ); ?>">
		<div class="yith-wcbk-selector__items">
			<?php foreach ( $options as $option ) : ?>
				<?php
				$option_key         = strval( $option['key'] ?? '' );
				$option_label       = $option['label'] ?? '';
				$option_description = $option['description'] ?? '';
				$option_image       = $option['image'] ?? '';
				$is_selected        = ! ! $multiple ? in_array( $option_key, $value, true ) : $option_key === $value;

				$item_classes = array(
					'yith-wcbk-selector__item',
					$is_selected ? 'yith-wcbk-selector__item--selected' : '',
				);
				$item_classes = implode( ' ', array_filter( $item_classes ) );
				?>
				<div class="<?php echo esc_attr( $item_classes ); ?>" data-key="<?php echo esc_attr( $option_key ); ?>">

					<?php if ( $multiple ) : ?>
						<div class="yith-wcbk-selector__item__check"></div>
					<?php endif; ?>

					<?php if ( $use_images ) : ?>
						<div class="yith-wcbk-selector__item__image">
							<?php if ( $option_image ) : ?>
								<?php echo wp_kses_post( $option_image ); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="yith-wcbk-selector__item__content">
						<div class="yith-wcbk-selector__item__label"><?php echo wp_kses_post( $option_label ); ?></div>
						<?php if ( $option_description ) : ?>
							<div class="yith-wcbk-selector__item__description"><?php echo wp_kses_post( $option_description ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $multiple || ( 'list' === $layout && $allow_clear ) ) : ?>
			<div class="yith-wcbk-selector__footer">
				<?php if ( $allow_clear ) : ?>
					<span class="yith-wcbk-selector__link yith-wcbk-selector__clear"><?php esc_html_e( 'Clear', 'yith-booking-for-woocommerce' ); ?></span>
				<?php endif; ?>
				<?php if ( $multiple && 'list' !== $layout ) : ?>
					<span class="yith-wcbk-selector__button yith-wcbk-selector__close"><?php esc_html_e( 'Save', 'yith-booking-for-woocommerce' ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
