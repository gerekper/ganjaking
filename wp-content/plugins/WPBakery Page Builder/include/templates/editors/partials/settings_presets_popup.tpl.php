<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$saveAsTemplateElements = apply_filters( 'vc_popup_save_as_template_elements', array(
	'vc_row',
	'vc_section',
) );
$custom_tag = 'script'; // TODO: Remove this file after 6.2 when BC is completed
?>
<div class="vc_ui-list-bar-group">
	<?php if ( in_array( $shortcode_name, $saveAsTemplateElements ) && vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) : ?>
		<ul class="vc_ui-list-bar">
			<li class="vc_ui-list-bar-item">
				<button type="button" class="vc_ui-list-bar-item-trigger" data-vc-save-template>
					<?php esc_html_e( 'Save as template', 'js_composer' ); ?>
				</button>
			</li>
		</ul>
	<?php endif; ?>
	<?php if ( ! in_array( $shortcode_name, $saveAsTemplateElements ) && vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get() ) : ?>
		<ul class="vc_ui-list-bar">
			<li class="vc_ui-list-bar-item">
				<button type="button" class="vc_ui-list-bar-item-trigger" data-vc-save-settings-preset>
					<?php esc_html_e( 'Save as Element', 'js_composer' ); ?>
				</button>
			</li>
		</ul>
	<?php endif; ?>
	<<?php echo esc_attr( $custom_tag ); ?>>
		window.vc_presets_data = {
			"presets": <?php echo wp_json_encode( $list_presets ); ?>,
			"presetsCount": <?php echo count( $list_presets[0] ) + count( $list_presets[1] ); ?>,
			"defaultId": <?php echo (int) $default_id; ?>,
			"can": <?php echo (int) vc_user_access()->part( 'presets' )->can()->get(); ?>,
			"defaultTitle": "<?php esc_attr_e( 'Untitled', 'js_composer' ); ?>"
		}
	</<?php echo esc_attr( $custom_tag ); ?>>
</div>
