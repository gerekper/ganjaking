<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/popups/class-vc-add-element-box-grid-item.php' );
$add_element_box = new Vc_Add_Element_Box_Grid_Item();
$add_element_box->render();
// Edit form for mapped shortcode.
wpbakery()->editForm()->render();
require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/popups/class-vc-templates-editor-grid-item.php' );
$templates_editor = new Vc_Templates_Editor_Grid_Item();
$templates_editor->renderUITemplate();

$grid_item = new Vc_Grid_Item();
$shortcodes = $grid_item->shortcodes();

if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_vendor_settings_presets = Vc_Settings_Preset::listDefaultVendorSettingsPresets();
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_vendor_settings_presets = array();
	$vc_all_presets = array();
}
$custom_tag = 'script';
?>
	<<?php echo esc_attr( $custom_tag ); ?>>
		window.vc_user_mapper = <?php echo wp_json_encode( WpbMap_Grid_Item::getGitemUserShortCodes() ); ?>;
		window.vc_mapper = <?php echo wp_json_encode( WpbMap_Grid_Item::getShortCodes() ); ?>;
		window.vc_vendor_settings_presets = <?php echo wp_json_encode( $vc_vendor_settings_presets ); ?>;
		window.vc_all_presets = <?php echo wp_json_encode( $vc_all_presets ); ?>;
		window.vc_frontend_enabled = false;
		window.vc_mode = '<?php echo esc_js( vc_mode() ); ?>';
		window.vcAdminNonce = '<?php echo esc_js( vc_generate_nonce( 'vc-admin-nonce' ) ); ?>';
	</<?php echo esc_attr( $custom_tag ); ?>>

	<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_settings-image-block">
		<li class="added">
			<div class="inner" style="width: 80px; height: 80px; overflow: hidden;text-align: center;">
				<img rel="{{ id }}" src="<# if(sizes && sizes.thumbnail) { #>{{ sizes.thumbnail.url }}<# } else {#>{{ url }}<# } #>"/>
			</div>
			<a href="#" class="vc_icon-remove"><i class="vc-composer-icon vc-c-icon-close"></i></a>
		</li>
	</<?php echo esc_attr( $custom_tag ); ?>>
<?php foreach ( WpbMap_Grid_Item::getShortCodes() as $sc_base => $el ) : ?>
	<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_shortcode-template-<?php echo esc_attr( $sc_base ); ?>">
		<?php
		// @codingStandardsIgnoreLine
		print wpbakery()->getShortCode( $sc_base )->template();
		?>
	</<?php echo esc_attr( $custom_tag ); ?>>
<?php endforeach ?>

<?php
vc_include_template( 'editors/partials/access-manager-js.tpl.php' );
