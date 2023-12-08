<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var string $type */
/** @var string $field_id */
?>
<div class="vc_ui-icon-ai"
	data-wpb-ai-element-type="<?php echo esc_attr( str_replace( ' ', '',  $type ) ); ?>"
	<?php
	if ( is_string( $field_id ) ) {
		echo ' data-field-id="' . esc_attr( $field_id ) . '" ';
	}
	?>
	title="<?php echo esc_html__( 'WPBakery AI Assistant', 'js_composer' ); ?>"
>
	<svg xmlns="http://www.w3.org/2000/svg" height="19px" width="19px" viewBox="0 0 20 20" fill="currentColor">
		<path d="M13 7H7v6h6V7z" />
		<path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd" />
	</svg>
	<span><?php echo esc_html__( 'AI', 'js_composer' ) ?></span>
</div>
