<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var Vc_Backend_Editor | Vc_Frontend_Editor $editor */

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

// [modal ai render]
vc_include_template( 'editors/popups/ai/modal.tpl.php' );
// [/modal ai render]
