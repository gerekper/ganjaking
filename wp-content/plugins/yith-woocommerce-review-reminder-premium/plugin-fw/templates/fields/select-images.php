<?php
/**
 * Template for displaying the select-images field
 *
 * @var array $field The field.
 * @package YITH\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $options, $custom_attributes, $data ) = yith_plugin_fw_extract( $field, 'id', 'class', 'name', 'value', 'options', 'custom_attributes', 'data' );

$class = isset( $class ) ? $class : 'yith-plugin-fw-select-images';
?>
<div id="<?php echo esc_attr( $field_id ); ?>-wrapper" class="yith-plugin-fw-select-images__wrapper" data-type="select-images">
	<select id="<?php echo esc_attr( $field_id ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			class="<?php echo esc_attr( $class ); ?>"
			style="display: none"
		<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
		<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
	>
		<?php foreach ( $options as $key => $item ) : ?>
			<?php
			$label = ! empty( $item['label'] ) ? $item['label'] : $key;
			?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?> ><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>

	<ul class="yith-plugin-fw-select-images__list">
		<?php foreach ( $options as $key => $item ) : ?>
			<?php
			$label = ! empty( $item['label'] ) ? $item['label'] : $key;
			$image = ! empty( $item['image'] ) ? $item['image'] : '';
			?>
			<?php if ( $image ) : ?>
				<?php
				$selected_class = 'yith-plugin-fw-select-images__item--selected';
				$current_class  = $key === $value ? $selected_class : '';
				?>
				<li class="yith-plugin-fw-select-images__item <?php echo esc_attr( $current_class ); ?>"
						data-type="select-images-item"
						data-key="<?php echo esc_attr( $key ); ?>"
					<?php echo isset( $item['data'] ) ? yith_plugin_fw_html_data_to_string( $item['data'] ) : ''; ?>
				>
					<div class="yith-plugin-fw-select-images__item__selected-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
							<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
						</svg>
					</div>
					<?php if ( $label ) : ?>
						<div class="yith-plugin-fw-select-images__item__label"><?php echo esc_html( $label ); ?></div>
					<?php endif; ?>
					<img class="yith-plugin-fw-select-images_src" src="<?php echo esc_url( $image ); ?>"/>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
