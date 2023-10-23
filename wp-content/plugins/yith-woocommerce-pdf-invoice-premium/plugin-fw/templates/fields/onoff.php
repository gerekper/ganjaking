<?php
/**
 * Template for displaying the onoff field
 *
 * @var array $field The field.
 * @package YITH\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $std, $value, $custom_attributes, $data, $desc_inline ) = yith_plugin_fw_extract( $field, 'id', 'class', 'name', 'std', 'value', 'custom_attributes', 'data', 'desc-inline' );

?>
<div class="yith-plugin-fw-onoff-container <?php echo ! empty( $class ) ? esc_attr( $class ) : ''; ?>"
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<input type="checkbox" id="<?php echo esc_attr( $field_id ); ?>"
			class="on_off"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
		<?php if ( isset( $std ) ) : ?>
			data-std="<?php echo esc_attr( $std ); ?>"
		<?php endif; ?>
		<?php checked( true, yith_plugin_fw_is_true( $value ) ); ?>
		<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	/>
	<span class="yith-plugin-fw-onoff">
		<span class="yith-plugin-fw-onoff__handle">
			<svg class="yith-plugin-fw-onoff__icon yith-plugin-fw-onoff__icon--on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
				<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
			</svg>
			<svg class="yith-plugin-fw-onoff__icon yith-plugin-fw-onoff__icon--off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
				<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
			</svg>
		</span>
		<span class="yith-plugin-fw-onoff__zero-width-space notranslate">&#8203;</span>
	</span>
</div>

<?php if ( isset( $desc_inline ) ) : ?>
	<span class='description inline'><?php echo wp_kses_post( $desc_inline ); ?></span>
<?php endif; ?>
