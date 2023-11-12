<?php
/**
 * @var array $value
 * @var array $field_prefix
 * @var string $area
 */
?>

<div class="vc_ui-settings-text-wrapper">
	<p>
		&lt;script&gt;
	</p>
	<?php
	vc_include_template(
		'editors/partials/icon-ai.tpl.php',
		[
			'type' => 'custom_js',
			'field_id' => 'wpb_js_' . esc_attr( $area ) . '_editor',
		]
	);
	?>
</div>
<textarea name="<?php echo esc_attr( $field_prefix ); ?>custom_js_<?php echo esc_attr( $area ); ?>" class="wpb_code_editor custom_code" data-code-type="html" style="display:none"><?php echo esc_textarea( $value ); ?></textarea>
<pre id="wpb_js_<?php echo esc_attr( $area ); ?>_editor" class="wpb_content_element custom_code">
	<?php
	echo esc_textarea( $value );
	?>
</pre>
<p>
	&lt;/script&gt;
</p>
