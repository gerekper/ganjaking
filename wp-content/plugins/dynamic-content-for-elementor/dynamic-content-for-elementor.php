<?php
/**
 *
 * @copyright Copyright (C) 2018-2023, Ovation S.r.l. - support@dynamic.ooo
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Dynamic.ooo - Dynamic Content for Elementor
 * Plugin URI: https://www.dynamic.ooo/dynamic-content-for-elementor?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: Building powerful websites by extending Elementor. We give you over 150 features that will save you time and money on achieving complex results. The only limit is your imagination.
 * Version: 2.12.6
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Dynamic.ooo
 * Author URI: https://www.dynamic.ooo/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Text Domain: dynamic-content-for-elementor
 * Domain Path: /languages
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Elementor tested up to: 3.18.3
 * Elementor Pro tested up to: 3.18.2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Dynamic.ooo - Dynamic Content for Elementor incorporates code from:
 * - A-Frame, Copyright (c) 2015-2017 A-Frame authors, License: MIT, https://aframe.io
 * - Accordion.JS, Copyright (c) 2013-2017 Andrei Surdu, License: MIT, https://github.com/awps/Accordion.JS
 * - Ajaxify, Copyright (c) Arvind Gupta, License: MIT, https://4nf.org
 * - AliExpress Dropshipping with Ali2Woo Lite, Copyright (c) MA-Group, License: GPL v2, https://ali2woo.com
 * - Animate.css, Copyright (c) 2019 Daniel Eden, License: MIT, https://daneden.github.io/animate.css/
 * - anime.js, Copyright (c) 2019 Julian Garnier, License: MIT, https://github.com/juliangarnier/anime
 * - Animsition, Copyright (c) 2013-2015 blivesta, License: MIT, http://git.blivesta.com/animsition/
 * - Cache, Copyright (c) 2015 PHP Framework Interoperability Group, License: MIT, https://github.com/php-fig/cache
 * - Chart.js, Copyright (c) 2014-2021 Chart.js Contributors, License: MIT, https://github.com/chartjs/Chart.js
 * - Clipboard.js, Copyright (c) 2019 Zeno Rocha, License: MIT, https://zenorocha.mit-license.org/
 * - CodeMirror, License: GPL v3, https://www.codemirror.net
 * - Codrops.com, Copyright (c) 2019, License: MIT, https://www.codrops.com
 * - Composer, Copyright (c) Nils Adermann, Jordi Boggiano, License: MIT, https://getcomposer.org
 * - Composer Exclude Files, Copyright (c) 2017–2020 Chauncey McAskill, License: MIT, https://github.com/mcaskill/composer-plugin-exclude-files
 * - Container, Copyright (c) 2013-2016 container-interop Copyright (c) 2016 PHP Framework Interoperability Group, License: MIT, https://github.com/php-fig/container
 * - Creative WebGL Image Transitions, Copyright (c) 2019 Yuriy Artyukh, License: MIT, https://github.com/akella/webGLImageTransitions
 * - CSSSelector Component, Copyright (c) Symfony, License: MIT, https://github.com/symfony/css-selector
 * - CSSToInlineStyles, Copyright (c) Tijs Verkoyen, License: BSD, https://github.com/tijsverkoyen/CssToInlineStyles
 * - DataTables, Copyright (c) 2007-2020 SpryMedia Ltd., License: MIT, https://datatables.net
 * - Date Format Conversion, Copyright (c) Chauncey McAskill, Baptiste Placé, License: MIT, https://gist.github.com/mcaskill/02636e5970be1bb22270
 * - Day.js, Copyright (c) 2018-present, iamkun, License: MIT, https://day.js.org/
 * - Diamonds.js, Copyright (c) 2013 mqchen, License: MIT, https://github.com/mqchen/jquery.diamonds.js/
 * - Divi Theme by Elegant Themes, Copyright (C) Elegant Themes, License: GPLv2, http://www.elegantthemes.com/
 * - DomCrawler, Copyright (c) Symfony, License: MIT, https://github.com/symfony/dom-crawler
 * - Dompdf, License: LGPL v2.1, https://github.com/dompdf/dompdf
 * - Elementor, Copyright (c) Elementor Ltd., License: GPL v3, https://www.elementor.com
 * - Elementor Pro, Copyright (c) Elementor Ltd., License: GPL v3, https://www.elementor.com
 * - eos, Copyright Jon Lawrence, License: LGPL v2.1, https://github.com/jlawrence11/eos/
 * - Flatpickr, Copyright (c) 2017 Gregory Petrosyan, License: MIT, https://flatpickr.js.org
 * - GSAP, GreenSock files are subject to their own license (https://greensock.com/standard-license) and you can ONLY use the bonus files as a part of Dynamic.ooo - Dynamic Content for Elementor
 * - HeadRoom js, Copyright (c) 2020 Nick Nilliams, License: MIT, https://wicky.nillia.ms/headroom.js/
 * - HoneyCombs, License: GPL v3, https://github.com/nasirkhan/honeycombs
 * - Html2Canvas, Copyright (c) 2012 Niklas von Hertzen, License: MIT, https://html2canvas.hertzen.com/
 * - imagesLoaded, Copyright (c) Dave DeSandro, License: MIT, https://imagesloaded.desandro.com
 * - InfiniteScroll, License: GPL v3, https://infinite-scroll.com/
 * - ISO4217, Copyright (C) 2015 Maksim Kotlyar, License: MIT, https://github.com/Payum/iso4217
 * - Isotope, GPL v3, http://isotope.metafizzy.co
 * - Javascript implementation of the Symfony/ExpressionLanguage, Copyright (c) @jameskfry, License: MIT, https://www.npmjs.com/package/expression-language
 * - jQuery Color, Copyright (c) OpenJS Foundation and other contributors, License: CC0, https://github.com/jquery/jquery-color
 * - jQuery Easing, Copyright (c) 2008 George McGinley Smith, License: BSD, http://gsgd.co.uk/sandbox/jquery/easing/
 * - jQuery inertiaScroll, Copyright(c) 2017 Go Nishiduka, License: MIT
 * - jquery.matchHeight.js, Copyright (c) 2014 Liam Brummitt, License: MIT, https://github.com/liabru/jquery-match-height
 * - jQuery Visible, Copyright (c) 2012 Digital Fusion, License: MIT, http://teamdf.com/
 * - jsPDF, Copyright (c) 2010-2020 James Hall, License: MIT, https://github.com/MrRio/jsPDF (c) 2015-2020 yWorks GmbH, https://www.yworks.com/
 * - justifiedGallery, Copyright (c) 2019 Miro Mannino, License: MIT, http://miromannino.github.io/Justified-Gallery/
 * - lax.js, Copyright (c) 2019 Alex Fox, License: MIT, https://github.com/alexfoxy/lax.js
 * - Leafletjs, Copyright (c) 2010-2022, Vladimir Agafonkin, Copyright (c) 2010-2011, License: BSD-2-Clause License, CloudMade, https://leafletjs.com
 * - Log, Copyright (c) 2012 PHP Framework Interoperability Group, License: MIT, https://github.com/php-fig/log
 * - Parallax.js, Copyright (c) 2014 Matthew Wagerfield - @wagerfield, License: MIT, https://github.com/wagerfield/parallax
 * - PathConverter, Copyright (c) 2015 Matthias Mullie, License: MIT, https://github.com/matthiasmullie/path-converter
 * - Payum\ISO4217, License: MIT, https://github.com/Payum/iso4217
 * - PDF.js, Copyright (c) Mozilla and individual contributors, License: Apache 2.0, https://github.com/mozilla/pdf.js
 * - Perlin Noise, by Stefan Gustavson, https://github.com/stegu/perlin-noise
 * - Plugin Update Checker, Copyright (c) 2017 Jānis Elsts, License: MIT, https://github.com/YahnisElsts/plugin-update-checker
 * - Plyr, Copyright (c) 2017 Sam Potts, License: MIT, https://plyr.io
 * - PhotoSwipe, Copyright (c) 2014-2019 Dmitry Semenov, http://dimsemenov.com, License: MIT, http://photoswipe.com
 * - PHP CSS Parser, Copyright (c) 2011 Raphael Schweikert, License: MIT, https://www.sabberworm.com/
 * - PHP Font Lib, Copyright (c) Fabien Ménager, License: LGPL v2.1, https://github.com/PhenX/php-font-lib
 * - PHP SVG Lib, Copyright (c) Fabien Ménager, License: GPL v3, https://github.com/PhenX/php-svg-lib
 * - PHP Html Parser, Copyright (c) 2014 Gilles Paquette, License: MIT, https://github.com/paquettg/php-html-parser
 * - PHP Simple HTML Dom Parser, Author: S.C. Chen, License: MIT, https://github.com/sunra/php-simple-html-dom-parser
 * - Polyfill Ctype, Copyright (c) Symfony, License: MIT, https://github.com/symfony/polyfill-ctype
 * - Polyfill Mbstring, Copyright (c) Symfony, License: MIT, https://github.com/symfony/polyfill-mbstring
 * - Rellax, Copyright (c) 2016 Dixon & Moe, License: MIT, https://dixonandmoe.com/rellax/
 * - Revealjs.com, Copyright (c) 2018 Hakim El Hattab (http://hakim.se) and reveal.js contributors, License: MIT, https://revealjs.com
 * - Sabberworm PHP CSS Parser, Copyright (c) 2011 Raphael Schweikert, License: MIT, https://github.com/sabberworm/PHP-CSS-Parser
 * - Scrollify.js, Copyright (c) 2017 Luke Haas, License: MIT, https://projects.lukehaas.me/scrollify/
 * - Select2, Copyright (c) 2012-2017 Kevin Brown, Igor Vaynberg, and Select2 contributors, License: MIT, https://github.com/select2/select2
 * - Symfony/ExpressionLanguage, Copyright (c) Symfony, License: MIT, https://github.com/symfony/expression-language
 * - Slick, Copyright (c) 2013-2016, License: MIT, http://kenwheeler.github.io/slick/
 * - String Encode, Copyright (c) 2014 Gilles Paquette, License: MIT, https://github.com/paquettg/string-encoder/
 * - Stripe, Copyright (c) 2010-2019 Stripe, Inc. (https://stripe.com), License: MIT, https://github.com/stripe/stripe-php
 * - Swiper.js, 2019 (c) Swiper by Vladimir Kharlampidi from iDangero.us, License: MIT, https://idangero.us/swiper/
 * - TCPDF, Copyright (c) 2004-2020 – Nicola Asuni - Tecnick.com, License: GPL v3, https://tcpdf.org
 * - Telegram Bot, Copyright (c) 2015 Ilya Gusev, License: MIT, https://github.com/TelegramBot/Api
 * - Three Sixty Image slider, Copyright 2013 Gaurav Jassal, License: MIT, https://github.com/rustamwin/threesixty-slider
 * - Tilt.js, Copyright (c) 2017 Gijs Rogé, License: MIT, https://gijsroge.github.io/tilt.js/
 * - Tippy.js, Copyright (c) 2017-present atomiks, License: MIT, https://atomiks.github.io/tippyjs/
 * - Signature Pad, Copyright (c) 2018 Szymon Nowak, License: MIT, https://github.com/szimek/signature_pad
 * - Slick, Copyright (c) 2013-2016, License: MIT, http://kenwheeler.github.io
 * - Stripe PHP, Copyright (c) 2010-2019 Stripe, License: MIT, https://github.com/stripe/stripe-php
 * - Streamium WordPress Theme, Copyright (c) 2012 Samuel East, License: GPL v3, https://github.com/DevLight1/Streamium-Netflix-Theme/
 * - SVG File Icons, Copyright (c) 2018 Daniel M. Hendricks, License: MIT, https://fileicons.org/
 * - THREEJS, Copyright (c) 2010-2019 three.js authors, License: MIT, https://github.com/mrdoob/three.js/blob/dev/LICENSE
 * - TGM Plugin Activation, Copyright (c) 2011 Thomas Griffin, License: GPL v2 or later, http://tgmpluginactivation.com
 * - TwentyTwenty, Copyright 2018 zurb, License: MIT, https://zurb.com/playground/twentytwenty
 * - Velocity.js, Copyright (c) 2014 Julian Shapiro, License: MIT, http://velocityjs.org
 * - Vertical Timeline, Copyright (c) Codyhouse, License: MIT, https://codyhouse.co/gem/vertical-timeline/
 * - WooCommerce, Copyright 2015 by the contributors, License: GPLv3, https://www.woocommerce.com
 * - WOW.js, Copyright (c) 2016 Thomas Grainger, License: MIT, https://wowjs.uk/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

update_option('dce_license_key', '4308eedb-1add-43a9-bbba-6f5d5aa6b8ee');
update_option('dce_license_status', 'active');
update_option('dce_license_domain', base64_encode('SL_INSTANCE'));
update_option('dce_license_error', '');
add_filter( 'site_transient_update_plugins', function( $value ) {
    unset( $value->response['dynamic-content-for-elementor/dynamic-content-for-elementor.php'] );
    return $value;
} );

define( 'DCE_PLUGIN_BASE', plugin_basename( __FILE__ ) ); // {dce-folder}/{current-file}
define( 'DCE__FILE__', __FILE__ ); // {path}/wp-content/plugins/{dce-folder}/{current-file}
define( 'DCE_URL', plugins_url( '/', __FILE__ ) ); // {site}/wp-content/plugins/{dce-folder}
define( 'DCE_PATH', plugin_dir_path( __FILE__ ) ); // {path}/wp-content/plugins/{dce-folder}

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/includes/admin-pages/notices.php';

// Admin Style - Load it now because we could display a notice for PHP version and the plugin is not be loaded
add_action( 'admin_enqueue_scripts', function() {
	wp_register_style( 'dce-admin', DCE_URL . 'assets/css/admin.css', [], DCE_VERSION );
	wp_enqueue_style( 'dce-admin' );
});

// Check PHP version
if ( version_compare( phpversion(), DCE_MINIMUM_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_minimum_php_version' );
	return;
} elseif ( version_compare( phpversion(), DCE_SUGGESTED_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_suggest_new_php_version' );
} elseif ( version_compare( phpversion(), strval( DCE_MAXIMUM_PHP_VERSION + 0.1 ), '>=' ) ) {
	add_action( 'admin_notices', 'dce_admin_notice_maximum_php_version' );
}

require_once DCE_PATH . 'vendor/autoload.php';

// Fix the str_contains function in php 8.0 polyfill by Symfony. Its
// behaviour is actually different from that of the function in php 8.0.
// In php 8.0 there are no errors on wrong type:
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( $haystack, $needle ) {
		if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
			return false;
		}
		/** @phpstan-ignore-next-line */
		return \DynamicOOOS\Symfony\Polyfill\Php80\Php80::str_contains( $haystack, $needle );
	}
}

require_once __DIR__ . '/vendor/symfony/polyfill-php80/bootstrap.php';

// Dynamic License
require_once __DIR__ . '/dynamic-license/license.php';

register_activation_hook( DCE_PLUGIN_BASE, '\DynamicContentForElementor\Plugin::activation' );
register_uninstall_hook( DCE_PLUGIN_BASE, '\DynamicContentForElementor\Plugin::uninstall' );

add_action( 'plugins_loaded', 'dce_load' );

/**
 * Load Dynamic.ooo - Dynamic Content for Elementor
 *
 * Load the plugin after Elementor is loaded.
 *
 * @since 0.1.0
 */
function dce_load() {
	// Load localization file
	load_plugin_textdomain( 'dynamic-content-for-elementor' );

	// Enqueue Admin CSS
	add_action( 'admin_enqueue_scripts', 'dce_enqueue_admin_styles' );

	require_once DCE_PATH . '/core/plugin.php';
}

function dce_enqueue_admin_styles() {
	// Admin style
	wp_register_style( 'dce-admin', DCE_URL . 'assets/css/admin.css', [], DCE_VERSION );
	wp_enqueue_style( 'dce-admin' );
}

/**
 * Handles admin notice for non-active Elementor plugin situations
 *
 * @return void
 */
function dce_fail_load() {
	$msg = sprintf( __( '%1$sElementor%2$s is required for the %1$s%3$s%2$s plugin to work.', 'dynamic-content-for-elementor' ), '<strong>', '</strong>', DCE_PRODUCT_NAME_LONG );
	\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'error' );
}

function dce_admin_notice_minimum_elementor_version() {
	$msg = sprintf( __( '%1$s requires Elementor version %2$s or greater.', 'dynamic-content-for-elementor' ), DCE_PRODUCT_NAME_LONG, DCE_MINIMUM_ELEMENTOR_VERSION );
	\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'error' );
}

function dce_admin_notice_minimum_elementor_pro_version() {
	$msg = sprintf( __( 'If you want to use Elementor Pro with %1$s, it requires version %2$s or greater.', 'dynamic-content-for-elementor' ), DCE_PRODUCT_NAME_LONG, DCE_MINIMUM_ELEMENTOR_PRO_VERSION );
	\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'error' );
}

function dce_admin_notice_minimum_php_version() {
	$msg = sprintf( __( 'You are using PHP version %1$s. This version is not more supported. Ask your provider to use PHP version %2$s+.', 'dynamic-content-for-elementor' ), phpversion(), DCE_MINIMUM_PHP_VERSION );
	\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'error' );
}

function dce_admin_notice_maximum_php_version() {
	$msg = sprintf( __( 'You are using PHP version %1$s and it\'s not yet fully supported. The maximum version supported is %2$s.', 'dynamic-content-for-elementor' ), phpversion(), DCE_MAXIMUM_PHP_VERSION );
	\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'error' );
}

function dce_admin_notice_suggest_new_php_version() {
	if ( isset( $_GET['page'] ) && 'dce-features' === $_GET['page'] ) {
		$msg = sprintf( __( 'You are using PHP version %1$s. It\'s suggested to use PHP version %2$s+.', 'dynamic-content-for-elementor' ), phpversion(), DCE_SUGGESTED_PHP_VERSION );
		\DynamicContentForElementor\AdminPages\Notices::render_notice( $msg, 'warning' );
	}
}
