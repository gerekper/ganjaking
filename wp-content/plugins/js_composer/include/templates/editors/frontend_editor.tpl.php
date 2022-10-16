<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var Vc_Frontend_Editor $editor */
global $menu, $submenu, $parent_file, $post_ID, $post, $post_type, $post_type_object;
$post_ID = $editor->post_id;
$post = $editor->post;
$post_type = $post->post_type;
$post_type_object = get_post_type_object( $post_type );
$post_title = trim( $post->post_title );
$nonce_action = $nonce_action = 'update-post_' . $editor->post_id;
$user_ID = isset( $editor->current_user ) && isset( $editor->current_user->ID ) ? (int) $editor->current_user->ID : 0;
$form_action = 'editpost';
$menu = array();
add_thickbox();
wp_enqueue_media( array( 'post' => $editor->post_id ) );
require_once $editor->adminFile( 'admin-header.php' );
// @since 4.8 js logic for user role access manager.
vc_include_template( 'editors/partials/access-manager-js.tpl.php' );
$custom_tag = 'script';
?>
	<div id="vc_preloader"></div>
	<div id="vc_overlay_spinner" class="vc_ui-wp-spinner vc_ui-wp-spinner-dark vc_ui-wp-spinner-lg" style="display:none;"></div>
	<<?php echo esc_attr( $custom_tag ); ?>>
		document.getElementById( 'vc_preloader' ).style.height = window.screen.availHeight;
		window.vc_mode = '<?php echo esc_js( vc_mode() ); ?>';
		window.vc_iframe_src = '<?php echo esc_js( $editor->url ); ?>';
		window.wpbGutenbergEditorUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post-new.php?post_type=wpb_gutenberg_param' ) ) ); ?>';
	</<?php echo esc_attr( $custom_tag ); ?>>
	<input type="hidden" name="vc_post_title" id="vc_title-saved" value="<?php echo esc_attr( $post_title ); ?>"/>
	<input type="hidden" name="vc_post_id" id="vc_post-id" value="<?php echo esc_attr( $editor->post_id ); ?>"/>
<?php

// [vc_navbar frontend]
require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar-frontend.php' );
$nav_bar = new Vc_Navbar_Frontend( $post );
$nav_bar->render();
// [/vc_navbar frontend]
?>
	<div id="vc_inline-frame-wrapper"></div>
<?php
// [add element popup/box]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
$add_element_box = new Vc_Add_Element_Box();
$add_element_box->render();
// [/add element popup/box]

// [shortcodes edit form panel render]
wpbakery()->editForm()->render();
// [/shortcodes edit form panel render]

// [templates panel editor render]
if ( vc_user_access()->part( 'templates' )->can()->get() ) {
	wpbakery()->templatesPanelEditor()->renderUITemplate();
}
// [/templates panel editor render]

// [preset panel editor render]
wpbakery()->presetPanelEditor()->renderUIPreset();
// [/preset panel editor render]

// [post settings panel render]
if ( vc_user_access()->part( 'post_settings' )->can()->get() ) {
	require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings.php' );
	$post_settings = new Vc_Post_Settings( $editor );
	$post_settings->renderUITemplate();
}
// [/post settings panel render]

// [panel edit layout render]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-edit-layout.php' );
$edit_layout = new Vc_Edit_Layout();
$edit_layout->renderUITemplate();
// [/panel edit layout render]

// fe controls
vc_include_template( 'editors/partials/frontend_controls.tpl.php' );

// [shortcodes presets data]
if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_vendor_settings_presets = Vc_Settings_Preset::listDefaultVendorSettingsPresets();
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_vendor_settings_presets = array();
	$vc_all_presets = array();
}
// [/shortcodes presets data]

?>
	<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css" value="<?php echo esc_attr( $editor->post_custom_css ); ?>" autocomplete="off"/>
	<<?php echo esc_attr( $custom_tag ); ?>>
		window.vc_user_mapper = <?php echo wp_json_encode( WPBMap::getUserShortCodes() ); ?>;
		window.vc_mapper = <?php echo wp_json_encode( WPBMap::getShortCodes() ); ?>;
		window.vc_vendor_settings_presets = <?php echo wp_json_encode( $vc_vendor_settings_presets ); ?>;
		window.vc_all_presets = <?php echo wp_json_encode( $vc_all_presets ); ?>;
		window.vc_roles = [];
		window.vcAdminNonce = '<?php echo esc_js( vc_generate_nonce( 'vc-admin-nonce' ) ); ?>';
		window.wpb_js_google_fonts_save_nonce = '<?php echo esc_js( wp_create_nonce( 'wpb_js_google_fonts_save' ) ); ?>';
		window.vc_post_id = <?php echo esc_js( $post_ID ); ?>;
	</<?php echo esc_attr( $custom_tag ); ?>>

<?php vc_include_template( 'editors/partials/vc_settings-image-block.tpl.php' ); ?>
<!-- BC for older plugins 5.5 !-->
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr( $post_ID ); ?>"/>
	<div style="height: 1px; visibility: hidden; overflow: hidden;">
		<?php
		// Disable notice in edit-form-advanced.php
		$is_IE = false;
		wp_editor( '', 'vc-hidden-editor', array(
			'editor_height' => 300,
			'tinymce' => array(
				'resize' => false,
				'wp_autoresize_on' => false,
				'add_unload_trigger' => false,
				'wp_keep_scroll_position' => ! $is_IE,
			),
		) );
		// Fix: WP 4.0
		wp_dequeue_script( 'editor-expand' );
		do_action( 'vc_frontend_editor_render_template' );
		?>
	</div>
<?php

// other admin footer files and actions.
require_once $editor->adminFile( 'admin-footer.php' ); ?>
