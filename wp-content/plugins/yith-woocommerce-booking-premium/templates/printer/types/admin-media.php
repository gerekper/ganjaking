<?php
/**
 * Admin date-picker field.
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

$field_id          = $id ?? '';
$name              = $name ?? '';
$class             = $class ?? '';
$value             = absint( $value ?? '' );
$icon              = $icon ?? 'photo-add';
$data              = $data ?? array();
$custom_attributes = ! empty( $custom_attributes ) && is_array( $custom_attributes ) ? $custom_attributes : array();
$image_src         = ! ! $value ? wp_get_attachment_image_src( $value ) : array();
$image_src         = ! ! $image_src ? $image_src[0] : '';

$classes = array(
	'yith-wcbk-admin-media',
	$class,
	! ! $value && ! ! $image_src ? 'yith-wcbk-admin-media--has-image' : '',
);
$classes = implode( ' ', array_filter( $classes ) );

wp_enqueue_media();
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<input type="hidden" class="yith-wcbk-admin-media__field" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
	<span class="yith-wcbk-admin-media__clear yith-icon yith-icon-close-alt"></span>
	<img class="yith-wcbk-admin-media__image" src="<?php echo esc_url( $image_src ); ?>"/>
	<div class="yith-wcbk-admin-media__placeholder">
		<i class="yith-icon yith-icon-<?php echo esc_attr( $icon ); ?>"></i>
	</div>
</div>
