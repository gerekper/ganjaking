<?php
/**
 * Template for displaying the Media field
 *
 * @var array $field The field.
 * @package YITH\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $store_as, $allow_custom_url, $default, $custom_attributes, $data ) = yith_plugin_fw_extract( $field, 'id', 'class', 'name', 'value', 'store_as', 'allow_custom_url', 'default', 'custom_attributes', 'data' );

$allowed_store_as = array( 'url', 'id' );
$store_as         = $store_as ?? 'url';
$store_as         = in_array( $store_as, $allowed_store_as, true ) ? $store_as : 'url';
$allow_custom_url = $allow_custom_url ?? true;
$allow_custom_url = $allow_custom_url && 'url' === $store_as;

$url = $value;
if ( 'id' === $store_as ) {
	$url = wp_get_attachment_url( $value );
}

$is_image  = $url && preg_match( '/(jpg|jpeg|png|gif|ico|svg|jpe|webp)$/', $url );
$file_name = $url ? basename( wp_parse_url( $url, PHP_URL_PATH ) ) : '';

$classes = array(
	'yith-plugin-fw-media',
	$allow_custom_url ? 'yith-plugin-fw-media--has-tabs' : '',
	$class,
);

$classes = implode( ' ', array_filter( $classes ) );

$preview_type = $is_image ? 'image' : ( $file_name ? 'file' : 'upload' );

wp_enqueue_media(); // Late enqueue media scripts.
wp_enqueue_script( 'wp-media-utils' );
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( $allow_custom_url ) : ?>
		<div class="yith-plugin-fw-media__tabs">
			<div class="yith-plugin-fw-media__tab yith-plugin-fw-media__upload-tab yith-plugin-fw-media__tab--active" data-tab-selector=".yith-plugin-fw-media__preview">
				<svg class="yith-plugin-fw-media__tab__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path>
				</svg>
				<span class="yith-plugin-fw-media__tab__title"><?php echo esc_html__( 'Upload file', 'yith-plugin-fw' ); ?></span>
			</div>
			<div class="yith-plugin-fw-media__tab yith-plugin-fw-media__url-tab" data-tab-selector=".yith-plugin-fw-media__value-container">
				<svg class="yith-plugin-fw-media__tab__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path>
				</svg>
				<span class="yith-plugin-fw-media__tab__title"><?php echo esc_html__( 'Enter URL', 'yith-plugin-fw' ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<div class="yith-plugin-fw-media__content">
		<div class="yith-plugin-fw-media__preview" data-type="<?php echo esc_attr( $preview_type ); ?>">
			<img class="yith-plugin-fw-media__preview__image" src="<?php echo esc_url( $url ); ?>"/>
			<div class="yith-plugin-fw-media__preview__file">
				<svg class="yith-plugin-fw-media__preview__file__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
				</svg>
				<span class="yith-plugin-fw-media__preview__file__name"><?php echo esc_html( $file_name ); ?></span>
			</div>
			<div class="yith-plugin-fw-media__preview__upload">
				<?php
				echo sprintf(
				// translators: %s is the alternative action "Upload a file".
					esc_html__( 'Drag or %s', 'yith-plugin-fw' ),
					'<a href="#" class="yith-plugin-fw-media__preview__upload__link">' . esc_html__( 'Upload a file', 'yith-plugin-fw' ) . '</a>'
				)
				?>
			</div>

			<div class="yith-plugin-fw-media__preview__actions">
				<i class="yith-plugin-fw-media__preview__action yith-plugin-fw-media__preview__action--edit yith-icon yith-icon-edit"></i>
				<i class="yith-plugin-fw-media__preview__action yith-plugin-fw-media__preview__action--delete yith-icon yith-icon-trash"></i>
			</div>

			<div class="yith-plugin-fw-media__preview__dropzone"></div>
		</div>
		<div class="yith-plugin-fw-media__value-container" style="display: none">
			<input type="<?php echo 'url' === $store_as ? 'text' : 'hidden'; ?>"
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					class="yith-plugin-fw-media__value <?php echo 'url' === $store_as ? 'yith-plugin-fw-media__url-value' : 'yith-plugin-fw-media__id-value'; ?>"
					value="<?php echo esc_attr( $value ); ?>"

				<?php if ( isset( $default ) ) : ?>
					data-std="<?php echo esc_attr( $default ); ?>"
				<?php endif; ?>

				<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
				<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
			/>
		</div>
	</div>
</div>
