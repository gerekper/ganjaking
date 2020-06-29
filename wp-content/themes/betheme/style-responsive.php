<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>

<?php
	$mobileMenuInitW = $stickyMenuInitW = mfn_opts_get( 'mobile-menu-initial', 1240 );

	$responsive_header_tab = mfn_opts_get( 'responsive-header-tablet' );
	if( is_array( $responsive_header_tab ) && isset( $responsive_header_tab['sticky'] ) ){
		$stickyMenuInitW = 768;
	}

	if( mfn_header_style( true ) == 'header-fixed' ){
		$stickyMenuInitW = 9999;	// disable sticky header
	}
?>

/**
 * Menu | Desktop *****
 */

@media only screen and (min-width: <?php echo esc_attr($mobileMenuInitW); ?>px)
{
	body:not(.header-simple) #Top_bar #menu{display:block!important}
	.tr-menu #Top_bar #menu{background:none!important}

	/* Mega Menu */

	#Top_bar .menu > li > ul.mfn-megamenu{width:984px}
	#Top_bar .menu > li > ul.mfn-megamenu > li{float:left}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-1{width:100%}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-2{width:50%}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-3{width:33.33%}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-4{width:25%}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-5{width:20%}
	#Top_bar .menu > li > ul.mfn-megamenu > li.mfn-megamenu-cols-6{width:16.66%}
	#Top_bar .menu > li > ul.mfn-megamenu > li > ul{display:block!important;position:inherit;left:auto;top:auto;border-width:0 1px 0 0}
	#Top_bar .menu > li > ul.mfn-megamenu > li:last-child > ul{border:0}
	#Top_bar .menu > li > ul.mfn-megamenu > li > ul li{width:auto}
	#Top_bar .menu > li > ul.mfn-megamenu a.mfn-megamenu-title{text-transform:uppercase;font-weight:400;background:none}
	#Top_bar .menu > li > ul.mfn-megamenu a .menu-arrow{display:none}
	.menuo-right #Top_bar .menu > li > ul.mfn-megamenu{left:auto;right:0}
	.menuo-right #Top_bar .menu > li > ul.mfn-megamenu-bg{box-sizing:border-box}

	/* Mega Menu | Background Image */

	#Top_bar .menu > li > ul.mfn-megamenu-bg{padding:20px 166px 20px 20px;background-repeat:no-repeat;background-position:right bottom}
	.rtl #Top_bar .menu > li > ul.mfn-megamenu-bg{padding-left:166px;padding-right:20px;background-position:left bottom}
	#Top_bar .menu > li > ul.mfn-megamenu-bg > li{background:none}
	#Top_bar .menu > li > ul.mfn-megamenu-bg > li a{border:none}
	#Top_bar .menu > li > ul.mfn-megamenu-bg > li > ul{background:none!important;-webkit-box-shadow:0 0 0 0;-moz-box-shadow:0 0 0 0;box-shadow:0 0 0 0}

	/* Mega Menu | Style: Vertical Lines */

	.mm-vertical #Top_bar .container{position:relative;}
	.mm-vertical #Top_bar .top_bar_left{position:static;}
	.mm-vertical #Top_bar .menu > li ul{box-shadow:0 0 0 0 transparent!important;background-image:none;}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu{width:98%!important;margin:0 1%;padding:20px 0;}
	.mm-vertical.header-plain #Top_bar .menu > li > ul.mfn-megamenu{width:100%!important;margin:0;}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li{display:table-cell;float:none!important;width:10%;padding:0 15px;border-right:1px solid rgba(0, 0, 0, 0.05);}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li:last-child{border-right-width:0}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li.hide-border{border-right-width:0}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li a{border-bottom-width:0;padding:9px 15px;line-height:120%;}
	.mm-vertical #Top_bar .menu > li > ul.mfn-megamenu a.mfn-megamenu-title{font-weight:700;}

	.rtl .mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li:first-child{border-right-width:0}
	.rtl .mm-vertical #Top_bar .menu > li > ul.mfn-megamenu > li:last-child{border-right-width:1px}

	/* Header | Plain */

	.header-plain:not(.menuo-right) #Header .top_bar_left{width:auto!important}

	/* Header | Stack */

	.header-stack.header-center #Top_bar #menu{display:inline-block!important}

	/* Header Simple | .header-simple */

	.header-simple #Top_bar #menu{display:none;height:auto;width:300px;bottom:auto;top:100%;right:1px;position:absolute;margin:0}
	.header-simple #Header a.responsive-menu-toggle{display:block;right:10px}

		/* Header Simple | Main Menu |  1st level */

		.header-simple #Top_bar #menu > ul{width:100%;float:left}
		.header-simple #Top_bar #menu ul li{width:100%;padding-bottom:0;border-right:0;position:relative}
		.header-simple #Top_bar #menu ul li a{padding:0 20px;margin:0;display:block;height:auto;line-height:normal;border:none}
		.header-simple #Top_bar #menu ul li a:after{display:none}
		.header-simple #Top_bar #menu ul li a span{border:none;line-height:44px;display:inline;padding:0}
		.header-simple #Top_bar #menu ul li.submenu .menu-toggle{display:block;position:absolute;right:0;top:0;width:44px;height:44px;line-height:44px;font-size:30px;font-weight:300;text-align:center;cursor:pointer;color:#444;opacity:0.33;}
		.header-simple #Top_bar #menu ul li.submenu .menu-toggle:after{content:"+"}
		.header-simple #Top_bar #menu ul li.hover > .menu-toggle:after{content:"-"}
		.header-simple #Top_bar #menu ul li.hover a{border-bottom:0}
		.header-simple #Top_bar #menu ul.mfn-megamenu li .menu-toggle{display:none}

		/* Header Simple | Main Menu | 2nd level */

		.header-simple #Top_bar #menu ul li ul{position:relative!important;left:0!important;top:0;padding:0;margin:0!important;width:auto!important;background-image:none}
		.header-simple #Top_bar #menu ul li ul li{width:100%!important;display:block;padding:0;}
		.header-simple #Top_bar #menu ul li ul li a{padding:0 20px 0 30px}
		.header-simple #Top_bar #menu ul li ul li a .menu-arrow{display:none}
		.header-simple #Top_bar #menu ul li ul li a span{padding:0}
		.header-simple #Top_bar #menu ul li ul li a span:after{display:none!important}
		.header-simple #Top_bar .menu > li > ul.mfn-megamenu a.mfn-megamenu-title{text-transform:uppercase;font-weight:400}
		.header-simple #Top_bar .menu > li > ul.mfn-megamenu > li > ul{display:block!important;position:inherit;left:auto;top:auto}

		/* Header Simple | Main Menu | 3rd level */

		.header-simple #Top_bar #menu ul li ul li ul{border-left:0!important;padding:0;top:0}
		.header-simple #Top_bar #menu ul li ul li ul li a{padding:0 20px 0 40px}

		/* Header Simple | RTL */

		.rtl.header-simple #Top_bar #menu{left:1px;right:auto}
		.rtl.header-simple #Top_bar a.responsive-menu-toggle{left:10px;right:auto}
		.rtl.header-simple #Top_bar #menu ul li.submenu .menu-toggle{left:0;right:auto}
		.rtl.header-simple #Top_bar #menu ul li ul{left:auto!important;right:0!important}
		.rtl.header-simple #Top_bar #menu ul li ul li a{padding:0 30px 0 20px}
		.rtl.header-simple #Top_bar #menu ul li ul li ul li a{padding:0 40px 0 20px}

	/* Menu style | Highlight */

	.menu-highlight #Top_bar .menu > li{margin:0 2px}
	.menu-highlight:not(.header-creative) #Top_bar .menu > li > a{margin:20px 0;padding:0;-webkit-border-radius:5px;border-radius:5px}
	.menu-highlight #Top_bar .menu > li > a:after{display:none}
	.menu-highlight #Top_bar .menu > li > a span:not(.description){line-height:50px}
	.menu-highlight #Top_bar .menu > li > a span.description{display:none}
	.menu-highlight.header-stack #Top_bar .menu > li > a{margin:10px 0!important}
	.menu-highlight.header-stack #Top_bar .menu > li > a span:not(.description){line-height:40px}
	.menu-highlight.header-transparent #Top_bar .menu > li > a{margin:5px 0}
	.menu-highlight.header-simple #Top_bar #menu ul li,.menu-highlight.header-creative #Top_bar #menu ul li{margin:0}
	.menu-highlight.header-simple #Top_bar #menu ul li > a,.menu-highlight.header-creative #Top_bar #menu ul li > a{-webkit-border-radius:0;border-radius:0}
	.menu-highlight:not(.header-fixed):not(.header-simple) #Top_bar.is-sticky .menu > li > a{margin:10px 0!important;padding:5px 0!important}
	.menu-highlight:not(.header-fixed):not(.header-simple) #Top_bar.is-sticky .menu > li > a span{line-height:30px!important}
	.header-modern.menu-highlight.menuo-right .menu_wrapper{margin-right:20px}

	/* Menu style | Line Below  */

	.menu-line-below #Top_bar .menu > li > a:after{top:auto;bottom:-4px}
	.menu-line-below #Top_bar.is-sticky .menu > li > a:after{top:auto;bottom:-4px}
	.menu-line-below-80 #Top_bar:not(.is-sticky) .menu > li > a:after{height:4px;left:10%;top:50%;margin-top:20px;width:80%}
	.menu-line-below-80-1 #Top_bar:not(.is-sticky) .menu > li > a:after{height:1px;left:10%;top:50%;margin-top:20px;width:80%}

	/* Menu style | Link color only  */

	.menu-link-color #Top_bar .menu > li > a:after{display:none!important}

	/* Menu style | Arrow Top  */

	.menu-arrow-top #Top_bar .menu > li > a:after{background:none repeat scroll 0 0 rgba(0,0,0,0)!important;border-color:#ccc transparent transparent;border-style:solid;border-width:7px 7px 0;display:block;height:0;left:50%;margin-left:-7px;top:0!important;width:0}
	.menu-arrow-top #Top_bar.is-sticky .menu > li > a:after{top:0!important}

	/* Menu style | Arrow Bottom  */

	.menu-arrow-bottom #Top_bar .menu > li > a:after{background:none!important;border-color:transparent transparent #ccc;border-style:solid;border-width:0 7px 7px;display:block;height:0;left:50%;margin-left:-7px;top:auto;bottom:0;width:0}
	.menu-arrow-bottom #Top_bar.is-sticky .menu > li > a:after{top:auto;bottom:0}

	/* Menu style | No Borders  */

	.menuo-no-borders #Top_bar .menu > li > a span{border-width:0!important}
	.menuo-no-borders #Header_creative #Top_bar .menu > li > a span{border-bottom-width:0}

	.menuo-no-borders.header-plain #Top_bar a#header_cart,
	.menuo-no-borders.header-plain #Top_bar a#search_button,
	.menuo-no-borders.header-plain #Top_bar .wpml-languages,
	.menuo-no-borders.header-plain #Top_bar a.action_button{border-width:0}

	/* Menu style | Right  */

	.menuo-right #Top_bar .menu_wrapper{float:right}
	.menuo-right.header-stack:not(.header-center) #Top_bar .menu_wrapper{margin-right:150px}

	/* Header Creative */

	body.header-creative{padding-left:50px}
	body.header-creative.header-open{padding-left:250px}
	body.error404,body.under-construction,body.template-blank{padding-left:0!important}

	.header-creative.footer-fixed #Footer,.header-creative.footer-sliding #Footer,.header-creative.footer-stick #Footer.is-sticky{box-sizing:border-box;padding-left:50px;}
	.header-open.footer-fixed #Footer,.header-open.footer-sliding #Footer,.header-creative.footer-stick #Footer.is-sticky{padding-left:250px;}

	.header-rtl.header-creative.footer-fixed #Footer,.header-rtl.header-creative.footer-sliding #Footer,.header-rtl.header-creative.footer-stick #Footer.is-sticky{padding-left:0;padding-right:50px;}
	.header-rtl.header-open.footer-fixed #Footer,.header-rtl.header-open.footer-sliding #Footer,.header-rtl.header-creative.footer-stick #Footer.is-sticky{padding-right:250px;}

	#Header_creative{background-color:#fff;position:fixed;width:250px;height:100%;left:-200px;top:0;z-index:9002;-webkit-box-shadow:2px 0 4px 2px rgba(0,0,0,.15);box-shadow:2px 0 4px 2px rgba(0,0,0,.15)}
	#Header_creative .container{width:100%}

	#Header_creative .creative-wrapper{opacity:0;margin-right:50px}

	#Header_creative a.creative-menu-toggle{display:block;width:34px;height:34px;line-height:34px;font-size:22px;text-align:center;position:absolute;top:10px;right:8px;border-radius:3px}
	.admin-bar #Header_creative a.creative-menu-toggle{top:42px}

	#Header_creative #Top_bar{position:static;width:100%}
	#Header_creative #Top_bar .top_bar_left{width:100%!important;float:none}
	#Header_creative #Top_bar .top_bar_right{width:100%!important;float:none;height:auto;margin-bottom:35px;text-align:center;padding:0 20px;top:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
	#Header_creative #Top_bar .top_bar_right:before{display:none}
	#Header_creative #Top_bar .top_bar_right_wrapper{top:0}

	#Header_creative #Top_bar .logo{float:none;text-align:center;margin:15px 0}

	#Header_creative #Top_bar #menu{background-color:transparent}
	#Header_creative #Top_bar .menu_wrapper{float:none;margin:0 0 30px}
	#Header_creative #Top_bar .menu > li{width:100%;float:none;position:relative}
	#Header_creative #Top_bar .menu > li > a{padding:0;text-align:center}
	#Header_creative #Top_bar .menu > li > a:after{display:none}
	#Header_creative #Top_bar .menu > li > a span{border-right:0;border-bottom-width:1px;line-height:38px}
	#Header_creative #Top_bar .menu li ul{left:100%;right:auto;top:0;box-shadow:2px 2px 2px 0 rgba(0,0,0,0.03);-webkit-box-shadow:2px 2px 2px 0 rgba(0,0,0,0.03)}
	#Header_creative #Top_bar .menu > li > ul.mfn-megamenu{margin:0;width:700px!important;}
	#Header_creative #Top_bar .menu > li > ul.mfn-megamenu > li > ul{left:0}
	#Header_creative #Top_bar .menu li ul li a{padding-top:9px;padding-bottom:8px}
	#Header_creative #Top_bar .menu li ul li ul{top:0}
	#Header_creative #Top_bar .menu > li > a span.description{display:block;font-size:13px;line-height:28px!important;clear:both}

	#Header_creative #Top_bar .search_wrapper{left:100%;top:auto;bottom:0}
	#Header_creative #Top_bar a#header_cart{display:inline-block;float:none;top:3px}
	#Header_creative #Top_bar a#search_button{display:inline-block;float:none;top:3px}
	#Header_creative #Top_bar .wpml-languages{display:inline-block;float:none;top:0}
	#Header_creative #Top_bar .wpml-languages.enabled:hover a.active{padding-bottom:11px}
	#Header_creative #Top_bar .action_button{display:inline-block;float:none;top:16px;margin:0}
	#Header_creative #Top_bar .banner_wrapper{display:block;text-align:center}
	#Header_creative #Top_bar .banner_wrapper img{max-width:100%;height:auto;display:inline-block}

	#Header_creative #Action_bar{display:none;position:absolute;bottom:0;top:auto;clear:both;padding:0 20px;box-sizing:border-box}
	#Header_creative #Action_bar .contact_details{text-align:center;margin-bottom:20px}
	#Header_creative #Action_bar .contact_details li{padding:0}
	#Header_creative #Action_bar .social{float:none;text-align:center;padding:5px 0 15px}
	#Header_creative #Action_bar .social li{margin-bottom:2px}
	#Header_creative #Action_bar .social-menu{float:none;text-align:center}
	#Header_creative #Action_bar .social-menu li{border-color:rgba(0,0,0,.1)}

	#Header_creative .social li a{color:rgba(0,0,0,.5)}
	#Header_creative .social li a:hover{color:#000}
	#Header_creative .creative-social{position:absolute;bottom:10px;right:0;width:50px}
	#Header_creative .creative-social li{display:block;float:none;width:100%;text-align:center;margin-bottom:5px}

	.header-creative .fixed-nav.fixed-nav-prev{margin-left:50px}
	.header-creative.header-open .fixed-nav.fixed-nav-prev{margin-left:250px}
	.menuo-last #Header_creative #Top_bar .menu li.last ul{top:auto;bottom:0}

	/* Header Creative | Always Open */

	.header-open #Header_creative{left:0}
	.header-open #Header_creative .creative-wrapper{opacity:1;margin:0!important;}
	.header-open #Header_creative .creative-menu-toggle,.header-open #Header_creative .creative-social{display:none}
	.header-open #Header_creative #Action_bar{display:block}

	/* Header Creative | Right */

	body.header-rtl.header-creative{padding-left:0;padding-right:50px}
	.header-rtl #Header_creative{left:auto;right:-200px}
	.header-rtl #Header_creative .creative-wrapper{margin-left:50px;margin-right:0}
	.header-rtl #Header_creative a.creative-menu-toggle{left:8px;right:auto}
	.header-rtl #Header_creative .creative-social{left:0;right:auto}
	.header-rtl #Footer #back_to_top.sticky{right:125px}
	.header-rtl #popup_contact{right:70px}
	.header-rtl #Header_creative #Top_bar .menu li ul{left:auto;right:100%}
	.header-rtl #Header_creative #Top_bar .search_wrapper{left:auto;right:100%;}
	.header-rtl .fixed-nav.fixed-nav-prev{margin-left:0!important}
	.header-rtl .fixed-nav.fixed-nav-next{margin-right:50px}

	/* Header Creative | Right | Always Open */

	body.header-rtl.header-creative.header-open{padding-left:0;padding-right:250px!important}
	.header-rtl.header-open #Header_creative{left:auto;right:0}
	.header-rtl.header-open #Footer #back_to_top.sticky{right:325px}
	.header-rtl.header-open #popup_contact{right:270px}
	.header-rtl.header-open .fixed-nav.fixed-nav-next{margin-right:250px}

	/* Header Creative | .active */

	#Header_creative.active{left:-1px;}
	.header-rtl #Header_creative.active{left:auto;right:-1px;}
	#Header_creative.active .creative-wrapper{opacity:1;margin:0}

	/* Header Creative | Visual Composer */

	.header-creative .vc_row[data-vc-full-width]{padding-left:50px}
	.header-creative.header-open .vc_row[data-vc-full-width]{padding-left:250px}
	.header-open .vc_parallax .vc_parallax-inner { left:auto; width: calc(100% - 250px); }
	.header-open.header-rtl .vc_parallax .vc_parallax-inner { left:0; right:auto; }

	/* Header Creative | Scroll */

	#Header_creative.scroll{height:100%;overflow-y:auto}
	#Header_creative.scroll:not(.dropdown) .menu li ul{display:none!important}
	#Header_creative.scroll #Action_bar{position:static}

	/* Header Creative | Dropdown */

	#Header_creative.dropdown{outline:none}
	#Header_creative.dropdown #Top_bar .menu_wrapper{float:left}

	/* Header Creative | Dropdown | Main Menu |  1st level */

	#Header_creative.dropdown #Top_bar #menu ul li{position:relative;float:left}
	#Header_creative.dropdown #Top_bar #menu ul li a:after{display:none}
	#Header_creative.dropdown #Top_bar #menu ul li a span{line-height:38px;padding:0}
	#Header_creative.dropdown #Top_bar #menu ul li.submenu .menu-toggle{display:block;position:absolute;right:0;top:0;width:38px;height:38px;line-height:38px;font-size:26px;font-weight:300;text-align:center;cursor:pointer;color:#444;opacity:0.33;}
	#Header_creative.dropdown #Top_bar #menu ul li.submenu .menu-toggle:after{content:"+"}
	#Header_creative.dropdown #Top_bar #menu ul li.hover > .menu-toggle:after{content:"-"}
	#Header_creative.dropdown #Top_bar #menu ul li.hover a{border-bottom:0}
	#Header_creative.dropdown #Top_bar #menu ul.mfn-megamenu li .menu-toggle{display:none}

	/* Header Creative | Dropdown | Main Menu | 2nd level */

	#Header_creative.dropdown #Top_bar #menu ul li ul{position:relative!important;left:0!important;top:0;padding:0;margin-left:0!important;width:auto!important;background-image:none}
	#Header_creative.dropdown #Top_bar #menu ul li ul li{width:100%!important}
	#Header_creative.dropdown #Top_bar #menu ul li ul li a{padding:0 10px;text-align:center}
	#Header_creative.dropdown #Top_bar #menu ul li ul li a .menu-arrow{display:none}
	#Header_creative.dropdown #Top_bar #menu ul li ul li a span{padding:0}
	#Header_creative.dropdown #Top_bar #menu ul li ul li a span:after{display:none!important}
	#Header_creative.dropdown #Top_bar .menu > li > ul.mfn-megamenu a.mfn-megamenu-title{text-transform:uppercase;font-weight:400}
	#Header_creative.dropdown #Top_bar .menu > li > ul.mfn-megamenu > li > ul{display:block!important;position:inherit;left:auto;top:auto}

	/* Header Creative | Dropdown | Main Menu | 3rd level */

	#Header_creative.dropdown #Top_bar #menu ul li ul li ul{border-left:0!important;padding:0;top:0}

	/* animations */

	#Header_creative{transition: left .5s ease-in-out, right .5s ease-in-out;}
	#Header_creative .creative-wrapper{transition: opacity .5s ease-in-out, margin 0s ease-in-out .5s;}
	#Header_creative.active .creative-wrapper{transition: opacity .5s ease-in-out, margin 0s ease-in-out;}
}

/**
 * Sticky Header *****
 */

@media only screen and (min-width: <?php echo esc_attr($stickyMenuInitW); ?>px)
{
	/* Sticky | .is-sticky */

	#Top_bar.is-sticky{position:fixed!important;width:100%;left:0;top:-60px;height:60px;z-index:701;background:#fff;opacity:.97;-webkit-box-shadow:0 2px 5px 0 rgba(0,0,0,0.1);-moz-box-shadow:0 2px 5px 0 rgba(0,0,0,0.1);box-shadow:0 2px 5px 0 rgba(0,0,0,0.1)}

	.layout-boxed.header-boxed #Top_bar.is-sticky{max-width:<?php echo esc_attr($mobileMenuInitW); ?>px;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}

	#Top_bar.is-sticky .top_bar_left,#Top_bar.is-sticky .top_bar_right,#Top_bar.is-sticky .top_bar_right:before{background:none}
	#Top_bar.is-sticky .top_bar_right{top:-4px;height:auto;}
	#Top_bar.is-sticky .top_bar_right_wrapper{top:15px}
	.header-plain #Top_bar.is-sticky .top_bar_right_wrapper{top:0}
	#Top_bar.is-sticky .logo{width:auto;margin:0 30px 0 20px;padding:0}
	#Top_bar.is-sticky #logo,
	#Top_bar.is-sticky .custom-logo-link{padding:5px 0!important;height:50px!important;line-height:50px!important}
	.logo-no-sticky-padding #Top_bar.is-sticky #logo{height:60px!important;line-height:60px!important}
	#Top_bar.is-sticky #logo img.logo-main{display:none}
	#Top_bar.is-sticky #logo img.logo-sticky{display:inline;max-height:35px;}
	#Top_bar.is-sticky .menu_wrapper{clear:none}
	#Top_bar.is-sticky .menu_wrapper .menu > li > a{padding:15px 0}
	#Top_bar.is-sticky .menu > li > a,#Top_bar.is-sticky .menu > li > a span{line-height:30px}
	#Top_bar.is-sticky .menu > li > a:after{top:auto;bottom:-4px}
	#Top_bar.is-sticky .menu > li > a span.description{display:none}
	#Top_bar.is-sticky .secondary_menu_wrapper,#Top_bar.is-sticky .banner_wrapper{display:none}
	.header-overlay #Top_bar.is-sticky{display:none}

		/* Sticky | Dark */

		.sticky-dark #Top_bar.is-sticky,.sticky-dark #Top_bar.is-sticky #menu{background:rgba(0,0,0,.8)}
		.sticky-dark #Top_bar.is-sticky .menu > li:not(.current-menu-item) > a{color:#fff}
		.sticky-dark #Top_bar.is-sticky .top_bar_right a:not(.action_button){color:rgba(255,255,255,.8)}
		.sticky-dark #Top_bar.is-sticky .wpml-languages a.active,.sticky-dark #Top_bar.is-sticky .wpml-languages ul.wpml-lang-dropdown{background:rgba(0,0,0,0.1);border-color:rgba(0,0,0,0.1)}

		/* Sticky | White */

		.sticky-white #Top_bar.is-sticky,.sticky-white #Top_bar.is-sticky #menu{background:rgba(255,255,255,.8)}
		.sticky-white #Top_bar.is-sticky .menu > li:not(.current-menu-item) > a{color:#222}
		.sticky-white #Top_bar.is-sticky .top_bar_right a:not(.action_button){color:rgba(0,0,0,.8)}
		.sticky-white #Top_bar.is-sticky .wpml-languages a.active,.sticky-white #Top_bar.is-sticky .wpml-languages ul.wpml-lang-dropdown{background:rgba(255,255,255,0.1);border-color:rgba(0,0,0,0.1)}
}

<?php if( $mobileMenuInitW == $stickyMenuInitW ): ?>
/* Tablet | Sticky Header OFF */

@media only screen and (min-width: 768px) and (max-width: <?php echo esc_attr($mobileMenuInitW); ?>px){
	.header_placeholder{height:0!important}
}
<?php endif; ?>


/**
 * Menu | Mobile *****
 */

@media only screen and (max-width: <?php echo esc_attr($mobileMenuInitW - 1); ?>px)
{
	/* Header */

	#Top_bar #menu{display:none;height:auto;width:300px;bottom:auto;top:100%;right:1px;position:absolute;margin:0}
	#Top_bar a.responsive-menu-toggle{display:block}

	/* Main Menu | 1st level */

	#Top_bar #menu > ul{width:100%;float:left}
	#Top_bar #menu ul li{width:100%;padding-bottom:0;border-right:0;position:relative}
	#Top_bar #menu ul li a{padding:0 25px;margin:0;display:block;height:auto;line-height:normal;border:none}
	#Top_bar #menu ul li a:after{display:none}
	#Top_bar #menu ul li a span{border:none;line-height:44px;display:inline;padding:0}
	#Top_bar #menu ul li a span.description{margin:0 0 0 5px}
	#Top_bar #menu ul li.submenu .menu-toggle{display:block;position:absolute;right:15px;top:0;width:44px;height:44px;line-height:44px;font-size:30px;font-weight:300;text-align:center;cursor:pointer;color:#444;opacity:0.33;}
	#Top_bar #menu ul li.submenu .menu-toggle:after{content:"+"}
	#Top_bar #menu ul li.hover > .menu-toggle:after{content:"-"}
	#Top_bar #menu ul li.hover a{border-bottom:0}
	#Top_bar #menu ul li a span:after{display:none!important}
	#Top_bar #menu ul.mfn-megamenu li .menu-toggle{display:none}

	/* Main Menu | 2nd level */

	#Top_bar #menu ul li ul{position:relative!important;left:0!important;top:0;padding:0;margin-left:0!important;width:auto!important;background-image:none!important;box-shadow:0 0 0 0 transparent!important;-webkit-box-shadow:0 0 0 0 transparent!important}
	#Top_bar #menu ul li ul li{width:100%!important}
	#Top_bar #menu ul li ul li a{padding:0 20px 0 35px}
	#Top_bar #menu ul li ul li a .menu-arrow{display:none}
	#Top_bar #menu ul li ul li a span{padding:0}
	#Top_bar #menu ul li ul li a span:after{display:none!important}
	#Top_bar .menu > li > ul.mfn-megamenu a.mfn-megamenu-title{text-transform:uppercase;font-weight:400}
	#Top_bar .menu > li > ul.mfn-megamenu > li > ul{display:block!important;position:inherit;left:auto;top:auto}

	/* Main Menu | 3rd level */

	#Top_bar #menu ul li ul li ul{border-left:0!important;padding:0;top:0}
	#Top_bar #menu ul li ul li ul li a{padding:0 20px 0 45px}

	/* Main Menu | RTL */

	.rtl #Top_bar #menu{left:1px;right:auto}
	.rtl #Top_bar a.responsive-menu-toggle{left:20px;right:auto}
	.rtl #Top_bar #menu ul li.submenu .menu-toggle{left:15px;right:auto;border-left:none;border-right:1px solid #eee}
	.rtl #Top_bar #menu ul li ul{left:auto!important;right:0!important}
	.rtl #Top_bar #menu ul li ul li a{padding:0 30px 0 20px}
	.rtl #Top_bar #menu ul li ul li ul li a{padding:0 40px 0 20px}

	/* Header | Stack */

	.header-stack .menu_wrapper a.responsive-menu-toggle{position:static!important;margin:11px 0!important}
	.header-stack .menu_wrapper #menu{left:0;right:auto}
	.rtl.header-stack #Top_bar #menu{left:auto;right:0}

	/* Header Creative */

	.admin-bar #Header_creative{top:32px}
	.header-creative.layout-boxed{padding-top:85px}
	.header-creative.layout-full-width #Wrapper{padding-top:60px}

	#Header_creative{position:fixed;width:100%;left:0!important;top:0;z-index:1001}
	#Header_creative .creative-wrapper{display:block!important;opacity:1!important}
	#Header_creative .creative-menu-toggle,#Header_creative .creative-social{display:none!important;opacity:1!important}
	#Header_creative #Top_bar{position:static;width:100%}
	#Header_creative #Top_bar #logo,
	#Header_creative #Top_bar .custom-logo-link{height:50px;line-height:50px;padding:5px 0}
	#Header_creative #Top_bar #logo img.logo-sticky{max-height:40px!important}

	#Header_creative #logo img.logo-main{display:none}
	#Header_creative #logo img.logo-sticky{display:inline-block}

	.logo-no-sticky-padding #Header_creative #Top_bar #logo{height:60px;line-height:60px;padding:0}
	.logo-no-sticky-padding #Header_creative #Top_bar #logo img.logo-sticky{max-height:60px!important}

	#Header_creative #Action_bar{display:none}
	#Header_creative #Top_bar .top_bar_right{height:60px;top:0}
	#Header_creative #Top_bar .top_bar_right:before{display:none}
	#Header_creative #Top_bar .top_bar_right_wrapper{top:0;padding-top:9px}

	/* Header Creative | Scroll */

	#Header_creative.scroll{overflow:visible!important}
}
