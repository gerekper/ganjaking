<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

global $groovyMenuSettings, $groovyMenuPreview;

$groovyMenuPreview = true;

$preset_id     = isset( $_GET['id'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['id'] ) ) ) : false; // @codingStandardsIgnoreLine
$navmenu_id    = isset( $_GET['navmenu_id'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['navmenu_id'] ) ) ) : false; // @codingStandardsIgnoreLine
$from_action   = isset( $_GET['from'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['from'] ) ) ) : null; // @codingStandardsIgnoreLine
$rtl_flag      = isset( $_GET['d'] ) ? true : false; // @codingStandardsIgnoreLine
$preset_params = empty( $_POST['menu'] ) ? array() : $_POST['menu']; // @codingStandardsIgnoreLine
$styles        = new GroovyMenuStyle( $preset_id );
$settings      = new GroovyMenuSettings();

GroovyMenu\StyleStorage::getInstance()->set_disable_storage();

// Save preview image.
if ( isset( $_POST ) && isset( $_POST['image'] ) && ! empty( $_GET['screen'] ) ) {
	$settings->savePreviewImage();
}

if ( 'api' === $from_action ) {

	$data = $settings->getPresetDataFromApiById( $preset_id );
	if ( ! empty( $data['settings'] ) ) {
		foreach ( $data['settings'] as $key => $val ) {
			$styles->set( $key, $val );
		}
	}

} elseif ( 'edit' === $from_action ) {

	if ( ! empty( $preset_params ) && is_array( $preset_params ) ) {
		foreach ( $preset_params as $group ) {
			foreach ( $group as $key => $val ) {
				$styles->set( $key, $val );
			}
		}
	}

}

if ( empty( $navmenu_id ) || ! $navmenu_id || 'default' === $navmenu_id ) {
	$navmenu_id = GroovyMenuUtils::getDefaultMenu();
}


$serialized_styles                          = $styles->serialize( false, true, true, false );
$groovyMenuSettings                         = $serialized_styles;
$groovyMenuSettings['preset']               = array(
	'id'   => $styles->getPreset()->getId(),
	'name' => $styles->getPreset()->getName(),
);
$groovyMenuSettings['extra_navbar_classes'] = $styles->getHtmlClasses();

$custom_css          = trim( stripslashes( $styles->get( 'general', 'css' ) ) );
$custom_js           = trim( stripslashes( $styles->get( 'general', 'js' ) ) );
$output_custom_media = '';

if ( $custom_css ) {
	$tag_name             = 'style';
	$output_custom_media .= "\n" . '<' . esc_attr( $tag_name ) . '>' . $custom_css . '</' . esc_attr( $tag_name ) . '>';
}
if ( $custom_js ) {
	$tag_name             = 'script';
	$output_custom_media .= "\n" . '<' . esc_attr( $tag_name ) . '>' . $custom_js . '</' . esc_attr( $tag_name ) . '>';
}

$header_style = intval( $groovyMenuSettings['header']['style'] );

if ( class_exists( 'GroovyMenuActions' ) ) {
	// Do custom shortcodes from preset.
	GroovyMenuActions::do_preset_shortcodes( $styles );

	if ( $groovyMenuSettings['toolbarMenuEnable'] ) {
		// Do custom shortcodes from preset.
		GroovyMenuActions::check_toolbar_menu( $styles );
	}

	if ( in_array( $header_style, [ 1, 2 ], true ) ) {
		// Do custom shortcodes from preset.
		GroovyMenuActions::check_menu_block_for_actions( $styles );
	}
}


// Disable admin bar.
add_filter( 'show_admin_bar', '__return_false' );
remove_action( 'wp_head', '_admin_bar_bump_cb' );

$style_name = 'preview' . ( $rtl_flag ? '-rtl' : '' ) . '.css';
wp_enqueue_style( 'groovy-preview-style', GROOVY_MENU_URL . 'assets/style/' . $style_name, [], 'v' . time() );

wp_enqueue_script( 'groovy-js-preview', GROOVY_MENU_URL . 'assets/js/preview.js', [], GROOVY_MENU_VERSION, true );


?><html <?php echo $rtl_flag ? 'dir="rtl"' : ''; ?>>
<head>
	<?php


	wp_head();


	/**
	 * Fires in <head> the groovy menu preview output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_preview_head_output' );


	?>
</head>
<body class="bg--transparent gm-preview-body" data-color="transparent">
<div class="gm-preload">
	<div class="ball-spin-fade-loader">
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>
</div>

<div class="gm-preview">
	<?php

	/**
	 * Fires before the groovy menu preview output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_before_preview' );


	echo $output_custom_media ? : '';

	$args = array(
		'menu'           => $navmenu_id,
		'gm_preset_id'   => $preset_id,
		'theme_location' => GroovyMenuUtils::getMasterLocation(),
		'menu_class'     => 'nav-menu',
		'gm_echo'        => true,
	);

	groovyMenu( $args );

	/**
	 * Fires after the groovy menu preview output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_after_preview' );

	?>
</div>


<?php


wp_footer();


/**
 * Fires in footer the groovy menu preview output.
 *
 * @since 1.2.20
 */
do_action( 'gm_preview_footer_output' );


?>

</body>

</html>
