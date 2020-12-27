<?php
/*----------------------------------------------------------------------------*\
	MPC_TYPOGRAPHY Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_typography', 'mpc_typography_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_typography_settings( $settings, $value ) {
	$return = '<select name="' . esc_attr( $settings[ 'param_name' ] ) . '" class="mpc-typography-select wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings[ 'param_name' ] ) . '" data-option="' . esc_attr( $value ) . '" data-selected="' . esc_attr( $value ) . '" data-wp_nonce="' . wp_create_nonce( 'mpc_typography_presets' ) . '">';
		$return .= '<option value="" ' . selected( '', $value, false ) . '></option>';
	$return .= '</select>';

	$return .= '<a href="#edit_preset" class="mpc-edit mpc-vc-button mpc-vc-badge mpc-hidden"><i class="dashicons dashicons-edit"></i></a>';
	$return .= '<a href="#delete_preset" class="mpc-delete mpc-vc-button mpc-vc-badge mpc-hidden"><i class="dashicons dashicons-trash"></i></a>';
	$return .= '<a href="#new_preset" class="mpc-new mpc-vc-button button"><i class="mpc-hover dashicons dashicons-plus-alt"></i><span class="mpc-regular">' . __( 'New', 'mpc' ) . '</span></a>';
	$return .= '<div class="mpc-buttons mpc-dynamic-buttons mpc-hidden">';
		$return .= '<a href="#accept" class="mpc-accept mpc-vc-button button">';
			$return .= '<span class="mpc-save">' . __( 'Save', 'mpc' ) . '</span>';
			$return .= '<span class="mpc-create">' . __( 'Create', 'mpc' ) . '</span>';
		$return .= '</a>';
		$return .= '<a href="#cancel" class="mpc-cancel mpc-vc-button button">' . __( 'Cancel', 'mpc' ) . '</a>';
	$return .= '</div>';
	$return .= '<div class="mpc-ajax mpc-active"><div><span></span><span></span><span></span></div></div>';
	$return .= '<div class="mpc-init-overlay"></div>';
	$return .= '<div class="mpc-typography"></div>';
	$return .= '<p class="mpc-error"><i class="dashicons dashicons-dismiss"></i>' . __( 'Something went wrong. Please try again :)', 'mpc' ) . '</p>';

	return $return;
}

add_action( 'load-post.php', 'mpc_typography_localization' );
add_action( 'load-post-new.php', 'mpc_typography_localization' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'mpc_typography_localization' );
function mpc_typography_localization() {
	global $mpc_js_localization;

	$mpc_js_localization[ 'mpc_typography' ]                     = array();
	$mpc_js_localization[ 'mpc_typography' ][ 'save_confirm' ]   = __( 'Saving values for preset: ', 'mpc' );
	$mpc_js_localization[ 'mpc_typography' ][ 'delete_confirm' ] = __( 'Deleting preset: ', 'mpc' );
}

/* Get typography form */
add_action( 'wp_ajax_mpc_get_typography_form', 'mpc_get_typography_form' );
function mpc_get_typography_form() {
	if ( ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( 'not set' );
	}
	global $mpc_ma_options;

	$disable_google_fonts = ( isset( $mpc_ma_options[ 'disable_google_fonts' ] ) && $mpc_ma_options[ 'disable_google_fonts' ] ) ? true : false;
	?>

	<div id="mpc_typography_form" class="mpc-typography-form">
		<div class="mpc-form-element mpc-full">
			<span class="mpc-description"><?php _e( 'Preset Name', 'mpc' ); ?></span>
			<input class="mpc-typography-name" name="preset-name" type="text" value="">
		</div>

		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Font Family', 'mpc' ); ?></span>
			<input class="mpc-typography-value" name="font-family" type="text" value="">
		</div>
		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Font Weight & Style', 'mpc' ); ?></span>
			<select class="mpc-typography-value" name="style">
				<option value=""></option>
			</select>
		</div>
		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Font Subset', 'mpc' ); ?></span>
			<select class="mpc-typography-value" name="subset">
				<option value=""></option>
			</select>
		</div>
		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Text Align', 'mpc' ); ?></span>
			<select class="mpc-typography-value" name="text-align">
				<option value=""></option>
				<option value="inherit"><?php _e( 'Inherit', 'mpc' ); ?></option>
				<option value="left"><?php _e( 'Left', 'mpc' ); ?></option>
				<option value="right"><?php _e( 'Right', 'mpc' ); ?></option>
				<option value="center"><?php _e( 'Center', 'mpc' ); ?></option>
				<option value="justify"><?php _e( 'Justify', 'mpc' ); ?></option>
				<option value="initial"><?php _e( 'Initial', 'mpc' ); ?></option>
			</select>
		</div>
		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Text Transform', 'mpc' ); ?></span>
			<select class="mpc-typography-value" name="text-transform">
				<option value=""></option>
				<option value="inherit"><?php _e( 'Inherit', 'mpc' ); ?></option>
				<option value="lowercase"><?php _e( 'Lowercase', 'mpc' ); ?></option>
				<option value="uppercase"><?php _e( 'Uppercase', 'mpc' ); ?></option>
				<option value="capitalize"><?php _e( 'Capitalize', 'mpc' ); ?></option>
				<option value="none"><?php _e( 'None', 'mpc' ); ?></option>
				<option value="initial"><?php _e( 'Initial', 'mpc' ); ?></option>
			</select>
		</div>

		<div class="mpc-form-element mpc-half">
			<span class="mpc-description"><?php _e( 'Font Color', 'mpc' ); ?></span>
			<input class="mpc-typography-value mpc-color-picker" name="color" type="text" value="">
		</div>
		<div class="mpc-form-element mpc-one-fourth mpc-with-unit mpc-clear--both">
			<span class="mpc-description"><?php _e( 'Font Size', 'mpc' ); ?></span>
			<input class="mpc-typography-value" name="font-size" type="text" value="">
			<span class="mpc-unit">px</span>
		</div>
		<div class="mpc-form-element mpc-one-fourth mpc-with-unit">
			<span class="mpc-description"><?php _e( 'Line Height', 'mpc' ); ?></span>
			<input class="mpc-typography-value" name="line-height" type="text" value="">
			<span class="mpc-unit">em</span>
		</div>

		<input class="mpc-typography-value" name="font-weight" type="hidden" value="400" placeholder="font-weight">
		<input class="mpc-typography-value" name="font-style" type="hidden" value="normal" placeholder="font-style">
		<input class="mpc-typography-value" name="url" type="hidden" value="">

		<div class="mpc-preview-wrap">
			<div class="mpc-preview">
				<textarea><?php _e( 'The quick brown fox jumps over the lazy dog.', 'mpc' ); ?>&#13;&#10;1 2 3 4 5 6 7 8 9 0 ? ! . , ( ) [ ] @ # % ^ & * ; : ' "</textarea>
			</div>
		</div>

		<div class="mpc-buttons">
			<a href="#accept" class="mpc-accept mpc-vc-button button">
				<span class="mpc-save"><?php _e( 'Save', 'mpc' ); ?></span>
				<span class="mpc-create"><?php _e( 'Create', 'mpc' ); ?></span>
			</a>
			<a href="#cancel" class="mpc-cancel mpc-vc-button button"><?php _e( 'Cancel', 'mpc' ); ?></a>
		</div>
	</div>

	<script>
		var _mpc_fonts = <?php ( $disable_google_fonts ) ? require_once( mpc_get_webfonts() ) : require_once( mpc_get_google_webfonts() ); ?>;
	</script>
	<?php

	die();
}

/* Get Google webfonts */
function mpc_get_google_webfonts() {

	if ( ! isset( $_POST[ 'wp_nonce' ] ) ||
	     ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' )
	) {
		return trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts_static.json';
	}

	$webfonts_file = trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts.json';
	$download_list = true;

	if ( file_exists( $webfonts_file ) ) {
		$file_time = filemtime( $webfonts_file );

		if ( time() - $file_time < DAY_IN_SECONDS ) {
			$download_list = false;
		}
	}

	if ( $download_list ) {
		$request = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAFJ3AraiITu49dSxFu9YA4WPOsQr6FFb8', array( 'sslverify' => false ) );

		if ( ! is_wp_error( $request ) && $request[ 'response' ][ 'code' ] == 200 ) {
			$fonts = json_decode( $request[ 'body' ] );

			$webfonts_file = mpc_format_google_webfonts( $fonts );
		} else {
			$webfonts_file = trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts_static.json';
		}
	}

	return $webfonts_file;
}

/* Format Google webfonts */
function mpc_format_google_webfonts( $fonts ) {
	$fonts     = $fonts->items;
	$formatted = array();

	foreach( $fonts as $font ) {
		sort( $font->subsets );

		$formatted[] = array(
			'id'       => $font->family,
			'text'     => $font->family,
			'variants' => $font->variants,
			'subsets'  => $font->subsets,
		);
	}

	$all_fonts = array();

	$all_fonts[] = apply_filters( 'ma_custom_fonts', array() );

	$all_fonts[] = array(
		'text'     => __( 'Standard Fonts', 'mpc' ),
		'children' => array(
			array(
				'id'       => "Arial, Helvetica, sans-serif",
				'text'     => "Arial, Helvetica, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Arial Black', Gadget, sans-serif",
				'text'     => "'Arial Black', Gadget, sans-serif",
				'variants' => array( 'regular', 'italic' ),
			),
			array(
				'id'       => "'Bookman Old Style', serif",
				'text'     => "'Bookman Old Style', serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Comic Sans MS', cursive",
				'text'     => "'Comic Sans MS', cursive",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Courier, monospace",
				'text'     => "Courier, monospace",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Garamond, serif",
				'text'     => "Garamond, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Georgia, serif",
				'text'     => "Georgia, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Impact, Charcoal, sans-serif",
				'text'     => "Impact, Charcoal, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Lucida Console', Monaco, monospace",
				'text'     => "'Lucida Console', Monaco, monospace",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
				'text'     => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'MS Sans Serif', Geneva, sans-serif",
				'text'     => "'MS Sans Serif', Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'MS Serif', 'New York', sans-serif",
				'text'     => "'MS Serif', 'New York', sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
				'text'     => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Tahoma,Geneva, sans-serif",
				'text'     => "Tahoma,Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Times New Roman', Times, serif",
				'text'     => "'Times New Roman', Times, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Trebuchet MS', Helvetica, sans-serif",
				'text'     => "'Trebuchet MS', Helvetica, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Verdana, Geneva, sans-serif",
				'text'     => "Verdana, Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
		),
	);

	$all_fonts[] = array(
		'text'     => __( 'Google Fonts', 'mpc' ),
		'children' => $formatted,
	);

	$formatted = json_encode( $all_fonts );

	$fonts_file = @fopen( trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts.json', 'w' );

	if ( $fonts_file === false ) {
		return 0;
	}

	$saved = fwrite( $fonts_file, $formatted );
	fclose( $fonts_file );

	if ( $saved !== false ) {
		return trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts.json';
	} else {
		return trailingslashit( dirname( __FILE__ ) ) . 'google_webfonts_static.json';
	}
}

/* Get webfonts */
function mpc_get_webfonts() {
	$all_fonts = array();

	$all_fonts[] = apply_filters( 'ma_custom_fonts', array() );

	if ( empty( $all_fonts ) ) {
		return trailingslashit( dirname( __FILE__ ) ) . 'standard_webfonts_static.json';
   	}

	$all_fonts[] = array(
		'text'     => __( 'Standard Fonts', 'mpc' ),
		'children' => array(
			array(
				'id'       => "Arial, Helvetica, sans-serif",
				'text'     => "Arial, Helvetica, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Arial Black', Gadget, sans-serif",
				'text'     => "'Arial Black', Gadget, sans-serif",
				'variants' => array( 'regular', 'italic' ),
			),
			array(
				'id'       => "'Bookman Old Style', serif",
				'text'     => "'Bookman Old Style', serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Comic Sans MS', cursive",
				'text'     => "'Comic Sans MS', cursive",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Courier, monospace",
				'text'     => "Courier, monospace",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Garamond, serif",
				'text'     => "Garamond, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Georgia, serif",
				'text'     => "Georgia, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Impact, Charcoal, sans-serif",
				'text'     => "Impact, Charcoal, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Lucida Console', Monaco, monospace",
				'text'     => "'Lucida Console', Monaco, monospace",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
				'text'     => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'MS Sans Serif', Geneva, sans-serif",
				'text'     => "'MS Sans Serif', Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'MS Serif', 'New York', sans-serif",
				'text'     => "'MS Serif', 'New York', sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
				'text'     => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Tahoma,Geneva, sans-serif",
				'text'     => "Tahoma,Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Times New Roman', Times, serif",
				'text'     => "'Times New Roman', Times, serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "'Trebuchet MS', Helvetica, sans-serif",
				'text'     => "'Trebuchet MS', Helvetica, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
			array(
				'id'       => "Verdana, Geneva, sans-serif",
				'text'     => "Verdana, Geneva, sans-serif",
				'variants' => array( 'regular', 'italic', '700', '700italic' ),
			),
		),
	);

	$all_fonts = json_encode( $all_fonts );

	$fonts_file = @fopen( trailingslashit( dirname( __FILE__ ) ) . 'standard_webfonts.json', 'w' );

	if ( $fonts_file === false ) {
		return 0;
	}

	$saved = fwrite( $fonts_file, $all_fonts );
	fclose( $fonts_file );

	if ( $saved !== false ) {
		return trailingslashit( dirname( __FILE__ ) ) . 'standard_webfonts.json';
	} else {
		return trailingslashit( dirname( __FILE__ ) ) . 'standard_webfonts_static.json';
	}
}

/* Get presets list */
add_action( 'wp_ajax_mpc_get_typography_presets', 'mpc_get_typography_presets' );
function mpc_get_typography_presets() {
	if ( ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( 'not set' );
	}

	$presets = get_option( 'mpc_presets_typography' );
	$presets = json_decode( $presets, true );

	if ( $presets !== false ) {
		foreach ( $presets as $name => $preset ) {
			if ( $name == '__index' ) {
				continue;
			}

			if ( ! isset( $preset[ '__name' ] ) ) {
				$preset[ '__name' ] = '';
			}

			echo '<option class="' . $name . '" value="' . $name . '">' . $preset[ '__name' ] . '</option>';
		}
	}

	die();
}

/* New preset */
add_action( 'wp_ajax_mpc_new_typography_preset', 'mpc_new_typography_preset' );
function mpc_new_typography_preset() {
	if ( ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'values' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( '{"error":"not set"}' );
	}

	$values = array_map( 'stripslashes_deep', $_POST[ 'values' ] );

	$typography_presets = get_option( 'mpc_presets_typography' );
	if ( $typography_presets === false ) {
		$typography_presets = array(
			'__index' => 0,
		);
	} else {
		$typography_presets = json_decode( $typography_presets, true );
	}

	$display_name = array( '__name' => $_POST[ 'name' ] );
	$values = $display_name + $values;

	$preset_name = ( defined( 'MPC_MASSIVE_PRESETS' ) ? MPC_MASSIVE_PRESETS : '' ) . 'preset_' . $typography_presets[ '__index' ];

	$typography_presets[ '__index' ] += 1;

	$typography_presets[ $preset_name ] = $values;

	uasort( $typography_presets, 'mpc_sort_presets' );

	$typography_presets = mpc_after_sort_presets( $typography_presets );

	$typography_presets = json_encode( $typography_presets );

	if ( $typography_presets !== false ) {
		update_option( 'mpc_presets_typography', $typography_presets );
	} else {
		die( '{"error":"not set"}' );
	}

	$response = array(
		'id' => $preset_name,
		'markup' => '<option class="' . esc_attr( $preset_name ) . '" value="' . esc_attr( $preset_name ) . '">' .$_POST[ 'name' ] . '</option>',
	);

	echo json_encode( $response );

	die();
}

/* Load preset */
add_action( 'wp_ajax_mpc_load_typography_preset', 'mpc_load_typography_preset' );
function mpc_load_typography_preset() {
	if ( ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( 'not set' );
	}

	$typography_presets = get_option( 'mpc_presets_typography' );
	$typography_presets = json_decode( $typography_presets, true );

	if ( empty( $typography_presets ) || empty( $typography_presets[ $_POST[ 'id' ] ] ) ) {
		echo '{"error":"not set"}';
	} else {
		echo json_encode( $typography_presets[ $_POST[ 'id' ] ] );
	}

	die();
}

/* Edit preset */
add_action( 'wp_ajax_mpc_edit_typography_preset', 'mpc_edit_typography_preset' );
function mpc_edit_typography_preset() {
	if ( ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'name' ] ) ||
		 ! isset( $_POST[ 'values' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( 'not set' );
	}

	$values = array_map( 'stripslashes_deep', $_POST[ 'values' ] );

	$typography_presets = get_option( 'mpc_presets_typography' );
	$typography_presets = json_decode( $typography_presets, true );

	if ( empty( $typography_presets ) ) {
		die( 'not set' );
	}

	if ( isset( $typography_presets[ $_POST[ 'id' ] ] ) ) {
		$values[ '__name' ] = $_POST[ 'name' ];

		$typography_presets[ $_POST[ 'id' ] ] = array_merge( $typography_presets[ $_POST[ 'id' ] ], $values );

		uasort( $typography_presets, 'mpc_sort_presets' );

		$typography_presets = mpc_after_sort_presets( $typography_presets );

		$typography_presets = json_encode( $typography_presets );

		if ( $typography_presets !== false ) {
			update_option( 'mpc_presets_typography', $typography_presets );
		} else {
			die( 'not set' );
		}
	} else {
		die( 'not set' );
	}

	die();
}

/* Delete preset */
add_action( 'wp_ajax_mpc_delete_typography_preset', 'mpc_delete_typography_preset' );
function mpc_delete_typography_preset() {
	if ( ! isset( $_POST[ 'id' ] ) ||
		 ! isset( $_POST[ 'wp_nonce' ] ) ||
		 ! wp_verify_nonce( $_POST[ 'wp_nonce' ], 'mpc_typography_presets' ) ) {
		die( 'not set' );
	}

	$typography_presets = get_option( 'mpc_presets_typography' );
	$typography_presets = json_decode( $typography_presets, true );

	if ( empty( $typography_presets ) ) {
		die( 'not set' );
	}

	if ( isset( $typography_presets[ $_POST[ 'id' ] ] ) ) {
		unset( $typography_presets[ $_POST[ 'id' ] ] );

		$typography_presets = json_encode( $typography_presets );

		if ( $typography_presets !== false ) {
			update_option( 'mpc_presets_typography', $typography_presets );
		} else {
			die( 'not set' );
		}
	} else {
		die( 'not set' );
	}

	die();
}