<?php
/**
 * @package Porto
 * @author P-Theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $porto_is_dark, $porto_settings, $porto_save_settings_is_rtl, $porto_settings_optimize;
$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;
$porto_is_dark         = ( 'dark' == $b['css-type'] );
$dark                  = $porto_is_dark;
$legacy_mode           = apply_filters( 'porto_legacy_mode', true );

if ( ( isset( $porto_save_settings_is_rtl ) && $porto_save_settings_is_rtl ) || is_rtl() ) {
	$left  = 'right';
	$right = 'left';
	$rtl   = true;
} else {
	$left  = 'left';
	$right = 'right';
	$rtl   = false;
}

if ( ! function_exists( 'porto_if_dark' ) ) {
	function porto_if_dark( $if, $else = '' ) {
		global $porto_is_dark;
		if ( $porto_is_dark ) {
			return $if;
		}
		return $else;
	}
}
if ( ! function_exists( 'porto_if_light' ) ) {
	function porto_if_light( $if, $else = '' ) {
		global $porto_is_dark;
		if ( ! $porto_is_dark ) {
			return $if;
		}
		return $else;
	}
}
if ( ! function_exists( 'porto_calc_container_width' ) ) :
	function porto_calc_container_width( $container, $flag, $container_width, $grid_gutter_width ) {
		if ( ! $flag ) :
			?>
			@media (min-width: 768px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 720 + $grid_gutter_width; ?>px; }
			}
			@media (min-width: 992px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 960 + $grid_gutter_width; ?>px; }
			}
			@media (min-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px; }
			}
		<?php else : ?>
			@media (min-width: 768px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 720 - $grid_gutter_width; ?>px; }
			}
			@media (min-width: 992px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 960 - $grid_gutter_width; ?>px; }
				<?php echo esc_html( $container ); ?> .container { max-width: <?php echo 960 - $grid_gutter_width * 2; ?>px; }
			}
			@media (min-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo esc_html( $container_width - $grid_gutter_width ); ?>px; }
				<?php echo esc_html( $container ); ?> .container { max-width: <?php echo esc_html( $container_width - $grid_gutter_width * 2 ); ?>px; }
			}
			<?php
		endif;
	}
endif;

require_once( PORTO_LIB . '/lib/color-lib.php' );
$porto_color_lib = PortoColorLib::getInstance();

if ( ! function_exists( 'porto_background_opacity' ) ) {
	function porto_background_opacity( $porto_color_lib, $bg_color, $opacity ) {
		if ( empty( $bg_color ) ) {
			return;
		}
		if ( 'transparent' == $bg_color || ! $opacity ) {
			echo 'box-shadow: none;';
		} else {
			echo 'background-color: rgba(' . $porto_color_lib->hexToRGB( $bg_color ) . ',' . $opacity . ');';
		}
	}
}

if ( $dark ) {
	$color_dark = $b['color-dark'];
} else {
	$color_dark = '#222529';
}
$color_dark_inverse = '#fff';
$color_dark_1       = $color_dark;
$color_dark_2       = $porto_color_lib->lighten( $color_dark_1, 2 );
$color_dark_3       = $porto_color_lib->lighten( $color_dark_1, 5 );
$color_dark_4       = $porto_color_lib->lighten( $color_dark_1, 8 );
$color_dark_5       = $porto_color_lib->lighten( $color_dark_1, 3 );
$color_darken_1     = $porto_color_lib->darken( $color_dark_1, 2 );

$dark_bg           = $color_dark;
$dark_default_text = '#808697';

if ( $dark ) {
	$color_price = '#eee';

	$widget_bg_color       = $color_dark_3;
	$widget_title_bg_color = $color_dark_4;
	$widget_border_color   = 'transparent';

	$image_border_color = $color_dark_4;
	$color_widget_title = '#fff';

	$price_slide_bg_color = $color_dark;
	$panel_default_border = $color_dark_3;
} else {
	$color_price = '#444';

	$widget_bg_color       = '#fbfbfb';
	$widget_title_bg_color = '#f5f5f5';
	$widget_border_color   = '#ddd';

	$image_border_color   = '#ddd';
	$color_widget_title   = '#313131';
	$price_slide_bg_color = '#eee';
	$panel_default_border = '#ddd';
}

$screen_large = '(max-width: ' . ( $b['container-width'] + $b['grid-gutter-width'] - 1 ) . 'px)';
$input_lists  = 'input[type="email"],
				input[type="number"],
				input[type="password"],
				input[type="search"],
				input[type="tel"],
				input[type="text"],
				input[type="url"],
				input[type="color"],
				input[type="date"],
				input[type="datetime"],
				input[type="datetime-local"],
				input[type="month"],
				input[type="time"],
				input[type="week"]';
if ( isset( $b['container-width'] ) && (int) $b['container-width'] >= 1360 ) {
	$xxl = (int) $b['container-width'];
} else {
	$xxl = 1360;
}

$header_bg_empty     = ( empty( $b['header-bg']['background-color'] ) || 'transparent' == $b['header-bg']['background-color'] ) && ( empty( $b['header-bg']['background-image'] ) || 'none' == $b['header-bg']['background-image'] );
$breadcrumb_bg_empty = ( empty( $b['breadcrumbs-bg']['background-color'] ) || 'transparent' == $b['breadcrumbs-bg']['background-color'] ) && ( empty( $b['breadcrumbs-bg']['background-image'] ) || 'none' == $b['breadcrumbs-bg']['background-image'] );
$content_bg_empty    = ( empty( $b['content-bg']['background-color'] ) || 'transparent' == $b['content-bg']['background-color'] ) && ( empty( $b['content-bg']['background-image'] ) || 'none' == $b['content-bg']['background-image'] );
$footer_bg_empty     = ( empty( $b['footer-bg']['background-color'] ) || 'transparent' == $b['footer-bg']['background-color'] ) && ( empty( $b['footer-bg']['background-image'] ) || 'none' == $b['footer-bg']['background-image'] );

/* base */
$fonts_settings = array(
	'paragraph'      => 'p, .porto-u-sub-heading',
	'footer'         => '.footer',
	'footer-heading' => '.footer h1, .footer h2, .footer h3, .footer h4, .footer h5, .footer h6, .footer .widget-title, .footer .widget-title a, .footer-top .widget-title',
);

foreach ( $fonts_settings as $key => $selector ) {
	$css = array();
	if ( isset( $porto_settings[ $key . '-font' ] ) && ! empty( $porto_settings[ $key . '-font' ]['font-family'] ) ) {
		$css[] = 'font-family:' . sanitize_text_field( $porto_settings[ $key . '-font' ]['font-family'] ) . ', sans-serif';
	}
	if ( ! empty( $b[ $key . '-font' ]['font-weight'] ) ) {
		$css[] = 'font-weight:' . esc_html( $b[ $key . '-font' ]['font-weight'] );
	}
	if ( ! empty( $b[ $key . '-font' ]['font-size'] ) ) {
		$css[] = 'font-size:' . esc_html( $b[ $key . '-font' ]['font-size'] );
	}
	if ( ! empty( $b[ $key . '-font' ]['line-height'] ) ) {
		$css[] = 'line-height:' . esc_html( $b[ $key . '-font' ]['line-height'] );
	}
	if ( ! empty( $b[ $key . '-font' ]['letter-spacing'] ) ) {
		$css[] = 'letter-spacing:' . esc_html( $b[ $key . '-font' ]['letter-spacing'] );
	}
	if ( ! empty( $b[ $key . '-font' ]['color'] ) ) {
		$css[] = 'color:' . esc_html( $b[ $key . '-font' ]['color'] );
	}
	if ( ! empty( $b[ $key . '-font' ]['font-style'] ) ) {
		$css[] = 'font-style:' . esc_html( $b[ $key . '-font' ]['font-style'] );
	}

	if ( ! empty( $css ) ) {
		echo esc_html( $selector ) . '{' . implode( ';', $css ) . '}';
	}
}
$font_family_settings = array(
	'custom1' => '.custom-font1',
	'custom2' => '.custom-font2',
	'custom3' => '.custom-font3',
);
foreach ( $font_family_settings as $key => $selector ) {
	if ( ! empty( $b[ $key . '-font' ]['font-family'] ) ) {
		echo esc_html( $selector ) . '{font-family:' . sanitize_text_field( $b[ $key . '-font' ]['font-family'] ) . ', sans-serif}';
	}
}
?>

/*-------------------- plugins -------------------- */
<?php if ( class_exists( 'Uni_Cpo' ) ) : ?>
	.uni-cpo-calculate-btn {
		height: 3rem;
		margin: 0 0.5rem 0.375rem 0;
		padding: 0 25px;
		line-height: 3rem;
		outline: none !important;
	}
	.uni-cpo-calculate-btn i {
		margin-<?php echo porto_filter_output( $right ); ?>: 10px;
	}
<?php endif; ?>

/*------------------ general ---------------------- */
<?php
$max_spacing_mobile = 20;
if ( defined( 'WPB_VC_VERSION' ) ) :
	?>
	<?php if ( $b['grid-gutter-width'] < 17.5 ) : ?>
		.container-fluid .top-row.vc_column-gap-35 { padding-left: 0; padding-right: 0; }
	<?php endif; ?>
	<?php
	endif;
?>
/*------ Search Border Radius ------- */
<?php if ( $b['search-border-radius'] ) : ?>
	<?php if ( 'simple' == $b['search-layout'] ) : ?>
		#header .searchform .searchform-fields { border-radius: 20px; }
		#header .searchform input,
		#header .searchform select,
		#header .searchform .selectric .label,
		#header .searchform button { height: 36px; }
	<?php else : ?>
		#header .searchform { border-radius: 25px; line-height: 40px; }
		#header .searchform input,
		#header .searchform select,
		#header .searchform .selectric .label,
		#header .searchform button { height: 40px; }
		#header .searchform .live-search-list { <?php echo porto_filter_output( $left ); ?>: 15px; <?php echo porto_filter_output( $right ); ?>: 46px; width: auto }
	<?php endif; ?>
	#header .searchform select,
	#header .searchform .selectric .label { line-height: inherit; }
	#header .searchform input { border-radius: <?php echo porto_filter_output( $rtl ? '0 20px 20px 0' : '20px 0 0 20px' ); ?>; }
	#header .searchform button { border-radius: <?php echo porto_filter_output( $rtl ? '20px 0 0 20px' : '0 20px 20px 0' ); ?>; }
	#header .searchform .autocomplete-suggestions { left: 15px; right: 15px; }

	#header .searchform select,
	#header .searchform .selectric .label { padding: <?php echo porto_filter_output( $rtl ? '0 10px 0 15px' : '0 15px 0 10px' ); ?>; }

	#header .searchform input { padding: <?php echo porto_filter_output( $rtl ? '0 20px 0 15px' : '0 15px 0 20px' ); ?>; }

	#header .searchform button { padding: <?php echo porto_filter_output( $rtl ? '0 13px 0 16px' : '0 16px 0 13px' ); ?>; }
<?php endif; ?>

<?php
/* theme */
?>
/*------------------ header ---------------------- */
<?php if ( isset( $b['header-bottom-height'] ) && $b['header-bottom-height'] ) : ?>
.header-bottom { min-height: <?php echo esc_html( $b['header-bottom-height'] ); ?>px }
<?php endif; ?>
<?php if ( isset( $b['header-top-height'] ) ) : ?>
.header-top > .container, .header-top > .container-fluid { min-height: <?php echo esc_html( $b['header-top-height'] ); ?>px }
<?php endif; ?>

<?php
	$header_type = porto_get_header_type();
?>
/* menu */
<?php if ( isset( $b['header-top-menu-hide-sep'] ) && ! $b['header-top-menu-hide-sep'] ) : ?>
	#header .header-top .top-links > li.menu-item:first-child > a { padding-<?php echo porto_filter_output( $left ); ?>: 0; }
<?php endif; ?>
<?php if ( 'transparent' == $b['switcher-hbg-color'] || ! $b['switcher-top-level-hover'] ) : ?>
	#header .porto-view-switcher:first-child > li.menu-item:first-child > a { padding-<?php echo porto_filter_output( $left ); ?>: 0; }
<?php endif; ?>
<?php if ( $b['switcher-top-level-hover'] ) : ?>
	#header .porto-view-switcher > li.menu-item:hover > a,
	#header .porto-view-switcher > li.menu-item > a.active { color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>; background: <?php echo esc_html( $b['switcher-hbg-color'] ); ?> }
<?php endif; ?>

<?php if ( isset( $b['header-top-bottom-border'] ) && $b['header-top-bottom-border']['border-top'] && '0px' != $b['header-top-bottom-border']['border-top'] && 'menu-hover-line' == $b['menu-type'] ) : ?>
	#header .header-top + .header-main {
		border-top: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['header-top-bottom-border']['border-color'] ); ?>;
	}
	.header-top + .header-main .menu-hover-line > li.menu-item > a:before { height: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?>; top: -<?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?>; }
	#header.sticky-header .header-main.change-logo .header-left,
	#header.sticky-header .header-main.change-logo .header-center,
	#header.sticky-header .header-main.change-logo .header-right { padding-top: 0; padding-bottom: 0; }
<?php endif; ?>
<?php if ( isset( $b['header-top-border'] ) && 'menu-hover-line' == $b['menu-type'] && $b['header-top-border']['border-top'] && '0px' != $b['header-top-border']['border-top'] ) : ?>
	.header-main:first-child .menu-hover-line > li.menu-item > a:before { height: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; top: -<?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; }
<?php endif; ?>
<?php if ( 'menu-hover-line menu-hover-underline' == $b['menu-type'] ) : ?>
	.mega-menu.menu-hover-underline > li.menu-item > a:before {
		margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-left'], 'px' ); ?>;
		margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-right'], 'px' ); ?>;
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		.mega-menu.menu-hover-underline > li.menu-item > a:before {
			margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-left'], 'px' ); ?>;
			margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-right'], 'px' ); ?>;
		}
	}
<?php endif; ?>

/* search form */
<?php if ( 'simple' == $b['search-layout'] || 'large' == $b['search-layout'] ) : ?>
	#header .search-popup .search-toggle { display: inline-block; }
	#header .search-popup .searchform { border-width: 5px; display: none; position: absolute; top: 100%; margin-top: 8px; z-index: 1003; box-shadow: 0 5px 8px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.1)', 'rgba(255, 255, 255, 0.08)' ); ?>; }
	@media (min-width: 992px) {
		#header .search-popup .searchform { <?php echo porto_filter_output( $left ); ?>: -25px; }
	}
	#header .header-left .searchform { <?php echo porto_filter_output( $left ); ?>: -10px; <?php echo porto_filter_output( $right ); ?>: auto; }
	#header .header-right .searchform { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: -22px; }
<?php endif; ?>
<?php if ( 'simple' == $b['search-layout'] || 'large' == $b['search-layout'] || 'advanced' == $b['search-layout'] ) : ?>
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		@media (max-width: 991px) {
	<?php endif; ?>
	#header .search-popup-left .searchform { left: auto; right: -1.25rem }
	#header .search-popup-center .searchform { left: 50%; right: auto; transform: translateX(-50%) }
	#header .search-popup-right .searchform { left: -1.25rem; right: auto }
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		}
	<?php endif; ?>
<?php endif; ?>
<?php if ( 'simple' == $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle { width: 1.4em; font-size: .8rem; }
	/* #header .searchform-popup .search-toggle i { position: relative; top: -1px; } */
	#header .search-popup .searchform { box-shadow: 0 10px 30px 10px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.05)', 'rgba(255, 255, 255, 0.02)' ); ?>; padding: 15px 17px; border: none; z-index: 1002; top: 100%; }
	#header .searchform .searchform-fields { border: 1px solid #eee; }
	#header .searchform input { max-width: 220px; }
	#header .searchform:not(.searchform-cats) input { border: none; }
	/*#header .searchform button { position: relative; top: -1px; }*/
<?php elseif ( 'reveal' == $b['search-layout'] ) : ?>
	#header .searchform-popup { position: static; }
	#header .search-popup .search-toggle { display: inline-block; }
	#header .search-popup .searchform { display: none; position: absolute; top: 0; z-index: 1003; border-radius: 0; border: none; left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; height: 100%; margin-top: 0; box-shadow: none; }
	#header .searchform .searchform-fields {  position: absolute; left: 0; width: 100%; height: 100%; align-items: center; }
	#header .searchform input { font-size: 22px; width: 100% !important; height: 44px; border-width: 0 0 2px 0; border-style: solid; border-radius: 0; padding: 0 15px; }
	#header .searchform .text { flex: 1; }
	#header .searchform .selectric-cat { display: none; }
	#header .searchform .button-wrap { position: absolute; <?php echo porto_filter_output( $right ); ?>: 10px; }
	#header .searchform .btn-close-search-form { font-size: 20px;<?php echo ! $b['mainmenu-toplevel-link-color']['regular'] ? '' : 'color: ' . esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?> }
<?php elseif ( 'overlay' == $b['search-layout'] ) : ?>
	#header .search-popup .search-toggle { display: inline-block; min-width: 25px; }
	#header .search-popup .searchform { display: none; position: fixed; top: 0; z-index: 1003; border-radius: 0; border: none; left: 0; right: 0; height: 100%; margin-top: 0; box-shadow: none; }
	#header .searchform .searchform-fields { height: 100%; align-items: center; }
	#header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric .label { border: none; height: 44px; }
	#header .searchform input { max-width: none; width: 100%; font-size: 22px; padding: 0 15px; border-radius: 0; }
	#header .searchform .selectric-cat,
	#header .searchform .text { border-bottom: 2px solid <?php echo esc_html( $b['skin-color'] ); ?>; }
	#header .searchform .text { flex: 1; }
	#header .searchform .button-wrap { position: absolute; <?php echo porto_filter_output( $right ); ?>: 30px; top: 20px; }
	#header .searchform .btn-close-search-form { font-size: 20px;<?php echo ! $b['mainmenu-toplevel-link-color']['regular'] ? '' : 'color: ' . esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform-popup .search-toggle:after { content: none }
<?php else : ?>
	#header .searchform-popup .search-toggle { font-size: 20px; width: 40px; height: 40px; line-height: 40px; }
	#header .searchform button { font-size: 16px; padding: 0 15px; }
	#header .searchform-popup .search-toggle i:before,
	#header .searchform button i:before { content: "\e884"; font-family: "porto"; font-weight: 600; }
	<?php if ( isset( $b['sticky-searchform-popup-border-color'] ) && $b['sticky-searchform-popup-border-color'] ) : ?>
		#header.sticky-header .searchform-popup .searchform { border-color: <?php echo esc_html( $b['sticky-searchform-popup-border-color'] ); ?>; }
		#header.sticky-header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['sticky-searchform-popup-border-color'] ); ?>; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( isset( $porto_settings['search-live'] ) && $porto_settings['search-live'] ) : ?>
	.searchform .live-search-list .autocomplete-suggestions { box-shadow: 0 10px 20px 5px <?php echo porto_filter_output( $porto_color_lib->isColorDark( $b['searchform-bg-color'] ) ? 'rgba(255, 255, 255, 0.03)' : 'rgba(0, 0, 0, 0.05)' ); ?>; }
	.searchform .live-search-list .autocomplete-suggestions::-webkit-scrollbar { width: 5px; }
	.searchform .live-search-list .autocomplete-suggestions::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
	.live-search-list .autocomplete-suggestion .search-price { color: <?php echo esc_html( $porto_color_lib->isColorDark( $b['searchform-bg-color'] ) ? 'rgba(255, 255, 255, .7)' : $color_dark ); ?>; font-weight: 600; }
<?php endif; ?>

@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
	#header .searchform input { width: 318px; }
	#header .searchform.searchform-cats input { width: 190px; }
}
<?php if ( 4 == $header_type ) : ?>
	#header .searchform input { width: 298px; }
	#header .searchform.searchform-cats input { width: 170px; }
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .searchform input { width: 240px; }
		#header .searchform.searchform-cats input { width: 112px; }
	}
<?php elseif ( 2 == $header_type || 8 == $header_type ) : ?>
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		@media (min-width: 992px) {
			#header .searchform input,
			#header .searchform.searchform-cats input { width: 140px; }
		}
	<?php endif; ?>
<?php endif; ?>

/* header type */
<?php if ( 1 == $header_type || 4 == $header_type || 9 == $header_type ) : ?>
	#header .my-wishlist {
		margin-<?php echo porto_filter_output( $right ); ?>: .25rem;
	}	
	#header .my-account {
		margin-<?php echo porto_filter_output( $left ); ?>: 5px;
		margin-<?php echo porto_filter_output( $right ); ?>: .25rem;
	}
	@media(min-width: 992px) {
		#header .my-wishlist {
			margin-<?php echo porto_filter_output( $right ); ?>: .5rem;
			padding-<?php echo porto_filter_output( $right ); ?>: .25rem;
		}	
		#header .my-account {
			margin-<?php echo porto_filter_output( $right ); ?>: .5rem;
		}
	}
<?php endif; ?>
<?php if ( 7 == $header_type ) : ?>
	#header .my-wishlist {
		margin-<?php echo porto_filter_output( $right ); ?>: .25rem;
	}
	#header .my-account {
		margin-<?php echo porto_filter_output( $right ); ?>: .25rem;
	}
	@media(min-width: 992px) {
		#header .my-account {
			margin-<?php echo porto_filter_output( $right ); ?>: .5rem;
		}
	}
<?php endif; ?>
<?php if ( 1 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type || 17 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header.header-loaded .header-main { transition: none; }
		#header .header-main .logo img { transition: none; -webkit-transform: scale(1); transform: scale(1); }
	}
<?php endif; ?>

/* mini cart */
<?php
	$minicart_type = porto_get_minicart_type();
?>
<?php if ( 'minicart-arrow-alt' == $minicart_type ) : ?>
	<?php if ( $header_type ) : ?>
	@media (max-width: 991px) {
		#mini-cart {
			margin-<?php echo porto_filter_output( $left ); ?>: .3125rem;
		}
	}
	<?php endif; ?>
<?php elseif ( 'simple' == $minicart_type ) : ?>
	#mini-cart .minicart-icon { font-size: 15px }
	<?php if ( empty( $b['minicart-icon'] ) ) : ?>
		#mini-cart .minicart-icon-default { border: 2px solid; border-radius: 0 0 5px 5px; width: 14px; height: 11px; position: relative; margin: 5px 3px 1px }
		#mini-cart .minicart-icon-default:before { content: ""; position: absolute; width: 8px; height: 9px; border: 2px solid; border-bottom: none; border-radius: 4px 4px 0 0; left: 1px; top: -7px; margin: 0 }
	<?php endif; ?>
	@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
		#mini-cart .cart-head { height: 30px }
	}
	<?php if ( empty( $header_type ) ) : ?>
		#header .searchform-popup + #mini-cart { margin-<?php echo porto_filter_output( $left ); ?>: .75em; }
	<?php endif; ?>
<?php endif; ?>
#mini-cart .cart_list::-webkit-scrollbar-thumb,
.wishlist-offcanvas .wishlist-popup::-webkit-scrollbar-thumb,
.minicart-offcanvas .widget_shopping_cart_content::-webkit-scrollbar-thumb { border-radius: 3px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }

/* main menu style for shop header and classic headers */
<?php if ( ( (int) $header_type >= 1 && (int) $header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( ( 'side' == $header_type || empty( $header_type ) ) && class_exists( 'Woocommerce' ) ) ) : ?>
	.sidebar-menu .wide li.sub > a,
	#header .main-menu .wide li.sub > a { font-weight: 700; }
<?php else : ?>
	.sidebar-menu .wide li.sub > a,
	#header .main-menu .wide li.sub > a { font-weight: 600; }
<?php endif; ?>

/* shop header type */
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	<?php if ( ! porto_header_type_is_side() && porto_header_type_is_preset() ) : ?>
		#header .header-contact { border-<?php echo porto_filter_output( $right ); ?>: 1px solid #dde0e2; padding-<?php echo porto_filter_output( $right ); ?>: 35px; margin-<?php echo porto_filter_output( $right ); ?>: 18px; line-height:22px; }
		#header .header-top .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: none; padding-<?php echo porto_filter_output( $right ); ?>: 0; }
	<?php endif; ?>

	<?php if ( 8 == $header_type ) : ?>
		@media (max-width: 360px) {
			#header .header-contact { display: none; }
		}
	<?php elseif ( 18 != $header_type ) : ?>
		@media (max-width: 991px) {
			#header .header-contact { display: none; }
		}
	<?php endif; ?>

	#header .porto-view-switcher .narrow ul.sub-menu,
	#header .top-links .narrow ul.sub-menu { padding: 5px 0; }

	<?php if ( 6 != $header_type && 7 != $header_type && 8 != $header_type ) : ?>
		@media (max-width: 767px) {
			#header:not(.header-builder) .header-top { display: flex; }
			#header:not(.header-builder) .switcher-wrap { display: inline-block; }
		}
	<?php endif; ?>

	<?php if ( (int) $header_type >= 2 ) : ?>
		.mega-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, .2); }
	<?php elseif ( porto_header_type_is_side() && empty( $b['side-menu-type'] ) ) : ?>
		.sidebar-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, .2); }
	<?php endif; ?>
	<?php if ( ! empty( $porto_settings['woo-show-default-page-header'] ) && $porto_settings['woo-show-default-page-header'] ) : ?>
		.page-header-8 { padding: 1.25rem 0; }
		.woocommerce-cart .main-content, .woocommerce-checkout .main-content { padding-top: 5px; }
		.page-header-8 .breadcrumb { margin-bottom: 0; justify-content: center; background: none; }
		.page-header-8 li { line-height: 3.521875rem; }
		.page-header-8 li a { color: <?php echo porto_if_light( '#222529', '#eee' ); ?>; font-family: 'Poppins', <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>, sans-serif; text-decoration: none;  font-size: 1.25rem; font-weight: 700; letter-spacing: -.03em; transition: opacity .3s; vertical-align: middle; }
		.page-header-8 li.disable a { pointer-events: none; }
		.page-header-8 li .delimiter.delimiter-2 { color: <?php echo porto_if_light( '#222529', '#eee' ); ?>; font-size: 1.875rem; font-weight: 700; margin: 0 1.2rem; float: <?php echo porto_filter_output( $left ); ?>; }
		.page-header-8 li.current~li a, .page-header-8 li.current~li .delimiter { opacity: .5; }
		.page-header-8 li.current a, .page-header-8 li:not(.disable) a:hover { color: var(--porto-primary-color); opacity: 1; }
	<?php endif; ?>
<?php endif; ?>

<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
	.mega-menu > li.has-sub:before,
	.mega-menu > li.has-sub:after { content: ''; position: absolute; bottom: -1px; z-index: 112; opacity: 0; <?php echo porto_filter_output( $left ); ?>: 50%; border: solid transparent; height: 0; width: 0; pointer-events: none; }
	.mega-menu > li.has-sub:before { bottom: 0; }
	.mega-menu > li.sub-ready:hover:before,
	.mega-menu > li.sub-ready:hover:after { opacity: 1; }
	.mega-menu > li.has-sub:before { border-bottom-color: <?php echo ! empty( $b['mainmenu-popup-bg-color'] ) && 'transparent' != $b['mainmenu-popup-bg-color'] ? esc_html( $b['mainmenu-popup-bg-color'] ) : '#fff'; ?>; border-width: 10px; margin-<?php echo porto_filter_output( $left ); ?>: -10px; }
	.mega-menu > li.has-sub:after { border-bottom-color: <?php echo ! empty( $b['mainmenu-popup-bg-color'] ) && 'transparent' != $b['mainmenu-popup-bg-color'] ? esc_html( $b['mainmenu-popup-bg-color'] ) : '#fff'; ?>; border-width: 9px; margin-<?php echo porto_filter_output( $left ); ?>: -9px; }
	.mega-menu.show-arrow > li.has-sub:before { margin-<?php echo porto_filter_output( $left ); ?>: -14px; }
	.mega-menu.show-arrow > li.has-sub:after { margin-<?php echo porto_filter_output( $left ); ?>: -13px; }

	/* menu effect down */
	.mega-menu > li.has-sub:before,
	.mega-menu > li.has-sub:after { bottom: 3px; transition: bottom .2s ease-out; }
	.mega-menu > li.has-sub:before { bottom: 4px; }
	.mega-menu > li.has-sub:hover:before { bottom: -1px; }
	.mega-menu > li.has-sub:hover:after { bottom: -2px; }
	/* end menu effect down */

	<?php if ( ! class_exists( 'Woocommerce' ) ) : ?>
		.mega-menu .wide .popup,
		.mega-menu .narrow ul.sub-menu { box-shadow: 0 9px 30px 10px rgba(0, 0, 0, 0.1) }
	<?php endif; ?>
<?php endif; ?>

<?php if ( 1 == $header_type || 2 == $header_type || 9 == $header_type ) : ?>
	#header .header-minicart { white-space: nowrap; }
	@media (max-width: 991px) {
		#header .header-main .header-center { flex: 1; justify-content: flex-end; }
	}
	#header .mobile-toggle { padding-left: 11px; padding-right: 11px; }
	#header .header-top .porto-view-switcher .narrow .popup > .inner > ul.sub-menu { border: 1px solid <?php echo porto_if_light( '#ccc', '#222529' ); ?> }
	#header .header-top .porto-view-switcher > li.has-sub:before { border-bottom-color: <?php echo porto_if_light( '#ccc', '#222529' ); ?> }
	.mega-menu > li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 2px; }

<?php endif; ?>

<?php if ( 1 == $header_type ) : ?>
	#header.sticky-header .main-menu { background: none; }
	.header-top .share-links a { border-radius: 50%; width: 2em; height: 2em; margin: 0; }
	.header-top .share-links a:not(:hover) { background: none; }
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $right ); ?>: .25rem;}
	<?php if ( ! empty( $b['header-top-link-color']['regular'] ) ) : ?>
		.header-top .share-links a:not(:hover) { color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>; }
	<?php endif; ?>

<?php elseif ( 2 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $left ); ?>; }
	}
	#header.sticky-header .header-main > .container { display: flex; align-items: center; }
	#header.sticky-header .header-main .header-center { flex: 1; }
	#header.sticky-header .header-main .header-right { width: auto; }
	#header .header-main .mega-menu { position: relative; padding-<?php echo porto_filter_output( $right ); ?>: 10px; margin-<?php echo porto_filter_output( $right ); ?>: .5rem; vertical-align: middle; }
	@media (min-width: 992px) {
		#header .header-main .mega-menu { display: inline-block; }
	}
	#header:not(.sticky-header) .header-main .mega-menu:after { content: ''; position: absolute; height: 24px; top: 7px; <?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: 1px solid #dde0e2; }
	#header.sticky-header .header-main.change-logo .container > div { padding-top: 0; padding-bottom: 0; }
	#header .header-main .mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }
	#header .searchform-popup .search-toggle { font-size: 15px; margin: 0 .5rem }

<?php elseif ( 3 == $header_type ) : ?>
	#nav-panel .mega-menu>li.menu-item { float: none; }
	#nav-panel .menu-custom-block { margin-top: 0; margin-bottom: 0; }
	#header .header-right .menu-custom-block { display: inline-block; }
	@media (max-width: 991px) {
		#header .header-right .menu-custom-block { display: none; }
	}
	#header .search-toggle .search-text { display: inline-block; }
	#header .header-top .searchform-popup { margin-<?php echo porto_filter_output( $right ); ?>: .5rem; }
<?php elseif ( 4 == $header_type ) : ?>
	.mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }
	<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
		#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	<?php else : ?>
		#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: .5rem; }
	<?php endif; ?>
	<?php if ( 'side' == $b['mobile-panel-type'] && $b['mobile-panel-add-search'] ) : ?>
		@media (max-width: 991px) {
			#header .searchform-popup { display: none; }
		<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
			#header .mobile-toggle { padding-<?php echo porto_filter_output( $left ); ?>: 0 }
		<?php endif; ?>
		}
	<?php endif; ?>

<?php elseif ( 5 == $header_type ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left,
		#header .header-main .header-center { display: inline-block; }
	}

<?php elseif ( 6 == $header_type ) : ?>
	#header { border-bottom: 1px solid rgba(138, 137, 132, 0.5) !important; }
	#header .header-main .header-right { width: 275px; white-space: nowrap; text-align: <?php echo porto_filter_output( $left ); ?>; }
	#header .header-main .header-center { width: 1%; white-space: nowrap; }
	#header .header-main .header-left,
	#header .header-main .header-right,
	#header .header-main .header-center { padding: 0 !important; border-<?php echo porto_filter_output( $right ); ?>: 1px solid rgba(138, 137, 132, 0.5); }
	#header .header-main .header-center > div { padding-left: 20px; padding-right: 20px; }
	#header .header-main .header-right-bottom { padding-left: 15px; padding-right: 15px; }
	#header .header-main .header-center-top,
	#header .header-main .header-right-top { border-bottom: 1px solid rgba(138, 137, 132, 0.5); }
	#header .header-main .header-right > div,
	#header .header-main .header-center > div { height: 50px; display: flex; align-items: center; }
	#header .header-main .header-center > div { justify-content: flex-end }
	#header .header-main .header-right > div { justify-content: center }
	#header.sticky-header .header-main .header-center-top,
	#header.sticky-header .header-main .header-right-top { display: none; }

	#header .header-main #main-menu { display: block; }
	#header .main-menu { text-align: inherit; white-space: nowrap; }
	.mega-menu>li.menu-item { display: inline-block; float: none; }
	#header .porto-view-switcher { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
	#header.header-6 .porto-view-switcher > li.menu-item > a { background: none; }
	#header .porto-view-switcher > li.menu-item > a { font-size: 14px; padding-top: 1px; }
	#header .header-right .porto-view-switcher > li.menu-item:hover > a { background: none; }
	#header .porto-view-switcher .narrow ul.sub-menu, #header .top-links .narrow ul.sub-menu { padding: 0; }
	#header .top-links > li.menu-item { float: none; }
	#header .top-links > li.menu-item > a { font-size: 13px; padding: 0 10px; }
	#header .mobile-toggle { margin: 0; }

	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		#header .searchform-popup .search-toggle { display: none; }
		#header.header-6 .header-right .searchform-popup .searchform { border: none; background: none; display: block; position: static; box-shadow: none; border-radius: 0; }
		#header .searchform .searchform-fields { border: none; }
		#header .searchform button i:before { font-weight: 400; }
	<?php endif; ?>
	#header .searchform input,
	#header .searchform button { font-size: 20px; }
	#header .searchform input { width: 218px !important; color: inherit; font-size: 13px; text-transform: uppercase; }
	#header .searchform input, #header .searchform select, #header .searchform .selectric { border-<?php echo porto_filter_output( $right ); ?>: none; }

	@media (max-width: 575px) {
		#header .searchform input { max-width: 140px !important; }
		#header .header-main .header-right { width: 174px; }
		#header .header-main .header-right .searchform-popup { margin-<?php echo porto_filter_output( $left ); ?>: -75px; }
		#header .header-main .header-center { border-right: none; }
	}
	@media (max-width: 767px) {
		#header .header-right { text-align: center; }
	}

<?php elseif ( 7 == $header_type ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left { display: none; }
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .mobile-toggle { display: inline-block; }
		#header .main-menu,
		#header.logo-center .header-main .container .header-left { display: none; }
	}
	@media (min-width: 992px) {
		#side-nav-panel .searchform { display: none; }
	}
	@media (max-width: 991px) {
		#header .header-contact { display: none; }
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $right ); ?>; }
		#header .header-main .header-right { width: 1%; }
	}
	#header .logo { padding-top: 10px; padding-bottom: 10px; }
	#header.sticky-header .logo,
	#header.sticky-header .header-main.change-logo .container>div { padding-top: 0; padding-bottom: 0; }
	.page-top ul.breadcrumb > li a { text-transform: inherit; }
	.switcher-wrap .mega-menu .popup,
	#header .currency-switcher,
	#header .view-switcher,
	#header .top-links { font-size: 13px; font-weight: 500; }
	#header .currency-switcher .popup,
	#header .view-switcher .popup,
	#header .top-links .popup { font-size: 11px; }
	#header .currency-switcher > li.menu-item > a,
	#header .view-switcher > li.menu-item > a,
	#header .top-links > li.menu-item > a { font-weight: 500; }
	#header .top-links { margin-<?php echo porto_filter_output( $left ); ?>: 15px; margin-<?php echo porto_filter_output( $right ); ?>: 20px; text-transform: uppercase; }
	@media (min-width: 576px) {
		#header .mobile-toggle { padding: 7px 10px; }
	}
	#header .header-main .header-contact { font-size: 16px; line-height: 36px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; padding-<?php echo porto_filter_output( $right ); ?>: 30px; border-<?php echo porto_filter_output( $right ); ?>-color: rgba(255, 255, 255, 0.3); }
	#header .searchform-popup .search-toggle { font-size: 16px; }

<?php elseif ( 8 == $header_type ) : ?>
	.mega-menu > li.menu-item > a { padding: 9px 12px; }
	.mega-menu.show-arrow > li.has-sub > a:after { position: relative; top: -1px; }
	#header .header-main .header-contact { padding-<?php echo porto_filter_output( $right ); ?>: 0; }
	#header .mobile-toggle { padding: 7px 10px; }
	#mini-cart .minicart-icon { font-size: 30px; }
	#mini-cart .minicart-icon:before { content: '\e871'; }
	#header:not(.sticky-header) #mini-cart .cart-head { width: 60px; }
	#header:not(.sticky-header) #mini-cart .cart-head { padding-<?php echo porto_filter_output( $right ); ?>: 20px; }
	@media (max-width: 767px) {
		#header .header-top { display: block; }
	}

<?php elseif ( 9 == $header_type ) : ?>
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	#main-toggle-menu .toggle-menu-wrap { box-shadow: none; }
	#main-toggle-menu .toggle-menu-wrap > ul { border-bottom: none; }
	#main-toggle-menu .menu-title { padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
	#main-toggle-menu .menu-title .toggle { position: relative; top: -1px; }
	.sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top-color: #e6ebee; margin-<?php echo porto_filter_output( $left ); ?>: 16px; margin-<?php echo porto_filter_output( $right ); ?>: 18px; padding: 14px 12px; }
	.widget_sidebar_menu .widget-title { padding: 14px 28px; }
	#main-menu .menu-custom-block { text-align: <?php echo porto_filter_output( $right ); ?>; }

/* classic header type */
<?php elseif ( ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
	<?php if ( ! empty( $header_type ) ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-right { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
		/*#header .header-main .header-right .searchform-popup { margin-<?php echo porto_filter_output( $right ); ?>: 0; }*/
	}
	<?php endif; ?>

	<?php if ( 'simple' != $b['search-layout'] && 'reveal' != $b['search-layout'] && 'overlay' != $b['search-layout'] ) : ?>
		#header .searchform { box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .searchform select,
		#header .searchform button,
		#header .searchform .selectric .label { height: 34px; line-height: 34px; }
		#header .searchform input { border: none; line-height: 1.5; height: 34px; width: 200px; }
		#header .searchform select { border-<?php echo porto_filter_output( $left ); ?>: 1px solid #ccc; padding-<?php echo porto_filter_output( $left ); ?>: 8px; margin-<?php echo porto_filter_output( $right ); ?>: -3px; font-size: 13px; }
		#header .searchform .selectric { border-<?php echo porto_filter_output( $left ); ?>: 1px solid #ccc; }
		#header .searchform .selectric .label { padding-<?php echo porto_filter_output( $left ); ?>: 8px; margin-<?php echo porto_filter_output( $right ); ?>: -3px; }
		#header .searchform button { padding: 0 12px; }
	<?php endif; ?>

	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		@media (min-width: 992px) {
			#header .searchform { box-shadow: none; }	
		}
	<?php endif; ?>

	.header-corporate .share-links a,
	.header-builder .share-links a { width: 28px; height: 28px; border-radius: 28px; margin: 0 1px; overflow: hidden; font-size: .8rem; }
	.header-corporate .share-links a:not(:hover),
	.header-builder .share-links a:not(:hover) { background-color: #fff; color: #333; }
<?php endif; ?>
<?php if ( 10 == $header_type ) : ?>
	#header .header-right-bottom { margin: 8px 0 6px; }
	#header .header-right-bottom > * { margin-<?php echo porto_filter_output( $left ); ?>: .8em }
	#header.sticky-header .header-right-bottom { margin-top: 5px; }
	@media (max-width: 991px) {
		#header .header-right-bottom { margin-top: 0; margin-bottom: 0; }
	}
	@media (max-width: 575px) {
		#header .share-links { display: none; }
	}
	#header .header-main .header-left,
	#header .header-main .header-center,
	#header .header-main .header-right { padding-top: 1rem; padding-bottom: 1rem; }
	#header .header-contact { margin: <?php echo porto_filter_output( $rtl ? '0 0 0 10px' : '0 10px 0 0' ); ?>; }
	@media (min-width: 992px) {
		#header .header-main.sticky .header-right-top { display: none; }
		#header.sticky-header .header-main.sticky .header-left,
		#header.sticky-header .header-main.sticky .header-center,
		#header.sticky-header .header-main.sticky .header-right { padding-top: 0; padding-bottom: 0; }
		#header .searchform { margin-bottom: 4px; margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
	}
<?php endif; ?>
<?php if ( (int) $header_type >= 11 && (int) $header_type <= 17 ) : ?>
	#header .header-main .searchform-popup, #header .header-main #mini-cart { display: none; }
	@media (min-width: 768px) {
		#header .switcher-wrap { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
		#header .header-main .block-inline { line-height: 50px; margin-bottom: 5px; }
		#header .header-left .block-inline { margin-<?php echo porto_filter_output( $right ); ?>: 8px; }
		#header .header-left .block-inline > * { margin: <?php echo porto_filter_output( $rtl ? '0 0 0 7px' : '0 7px 0 0' ); ?>; }
		#header .header-right .block-inline { margin-<?php echo porto_filter_output( $left ); ?>: 8px; }
		#header .header-right .block-inline > * { margin: <?php echo porto_filter_output( $rtl ? '0 7px 0 0' : '0 0 0 7px' ); ?>; }
		#header .share-links { line-height: 1; }
	}
	#header .header-top .welcome-msg { font-size: 1.15em; }
	#header .header-top #mini-cart { font-size: 1em; }
	#header .header-top #mini-cart:first-child { margin-left: 0; margin-right: 0; }
	@media (max-width: 991px) {
		#header .header-top .header-left > *, #header .header-top .header-right > * { display: none; }
		#header .header-top .header-left > .block-inline, #header .header-top .header-right > .block-inline { display: block; }
		#header .header-top .searchform-popup, #header .header-top #mini-cart { display: none; }
		#header .header-main .searchform-popup, #header .header-main #mini-cart { display: inline-block; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type || 12 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-left,
		#header .header-main .header-right,
		#header .header-main .header-center,
		.fixed-header #header .header-main .header-left,
		.fixed-header #header .header-main .header-right,
		.fixed-header #header .header-main .header-center,
		#header.sticky-header .header-main.sticky .header-left,
		#header.sticky-header .header-main.sticky .header-right,
		#header.sticky-header .header-main.sticky .header-center { padding-top: 0; padding-bottom: 0; }
		#header .main-menu > li.menu-item > a { border-radius: 0; margin-bottom: 0; }
		#header .main-menu .popup { margin-top: 0; }
		#header .main-menu .wide .popup,
		.header-wrapper #header .main-menu .wide .popup > .inner,
		#header .main-menu .narrow .popup > .inner > ul.sub-menu,
		#header .main-menu .narrow ul.sub-menu ul.sub-menu { border-radius: 0; }
		#header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding2']['padding-top'] - 18; ?>px; }
		#header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding2']['padding-top'] - $b['mainmenu-toplevel-padding2']['padding-bottom'] - 1; ?>px; }
		<?php if ( $b['enable-sticky-header'] && isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) : ?>
			#header.sticky-header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding3']['padding-top'] - 18; ?>px; }
			#header.sticky-header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding3']['padding-top'] - $b['mainmenu-toplevel-padding3']['padding-bottom'] - 1; ?>px; }
		<?php endif; ?>
	}
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		#header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding1']['padding-top'] - 18; ?>px; }
		#header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding1']['padding-top'] - $b['mainmenu-toplevel-padding1']['padding-bottom'] - 1; ?>px; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type || 15 == $header_type || 16 == $header_type ) : ?>
	#header .searchform { margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
	@media (max-width: 991px) {
		#header .share-links { display: none; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main.change-logo .main-menu > li.menu-item,
		#header .header-main.change-logo .main-menu > li.menu-item.active,
		#header .header-main.change-logo .main-menu > li.menu-item:hover { margin-top: 0; }
		#header .header-main.change-logo .main-menu > li.menu-item > a,
		#header .header-main.change-logo .main-menu > li.menu-item.active > a,
		#header .header-main.change-logo .main-menu > li.menu-item:hover > a { border-width: 0; }
		#header .show-header-top .main-menu > li.menu-item,
		#header .show-header-top .main-menu > li.menu-item.active,
		#header .show-header-top .main-menu > li.menu-item:hover { margin-top: 0; }
		#header .show-header-top .main-menu > li.menu-item > a,
		#header .show-header-top .main-menu > li.menu-item.active > a,
		#header .show-header-top .main-menu > li.menu-item:hover > a { border-width: 0; }
	}
<?php elseif ( 12 == $header_type ) : ?>
	#header .share-links a { box-shadow: none; }
	#header .share-links a:not(:hover) { background: none; 
	<?php
	if ( $b['header-link-color']['regular'] ) {
		echo 'color: ' . esc_html( $b['header-link-color']['regular'] ); }
	?>
	}
	@media (max-width: 991px) {
		#header .header-main .share-links { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	}
	@media (max-width: 575px) {
		#header .header-main .share-links { display: none; }
	}
<?php elseif ( 14 == $header_type ) : ?>
	#header .main-menu > li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 2px; }

<?php elseif ( 18 == $header_type ) : ?>
	#header .header-contact { border-<?php echo porto_filter_output( $right ); ?>: none; padding-<?php echo porto_filter_output( $right ); ?>: 30px; }

	#header #mini-cart .cart-items { font-size: 11px; font-weight: 400; }
	#mini-cart .minicart-icon { font-size: 24px; }
	#mini-cart .cart-items { background-color: #b7597c; width: 14px; height: 14px; line-height: 14px; font-size: 9px; }
	#mini-cart .cart-head:after { top: -1px; font-size: 16px; }
	#header .searchform-popup .search-toggle { font-size: 20px; line-height: 39px; }
	#header .searchform-popup .search-toggle i:before { content: "\e884"; font-family: "porto"; font-weight: 400; }

	.mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }

	@media (min-width: 992px) {
		#header .header-main .header-right .searchform-popup { margin-left: 18px; margin-right: 18px; }
		.fixed-header #header .header-main .header-left { padding: 25px 0; }
	}

<?php elseif ( 19 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-center { padding-top: 30px; padding-bottom: 30px; }
		#header.logo-center .header-main .header-left,
		#header.logo-center .header-main .header-right { justify-content: center; }
	}
	#header .header-main #main-menu { display: flex; }
	#header.header-19 .searchform-popup .search-toggle { color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>; }
	#header.header-19 .searchform-popup .search-toggle:hover { color: <?php echo esc_html( $b['header-top-link-color']['hover'] ); ?>; }
	#header .searchform { line-height: 36px; font-family: <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif }
	#header .searchform input,
	#header .searchform.searchform-cats input { width: 200px; }
	<?php if ( $b['search-border-radius'] ) : ?>
		#header.header-19 .searchform { border-radius: 4px; border: none; }
		#header .searchform .live-search-list { <?php echo porto_filter_output( $right ); ?>: 0; width: 100%; <?php echo porto_filter_output( $left ); ?>: auto; }
	<?php endif; ?>
	#header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button { height: 36px; }
	<?php if ( 'none' == $b['minicart-type'] || ! class_exists( 'Woocommerce' ) ) : ?>
		#header .header-top { padding: 5px 0; }
	<?php endif; ?>
	#header .header-top .share-links { margin: 5px; }
	#header .searchform input { padding: 0 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
	#header .searchform button { padding: 0 12px; font-size: 12px; }
	#header .header-right > *:not(:last-child),
	#header .top-links>li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
	#header .header-main .header-left { text-align: <?php echo porto_filter_output( $right ); ?> }
	#header .searchform:not(.searchform-cats) input { border-<?php echo porto_filter_output( $right ); ?>: none; }
	#header .header-main { border-bottom: 1px solid rgba(0, 0, 0, 0.075); }
	<?php if ( isset( $b['minicart-type'] ) && 'none' != $b['minicart-type'] && class_exists( 'Woocommerce' ) ) : ?>
		#mini-cart { padding: <?php echo porto_filter_output( $rtl ? '5px 25px 5px 0' : '5px 0 5px 25px' ); ?>; line-height: 38px; border-<?php echo porto_filter_output( $left ); ?>: 1px solid rgba(255, 255, 255, 0.15) }
		#mini-cart .cart-items,
		#mini-cart .cart-head:after { display: none; }
		#mini-cart .cart-items-text { display: inline; text-transform: uppercase; font-weight: 300; vertical-align: middle; }
		#mini-cart .minicart-icon { border-bottom: 18px solid; border-top: none; border-left: 1px solid transparent; border-right: 1px solid transparent; width: 18px; position: relative; top: 2px; margin-right: 6px; border-radius: 0; }
		#mini-cart .minicart-icon:before { content: ''; position: absolute; bottom: 100%; top: auto; left: 50%; margin-left: -5px; border-width: 2px 2px 0 2px; width: 10px; height: 5px; border-radius: 3px 3px 0 0; border-style: solid; }
	<?php endif; ?>

<?php elseif ( 'side' == $header_type ) : ?>
	.header-wrapper #header .side-top { display: block; text-align: <?php echo porto_filter_output( $left ); ?>; }
	.header-wrapper #header .side-top > .container { display: flex; align-items: center; min-height: 0 !important; position: static; }
	.header-wrapper #header .share-links { margin: 0 0 16px; }
	#header .share-links a { width: 30px; height: 30px; margin: 1px; box-shadow: none; }

	@media (min-width: 992px) {
		.admin-bar .header-wrapper #header { min-height: calc(100vh - 32px); }
		.page-wrapper { display: flex; flex-wrap: wrap; }
		.header-wrapper { flex: 0 0 256px; max-width: 256px; }
		.header-wrapper #header { min-height: 100vh; padding: 15px 15px 160px; position: relative; }
		.page-wrapper > .content-wrapper { flex: 1; max-width: calc(100% - 256px); }
		.header-wrapper #header .side-top > .container { padding: 0; width: 100%; }
		.header-wrapper #header .header-main > .container { position: static; padding: 0; width: 100%; display: block; }
		.header-wrapper #header .header-main > .container > * { position: static; display: block; padding: 0; }
		#header .logo { text-align: center; margin: 30px auto; }
		<?php if ( 'advanced' == $b['search-layout'] ) : ?>
			.header-wrapper #header .searchform { margin-bottom: 20px; }
			.header-wrapper #header .searchform input { padding: 0 10px; border-width: 0; width: 190px; }
			.header-wrapper #header .searchform.searchform-cats input { width: 95px; }
			.header-wrapper #header .searchform button { padding: <?php echo porto_filter_output( $rtl ? '0 8px 0 9px' : '0 9px 0 8px' ); ?>; }
			.header-wrapper #header .searchform select { border-width: 0;  padding: 0 5px; width: 93px; }
			.header-wrapper #header .searchform .selectric-cat { width: 93px; }
			.header-wrapper #header .searchform .selectric { border-width: 0; }
			.header-wrapper #header .searchform .selectric .label { padding: 0 5px; }
			.header-wrapper #header .searchform .autocomplete-suggestions { left: -1px; right: -1px; }
		<?php endif; ?>
		.header-wrapper #header .top-links { display: block; font-size: .8em; margin-bottom: 20px; }
		.header-wrapper #header .top-links li.menu-item { display: block; float: none; margin: 0; }
		.header-wrapper #header .top-links li.menu-item:after { display: none; }
		.header-wrapper #header .top-links li.menu-item > a { margin: 0; padding-top: 7px; padding-bottom: 7px; }
		.header-wrapper #header .top-links li.menu-item:first-child > a { border-top-width: 0; }
		.header-wrapper #header .header-contact { margin: 5px 0 20px; white-space: normal; }
		.header-wrapper #header .header-copyright { font-size: .9em; }
		.header-wrapper #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: 0; <?php echo porto_filter_output( $right ); ?>: auto; }
		.header-wrapper .side-bottom { text-align: center; position: absolute; bottom: 0; left: 0; right: 0; margin: 20px 10px; }
		.page-wrapper.side-nav .page-top.fixed-pos { position: fixed; z-index: 1001; width: 100%; box-shadow: 0 1px 0 0 rgba(0, 0, 0, .1); }
		.page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: -32px; }

		<?php if ( $b['side-menu-type'] && 'columns' != $b['side-menu-type'] ) : ?>
			<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { top: <?php echo ( 16 + (float) $b['menu-popup-font']['line-height'] - 30 ) / 2; ?>px }
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
			#header .header-main .header-center { overflow: hidden }
		<?php endif; ?>
	}
	@media (min-width: 1440px) {
		.header-wrapper { flex: 0 0 300px; max-width: 300px; }
		.header-wrapper #header { padding-left: 30px; padding-right: 30px; }
		.page-wrapper > .content-wrapper { max-width: calc(100% - 300px); }
		.header-wrapper #header .searchform input { width: 204px; }
		#header .header-copyright { clear: both; }
		.header-wrapper .side-bottom { display: flex; align-items: center; margin-left: 30px; justify-content: center; flex-wrap: wrap; margin-right: 30px; }
		.side-bottom .header-contact { order: 2; margin-<?php echo porto_filter_output( $left ); ?>: auto; }
	}

	<?php if ( empty( $b['side-menu-type'] ) ) : ?>
		.sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $left ); ?>: 30px; }
		.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $right ); ?>: 30px; }
		@media (max-width: 1439px) {
			.sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
			.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
		}
	<?php endif; ?>
	@media (max-width: 1439px) {
		.header-wrapper #header .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
	}

	.sidebar-menu { margin-bottom: 20px; }
	.sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top: none; }
	.sidebar-menu > li.menu-item > a { margin-left: 0; margin-right: 0; }
	.sidebar-menu > li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: 5px; }
	.sidebar-menu > li.menu-item:last-child:hover { border-radius: 0; }
	<?php if ( 'columns' != $b['side-menu-type'] ) : ?>
		.sidebar-menu > li.has-sub:before { content: ''; position: absolute; top: 0; bottom: 0; <?php echo porto_filter_output( $left ); ?>: 100%; width: 30px; }
	<?php else : ?>
		.sidebar-menu > li.has-sub > a:before { content: ''; position: absolute; top: 0; bottom: 0; <?php echo porto_filter_output( $left ); ?>: 100%; width: 30px; }
	<?php endif; ?>
	.sidebar-menu .menu-custom-block a { margin-left: 0; margin-right: 0; padding-left: 5px; padding-right: 5px; }
	.sidebar-menu .menu-custom-block .fas,
	.sidebar-menu .menu-custom-block .fab
	.sidebar-menu .menu-custom-block .far { width: 18px; text-align: center; }
	.sidebar-menu .menu-custom-block .fas,
	.sidebar-menu .menu-custom-block .fab,
	.sidebar-menu .menu-custom-block .far,
	.sidebar-menu .menu-custom-block .avatar { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
	.sidebar-menu .menu-custom-block > .avatar img { margin-top: -5px; }

	@media (max-width: 991px) {
		.header-wrapper #header .side-top { padding: 10px 0 0; }
		.header-wrapper #header .side-top .porto-view-switcher { float: <?php echo porto_filter_output( $left ); ?>; }
		.header-wrapper #header .side-top .mini-cart { float: <?php echo porto_filter_output( $right ); ?>; }
		.header-wrapper #header .logo { margin-bottom: 5px; }
		.header-wrapper #header .sidebar-menu { display: none; }
		.header-wrapper #header .share-links { margin: <?php echo porto_filter_output( $rtl ? '0 10px 0 0' : '0 0 0 10px' ); ?>; }
		.header-wrapper #header .share-links a:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
		.header-wrapper #header .header-copyright { display: none; }

		.header-wrapper #header .side-top { padding-top: 0; }
		.header-wrapper #header .side-top > .container > * { display: none !important; }
		.header-wrapper #header .side-top > .container > .mini-cart { display: block !important; position: absolute !important; top: 50%; bottom: 50%; height: 26px; margin-top: -12px; <?php echo porto_filter_output( $right ); ?>: 15px; z-index: 1001; }
		.header-wrapper #header .logo { margin: 0; }
		.header-wrapper #header .share-links { display: none; }
		.header-wrapper #header .show-minicart .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 80px; }
	}

	@media (min-width: 992px) {
		body.boxed.body-side .header-wrapper { <?php echo porto_filter_output( $left ); ?>: -276px; margin-top: -30px; }
		body.boxed.body-side .page-wrapper.side-nav { max-width: 100%; }
		body.boxed.body-side .page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $left ); ?> : 0; }
		body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { width: auto; }
	}

	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.header-wrapper a { color: inherit }
		#header .header-minicart > a { display: inline-block; font-size: 20px; min-width: 32px; text-align: center; margin-<?php echo porto_filter_output( $right ); ?>: .5rem; }
		#mini-cart .minicart-icon { font-size: 20px; }
		#mini-cart .cart-head { padding: 0; text-transform: uppercase; }
		#mini-cart .cart-subtotal { font-size: 13px; font-weight: 500; margin-<?php echo porto_filter_output( $left ); ?>: 1px; }
		#header .side-top .header-minicart { margin-<?php echo porto_filter_output( $left ); ?>: auto; }
	<?php endif; ?>
	#header .searchform-popup .search-toggle { font-size: 16px; }
	@media (max-width: 991px) {
		#header .header-right { width: 1%; white-space: nowrap; }
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $right ); ?> }
		#header .mobile-toggle {
			margin-<?php echo porto_filter_output( $left ); ?>: 0;
		}
		.fixed-header #header .header-main { padding-top: 5px; padding-bottom: 5px; }
		#mini-cart .minicart-icon { font-size: 22px; }
		#mini-cart .cart-head:after { display: none; }
	}

/* header builder side type */
	<?php
elseif ( empty( $header_type ) && porto_header_type_is_side() ) :
	$current_layout      = porto_header_builder_layout();
	$side_position       = empty( $porto_settings['header-side-position'] ) ? 'left' : 'right';
	$side_mobile_visible = isset( $current_layout['side_header_toggle'] ) && $current_layout['side_header_toggle'];
	?>
	<?php if ( ! $side_mobile_visible ) : ?>
		@media (min-width: 992px) {
	<?php endif; ?>
		.header-wrapper #header { display: flex; flex-direction: column; position: fixed; z-index: 1110; top: 0; <?php echo porto_filter_output( $side_position ); ?>: 0; <?php echo 'left' === $side_position ? 'right' : 'left'; ?>: auto; width: 100%; max-width: <?php echo isset( $current_layout['side_header_width'] ) ? $current_layout['side_header_width'] : '255'; ?>px; box-shadow: 0 0 30px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.05)', 'rgba(255, 255, 255, 0.03)' ); ?> }
		<?php if ( $side_mobile_visible ) : ?>
			@media (max-width: <?php echo 90 + ( isset( $current_layout['side_header_width'] ) ? (int) $current_layout['side_header_width'] : 255 ); ?>px) {
				.header-wrapper #header { max-width: calc(100% - 90px) }
			}
		<?php endif; ?>
		#header .header-main { display: flex; height: 100%; }
		#header .header-main .header-row { height: 100%; align-items: flex-start; }
		#header .header-main .header-row > div { align-items: flex-start; }
		#header .header-center { flex: 1; text-align: center; }
		.header-row .header-col > * { flex: 0 0 100%; margin-bottom: 1em; }
		.header-bottom { flex-wrap: wrap; padding: 15px 0 30px; }
		.header-copyright { text-align: center; width: 100%; font-size: .8em; }
		.page-wrapper.side-nav .page-top.fixed-pos { position: fixed; z-index: 1001; width: 100%; box-shadow: 0 1px 0 0 rgba(0, 0, 0, .1); }
		#header.sticky-header .header-main.sticky { position: static; }

		.header-wrapper { z-index: 1002; }
		<?php if ( ! isset( $current_layout['side_header_toggle'] ) || ! $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $side_position ); ?>: <?php echo isset( $current_layout['side_header_width'] ) ? $current_layout['side_header_width'] : '255'; ?>px; padding-<?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: 0; }
		<?php endif; ?>
		#header .logo { padding: 7vh 0 5vh; }

		<?php
		if ( $b['mainmenu-popup-bg-color'] && 'transparent' != $b['mainmenu-popup-bg-color'] ) :
			$main_menu_popup_bg_arr = $porto_color_lib->hexToRGB( $b['mainmenu-popup-bg-color'], false );
			?>
			<?php if ( ! $side_mobile_visible ) : ?>
				#header .sidebar-menu > li > a
			<?php else : ?>
				#header .sidebar-menu li > a
			<?php endif; ?>
				{ <?php if ( ( (int) $main_menu_popup_bg_arr[0] * 256 + (int) $main_menu_popup_bg_arr[1] * 16 + (int) $main_menu_popup_bg_arr[2] ) < ( 79 * 256 + 255 * 16 + 255 ) ) : ?>
					border-bottom: 1px solid <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
				<?php else : ?>
					border-bottom: 1px solid <?php echo esc_html( $porto_color_lib->darken( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
				<?php endif; ?> }
		<?php endif; ?>
		#header .sidebar-menu li:last-child > a { border-bottom: none; }

		<?php if ( $b['side-menu-type'] && 'columns' != $b['side-menu-type'] ) : ?>
			.header-wrapper #header { height: 100vh; justify-content: center; }
			.admin-bar #header { height: calc(100vh - 32px); top: 32px; }
			#header .header-main { overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
			#header .header-main::-webkit-scrollbar,
			#header .side-menu-slide ul.sub-menu::-webkit-scrollbar { width: 5px; }
			#header .header-main::-webkit-scrollbar-thumb,
			#header .side-menu-slide ul.sub-menu::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
			<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { top: <?php echo ( 16 + (float) $b['menu-popup-font']['line-height'] - 30 ) / 2; ?>px }
			<?php endif; ?>
			<?php if ( $b['menu-font']['line-height'] ) : ?>
				#header .sidebar-menu > li.menu-item > .arrow { top: <?php echo ( 22 + (float) $b['menu-font']['line-height'] - 30 ) / 2; ?>px }
			<?php endif; ?>
			<?php if ( class_exists( 'Woocommerce' ) ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: 10px }
			<?php endif; ?>
			#header .sidebar-menu li.menu-item:hover > .arrow { <?php echo porto_filter_output( $right ); ?>: 5px }
		<?php elseif ( 'columns' == $b['side-menu-type'] ) : ?>
			.header-wrapper #header { height: 100vh; justify-content: center; }
			.admin-bar #header { height: calc(100vh - 32px); top: 32px; }
			#header .header-main .header-row,
			#header .header-main .header-row > div { align-items: center; height: 100%; }
		<?php else : ?>
			.header-wrapper #header { min-height: 100vh; }
			.admin-bar #header { min-height: calc(100vh - 32px); top: 0; }
			#header .header-main { flex: 1; }
			#header.initialize { position: absolute; }
		<?php endif; ?>


		#header .sidebar-menu li.menu-item > .arrow { transition: <?php echo porto_filter_output( $right ); ?> .25s; font-size: .8em; }
		#header .sidebar-menu li.menu-item li.menu-item > a { font-size: inherit; text-transform: inherit; font-weight: inherit; }
		.side-header-overlay { width: 100%; height: 0; top: 0; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.8); position: fixed; z-index: 10; opacity: 0; }
		<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
			#header .header-main .header-row { padding: 0; margin: 0 15px; height: 100%; align-items: center; overflow: hidden; }
			#header .header-main .header-row > div { align-items: center; height: 100%; }
			<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
				#header .sidebar-menu .narrow li.menu-item:hover > a { background: none; }
				#header .sidebar-menu li.menu-item:not(:last-child) > a { border-bottom: 1px solid <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>; }
			<?php endif; ?>
		<?php endif; ?>
	<?php if ( ! $side_mobile_visible ) : ?>
		}
	<?php endif; ?>

	<?php if ( ! $side_mobile_visible ) : // default side layout ?>
		@media (min-width: 992px) {
			#header.fixed-bottom { position: fixed; bottom: 0; top: auto; }
			.forcefullwidth_wrapper_tp_banner .rev_slider_wrapper { width: 100% !important; left: auto !important; }
		}
		@media (max-width: 991px) {
			#header .sidebar-menu,
			#header .header-copyright { display: none; }
		}
		<?php
	else : // fixed
		$side_narrow_bar_bg = ! empty( $b['header-bg']['background-color'] ) && 'transparent' != $b['header-bg']['background-color'] ? $b['header-bg']['background-color'] : '#fff';
		?>
		#header .mobile-toggle { display: none; }
		.header-wrapper #header { <?php echo porto_filter_output( $side_position ); ?>: -<?php echo isset( $current_layout['side_header_width'] ) ? (int) $current_layout['side_header_width'] + 10 : '265'; ?>px; <?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: auto; transition: <?php echo porto_filter_output( $side_position ); ?> .4s cubic-bezier(0.55, 0, 0.1, 1); background: <?php echo esc_html( isset( $b['header-bg']['background-color'] ) ? $b['header-bg']['background-color'] : '' ); ?> }
		#header.side-header-visible { <?php echo porto_filter_output( $side_position ); ?>: 90px; }
		.forcefullwidth_wrapper_tp_banner .rev_slider_wrapper { width: 100% !important; left: auto !important; }
		<?php if ( 'side' == $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $side_position ); ?>: 90px; padding-<?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: 0; }
		<?php elseif ( 'top' == $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav>* { padding-left: 0; padding-right: 0; }
			.header-wrapper #header { z-index: 1112; }
		<?php endif; ?>
			.side-header-narrow-bar { display: flex; flex-direction: column; background-color: <?php echo esc_html( $side_narrow_bar_bg ); ?>; width: 90px; position: <?php echo 'side' == $current_layout['side_header_toggle'] ? 'fixed' : 'absolute'; ?>; top: 0; <?php echo porto_filter_output( $side_position ); ?>: 0; height: 100%; z-index: 1111; text-align: center; }
			.side-header-narrow-bar.side-header-narrow-bar-sticky { position: fixed; }
			@media (min-width: 601px) {
			<?php if ( 'top' == $current_layout['side_header_toggle'] ) : ?>
				.admin-bar .side-header-narrow-bar.side-header-narrow-bar-sticky { top: 32px; }
			<?php else : ?>
				.admin-bar .side-header-narrow-bar { top: 32px; height: calc(100% - 32px); }
			<?php endif; ?>
			}
			.side-header-narrow-bar:after { content: ""; width: 1px; height: 100%; top: 0; bottom: 0; left: auto; right: 0; background: <?php echo esc_html( $porto_color_lib->isColorDark( $side_narrow_bar_bg ) ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.06)' ); ?>; position: absolute; }
			.side-header-narrow-bar-logo > a { display: inline-block; margin: 1em; max-width: 120px; }
			.side-header-narrow-bar-content { display: flex; height: 100%; align-items: center; flex: 1; justify-content: center; }
			.side-header-narrow-bar-content-vertical { transform: rotate(-90deg); white-space: nowrap; text-transform: uppercase; }
			.side-header-narrow-bar-side .side-header-narrow-bar-toggle { padding-bottom: 15px; }

			.side-header-narrow-bar-top { height: auto; width: 100%; flex-direction: row; align-items: center; padding: 0 <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
			.side-header-narrow-bar-top.side-header-narrow-bar-sticky { border-bottom: 1px solid <?php echo porto_filter_output( $porto_color_lib->isColorDark( $side_narrow_bar_bg ) ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.06)' ); ?>; }
			.side-header-narrow-bar-top + #header.side-header-visible { <?php echo porto_filter_output( $side_position ); ?>: 0; }
			.side-header-narrow-bar-top .side-header-narrow-bar-logo > a { margin: 1.8em 0; }

		.side-header-visible + .side-header-overlay { height: 100vh; opacity: 1; }

	<?php endif; ?>
<?php endif; ?>

/* menu type */
<?php if ( porto_header_type_is_side() && 'columns' == $b['side-menu-type'] ) : ?>
	.header-side-nav .sidebar-menu .wide .popup > .inner:before,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:before,
	.header-side-nav .sidebar-menu .wide .popup > .inner:after,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:after { content: ''; position: absolute; bottom: 100%; height: 50vh; width: calc(100% + 2px); left: -1px; background-color: inherit; border-width: 0 1px 0 1px; border-style: solid; border-color: inherit; }
	.header-side-nav .sidebar-menu .wide .popup > .inner:after,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:after { bottom: auto; top: 100%; }
	.header-side-nav .sidebar-menu .wide .popup > .inner,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu { border-color: rgba(0, 0, 0, .03); border-width: 0 1px 0 1px; border-style: solid; position: relative; }
<?php endif; ?>
<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
	/*.header-side-nav, */.main-sidebar-menu .sidebar-menu-wrap { overflow: hidden }
<?php elseif ( 'columns' == $b['side-menu-type'] ) : ?>
	.header-side-nav .side-menu-columns, .main-sidebar-menu .side-menu-columns { position: relative }
<?php endif; ?>

<?php if ( ! isset( $b['show-sticky-contact-info'] ) || ! $b['show-sticky-contact-info'] ) : ?>
	#header.sticky-header .header-contact { display: none; }
<?php endif; ?>

/*------------------ footer ---------------------- */
<?php if ( $b['border-radius'] || ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
	.footer .wysija-input { border-radius: <?php echo porto_filter_output( $rtl ? '0 30px 30px 0' : '30px 0 0 30px' ); ?>; padding-<?php echo porto_filter_output( $left ); ?>: 1rem; }
	.footer .wysija-submit { border-radius: <?php echo porto_filter_output( $rtl ? '30px 0 0 30px' : '0 30px 30px 0' ); ?> }
<?php endif; ?>


/*------------------ theme ---------------------- */
/*------ Grid Gutter Width ------- */
<?php if ( porto_header_type_is_side() ) : ?>
@media (min-width: 992px) {
	body.boxed.body-side { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	body.boxed.body-side.modal-open { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px !important; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px !important; }
	body.boxed.body-side .page-wrapper.side-nav .container { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { <?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px; <?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
}
<?php endif; ?>

/*------ Screen Large Variable ------- */
@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
	.ccols-xl-2 > * {
		--porto-cw: 50%;
	}
	.ccols-xl-3 > * {
		--porto-cw: 33.3333%;
	}
	.ccols-xl-4 > * {
		--porto-cw: 25%;
	}
	.ccols-xl-5 > * {
		--porto-cw: 20%;
	}
	.ccols-xl-6 > * {
		--porto-cw: 16.6666%;
	}
	.ccols-xl-7 > * {
		--porto-cw: 14.2857%;
	}
	.ccols-xl-8 > * {
		--porto-cw: 12.5%;
	}
	.ccols-xl-9 > * {
		--porto-cw: 11.1111%;
	}
	.ccols-xl-10 > * {
		--porto-cw: 10%;
	}
}
@media (min-width: 1400px) {
	.ccols-sl-10 > * {
		--porto-cw: 10%;
	}
	.ccols-sl-9 > * {
		--porto-cw: 11.1111%;
	}
	.ccols-sl-8 > * {
		--porto-cw: 12.5%;
	}
	.ccols-sl-7 > * {
		--porto-cw: 14.2857%;
	}
}

@media <?php echo porto_filter_output( $screen_large ); ?> {
	/*#header .header-top .porto-view-switcher > li.menu-item > a,
	#header .header-top .top-links > li.menu-item > a { padding-top: 3px !important; padding-bottom: 3px !important; }*/

	.mega-menu > li.menu-item > a { padding: 9px 9px 8px; }

	.widget_sidebar_menu .widget-title { font-size: 0.8571em; line-height: 13px; padding: 10px 15px; }

	.sidebar-menu > li.menu-item > a { font-size: 0.9286em; line-height: 17px; padding: 9px 5px; }
	.sidebar-menu .menu-custom-block a { font-size: 0.9286em; line-height: 16px; padding: 9px 5px; }
	.sidebar-menu > li.menu-item .popup:before { top: 11px; }

	.porto-links-block { font-size: 13px; }
	/*.porto-links-block .links-title { padding: 8px 12px 6px; }
	.porto-links-block li.porto-links-item > a,
	.porto-links-block li.porto-links-item > span { padding: 7px 5px; line-height: 19px; margin: 0 7px -1px; }*/

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	ul.pcols-md-6 li.product-col { width: 16.6666% }
	ul.pwidth-md-6 .product-image { font-size: 0.8em; }
	ul.pwidth-md-6 .add-links { font-size: 0.85em; }
	ul.pcols-md-5 li.product-col { width: 20% }
	ul.pwidth-md-5 .product-image { font-size: 0.9em; }
	ul.pwidth-md-5 .add-links { font-size: 0.95em; }
	ul.pcols-md-4 li.product-col { width: 25% }
	ul.pwidth-md-4 .product-image { font-size: 1em; }
	ul.pwidth-md-4 .add-links { font-size: 1em; }
	ul.pcols-md-3 li.product-col { width: 33.3333% }
	ul.pwidth-md-3 .product-image { font-size: 1.15em; }
	ul.pwidth-md-3 .add-links { font-size: 1em; }
	ul.pcols-md-2 li.product-col { width: 50% }
	ul.pwidth-md-2 .product-image { font-size: 1.4em; }
	ul.pwidth-md-2 .add-links { font-size: 1em; }
	ul.pcols-md-1 li.product-col { width: 100% }
<?php endif; ?>
}

@media (min-width: 992px) and <?php echo porto_filter_output( $screen_large ); ?> {
	.portfolio-row .portfolio-col-6 { width: 20%; }
	.portfolio-row .portfolio-col-6.w2 { width: 40%; }

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	.column2 ul.pwidth-md-5 .product-image { font-size: 0.75em; }
	.column2 ul.pwidth-md-5 .add-links { font-size: 0.8em; }
	.column2 ul.pwidth-md-4 .product-image { font-size: 0.8em; }
	.column2 ul.pwidth-md-4 .add-links { font-size: 0.9em; }
	.column2 ul.pwidth-md-3 .product-image { font-size: 0.9em; }
	.column2 ul.pwidth-md-3 .add-links { font-size: 1em; }
	.column2 ul.pwidth-md-2 .product-image { font-size: 1.1em; }
	.column2 ul.pwidth-md-2 .add-links { font-size: 1em; }

	.column2 .shop-loop-before .woocommerce-pagination ul { margin-<?php echo porto_filter_output( $left ); ?>: -5px; }

	.quickview-wrap { width: 720px; }
	ul.product_list_widget li .product-image { width: 70px; flex: 0 0 70px; margin-<?php echo porto_filter_output( $right ); ?>: 15px }
	ul.product_list_widget li .product-details { width: calc(100% - 85px); }
<?php endif; ?>
}

@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
	.column2 .portfolio-row .portfolio-col-4 { width: 33.3333%; }
	.column2 .portfolio-row .portfolio-col-4.w2 { width: 66.6666%; }
	.column2 .portfolio-row .portfolio-col-5,
	.column2 .portfolio-row .portfolio-col-6 { width: 25%; }
	.column2 .portfolio-row .portfolio-col-5.w2,
	.column2 .portfolio-row .portfolio-col-6.w2 { width: 50%; }
}

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	@media (min-width: 768px) and (max-width: 991px) {
		ul.pcols-sm-4 li.product-col { width: 25% }
		ul.pcols-sm-3 li.product-col { width: 33.3333% }
		ul.pcols-sm-2 li.product-col { width: 50% }
		ul.pcols-sm-1 li.product-col { width: 100% }
	}
	@media (max-width: 767px) {
		ul.pcols-xs-4 li.product-col { width: 25% }
		ul.pcols-xs-3 li.product-col { width: 33.3333% }
		ul.pwidth-xs-3 .product-image { font-size: .85em; }
		ul.pwidth-xs-3 .add-links { font-size: .85em; }
		ul.pcols-xs-2 li.product-col { width: 50% }
		ul.pwidth-xs-2 .product-image { font-size: 1em; }
		ul.pwidth-xs-2 .add-links { font-size: 1em; }
		ul.pcols-xs-1 li.product-col { width: 100% }
		ul.pwidth-xs-1 .product-image { font-size: 1.2em; }
		ul.pwidth-xs-1 .add-links { font-size: 1em; }
	}
	@media (max-width: 575px) {
		ul.pcols-ls-2 li.product-col { width: 50% }
		ul.pwidth-ls-2 .product-image { font-size: .8em; }
		ul.pwidth-ls-2 .add-links { font-size: .85em; }
		ul.pcols-ls-1 li.product-col { width: 100% }
		ul.pwidth-ls-1 .product-image { font-size: 1.1em; }
		ul.pwidth-ls-1 .add-links { font-size: 1em; }
	}
	@media (min-width: 576px) {
		ul.list li.product { width: 100% }
	}
<?php endif; ?>

/*------ Border Radius ------- */
<?php if ( ! $b['border-radius'] && class_exists( 'Woocommerce' ) ) : ?>
	.wishlist_table .add_to_cart.button,
	.yith-wcwl-popup-button a.add_to_wishlist,
	.wishlist_table a.ask-an-estimate-button,
	.wishlist-title a.show-title-form,
	.hidden-title-form a.hide-title-form,
	.woocommerce .yith-wcwl-wishlist-new button,
	.wishlist_manage_table a.create-new-wishlist,
	.wishlist_manage_table button.submit-wishlist-changes,
	.yith-wcwl-wishlist-search-form button.wishlist-search-button { border-radius: 0; }
<?php endif; ?>

/*------ Thumb Padding ------- */
<?php if ( $b['thumb-padding'] ) : ?>
	.mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.page-wrapper .fdm-item-image,
	.thumb-info-side-image .thumb-info-side-image-wrapper,
	.flickr_badge_image,
	.wpb_content_element .flickr_badge_image { padding: 4px; }
	.img-thumbnail .zoom { <?php echo porto_filter_output( $right ); ?>: 8px; bottom: 8px; }
	.thumb-info .thumb-info-wrapper { margin: 4px; }
	.thumb-info .thumb-info-wrapper:after { bottom: -4px; top: -4px; left: -4px; right: -4px; }

	.flickr_badge_image,
	.wpb_content_element .flickr_badge_image { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#ddd' ); ?>; }

	.owl-carousel .img-thumbnail,
	.owl-carousel .owl-nav,
	.owl-carousel .thumb-info { max-width: 99.8%; }
	.thumb-info { background-color: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
	.thumb-info-social-icons { border-top: <?php echo porto_if_light( '1px dotted #ddd', '1px solid ' . $porto_color_lib->lighten( $dark_bg, 12 ) ); ?>; }

	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.yith-wcbm-badge { margin: 5px; }
		.yith-wcbm-badge img { margin: -5px !important; }
		.product-images .zoom { <?php echo porto_filter_output( $right ); ?>: 8px; bottom: 8px; }

		.product-image-slider.owl-carousel .img-thumbnail,
		.product-thumbs-slider.owl-carousel .img-thumbnail { width: 99.8%; padding: 3px; }

		.product-image { padding: 0.2381em; }

		.widget_recent_reviews .product_list_widget li img { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#ddd' ); ?>; padding: 3px; }

		.product-nav .product-popup .product-image,
		ul.product_list_widget li .product-image { padding: 3px; }
	<?php endif; ?>
<?php else : ?>
	.page-wrapper .fdm-item-image,
	.thumb-info { border-width: 0; background: none; }
	.thumb-info-caption .thumb-info-caption-text { padding: 15px 0; margin-bottom: 0; }
	.thumb-info-social-icons { padding: 0; }
	.thumb-info-social-icons:first-child { padding: 10px 0; }
	.thumb-info .share-links a { background: <?php echo esc_html( $b['skin-color'] ); ?> }
	.thumb-info .share-links a:hover { opacity: .9 }
<?php endif; ?>
.thumb-info .thumb-info-wrapper:after { background: <?php echo porto_if_light( 'rgba(33, 37, 41, 0.8)', 'rgba(' . $porto_color_lib->hexToRGB( $color_dark ) . ', 0.9)' ); ?>; }

/*------ Dark version ------- */
<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
	#buddypress form#whats-new-form textarea { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo porto_if_dark( '#999', '#777' ); ?>; }
	#buddypress .activity-list li.load-more a:hover,
	#buddypress .activity-list li.load-newest a:hover { color: <?php echo porto_if_dark( '#999', '#333' ); ?>; }
	#buddypress a.bp-primary-action span,
	#buddypress #reply-title small a span { background: <?php echo porto_if_dark( '#555', '#fff' ); ?>; <?php echo porto_if_dark( '', 'color: #999;' ); ?> }
	#buddypress a.bp-primary-action:hover span,
	#buddypress #reply-title small a:hover span { background: <?php echo porto_if_dark( '#777', '#fff' ); ?>; <?php echo porto_if_dark( '', 'color: #999;' ); ?> }
	#buddypress div.activity-comments ul li { border-top: 1px solid <?php echo porto_if_dark( $color_dark_3, '#eee' ); ?>; }
	#buddypress div.activity-comments form .ac-textarea { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo porto_if_dark( '#999', '#777' ); ?>; border: 1px solid <?php echo porto_if_dark( $color_dark_3, '#ccc' ); ?>; }
	#buddypress div.activity-comments form textarea { color: <?php echo porto_if_dark( '#999', '#777' ); ?>; }
	#buddypress #pass-strength-result { border-color: <?php echo porto_if_dark( $color_dark_4, '#ddd' ); ?>; }
	#buddypress .standard-form textarea,
	#buddypress .standard-form input[type=text],
	#buddypress .standard-form input[type=color],
	#buddypress .standard-form input[type=date],
	#buddypress .standard-form input[type=datetime],
	#buddypress .standard-form input[type=datetime-local],
	#buddypress .standard-form input[type=email],
	#buddypress .standard-form input[type=month],
	#buddypress .standard-form input[type=number],
	#buddypress .standard-form input[type=range],
	#buddypress .standard-form input[type=search],
	#buddypress .standard-form input[type=tel],
	#buddypress .standard-form input[type=time],
	#buddypress .standard-form input[type=url],
	#buddypress .standard-form input[type=week],
	#buddypress .standard-form select,
	#buddypress .standard-form input[type=password],
	#buddypress .dir-search input[type=search],
	#buddypress .dir-search input[type=text],
	#buddypress .groups-members-search input[type=search],
	#buddypress .groups-members-search input[type=text] {
	<?php if ( $dark ) : ?>
		border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>;
		background: <?php echo esc_html( $color_dark_3 ); ?>;
		color: #999;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#999', '#777' ); ?>;
	}
	#buddypress .standard-form input:focus,
	#buddypress .standard-form textarea:focus,
	#buddypress .standard-form select:focus {
	<?php if ( $dark ) : ?>
		background: $color-dark-3;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#999', '#777' ); ?>;
	}
	#buddypress table.forum tr td.label {
	<?php if ( $dark ) : ?>
		border-<?php echo porto_filter_output( $right ); ?>-width: 0;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#fff', '#777' ); ?>;
	}
	#buddypress div.item-list-tabs ul li.selected a span,
	#buddypress div.item-list-tabs ul li.current a span {
		border-color: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>;
	<?php if ( $dark ) : ?>
		background-color: <?php echo esc_html( $color_dark_3 ); ?>;
	<?php endif; ?>
	}
	<?php if ( $dark ) : ?>
		#buddypress div.pagination .pagination-links a,
		#buddypress div.pagination .pagination-links span.current { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.pagination .pagination-links a.dots,
		#buddypress div.pagination .pagination-links span.current.dots { background: transparent; }
		#buddypress .activity-list li.load-more a,
		#buddypress .activity-list li.load-newest a { color: #777; }
		#buddypress .activity-list li.new_forum_post .activity-content .activity-inner,
		#buddypress .activity-list li.new_forum_topic .activity-content .activity-inner { border-<?php echo porto_filter_output( $left ); ?>: 2px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress .activity-list .activity-content img.thumbnail { border: 2px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress .activity-list li.load-more,
		#buddypress .activity-list li.load-newest { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.ac-reply-avatar img { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress #pass-strength-result { background-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div#invite-list { background: transparent; }
		#buddypress table.notifications tr.alt td,
		#buddypress table.notifications-settings tr.alt td,
		#buddypress table.profile-settings tr.alt td,
		#buddypress table.profile-fields tr.alt td,
		#buddypress table.wp-profile-fields tr.alt td,
		#buddypress table.messages-notices tr.alt td,
		#buddypress table.forum tr.alt td { background: transparent; }
		#buddypress ul.item-list { border-top: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress ul.item-list li { border-bottom: 1px solid <?php echo esc_html( $color_dark_3 ); ?> }
		#buddypress div.item-list-tabs ul li a span { background: <?php echo esc_html( $color_dark_3 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.item-list-tabs ul li.selected a span,
		#buddypress div.item-list-tabs ul li.current a span,
		#buddypress div.item-list-tabs ul li a:hover span { background-color: <?php echo esc_html( $color_dark_3 ); ?>; border-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div#message-thread div.alt { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		.bp-avatar-nav ul.avatar-nav-items li.current { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		.bp-avatar-nav ul { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#drag-drop-area { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#buddypress input[type="submit"].pending:hover,
		#buddypress input[type="button"].pending:hover,
		#buddypress input[type="reset"].pending:hover,
		#buddypress button.pending:hover,
		#buddypress div.pending a:hover,
		#buddypress a.disabled:hover { border-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress ul#topic-post-list li.alt { background: transparent; }
		#buddypress table.notifications thead tr,
		#buddypress table.notifications-settings thead tr,
		#buddypress table.profile-settings thead tr,
		#buddypress table.profile-fields thead tr,
		#buddypress table.wp-profile-fields thead tr,
		#buddypress table.messages-notices thead tr,
		#buddypress table.forum thead tr { background: <?php echo esc_html( $color_dark_3 ); ?>; }

		#bbpress-forums div.even, #bbpress-forums ul.even { background-color: <?php echo esc_html( $color_dark ); ?>; }
		#bbpress-forums div.odd, #bbpress-forums ul.odd { background-color: <?php echo esc_html( $color_dark_2 ); ?>; }
		#bbpress-forums div.bbp-forum-header,
		#bbpress-forums div.bbp-topic-header,
		#bbpress-forums div.bbp-reply-header { background-color: <?php echo esc_html( $color_dark_5 ); ?>; }
		#bbpress-forums .status-trash.even, #bbpress-forums .status-spam.even { background-color: <?php echo esc_html( $color_dark ); ?>; }
		#bbpress-forums .status-trash.odd, #bbpress-forums .status-spam.odd { background-color: <?php echo esc_html( $color_dark_2 ); ?>; }
		#bbpress-forums ul.bbp-lead-topic,
		#bbpress-forums ul.bbp-topics,
		#bbpress-forums ul.bbp-forums,
		#bbpress-forums ul.bbp-replies,
		#bbpress-forums ul.bbp-search-results { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums li.bbp-header,
		#bbpress-forums li.bbp-footer { background: <?php echo esc_html( $color_dark_3 ); ?>; border-top: <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums li.bbp-header { background: <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums .bbp-forums-list { border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums li.bbp-body ul.forum,
		#bbpress-forums li.bbp-body ul.topic { border-top: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		div.bbp-forum-header,
		div.bbp-topic-header,
		div.bbp-reply-header { border-top: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums div.bbp-topic-content code,
		#bbpress-forums div.bbp-topic-content pre,
		#bbpress-forums div.bbp-reply-content code,
		#bbpress-forums div.bbp-reply-content pre  { background-color: <?php echo esc_html( $color_dark_4 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		.bbp-pagination-links a,
		.bbp-pagination-links span.current,
		.bbp-topic-pagination a { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		.bbp-pagination-links a.dots,
		.bbp-pagination-links span.current.dots,
		.bbp-topic-pagination a.dots { background: transparent; }
		#bbpress-forums fieldset.bbp-form { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		body.topic-edit .bbp-topic-form div.avatar img,
		body.reply-edit .bbp-reply-form div.avatar img,
		body.single-forum .bbp-topic-form div.avatar img,
		body.single-reply .bbp-reply-form div.avatar img { border: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; background-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums  div.bbp-the-content-wrapper div.quicktags-toolbar { background: <?php echo esc_html( $color_dark_4 ); ?>; border-bottom-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset input,
		#bbpress-forums #bbp-your-profile fieldset textarea { background: <?php echo esc_html( $color_dark_3 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset input:focus,
		#bbpress-forums #bbp-your-profile fieldset textarea:focus { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset span.description { border: transparent 1px solid; background-color: transparent; }
		.bbp-topics-front ul.super-sticky,
		.bbp-topics ul.super-sticky,
		.bbp-topics ul.sticky,
		.bbp-forum-content ul.sticky { background-color: <?php echo esc_html( $color_dark_3 ); ?> !important; }
		#bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log,
		#bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log,
		#bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log { border-top: 1px dotted <?php echo esc_html( $color_dark_4 ); ?>; }
		.activity-list li.bbp_topic_create .activity-content .activity-inner,
		.activity-list li.bbp_reply_create .activity-content .activity-inner { border-<?php echo porto_filter_output( $left ); ?>: 2px solid <?php echo esc_html( $color_dark_4 ); ?>; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $dark ) : ?>
	.dir-arrow { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows-dark.png) no-repeat 0 0; }
	.dir-arrow.arrow-light { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows.png) no-repeat 0 0; }
	.elementor hr, hr, .divider { background: rgba(255, 255, 255, 0.06); }
	hr.light { background: rgba(0, 0, 0, 0.06); }
	.featured-boxes-style-7 .featured-box .icon-featured:after { box-shadow: 3px 3px <?php echo esc_html( $porto_color_lib->darken( $color_dark, 3 ) ); ?>; }
	.porto-concept { background-image: url(<?php echo PORTO_URI; ?>/images/concept-dark.png); }
	.porto-concept .process-image { background-image: url(<?php echo PORTO_URI; ?>/images/concept-item-dark.png); }
	.porto-concept .project-image { background-image: url(<?php echo PORTO_URI; ?>/images/concept-item-dark.png); }
	.porto-concept .sun { background-image: url(<?php echo PORTO_URI; ?>/images/concept-icons-dark.png); }
	.porto-concept .cloud { background-image: url(<?php echo PORTO_URI; ?>/images/concept-icons-dark.png); }
	.porto-map-section { background-image: url(<?php echo PORTO_URI; ?>/images/map-dark.png); }
	.porto-map-section .map-content { background-color: rgba(33, 38, 45, 0.5); }
	.slider-title .line, .section-title .line { background-image:linear-gradient(to <?php echo porto_filter_output( $right ); ?>, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.15) 70%, rgba(255, 255, 255, 0) 100%); }
	.porto-radio .porto-control-label::after { border-width: 0; }
	@media (max-width: 767px) {
		.resp-tab-content:last-child,
		.resp-vtabs .resp-tab-content:last-child { border-bottom: 1px solid <?php echo esc_html( $color_dark_4 ); ?> !important; }
	}
	.resp-easy-accordion h2.resp-tab-active { background: <?php echo esc_html( $color_dark_4 ); ?> !important; }
	.card > .card-header { background-color: <?php echo esc_html( $color_dark_4 ); ?>; }
	.btn-default { background-color: <?php echo esc_html( $color_dark_2 ); ?> !important; border-color: <?php echo esc_html( $color_dark_2 ); ?> !important; }
	.porto-history .thumb { background: transparent url(<?php echo PORTO_URI; ?>/images/history-thumb-dark.png) no-repeat 0 <?php echo porto_filter_output( $rtl ? '-200px' : '0' ); ?>; }
	select { background-image: url("<?php echo PORTO_URI; ?>/images/select-bg-light.svg"); }
<?php else : ?>
	.dir-arrow { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows.png) no-repeat 0 0; }
	.dir-arrow.arrow-light { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows-dark.png) no-repeat 0 0; }
	.elementor hr, hr, .divider,
	.slider-title .line,
	.section-title .line { background: rgba(0, 0, 0, .08); }
	hr.light { background: rgba(255, 255, 255, .06); }
	.porto-history .thumb { background: transparent url(<?php echo PORTO_URI; ?>/images/history-thumb.png) no-repeat 0 <?php echo porto_filter_output( $rtl ? '-200px' : '0' ); ?>; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	<?php if ( $dark ) : ?>
		.select2-drop, .select2-drop-active,
		.select2-drop input, .select2-drop-active input,
		.select2-drop .select2-results .select2-highlighted,
		.select2-drop-active .select2-results .select2-highlighted { background: <?php echo esc_html( $color_dark_2 ); ?>; }
		.cart-v2 .card.card-default .card-body, .card { background: <?php echo esc_html( $color_dark_5 ); ?> }
		.cart-v2 .card.card-default { border-radius: 0 0 6.99px 6.99px }
		.blockUI { background: <?php echo esc_html( $color_dark_2 ); ?> !important }
		.woocommerce-message, .woocommerce-error { background-color: transparent; }
	<?php endif; ?>
<?php endif; ?>
[type="submit"].btn-default { color: <?php echo porto_if_dark( '#666', '#333' ); ?>; }
.btn-default.btn:hover { color: <?php echo porto_if_dark( '#777', '#333' ); ?>; }
.divider.divider-small hr { background: <?php echo porto_if_dark( '#3f4247', '#555' ); ?>; }
<?php echo porto_filter_output( $input_lists ); ?>,
textarea,
.form-control,
select,
.porto-wpforms-inline .wpforms-field-large {
	background-color: <?php echo empty( $b['form-field-bgc'] ) ? porto_if_dark( $color_dark_3, '#fff' ) : esc_html( $b['form-field-bgc'] ); ?>;
	color: <?php echo empty( $b['form-color'] ) ? porto_if_dark( '#999', '#777' ) : esc_html( $b['form-color'] ); ?>;
	border-color: <?php echo esc_html( empty( $b['form-field-bc'] ) ? 'var(--porto-input-bc)' : $b['form-field-bc'] ); ?>;
<?php if ( ! empty( $b['form-field-bw'] ) && ( ! empty( $b['form-field-bw']['top'] ) || ! empty( $b['form-field-bw']['right'] ) || ! empty( $b['form-field-bw']['bottom'] ) || ! empty( $b['form-field-bw']['left'] ) || '0' === $b['form-field-bw']['top'] || '0' === $b['form-field-bw']['right'] || '0' === $b['form-field-bw']['bottom'] || '0' === $b['form-field-bw']['left'] ) ) : ?>
	border-width: <?php echo empty( $b['form-field-bw']['top'] ) && '0' !== $b['form-field-bw']['top'] ? '' : porto_config_value( $b['form-field-bw']['top'], 'px' ), empty( $b['form-field-bw']['right'] ) && '0' !== $b['form-field-bw']['right'] ? '' : ' ' . porto_config_value( $b['form-field-bw']['right'], 'px' ), empty( $b['form-field-bw']['bottom'] ) && '0' !== $b['form-field-bw']['bottom'] ? '' : ' ' . porto_config_value( $b['form-field-bw']['bottom'], 'px' ), ! $b['form-field-bw']['left'] && '0' !== $b['form-field-bw']['left'] ? '' : ' ' . porto_config_value( $b['form-field-bw']['left'], 'px' ); ?>;
<?php endif; ?>
<?php if ( ! empty( $b['form-fs'] ) ) : ?>
	font-size: <?php echo porto_config_value( $b['form-fs'], 'px' ); ?>;
<?php endif; ?>
<?php if ( ! empty( $b['form-ih'] ) ) : ?>
	height: <?php echo porto_config_value( $b['form-ih'], 'px' ); ?>;
<?php endif; ?>
}
<?php if ( ! empty( $b['form-ih'] ) ) : ?>
	textarea,
	.porto-wpforms-inline .wpforms-submit { height: auto }
<?php endif; ?>
<?php if ( ! empty( $b['form-fs'] ) ) : ?>
	form, form p { font-size: <?php echo porto_config_value( $b['form-fs'], 'px' ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['form-field-bcf'] ) ) : ?>
	input[type="email"]:focus, input[type="number"]:focus, input[type="password"]:focus, input[type="text"]:focus, textarea:focus, select:focus {
		border-color: <?php echo esc_html( $b['form-field-bcf'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['form-field-bgc'] ) || ! empty( $b['form-field-bc'] ) || ! empty( $b['form-field-bcf'] ) ) : ?>
	.form-control:focus {
	<?php if ( ! empty( $b['form-field-bgc'] ) ) : ?>
		background-color: <?php echo esc_html( $b['form-field-bgc'] ); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $b['form-field-bc'] ) || ! empty( $b['form-field-bcf'] ) ) : ?>
		border-color: <?php echo esc_html( ! empty( $b['form-field-bcf'] ) ? $b['form-field-bcf'] : $b['form-field-bc'] ); ?>;
	<?php endif; ?>
	}
<?php endif; ?>
<?php if ( ! empty( $b['form-br'] ) || '0' == $b['form-br'] ) : ?>
	input, textarea, .form-control, select {
		border-radius: <?php echo esc_html( $b['form-br'] ); ?>
	}
<?php endif; ?>

<?php if ( ! empty( $b['sidebar-bw'] ) ) : ?>
	.sidebar-content, .woocommerce-page.archive .sidebar-content {
		border: <?php echo porto_config_value( $b['sidebar-bw'], 'px' ); ?> solid<?php echo empty( $b['sidebar-bc'] ) ? '' : ' ' . esc_html( $b['sidebar-bc'] ); ?>
	}
	.woocommerce-page.archive .sidebar-content aside.widget {
		border-bottom: <?php echo porto_config_value( $b['sidebar-bw'], 'px' ); ?> solid<?php echo empty( $b['sidebar-bc'] ) ? '' : ' ' . esc_html( $b['sidebar-bc'] ); ?>
	}
<?php endif; ?>
<?php if ( ! empty( $b['sidebar-pd'] ) && ( ! empty( $b['sidebar-pd']['padding-top'] ) || ! empty( $b['sidebar-pd']['padding-right'] ) || ! empty( $b['sidebar-pd']['padding-bottom'] ) || ! empty( $b['sidebar-pd']['padding-left'] ) ) ) : ?>
	.sidebar-content, .woocommerce-page.archive .sidebar-content aside.widget { padding: <?php echo empty( $b['sidebar-pd']['padding-top'] ) ? '' : porto_config_value( $b['sidebar-pd']['padding-top'], 'px' ), empty( $b['sidebar-pd']['padding-right'] ) ? '' : ' ' . porto_config_value( $b['sidebar-pd']['padding-right'], 'px' ), empty( $b['sidebar-pd']['padding-bottom'] ) ? '' : ' ' . porto_config_value( $b['sidebar-pd']['padding-bottom'], 'px' ), ! $b['sidebar-pd']['padding-left'] ? '' : ' ' . porto_config_value( $b['sidebar-pd']['padding-left'], 'px' ); ?>; }
	.woocommerce-page.archive .sidebar-content { padding: 0 }
<?php endif; ?>

<?php if ( ! $dark ) : ?>
	.btn-default.btn { border-bottom-color: rgba(0, 0, 0, .2) }
<?php else : ?>
	.pricing-table .plan-ribbon { background-color: <?php echo porto_filter_output( $color_dark_3 ); ?>; }
<?php endif; ?>
<?php if ( ! empty( $b['footer-reveal'] ) ) : ?>
	.page-wrapper { background-color: <?php echo porto_if_light( '#fff', '#000' ); ?>; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	.login-more.heading-tag { color: #999; }
	.star-rating:before { color: <?php echo porto_if_light( 'rgba(0,0,0,0.16)', 'rgba(255,255,255,0.13)' ); ?>; }

	<?php if ( isset( $b['woo-show-product-border'] ) && $b['woo-show-product-border'] ) : ?>
		.product-image { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#f4f4f4' ); ?>; /*width: 99.9999%;*/ }
	<?php endif; ?>
	<?php if ( ! $b['thumb-padding'] ) : ?>
		.product-images .product-image-slider.owl-carousel .img-thumbnail { padding-right: 1px; padding-left: 1px; }
		.product-images .img-thumbnail .inner { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#f4f4f4' ); ?>; }
	<?php endif; ?>
<?php endif; ?>

.text-dark,
.text-dark.wpb_text_column p { color: <?php echo esc_html( $color_dark ); ?> !important; }
.alert.alert-dark { background-color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 10 ) ); ?>; border-color: <?php echo esc_html( $porto_color_lib->darken( $color_dark, 10 ) ); ?>; color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 70 ) ); ?>; }
.alert.alert-dark .alert-link { color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 85 ) ); ?>; }
html.dark .text-muted { color: <?php echo esc_html( $porto_color_lib->darken( $dark_default_text, 20 ) ); ?> !important; }

/*------------------ header type 17 ---------------------- */
<?php if ( 17 == $header_type ) : ?>
	#header .main-menu-wrap .menu-right { position: relative; top: auto; padding-<?php echo porto_filter_output( $left ); ?>: 0; display: table-cell; vertical-align: middle; }
	#header .main-menu-wrap #mini-cart { display: inline-block; }
	#header .main-menu-wrap .searchform-popup { display: inline-block; }
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		#header .main-menu-wrap .searchform-popup .search-toggle { display: none; }
		#header .main-menu-wrap .searchform-popup .searchform { margin-top: 0; position: static; display: block; border-width: 0; box-shadow: none; background: rgba(0, 0, 0, 0.07); }
		#header .main-menu-wrap .menu-right .searchform-popup .searchform { border-radius: 0; }
		#header .main-menu-wrap .searchform-popup .searchform fieldset { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
		#header .main-menu-wrap .searchform-popup .searchform input,
		#header .main-menu-wrap .searchform-popup .searchform select,
		#header .main-menu-wrap .searchform-popup .searchform button { border-radius: 0; color: #fff; height: 60px; }
		#header .main-menu-wrap .searchform-popup .searchform input::-webkit-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform select::-webkit-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform button::-webkit-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
		#header .main-menu-wrap .searchform-popup .searchform input:-ms-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform select:-ms-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform button:-ms-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 60px; line-height: 62px; }
		#header .main-menu-wrap .searchform-popup .searchform input { font-weight: 700; width: 200px; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		@media <?php echo porto_filter_output( $screen_large ); ?> {
			#header .main-menu-wrap .searchform-popup .searchform input,
			#header .main-menu-wrap .searchform-popup .searchform select,
			#header .main-menu-wrap .searchform-popup .searchform button { height: 50px; }
			#header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 50px; line-height: 52px; }
			#header .main-menu-wrap .searchform-popup .searchform input { width: 198px; }
		}
		#header .main-menu-wrap .searchform-popup .searchform select { font-weight: 700; width: 120px; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric-cat { width: 120px; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric .label { font-weight: 700; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .main-menu-wrap .searchform-popup .searchform button { margin-<?php echo porto_filter_output( $left ); ?>: -1px; font-size: 20px; padding: 6px 15px; color: #fff; opacity: 0.4; }
		#header .main-menu-wrap .searchform-popup .searchform button:hover { color: #000; }
		#header .main-menu-wrap .searchform-popup .searchform button .fa-search { font-family: "Simple-Line-Icons"; vertical-align: middle; }
		#header .main-menu-wrap .searchform-popup .searchform button .fa-search:before { content: "\e090"; font-family: inherit; }
	<?php endif; ?>
	#header .searchform .live-search-list { left: 0; right: 0; }
	@media (min-width: 768px) {
		#header .header-main .header-left,
		#header .header-main .header-center,
		#header .header-main .header-right { padding-top: 0; padding-bottom: 0; }
	}
	#header .feature-box .feature-box-icon,
	#header .feature-box .feature-box-info { float: <?php echo porto_filter_output( $left ); ?>; padding-<?php echo porto_filter_output( $left ); ?>: 0; }
	#header .feature-box .feature-box-icon { height: auto; top: 0; margin-<?php echo porto_filter_output( $right ); ?>: 0; }
	#header .feature-box .feature-box-icon > i { margin: 0; }
	#header .feature-box .feature-box-info > h4 { line-height: 110px; margin: 0; }
	#header .header-contact { margin: 0; }
	#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 32px; margin-<?php echo porto_filter_output( $left ); ?>: 22px; border-<?php echo porto_filter_output( $right ); ?>: 1px solid #e9e9e9; }
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 30px; margin-<?php echo porto_filter_output( $left ); ?>: 20px; }
	}
	#header .header-extra-info li:first-child { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	#header .header-extra-info li:last-child { padding-<?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: medium none; }
	@media (max-width: 991px) {
		#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 15px; margin-<?php echo porto_filter_output( $left ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: medium none; }
		#header .header-extra-info li:last-child { padding-<?php echo porto_filter_output( $right ); ?>: 15px; }
	}
	#header.sticky-header .mobile-toggle { margin: 0; }
<?php endif; ?>

/*------------------ Skin ---------------------- */
<?php
	$body_color = $b['body-font']['color'];
	$skin_color = $b['skin-color'];

	$bg_arr = array(
		array( 'body', 'body-bg', 'body-bg-gcolor' ),
		array( '.header-wrapper', 'header-wrap-bg', 'header-wrap-bg-gcolor' ),
		array( '#header .header-main', 'header-bg', 'header-bg-gcolor' ),
		array( '#main', 'content-bg', 'content-bg-gcolor' ),
		array( '#main .content-bottom-wrapper', 'content-bottom-bg', 'content-bottom-bg-gcolor' ),
		array( '.page-top', 'breadcrumbs-bg', 'breadcrumbs-bg-gcolor' ),
		array( '#footer', 'footer-bg', 'footer-bg-gcolor' ),
		array( '#footer .footer-main', 'footer-main-bg', 'footer-main-bg-gcolor' ),
		array( '.footer-top', 'footer-top-bg', 'footer-top-bg-gcolor' ),
		array( '#footer .footer-bottom', 'footer-bottom-bg', 'footer-bottom-bg-gcolor' ),
	);
	foreach ( $bg_arr as $element_bg ) {
		$css = array();

		if ( ! empty( $b[ $element_bg[1] ] ) ) {
			$bg_vars = 'footer-bottom-bg' == $element_bg[1] ? ( isset( $porto_settings['footer-bottom-bg'] ) ? $porto_settings['footer-bottom-bg'] : array() ) : $b[ $element_bg[1] ];

			if ( isset( $bg_vars['background-color'] ) && $bg_vars['background-color'] ) {
				$css[] = 'background-color:' . esc_html( $bg_vars['background-color'] );
			}
			if ( ! empty( $bg_vars['background-image'] ) ) {
				$css[] = 'background-image: url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), esc_url( $bg_vars['background-image'] ) ) . ')';
				if ( ! empty( $bg_vars['background-repeat'] ) ) {
					$css[] = 'background-repeat:' . esc_html( $bg_vars['background-repeat'] );
				}
				if ( ! empty( $bg_vars['background-size'] ) ) {
					$css[] = 'background-size:' . esc_html( $bg_vars['background-size'] );
				}
				if ( ! empty( $bg_vars['background-attachment'] ) ) {
					$css[] = 'background-attachment:' . esc_html( $bg_vars['background-attachment'] );
				}
				if ( ! empty( $bg_vars['background-position'] ) ) {
					$css[] = 'background-position:' . esc_html( $bg_vars['background-position'] );
				}
			}
		}

		if ( isset( $b[ $element_bg[1] . '-gradient' ] ) && $b[ $element_bg[1] . '-gradient' ] && $b[ $element_bg[2] ]['from'] && $b[ $element_bg[2] ]['to'] ) {
			$css[] = 'background-image: -webkit-linear-gradient(top, ' . esc_html( $b[ $element_bg[2] ]['from'] ) . ',' . esc_html( $b[ $element_bg[2] ]['to'] ) . ')';
			$css[] = 'background-image: linear-gradient(to bottom, ' . esc_html( $b[ $element_bg[2] ]['from'] ) . ',' . esc_html( $b[ $element_bg[2] ]['to'] ) . ')';
			$css[] = 'background-repeat: repeat-x';
		}

		if ( ! empty( $css ) ) {
			echo porto_filter_output( $element_bg[0] ) . '{' . implode( ';', $css ) . '}';
		}
	}
	?>
<?php if ( isset( $b['header-bg'] ) ) : ?>
@media (min-width: 992px) {
	.header-wrapper.header-side-nav:not(.fixed-header) #header {
		background-color: <?php echo esc_html( isset( $b['header-bg']['background-color'] ) ? $b['header-bg']['background-color'] : '' ); ?>;
		<?php if ( ! empty( $b['header-bg']['background-repeat'] ) ) { ?>
			background-repeat: <?php echo esc_html( $b['header-bg']['background-repeat'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-size'] ) ) { ?>
			background-size: <?php echo esc_html( $b['header-bg']['background-size'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-attachment'] ) ) { ?>
			background-attachment: <?php echo esc_html( $b['header-bg']['background-attachment'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-position'] ) ) { ?>
			background-position: <?php echo esc_html( $b['header-bg']['background-position'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-image'] ) ) { ?>
			background-image: url(<?php echo str_replace( array( 'http://', 'https://' ), array( '//', '//' ), esc_url( $b['header-bg']['background-image'] ) ); ?>);
		<?php } ?>
	<?php if ( ! empty( $b['header-bg-gradient'] ) && $b['header-bg-gcolor']['from'] && $b['header-bg-gcolor']['to'] ) : ?>
		background-image: -webkit-linear-gradient(top, <?php echo esc_html( $b['header-bg-gcolor']['from'] ); ?>, <?php echo esc_html( $b['header-bg-gcolor']['to'] ); ?>);
		background-image: linear-gradient(to bottom, <?php echo esc_html( $b['header-bg-gcolor']['from'] ); ?>, <?php echo esc_html( $b['header-bg-gcolor']['to'] ); ?>);
		background-repeat: repeat-x;
	<?php endif; ?>
	}
}
<?php endif; ?>

<?php if ( isset( $b['content-bottom-padding'] ) && ( ! empty( $b['content-bottom-padding']['padding-top'] ) || ! empty( $b['content-bottom-padding']['padding-bottom'] ) ) ) : ?>
	#main .content-bottom-wrapper {
		<?php
		if ( ! empty( $b['content-bottom-padding']['padding-top'] ) ) :
			?>
		padding-top: <?php echo esc_html( $b['content-bottom-padding']['padding-top'] ); ?>px; <?php endif; ?>
		<?php
		if ( ! empty( $b['content-bottom-padding']['padding-bottom'] ) ) :
			?>
		padding-bottom: <?php echo esc_html( $b['content-bottom-padding']['padding-bottom'] ); ?>px; <?php endif; ?>
	}
<?php endif; ?>
<?php if ( isset( $b['footer-top-padding'] ) && ( ! empty( $b['footer-top-padding']['padding-top'] ) || ! empty( $b['footer-top-padding']['padding-bottom'] ) ) ) : ?>
	.footer-top {
		<?php
		if ( ! empty( $b['footer-top-padding']['padding-top'] ) ) :
			?>
		padding-top: <?php echo esc_html( $b['footer-top-padding']['padding-top'] ); ?>px; <?php endif; ?>
		<?php
		if ( ! empty( $b['footer-top-padding']['padding-bottom'] ) ) :
			?>
		padding-bottom: <?php echo esc_html( $b['footer-top-padding']['padding-bottom'] ); ?>px; <?php endif; ?>
	}
<?php endif; ?>

/* layout */
<?php porto_calc_container_width( '#banner-wrapper.banner-wrapper-boxed', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( '#main.main-boxed', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( 'body.boxed .page-wrapper', false, $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( '#main.main-boxed .vc_row[data-vc-stretch-content]', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
	body.boxed .vc_row[data-vc-stretch-content],
	body.boxed #header.sticky-header .header-main.sticky,
	body.boxed #header.sticky-header .main-menu-wrap,
	body.boxed #header.sticky-header .header-main.sticky,
	#header-boxed #header.sticky-header .header-main.sticky,
	body.boxed #header.sticky-header .main-menu-wrap,
	#header-boxed #header.sticky-header .main-menu-wrap {
		max-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px;
	}

	.col-xl-1-5 { width: 20% }
	.col-xl-2-5 { width: 40% }
	.col-xl-3-5 { width: 60% }
	.col-xl-4-5 { width: 80% }
	.offset-xl-1\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 20% }
	.offset-xl-2\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 40% }
	.offset-xl-3\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 60% }
	.offset-xl-4\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 80% }
}

@media (min-width: <?php echo (int) $xxl + (int) $b['grid-gutter-width'] * 2; ?>px) {
	.col-xxl-1-5 { width: 20% }
	.col-xxl-2-5 { width: 40% }
	.col-xxl-3-5 { width: 60% }
	.col-xxl-4-5 { width: 80% }
	.offset-xxl-1\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 20% }
	.offset-xxl-2\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 40% }
	.offset-xxl-3\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 60% }
	.offset-xxl-4\/5 { margin-<?php echo porto_filter_output( $left ); ?>: 80% }
}

/* header */
<?php if ( isset( $b['header-top-font-size'] ) && $b['header-top-font-size'] ) : ?>
	#header .header-top { font-size: <?php echo esc_attr( $b['header-top-font-size'] ); ?>px; }
<?php endif; ?>
#header .separator { border-left: 1px solid <?php echo porto_filter_output( $porto_color_lib->isColorDark( isset( $b['header-link-color']['regular'] ) ? $b['header-link-color']['regular'] : '' ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
#header .header-top .separator { border-left-color: <?php echo porto_filter_output( $porto_color_lib->isColorDark( isset( $b['header-top-link-color']['regular'] ) ? $b['header-top-link-color']['regular'] : '' ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
<?php if ( isset( $b['header-top-bg-color'] ) && ( 'transparent' == $b['header-top-bg-color'] || ! $porto_color_lib->isColorDark( $b['header-top-bg-color'] ) ) ) : ?>
	<?php if ( ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
		#header .header-top .share-links > a:not(:hover) { background: none; }
	<?php endif; ?>
<?php endif; ?>

<?php
	$header_bg_color = isset( $b['header-bg']['background-color'] ) ? $b['header-bg']['background-color'] : '';
	$header_opacity  = ( isset( $b['header-opacity'] ) && (int) $b['header-opacity'] ) ? (int) $b['header-opacity'] : 80;
	$header_opacity  = (float) $header_opacity / 100;

	$searchform_opacity = ( isset( $b['searchform-opacity'] ) && (int) $b['searchform-opacity'] ) ? (int) $b['searchform-opacity'] : 50;
	$searchform_opacity = (float) $searchform_opacity / 100;
	$menuwrap_opacity   = ( isset( $b['menuwrap-opacity'] ) && (int) $b['menuwrap-opacity'] ) ? (int) $b['menuwrap-opacity'] : 30;
	$menuwrap_opacity   = (float) $menuwrap_opacity / 100;
	$menu_opacity       = ( isset( $b['menu-opacity'] ) && (int) $b['menu-opacity'] ) ? (int) $b['menu-opacity'] : 30;
	$menu_opacity       = (float) $menu_opacity / 100;

	$footer_opacity = ( isset( $b['footer-opacity'] ) && (int) $b['footer-opacity'] ) ? (int) $b['footer-opacity'] : 80;
	$footer_opacity = (float) $footer_opacity / 100;
?>
.fixed-header #header .header-main {
<?php if ( 'transparent' == $header_bg_color ) : ?>
	box-shadow: none;
<?php elseif ( $header_bg_color ) : ?>
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $header_bg_color ) ); ?>, <?php echo esc_html( $header_opacity ); ?>);
<?php endif; ?>
}
<?php if ( isset( $b['header-top-bg-color'] ) && 'transparent' != $b['header-top-bg-color'] && $b['header-top-bg-color'] ) : ?>
	.fixed-header #header .header-top {
		background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['header-top-bg-color'] ) ); ?>, <?php echo esc_html( $header_opacity ); ?>);
	}
<?php endif; ?>
@media (min-width: 992px) {
	.header-wrapper.header-side-nav.fixed-header #header {
		<?php porto_background_opacity( $porto_color_lib, $header_bg_color, $header_opacity ); ?>
	}
}

<?php
if ( ! empty( $b['mainmenu-wrap-bg-color-sticky'] ) && 'transparent' != $b['mainmenu-wrap-bg-color-sticky'] ) {
	$sticky_menu_bg_color = $b['mainmenu-wrap-bg-color-sticky'];
} elseif ( ! empty( $b['mainmenu-bg-color'] ) && 'transparent' != $b['mainmenu-bg-color'] ) {
	$sticky_menu_bg_color = $b['mainmenu-bg-color'];
} elseif ( ! empty( $b['mainmenu-wrap-bg-color'] ) && 'transparent' != $b['mainmenu-wrap-bg-color'] ) {
	$sticky_menu_bg_color = $b['mainmenu-wrap-bg-color'];
} else {
	$sticky_menu_bg_color = $b['sticky-header-bg']['background-color'];
}
?>
#header.sticky-header .header-main,
.fixed-header #header.sticky-header .header-main
<?php echo 'transparent' == $sticky_menu_bg_color ? ', #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .main-menu-wrap' : ''; ?> {
	<?php if ( ! empty( $b['sticky-header-bg']['background-color'] ) && 'transparent' != $b['sticky-header-bg']['background-color'] ) { ?>
		background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg']['background-color'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>);
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-repeat'] ) && ! empty( $b['sticky-header-bg']['background-repeat'] ) ) { ?>
		background-repeat: <?php echo esc_html( $b['sticky-header-bg']['background-repeat'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-size'] ) && ! empty( $b['sticky-header-bg']['background-size'] ) ) { ?>
		background-size: <?php echo esc_html( $b['sticky-header-bg']['background-size'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-attachment'] ) && ! empty( $b['sticky-header-bg']['background-attachment'] ) ) { ?>
		background-attachment: <?php echo esc_html( $b['sticky-header-bg']['background-attachment'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-position'] ) && ! empty( $b['sticky-header-bg']['background-position'] ) ) { ?>
		background-position: <?php echo esc_html( $b['sticky-header-bg']['background-position'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-image'] ) && ! empty( $b['sticky-header-bg']['background-image'] ) ) { ?>
		background-image: url(<?php echo str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $b['sticky-header-bg']['background-image'] ); ?>);
	<?php } ?>
<?php if ( ! empty( $b['sticky-header-bg-gradient'] ) && ! empty( $b['sticky-header-bg-gcolor']['from'] ) && $b['sticky-header-bg-gcolor']['to'] && 'transparent' != $b['sticky-header-bg-gcolor']['from'] && 'transparent' != $b['sticky-header-bg-gcolor']['to'] ) : ?>
	background-image: -webkit-linear-gradient(top, rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['from'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['to'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
	background-image: linear-gradient(to bottom, rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['from'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['to'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
	background-repeat: repeat-x;
<?php endif; ?>
}
<?php if ( 'transparent' != $sticky_menu_bg_color ) : ?>
#header.sticky-header .main-menu-wrap,
.fixed-header #header.sticky-header .main-menu-wrap {
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $sticky_menu_bg_color ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>);
}
<?php endif; ?>
<?php if ( ! empty( $b['sticky-header-bg']['background-image'] ) ) { ?>
	#header.header-loaded .header-main { transition: none; }
<?php } ?>

.fixed-header #header .searchform {
	<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], $searchform_opacity ); ?>
	<?php if ( 'transparent' != $b['searchform-border-color'] && $searchform_opacity ) : ?>
		border-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['searchform-border-color'] ) ); ?>, <?php echo esc_html( $searchform_opacity ); ?>);
	<?php endif; ?>
}
@media (max-width: 991px) {
	.fixed-header #header .searchform {
		<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], 1 ); ?>
	}
}
.fixed-header #header .searchform-popup .searchform {
	<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], 1 ); ?>
}
.fixed-header #header .main-menu-wrap {
	<?php porto_background_opacity( $porto_color_lib, isset( $b['mainmenu-wrap-bg-color'] ) ? $b['mainmenu-wrap-bg-color'] : '', $menuwrap_opacity ); ?>
}
.fixed-header #header .main-menu {
	<?php porto_background_opacity( $porto_color_lib, isset( $b['mainmenu-bg-color'] ) ? $b['mainmenu-bg-color'] : '', $menu_opacity ); ?>
}
#header .searchform,
.fixed-header #header.sticky-header .searchform {
	<?php if ( $b['searchform-bg-color'] ) : ?>
		background: <?php echo esc_html( 'overlay' == $b['search-layout'] ? 'rgba(' . $porto_color_lib->hexToRGB( $b['searchform-bg-color'] ) . ', .95)' : $b['searchform-bg-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['searchform-border-color'] ) : ?>
		border-color: <?php echo esc_html( $b['searchform-border-color'] ); ?>;
	<?php endif; ?>
}
<?php if ( 'simple' != $b['search-layout'] && 'overlay' != $b['search-layout'] ) : ?>
	.fixed-header #header.sticky-header .searchform {
		border-radius: <?php echo porto_filter_output( $b['search-border-radius'] ? '20px' : '0' ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['mainmenu-bg-color'] ) && 'transparent' != $b['mainmenu-bg-color'] ) : ?>
	.fixed-header #header.sticky-header .main-menu,
	#header .main-menu,
	#main-toggle-menu .toggle-menu-wrap {
		background-color: <?php echo esc_html( $b['mainmenu-bg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-bg-color'] ) ) : ?>
	.header-bottom { background-color: <?php echo esc_html( $b['header-bottom-bg-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-container-bg-color'] ) ) : ?>
	.header-bottom > .container { background-color: <?php echo esc_html( $b['header-bottom-container-bg-color'] ); ?> }
<?php endif; ?>

<?php if ( ! empty( $b['header-text-color'] ) ) : ?>
	#header,
	#header .header-main .header-contact .nav-top > li > a,
	#header .top-links > li.menu-item:before { color: <?php echo esc_html( $b['header-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-link-color']['regular'] ) ) : ?>
	.header-main .header-contact a,
	#header .tooltip-icon,
	#header .top-links > li.menu-item > a,
	#header .searchform-popup .search-toggle,
	.header-wrapper .custom-html a:not(.btn),
	#header .my-account,
	#header .my-wishlist,
	#header .yith-woocompare-open {
		color: <?php echo esc_html( $b['header-link-color']['regular'] ); ?>;
	}
	#header .tooltip-icon { border-color: <?php echo esc_html( $b['header-link-color']['regular'] ); ?>; }
<?php endif; ?>
<?php if ( ! empty( $b['header-link-color']['hover'] ) ) : ?>
	.header-main .header-contact a:hover,
	#header .top-links > li.menu-item:hover > a,
	#header .top-links > li.menu-item > a.active,
	#header .top-links > li.menu-item > a.focus,
	#header .top-links > li.menu-item.has-sub:hover > a,
	#header .searchform-popup .search-toggle:hover,
	.header-wrapper .custom-html a:not(.btn):hover,
	#header .my-account:hover,
	#header .my-wishlist:hover,
	#header .yith-woocompare-open:hover {
		color: <?php echo esc_html( $b['header-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-top-text-color'] ) ) : ?>
	#header .header-top,
	.header-top .top-links > li.menu-item:after { color: <?php echo esc_html( $b['header-top-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-top-link-color']['regular'] ) ) : ?>
	.header-top .header-contact a,
	.header-top .custom-html a:not(.btn),
	#header .header-top .top-links > li.menu-item > a,
	.header-top .welcome-msg a {
		color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-top-link-color']['hover'] ) ) : ?>
	.header-top .header-contact a:hover,
	.header-top .custom-html a:not(.btn):hover,
	#header .header-top .top-links > li.menu-item.active > a,
	#header .header-top .top-links > li.menu-item:hover > a,
	#header .header-top .top-links > li.menu-item > a.active,
	#header .header-top .top-links > li.menu-item.has-sub:hover > a,
	.header-top .welcome-msg a:hover {
		color: <?php echo esc_html( $b['header-top-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-text-color'] ) ) : ?>
	#header .header-bottom { color: <?php echo esc_html( $b['header-bottom-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-link-color']['regular'] ) ) : ?>
	#header .header-bottom a:not(.btn) { color: <?php echo esc_html( $b['header-bottom-link-color']['regular'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-link-color']['hover'] ) ) : ?>
	#header .header-bottom a:not(.btn):hover { color: <?php echo esc_html( $b['header-bottom-link-color']['hover'] ); ?> }
<?php endif; ?>

<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
	#header .header-main .top-links > li.menu-item.has-sub:hover > a,
	#header .header-bottom .top-links > li.menu-item.has-sub:hover > a {
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
	#header .top-links .narrow ul.sub-menu,
	#header .main-menu .wide .popup > .inner,
	.side-nav-wrap .sidebar-menu .wide .popup > .inner,
	.sidebar-menu .narrow ul.sub-menu {
		background-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
	}
	<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
		#header .top-links > li.has-sub:before,
		#header .top-links > li.has-sub:after {
			border-bottom-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
		}
	<?php endif; ?>
	.sidebar-menu .menu-custom-block a:hover,
	.sidebar-menu .menu-custom-block a:hover + a {
		border-top-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
	#header .top-links .narrow li.menu-item > a,
	#header .main-menu .wide li.sub li.menu-item > a,
	.side-nav-wrap .sidebar-menu .wide li.menu-item li.menu-item > a,
	.sidebar-menu .wide li.sub li.menu-item > a,
	.sidebar-menu .narrow li.menu-item > a,
	.porto-popup-menu .sub-menu a {
		color: <?php echo esc_html( $b['mainmenu-popup-text-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
	#header .top-links .narrow li.menu-item:hover > a,
	.porto-popup-menu .sub-menu a:hover {
		color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
	#header .top-links .narrow li.menu-item:hover > a,
	#header .sidebar-menu .narrow .menu-item:hover > a,
	.main-sidebar-menu .sidebar-menu .narrow .menu-item:hover > a { background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( 'slide' != $b['side-menu-type'] ) : ?>
	.side-nav-wrap .sidebar-menu .wide li.menu-item li.menu-item > a:hover {
		<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
		<?php endif; ?>
	}
<?php endif; ?>
<?php porto_calc_container_width( '#header-boxed', false, $b['container-width'], $b['grid-gutter-width'] ); ?>

<?php if ( isset( $b['header-top-menu-padding'] ) && ( $b['header-top-menu-padding']['padding-top'] || $b['header-top-menu-padding']['padding-bottom'] || $b['header-top-menu-padding']['padding-left'] || $b['header-top-menu-padding']['padding-right'] ) ) : ?>
	#header .header-top .top-links > li.menu-item > a {
		<?php if ( $b['header-top-menu-padding']['padding-top'] ) : ?>
			padding-top: <?php echo esc_html( $b['header-top-menu-padding']['padding-top'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-bottom'] ) : ?>
			padding-bottom: <?php echo esc_html( $b['header-top-menu-padding']['padding-bottom'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-left'] ) : ?>
			padding-left: <?php echo esc_html( $b['header-top-menu-padding']['padding-left'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-right'] ) : ?>
			padding-right: <?php echo esc_html( $b['header-top-menu-padding']['padding-right'] ); ?>px;
		<?php endif; ?>
	}
<?php endif; ?>
#header .header-top .top-links .narrow li.menu-item:hover > a { text-decoration: none; }
<?php if ( ( isset( $b['header-top-menu-hide-sep'] ) && $b['header-top-menu-hide-sep'] ) || ! $legacy_mode ) : ?>
	#header .top-links > li.menu-item:after { content: none; }
	#header .header-top .gap { visibility: hidden; }
<?php endif; ?>

.header-top {
	<?php if ( isset( $b['header-top-bottom-border'] ) && $b['header-top-bottom-border']['border-top'] && '0px' != $b['header-top-bottom-border']['border-top'] && 'menu-hover-line' != $b['menu-type'] ) : ?>
		border-bottom: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['header-top-bottom-border']['border-color'] ); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $b['header-top-bg-color'] ) ) : ?>
		background-color: <?php echo esc_html( $b['header-top-bg-color'] ); ?>;
	<?php endif; ?>
}

.main-menu-wrap {
	<?php if ( ! empty( $b['mainmenu-wrap-bg-color'] ) ) : ?>
		background-color: <?php echo esc_html( $b['mainmenu-wrap-bg-color'] ); ?>;
	<?php endif; ?>

	<?php if ( isset( $b['mainmenu-wrap-padding'] ) ) : ?>
	padding: <?php echo porto_config_value( $b['mainmenu-wrap-padding']['padding-top'], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-wrap-padding'][ 'padding-' . $right ], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-wrap-padding']['padding-bottom'], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-wrap-padding'][ 'padding-' . $left ], 'px' ); ?>;
	<?php endif; ?>
}

<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-top'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-bottom'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-left'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-right'] ) : ?>
	#header.sticky-header .main-menu-wrap,
	#header.sticky-header .header-main.sticky .header-left,
	#header.sticky-header .header-main.sticky .header-right {
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-top'] ) : ?>
			padding-top: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-top'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ) : ?>
			padding-bottom: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-left'] ) : ?>
			padding-left: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-left'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-right'] ) : ?>
			padding-right: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-right'] ); ?>px;
		<?php endif; ?>
	}
	<?php // For wpbakery & elementor page builder ?>	
	.header-builder-p.sticky-header .header-main.sticky {
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-top'] ) : ?>
			padding-top: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-top'] ); ?>px !important;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ) : ?>
			padding-bottom: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ); ?>px !important;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-left'] ) : ?>
			padding-left: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-left'] ); ?>px !important;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-right'] ) : ?>
			padding-right: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-right'] ); ?>px !important;
		<?php endif; ?>
	}
<?php endif; ?>
#header.sticky-header .header-main.sticky .header-center {
	padding-top: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-top'] ); ?>px;
	padding-bottom: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ); ?>px
}
<?php if ( $b['border-radius'] && ( empty( $b['mainmenu-bg-color'] ) || 'transparent' == $b['mainmenu-bg-color'] ) && ( empty( $b['mainmenu-toplevel-hbg-color'] ) || 'transparent' == $b['mainmenu-toplevel-hbg-color'] ) ) : ?>
	.main-menu-wrap .main-menu .wide .popup,
	.main-menu-wrap .main-menu .wide .popup > .inner,
	.main-menu-wrap .main-menu .wide.pos-left .popup,
	.main-menu-wrap .main-menu .wide.pos-right .popup,
	.main-menu-wrap .main-menu .wide.pos-left .popup > .inner,
	.main-menu-wrap .main-menu .wide.pos-right .popup > .inner,
	.main-menu-wrap .main-menu .narrow .popup > .inner > ul.sub-menu,
	.main-menu-wrap .main-menu .narrow.pos-left .popup > .inner > ul.sub-menu,
	.main-menu-wrap .main-menu .narrow.pos-right .popup > .inner > ul.sub-menu { border-radius: 0 0 2px 2px; }
<?php endif; ?>
.main-menu-wrap .main-menu > li.menu-item > a .tip {
	<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ); ?>px;
	top: <?php echo (int) porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ) - 15; ?>px;
}
#header .main-menu-wrap .menu-custom-block a,
#header .main-menu-wrap .menu-custom-block span {
	padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $left ] ); ?>px;
}
<?php if ( $b['mainmenu-toplevel-padding1']['padding-top'] ) : ?>
	#header .main-menu-wrap .menu-custom-block {
		padding-top: 0;
		padding-bottom: 0
	}
<?php endif; ?>
@media <?php echo porto_filter_output( $screen_large ); ?> {
	.main-menu-wrap .main-menu > li.menu-item > a .tip {
		<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px;
		top: <?php echo (int) porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ) - 15; ?>px;
	}
	#header .main-menu-wrap .menu-custom-block a,
	#header .main-menu-wrap .menu-custom-block span {
		padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
}
<?php if ( $b['enable-sticky-header'] ) : ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ) ) : ?>
		.sticky-header .main-menu-wrap .main-menu > li.menu-item > a .tip,
		#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block .tip {
			<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ); ?>px;
		}
	<?php endif; ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) ) : ?>
		.sticky-header .main-menu-wrap .main-menu > li.menu-item > a .tip,
		#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block .tip {
			top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-top'] ) - 15; ?>px;
		}
	<?php endif; ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ( ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-left'] ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-right'] ) ) : ?>
		@media (min-width: 992px) {
			#header.sticky-header .main-menu > li.menu-item > a,
			#header.sticky-header .menu-custom-block span,
			#header.sticky-header .menu-custom-block a,
			#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block a,
			#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block span {
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) ) : ?>
					padding-top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-top'] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) : ?>
					padding-bottom: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-bottom'] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $left ] ) ) : ?>
					padding-<?php echo porto_filter_output( $left ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $left ] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ) ) : ?>
					padding-<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ); ?>px;
				<?php endif; ?>
			}
		}
	<?php endif; ?>
	<?php if ( 'menu-hover-line menu-hover-underline' == $b['menu-type'] && isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-left'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-right'] ) ) : ?>
		.sticky-header .mega-menu.menu-hover-underline > li.menu-item > a:before {
			margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-left'] ); ?>px;
			margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-right'] ); ?>px;
		}
	<?php endif; ?>
<?php endif; ?>

#header .main-menu-wrap .menu-custom-block .tip {
	<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ) - 5; ?>px;
	top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ) - 15; ?>px;
}

#header .main-menu > li.menu-item > a {
	<?php if ( $b['menu-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>;
	<?php endif; ?>
	padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-bottom'], 'px' ); ?> <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $left ], 'px' ); ?>;
}
<?php
	$main_menu_level1_abg_color    = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
	$main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
?>
#header .main-menu > li.menu-item.active > a {
	<?php if ( $main_menu_level1_abg_color ) : ?>
		background-color: <?php echo esc_html( $main_menu_level1_abg_color ); ?>;
	<?php endif; ?>
	<?php if ( $main_menu_level1_active_color ) : ?>
		color: <?php echo esc_html( $main_menu_level1_active_color ); ?>;
	<?php endif; ?>
}
#header .main-menu > li.menu-item.active:hover > a,
#header .main-menu > li.menu-item:hover > a {
	<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
	<?php endif; ?>
}
#header .main-menu .popup a,
.side-nav-wrap .sidebar-menu .popup,
.main-sidebar-menu .sidebar-menu .popup,
.porto-popup-menu .sub-menu {
	<?php if ( $b['menu-popup-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-popup-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-popup-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-popup-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-popup-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-popup-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}

<?php
if ( isset( $b['mainmenu-popup-top-border'] ) && isset( $b['mainmenu-popup-top-border']['border-top'] ) ) {
	if ( '0px' == $b['mainmenu-popup-top-border']['border-top'] ) {
		$b['mainmenu-popup-border'] = false;
	} else {
		$b['mainmenu-popup-border']       = $b['mainmenu-popup-top-border']['border-top'];
		$b['mainmenu-popup-border-color'] = $b['mainmenu-popup-top-border']['border-color'];
	}
} elseif ( $b['mainmenu-popup-border'] ) {
	$b['mainmenu-popup-border'] = '3px';
} else {
	$b['mainmenu-popup-border'] = false;
}
?>
<?php if ( $b['mainmenu-popup-border'] ) : ?>
	#header .main-menu .wide .popup { border-top: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>; }
	#header .sidebar-menu .wide .popup { border-<?php echo empty( $porto_settings['header-side-position'] ) ? 'left' : 'right'; ?>: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( ! $b['mainmenu-popup-border'] ) : ?>
	#header .main-menu .wide .popup,
	#header .sidebar-menu .wide .popup {
		border-width: 0;
	}
	<?php if ( $b['border-radius'] ) : ?>
		#header .main-menu .wide .popup > .inner { border-radius: 2px; }
		#header .main-menu .wide.pos-left .popup > .inner { border-radius: 0 2px 2px 2px; }
		#header .main-menu .wide.pos-right .popup > .inner { border-radius: 2px 0 2px 2px; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $b['mainmenu-popup-heading-color'] ) : ?>
	#header .main-menu .wide li.sub > a,
	.side-nav-wrap .sidebar-menu .wide li.sub > a {
		color: <?php echo esc_html( $b['mainmenu-popup-heading-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['mainmenu-popup-narrow-type'] ) && $b['mainmenu-popup-narrow-type'] && $b['mainmenu-toplevel-hbg-color'] && 'transparent' != $b['mainmenu-toplevel-hbg-color'] ) : ?>
	#header .main-menu .narrow ul.sub-menu {
		background: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	#header .main-menu .narrow li.menu-item > a {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	#header .main-menu .narrow li.menu-item:hover > a {
		background: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>
	}
	<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
		#header .main-menu .narrow.has-sub:before,
		#header .main-menu .narrow.has-sub:after { content: none }
	<?php endif; ?>
<?php else : ?>
	#header .main-menu .narrow ul.sub-menu {
		<?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-border'] ) : ?>
			border-top: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>;
		<?php endif; ?>
	}
	<?php if ( $b['mainmenu-popup-border'] ) : ?>
		#header .main-menu .narrow li.menu-item:hover > ul.sub-menu { top: <?php echo - 5 - (int) str_replace( 'px', '', $b['mainmenu-popup-border'] ); ?>px; }
	<?php endif; ?>
	#header .main-menu .narrow li.menu-item > a {
		<?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['regular'] ); ?>;
		<?php endif; ?>
		<?php
		if ( $b['mainmenu-popup-bg-color'] && 'transparent' != $b['mainmenu-popup-bg-color'] ) :
			$main_menu_popup_bg_arr = $porto_color_lib->hexToRGB( $b['mainmenu-popup-bg-color'], false );
			if ( ( $main_menu_popup_bg_arr[0] * 256 + $main_menu_popup_bg_arr[1] * 16 + $main_menu_popup_bg_arr[2] ) < ( 79 * 256 + 255 * 16 + 255 ) ) :
				?>
				border-bottom-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
			<?php else : ?>
				border-bottom-color: <?php echo esc_html( $porto_color_lib->darken( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
			<?php endif; ?>
		<?php endif; ?>
	}
	#header .main-menu .narrow li.menu-item:hover > a {
		<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>;
		<?php endif; ?>
	}
<?php endif; ?>

<?php if ( ! empty( $b['menu-custom-text-color'] ) ) : ?>
	#header .menu-custom-block,
	#header .menu-custom-block span { color: <?php echo esc_html( $b['menu-custom-text-color'] ); ?>; }
<?php endif; ?>
#header .menu-custom-block span,
#header .menu-custom-block a {
	<?php if ( $b['menu-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}
<?php if ( ! empty( $b['menu-custom-link']['regular'] ) ) : ?>
	#header .menu-custom-block a {
		color: <?php echo esc_html( $b['menu-custom-link']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['menu-custom-link']['hover'] ) ) : ?>
	#header .menu-custom-block a:hover { color: <?php echo esc_html( $b['menu-custom-link']['hover'] ); ?>; }
<?php endif; ?>

<?php if ( ! empty( $b['show-account-dropdown'] ) ) : ?>
	.account-dropdown .sub-menu li.menu-item > a {
		<?php if ( ! empty( $b['account-menu-font']['font-family'] ) ) : ?>
			font-family: <?php echo sanitize_text_field( $b['account-menu-font']['font-family'] ); ?>, sans-serif;
		<?php endif; ?>
		<?php if ( ! empty( $b['account-menu-font']['font-weight'] ) ) : ?>
			font-weight: <?php echo esc_html( $b['account-menu-font']['font-weight'] ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $b['account-menu-font']['font-size'] ) ) : ?>
			font-size: <?php echo esc_html( $b['account-menu-font']['font-size'] ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $b['account-menu-font']['line-height'] ) ) : ?>
			line-height: <?php echo esc_html( $b['account-menu-font']['line-height'] ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $b['account-menu-font']['letter-spacing'] ) ) : ?>
			letter-spacing: <?php echo esc_html( $b['account-menu-font']['letter-spacing'] ); ?>;
		<?php endif; ?>
	}
	<?php if ( $b['account-dropdown-lc']['regular'] ) : ?>
		.account-dropdown .sub-menu li.menu-item:before,
		.account-dropdown .sub-menu li.menu-item > a { color: <?php echo esc_html( $b['account-dropdown-lc']['regular'] ); ?>; }
	<?php endif; ?>
	<?php if ( $b['account-dropdown-bgc'] ) : ?>
		ul.account-dropdown .narrow ul.sub-menu { background-color: <?php echo esc_html( $b['account-dropdown-bgc'] ); ?>; }
	<?php endif; ?>
	ul.account-dropdown .sub-menu li.menu-item:hover > a,
	ul.account-dropdown .sub-menu li.menu-item.active > a,
	ul.account-dropdown .sub-menu li.menu-item.is-active > a {
		<?php if ( $b['account-dropdown-lc']['hover'] ) : ?>
			color: <?php echo esc_html( $b['account-dropdown-lc']['hover'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['account-dropdown-hbgc'] ) : ?>
			background: <?php echo esc_html( $b['account-dropdown-hbgc'] ); ?>;
		<?php elseif ( $b['account-dropdown-bgc'] ) : ?>
			background: <?php echo esc_html( $porto_color_lib->darken( $b['account-dropdown-bgc'], 5 ) ); ?>;
		<?php endif; ?>
	}
<?php endif; ?>

<?php if ( $b['switcher-link-color']['regular'] ) : ?>
	#header .porto-view-switcher > li.menu-item:before,
	#header .porto-view-switcher > li.menu-item > a { color: <?php echo esc_html( $b['switcher-link-color']['regular'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-bg-color'] ) : ?>
	#header .porto-view-switcher > li.menu-item > a { background-color: <?php echo esc_html( $b['switcher-bg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-hbg-color'] ) : ?>
	#header .porto-view-switcher .narrow ul.sub-menu { background: <?php echo esc_html( $b['switcher-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-link-color']['hover'] ) : ?>
	#header .porto-view-switcher .narrow li.menu-item > a { color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>; }
<?php endif; ?>
#header .porto-view-switcher .narrow li.menu-item > a.active,
#header .porto-view-switcher .narrow li.menu-item:hover > a {
	<?php if ( $b['switcher-link-color']['hover'] ) : ?>
		color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['switcher-hbg-color'] ) : ?>
		background: <?php echo esc_html( $porto_color_lib->darken( $b['switcher-hbg-color'], 5 ) ); ?>;
	<?php endif; ?>
}
<?php if ( ! empty( $b['show-account-dropdown'] ) ) : ?>
	#header .account-dropdown .narrow ul.sub-menu { min-width: 160px; }
	#header .account-dropdown > li.menu-item > a { padding-top: 10px; padding-bottom: 10px; line-height: 1 }
	#header .account-dropdown > li.menu-item > a > i { margin-right: 0; }
	#header .account-dropdown > li.has-sub > a::after { font-size: 12px; vertical-align: middle; }
<?php endif; ?>
<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] && $b['switcher-hbg-color'] && 'transparent' != $b['switcher-hbg-color'] ) : ?>
	#header .porto-view-switcher > li.has-sub:before,
	#header .porto-view-switcher > li.has-sub:after { border-bottom-color: <?php echo esc_html( $b['switcher-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['searchform-text-color'] ) : ?>
	#header .searchform input,
	#header .searchform select,
	#header .searchform button,
	#header .searchform .selectric .label,
	#header .searchform .selectric-items li,
	#header .searchform .selectric-items li:hover,
	#header .searchform .selectric-items li.selected,
	#header .searchform .autocomplete-suggestion .yith_wcas_result_content .title { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input:-ms-input-placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input::-ms-input-placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input::placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-border-color'] ) : ?>
	<?php if ( 'simple' == $b['search-layout'] ) : ?>
		#header .searchform .searchform-fields,
	<?php endif; ?>
	#header .searchform input,
	#header .searchform select,
	#header .searchform .selectric,
	#header .searchform .selectric-hover .selectric,
	#header .searchform .selectric-open .selectric,
	#header .searchform .autocomplete-suggestions,
	#header .searchform .selectric-items { border-color: <?php echo esc_html( $b['searchform-border-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-hover-color'] ) : ?>
	#header .searchform button {
		color: <?php echo esc_html( $b['searchform-hover-color'] ); ?>;
	}
<?php endif; ?>
#header .searchform select option,
#header .searchform .autocomplete-suggestion,
#header .searchform .autocomplete-suggestions,
#header .searchform .selectric-items {
	<?php if ( $b['searchform-text-color'] ) : ?>
		color: <?php echo esc_html( $b['searchform-text-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['searchform-bg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['searchform-bg-color'] ); ?>;
	<?php endif; ?>
}
<?php if ( $b['searchform-bg-color'] ) : ?>
	#header .searchform .selectric-items li:hover,
	#header .searchform .selectric-items li.selected { background-color: <?php echo esc_html( $porto_color_lib->darken( $b['searchform-bg-color'], 10 ) ); ?> }
	#header .searchform .autocomplete-selected,
	#header .searchform .autocomplete-suggestion:hover { background-color: <?php echo esc_html( $porto_color_lib->darken( $b['searchform-bg-color'], 3 ) ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-popup-border-color'] && 'simple' != $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	#header .search-popup .searchform { border-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	@media (max-width: 991px) {
		#header .searchform { border-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	}
<?php elseif ( $b['searchform-bg-color'] && 'simple' == $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['searchform-bg-color'] ); ?> }
<?php endif; ?>
<?php if ( isset( $b['sticky-searchform-toggle-color'] ) && ! empty( $b['sticky-searchform-toggle-color']['regular'] ) ) : ?>
	#header.sticky-header .searchform-popup .search-toggle { color: <?php echo esc_html( $b['sticky-searchform-toggle-color']['regular'] ); ?> }
<?php endif; ?>
<?php if ( isset( $b['sticky-searchform-toggle-color'] ) && ! empty( $b['sticky-searchform-toggle-color']['hover'] ) ) : ?>
	#header.sticky-header .searchform-popup .search-toggle:hover { color: <?php echo esc_html( $b['sticky-searchform-toggle-color']['hover'] ); ?> }
<?php endif; ?>

#header .mobile-toggle {
	<?php if ( $b['mobile-menu-toggle-text-color'] ) : ?>
		color: <?php echo esc_html( $b['mobile-menu-toggle-text-color'] ); ?>;
	<?php endif; ?>
	background-color: <?php echo empty( $b['mobile-menu-toggle-bg-color'] ) ? $b['skin-color'] : $b['mobile-menu-toggle-bg-color']; ?>;
	<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
		font-size: 20px;
	<?php elseif ( class_exists( 'Woocommerce' ) ) : ?>
		margin-<?php echo porto_filter_output( $right ); ?>: .5rem
	<?php endif; ?>
}
<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
	#header .mobile-toggle:first-child { padding-<?php echo porto_filter_output( $left ); ?>: 1px }
<?php endif; ?>

@media <?php echo porto_filter_output( $screen_large ); ?> {
	#header .main-menu-wrap .menu-custom-block .tip {
		<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ) - 5; ?>px;
		top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ) - 15; ?>px;
	}
	#header .main-menu > li.menu-item > a/*,
	#header .menu-custom-block span,
	#header .menu-custom-block a*/ {
		padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
<?php if ( ! empty( $b['menu-block'] ) ) : ?>
	#header .menu-custom-block span,
	#header .menu-custom-block a {
		padding-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px;
		padding-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
<?php endif; ?>
}

/* side header */
<?php if ( 'side' == $header_type ) : ?>
	@media (min-width: 992px) {
		.page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: 0; <?php echo porto_filter_output( $right ); ?>: auto; }
		.page-wrapper.side-nav.side-nav-right > .header-wrapper { order: 2; }
		.page-wrapper.side-nav.side-nav-right > .header-wrapper.header-side-nav #header { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
		.page-wrapper.side-nav-right > .header-wrapper.header-side-nav #header.initialize { position: fixed; }
		.page-wrapper.side-nav-right .sidebar-menu > li.has-sub:before { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 100%; }
		.page-wrapper.side-nav-right .sidebar-menu li.menu-item > a,
		.page-wrapper.side-nav-right .accordion-menu a { text-align: <?php echo porto_filter_output( $right ); ?>; }
		.page-wrapper.side-nav-right .sidebar-menu li.menu-item > a > .thumb-info-preview { <?php echo porto_filter_output( $left ); ?>: auto; }
		.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup:before { border-<?php echo porto_filter_output( $left ); ?>: 12px solid #fff; border-<?php echo porto_filter_output( $right ); ?>: none; right: -12px; left: auto; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 5px; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item:hover > .arrow:before { color: #fff; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu .narrow ul.sub-menu ul.sub-menu { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 100%; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $left ); ?> > li.menu-item .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $left ); ?> .narrow ul.sub-menu li.menu-item > ul.sub-menu { animation: menuFadeInRight 0.2s ease-out; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $right ); ?> > li.menu-item .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $right ); ?> > .narrow ul.sub-menu li.menu-item > ul.sub-menu { animation: menuFadeInLeft 0.2s ease-out; }
		.page-wrapper.side-nav.side-nav-right #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
	}
<?php endif; ?>

/* sticky header */
<?php if ( isset( $porto_settings['show-sticky-logo'] ) && ! $porto_settings['show-sticky-logo'] ) : ?>
	#header.sticky-header .logo { display: none !important; }
<?php endif; ?>
<?php if ( $legacy_mode && ( ! isset( $porto_settings['show-sticky-searchform'] ) || ! $porto_settings['show-sticky-searchform'] ) ) : ?>
	#header.sticky-header .searchform-popup { display: none !important; }
<?php endif; ?>
<?php if ( $legacy_mode && ( ! isset( $porto_settings['show-sticky-minicart'] ) || ! $porto_settings['show-sticky-minicart'] ) ) : ?>
	.sticky-header #mini-cart:not(.minicart-opened) { display: none !important; }
<?php endif; ?>
<?php if ( isset( $porto_settings['show-sticky-menu-custom-content'] ) && ! $porto_settings['show-sticky-menu-custom-content'] ) : ?>
	#header.sticky-header .menu-custom-content { display: none !important; }
<?php endif; ?>

/* header type */
<?php if ( (int) $header_type >= 11 && (int) $header_type <= 17 ) : ?>
	@media (min-width: 992px) {
		#header .searchform button { color: <?php echo esc_html( $porto_color_lib->lighten( $b['searchform-text-color'], 43 ) ); ?>; }
	}
<?php endif; ?>
<?php if ( 9 == $header_type ) : ?>
	#header.sticky-header .main-menu-wrap,
	.fixed-header #header.sticky-header .main-menu-wrap { background-color: <?php echo esc_html( $b['mainmenu-wrap-bg-color'] ); ?>; }
<?php elseif ( 11 == $header_type && $b['header-top-border']['border-top'] && '0px' != $b['header-top-border']['border-top'] ) : ?>
	<?php
		$main_menu_level1_abg_color    = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
		$main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
	?>
	@media (min-width: 992px) {
		#header .main-menu > li.menu-item { margin-top: -<?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; }
		#header .main-menu > li.menu-item > a { border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid transparent; }
		#header .main-menu > li.menu-item.active > a {
			border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid <?php echo 'transparent' == $main_menu_level1_abg_color ? $main_menu_level1_active_color : $main_menu_level1_abg_color; ?>;
		}
		#header .main-menu > li.menu-item:hover > a {
			border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid <?php echo 'transparent' == $b['mainmenu-toplevel-hbg-color'] ? $b['mainmenu-toplevel-link-color']['hover'] : $b['mainmenu-toplevel-hbg-color']; ?>;
		}
	}
<?php elseif ( 18 == $header_type ) : ?>
	#header .searchform-popup .search-toggle { color: <?php echo esc_html( $skin_color ); ?>; }
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $right ); ?>: .5rem }

<?php elseif ( porto_header_type_is_side() && isset( $b['side-social-bg-color'] ) ) : ?>
	<?php if ( ! $b['side-social-bg-color'] || 'transparent' == $b['side-social-bg-color'] ) : ?>
		#header .share-links a { background-color: transparent; color: <?php echo esc_html( $b['side-social-color'] ); ?>; }
	<?php else : ?>
		#header .share-links a:not(:hover) { background-color: <?php echo esc_html( $b['side-social-bg-color'] ); ?>; color: <?php echo esc_html( $b['side-social-color'] ); ?>; }
	<?php endif; ?>
	.header-wrapper #header .header-copyright { color: <?php echo esc_html( $b['side-copyright-color'] ); ?>; }

	@media (min-width: 992px) {
		.header-wrapper #header .header-main { position: static; background: transparent; }
		<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
		.header-wrapper #header .top-links li.menu-item > a { border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>; }
		<?php endif; ?>
		.header-wrapper #header .top-links li.menu-item > a,
		.header-wrapper #header .top-links li.menu-item.active > a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>; }
		.header-wrapper #header .top-links li.menu-item:hover,
		.header-wrapper #header .top-links li.menu-item.active:hover { background: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>; }
		.header-wrapper #header .top-links li.menu-item:hover > a,
		.header-wrapper #header .top-links li.menu-item.active:hover > a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>; }
	}
<?php endif; ?>

/* mini cart */
.cart-popup .quantity, .cart-popup .quantity .amount, .wishlist-popup .quantity, .wishlist-popup .amount { color: #696969 !important }
<?php if ( isset( $b['minicart-icon-font-size'] ) && $b['minicart-icon-font-size'] ) : ?>
	<?php
		$unit = trim( preg_replace( '/[0-9.]/', '', $b['minicart-icon-font-size'] ) );
	if ( ! $unit ) {
		$b['minicart-icon-font-size'] .= 'px';
	}
	?>
	#mini-cart .minicart-icon { font-size: <?php echo esc_html( $b['minicart-icon-font-size'] ); ?> }
<?php endif; ?>
<?php if ( isset( $b['minicart-popup-border-color'] ) && $b['minicart-popup-border-color'] ) : ?>
	<?php if ( 'transparent' != $b['minicart-popup-border-color'] ) : ?>
		#mini-cart .cart-popup { border: 1px solid <?php echo esc_html( $b['minicart-popup-border-color'] ); ?>; }
	<?php endif; ?>
	#mini-cart .cart-icon:after { border-color: <?php echo esc_html( $b['minicart-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( isset( $b['sticky-minicart-popup-border-color'] ) && $b['sticky-minicart-popup-border-color'] ) : ?>
	<?php if ( 'transparent' != $b['sticky-minicart-popup-border-color'] ) : ?>
		.sticky-header #mini-cart .cart-popup { border: 1px solid <?php echo esc_html( $b['sticky-minicart-popup-border-color'] ); ?>; }
	<?php endif; ?>
	.sticky-header #mini-cart .cart-icon:after { border-color: <?php echo esc_html( $b['sticky-minicart-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( isset( $b['minicart-bg-color'] ) && $b['minicart-bg-color'] ) : ?>
	#mini-cart {
		background: <?php echo esc_html( $b['minicart-bg-color'] ); ?>;
	<?php if ( $b['border-radius'] ) : ?>
		border-radius: 4px
	<?php endif; ?>
	}
<?php endif; ?>
<?php if ( isset( $b['sticky-minicart-bg-color'] ) && $b['sticky-minicart-bg-color'] ) : ?>
	.sticky-header #mini-cart {
		background: <?php echo esc_html( $b['sticky-minicart-bg-color'] ); ?>;
	<?php if ( $b['border-radius'] ) : ?>
		border-radius: 4px
	<?php endif; ?>
	}
<?php endif; ?>
<?php if ( ! empty( $b['minicart-icon-color'] ) ) : ?>
	#mini-cart .cart-subtotal, #mini-cart .minicart-icon { color: <?php echo esc_html( $b['minicart-icon-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['minicart-item-color'] ) ) : ?>
	#mini-cart .cart-items, #mini-cart .cart-items-text { color: <?php echo esc_html( $b['minicart-item-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['minicart-item-bg-color'] ) ) : ?>
	#mini-cart .cart-items { background-color: <?php echo esc_html( $b['minicart-item-bg-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-minicart-icon-color'] ) ) : ?>
	.sticky-header #mini-cart .cart-subtotal,
	.sticky-header #mini-cart .minicart-icon,
	.sticky-header #mini-cart.minicart-arrow-alt .cart-head:after { color: <?php echo esc_html( $b['sticky-minicart-icon-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-minicart-item-color'] ) ) : ?>
	.sticky-header #mini-cart .cart-items,
	.sticky-header #mini-cart .cart-items-text { color: <?php echo esc_html( $b['sticky-minicart-item-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-minicart-item-bg-color'] ) ) : ?>
	.sticky-header #mini-cart .cart-items { background-color: <?php echo esc_html( $b['sticky-minicart-item-bg-color'] ); ?> }
<?php endif; ?>

/* mobile panel */
<?php
	$panel_link_color = empty( $b['panel-link-color']['regular'] ) ? ( 'side' == $b['mobile-panel-type'] ? '#fff' : '#333' ) : $b['panel-link-color']['regular'];
?>
<?php if ( ! empty( $b['panel-bg-color'] ) ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel';
	else :
		echo '#nav-panel .mobile-nav-wrap';
	endif;
	?>
	 { background-color: <?php echo esc_html( $b['panel-bg-color'] ); ?>; }
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel .accordion-menu li.menu-item.active > a,';
		echo '#side-nav-panel .menu-custom-block a:hover';
	else :
		echo '#nav-panel .menu-custom-block a:hover';
	endif;
	?>
	 { background-color: <?php echo esc_html( $porto_color_lib->lighten( $b['panel-bg-color'], 5 ) ); ?>; }
<?php endif; ?>
<?php if ( ! empty( $b['panel-text-color'] ) ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel, #side-nav-panel .welcome-msg, #side-nav-panel .accordion-menu, #side-nav-panel .menu-custom-block, #side-nav-panel .menu-custom-block span';
	else :
		echo '#nav-panel, #nav-panel .welcome-msg, #nav-panel .accordion-menu, #nav-panel .menu-custom-block, #nav-panel .menu-custom-block span';
	endif;
	?>
	 { color: <?php echo esc_html( $b['panel-text-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['panel-border-color'] ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel .accordion-menu li:not(:last-child)';
	else :
		echo '#nav-panel .accordion-menu li';
	endif;
	?>
	{ border-bottom-color: <?php echo esc_html( $b['panel-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
	#nav-panel .accordion-menu li.menu-item.active > a { background-color: <?php echo esc_html( $skin_color ); ?> }

	<?php if ( ! empty( $b['panel-link-hbgcolor'] ) ) : ?>
		#nav-panel .accordion-menu li.menu-item.active > a, #nav-panel .accordion-menu li.menu-item:hover > a, #nav-panel .accordion-menu .sub-menu li:not(.active):hover > a { background: <?php echo esc_html( $b['panel-link-hbgcolor'] ); ?>; }
	<?php endif; ?>

	#nav-panel .accordion-menu > li.menu-item > a,
	#nav-panel .accordion-menu > li.menu-item > .arrow { color: <?php echo esc_html( $skin_color ); ?>; }
	#nav-panel .accordion-menu li.menu-item > a,
	#nav-panel .accordion-menu > li.menu-item > a,
	#nav-panel .accordion-menu .arrow,
	#nav-panel .menu-custom-block a,
	#nav-panel .accordion-menu > li.menu-item > .arrow { color: <?php echo esc_html( $panel_link_color ); ?>; }
	#nav-panel .mobile-nav-wrap::-webkit-scrollbar-thumb { background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
	<?php if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
		#nav-panel .mobile-nav-wrap:empty { min-height: 200px; }
	<?php endif; ?>
<?php elseif ( 'side' == $b['mobile-panel-type'] ) : ?>
	#side-nav-panel .accordion-menu li.menu-item > a,
	#side-nav-panel .menu-custom-block a { color: <?php echo esc_html( $panel_link_color ); ?>; }
	#side-nav-panel::-webkit-scrollbar { width: 5px; }
	#side-nav-panel::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
	<?php
		$mobile_panel_pos = isset( $b['mobile-panel-pos'] ) && $b['mobile-panel-pos'] ? ( $rtl ? ( 'panel-left' == $b['mobile-panel-pos'] ? 'right' : 'left' ) : str_replace( 'panel-', '', $b['mobile-panel-pos'] ) ) : $left;
	?>
	.page-wrapper,
	#header.sticky-header .header-main.sticky { transition: <?php echo porto_filter_output( $mobile_panel_pos ); ?> .3s }
	html.panel-opened .page-wrapper,
	html.panel-opened #header.sticky-header .header-main.sticky,
	html.sidebar-opened #header.sticky-header .header-main.sticky,
	.filter-sidebar-opened #header.sticky-header .header-main.sticky { <?php echo porto_filter_output( $mobile_panel_pos ); ?>: 260px; <?php echo 'left' == $mobile_panel_pos ? 'right' : 'left'; ?>: auto }

	<?php if ( isset( $b['mobile-panel-add-search'] ) && $b['mobile-panel-add-search'] ) : ?>
		@media (max-width: 991px) {
			#side-nav-panel .searchform select { display: none; }
			#side-nav-panel .searchform { padding: 0 16px; }
			#side-nav-panel .searchform-fields {
				display: flex; align-items: center;
				background: <?php echo esc_html( $porto_color_lib->lighten( $b['panel-bg-color'], 5 ) ); ?>;
			}
			#side-nav-panel .searchform .text { flex: 1; }
			#side-nav-panel .searchform input[type="text"] { width: 100%; height: 38px; background: none; border: none; font-size: inherit; }
			#side-nav-panel .searchform .btn { background: none; border: none; box-shadow: none; padding: .625rem .875rem; color: <?php echo esc_html( $panel_link_color ); ?> }
		}
		@media (max-width: 575px) {
			#header .searchform-popup { display: none; }
		}
	<?php endif; ?>
<?php endif; ?>

<?php if ( ! empty( $b['panel-link-color']['hover'] ) ) : ?>
	<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
		#nav-panel .accordion-menu li.menu-item:hover > a,
		#nav-panel .accordion-menu .arrow:hover,
		#nav-panel .menu-custom-block a:hover { color: <?php echo esc_html( $b['panel-link-color']['hover'] ); ?>; }
	<?php else : ?>
		#side-nav-panel .accordion-menu li.menu-item.active > a,
		#side-nav-panel .accordion-menu li.menu-item:hover > a,
		#side-nav-panel .menu-custom-block a:hover { color: <?php echo esc_html( $b['panel-link-color']['hover'] ); ?>; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( ! empty( $b['mobile-panel-type'] ) && ! empty( $b['panel-link-hbgcolor'] ) ) : ?>
	#side-nav-panel .accordion-menu li.menu-item.active > a,
	#side-nav-panel .accordion-menu li.menu-item:hover > a,
	#side-nav-panel .menu-custom-block a:hover { background-color: <?php echo esc_html( $b['panel-link-hbgcolor'] ); ?>; }
<?php endif; ?>
.fixed-header #nav-panel .mobile-nav-wrap { padding: 15px !important; }

/* footer */
.footer-wrapper.fixed #footer .footer-bottom {
<?php if ( empty( $b['footer-bottom-bg']['background-color'] ) ) : ?>
<?php elseif ( 'transparent' == $b['footer-bottom-bg']['background-color'] ) : ?>
	box-shadow: none;
<?php else : ?>
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['footer-bottom-bg']['background-color'] ) ); ?>, <?php echo esc_html( $footer_opacity ); ?>);
<?php endif; ?>
}
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	#footer .footer-main > .container { padding-top: 4rem; padding-bottom: 1.5rem; }
<?php endif; ?>
<?php if ( ! empty( $b['footer-label-color'] ) ) : ?>
	.footer .contact-details strong { color: <?php echo esc_html( $b['footer-label-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['footer-bottom-text-color'] ) ) : ?>
	.footer .footer-bottom,
	.footer .footer-bottom p,
	.footer .footer-bottom .widget > div > ul li,
	.footer .footer-bottom .widget > ul li { color: <?php echo esc_html( $b['footer-bottom-text-color'] ); ?> }
<?php endif; ?>

/* skin color */
<?php
	// Color Variables
	$theme_colors         = array(
		'primary'    => $skin_color,
		'secondary'  => $b['secondary-color'],
		'tertiary'   => $b['tertiary-color'],
		'quaternary' => $b['quaternary-color'],
		'dark'       => $b['dark-color'],
		'light'      => $b['light-color'],
	);
	$theme_colors_inverse = array(
		'primary'    => $b['skin-color-inverse'],
		'secondary'  => $b['secondary-color-inverse'],
		'tertiary'   => $b['tertiary-color-inverse'],
		'quaternary' => $b['quaternary-color-inverse'],
		'dark'       => $b['dark-color-inverse'],
		'light'      => $b['light-color-inverse'],
	);

	?>

.widget_recent_entries li,
.widget_recent_comments li,
.widget_pages li,
.widget_meta li,
.widget_nav_menu li,
.widget_archive li,
.widget_categories li,
.widget_rss li,
.wp-block-latest-posts__list.wp-block-latest-posts li,
.wp-block-latest-posts__list.wp-block-latest-posts li a {
	color: <?php echo esc_html( $porto_color_lib->darken( $body_color, 6.67 ) ); ?>;
}

.widget .rss-date,
.widget .post-date,
.widget .comment-author-link,
.wp-block-latest-posts__list time,
.wp-block-latest-posts__list .wp-block-latest-posts__post-excerpt {
	color: <?php echo esc_html( $porto_color_lib->lighten( $body_color, 6.67 ) ); ?>;
}

.color-primary,
article.post .post-title,
ul.list.icons li i,
ul.list.icons li a:hover,
ul[class^="wsp-"] li:before,
.featured-box .wpb_heading,
h2.resp-accordion,
.widget .widget-title a:hover,
.widget .widgettitle a:hover,
.widget li.active > a,
.widget_wysija_cont .showerrors,
.portfolio-info ul li a:hover,
article.member .member-role,
html #topcontrol:hover,
ul.portfolio-details h5,
.page-not-found h4,
article.post .sticky-post {
	color: <?php echo esc_html( $skin_color ); ?>;
}
.highlight {
	background-image: linear-gradient( 90deg, rgba(255,255,255,0) 50%, <?php echo 'rgba(' . $porto_color_lib->hexToRGB( $b['skin-color'] ) . ', 0.2)'; ?> 0 );
}
.thumb-info .link, .icon-featured,
.featured-box .icon-featured,
.inverted,
.share-links a,
.mega-menu:not(:hover) > li.menu-item.active > a,
.mega-menu:not(:hover) > li.menu-item:hover > a
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
,.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active
,.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li:hover
<?php endif; ?>
<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
	,.swiper-pagination-bullet-active
<?php endif; ?>
{
	background-color: <?php echo esc_html( $skin_color ); ?>;
}

/* misc */
<?php foreach ( $theme_colors as $key => $theme_color ) : ?>
	<?php if ( 'primary' != $key ) : ?>
	.nav-pills-<?php echo porto_filter_output( $key ); ?> a,
	<?php endif; ?>
	html .divider.divider-<?php echo porto_filter_output( $key ); ?> i,
	.featured-box-<?php echo porto_filter_output( $key ); ?> h4,
	.featured-box-effect-7.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:before,
	.has-<?php echo porto_filter_output( $key ); ?>-color { color: <?php echo esc_html( $theme_color ); ?>; }

	html .heading-<?php echo porto_filter_output( $key ); ?>,
	html .lnk-<?php echo porto_filter_output( $key ); ?>,
	.text-color-<?php echo porto_filter_output( $key ); ?> { color: <?php echo esc_html( $theme_color ); ?> !important; }

	<?php if ( 'primary' != $key ) : ?>
	.nav-pills-<?php echo porto_filter_output( $key ); ?> a:hover,
	.nav-pills-<?php echo porto_filter_output( $key ); ?> a:focus { color: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 5 ) ); ?>; }
	.nav-pills-<?php echo porto_filter_output( $key ); ?> > li.active > a,
	<?php endif; ?>
	html .label-<?php echo porto_filter_output( $key ); ?>,
	html .alert-<?php echo porto_filter_output( $key ); ?>,
	html .divider.divider-<?php echo porto_filter_output( $key ); ?>.divider-small hr,
	html .divider.divider-style-2.divider-<?php echo porto_filter_output( $key ); ?> i,
	.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured,
	html .inverted-<?php echo porto_filter_output( $key ); ?>,
	.has-<?php echo porto_filter_output( $key ); ?>-background-color { background-color: <?php echo esc_html( $theme_color ); ?>; }

	html .background-color-<?php echo porto_filter_output( $key ); ?>,
	.featured-box-effect-3.featured-box-<?php echo porto_filter_output( $key ); ?>:hover .icon-featured { background-color: <?php echo esc_html( $theme_color ); ?> !important; }

	html .alert-<?php echo porto_filter_output( $key ); ?>,
	html .alert-<?php echo porto_filter_output( $key ); ?> .alert-link,
	html .divider.divider-style-2.divider-<?php echo porto_filter_output( $key ); ?> i { color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>; }

	html .label-<?php echo porto_filter_output( $key ); ?>,
	html .divider.divider-style-3.divider-<?php echo porto_filter_output( $key ); ?> i,
	.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after,
	html .heading.heading-<?php echo porto_filter_output( $key ); ?> .heading-tag { border-color: <?php echo esc_html( $theme_color ); ?>; }

	.border-color-<?php echo porto_filter_output( $key ); ?> { border-color: <?php echo esc_html( $theme_color ); ?> !important; }

	.featured-box-<?php echo porto_filter_output( $key ); ?> .box-content { border-top-color: <?php echo esc_html( $theme_color ); ?>; }

	html .alert-<?php echo porto_filter_output( $key ); ?> { border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 3 ) ); ?>; }

	.featured-box-effect-2.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after { box-shadow: 0 0 0 3px <?php echo esc_html( $theme_color ); ?>; }
	.featured-box-effect-3.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after { box-shadow: 0 0 0 10px <?php echo esc_html( $theme_color ); ?>; }

<?php endforeach; ?>

<?php
if ( $dark ) {
	$color_background = '#1d2127';
	$function_name    = 'lighten';
} else {
	$color_background = '#f4f4f4';
	$function_name    = 'darken';
}

	$color_background_scale = array( $porto_color_lib->$function_name( $color_background, 10 ), $porto_color_lib->$function_name( $color_background, 20 ), $porto_color_lib->$function_name( $color_background, 30 ), $porto_color_lib->$function_name( $color_background, 40 ), $porto_color_lib->$function_name( $color_background, 50 ), $porto_color_lib->$function_name( $color_background, 60 ), $porto_color_lib->$function_name( $color_background, 70 ), $porto_color_lib->$function_name( $color_background, 80 ), $porto_color_lib->$function_name( $color_background, 90 ) );
?>
<?php foreach ( $color_background_scale as $index => $value ) : ?>
	html .section.section-default-scale-<?php echo porto_filter_output( $index + 1 ); ?> {
		background-color: <?php echo esc_html( $value ); ?> !important;
		border-top-color: <?php echo esc_html( $porto_color_lib->darken( $value, 3 ) ); ?> !important;
	}
<?php endforeach; ?>

/* side menu */
.side-nav-wrap .sidebar-menu > li.menu-item > a,
.main-sidebar-menu .sidebar-menu > li.menu-item > a,
.side-nav-wrap .sidebar-menu .menu-custom-block span,
.main-sidebar-menu .sidebar-menu .menu-custom-block span,
.side-nav-wrap .sidebar-menu .menu-custom-block a,
.main-sidebar-menu .sidebar-menu .menu-custom-block a {
	<?php if ( $b['menu-side-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-side-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-side-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-side-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-side-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-side-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}
<?php if ( class_exists( 'Woocommerce' ) && $b['menu-side-font']['line-height'] && empty( $b['side-menu-type'] ) ) : ?>
	.side-nav-wrap .sidebar-menu .popup:before,
	.main-sidebar-menu .sidebar-menu .popup:before {
		top: <?php echo  ( 23 + (float) $b['menu-side-font']['line-height'] - 20 ) / 2 + 1; ?>px
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		.side-nav-wrap .sidebar-menu .popup:before,
		.main-sidebar-menu .sidebar-menu .popup:before {
			top: <?php echo  ( 18 + (float) $b['menu-side-font']['line-height'] - 20 ) / 2 + 1; ?>px
		}
	}
<?php endif; ?>
<?php if ( $b['menu-side-font']['line-height'] && 'accordion' == $b['side-menu-type'] ) : ?>
	.header-side-nav .sidebar-menu > li.menu-item > .arrow,
	.main-sidebar-menu .sidebar-menu > li.menu-item > .arrow {
		top: <?php echo ( 18 + (float) $b['menu-side-font']['line-height'] - 30 ) / 2; ?>px
	}
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		.header-side-nav .sidebar-menu > li.menu-item > .arrow,
		.main-sidebar-menu .sidebar-menu > li.menu-item > .arrow { top: <?php echo ( 22 + (float) $b['menu-side-font']['line-height'] - 30 ) / 2; ?>px }
	}
<?php endif; ?>

<?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item > a,
	.side-nav-wrap .sidebar-menu > li.menu-item > .arrow:before,
	.side-nav-wrap .sidebar-menu .menu-custom-block a {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>
	}
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item:hover,
	.side-nav-wrap .sidebar-menu > li.menu-item.active,
	.side-nav-wrap .sidebar-menu .menu-custom-block a:hover {
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	<?php if ( 'transparent' != $b['mainmenu-toplevel-hbg-color'] ) : ?>
		.side-nav-wrap .sidebar-menu > li.menu-item > a,
		.side-nav-wrap .sidebar-menu > li.menu-item:hover > a,
		.side-nav-wrap .sidebar-menu > li.menu-item.active > a,
		.side-nav-wrap .sidebar-menu .menu-custom-block a,
		.side-nav-wrap .sidebar-menu .menu-custom-block a:hover,
		.side-nav-wrap .sidebar-menu .menu-custom-block a:hover + a {
			border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>;
		}
		.side-nav-wrap .sidebar-menu > li.menu-item.active + li.menu-item > a {
			border-top: none;
		}
		.sidebar-menu > li.menu-item:hover + li.menu-item > a {
			border-top-color: transparent;
		}
	<?php else : ?>
		.side-nav-wrap .sidebar-menu > li.menu-item > a { border-top-color: transparent; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item:hover > a,
	.side-nav-wrap .sidebar-menu > li.menu-item.active > a,
	.side-nav-wrap .sidebar-menu > li.menu-item.active > .arrow:before,
	.side-nav-wrap .sidebar-menu > li.menu-item:hover > .arrow:before,
	.side-nav-wrap .sidebar-menu .menu-custom-block a:hover {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
.toggle-menu-wrap .sidebar-menu > li.menu-item > a { border-top-color: rgba(0, 0, 0, .125) }

/* breadcrumb */
<?php
$css = array();
if ( $b['breadcrumbs-top-border']['border-top'] && '0px' != $b['breadcrumbs-top-border']['border-top'] ) {
	$css[] = 'border-top:' . esc_html( $b['breadcrumbs-top-border']['border-top'] ) . ' solid ' . esc_html( $b['breadcrumbs-top-border']['border-color'] );
}
if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) {
	$css[] = 'border-bottom:' . esc_html( $b['breadcrumbs-bottom-border']['border-top'] ) . ' solid ' . esc_html( $b['breadcrumbs-bottom-border']['border-color'] );
}
if ( ! empty( $css ) ) {
	echo '.page-top {' . implode( ';', $css ) . '}';
}
?>
.page-top > .container {
	padding-top: <?php echo porto_config_value( $b['breadcrumbs-padding']['padding-top'] ); ?>px;
	padding-bottom: <?php echo porto_config_value( $b['breadcrumbs-padding']['padding-bottom'] ); ?>px;
}
<?php if ( $b['breadcrumbs-text-color'] ) : ?>
	.page-top .yoast-breadcrumbs,
	.page-top .breadcrumbs-wrap {
		color: <?php echo esc_html( $b['breadcrumbs-text-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['breadcrumbs-link-color'] ) : ?>
	.page-top .yoast-breadcrumbs a,
	.page-top .breadcrumbs-wrap a,
	.page-top .product-nav .product-link {
		color: <?php echo esc_html( $b['breadcrumbs-link-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['breadcrumbs-delimiter-font'] ) || '0' == $b['breadcrumbs-delimiter-font'] ) : ?>
	.page-top ul.breadcrumb > li .delimiter {
		font-size: <?php echo esc_html( $b['breadcrumbs-delimiter-font'] ); ?>
	}
<?php endif; ?>
.page-top .page-title {
	<?php if ( $b['breadcrumbs-title-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['breadcrumbs-title-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-title-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['breadcrumbs-title-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-title-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['breadcrumbs-title-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-title-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['breadcrumbs-title-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-title-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['breadcrumbs-title-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-title-color'] ) : ?>
		color: <?php echo esc_html( $b['breadcrumbs-title-color'] ); ?>
	<?php endif; ?>
}
.page-top .page-sub-title {
	<?php if ( $b['breadcrumbs-subtitle-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['breadcrumbs-subtitle-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-subtitle-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['breadcrumbs-subtitle-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-subtitle-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['breadcrumbs-subtitle-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-subtitle-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['breadcrumbs-subtitle-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-subtitle-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['breadcrumbs-subtitle-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-subtitle-color'] ) : ?>
		color: <?php echo esc_html( $b['breadcrumbs-subtitle-color'] ); ?>;
	<?php endif; ?>
	margin: <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $right ] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $left ] ); ?>px;
}
<?php if ( $b['breadcrumbs-path-margin'] ) : ?>
	.page-top .breadcrumbs-wrap {
		margin: <?php echo porto_config_value( $b['breadcrumbs-path-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-path-margin'][ 'margin-' . $right ] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-path-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-path-margin'][ 'margin-' . $left ] ); ?>px;
	}
<?php endif; ?>
.page-top .breadcrumb {
	<?php if ( $b['breadcrumbs-path-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['breadcrumbs-path-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-path-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['breadcrumbs-path-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-path-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['breadcrumbs-path-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-path-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['breadcrumbs-path-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['breadcrumbs-path-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['breadcrumbs-path-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	margin: <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $right ] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $left ] ); ?>px;
}
<?php if ( $b['breadcrumbs-title-color'] ) : ?>
	.page-top .sort-source > li > a { color: <?php echo esc_html( $b['breadcrumbs-title-color'] ); ?>; }
<?php endif; ?>
@media (max-width: 767px) {
	.page-top .sort-source {
		<?php if ( ! empty( $b['breadcrumbs-bg']['background-color'] ) ) : ?>
			background: <?php echo esc_html( $b['breadcrumbs-bg']['background-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
			border-top: <?php echo esc_html( $b['breadcrumbs-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['breadcrumbs-bottom-border']['border-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
			margin-bottom: -<?php echo esc_html( $b['breadcrumbs-bottom-border']['border-top'] ); ?>;
			bottom: -<?php echo (int) porto_config_value( $b['breadcrumbs-bottom-border']['border-top'] ) + 1; ?>px;
		<?php endif; ?>
	}
}
<?php porto_calc_container_width( '#breadcrumbs-boxed', ( $header_bg_empty && ! $breadcrumb_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

<?php if ( $b['social-color'] ) : ?>
	#main .share-links a {
		background-color: <?php echo esc_html( $skin_color ); ?> !important;
		color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> !important;
	}
	#main .share-links a:hover {
		background-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?> !important;
	}
<?php endif; ?>

/* button */
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
	input[type="submit"].dokan-btn-theme, a.dokan-btn-theme, .dokan-btn-theme {
		background-color: <?php echo esc_html( $skin_color ); ?>;
		border-color: <?php echo esc_html( $skin_color ); ?>;
	}
	input[type="submit"].dokan-btn-theme:hover, a.dokan-btn-theme:hover, .dokan-btn-theme:hover, input[type="submit"].dokan-btn-theme:focus, a.dokan-btn-theme:focus, .dokan-btn-theme:focus, input[type="submit"].dokan-btn-theme:active, a.dokan-btn-theme:active, .dokan-btn-theme:active, input[type="submit"].dokan-btn-theme.active, a.dokan-btn-theme.active, .dokan-btn-theme.active, .open .dropdown-toggleinput[type="submit"].dokan-btn-theme, .open .dropdown-togglea.dokan-btn-theme, .open .dropdown-toggle.dokan-btn-theme {
		border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
		background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	}
<?php endif; ?>
<?php if ( 'btn-borders' == porto_get_button_style() ) : ?>
	.btn, .button, input.submit,
	[type="submit"].btn-primary,
	[type="submit"] {
		background: transparent;
		border-color: <?php echo esc_html( $skin_color ); ?>;
		color: <?php echo esc_html( $skin_color ); ?>;
		text-shadow: none;
		border-width: 3px;
		padding: 4px 10px;
	}
	.btn.btn-lg, .button.btn-lg, input.submit.btn-lg,
	[type="submit"].btn-primary.btn-lg,
	[type="submit"].btn-lg,
	.btn-group-lg > .btn, .btn-group-lg > .button, .btn-group-lg > input.submit,
	.btn-group-lg > [type="submit"].btn-primary,
	.btn-group-lg > [type="submit"] {
		padding: 8px 14px;
	}
	.btn.btn-sm, .button.btn-sm, input.submit.btn-sm,
	[type="submit"].btn-primary.btn-sm,
	[type="submit"].btn-sm,
	.btn-group-sm > .btn, .btn-group-sm > .button, .btn-group-sm > input.submit,
	.btn-group-sm > [type="submit"].btn-primary,
	.btn-group-sm > [type="submit"] {
		border-width: 2px;
		padding: 4px 10px;
	}
	.btn.btn-xs, .button.btn-xs, input.submit.btn-xs,
	[type="submit"].btn-primary.btn-xs,
	[type="submit"].btn-xs,
	.btn-group-xs > .btn, .btn-group-xs > .button, .btn-group-xs > input.submit,
	.btn-group-xs > [type="submit"].btn-primary,
	.btn-group-xs > [type="submit"] {
		padding: 1px 5px;
		border-width: 1px;
	}
	.btn:hover, .button:hover, input.submit:hover,
	[type="submit"].btn-primary:hover,
	[type="submit"]:hover,
	.btn:focus, .button:focus, input.submit:focus,
	[type="submit"].btn-primary:focus,
	[type="submit"]:focus,
	.btn:active, .button:active, input.submit:active,
	[type="submit"].btn-primary:active,
	[type="submit"]:active {
		background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
		border-color: <?php echo esc_html( $skin_color ); ?> !important;
		color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
	}

	.btn-default,
	[type="submit"].btn-default {
		border-color: #ccc;
		color: #333;
	}
	.btn-default:hover,
	[type="submit"].btn-default:hover,
	.btn-default:focus,
	[type="submit"].btn-default:focus,
	.btn-default:active,
	[type="submit"].btn-default:active {
		background-color: #e6e6e6;
		border-color: #adadad !important;
		color: #333;
	}

	body .cart-actions .button,
	body .checkout-button,
	body #place_order {
		padding: 8px 14px;
	}
	<?php foreach ( $theme_colors as $key => $theme_color ) : ?>
		.btn-<?php echo porto_filter_output( $key ); ?>,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?> {
			background: transparent;
			border-color: <?php echo esc_html( $theme_color ); ?>;
			color: <?php echo esc_html( $theme_color ); ?>;
			text-shadow: none;
			border-width: 3px;
		}
		.btn-<?php echo porto_filter_output( $key ); ?>:hover,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:hover,
		.btn-<?php echo porto_filter_output( $key ); ?>:focus,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:focus,
		.btn-<?php echo porto_filter_output( $key ); ?>:active,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:active {
			background-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 5 ) ); ?>;
			border-color: <?php echo esc_html( $theme_color ); ?> !important;
			color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
		}

	<?php endforeach; ?>
<?php endif; ?>

.popup .sub-menu,
.header-side-nav .narrow .popup {
	text-transform: <?php echo esc_html( $b['menu-popup-text-transform'] ); ?>;
}

<?php if ( $b['mainmenu-tip-bg-color'] ) : ?>
	.mega-menu .tip,
	.sidebar-menu .tip,
	.accordion-menu .tip,
	.menu-custom-block .tip {
		background: <?php echo esc_html( $b['mainmenu-tip-bg-color'] ); ?>;
		border-color: <?php echo esc_html( $b['mainmenu-tip-bg-color'] ); ?>;
	}
<?php endif; ?>

/* shortcodes */
.porto-vc-testimonial blockquote,
.testimonial blockquote,
.testimonial blockquote p {
	font-family: <?php echo isset( $b['shortcode-testimonial-font']['font-family'] ) && $b['shortcode-testimonial-font']['font-family'] ? sanitize_text_field( $b['shortcode-testimonial-font']['font-family'] ) . ',' : ''; ?>Georgia, serif;
	<?php if ( isset( $b['shortcode-testimonial-font']['letter-spacing'] ) && $b['shortcode-testimonial-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['shortcode-testimonial-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}

/* footer */
<?php if ( isset( $b['footer-text-color'] ) && $b['footer-text-color'] ) : ?>
	.footer,
	.footer p,
	.footer .widget > div > ul li,
	.footer .widget > ul li  {
		color: <?php echo esc_html( $b['footer-text-color'] ); ?>;
	}
	<?php if ( 'transparent' != $b['footer-text-color'] ) : ?>
		.footer .widget > div > ul,
		.footer .widget > ul,
		.footer .widget > div > ul li,
		.footer .widget > ul li,
		.footer .post-item-small {
			border-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['footer-text-color'] ) ); ?>, 0.3);
		}
	<?php endif; ?>
<?php endif; ?>
<?php if ( isset( $b['footer-link-color'] ) && $b['footer-link-color']['regular'] ) : ?>
	.footer a:not(.btn),
	.footer .tooltip-icon {
		color: <?php echo esc_html( $b['footer-link-color']['regular'] ); ?>;
	}
	.footer .tooltip-icon {
		border-color: <?php echo esc_html( $b['footer-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['footer-link-color'] ) && $b['footer-link-color']['hover'] ) : ?>
	.footer a:hover {
		color: <?php echo esc_html( $b['footer-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['footer-heading-color'] ) ) : ?>
	.footer h1,
	.footer h2,
	.footer h3,
	.footer h4,
	.footer h5,
	.footer h6,
	.footer .widget-title,
	.footer .widgettitle,
	.footer h1 a,
	.footer h2 a,
	.footer h3 a,
	.footer h4 a,
	.footer h5 a,
	.footer h6 a,
	.footer .widget-title a,
	.footer .widgettitle a,
	.footer .widget.twitter-tweets .fa-twitter {
		color: <?php echo esc_html( $b['footer-heading-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-ribbon-bg-color'] ) : ?>
	#footer .footer-ribbon {
		background-color: <?php echo esc_html( $b['footer-ribbon-bg-color'] ); ?>;
	}
	#footer .footer-ribbon:before {
		border-<?php echo porto_filter_output( $right ); ?>-color: <?php echo esc_html( $porto_color_lib->darken( $b['footer-ribbon-bg-color'], 15 ) ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-ribbon-text-color'] ) : ?>
	#footer .footer-ribbon,
	#footer .footer-ribbon a,
	#footer .footer-ribbon a:hover,
	#footer .footer-ribbon a:focus {
		color: <?php echo esc_html( $b['footer-ribbon-text-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['footer-bottom-link-color'] ) && $b['footer-bottom-link-color']['regular'] ) : ?>
	.footer .footer-bottom a,
	.footer .footer-bottom .widget_nav_menu ul li:before {
		color: <?php echo esc_html( $b['footer-bottom-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['footer-bottom-link-color'] ) && $b['footer-bottom-link-color']['hover'] ) : ?>
	.footer .footer-bottom a:hover {
		color: <?php echo esc_html( $b['footer-bottom-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['footer-social-bg-color'] ) && 'transparent' == $b['footer-social-bg-color'] ) : ?>
	.footer .share-links a:not(:hover),
	.footer-top .share-links a:not(:hover) {
		background: none;
		color: <?php echo esc_html( ! empty( $b['footer-social-link-color'] ) ? $b['footer-social-link-color'] : '#525252' ); ?>;
	}
<?php else : ?>
	.footer .share-links a:not(:hover),
	.footer-top .share-links a:not(:hover) {
		<?php if ( isset( $b['footer-social-bg-color'] ) && $b['footer-social-bg-color'] ) : ?>
			background: <?php echo esc_html( $b['footer-social-bg-color'] ); ?>;
		<?php endif; ?>
		color: <?php echo esc_html( ! empty( $b['footer-social-link-color'] ) ? $b['footer-social-link-color'] : '#525252' ); ?>;
	}
<?php endif; ?>
<?php porto_calc_container_width( '#footer-boxed', ( $header_bg_empty && ! $footer_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

/*------------------ Fonts ---------------------- */
<?php if ( $b['alt-font']['font-weight'] ) : ?>
	.alternative-font,
	#footer .footer-ribbon { font-weight: <?php echo esc_html( $b['alt-font']['font-weight'] ); ?>; }
<?php endif; ?>

/*------------------ Theme Dokan --------------------- */
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
	/*.dokan-dashboard .dokan-dash-sidebar,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu { background: #f4f4f4; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.dokan-common-links a { border-color: #e1e1e1; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li a { color: <?php echo esc_html( $b['dark-color'] ); ?>; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active a,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li:hover a { color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> }*/
	.dokan-form-group label.error { display: block; margin-top: 0; }
	.dokan-single-store .profile-info li { line-height: 24px; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active:after { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
	input[type="submit"].dokan-btn, a.dokan-btn, .dokan-btn { font-weight: 600; font-size: .9em; padding: 0.5rem 1rem; }
	.dokan-store-sidebar .widget>div>ul { border-bottom: none; }
	.dokan-store-sidebar .widget>div>ul li { padding: 0; border-top: none; }
	.dokan-store-sidebar .widget>div>ul li > ul { margin-top: 0; margin-bottom: 0; }
	.dokan-store-sidebar .widget>div>ul li > a { display: block; padding: 5px 0; }
	.dokan-store-sidebar .widget .caret-icon { float: <?php echo porto_filter_output( $right ); ?>; padding: 0 10px; }
	.dokan-store-widget .product_list_widget li { display: block }
<?php endif; ?>

/*------------------ Theme Shop ---------------------- */
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	<?php if ( defined( 'YITH_WOOCOMPARE' ) ) : ?>
		@media (max-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] - 1 ); ?>px) {
			.shop_table.wishlist_table .yith-compare, table.wishlist_table .yith-compare { margin-left: 0 !important; margin-right: 0 !important; }
		}
	<?php endif; ?>
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		.divider-line.pcols-lg-6 > .product-col:nth-child(6n),
		.divider-line.pcols-lg-5 > .product-col:nth-child(5n),
		.divider-line.pcols-lg-4 > .product-col:nth-child(4n),
		.divider-line.pcols-lg-3 > .product-col:nth-child(3n),
		.divider-line.pcols-lg-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}
	@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
		.divider-line.pcols-md-5 > .product-col:nth-child(5n),
		.divider-line.pcols-md-4 > .product-col:nth-child(4n),
		.divider-line.pcols-md-3 > .product-col:nth-child(3n),
		.divider-line.pcols-md-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}

	.stock,
	ul.cart_list .product-details a:hover,
	.product_list_widget .product-details a:hover,
	ul.cart_list li a:hover,
	ul.product_list_widget li a:hover,
	.shipping_calculator h2,
	.shipping_calculator h2 a,
	.product-subtotal .woocommerce-Price-amount { color: <?php echo esc_html( $skin_color ); ?> }

	<?php if ( isset( $b['product_variation_display_mode'] ) && 'button' == $b['product_variation_display_mode'] ) : ?>
		.single-product .variations .label label { line-height: 32px; }
	<?php endif; ?>

	/* WC Marketplace Compatibility */
	<?php if ( class_exists( 'WC_Dependencies_Product_Vendor' ) ) : ?>
		.wcmp-wrapper .nav { display: block; }
		.wcmp-wrapper .top-user-nav>li a.dropdown-toggle:after { display: none; }
		.wcmp-wrapper select,
		.wcmp-wrapper select.form-control { background-size: auto; }
		.wcmp-wrapper .row-actions span.divider { margin: 0; background-image: none; }
		.wcmp-wrapper .content-padding:not(.dashboard) .dataTables_wrapper.dt-bootstrap .dataTables_paginate li { text-indent: 0; width: auto; }
		.wcmp-wrapper .input-group-addon { width: auto; line-height: inherit; }
	<?php endif; ?>

	<?php if ( porto_is_product() ) : ?>
		.sidebar-content { padding-bottom: 15px; }
	<?php endif; ?>

	<?php if ( isset( $porto_settings['product-attr-desc'] ) && $porto_settings['product-attr-desc'] ) : ?>
		.product-attr-description { padding: 1rem 0 .125rem; }
		.product-attr-description > div { display: none; }
		.product-attr-description > a { font-size: .8571em; font-style: italic; color: inherit; text-decoration: none; display: none; }
		.product-attr-description.active > a { display: inline-block; }
		.product-attr-description .attr-desc { padding: .5rem 0 0; display: none; }
		.product-attr-description .attr-desc.active { display: block; }
	<?php endif; ?>

<?php endif; ?>

/*---------------- Skeleton screen ---------------- */
<?php
	$skeleton_color  = isset( $b['placeholder-color'] ) ? $b['placeholder-color'] : '#f4f4f4';
	$animation_color = $dark ? $porto_color_lib->hexToRGB( $color_dark ) : '255, 255, 255';
?>
<?php if ( ! empty( $b['show-skeleton-screen'] ) ) : ?>
	.skeleton-loading, .skeleton-loading-wrap { height: 0 !important; overflow: hidden !important; visibility: hidden; margin-top: 0 !important; margin-bottom: 0 !important; padding: 0 !important; min-height: 0 !important; }
	@keyframes skeletonloading {
		to {
			background-position: 200% 0;
		}
	}
	.skeleton-body.product .entry-summary,
	.products.skeleton-body li,
	.skeleton-body.product .tab-content,
	.skeleton-body .post { overflow: hidden; position: relative; }
	.skeleton-body.product .entry-summary:after,
	.products.skeleton-body li:after,
	.sidebar-content.skeleton-body aside:after,
	.skeleton-body.product .tab-content:after,
	.skeleton-body.tab-content:after,
	.skeleton-body .post:after {
		content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%; transform: rotate(45deg);
		background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 80%);
		background-size: 60% 100%;
		background-position: -100% 0;
		background-repeat: no-repeat;
		animation: skeletonloading 1.5s infinite .2s;
	}
	.skeleton-body.product .tab-content:after,
	.skeleton-body.tab-content:after { background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 80%); transform: none; animation-duration: 2s; }
	<?php
	if ( class_exists( 'Woocommerce' ) ) {
		$cropping = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
		if ( 'custom' === $cropping ) {
			$width  = (float) max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
			$height = (float) max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
		}
	}
	?>
	<?php if ( class_exists( 'Woocommerce' ) && ( in_array( 'product', $b['show-skeleton-screen'] ) || in_array( 'quickview', $b['show-skeleton-screen'] ) ) ) : ?>
		.skeleton-body.product { position: relative; z-index: 1; }
		.skeleton-body.product-layout-transparent .product-summary-wrap:before,
		.skeleton-body.product-layout-transparent .product-summary-wrap:after { content: none; }
		.skeleton-body.product .summary-before { order: initial; }

		/* quickview */
		.skeleton-body.product .summary-before { padding-top: 59%; }
		.skeleton-body.product .entry-summary { min-height: 500px; overflow: hidden; }
		.skeleton-body.product > .row { align-items: flex-start; }
		.skeleton-body.product > .row > div:before,
		.skeleton-body.tab-content:before { content: ''; display: block; position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: 0 <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; background-repeat: no-repeat; }
		.skeleton-body.product .summary-before:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0);
			background-size: 100% calc(80% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px);
			background-position: center top, left bottom, 33.3333% bottom, 66.6666% bottom, right bottom;
		}
		.skeleton-body.product .entry-summary:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 1px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 35px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0);
			background-size: 55% 25px, 70% 14px, 40% 18px, 100% 14px, 100% 14px, 100% 14px, 100% 14px, 40% 16px, 40% 16px, 100% 1px, 70% 25px, 60% 35px, 60% 25px;
			background-position: <?php echo porto_filter_output( $left ); ?> top, <?php echo porto_filter_output( $left ); ?> 34px, <?php echo porto_filter_output( $left ); ?> 75px, <?php echo porto_filter_output( $left ); ?> 120px, <?php echo porto_filter_output( $left ); ?> 147px, <?php echo porto_filter_output( $left ); ?> 174px, <?php echo porto_filter_output( $left ); ?> 201px, <?php echo porto_filter_output( $left ); ?> 240px, <?php echo porto_filter_output( $left ); ?> 270px, <?php echo porto_filter_output( $left ); ?> 320px, <?php echo porto_filter_output( $left ); ?> 350px, <?php echo porto_filter_output( $left ); ?> 395px, <?php echo porto_filter_output( $left ); ?> 455px;
		}
		@media (max-width: 767px) {
			.skeleton-body.product .summary-before { margin-bottom: 2rem; padding-top: 100%; max-width: 100%; }
		}
		@media (max-width: 991px) {
			.quickview-wrap.skeleton-body .summary-before { margin-bottom: 30px; }
		}
	<?php endif; ?>
	<?php if ( class_exists( 'Woocommerce' ) && in_array( 'product', $b['show-skeleton-screen'] ) ) : ?>
		@media (min-width: 768px) {
			.skeleton-body.product-layout-default .summary-before.col-md-5,
			.skeleton-body.product-layout-builder .summary-before.col-md-5 { padding-top: 50% }
		}
		.skeleton-body.product .tab-content,
		.tab-content.skeleton-body { min-height: 180px; position: relative; margin-top: 3rem; padding-top: 0 !important; }
		.skeleton-body.tab-content:before { margin: 0 }
		.skeleton-body.product .tab-content:before,
		.skeleton-body.tab-content:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0);
			background-size: 100px 40px, 100px 40px, 100px 40px, 100% 14px, 100% 14px, 100% 14px, 100% 14px;
			background-position: 0 0, 120px 0, 240px 0, 0 70px, 0 100px, 0 130px, 0 160px;
		}
	<?php endif; ?>
	<?php if ( class_exists( 'Woocommerce' ) && in_array( 'shop', $b['show-skeleton-screen'] ) ) : ?>
		<?php
		if ( 'custom' === $cropping ) {
			$product_h = round( ( $height / $width - 1 ) * 50 + 100 );
			$thumb_h   = round( $product_h * 0.62 );
		} else {
			$product_h = 100;
			$thumb_h   = 62;
		}
		?>
		.products.skeleton-body .product:before {
			content: ''; display: block; padding-top: calc(<?php echo (int) $product_h; ?>% + 110px); background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0);
			background-size: 100% <?php echo (int) $thumb_h; ?>%, 50% 12px, 70% 16px, 50% 12px, 40% 16px;
			background-position: <?php echo porto_filter_output( $left ); ?> 0, <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 20px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 44px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 66px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 92px);
		}
		.grid.skeleton-body .product-default:before,
		.grid.skeleton-body .product-wq_onimage:before { background-position-x: center, center, center, center, center }
		<?php $bar_bg_pos = $rtl ? 'calc(100% - 270px)' : '270px'; ?>
		@media (min-width: 576px) {
			.list.skeleton-body .product:before {
				padding-top: 250px;
				background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 250px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
				background-size: 250px 100%, 150px 12px, 250px 16px, 150px 12px, 100% 14px, 100% 14px, 120px 16px, 200px 20px;
				background-position: <?php echo porto_filter_output( $left ); ?> 0, <?php echo porto_filter_output( $bar_bg_pos ); ?> 20px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 44px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 70px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 105px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 130px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 170px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 205px;
			}
			.list.skeleton-body .product:after { transform: none; background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 40%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 60%); animation: skeletonloading 2.5s infinite .2s; }
		}
	<?php endif; ?>
	<?php if ( in_array( 'blog', $b['show-skeleton-screen'] ) ) : ?>
		.skeleton-body .post { border-bottom: none; margin-bottom: 30px; padding-bottom: 0; }
		.skeleton-body .post:before {
			content: ''; display: block; padding-top: calc(60% + 165px); background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
			background-size: 100% calc(100% - 165px), 50% 16px, 100% 14px, 100% 14px, 100% 14px, 70% 20px;
			background-position: 0 0, <?php echo porto_filter_output( $left ); ?> calc(100% - 130px), <?php echo porto_filter_output( $left ); ?> calc(100% - 100px), <?php echo porto_filter_output( $left ); ?> calc(100% - 75px), <?php echo porto_filter_output( $left ); ?> calc(100% - 50px), <?php echo porto_filter_output( $left ); ?> calc(100% - 10px);
		}
		@media (min-width: 992px) {
			.posts-medium.skeleton-body .post:before,
			.posts-medium-alt.skeleton-body .post:before {
				padding-top: 25%; min-height: 180px;
				background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
				background-size: 40% 100%, 30% 16px, 58% 14px, 58% 14px, 58% 14px, 58% 14px, 58% 20px;
				background-position: <?php echo ! $rtl ? '0' : '100%'; ?> 0, <?php echo ! $rtl ? '60%' : '40%'; ?> 10px, <?php echo ! $rtl ? '100%' : '0'; ?> 45px, <?php echo ! $rtl ? '100%' : '0'; ?> 70px, <?php echo ! $rtl ? '100%' : '0'; ?> 95px, <?php echo ! $rtl ? '100%' : '0'; ?> 120px, <?php echo ! $rtl ? '100%' : '0'; ?> 155px;
			}
			.posts-medium.skeleton-body .post:after,
			.posts-medium-alt.skeleton-body .post:after { transform: none; background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 40%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 60%); animation: skeletonloading 2s infinite .2s; }
		}
	<?php endif; ?>

	<?php $bar_bg_pos = $rtl ? 'calc(100% - 45px)' : '45px'; ?>
	.sidebar-content.skeleton-body aside { overflow: hidden; position: relative; }
	.sidebar-content.skeleton-body aside:before {
		content: ''; display: block; height: 320px; background-repeat: no-repeat;
		background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0);
		background-size: 55% 20px, 30px 30px, 100% 14px, 30px 30px, 60% 14px, 30px 30px, 80% 14px, 30px 30px, 50% 14px, 30px 30px, 100% 14px, 30px 30px, 70% 14px;
		background-position: <?php echo porto_filter_output( $left ); ?> 5px, <?php echo porto_filter_output( $left ); ?> 50px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 58px, <?php echo porto_filter_output( $left ); ?> 95px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 103px, <?php echo porto_filter_output( $left ); ?> 140px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 148px, <?php echo porto_filter_output( $left ); ?> 185px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 193px, <?php echo porto_filter_output( $left ); ?> 230px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 238px, <?php echo porto_filter_output( $left ); ?> 275px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 283px;

	}
<?php endif; ?>

<?php if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
	<?php
		$nav_loading_color = $b['panel-bg-color'] && 'transparent' != $b['panel-bg-color'] ? $porto_color_lib->hexToRGB( $b['panel-bg-color'] ) : ( $dark ? $porto_color_lib->hexToRGB( $color_dark ) : '255, 255, 255' );
	if ( empty( $b['mobile-panel-type'] ) ) {
		$nav_skeleton_color = isset( $b['placeholder-color'] ) ? $b['placeholder-color'] : '#f4f4f4';
		$nav_selector       = '#nav-panel';
		if ( $b['panel-bg-color'] && 'transparent' != $b['panel-bg-color'] && $porto_color_lib->isColorDark( $b['panel-bg-color'] ) ) {
			$nav_skeleton_color = $porto_color_lib->lighten( $b['panel-bg-color'], 12 );
			$nav_loading_color  = $porto_color_lib->hexToRGB( $porto_color_lib->lighten( $b['panel-bg-color'], 8 ) );
		}
	} else {
		$nav_skeleton_color = $porto_color_lib->lighten( $b['panel-bg-color'], 15 );
		$nav_selector       = '#side-nav-panel';
	}

	$submenu_skeleton_color  = $skeleton_color;
	$submenu_animation_color = $animation_color;
	if ( $porto_color_lib->isColorDark( $b['mainmenu-popup-bg-color'] ) ) {
		$submenu_skeleton_color  = $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 15 );
		$submenu_animation_color = $porto_color_lib->hexToRGB( $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 5 ) );
	}
	?>
	@keyframes side_nav_loading {
		to {
			background-position: 200% 0;
		}
	}

	<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
		#nav-panel .skeleton-body { position: relative; overflow: hidden; }
		#nav-panel .skeleton-body:after {
			content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%;
			transform: rotate(45deg);
			background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 80%);
			background-size: 60% 100%;
			background-position: -100% 0;
			background-repeat: no-repeat;
			animation: side_nav_loading 1.5s infinite .2s;
		}
		@media (min-width: 480px) {
			#nav-panel .skeleton-body:after {
				transform: none;
				background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 80%);
				animation-duration: 2s;
			}
		}
		<?php echo porto_filter_output( $nav_selector ); ?> .skeleton-body:before {
			content: ''; display: block; min-height: 200px; background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0);
			background-size: 100% 20px, 100% 20px, 100% 20px, 100% 20px, 100% 20px;
			background-position: 0 0, 0 40px, 0 80px, 0 120px, 0 160px;
		}
	<?php endif; ?>

	.menu-item.narrow .sub-menu.skeleton-body,
	.menu-item.wide .sub-menu.skeleton-body li { overflow: hidden; position: relative; }
	.menu-item.narrow .sub-menu.skeleton-body {  }
	.menu-item.wide .sub-menu.skeleton-body li { flex: 1; }
	.menu-item.narrow .sub-menu.skeleton-body:after,
	.menu-item.wide .sub-menu.skeleton-body li:after {
		content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%; background-repeat: no-repeat;
		transform: rotate(45deg);
		background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $submenu_animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $submenu_animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $submenu_animation_color ); ?>, 0) 80%);
		background-size: 60% 100%;
		background-position: -100% 0;
		background-repeat: no-repeat;
	}
	.menu-item.narrow .sub-menu.skeleton-body:before,
	.menu-item.wide .sub-menu.skeleton-body li:before {
		content: ''; display: block; background-repeat: no-repeat; min-height: 180px; margin: 15px 10px 0;
		background-image: linear-gradient(<?php echo esc_html( $submenu_skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $submenu_skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $submenu_skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $submenu_skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $submenu_skeleton_color ); ?> 18px, transparent 0);
		background-size: 100% 18px, 100% 18px, 100% 18px, 100% 18px, 100% 18px;
		background-position: 0 0, 0 36px, 0 72px, 0 108px, 0 144px;
	}
	.mega-menu > li.menu-item:hover .skeleton-body:after,
	.mega-menu > li.menu-item:hover .skeleton-body li:after {
		animation: side_nav_loading 1.5s infinite .2s;
	}
<?php endif; ?>

/*---------------- Elementor Styles ---------------- */
<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
	<?php if ( (int) $b['container-width'] >= 1360 ) : ?>
	@media (min-width: <?php echo 1140 + (int) $b['grid-gutter-width']; ?>px) and (max-width: <?php echo (int) $b['container-width'] + 2 * (int) $b['grid-gutter-width'] - 1; ?>px) {
		.elementor-section.elementor-section-boxed > .elementor-container { max-width: 1140px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-no { max-width: <?php echo 1140 - (int) $b['grid-gutter-width']; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-narrow { max-width: <?php echo 1140 - (int) $b['grid-gutter-width'] + 10; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-extended { max-width: <?php echo 1140 - (int) $b['grid-gutter-width'] + 30; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wide { max-width: <?php echo 1140 - (int) $b['grid-gutter-width'] + 40; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wider { max-width: <?php echo 1140 - (int) $b['grid-gutter-width'] + 60; ?>px }
	}
	@media (min-width: 992px) and (max-width: <?php echo 1140 + (int) $b['grid-gutter-width'] - 1; ?>px) 
	<?php else : ?>
	@media (min-width: 992px) and (max-width: <?php echo (int) $b['container-width'] + $b['grid-gutter-width'] - 1; ?>px) 
	<?php endif; ?>
	{
		.elementor-section.elementor-section-boxed > .elementor-container { max-width: 960px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-no { max-width: <?php echo 960 - (int) $b['grid-gutter-width']; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-narrow { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 10; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-extended { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 30; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wide { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 40; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wider { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 60; ?>px }
	}
	<?php if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) : ?>
		.elementor-products-grid .products li.product-col { max-width: none; padding: 0; margin-bottom: 0 }
		.elementor-products-grid .products li.product-col .woocommerce-loop-product__title,
		.elementor-products-grid .products .category-list { white-space: normal }
		.elementor-products-grid nav.woocommerce-pagination { margin-top: 0 }
	<?php endif; ?>
<?php endif; ?>

/*---------------- Visual Composer Styles ---------------- */
<?php if ( defined( 'VCV_VERSION' ) ) : ?>
	.vce-row--col-gap-<?php echo (int) $b['grid-gutter-width']; ?> > .vce-row-content > .vce-col { margin-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px }
	.vce-row--col-gap-<?php echo (int) $b['grid-gutter-width']; ?> > .vce-row-content > .vce-column-resizer .vce-column-resizer-handler { width: <?php echo (int) $b['grid-gutter-width']; ?>px }
<?php endif; ?>

/*---------------- Gutenberg Styles ---------------- */
.wp-block-columns, .page-content > .wp-block-columns.alignwide { margin-left: calc( -1 * var(--porto-column-spacing) ); margin-right: calc( -1 * var(--porto-column-spacing) ); flex-wrap: wrap }
@media (min-width: 768px) {
	.wp-block-columns.alignwide { max-width: none; width: auto }
}
.wp-block-column { padding-left: var(--porto-column-spacing); padding-right: var(--porto-column-spacing) }

/*.wp-block-column:not([class*=" col-"]) { flex-basis: 50%; }*/
.wp-block-columns.is-not-stacked-on-mobile>.wp-block-column:not(:first-child),
.wp-block-columns .wp-block-column { margin-left: 0; margin-right: 0 }
@media (max-width: 781px) and (min-width: 600px) {
	.wp-block-column:not(:only-child),
	.wp-block-columns:not(.is-not-stacked-on-mobile)>.wp-block-column:not(:only-child) {
		flex-basis: 50%!important;
		flex-grow: 0;
	}
	.wp-block-columns:not(.is-not-stacked-on-mobile)>.wp-block-column:nth-child(2n) {
		margin-left: 0;
		margin-right: 0;
	}
}
@media (min-width: 782px) {
	.wp-block-columns:not(.is-not-stacked-on-mobile)>.wp-block-column:not(:first-child) {
		margin-left: 0;
		margin-right: 0;
	}
}
.btn.btn-block { display: block; padding-left: 0; padding-right: 0 }

/* header builder */
.gutenberg-hb > .porto-block,
.gutenberg-hb .porto-section,
.gutenberg-hb .porto-section > .container {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
}
.gutenberg-hb .porto-section {
	flex: 0 0 auto;
	width: 100%;
}
.gutenberg-hb > .porto-block > *:not(.porto-section),
.gutenberg-hb .porto-section > *:not(.container),
.gutenberg-hb .porto-section > .container > * {
	margin-<?php echo porto_filter_output( $right ); ?>: .875rem
}
.gutenberg-hb > .porto-block > *:last-child,
.gutenberg-hb .porto-section > *:not(.container):last-child,
.gutenberg-hb .porto-section > .container > *:last-child {
	margin-<?php echo porto_filter_output( $right ); ?>: 0
}
<?php
global $wp_version;
if ( version_compare( $wp_version, '6.0', '>=' ) ) :
	?>
	/* Styles above WordPress Version 6.0 */
	.page-wrapper [class*=wp-container-] {
		gap: 0 !important;
		flex-wrap: wrap!important;
	}
<?php endif; ?>
/*---------------- Sticky Icon Bar ---------------- */
<?php if ( ! empty( $porto_settings['show-icon-menus-mobile'] ) || is_customize_preview() ) : ?>
	.porto-sticky-navbar:not(.fixed) { display: none }
	.porto-sticky-navbar > div { text-align: center; padding: 10px 0 5px;}
	.porto-sticky-navbar .sticky-icon + .sticky-icon { border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo porto_if_dark( '#333', '#f1f1f1' ); ?>; }
	.porto-sticky-navbar a, .porto-sticky-navbar .label { color: <?php echo porto_if_dark( '#fff', '#222529' ); ?>; }
	.porto-sticky-navbar i { font-size: 27px; display: block; line-height: 27px; margin-bottom: 4px }
	.porto-sticky-navbar .porto-icon-bars { font-size: 22px; }
	.porto-sticky-navbar .label { display: block; font-family: <?php echo sanitize_text_field( $b['h4-font']['font-family'] ); ?>, sans-serif; text-transform: uppercase; font-size: 9px; letter-spacing: -.5px; font-weight: 600; }
	.porto-sticky-navbar .cart-icon { display: inline-block; position: relative; }
	.porto-sticky-navbar .cart-items { display: inline-block; position: absolute; width: 15px; height: 15px; top: -2px; right: -6px; background-color: #ed5348; color: #fff; line-height: 15px; font-size: 9px; font-weight: 600; text-align: center; border-radius: 8px; box-shadow: -1px 1px 2px 0 rgba(0,0,0,0.3); overflow: hidden; }
	@media (max-width: 575px) {
		.porto-sticky-navbar.fixed { 
			display: flex;
			position: fixed;
			bottom: 0;
			<?php echo porto_filter_output( $left ); ?>: 0;
			width: 100%;
			background-color: <?php echo porto_if_dark( $color_dark_1, '#fff' ); ?>; 
			border-top: 1px solid <?php echo porto_if_dark( '#333', '#e7e7e7' ); ?>;
			z-index: 1040;
			animation: .3s linear menuFadeInUp;
			transition: <?php echo porto_filter_output( $left ); ?> .3s;
		}
		.sidebar-opened .porto-sticky-navbar.fixed, .panel-opened .porto-sticky-navbar.fixed {
			<?php echo porto_filter_output( $left ); ?>: 260px;
		}
		#footer { margin-bottom: 3.75rem }
	}
<?php endif; ?>
