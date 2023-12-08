<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
global $vc_grid_item_editor;
/**
 * Creates new post type for grid_editor.
 *
 * @since 4.4
 */
function vc_grid_item_editor_create_post_type() {
	if ( is_admin() ) {
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
		Vc_Grid_Item_Editor::createPostType();
		add_action( 'vc_menu_page_build', 'vc_gitem_add_submenu_page' );
		// TODO: add check vendor is active
		add_filter( 'vc_vendor_qtranslate_enqueue_js_backend', 'vc_vendor_qtranslate_enqueue_js_backend_grid_editor' );
	}
}

/**
 * @since 4.5
 */
function vc_vendor_qtranslate_enqueue_js_backend_grid_editor() {
	return true;
}

/**
 * Set required objects to render editor for grid item
 *
 * @since 4.4
 */
function vc_grid_item_editor_init() {
	global $vc_grid_item_editor;
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-wpb-map-grid-item.php' );
	$vc_grid_item_editor = new Vc_Grid_Item_Editor();
	$vc_grid_item_editor->addMetaBox();
	add_action( 'wp_ajax_vc_grid_item_editor_load_template_preview', array(
		&$vc_grid_item_editor,
		'renderTemplatePreview',
	) );
	$vc_grid_item_editor->addHooksSettings();
}

/**
 *  Render preview for grid item
 * @since 4.4
 */
function vc_grid_item_render_preview() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( array(
		'edit_post',
		(int) vc_request_param( 'post_id' ),
	) )->validateDie()->part( 'grid_builder' )->can()->validateDie();

	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
	$grid_item = new Vc_Grid_Item();
	$grid_item->mapShortcodes();
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-preview.php' );
	$vcGridPreview = new Vc_Grid_Item_Preview();
	add_filter( 'vc_gitem_template_attribute_post_image_background_image_css_value', array(
		$vcGridPreview,
		'addCssBackgroundImage',
	) );
	add_filter( 'vc_gitem_template_attribute_post_image_url_value', array(
		$vcGridPreview,
		'addImageUrl',
	) );
	add_filter( 'vc_gitem_template_attribute_post_full_image_url_value', array(
		$vcGridPreview,
		'addImageUrl',
	) );
	add_filter( 'vc_gitem_template_attribute_post_image_html', array(
		$vcGridPreview,
		'addImage',
	) );
	add_filter( 'vc_gitem_attribute_featured_image_img', array(
		$vcGridPreview,
		'addPlaceholderImage',
	) );
	add_filter( 'vc_gitem_post_data_get_link_real_link', array(
		$vcGridPreview,
		'disableRealContentLink',
	), 10, 4 );
	add_filter( 'vc_gitem_post_data_get_link_link', array(
		$vcGridPreview,
		'disableContentLink',
	), 10, 3 );
	add_filter( 'vc_gitem_zone_image_block_link', array(
		$vcGridPreview,
		'disableGitemZoneLink',
	) );
	$vcGridPreview->render();
	die();
}

/**
 * Map grid element shortcodes.
 *
 * @since 4.5
 */
function vc_grid_item_map_shortcodes() {
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
	$grid_item = new Vc_Grid_Item();
	$grid_item->mapShortcodes();
	vc_mapper()->setCheckForAccess( false );
}

/**
 * Get current post type
 *
 * @return null|string
 */
function vc_grid_item_get_post_type() {
	$post_type = null;
	if ( vc_request_param( 'post_type' ) ) {
		$post_type = vc_request_param( 'post_type' );
	} elseif ( vc_request_param( 'post' ) ) {
		$post = get_post( vc_request_param( 'post' ) );
		$post_type = $post instanceof WP_Post && $post->post_type ? $post->post_type : null;
	}

	return $post_type;
}

/**
 * Check and Map grid element shortcodes if required.
 * @since 4.5
 */
function vc_grid_item_editor_shortcodes() {
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
	// TODO: remove this because mapping can be based on post_type
	if ( ( 'true' === vc_request_param( 'vc_grid_item_editor' ) || ( is_admin() && vc_grid_item_get_post_type() === Vc_Grid_Item_Editor::postType() ) && vc_user_access()
			->wpAny( 'edit_posts', 'edit_pages' )->part( 'grid_builder' )->can()->get() ) ) {

		global $vc_grid_item_editor;
		add_action( 'vc_user_access_check-shortcode_edit', array(
			&$vc_grid_item_editor,
			'accessCheckShortcodeEdit',
		), 10, 2 );
		add_action( 'vc_user_access_check-shortcode_all', array(
			&$vc_grid_item_editor,
			'accessCheckShortcodeAll',
		), 10, 2 );

		vc_grid_item_map_shortcodes();
	}
}

/**
 * add action in admin for vc grid item editor manager
 */
add_action( 'init', 'vc_grid_item_editor_create_post_type' );
add_action( 'admin_init', 'vc_grid_item_editor_init' );
add_action( 'vc_after_init', 'vc_grid_item_editor_shortcodes' );
/**
 * Call preview as ajax request is called.
 */
add_action( 'wp_ajax_vc_gitem_preview', 'vc_grid_item_render_preview', 5 );

/**
 * Add WP ui pointers in grid element editor.
 */
if ( is_admin() ) {
	add_filter( 'vc_ui-pointers-vc_grid_item', 'vc_grid_item_register_pointer' );
}

/**
 * @param $pointers
 * @return mixed
 */
function vc_grid_item_register_pointer( $pointers ) {
	$screen = get_current_screen();
	if ( 'add' === $screen->action ) {
		$pointers['vc_grid_item'] = array(
			'name' => 'vcPointersController',
			'messages' => array(
				array(
					'target' => '#vc_templates-editor-button',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Start Here!', 'js_composer' ), esc_html__( 'Start easy - use predefined template as a starting point and modify it.', 'js_composer' ) ),
						'position' => array(
							'edge' => 'left',
							'align' => 'center',
						),
					),
				),
				array(
					'target' => '[data-vc-navbar-control="animation"]',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Use Animations', 'js_composer' ), esc_html__( 'Select animation preset for grid element. "Hover" state will be added next to the "Normal" state tab.', 'js_composer' ) ),
						'position' => array(
							'edge' => 'right',
							'align' => 'center',
						),
					),
				),
				array(
					'target' => '.vc_gitem_animated_block-shortcode',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Style Design Options', 'js_composer' ), esc_html__( 'Edit "Normal" state to set "Featured image" as a background, control zone sizing proportions and other design options (Height mode: Select "Original" to scale image without cropping).', 'js_composer' ) ),
						'position' => array(
							'edge' => 'bottom',
							'align' => 'center',
						),
					),
				),
				array(
					'target' => '[data-vc-gitem="add-c"][data-vc-position="top"]',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Extend Element', 'js_composer' ), esc_html__( 'Additional content zone can be added to grid element edges (Note: This zone can not be animated).', 'js_composer' ) ) . '<p><img src="' . esc_url( vc_asset_url( 'vc/gb_additional_content.png' ) ) . '" alt="" /></p>',
						'position' => array(
							'edge' => 'right',
							'align' => 'center',
						),
					),
				),
				array(
					'target' => '#wpadminbar',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> %s', esc_html__( 'Watch Video Tutorial', 'js_composer' ), '<p>' . esc_html__( 'Have a look how easy it is to work with grid element builder.', 'js_composer' ) . '</p>' . '<iframe width="500" height="281" src="https://www.youtube.com/embed/sBvEiIL6Blo" frameborder="0" allowfullscreen></iframe>' ),
						'position' => array(
							'edge' => 'top',
							'align' => 'center',
						),
						'pointerClass' => 'vc_gitem-animated-block-pointer-video',
						'pointerWidth' => '530',
					),
				),
			),
		);
	}

	return $pointers;
}

/**
 * @return array|mixed|void
 */
function vc_gitem_content_shortcodes() {
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
	$grid_item = new Vc_Grid_Item();
	$invalid_shortcodes = apply_filters( 'vc_gitem_zone_grid_item_not_content_shortcodes', array(
		'vc_gitem',
		'vc_gitem_animated_block',
		'vc_gitem_zone',
		'vc_gitem_zone_a',
		'vc_gitem_zone_b',
		'vc_gitem_zone_c',
		'vc_gitem_row',
		'vc_gitem_col',
	) );

	return array_diff( array_keys( $grid_item->shortcodes() ), $invalid_shortcodes );
}

/**
 * @param $content
 * @return false|int
 */
function vc_gitem_has_content( $content ) {
	$tags = vc_gitem_content_shortcodes();
	$regexp = vc_get_shortcode_regex( implode( '|', $tags ) );

	return preg_match( '/' . $regexp . '/', $content );
}

/**
 * Add sub page to WPBakery Page Builder pages
 *
 * @since 4.5
 */
function vc_gitem_add_submenu_page() {
	if ( vc_user_access()->part( 'grid_builder' )->can()->get() ) {
		$labels = Vc_Grid_Item_Editor::getPostTypesLabels();
		add_submenu_page( VC_PAGE_MAIN_SLUG, $labels['name'], $labels['name'], 'edit_posts', 'edit.php?post_type=' . rawurlencode( Vc_Grid_Item_Editor::postType() ), '' );
	}
}

/**
 * Highlight Vc submenu.
 * @since 4.5
 */
function vc_gitem_menu_highlight() {
	global $parent_file, $submenu_file, $post_type;
	require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
	if ( Vc_Grid_Item_Editor::postType() === $post_type && defined( 'VC_PAGE_MAIN_SLUG' ) ) {
		$parent_file = VC_PAGE_MAIN_SLUG;
		$submenu_file = 'edit.php?post_type=' . rawurlencode( Vc_Grid_Item_Editor::postType() );
	}

}

add_action( 'admin_head', 'vc_gitem_menu_highlight' );

function vc_gitem_set_mapper_check_access() {
	if ( vc_user_access()->checkAdminNonce()->wpAny( 'edit_posts', 'edit_pages' )->part( 'grid_builder' )->can()->get() && 'true' === vc_post_param( 'vc_grid_item_editor' ) ) {
		vc_mapper()->setCheckForAccess( false );
	}
}

add_action( 'wp_ajax_vc_edit_form', 'vc_gitem_set_mapper_check_access' );
