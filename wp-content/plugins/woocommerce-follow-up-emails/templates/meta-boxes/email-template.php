<?php
/* @var FUE_Email $email */
$templates = fue_get_installed_templates();
?>
<select id="template" name="template" style="width: 100%;" data-nonce="<?php echo esc_attr( wp_create_nonce( 'update_email_template' ) ); ?>">
	<?php
	foreach ( $templates as $template ):
		$template = fue_template_basename( $template );
		$tpl = new FUE_Email_Template( $template );
		$name = $tpl->name;

		if (! $name ) {
			$name = $template;
		}
	?>
	<option value="<?php echo esc_attr( $template ); ?>" <?php selected( basename( $email->template ), $template ); ?>><?php echo wp_kses( $name, array() ); ?></option>
	<?php endforeach; ?>
</select>