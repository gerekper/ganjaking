<?php
/**
 * @var array $value
 * @var string $field_prefix
 */
?>

<div class="vc_ui-settings-text-wrapper">
	<?php
	wpb_add_ai_icon_to_code_field( 'custom_css',  'wpb_css_editor' );
	?>
</div>
<textarea name="<?php echo esc_attr( $field_prefix ) ?>custom_css" class="wpb_code_editor custom_code" style="display:none"><?php echo esc_textarea( $value ); ?></textarea>
<pre id="wpb_css_editor" class="wpb_content_element custom_code" >
	<?php echo esc_textarea( $value ); ?>
</pre>
<p class="description indicator-hint">
	<?php
	esc_html_e( 'Add custom CSS code to the plugin without modifying files.', 'js_composer' );
	?>
</p>
