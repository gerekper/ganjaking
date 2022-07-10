<?php
/*----------------------------------------------------------------------------*\
	MPC_CONTENT Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_content', 'mpc_content_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_content_settings( $settings, $value ) {
	if ( ! isset( $settings[ 'shortcode' ] ) )
		return '';

	$defaults = array(
		'extended' => false,
	);
	$settings = wp_parse_args( $settings, $defaults );

	$base_url = get_option( 'mpc_previews_source' );

	$base_url = $base_url ? $base_url . 'mpc_contents/' : 'https://products.mpcthemes.net/ma/contents/';

	$return = '<a href="#preview_content" class="mpc-preview mpc-vc-button button"><i class="mpc-hover dashicons dashicons-visibility"></i><span class="mpc-regular">' . __( 'Preview & Load Content', 'mpc' ) . '</span></a>';
	$return .= '<select name="' . esc_attr( $settings[ 'param_name' ] ) . '" class="mpc-content-select wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings[ 'param_name' ] ) . '" data-option="' . esc_attr( $value ) . '" data-shortcode="' . esc_attr( $settings[ 'shortcode' ] ) . '" data-selected="' . esc_attr( $value ) . '" data-wp_nonce="' . wp_create_nonce( 'mpc_shortcode_contents_' . $settings[ 'shortcode' ] ) . '" data-extended="' . esc_attr( $settings[ 'extended' ] ) . '" data-placeholder="' . mpc_get_plugin_path( __FILE__ ) . '/assets/images/mpc-image-placeholder.png' . '" data-baseurl="' . $base_url . '">';
		$return .= '<option class="mpc-base-option" value="" ' . selected( '', $value, false ) . '></option>';
	$return .= '</select>';
	$return .= '<a href="#rename_content" class="mpc-rename mpc-vc-button mpc-vc-badge mpc-hidden"><i class="mpc-regular dashicons dashicons-edit"></i></a>';
	$return .= '<a href="#delete_content" class="mpc-delete mpc-vc-button mpc-vc-badge mpc-hidden"><i class="mpc-regular dashicons dashicons-trash"></i></a>';

	$return .= '<div class="mpc-vc-buttons">';
		$return .= '<a href="#new_content" class="mpc-new mpc-vc-button button"><i class="mpc-hover dashicons dashicons-plus-alt"></i><span class="mpc-regular">' . __( 'Create New', 'mpc' ) . '</span></a>';
		$return .= '<a href="#save_content" class="mpc-save mpc-vc-button button mpc-hidden"><i class="mpc-hover dashicons dashicons-upload"></i><span class="mpc-regular">' . __( 'Save Content', 'mpc' ) . '</span></a>';
		$return .= '<a href="#load_content" class="mpc-load mpc-vc-button button mpc-hidden"><i class="mpc-hover dashicons dashicons-download"></i><span class="mpc-regular">' . __( 'Load Content', 'mpc' ) . '</span></a>';
	$return .= '</div>';

	$return .= '<div class="mpc-placement mpc-hidden">';
		$return .= '<h3>' . __( 'Please choose content placement:', 'mpc' ) . '</h3>';
		$return .= '<a href="#prepend" class="mpc-prepend mpc-vc-button button"><i class="mpc-hover dashicons dashicons-undo"></i><span class="mpc-regular">' . __( 'Prepend Content', 'mpc' ) . '</span></a>';
		$return .= '<a href="#replace" class="mpc-replace mpc-vc-button button"><i class="mpc-hover dashicons dashicons-image-rotate"></i><span class="mpc-regular">' . __( 'Replace Content', 'mpc' ) . '</span></a>';
		$return .= '<a href="#append" class="mpc-append mpc-vc-button button"><i class="mpc-hover dashicons dashicons-redo"></i><span class="mpc-regular">' . __( 'Append Content', 'mpc' ) . '</span></a>';
		$return .= '<a href="#close" class="mpc-close mpc-vc-button button"><i class="mpc-hover dashicons dashicons-dismiss"></i><span class="mpc-regular">' . __( 'Cancel', 'mpc' ) . '</span></a>';
	$return .= '</div>';

	$return .= '<div class="mpc-ajax mpc-active"><div><span></span><span></span><span></span></div></div>';
	$return .= '<div class="mpc-init-overlay"></div>';
	$return .= '<div class="mpc-name">';
		$return .= '<label><span class="mpc-label">' . __( 'Content name:', 'mpc' ) . '</span><input class="mpc-content-name" type="text" value="" /></label>';
		$return .= '<a href="#accept" class="mpc-accept mpc-vc-button button">' . __( 'Accept', 'mpc' ) . '</a>';
		$return .= '<a href="#cancel" class="mpc-cancel mpc-vc-button button">' . __( 'Cancel', 'mpc' ) . '</a>';
	$return .= '</div>';
	$return .= '<p class="mpc-error"><i class="dashicons dashicons-dismiss"></i>' . __( 'Something went wrong. Please try again :)', 'mpc' ) . '</p>';
	$return .= '<p class="mpc-warning"><i class="dashicons dashicons-warning"></i>' . __( 'Nothing to save. Every option is set to default value :)', 'mpc' ) . '</p>';

	return $return;
}

/* Add icons grid to menu panel */
add_action( 'admin_footer-post.php', 'mpc_contents_preview_modal' );
add_action( 'admin_footer-post-new.php', 'mpc_contents_preview_modal' );
if ( ! function_exists( 'mpc_contents_preview_modal' ) ) {
	function mpc_contents_preview_modal() { ?>
		<div id="mpc_contents_previews" class="mpc-content-previews mpc-modal-init" data-title="<?php _e( 'Contents previews', 'mpc' ) ?>">
			<h2 class="mpc-contents-section mpc-contents--user"><?php _e( 'User Contents', 'mpc' ); ?></h2>
			<h2 class="mpc-contents-section mpc-contents--premade"><?php _e( 'Premade Contents', 'mpc' ) ?></h2>
		</div>
	<?php }
}

/* JS Localization */
add_action( 'load-post.php', 'mpc_content_localization' );
add_action( 'load-post-new.php', 'mpc_content_localization' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'mpc_content_localization' );
function mpc_content_localization() {
	global $mpc_js_localization;

	$mpc_js_localization[ 'mpc_content' ]                      = array();
	$mpc_js_localization[ 'mpc_content' ][ 'save_confirm' ]    = __( 'Saving content preset: ', 'mpc' );
	$mpc_js_localization[ 'mpc_content' ][ 'delete_confirm' ]  = __( 'Deleting content preset: ', 'mpc' );
	$mpc_js_localization[ 'mpc_content' ][ 'replace_confirm' ] = __( 'Are you sure you want to replace the content?', 'mpc' );
}

/* Get contents list */
add_action( 'wp_ajax_mpc_get_shortcode_contents', 'mpc_get_shortcode_contents' );
function mpc_get_shortcode_contents() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		die();
	}

	$list = get_transient( 'list_' . $_POST[ 'shortcode' ] );

	if ( ! $list ) {
		mpc_sort_content_presets( $_POST[ 'shortcode' ] );

		$list = get_transient( 'list_' . $_POST[ 'shortcode' ] );
	}

	if ( ! $list ) {
		$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ], false );

		if ( $preset_post_id ) {
			$preset_meta = get_post_meta( $preset_post_id );

			$list = array();
			foreach ( $preset_meta as $name => $content ) {
				if ( strpos( $name, '_preset_' ) === false ) {
					continue;
				}

				$content = json_decode( $content[ 0 ], true );

				if ( $content ) {
					$list[ $name ] = array(
						'name' => $content[ 'name' ] ?: '',
					);

					if ( isset( $content[ 'image' ] ) ) {
						$list[ $name ][ 'image' ] = $content[ 'image' ];
					}
				}
			}
		}
	}

	if ( ! is_array( $list ) || empty( $list ) ) {
		die();
	}

	foreach ( $list as $name => $content ) {
		$image = '';
		if ( isset( $content[ 'image' ] ) ) {
			$image = ' data-content-image="' . esc_attr( $content[ 'image' ] ) . '"';
		}

		if ( ! isset( $content[ 'name' ] ) ) {
			$content[ 'name' ] = '';
		}

		echo '<option class="' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '"' . $image . '>' . $content[ 'name' ] . '</option>';
	}

	die();
}

/* New content */
add_action( 'wp_ajax_mpc_new_shortcode_content', 'mpc_new_shortcode_content' );
function mpc_new_shortcode_content() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'content' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		wp_send_json_error();
	}

	$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ], false );

	if ( ! $preset_post_id ) {
		$preset_post_id = wp_insert_post( array(
			'post_title'     => $_POST[ 'shortcode' ],
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'mpc_content_preset',
			'post_mime_type' => 'mpc-content-preset/' . str_replace( '_', '-', $_POST[ 'shortcode' ] ),
			'meta_input'     => array(
				'_mpc_presets_index' => 0,
			),
		) );

		delete_post_meta( $preset_post_id, '_vc_post_settings' );
	}

	if ( ! $preset_post_id ) {
		wp_send_json_error();
	}

	$preset_index = get_post_meta( $preset_post_id, '_mpc_presets_index', true );

	if ( $preset_index === '' ) {
		$preset_index = count( get_post_meta( $preset_post_id ) );
	}

	$preset_id = '_' . ( defined( 'MPC_MASSIVE_PRESETS' ) ? MPC_MASSIVE_PRESETS : '' ) . 'preset_' . $preset_index;

	$preset = array(
		'name'    => wp_unslash( $_POST[ 'name' ] ),
		'content' => wp_unslash( $_POST[ 'content' ] ),
	);

	$preset = json_encode( $preset );

	$new_preset_id = add_post_meta( $preset_post_id, $preset_id, wp_slash( $preset ), true );

	if ( $new_preset_id ) {
		$response = array(
			'success' => true,
			'id'      => $preset_id,
			'markup'  => '<option class="' . $preset_id . '" value="' . $preset_id . '">' . wp_unslash( $_POST[ 'name' ] ) . '</option>',
		);

		update_post_meta( $preset_post_id, '_mpc_presets_index', ++$preset_index );

		mpc_sort_content_presets( $_POST[ 'shortcode' ] );

		wp_send_json( $response );
	} else {
		wp_send_json_error();
	}
}

/* Load preset */
add_action( 'wp_ajax_mpc_load_shortcode_content', 'mpc_load_shortcode_content' );
function mpc_load_shortcode_content() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		wp_send_json_error();
	}

	$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ] );

	$content = get_post_meta( $preset_post_id, $_POST[ 'id' ], true );

	$content = json_decode( $content, true );

	if ( ! empty( $content[ 'content' ] ) ) {
		wp_send_json( array(
			'success' => true,
			'content' => $content[ 'content' ]
		) );
	} else {
		wp_send_json_error();
	}
}

/* Save preset */
add_action( 'wp_ajax_mpc_save_shortcode_content', 'mpc_save_shortcode_content' );
function mpc_save_shortcode_content() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'content' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		wp_send_json_error();
	}

	$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ], false );

	if ( ! $preset_post_id ) {
		$_POST[ 'name' ] = '';

		mpc_new_shortcode_content();
	}

	$content = get_post_meta( $preset_post_id, $_POST[ 'id' ], true );

	$content = json_decode( $content, true );

	if ( ! is_array( $content ) ) {
		$content = array();
	}

	if ( ! isset( $content[ 'name' ] ) ) {
		$content[ 'name' ] = '';
	}

	$compare_content = $content[ 'content' ];

	$content[ 'content' ] = wp_unslash( $_POST[ 'content' ] );

	$content = json_encode( $content );

	$saved = update_post_meta( $preset_post_id, $_POST[ 'id' ], wp_slash( $content ) );

	if ( $saved || $compare_content == $content[ 'content' ] ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}

/* Rename preset */
add_action( 'wp_ajax_mpc_rename_shortcode_content', 'mpc_rename_shortcode_content' );
function mpc_rename_shortcode_content() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		wp_send_json_error();
	}

	$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ] );

	$content = get_post_meta( $preset_post_id, $_POST[ 'id' ], true );

	$content = json_decode( $content, true );

	if ( ! isset( $content[ 'name' ] ) ) {
		wp_send_json_error();
	} else {
		$content[ 'name' ] = wp_unslash( $_POST[ 'name' ] );

		$content = json_encode( $content );

		update_post_meta( $preset_post_id, $_POST[ 'id' ], wp_slash( $content ) );

		mpc_sort_content_presets( $_POST[ 'shortcode' ] );

		wp_send_json_success();
	}
}

/* Delete preset */
add_action( 'wp_ajax_mpc_delete_shortcode_content', 'mpc_delete_shortcode_content' );
function mpc_delete_shortcode_content() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_contents_' . $_POST[ 'shortcode' ] ) ) {
		wp_send_json_error();
	}

	$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ] );

	$deleted = delete_post_meta( $preset_post_id, $_POST[ 'id' ] );

	mpc_sort_content_presets( $_POST[ 'shortcode' ] );

	if ( $deleted ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}