<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var Vc_Backend_Editor $editor */

// [shortcodes presets data]
if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_all_presets = array();
}
// [/shortcodes presets data]
global $wp_version;
$custom_tag = 'script'; // TODO: Use ajax for variables
$is_gutenberg = version_compare( $wp_version, '4.9.8', '>' ) && ! get_option( 'wpb_js_gutenberg_disable' );
if ( $is_gutenberg ) {
	$is_gutenberg = get_post_type_object( get_post_type() )->show_in_rest;
}
?>
	<<?php echo esc_attr( $custom_tag ); ?>>
		window.vc_all_presets = <?php echo wp_json_encode( $vc_all_presets ); ?>;
		window.vc_post_id = <?php echo esc_js( get_the_ID() ); ?>;
		window.wpbGutenbergEditorUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post-new.php?post_type=wpb_gutenberg_param' ) ) ); ?>';
		window.wpbGutenbergEditorSWitchUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&vcv-gutenberg-editor' ) ) ); ?>';
		window.wpbGutenbergEditorClassicSWitchUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&classic-editor' ) ) ); ?>';
		window.wpbIsGutenberg = <?php echo $is_gutenberg ? 'true' : 'false'; ?>;
	</<?php echo esc_attr( $custom_tag ); ?>>

<?php
require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar.php' );
/** @var WP_Post $post */
$nav_bar = new Vc_Navbar( $post );
$nav_bar->render();
$first_tag = 'style';
?>
	<style>
		#wpb_wpbakery {
			display: none;
		}
	</style>
	<div class="metabox-composer-content">
		<div id="wpbakery_content" class="wpb_main_sortable main_wrapper"></div>
		<?php
		vc_include_template(
			'editors/partials/vc_welcome_block.tpl.php',
			[ 'editor' => 'backend' ]
		);
		?>
	</div>

<?php
$wpb_vc_status = apply_filters( 'wpb_vc_js_status_filter', vc_get_param( 'wpb_vc_js_status', get_post_meta( $post->ID, '_wpb_vc_js_status', true ) ) );

if ( '' === $wpb_vc_status || ! isset( $wpb_vc_status ) ) {
	$wpb_vc_status = vc_user_access()->part( 'backend_editor' )->checkState( 'default' )->get() ? 'true' : 'false';
}

?>
<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="<?php echo esc_attr( $wpb_vc_status ); ?>"/>
<input type="hidden" id="wpb_js_google_fonts_save_nonce" name="wpb_js_google_fonts_save_nonce" value="<?php echo esc_js( wp_create_nonce( 'wpb_js_google_fonts_save' ) ); ?>"/>

<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading"
		value="<?php esc_attr_e( 'Loading, please wait...', 'js_composer' ); ?>"/>
<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row"
		value="<?php esc_attr_e( 'Crunching...', 'js_composer' ); ?>"/>

<?php
vc_include_template(
	'editors/partials/vc_post_custom_meta.tpl.php',
	[ 'editor' => $editor ]
);
?>

<div id="vc_preloader" style="display: none;"></div>
<div id="vc_overlay_spinner" class="vc_ui-wp-spinner vc_ui-wp-spinner-dark vc_ui-wp-spinner-lg" style="display:none;"></div>
<?php
vc_include_template( 'editors/partials/access-manager-js.tpl.php' );
