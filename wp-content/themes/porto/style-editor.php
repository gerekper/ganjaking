<?php
/**
 * Porto: Gutenberg Editor Style
 *
 * @package porto
 * @since 5.0
 */

global $porto_settings;
$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;
$porto_is_dark         = ( 'dark' == $b['css-type'] );
$dark                  = $porto_is_dark;

if ( is_rtl() ) {
	$left_escaped  = 'right';
	$right_escaped = 'left';
	$rtl_escaped   = true;
} else {
	$left_escaped  = 'left';
	$right_escaped = 'right';
	$rtl_escaped   = false;
}

if ( $dark ) {
	$color_dark = $b['color-dark'];
} else {
	$color_dark = $b['dark-color'];
}

$skin_color = $b['skin-color'];

require_once( PORTO_LIB . '/lib/color-lib.php' );
$porto_color_lib = PortoColorLib::getInstance();
?>

<?php if ( ! $b['thumb-padding'] ) : ?>
	.thumb-info { border-width: 0 }
<?php endif; ?>

<?php
/* menu */
if ( ! empty( $b['header-text-color'] ) ) :
	?>
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
<?php if ( ! empty( $b['header-bottom-text-color'] ) ) : ?>
	#header .header-bottom { color: <?php echo esc_html( $b['header-bottom-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-link-color']['regular'] ) ) : ?>
	#header .header-bottom a:not(.btn) { color: <?php echo esc_html( $b['header-bottom-link-color']['regular'] ); ?> }
<?php endif; ?>

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

/* swticher */
<?php if ( $b['switcher-link-color']['regular'] ) : ?>
	#header .porto-view-switcher > li.menu-item:before,
	#header .porto-view-switcher > li.menu-item > a { color: <?php echo esc_html( $b['switcher-link-color']['regular'] ); ?>; }
<?php endif; ?>

<?php
	$header_type = porto_get_header_type();
?>

/* mini cart */
.cart-popup .quantity, .cart-popup .quantity .amount { color: #696969 !important; }
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
	#mini-cart .cart-icon:after { border-color: <?php echo esc_html( $b['minicart-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( isset( $b['minicart-bg-color'] ) && $b['minicart-bg-color'] ) : ?>
	#mini-cart {
		background: <?php echo esc_html( $b['minicart-bg-color'] ); ?>;
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

/* social icons */
<?php if ( ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
	#header .share-links a { width: 30px; height: 30px; border-radius: 30px; margin: 0 1px; overflow: hidden; font-size: .8rem; }
	#header .share-links a:not(:hover) { background-color: #fff; color: #333; }
<?php endif; ?>

/* header vertical divider */
#header .separator { border-left: 1px solid <?php echo porto_filter_output( $porto_color_lib->isColorDark( isset( $b['header-link-color']['regular'] ) ? $b['header-link-color']['regular'] : '' ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
#header .header-top .separator { border-left-color: <?php echo porto_filter_output( $porto_color_lib->isColorDark( isset( $b['header-top-link-color']['regular'] ) ? $b['header-top-link-color']['regular'] : '' ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
