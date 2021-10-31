<?php
/**
 * Default header type for header builder
 *
 * @since 4.8
 */

global $porto_settings;

if ( is_customize_preview() ) {
	$current_layout           = get_option( 'porto_header_builder', '' );
	$header_builder_positions = get_option( 'porto_header_builder_layouts', array() );
	if ( isset( $current_layout['selected_layout'] ) && $current_layout['selected_layout'] && isset( $header_builder_positions[ $current_layout['selected_layout'] ] ) ) {
		$header_elements = get_option( 'porto_header_builder_elements', array() );
	} else {
		$header_elements = array();
	}
} else {
	$current_layout  = porto_header_builder_layout();
	$header_elements = isset( $current_layout['elements'] ) ? $current_layout['elements'] : array();
}

if ( is_customize_preview() && porto_get_wrapper_type() != 'boxed' && 'boxed' == $porto_settings['header-wrapper'] ) : ?>
	<div id="header-boxed">
<?php endif; ?>

<?php if ( porto_header_type_is_side() && isset( $current_layout['side_header_toggle'] ) && $current_layout['side_header_toggle'] ) : ?>
	<div class="side-header-narrow-bar side-header-narrow-bar-<?php echo esc_attr( $current_layout['side_header_toggle'] ); ?>">
		<div class="side-header-narrow-bar-logo">
		<?php if ( isset( $current_layout['side_header_toggle_logo'] ) && $current_layout['side_header_toggle_logo'] ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>"><img src="<?php echo esc_url( $current_layout['side_header_toggle_logo'] ); ?>" alt="<?php esc_attr_e( 'Secondary Logo', 'porto' ); ?>"></a>
		<?php endif; ?>
		</div>
		<div class="side-header-narrow-bar-content">
		<?php if ( isset( $current_layout['side_header_toggle_desc'] ) && $current_layout['side_header_toggle_desc'] ) : ?>
			<?php echo porto_strip_script_tags( $current_layout['side_header_toggle_desc'] ); ?>
		<?php endif; ?>
		</div>
		<div class="side-header-narrow-bar-toggle">
			<button class="hamburguer-btn">
				<span class="hamburguer">
					<span></span>
					<span></span>
					<span></span>
				</span>
				<span class="close">
					<span></span>
					<span></span>
				</span>
			</button>
		</div>
	</div>
<?php endif; ?>

	<header id="header" class="header-builder<?php echo porto_header_type_is_side() ? ' header-side sticky-menu-header' : '', $porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url'] ? ' logo-overlay-header' : ''; ?>">
	<?php if ( porto_header_type_is_side() && isset( $current_layout['side_header_toggle'] ) && 'top' == $current_layout['side_header_toggle'] && isset( $current_layout['side_header_disable_overlay'] ) && $current_layout['side_header_disable_overlay'] ) : ?>
		<button class="hamburguer-btn hamburguer-close active">
			<span class="close">
				<span></span>
				<span></span>
			</span>
		</button>
	<?php endif; ?>

	<?php
		$header_rows     = array( 'top', 'main', 'bottom' );
		$header_columns  = array( 'left', 'center', 'right' );
		$mobile_use_same = true;
	foreach ( $header_rows as $row ) {
		foreach ( $header_columns as $column ) {
			if ( isset( $header_elements[ 'mobile_' . $row . '_' . $column ] ) && $header_elements[ 'mobile_' . $row . '_' . $column ] && ! empty( json_decode( $header_elements[ 'mobile_' . $row . '_' . $column ] ) ) ) {
				$mobile_use_same = false;
				break 2;
			}
		}
	}
	foreach ( $header_rows as $row ) {
		$header_row_used          = false;
		$mobile_header_row_used   = false;
		$header_has_center        = isset( $header_elements[ $row . '_center' ] ) && $header_elements[ $row . '_center' ] && ! empty( json_decode( $header_elements[ $row . '_center' ] ) );
		$mobile_header_has_center = false ? $header_has_center : ( isset( $header_elements[ 'mobile_' . $row . '_center' ] ) && $header_elements[ 'mobile_' . $row . '_center' ] && ! empty( json_decode( $header_elements[ 'mobile_' . $row . '_center' ] ) ) );
		foreach ( $header_columns as $column ) {
			if ( isset( $header_elements[ $row . '_' . $column ] ) && $header_elements[ $row . '_' . $column ] && ! empty( json_decode( $header_elements[ $row . '_' . $column ] ) ) ) {
				$header_row_used = true;
				if ( $mobile_use_same ) {
					break;
				}
			}
			if ( ! $mobile_use_same && isset( $header_elements[ 'mobile_' . $row . '_' . $column ] ) && $header_elements[ 'mobile_' . $row . '_' . $column ] && ! empty( json_decode( $header_elements[ 'mobile_' . $row . '_' . $column ] ) ) ) {
				$mobile_header_row_used = true;
			}
		}
		if ( $header_row_used && $mobile_use_same ) {
			$mobile_header_row_used = true;
		}
		if ( $header_row_used || $mobile_header_row_used ) {
			$main_menu_wrap = '';
			if ( 'bottom' == $row ) {
				foreach ( $header_columns as $column ) {
					$elements = isset( $header_elements[ $row . '_' . $column ] ) ? $header_elements[ $row . '_' . $column ] : '';
					if ( strpos( $elements, '"main-menu"' ) !== false || false !== strpos( $elements, '"main-toggle-menu"' ) || false !== strpos( $elements, '"secondary-menu"' ) ) {
						$main_menu_wrap             = ' main-menu-wrap';
						$GLOBALS['porto_menu_wrap'] = true;
						break;
					}
				}
			}
			echo '<div class="header-' . $row . ( $header_has_center ? ' header-has-center' : '' ) . ( $mobile_header_has_center ? ' header-has-center-sm' : '' ) . ( $header_has_center && ! $mobile_use_same && ! $mobile_header_has_center ? ' header-has-not-center-sm' : '' ) . ( 'top' == $row && $header_row_used && ! $mobile_header_row_used ? ' hidden-for-sm' : '' ) . $main_menu_wrap . '">';
				/*
					if ( porto_header_type_is_side() ) {
					echo '<div class="header-row">';
				} else {*/
					echo '<div class="header-row ' . ( 'wide' == $porto_settings['header-wrapper'] ? 'container-fluid' : 'container' ) . '">';
				// }
			foreach ( $header_columns as $column ) {
				$elements        = isset( $header_elements[ $row . '_' . $column ] ) ? json_decode( $header_elements[ $row . '_' . $column ] ) : array();
				$mobile_elements = isset( $header_elements[ 'mobile_' . $row . '_' . $column ] ) ? json_decode( $header_elements[ 'mobile_' . $row . '_' . $column ] ) : array();

				$mobile_col_use_same = $mobile_use_same;
				if ( ! $mobile_col_use_same ) {
					$mobile_col_use_same = empty( $elements ) && empty( $mobile_elements ) ? true : ( isset( $header_elements[ $row . '_' . $column ] ) && isset( $header_elements[ 'mobile_' . $row . '_' . $column ] ) && $header_elements[ $row . '_' . $column ] == $header_elements[ 'mobile_' . $row . '_' . $column ] ? true : false );
				}
				if ( ! empty( $elements ) ) {
					echo '<div class="header-col header-' . $column . ( ! $mobile_col_use_same ? ' hidden-for-sm' : '' ) . '">';
						porto_header_elements( $elements );
					echo '</div>';
				}
				if ( ! empty( $mobile_elements ) && ! $mobile_col_use_same ) {
					echo '<div class="header-col visible-for-sm header-' . $column . '">';
						porto_header_elements( $mobile_elements, '', true );
					echo '</div>';
				}
			}
				echo '</div>';
			if ( 'main' == $row && ( ! porto_header_type_is_side() || ! isset( $current_layout['side_header_toggle'] ) || ! $current_layout['side_header_toggle'] ) ) {
				get_template_part( 'header/mobile_menu' );
			}
			if ( 'bottom' == $row && porto_header_type_is_side() && $porto_settings['header-copyright'] ) {
				echo '<div class="header-copyright container"><p>' . esc_html( $porto_settings['header-copyright'] ) . '</p></div>';
			}
				echo '</div>';
		}
	}
	?>
	</header>
<?php if ( is_customize_preview() && porto_get_wrapper_type() != 'boxed' && 'boxed' == $porto_settings['header-wrapper'] ) : ?>
	</div>
<?php endif; ?>
<?php if ( porto_header_type_is_side() && isset( $current_layout['side_header_toggle'] ) && 'top' == $current_layout['side_header_toggle'] && ( ! isset( $current_layout['side_header_disable_overlay'] ) || ! $current_layout['side_header_disable_overlay'] ) ) : ?>
	<div class="side-header-overlay hamburguer-close"></div>
<?php endif; ?>
