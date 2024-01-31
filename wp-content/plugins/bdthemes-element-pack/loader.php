<?php

namespace ElementPack;

use Elementor\Core\Kits\Documents\Kit;
use Elementor\Plugin;
use ElementPack\Includes\ElementPack_FB_Access_Token_Generator_Control;
use ElementPack\Includes\ElementPack_JSON_File_Upload_Control;
use ElementPack\Includes\Element_Pack_WPML;
use ElementPack\Includes\Pro_Widget_Map;
use ElementPack\Includes\SVG_Support;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class for element pack
 */
class Element_Pack_Loader {

	/**
	 * @var Element_Pack_Loader
	 */
	private static $_instance;
	public $_modules_manager;

	public $elements_data = [ 
		'sections' => [],
		'columns'  => [],
		'widgets'  => [],
	];

	private function get_upload_dir() {
		return trailingslashit( wp_upload_dir()['basedir'] ) . 'element-pack/minified/';
	}

	private function get_upload_url() {
		return trailingslashit( wp_upload_dir()['baseurl'] ) . 'element-pack/minified/';
	}

	/**
	 * @return string
	 * @deprecated
	 *
	 */
	public function get_version() {
		return BDTEP_VER;
	}

	/**
	 * return active theme
	 */
	public function get_theme() {
		return wp_get_theme();
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bdthemes-element-pack' ), '1.6.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bdthemes-element-pack' ), '1.6.0' );
	}

	/**
	 * @return Plugin
	 */

	public static function elementor() {
		return Plugin::$instance;
	}

	function pro_return() {
		return true;
	}

	/**
	 * @return Element_Pack_Loader
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * we loaded module manager + admin php from here
	 * @return [type] [description]
	 */
	private function _includes() {

		$live_copy            = element_pack_option( 'live-copy', 'element_pack_other_settings', 'off' );
		$essential_shortcodes = element_pack_option( 'essential-shortcodes', 'element_pack_other_settings', 'off' );
		$template_library     = element_pack_option( 'template-library', 'element_pack_other_settings', 'off' );
		$context_menu         = element_pack_option( 'context-menu', 'element_pack_other_settings', 'off' );
		$duplicator           = element_pack_option( 'duplicator', 'element_pack_other_settings', 'off' );
		$mega_menu            = element_pack_option( 'mega-menu', 'element_pack_other_settings', 'off' );
		$smooth_scroller      = element_pack_option( 'smooth-scroller', 'element_pack_other_settings', 'off' );

		// Admin settings controller
		require_once BDTEP_ADMIN_PATH . 'module-settings.php';
		//Assets Manager
		require_once 'admin/optimizer/asset-minifier-manager.php';

		// Dynamic Select control
		require_once BDTEP_INC_PATH . 'controls/select-input/dynamic-select-input-module.php';
		require_once BDTEP_INC_PATH . 'controls/select-input/dynamic-select.php';

		// Global Controls
		require_once BDTEP_PATH . 'traits/global-widget-controls.php';
		require_once BDTEP_PATH . 'traits/global-swiper-controls.php';
		require_once BDTEP_PATH . 'traits/global-mask-controls.php';

		// Load Context Menu
		if ( 'on' === $context_menu ) {
			require_once 'includes/context-menu/settings.php';
			require_once 'includes/context-menu/controls.php';
		}
		if ( 'on' === $mega_menu ) {
			require_once BDTEP_INC_PATH . 'mega-menu/classes/class-mega-menu-init.php';
		}

		if ( 'on' === $smooth_scroller ) {
			require_once BDTEP_INC_PATH . 'smooth-scroller/settings.php';
			require_once BDTEP_INC_PATH . 'smooth-scroller/controls.php';
		}

		// json upload support for wordpress
		require_once BDTEP_INC_PATH . 'class-json-file-upload-control.php';
		// svg support for full wordpress site
		require_once BDTEP_INC_PATH . 'class-svg-support.php';
		// All modules loading from here
		require_once BDTEP_INC_PATH . 'modules-manager.php';
		// wpml compatibility class for wpml support
		require_once BDTEP_INC_PATH . 'class-elements-wpml-compatibility.php';
		// For changelog file parse
		require_once BDTEP_INC_PATH . 'class-parsedown.php';

		if ( ( $live_copy == 'on' ) && ( ! is_plugin_active( 'live-copy-paste/live-copy-paste.php' ) ) ) {
			require_once BDTEP_INC_PATH . 'live-copy/class-live-copy.php';
		}

		// register the elementor template loading widget in widgets
		require_once BDTEP_INC_PATH . 'widgets/elementor-template.php';

		// Facebook access token generator control for editor
		require_once BDTEP_INC_PATH . 'class-fb-access-token-generator-control.php';

		require_once BDTEP_INC_PATH . 'class-google-recaptcha.php';

		// Shortcode loader for works some essential shortcode that need for any purpose
		if ( $essential_shortcodes == 'on' ) {
			require_once BDTEP_INC_PATH . 'shortcodes/shortcode-loader.php';
		}

		if ( $duplicator == 'on' ) {
			require_once BDTEP_INC_PATH . 'class-duplicator.php';
		}

		// Rooten theme header footer compatibility
		if ( 'Rooten' === $this->get_theme()->name or 'Rooten' === $this->get_theme()->parent_theme ) {
			if ( ! class_exists( 'RootenCustomTemplate' ) ) {
				require_once BDTEP_INC_PATH . 'class-rooten-theme-compatibility.php';
			}
		}

		// editor template library
		if ( ! defined( 'BDTEP_CH' ) and $template_library == 'on' ) {
			require_once BDTEP_INC_PATH . 'template-library/editor/init.php';
			require_once BDTEP_INC_PATH . 'template-library/editor/init.php';
		}

		if ( is_admin() ) {

			if ( ! defined( 'BDTEP_CH' ) ) {
				require_once BDTEP_ADMIN_PATH . 'admin.php';

				if ( ! defined( 'BDTEP_CH' ) and $template_library == 'on' ) {
					require_once BDTEP_INC_PATH . 'template-library/template-library-base.php';
					require_once BDTEP_INC_PATH . 'template-library/editor/manager/api.php';
				}

				// Load admin class for admin related content process
				new Admin();
			}
		}
	}

	/**
	 * Autoloader function for all classes files
	 *
	 * @param  [type] class [description]
	 *
	 * @return [type]        [description]
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		if ( ! class_exists( $class_to_load ) ) {
			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z0-9])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_to_load
				)
			);

			$filename = BDTEP_PATH . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include $filename;
			}
		}
	}

	/**
	 * Register all script that need for any specific widget on call basis.
	 * @return [type] [description]
	 */
	public function register_site_scripts() {

		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$api_settings = get_option( 'element_pack_api_settings' );

		if ( element_pack_is_widget_enabled( 'social-share' ) ) {
			wp_register_script( 'goodshare', BDTEP_ASSETS_URL . 'vendor/js/goodshare.min.js', [ 'jquery' ], '4.1.2', true );
		}
		if ( element_pack_is_widget_enabled( 'progress-pie' ) ) {
			wp_register_script( 'aspieprogress', BDTEP_ASSETS_URL . 'vendor/js/jquery-asPieProgress.min.js', [ 'jquery' ], '0.4.7', true );
		}

		if ( element_pack_is_widget_enabled( 'animated-heading' ) ) {
			wp_register_script( 'morphext', BDTEP_ASSETS_URL . 'vendor/js/morphext.min.js', [ 'jquery' ], '2.4.7', true );
			wp_register_script( 'typed', BDTEP_ASSETS_URL . 'vendor/js/typed.min.js', [ 'jquery' ], null, true );
		}

		if ( element_pack_is_widget_enabled( 'qrcode' ) ) {
			wp_register_script( 'qrcode', BDTEP_ASSETS_URL . 'vendor/js/jquery-qrcode.min.js', [ 'jquery' ], '0.14.0', true );
		}
		if ( element_pack_is_widget_enabled( 'barcode' ) ) {
			wp_register_script( 'jsBarcode', BDTEP_ASSETS_URL . 'vendor/js/jsBarcode.min.js', [], 'v3.11.5', true );
		}
		if ( element_pack_is_widget_enabled( 'video-player' ) or element_pack_is_widget_enabled( 'audio-player' ) ) {
			wp_register_script( 'jplayer', BDTEP_ASSETS_URL . 'vendor/js/jquery.jplayer.min.js', [ 'jquery' ], '2.9.2', true );
		}
		if ( element_pack_is_widget_enabled( 'circle-menu' ) ) {
			wp_register_script( 'circle-menu', BDTEP_ASSETS_URL . 'vendor/js/jQuery.circleMenu.min.js', [ 'jquery' ], '0.1.1', true );
		}
		if ( element_pack_is_widget_enabled( 'cookie-consent' ) ) {
			wp_register_script( 'cookieconsent', BDTEP_ASSETS_URL . 'vendor/js/cookieconsent.min.js', [ 'jquery' ], '3.1.0', true );
		}
		if ( element_pack_is_widget_enabled( 'post-grid-tab' ) or element_pack_is_widget_enabled( 'static-grid-tab' ) ) {
			wp_register_script( 'gridtab', BDTEP_ASSETS_URL . 'vendor/js/gridtab.min.js', [ 'jquery' ], '2.1.1', true );
		}
		if ( element_pack_is_widget_enabled( 'dark-mode' ) ) {
			wp_register_script( 'darkmode', BDTEP_ASSETS_URL . 'vendor/js/darkmode.min.js', [ 'jquery' ], '1.1.1', true );
		}
		if ( element_pack_is_widget_enabled( 'user-login' ) or element_pack_is_widget_enabled( 'user-register' ) or element_pack_is_widget_enabled( 'contact-form' ) ) {
			wp_register_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'chart' ) ) {
			wp_register_script( 'chart', BDTEP_ASSETS_URL . 'vendor/js/chart.min.js', [ 'jquery' ], '3.9.1', true );
		}
		if ( element_pack_is_widget_enabled( 'advanced-gmap' ) ) {
			if ( ! empty( $api_settings['google_map_key'] ) ) {
				wp_register_script( 'gmap-api', 'https://maps.googleapis.com/maps/api/js?key=' . $api_settings['google_map_key'], [ 'jquery' ], null, true );
			}
			wp_register_script( 'gmap', BDTEP_ASSETS_URL . 'vendor/js/gmap.min.js', [ 'jquery', 'gmap-api' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'open-street-map' ) ) {
			wp_register_script( 'leaflet', BDTEP_ASSETS_URL . 'vendor/js/leaflet.min.js', [ 'jquery' ], '', true );
		}
		if ( element_pack_is_widget_enabled( 'panel-slider' ) ) {
			wp_register_script( 'bdt-parallax', BDTEP_ASSETS_URL . 'vendor/js/parallax.min.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'table-of-content' ) ) {
			wp_register_script( 'tocify', BDTEP_ASSETS_URL . 'vendor/js/tocify.min.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'image-magnifier' ) ) {
			wp_register_script( 'imagezoom', BDTEP_ASSETS_URL . 'vendor/js/jquery.imagezoom.min.js', [ 'jquery' ], null, true );
		}

		/**
		 * No need condition for datatable
		 */
		wp_register_script( 'datatables', BDTEP_ASSETS_URL . 'vendor/js/datatables.min.js', [ 'jquery' ], null, true );
		wp_register_script( 'datatables-uikit', BDTEP_ASSETS_URL . 'vendor/js/datatables.uikit.min.js', [ 'jquery', 'datatables' ], null, true );

		if ( element_pack_is_widget_enabled( 'timeline' ) ) {
			wp_register_script( 'timeline', BDTEP_ASSETS_URL . 'vendor/js/timeline.min.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'scrollnav' ) or element_pack_is_widget_enabled( 'price-table' ) or element_pack_is_widget_enabled( 'marker' ) or element_pack_is_widget_enabled( 'logo-grid' ) or element_pack_is_widget_enabled( 'logo-carousel' ) or element_pack_is_widget_enabled( 'iconnav' ) or element_pack_is_widget_enabled( 'helpdesk' ) or element_pack_is_widget_enabled( 'circle-menu' ) or element_pack_is_widget_enabled( 'image-stack' ) ) {
			wp_register_script( 'popper', BDTEP_ASSETS_URL . 'vendor/js/popper.min.js', [ 'jquery' ], null, true );
			wp_register_script( 'tippyjs', BDTEP_ASSETS_URL . 'vendor/js/tippy.all.min.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'slinky-vertical-menu' ) ) {
			wp_register_script( 'slinky', BDTEP_ASSETS_URL . 'vendor/js/slinky.js', [], '4.2.1', true );
		}
		//advanced-image-gallery
		if ( element_pack_is_widget_enabled( 'testimonial-grid' ) or element_pack_is_widget_enabled( 'post-gallery' ) or element_pack_is_widget_enabled( 'custom-gallery' ) or element_pack_is_widget_enabled( 'advanced-image-gallery' ) or element_pack_is_widget_enabled( 'portfolio-list' ) or element_pack_is_widget_enabled( 'portfolio-gallery' ) or element_pack_is_widget_enabled( 'portfolio-carousel' ) or element_pack_is_widget_enabled( 'tutor-lms-course-grid' ) ) {
			wp_register_script( 'tilt', BDTEP_ASSETS_URL . 'vendor/js/vanilla-tilt.min.js', [ 'jquery' ], null, true );
		}
		if ( element_pack_is_widget_enabled( 'video-gallery' ) ) {
			wp_register_script( 'rvslider', BDTEP_ASSETS_URL . 'vendor/js/rvslider.min.js', [ 'jquery' ], null, true );
		}
		//threesixty-product-viewer
		if ( element_pack_is_widget_enabled( 'threesixty-product-viewer' ) ) {
			wp_register_script( 'spritespin', BDTEP_ASSETS_URL . 'vendor/js/spritespin.min.js', [ 'jquery' ], '4.0.5', true );
		}
		if ( element_pack_is_widget_enabled( 'post-grid-tab' ) or element_pack_is_widget_enabled( 'iframe' ) ) {
			wp_register_script( 'recliner', BDTEP_ASSETS_URL . 'vendor/js/recliner.min.js', [ 'jquery' ], '0.2.2', true );
		}
		if ( element_pack_is_widget_enabled( 'advanced-image-gallery' ) ) {
			wp_register_script( 'ep-justified-gallery', BDTEP_ASSETS_URL . 'vendor/js/jquery.justifiedGallery.min.js', [ 'jquery' ], '1.0.0', true );
		}
		if ( element_pack_is_widget_enabled( 'lottie-image' ) or element_pack_is_widget_enabled( 'lottie-icon-box' ) ) {
			wp_register_script( 'lottie', BDTEP_ASSETS_URL . 'vendor/js/lottie.min.js', [], '5.6.10', true );
		}
		if ( element_pack_is_widget_enabled( 'slideshow' ) ) {
			wp_register_script( 'thumbnail-scroller', BDTEP_ASSETS_URL . 'vendor/js/jquery.mThumbnailScroller.min.js', [ 'jquery' ], '2.0.2', true );
		}
		if ( element_pack_is_widget_enabled( 'reading-progress' ) ) {
			wp_register_script( 'progressHorizontal', BDTEP_ASSETS_URL . 'vendor/js/jquery.progressHorizontal.min.js', [ 'jquery' ], '2.0.2', true );
		}
		if ( element_pack_is_widget_enabled( 'source-code' ) ) {
			wp_register_script( 'prism', BDTEP_ASSETS_URL . 'vendor/js/prism.min.js', [], '1.17.1', true );
		}
		if ( element_pack_is_widget_enabled( 'advanced-counter' ) or element_pack_is_widget_enabled( 'total-count' ) ) {
			wp_register_script( 'advanced-counter', BDTEP_ASSETS_URL . 'vendor/js/countUp.min.js', [ 'jquery' ], '2.0.4', true );
		}
		if ( element_pack_is_widget_enabled( 'time-zone' ) or element_pack_is_widget_enabled( 'business-hours' ) ) {
			wp_register_script( 'jclock', BDTEP_ASSETS_URL . 'vendor/js/jquery.jclock.min.js', [ 'jquery' ], '0.0.1', true );
		}
		if ( element_pack_is_widget_enabled( 'honeycombs' ) ) {
			wp_register_script( 'honeycombs', BDTEP_ASSETS_URL . 'vendor/js/jquery.honeycombs.min.js', [ 'jquery' ], '0.0.1', true );
		}
		if ( element_pack_is_widget_enabled( 'image-compare' ) ) {
			wp_register_script( 'image-compare-viewer', BDTEP_ASSETS_URL . 'vendor/js/image-compare-viewer.min.js', [ 'jquery' ], '0.0.1', true );
		}
		if ( element_pack_is_widget_enabled( 'interactive-card' ) or element_pack_is_widget_enabled( 'image-expand' ) or element_pack_is_widget_enabled( 'animated-heading' ) or element_pack_is_widget_enabled( 'stacker' ) or element_pack_is_widget_enabled( 'horizontal-scroller' ) or element_pack_is_widget_enabled( 'marquee' ) ) {
			wp_register_script( 'gsap', BDTEP_ASSETS_URL . 'vendor/js/gsap.min.js', [], '3.12.2', true );
		}
		if ( element_pack_is_widget_enabled( 'image-expand' ) or element_pack_is_widget_enabled( 'animated-heading' ) ) {
			wp_register_script( 'split-text-js', BDTEP_ASSETS_URL . 'vendor/js/SplitText.min.js', [ 'jquery', 'gsap' ], '3.9.1', true );
		}
		if ( element_pack_is_widget_enabled( 'interactive-card' ) ) {
			wp_register_script( 'wavify', BDTEP_ASSETS_URL . 'vendor/js/wavify.min.js', [ 'gsap' ], '0.0.1', true );
		}
		if ( element_pack_is_widget_enabled( 'svg-blob' ) ) {
			wp_register_script( 'animejs', BDTEP_ASSETS_URL . 'vendor/js/anime.min.js', [], '3.11.3', true );
		}

		if ( element_pack_is_widget_enabled( 'svg-maps' ) ) {
			wp_register_script( 'gs-charts', '//www.gstatic.com/charts/loader.js', [], '0.0.1', true );
		}
		if ( element_pack_is_widget_enabled( 'tags-cloud' ) ) {
			wp_register_script( 'tags-cloud', BDTEP_ASSETS_URL . 'vendor/js/awesomeCloud.min.js', [], '0.2', true );
			wp_register_script( 'tags-exCanvas', BDTEP_ASSETS_URL . 'vendor/js/jquery.tagcanvas.min.js', [], '2.9', true );
		}
		if ( element_pack_is_widget_enabled( 'video-player' ) ) {
			wp_register_script( 'ep-video-player', BDTEP_ASSETS_URL . 'js/widget/ep-video-player' . $suffix . '.js', [], '2.9', true );
		}
		if ( element_pack_is_widget_enabled( 'vertical-menu' ) ) {
			wp_register_script( 'metis-menu', BDTEP_ASSETS_URL . 'vendor/js/metisMenu.min.js', [], '3.0.6', true );
		}

		if ( element_pack_is_widget_enabled( 'calendly' ) ) {
			wp_register_script( 'calendly', BDTEP_ASSETS_URL . 'vendor/js/calendly.min.js', [ 'jquery' ], '0.0.1', true );
		}

		if ( element_pack_is_widget_enabled( 'advanced-calculator' ) ) {
			wp_register_script( 'jstat', BDTEP_ASSETS_URL . 'vendor/js/jstat.min.js', [], '1.9.2', true );
			wp_register_script( 'formula', BDTEP_ASSETS_URL . 'vendor/js/formula.min.js', [], '2.9.2', true );
		}
		if ( element_pack_is_widget_enabled( 'horizontal-scroller' ) ) {
			wp_register_script( 'scroll-trigger-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollTrigger.min.js', [ 'gsap' ], '3.9.1', true );
			wp_register_script( 'scroll-to-plugin-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollToPlugin.min.js', [ 'gsap' ], '3.9.1', true );
		}

		if ( element_pack_is_widget_enabled( 'stacker' ) ) {
			wp_register_script( 'scroll-trigger-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollTrigger.min.js', [ 'gsap' ], '3.9.1', true );
		}

		if ( element_pack_is_widget_enabled( 'svg-image' ) ) {
			wp_register_script( 'scroll-trigger-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollTrigger.min.js', [ 'gsap' ], '3.9.1', true );
			wp_register_script( 'draw-svg-plugin-js', BDTEP_ASSETS_URL . 'vendor/js/DrawSVGPlugin.min.js', [ 'gsap' ], '3.11.3', true );
			wp_register_script( 'magic-scroll-animation-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollMagicAnimation.min.js', [ 'gsap' ], '2.0.8', true );
			wp_register_script( 'magic-scroll-js', BDTEP_ASSETS_URL . 'vendor/js/ScrollMagic.min.js', [ 'gsap' ], '2.0.8', true );
		}


		if ( element_pack_is_widget_enabled( 'marquee' ) ) {
			wp_register_script( 'draggable', BDTEP_ASSETS_URL . 'vendor/js/Draggable.min.js', [ 'gsap' ], '3.12.2', true );
			wp_register_script( 'InertiaPlugin', BDTEP_ASSETS_URL . 'vendor/js/InertiaPlugin.min.js', [ 'gsap' ], '3.12.2', true );
		}
		/**
		 * TODO: Need to separate wc widget js
		 */
		wp_register_script( 'ep-woocommerce', BDTEP_ASSETS_URL . 'vendor/js/ep-woocommerce' . $suffix . '.js', [ 'jquery', 'datatables' ], BDTEP_VER, true );
		wp_register_script( 'ep-ticker', BDTEP_ASSETS_URL . 'vendor/js/ticker.min.js', [ 'jquery' ], BDTEP_VER, true );
	}

	public function register_site_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		// third party widget css
		/**
		 * No need condition datatables
		 */

		wp_register_style( 'datatables', BDTEP_ASSETS_URL . 'css/datatables' . $direction_suffix . '.css', [], BDTEP_VER );
		wp_register_style( 'datatables-uikit', BDTEP_ASSETS_URL . 'css/datatables-uikit' . $direction_suffix . '.css', [], BDTEP_VER );

		if ( element_pack_is_widget_enabled( 'image-magnifier' ) ) {
			wp_register_style( 'imagezoom', BDTEP_ASSETS_URL . 'css/imagezoom' . $direction_suffix . '.css', [], BDTEP_VER );
		}

		if ( element_pack_is_widget_enabled( 'slideshow' ) ) {
			wp_register_style( 'mThumbnailScroller', BDTEP_ASSETS_URL . 'css/jquery-mThumbnailScroller' . $direction_suffix . '.css', [], BDTEP_VER );
		}

		/**
		 * ?TODO: Need to separate wc widget css
		 */
		wp_register_style( 'ep-tutor-lms', BDTEP_ASSETS_URL . 'css/ep-tutor-lms' . $direction_suffix . '.css', [], BDTEP_VER );
		// Vendor style register
		wp_register_style( 'tippy', BDTEP_ASSETS_URL . 'css/tippy' . $direction_suffix . '.css', [], BDTEP_VER );
		wp_register_style( 'prism', BDTEP_ASSETS_URL . 'vendor/css/prism' . $direction_suffix . '.css', [], BDTEP_VER );
	}

	/**
	 * Loading site related style from here.
	 */
	public function enqueue_site_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style( 'bdt-uikit', BDTEP_ASSETS_URL . 'css/bdt-uikit' . $direction_suffix . '.css', [], '3.17.11' );
		wp_enqueue_style( 'ep-helper', BDTEP_ASSETS_URL . 'css/ep-helper' . $direction_suffix . '.css', [], BDTEP_VER );
	}

	/**
	 * Loading site related script that needs all time such as uikit.
	 * @return [type] [description]mn
	 */
	public function enqueue_site_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'bdt-uikit', BDTEP_ASSETS_URL . 'js/bdt-uikit' . $suffix . '.js', [ 'jquery' ], '3.17.11', true );

		if ( ! element_pack_is_asset_optimization_enabled() ) {
			wp_enqueue_script( 'element-pack-helper', BDTEP_ASSETS_URL . 'js/common/helper' . $suffix . '.js', [ 'jquery', 'elementor-frontend' ], BDTEP_VER, true );
		}

		$script_config = [ 
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'element-pack-site' ),
			'data_table'    => [ 
				'language' => [ 
					'sLengthMenu'  => sprintf( esc_html_x( 'Show %1s Entries', 'DataTable String', 'bdthemes-element-pack' ), '_MENU_' ),
					'sInfo'        => sprintf( esc_html_x( 'Showing %1s to %2s of %3s entries', 'DataTable String', 'bdthemes-element-pack' ), '_START_', '_END_', '_TOTAL_' ),
					'sSearch'      => esc_html_x( 'Search :', 'DataTable String', 'bdthemes-element-pack' ),
					'sZeroRecords' => esc_html_x( 'No matching records found', 'DataTable String', 'bdthemes-element-pack' ),
					'oPaginate'    => [ 
						'sPrevious' => esc_html_x( 'Previous', 'DataTable String', 'bdthemes-element-pack' ),
						'sNext'     => esc_html_x( 'Next', 'DataTable String', 'bdthemes-element-pack' ),
					],
				],
			],
			'contact_form'  => [ 
				'sending_msg' => esc_html_x( 'Sending message please wait...', 'Contact Form String', 'bdthemes-element-pack' ),
				'captcha_nd'  => esc_html_x( 'Invisible captcha not defined!', 'Contact Form String', 'bdthemes-element-pack' ),
				'captcha_nr'  => esc_html_x( 'Could not get invisible captcha response!', 'Contact Form String', 'bdthemes-element-pack' ),

			],
			'mailchimp'     => [ 
				'subscribing' => esc_html_x( 'Subscribing you please wait...', 'Mailchimp String', 'bdthemes-element-pack' ),
			],
			'search'        => [ 
				'more_result'   => esc_html_x( 'More Results', 'Search Widget String', 'bdthemes-element-pack' ),
				'search_result' => esc_html_x( 'SEARCH RESULT', 'Search Widget String', 'bdthemes-element-pack' ),
				'not_found'     => esc_html_x( 'not found', 'Search Widget String', 'bdthemes-element-pack' ),
			],
			'elements_data' => $this->elements_data,
		];

		// localize for user login widget ajax login script

		wp_localize_script(
			'bdt-uikit',
			'element_pack_ajax_login_config',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'language'       => substr( get_locale(), 0, 2 ),
				'loadingmessage' => esc_html_x( 'Sending user info, please wait...', 'User Login and Register', 'bdthemes-element-pack' ),
				'unknownerror'   => esc_html_x( 'Unknown error, make sure access is correct!', 'User Login and Register', 'bdthemes-element-pack' ),
			)
		);

		$script_config = apply_filters( 'element_pack/frontend/localize_settings', $script_config );

		// TODO for editor script
		wp_localize_script( 'bdt-uikit', 'ElementPackConfig', $script_config );
	}

	public function enqueue_editor_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'element-pack',
			BDTEP_ASSETS_URL . 'js/ep-editor' . $suffix . '.js',
			[ 
				'backbone-marionette',
				'elementor-common-modules',
				'elementor-editor-modules',
			],
			BDTEP_VER,
			true
		);

		$localize_data = [ 
			'pro_installed'       => element_pack_pro_activated(),
			'promotional_widgets' => [],
		];

		if ( ! element_pack_pro_activated() ) {
			$pro_widget_map                       = new Pro_Widget_Map();
			$localize_data['promotional_widgets'] = $pro_widget_map->get_pro_widget_map();
		}

		wp_localize_script(
			'element-pack',
			'ElementPackConfigPromotional',
			$localize_data
		);
	}

	/**
	 * Load editor editor related style from here
	 */
	public function enqueue_preview_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style( 'ep-preview', BDTEP_ASSETS_URL . 'css/ep-preview' . $direction_suffix . '.css', [], BDTEP_VER );
	}

	public function enqueue_editor_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_register_style( 'ep-editor', BDTEP_ASSETS_URL . 'css/ep-editor' . $direction_suffix . '.css', [], BDTEP_VER );
		wp_enqueue_style( 'ep-editor' );
	}

	public function enqueue_minified_css() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		$upload_dir = $this->get_upload_dir() . 'css/ep-styles.css';
		$version    = get_option( 'element-pack-minified-asset-manager-version' );

		if ( element_pack_is_asset_optimization_enabled() && file_exists( $upload_dir ) ) {
			$upload_url = $this->get_upload_url() . 'css/ep-styles.css';
			wp_register_style( 'ep-styles', $upload_url, [], $version );
		} else {
			wp_register_style( 'ep-styles', BDTEP_URL . 'assets/css/ep-styles' . $direction_suffix . '.css', [], BDTEP_VER );
			wp_register_style( 'ep-font', BDTEP_ASSETS_URL . 'css/ep-font' . $direction_suffix . '.css', [], BDTEP_VER );
		}

		if ( element_pack_is_asset_optimization_enabled() ) {
			wp_enqueue_style( 'ep-styles' );
		}
	}

	public function enqueue_minified_js() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$upload_dir = $this->get_upload_dir() . 'js/ep-scripts.js';
		$version    = get_option( 'element-pack-minified-asset-manager-version' );

		if ( element_pack_is_asset_optimization_enabled() && file_exists( $upload_dir ) ) {
			$upload_url = $this->get_upload_url() . 'js/ep-scripts.js';

			wp_register_script( 'ep-scripts', $upload_url, [ 'elementor-frontend' ], $version, true );
		} else {
			wp_register_script( 'ep-scripts', BDTEP_URL . 'assets/js/ep-scripts' . $suffix . '.js', [ 'elementor-frontend' ], BDTEP_VER, true );
		}

		if ( element_pack_is_asset_optimization_enabled() ) {
			wp_enqueue_script( 'ep-scripts' );
		}
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_style( 'ep-notice-css', BDTEP_ADMIN_URL . 'assets/css/ep-notice.css', [], BDTEP_VER, 'all' );
		wp_enqueue_script( 'ep-notice-js', BDTEP_ADMIN_URL . 'assets/js/ep-notice.js', [ 'jquery' ], BDTEP_VER, true );
	}

	/**
	 * Callback to shortcodes template
	 * @param array $atts attributes for shortcode.
	 */
	public function shortcode_template( $atts ) {

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'rooten_custom_template'
		);

		$id = ! empty( $atts['id'] ) ? intval( $atts['id'] ) : '';

		if ( empty( $id ) ) {
			return '';
		}

		return self::elementor()->frontend->get_builder_content_for_display( $id );
	}

	/**
	 * Add element_pack_ajax_login() function with wp_ajax_nopriv_ function
	 */
	public function element_pack_ajax_login_init() {
		add_action( 'wp_ajax_nopriv_element_pack_ajax_login', [ $this, "element_pack_ajax_login" ] );
	}

	/**
	 * For ajax login
	 */
	public function element_pack_ajax_login() {
		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ajax-login-nonce', 'bdt-user-login-sc' );

		// Nonce is checked, get the POST data and sign user on
		$access_info                  = [];
		$access_info['user_login']    = ! empty( $_POST['user_login'] ) ? $_POST['user_login'] : "";
		$access_info['user_password'] = ! empty( $_POST['user_password'] ) ? $_POST['user_password'] : "";
		$access_info['remember']      = ! empty( $_POST['rememberme'] ) ? true : false;
		$user_signon                  = wp_signon( $access_info, false );

		if ( ! is_wp_error( $user_signon ) ) {
			echo wp_json_encode(
				[ 
					'loggedin' => true,
					'message'  => esc_html_x( 'Login successful, Redirecting...', 'User Login and Register', 'bdthemes-element-pack' ),
				]
			);
		} else {
			echo wp_json_encode(
				[ 
					'loggedin' => false,
					'message'  => esc_html_x( 'Oops! Wrong username or password!', 'User Login and Register', 'bdthemes-element-pack' ),
				]
			);
		}

		die();
	}

	// Load WPML compatibility instance
	public function wpml_compatiblity() {
		return Element_Pack_WPML::get_instance();
	}

	/**
	 * initialize the category
	 * @return void
	 */
	public function element_pack_init() {
		$context_menu = element_pack_option( 'context-menu', 'element_pack_other_settings', 'off' );

		$this->_modules_manager = new Manager();

		do_action( 'bdthemes_element_pack/init' );

		if ( 'on' === $context_menu ) {
			add_action( 'elementor/kit/register_tabs', function (Kit $kit) {
				$kit->register_tab( 'element-pack-context-menu', Includes\Settings\Context_Menu_Controls::class);
			}, 1, 40 );
		}
	}

	public function ElementPack_Json_File_Import_register_controls() {
		$controls_manager = Plugin::$instance->controls_manager;
		$controls_manager->register( new ElementPack_JSON_File_Upload_Control() );
	}

	public function ElementPack_FB_Token_Register_Controls() {

		$controls_manager = Plugin::$instance->controls_manager;
		$controls_manager->register( new ElementPack_FB_Access_Token_Generator_Control() );
	}

	/**
	 * initialize the category
	 * @return [type] [description]
	 */
	public function element_pack_category_register() {

		$elementor = Plugin::$instance;

		if ( ! element_pack_pro_activated() ) {
			$elementor->elements_manager->add_category( BDTEP_SLUG, [ 'title' => 'Element Pack (Lite)', 'icon' => 'font' ] );
		} else {
			$elementor->elements_manager->add_category( BDTEP_SLUG, [ 'title' => BDTEP_TITLE, 'icon' => 'font' ] );
			$elementor->elements_manager->add_category( BDTEP_SLUG . '-bbpress', [ 'title' => 'BbPress', 'icon' => 'eicon-search' ] );
		}
	}

	public function element_pack_svg_support() {

		return SVG_Support::get_instance();
	}

	private function setup_hooks() {

		add_action( 'elementor/controls/register', [ $this, 'ElementPack_Json_File_Import_register_controls' ] );

		add_action( 'elementor/controls/register', [ $this, 'ElementPack_FB_Token_Register_Controls' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'element_pack_category_register' ] );
		add_action( 'elementor/init', [ $this, 'element_pack_init' ] );

		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

		add_action( 'elementor/frontend/before_register_styles', [ $this, 'register_site_styles' ] );
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_site_scripts' ] );

		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_preview_styles' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'enqueue_site_styles' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_site_scripts' ] );

		// For frontend css load
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_minified_css' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_minified_js' ] );

		add_shortcode( 'rooten_custom_template', [ $this, 'shortcode_template' ] );

		// When user not login add this action
		if ( ! is_user_logged_in() ) {
			add_action( 'elementor/init', [ $this, 'element_pack_ajax_login_init' ] );
		}

		// load WordPress dashboard scripts
		add_action( 'admin_init', [ $this, 'enqueue_admin_scripts' ] );

		if ( ! element_pack_pro_activated() ) {
			$pro_widget_map = new Pro_Widget_Map();
			$pro_widgets    = $pro_widget_map->get_pro_widget_map();

			global $elementor_widget_blacklist;

			$elementor_widget_blacklist = $pro_widgets;

			add_filter(
				'elementor/editor/localize_settings',
				function ($settings) {
					global $elementor_widget_blacklist;

					foreach ( $elementor_widget_blacklist as $widgets ) {
						$widget_name                                                              = 'bdt-' . $widgets['name'];
						$settings['initial_document']['widgets'][ $widget_name ]['show_in_panel'] = false;
					}
					return $settings;
				},
				99
			);
		}
	}

	/**
	 * Element_Pack_Loader constructor.
	 */
	private function __construct() {
		// Register class automatically
		spl_autoload_register( [ $this, 'autoload' ] );
		// Include some backend files
		$this->_includes();

		add_filter( 'bdt_ep_init_pro', [ $this, 'pro_return' ], 100 );

		// Finally hooked up all things here
		$this->setup_hooks();

		$this->element_pack_svg_support()->init();

		$this->wpml_compatiblity()->init();
	}
}

if ( ! defined( 'BDTEP_TESTS' ) ) {
	// In tests we run the instance manually.
	Element_Pack_Loader::instance();
}
// handy function for push data
function element_pack_config() {
	return Element_Pack_Loader::instance();
}
