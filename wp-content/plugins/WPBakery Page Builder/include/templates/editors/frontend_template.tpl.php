<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div id="vc_template-html">
	<?php
	/** @var Vc_Frontend_Editor $editor */
	?>
	<?php
	// @codingStandardsIgnoreLine
	print apply_filters( 'vc_frontend_template_the_content', $editor->parseShortcodesString( $editor->getTemplateContent() ) );
	?>
	<div data-type="files">
		<?php
		_print_styles();
		print_head_scripts();
		print_late_styles();
		print_footer_scripts();
		?>
	</div>
</div>
<div id="vc_template-data"><?php echo esc_html( wp_json_encode( $editor->post_shortcodes ) ); ?></div>
