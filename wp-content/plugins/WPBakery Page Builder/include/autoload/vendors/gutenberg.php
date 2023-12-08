<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @param $post
 * @return bool
 */
function vcv_disable_gutenberg_for_classic_editor( $post ) {
	return false;
}

/**
 * @param \Vc_Settings $settings
 */
function vc_gutenberg_add_settings( $settings ) {
	global $wp_version;
	if ( function_exists( 'the_gutenberg_project' ) || version_compare( $wp_version, '4.9.8', '>' ) ) {
		$settings->addField( 'general', esc_html__( 'Disable Gutenberg Editor', 'js_composer' ), 'gutenberg_disable', 'vc_gutenberg_sanitize_disable_callback', 'vc_gutenberg_disable_render_callback' );
	}
}

/**
 * @param $rules
 *
 * @return mixed
 */
function vc_gutenberg_sanitize_disable_callback( $rules ) {
	return (bool) $rules;
}

/**
 * Not responsive checkbox callback function
 */
function vc_gutenberg_disable_render_callback() {
    // phpcs:ignore
	$checked = ( $checked = get_option( 'wpb_js_gutenberg_disable' ) ) ? $checked : false;
	?>
	<label>
		<input type="checkbox"<?php echo esc_attr( $checked ) ? ' checked' : ''; ?> value="1"
			name="<?php echo 'wpb_js_gutenberg_disable' ?>">
		<?php esc_html_e( 'Disable', 'js_composer' ) ?>
	</label><br/>
	<p
			class="description indicator-hint"><?php esc_html_e( 'Disable Gutenberg Editor.', 'js_composer' ); ?></p>
	<?php
}

/**
 * @param $result
 * @param $postType
 * @return bool
 */
function vc_gutenberg_check_disabled( $result, $postType ) {
	global $pagenow;
	if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
		// we are in single post type editing
		if ( isset( $_GET['classic-editor'] ) && ! isset( $_GET['classic-editor__forget'] ) ) {
			return false;
		}
		if ( isset( $_GET['classic-editor__forget'] ) ) {
			return true;
		}
		if ( 'wpb_gutenberg_param' === $postType ) {
			return true;
		}
		if ( ! isset( $_GET['vcv-gutenberg-editor'] ) && ( get_option( 'wpb_js_gutenberg_disable' ) || vc_is_wpb_content() || isset( $_GET['classic-editor'] ) ) ) {
			return false;
		}
	}

	return $result;
}

/**
 * @param $result
 * @param $postType
 * @return bool
 */
function vc_gutenberg_check_disabled_regular( $editors, $postType ) {
	if ( 'wpb_gutenberg_param' === $postType ) {
		$editors['gutenberg_editor'] = false;
	}
	if ( ! isset( $_GET['vcv-gutenberg-editor'] ) && ( get_option( 'wpb_js_gutenberg_disable' ) || vc_is_wpb_content() || isset( $_GET['classic-editor'] ) ) ) {
		$editors['gutenberg_editor'] = false;
		$editors['classic_editor'] = false;
	}

	return $editors;
}

function vc_classic_editor_post_states( $state ) {
	if ( vc_is_wpb_content() ) {
		unset( $state['classic-editor-plugin'] );
	}

	return $state;
}

/**
 * @return bool
 */
function vc_is_wpb_content() {
	$post = get_post();
	if ( ! empty( $post ) && isset( $post->post_content ) && preg_match( '/\[vc_row/', $post->post_content ) ) {
		return true;
	}

	return false;
}

function vc_gutenberg_map() {
	global $wp_version;
	if ( function_exists( 'the_gutenberg_project' ) || version_compare( $wp_version, '4.9.8', '>' ) ) {
		vc_lean_map( 'vc_gutenberg', null, dirname( __FILE__ ) . '/shortcode-vc-gutenberg.php' );
	}
}

add_filter( 'classic_editor_enabled_editors_for_post', 'vc_gutenberg_check_disabled_regular', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'vc_gutenberg_check_disabled', 10, 2 );
add_filter( 'display_post_states', 'vc_classic_editor_post_states', 11, 2 );
add_action( 'vc_settings_tab-general', 'vc_gutenberg_add_settings' );
add_action( 'init', 'vc_gutenberg_map' );

/** @see include/params/gutenberg/class-vc-gutenberg-param.php */
require_once vc_path_dir( 'PARAMS_DIR', 'gutenberg/class-vc-gutenberg-param.php' );
new Vc_Gutenberg_Param();
