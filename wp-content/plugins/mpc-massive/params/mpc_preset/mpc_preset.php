<?php
/*----------------------------------------------------------------------------*\
	MPC_PRESET Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_preset', 'mpc_preset_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_preset_settings( $settings, $value ) {
	if ( ! isset( $settings[ 'shortcode' ] ) )
		return '';

	$defaults = array(
		'sub_type'   => '',
		'wide_modal' => false,
	);
	$settings = wp_parse_args( $settings, $defaults );

	$base_url = get_option( 'mpc_previews_source' );
	$base_url = $base_url ? $base_url . 'mpc_presets/' : 'https://products.mpcthemes.net/ma/presets/';

	$return = '<a href="#preview_preset" class="mpc-preview mpc-vc-button button"><i class="mpc-hover dashicons dashicons-visibility"></i><span class="mpc-regular">' . __( 'Preview & Load Preset', 'mpc' ) . '</span></a>';
	$return .= '<select name="' . esc_attr( $settings[ 'param_name' ] ) . '" class="mpc-preset-select wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings[ 'param_name' ] ) . '" data-option="' . esc_attr( $value ) . '" data-shortcode="' . esc_attr( $settings[ 'shortcode' ] ) . '" data-sub_type="' . esc_attr( $settings[ 'sub_type' ] ) . '" data-selected="' . esc_attr( $value ) . '" data-wp_nonce="' . wp_create_nonce( 'mpc_shortcode_presets_' . $settings[ 'shortcode' ] ) . '" data-wide-modal="' . esc_attr( $settings[ 'wide_modal' ] ) . '" data-placeholder="' . mpc_get_plugin_path( __FILE__ ) . '/assets/images/mpc-image-placeholder.png' . '" data-baseurl="' . $base_url . '">';
		$return .= '<option class="mpc-base-option" value="" ' . selected( '', $value, false ) . '></option>';
	$return .= '</select>';
	$return .= '<a href="#rename_preset" class="mpc-rename mpc-vc-button mpc-vc-badge mpc-hidden"><i class="mpc-regular dashicons dashicons-edit"></i></a>';
	$return .= '<a href="#delete_preset" class="mpc-delete mpc-vc-button mpc-vc-badge mpc-hidden"><i class="mpc-regular dashicons dashicons-trash"></i></a>';

	$return .= '<div class="mpc-vc-buttons">';
		$return .= '<a href="#new_preset" class="mpc-new mpc-vc-button button"><i class="mpc-hover dashicons dashicons-plus-alt"></i><span class="mpc-regular">' . __( 'Create New', 'mpc' ) . '</span></a>';
		$return .= '<a href="#save_preset" class="mpc-save mpc-vc-button button mpc-hidden"><i class="mpc-hover dashicons dashicons-upload"></i><span class="mpc-regular">' . __( 'Save Changes', 'mpc' ) . '</span></a>';
		$return .= '<a href="#load_preset" class="mpc-load mpc-vc-button button mpc-hidden"><i class="mpc-hover dashicons dashicons-download"></i><span class="mpc-regular">' . __( 'Reload', 'mpc' ) . '</span></a>';
		$return .= '<a href="#clear_preset" class="mpc-clear mpc-vc-button button"><i class="mpc-hover dashicons dashicons-editor-removeformatting"></i><span class="mpc-regular">' . __( 'Clear', 'mpc' ) . '</span></a>';
	$return .= '</div>';

	$return .= '<div class="mpc-ajax mpc-active"><div><span></span><span></span><span></span></div></div>';
	$return .= '<div class="mpc-init-overlay"></div>';
	$return .= '<div class="mpc-name">';
		$return .= '<label><span class="mpc-label">' . __( 'Preset name:', 'mpc' ) . '</span><input class="mpc-preset-name" type="text" value="" /></label>';
		$return .= '<a href="#accept" class="mpc-accept mpc-vc-button button">' . __( 'Accept', 'mpc' ) . '</a>';
		$return .= '<a href="#cancel" class="mpc-cancel mpc-vc-button button">' . __( 'Cancel', 'mpc' ) . '</a>';
	$return .= '</div>';
	$return .= '<input class="mpc-preset-dynamic-name" type="hidden" value="" />';
	$return .= '<p class="mpc-error"><i class="dashicons dashicons-dismiss"></i>' . __( 'Something went wrong. Please try again :)', 'mpc' ) . '</p>';
	$return .= '<p class="mpc-warning"><i class="dashicons dashicons-warning"></i>' . __( 'Nothing to save. Every option is set to default value :)', 'mpc' ) . '</p>';

	return $return;
}

/* Add icons grid to menu panel */
add_action( 'admin_footer-post.php', 'mpc_presets_preview_modal' );
add_action( 'admin_footer-post-new.php', 'mpc_presets_preview_modal' );
if ( ! function_exists( 'mpc_presets_preview_modal' ) ) {
	function mpc_presets_preview_modal() { ?>
		<div id="mpc_presets_previews" class="mpc-preset-previews mpc-modal-init" data-title="<?php _e( 'Presets previews', 'mpc' ) ?>">
			<h2 class="mpc-presets-section mpc-presets--user"><?php _e( 'User Presets', 'mpc' ); ?></h2>
			<h2 class="mpc-presets-section mpc-presets--premade"><?php _e( 'Premade Presets', 'mpc' ) ?></h2>
		</div>
	<?php }
}

/* JS Localization */
add_action( 'load-post.php', 'mpc_preset_localization' );
add_action( 'load-post-new.php', 'mpc_preset_localization' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'mpc_preset_localization' );
function mpc_preset_localization() {
	global $mpc_js_localization;

	$mpc_js_localization[ 'mpc_preset' ]                                  = array();
	$mpc_js_localization[ 'mpc_preset' ][ 'save_confirm' ]                = __( 'Saving values for preset: ', 'mpc' );
	$mpc_js_localization[ 'mpc_preset' ][ 'delete_confirm' ]              = __( 'Deleting preset: ', 'mpc' );
	$mpc_js_localization[ 'mpc_preset' ][ 'clear_confirm' ]               = __( 'Clearing all shortcode fields.', 'mpc' );
	$mpc_js_localization[ 'mpc_preset' ][ 'save_navigation_preset' ]      = __( 'Save Navigation Preset', 'mpc' );
	$mpc_js_localization[ 'mpc_preset' ][ 'save_pagination_preset' ]      = __( 'Save Pagination Preset', 'mpc' );
}

/* Get presets list */
add_action( 'wp_ajax_mpc_get_shortcode_presets', 'mpc_get_shortcode_presets' );
function mpc_get_shortcode_presets() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die();
	}

	$presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	if ( $presets === false ) {
		$presets = array();
	} else {
		$presets = json_decode( $presets, true );
	}

	if ( $presets !== false ) {
		foreach ( $presets as $name => $preset ) {
			if ( $name == '__index' ) {
				continue;
			}

			$image = '';
			if ( isset( $preset[ '__image' ] ) ) {
				$image = ' data-preset-image="' . esc_attr( $preset[ '__image' ] ) . '"';
			}

			if ( ! isset( $preset[ '__name' ] ) ) {
				$preset[ '__name' ] = '';
			}

			echo '<option class="' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '"' . $image . '>' . $preset[ '__name' ] . '</option>';
		}
	}

	die();
}

/* Preset fields */
add_action( 'wp_ajax_mpc_get_shortcode_fields', 'mpc_get_shortcode_fields' );
function mpc_get_shortcode_fields() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( '{"error":"not set"}' );
	}

	$fields = vc_map_get_attributes( $_POST[ 'shortcode' ], array() );

	echo json_encode( array_keys( $fields ) );

	die();
}

/* New preset */
add_action( 'wp_ajax_mpc_new_shortcode_preset', 'mpc_new_shortcode_preset' );
function mpc_new_shortcode_preset() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'values' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( '{"error":"not set"}' );
	}

	$values = array_map( 'stripslashes_deep', $_POST[ 'values' ] );

	$shortcode_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	if ( $shortcode_presets === false ) {
		$shortcode_presets = array(
			'__index' => 0,
		);
	} else {
		$shortcode_presets = json_decode( $shortcode_presets, true );
	}

	$display_name = array( '__name' => $_POST[ 'name' ] );
	$values = $display_name + $values;

	$preset_name = ( defined( 'MPC_MASSIVE_PRESETS' ) ? MPC_MASSIVE_PRESETS : '' ) . 'preset_' . $shortcode_presets[ '__index' ];

	$shortcode_presets[ '__index' ] += 1;

	$shortcode_presets[ $preset_name ] = $values;

	uasort( $shortcode_presets, 'mpc_sort_presets' );

	$shortcode_presets = mpc_after_sort_presets( $shortcode_presets );

	$shortcode_presets = json_encode( $shortcode_presets );

	if ( $shortcode_presets !== false ) {
		if ( ! add_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets, '', 'no' ) ) {
			update_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets );
		}
	} else {
		die( '{"error":"not set"}' );
	}

	$response = array(
		'id' => $preset_name,
		'markup' => '<option class="' . $preset_name . '" value="' . $preset_name . '">' .$_POST[ 'name' ] . '</option>',
	);

	echo json_encode( $response );

	die();
}

/* Load preset */
add_action( 'wp_ajax_mpc_load_shortcode_preset', 'mpc_load_shortcode_preset' );
function mpc_load_shortcode_preset() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( '{"error":"not set"}' );
	}

	$shortcode_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	$shortcode_presets = json_decode( $shortcode_presets, true );

	if ( $_POST[ 'shortcode' ] == 'mpc_icon_list' && isset( $shortcode_presets[ $_POST[ 'id' ] ][ 'list' ] ) ) {
		$shortcode_presets[ $_POST[ 'id' ] ][ 'list' ] = json_decode( urldecode( $shortcode_presets[ $_POST[ 'id' ] ][ 'list' ] ), true );
	}

	if ( empty( $shortcode_presets ) || empty( $shortcode_presets[ $_POST[ 'id' ] ] ) ) {
		echo '{"error":"not set"}';
	} else {
		echo json_encode( $shortcode_presets[ $_POST[ 'id' ] ] );
	}

	die();
}

/* Save preset */
add_action( 'wp_ajax_mpc_save_shortcode_preset', 'mpc_save_shortcode_preset' );
function mpc_save_shortcode_preset() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'values' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( 'not set' );
	}

	$values = array_map( 'stripslashes_deep', $_POST[ 'values' ] );

	$shortcode_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	$shortcode_presets = json_decode( $shortcode_presets, true );

	if ( empty( $shortcode_presets ) ) {
		$shortcode_presets = array(
			'__index' => 0,
		);
	}

	$base = array(
		'__name' => isset( $shortcode_presets[ $_POST[ 'id' ] ][ '__name' ] ) ? $shortcode_presets[ $_POST[ 'id' ] ][ '__name' ] : '',
	);

	if ( ! empty( $_POST[ 'name' ] ) ) {
		$base[ '__name' ] = wp_unslash( $_POST[ 'name' ] );
	}

	if ( isset( $shortcode_presets[ $_POST[ 'id' ] ][ '__image' ] ) ) {
		$base[ '__image' ] = $shortcode_presets[ $_POST[ 'id' ] ][ '__image' ];
	}

	$shortcode_presets[ $_POST[ 'id' ] ] = $base + $values;

	uasort( $shortcode_presets, 'mpc_sort_presets' );

	$shortcode_presets = mpc_after_sort_presets( $shortcode_presets );

	$shortcode_presets = json_encode( $shortcode_presets );

	if ( $shortcode_presets !== false ) {
		if ( ! add_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets, '', 'no' ) ) {
			update_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets );
		}
	} else {
		die( 'not set' );
	}

	die();
}

/* Rename preset */
add_action( 'wp_ajax_mpc_rename_shortcode_preset', 'mpc_rename_shortcode_preset' );
function mpc_rename_shortcode_preset() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( 'not set' );
	}

	$shortcode_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	$shortcode_presets = json_decode( $shortcode_presets, true );

	if ( empty( $shortcode_presets ) ) {
		die( 'not set' );
	}

	if ( isset( $shortcode_presets[ $_POST[ 'id' ] ] ) ) {
		$shortcode_presets[ $_POST[ 'id' ] ][ '__name' ] = $_POST[ 'name' ];

		uasort( $shortcode_presets, 'mpc_sort_presets' );

		$shortcode_presets = mpc_after_sort_presets( $shortcode_presets );

		$shortcode_presets = json_encode( $shortcode_presets );

		if ( $shortcode_presets !== false ) {
			update_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets );
		} else {
			die( 'not set' );
		}
	} else {
		die( 'not set' );
	}

	die();
}

/* Delete preset */
add_action( 'wp_ajax_mpc_delete_shortcode_preset', 'mpc_delete_shortcode_preset' );
function mpc_delete_shortcode_preset() {
	if ( ! isset( $_POST[ 'shortcode' ] ) ||
		 ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_shortcode_presets_' . $_POST[ 'shortcode' ] ) ) {
		die( 'not set' );
	}

	$shortcode_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
	$shortcode_presets = json_decode( $shortcode_presets, true );

	if ( empty( $shortcode_presets ) ) {
		die( 'not set' );
	}

	if ( isset( $shortcode_presets[ $_POST[ 'id' ] ] ) ) {
		unset( $shortcode_presets[ $_POST[ 'id' ] ] );

		$shortcode_presets = json_encode( $shortcode_presets );

		if ( $shortcode_presets !== false ) {
			update_option( 'mpc_presets_' . $_POST[ 'shortcode' ], $shortcode_presets );
		} else {
			die( 'not set' );
		}
	} else {
		die( 'not set' );
	}

	die();
}

/* Helpers */
add_action( 'wp_ajax_mpc_get_image_url', 'mpc_get_image_url' );
function mpc_get_image_url() {
	if ( ! isset( $_POST[ 'id' ] ) || ! is_numeric( $_POST[ 'id' ] ) ) {
		die( 'not set' );
	}

	echo fieldAttachedImages( array( $_POST[ 'id' ] ) );

	die();
}