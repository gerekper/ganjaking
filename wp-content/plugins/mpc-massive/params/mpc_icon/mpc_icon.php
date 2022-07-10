<?php
/*----------------------------------------------------------------------------*\
	MPC_ICON Param
\*----------------------------------------------------------------------------*/

/* Add icons grid to menu panel */
add_action( 'vc_frontend_editor_render', 'mpc_icon_display_icon_select_grid' );
add_action( 'vc_backend_editor_footer_render', 'mpc_icon_display_icon_select_grid' );
if ( ! function_exists( 'mpc_icon_display_icon_select_grid' ) ) {
	function mpc_icon_display_icon_select_grid() { ?>
		<div id="mpc_icon_select_grid_modal" class="mpc-modal-init" data-modal-title="<?php _e( 'Select icon', 'mpc' ); ?>">
			<div id="mpc_icon_select_grid_wrap">
				<input type="text" id="mpc_icon_select_search" value="" placeholder="<?php _e( 'Search icons', 'mpc' ); ?>"/>
				<select id="mpc_icon_select_family">
					<option value="fa"><?php _e( 'Font Awesome', 'mpc' ); ?></option>
					<option value="eti"><?php _e( 'Elegant Icons', 'mpc' ); ?></option>
					<option value="etl"><?php _e( 'Elegant Line Icons', 'mpc' ); ?></option>
					<option value="el"><?php _e( 'Elusive Icons', 'mpc' ); ?></option>
					<option value="mi"><?php _e( 'Material Icons', 'mpc' ); ?></option>
					<option value="mpci"><?php _e( 'Massive Icons', 'mpc' ); ?></option>
					<option value="typcn"><?php _e( 'TypeIcons', 'mpc' ); ?></option>
					<option value="dashicons"><?php _e( 'Dashicons', 'mpc' ); ?></option>
					<option value="ti"><?php _e( 'Themify Icons', 'mpc' ); ?></option>
					<option value="mfgi"><?php _e( 'MFG Lab Icons', 'mpc' ); ?></option>
					<option value="lnr"><?php _e( 'Linear Icons (Free)', 'mpc' ); ?></option>
					<option value="icnm"><?php _e( 'IconMoon (Free)', 'mpc' ); ?></option>

					<?php do_action( 'ma/icon_fonts/select' ); ?>
				</select>

				<div id="mpc_icon_select_ajax" class="mpc-fonts-ajax"><span></span><span></span><span></span></div>
				<div id="mpc_icon_select_grid"></div>
			</div>
		</div>
	<?php }
}

/* Icon select */
add_action( 'wp_ajax_mpc_icon_get_icons_modal_font_link', 'mpc_icon_get_icons_modal_font_link' );
if ( ! function_exists( 'mpc_icon_get_icons_modal_font_link' ) ) {
	function mpc_icon_get_icons_modal_font_link() {
		if( false !== ( $custom_url = apply_filters( 'ma/icon_font/url', false, $_POST[ 'font' ] ) ) ) {
			echo '<link rel="stylesheet" href="' . $custom_url . '" media="all" />';
			die();
		}

		if( $_POST[ 'font' ] !== 'dashicons' ) : ?>
			<link rel="stylesheet" href="<?php echo mpc_get_plugin_path( __FILE__ ); ?>/assets/fonts/<?php echo esc_attr( $_POST[ 'font' ] ); ?>/<?php echo esc_attr( $_POST[ 'font' ] ); ?>.min.css" media="all" />
		<?php endif;
		die();
	}
}

/* Icon modal parts */
add_action( 'wp_ajax_mpc_icon_get_icons_modal_font_icons', 'mpc_icon_get_icons_modal_font_icons' );
if ( ! function_exists( 'mpc_icon_get_icons_modal_font_icons' ) ) {
	function mpc_icon_get_icons_modal_font_icons() {
		require_once( 'icon_grid.php' );
		die();
	}
}

vc_add_shortcode_param( 'mpc_icon', 'mpc_icon_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_icon_settings( $settings, $value ) {
	$mpc_icon = esc_attr( $value );
	$mpc_icon = empty( $mpc_icon ) ? '' : $mpc_icon;
	$param_name = esc_attr( $settings[ 'param_name' ] );

	$return = '<a href="#" class="mpc-icon-select ' . ( $mpc_icon == '' ? 'mpc-icon-empty' : '' ) . '">';
		$return .= '<i class="' . $mpc_icon . '"></i>';
	$return .= '</a>';
	$return .= '<a href="#" class="mpc-icon-clear">&times;</a>';
	$return .= '<input type="hidden" id="' . $param_name . '" name="' . $param_name . '" value="' . $mpc_icon . '" class="wpb_vc_param_value mpc-icon-value ' . $param_name . ' ' . esc_attr( $settings[ 'type' ] ) . '_field" />';

	return $return;
}
