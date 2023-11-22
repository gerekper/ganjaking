<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class Assets
{
    public static $dce_styles = [];
    public static $dce_scripts = [];
    public static $styles = array(
        'dce-icon' => '/assets/css/dce-icon.css',
        'dce-crypto-badge' => '/assets/css/crypto-badge.css',
        'dce-icons-form-style' => '/assets/css/icons-form.css',
        'dce-hidden-label' => 'assets/css/hidden-label.css',
        'dce-globalsettings' => 'assets/css/global-settings.css',
        'dce-style' => '/assets/css/style.css',
        'dce-animations' => '/assets/css/animations.css',
        'dce-preview' => '/assets/css/preview.css',
        'dce-acf' => '/assets/css/acf-fields.css',
        'dce-acf-relationship-old-version' => '/assets/css/acf-relationship-old-version.css',
        'dce-acfslider' => '/assets/css/acf-slider.css',
        'dce-acfGallery' => '/assets/css/acf-gallery.css',
        'dce-acf-repeater-old' => '/assets/css/acf-repeater-old.css',
        'dce-acf-repeater' => '/assets/css/acf-repeater.css',
        'dce-add-to-calendar' => '/assets/css/add-to-calendar.css',
        'dce-dynamic-visibility' => '/assets/css/dynamic-visibility.css',
        'dce-copy-to-clipboard' => '/assets/css/copy-to-clipboard.css',
        'dce-google-maps' => '/assets/css/dynamic-google-maps.css',
        'dce-dynamic-google-maps-directions' => '/assets/css/dynamic-google-maps-directions.css',
        'dce-pods' => '/assets/css/pods-fields.css',
        'dce-pods-gallery' => '/assets/css/pods-gallery.css',
        'dce-toolset' => '/assets/css/toolset-fields.css',
        'dce-tooltip' => '/assets/css/tooltip.css',
        'dce-pdf-viewer' => '/assets/css/pdf-viewer.css',
        // Dynamic Posts - old version
        'dce-dynamic-posts-old-version' => '/assets/css/dynamic-posts-old-version.css',
        'dce-dynamicPosts_slick' => '/assets/css/dynamic-posts-slick.css',
        'dce-dynamicPosts_swiper' => '/assets/css/dynamic-posts-swiper.css',
        'dce-dynamicPosts_timeline' => '/assets/css/dynamic-posts-timeline.css',
        // Dynamic Posts
        'dce-dynamic-posts' => '/assets/css/dynamic-posts.css',
        'dce-dynamicPosts-grid' => '/assets/css/dynamic-posts-skin-grid.css',
        'dce-dynamicPosts-carousel' => '/assets/css/dynamic-posts-skin-carousel.css',
        'dce-dynamicPosts-dualcarousel' => '/assets/css/dynamic-posts-skin-dual-carousel.css',
        'dce-dynamicPosts-accordion' => ['path' => '/assets/css/dynamic-posts-skin-accordion.css', 'deps' => ['dce-accordionjs']],
        'dce-dynamicPosts-timeline' => '/assets/css/dynamic-posts-skin-timeline.css',
        'dce-dynamicPosts-smoothscroll' => '/assets/css/dynamic-posts-skin-smoothscroll.css',
        'dce-dynamicPosts-gridtofullscreen3d' => '/assets/css/dynamic-posts-skin-grid-to-fullscreen-3d.css',
        'dce-dynamicPosts-crossroadsslideshow' => '/assets/css/dynamic-posts-skin-crossroads-slideshow.css',
        'dce-dynamicPosts-nextpost' => '/assets/css/dynamic-posts-skin-next-post.css',
        'dce-dynamicPosts-3d' => '/assets/css/dynamic-posts-skin-3d.css',
        'dce-dynamicUsers' => '/assets/css/dynamic-users.css',
        'dce-iconFormat' => '/assets/css/icon-format.css',
        'dce-nextPrev' => '/assets/css/prev-next.css',
        'dce-list' => '/assets/css/taxonomy-terms-list.css',
        'dce-featuredImage' => '/assets/css/featured-image.css',
        'dce-modalWindow' => '/assets/css/fire-modal-window.css',
        'dce-modal' => '/assets/css/modals.css',
        'dce-pageScroll' => '/assets/css/page-scroll.css',
        'dce-reveal' => '/assets/css/reveal.css',
        'dce-threesixtySlider' => '/assets/css/360-slider.css',
        'dce-before-after' => '/assets/css/before-after.css',
        'dce-parallax' => '/assets/css/parallax.css',
        'dce-filebrowser' => '/assets/css/file-browser.css',
        'dce-animatetext' => '/assets/css/animated-text.css',
        'dce-imagesDistortion' => '/assets/css/distortion-image.css',
        'dce-animatedOffcanvasMenu' => '/assets/css/animated-off-canvas-menu.css',
        'dce-cursorTracker' => '/assets/css/cursor-tracker.css',
        'dce-dynamic-title' => '/assets/css/dynamic-title.css',
        'dce-breadcrumbs' => '/assets/css/breadcrumbs.css',
        'dce-date' => '/assets/css/date.css',
        'dce-add-to-favorites' => '/assets/css/add-to-favorites.css',
        'dce-terms' => '/assets/css/terms-and-taxonomy.css',
        'dce-content' => '/assets/css/content.css',
        'dce-excerpt' => '/assets/css/excerpt.css',
        'dce-readmore' => '/assets/css/read-more.css',
        'dce-bgCanvas' => '/assets/css/bg-canvas.css',
        'dce-svg' => '/assets/css/svg.css',
        'dce-views' => '/assets/css/views.css',
    );
    public static $vendor_css = array('dce-jquery-confirm' => '/assets/lib/jquery-confirm/jquery-confirm.min.css', 'dce-osm-map' => '/assets/lib/leaflet/leaflet.css', 'dce-osm-map-marker-cluster' => '/assets/lib/leaflet/plugins/markercluster/MarkerCluster.css', 'dce-photoSwipe_default' => '/assets/lib/photoSwipe/photoswipe.min.css', 'dce-photoSwipe_skin' => '/assets/lib/photoSwipe/default-skin/default-skin.css', 'dce-justifiedGallery' => '/assets/lib/justifiedGallery/css/justifiedGallery.min.css', 'dce-file-icon' => '/assets/lib/file-icon/file-icon-vivid.min.css', 'animatecss' => '/assets/lib/animate/animate.min.css', 'datatables' => '/assets/lib/datatables/datatables.min.css', 'dce-plyr' => '/assets/lib/plyr/plyr.css', 'dce-swiper' => '/assets/lib/swiper/css/swiper.min.css', 'dce-accordionjs' => '/assets/lib/accordionjs/accordion.css');
    public static $vendor_js;
    private static function init_vendor_js()
    {
        $google_maps_api = get_option('dce_google_maps_api');
        $locale2 = \substr(get_locale(), 0, 2);
        if (get_option('dce_paypal_api_mode', 'sandbox') === 'sandbox') {
            $paypal_option = get_option('dce_paypal_api_client_id_sandbox');
        } else {
            $paypal_option = get_option('dce_paypal_api_client_id_live');
        }
        // Many user have incorrectly set their email as client id, make sure
        // this is not the case:
        if (!\strpos($paypal_option, '@')) {
            $paypal_client_id = $paypal_option;
        } else {
            $paypal_client_id = \false;
        }
        $paypal_currency = get_option('dce_paypal_api_currency', 'USD');
        self::$vendor_js = [
            'dce-pdf-js' => ['path' => '/assets/lib/pdf-js/pdf.min.js', 'deps' => []],
            'dce-chart-js' => '/assets/lib/chart-js/chart.min.js',
            'dce-imagesloaded' => '/assets/lib/imagesloaded/imagesloaded.pkgd.min.js',
            'dce-jquery-confirm' => '/assets/lib/jquery-confirm/jquery-confirm.min.js',
            'dce-leaflet' => '/assets/lib/leaflet/leaflet.js',
            'dce-leaflet-markercluster' => '/assets/lib/leaflet/plugins/markercluster/leaflet.markercluster.js',
            'dce-expressionlanguage' => '/assets/lib/expressionlanguage/expressionlanguage.min.js',
            'dce-html2canvas' => '/assets/lib/html2canvas/html2canvas.min.js',
            'dce-jspdf' => '/assets/lib/js-pdf/jspdf.umd.min.js',
            'dce-aframe' => ['path' => '/assets/lib/aframe/aframe.min.js', 'deps' => [], 'in_footer' => \false],
            'dce-datatables' => '/assets/lib/datatables/datatables.min.js',
            'dce-plyr-js' => '/assets/lib/plyr/plyr.polyfilled.js',
            'dce-dayjs' => '/assets/lib/dayjs/dayjs.min.js',
            'dce-wow' => '/assets/lib/wow/wow.min.js',
            'dce-jquery-match-height' => 'assets/lib/jquery-match-height/jquery.matchHeight-min.js',
            'isotope' => '/assets/lib/isotope/isotope.pkgd.min.js',
            'dce-infinitescroll' => '/assets/lib/infiniteScroll/infinite-scroll.pkgd.min.js',
            'dce-jquery-slick' => '/assets/lib/slick/slick.min.js',
            'velocity' => '/assets/lib/velocity/velocity.min.js',
            'velocity-ui' => '/assets/lib/velocity/velocity.ui.min.js',
            'dce-diamonds' => '/assets/lib/diamonds/jquery.diamonds.js',
            'dce-homeycombs' => '/assets/lib/homeycombs/jquery.homeycombs.js',
            'photoswipe' => '/assets/lib/photoSwipe/photoswipe.min.js',
            'photoswipe-ui' => '/assets/lib/photoSwipe/photoswipe-ui-default.min.js',
            'tilt-lib' => '/assets/lib/tilt/tilt.jquery.min.js',
            'dce-jquery-visible' => '/assets/lib/visible/jquery-visible.min.js',
            'jquery-easing' => '/assets/lib/jquery-easing/jquery-easing.min.js',
            'justifiedGallery-lib' => '/assets/lib/justifiedGallery/js/jquery.justifiedGallery.min.js',
            'dce-parallaxjs-lib' => '/assets/lib/parallaxjs/parallax.min.js',
            'dce-threesixtyslider-lib' => '/assets/lib/threesixty-slider/threesixty.min.js',
            'dce-jqueryeventmove-lib' => ['path' => '/assets/lib/twentytwenty/jquery.event.move.js', 'deps' => ['jquery']],
            'dce-twentytwenty-lib' => '/assets/lib/twentytwenty/jquery.twentytwenty.js',
            'dce-anime-lib' => '/assets/lib/anime/anime.min.js',
            'dce-signature-lib' => '/assets/lib/signature-pad/signature_pad.min.js',
            'dce-distortion-lib' => '/assets/lib/distortion/distortion-lib.js',
            'dce-threejs-lib' => '/assets/lib/threejs/three.min.js',
            'dce-threejs-figure' => '/assets/lib/threejs/figure.js',
            'dce-threejs-EffectComposer' => '/assets/lib/threejs/postprocessing/EffectComposer.js',
            'dce-threejs-RenderPass' => '/assets/lib/threejs/postprocessing/RenderPass.js',
            'dce-threejs-ShaderPass' => '/assets/lib/threejs/postprocessing/ShaderPass.js',
            'dce-threejs-BloomPass' => '/assets/lib/threejs/postprocessing/BloomPass.js',
            'dce-threejs-FilmPass' => '/assets/lib/threejs/postprocessing/FilmPass.js',
            'dce-threejs-HalftonePass' => '/assets/lib/threejs/postprocessing/HalftonePass.js',
            'dce-threejs-DotScreenPass' => '/assets/lib/threejs/postprocessing/DotScreenPass.js',
            'dce-threejs-GlitchPass' => '/assets/lib/threejs/postprocessing/GlitchPass.js',
            'dce-threejs-CopyShader' => '/assets/lib/threejs/shaders/CopyShader.js',
            'dce-threejs-HalftoneShader' => '/assets/lib/threejs/shaders/HalftoneShader.js',
            'dce-threejs-RGBShiftShader' => '/assets/lib/threejs/shaders/RGBShiftShader.js',
            'dce-threejs-DotScreenShader' => '/assets/lib/threejs/shaders/DotScreenShader.js',
            'dce-threejs-ConvolutionShader' => '/assets/lib/threejs/shaders/ConvolutionShader.js',
            'dce-threejs-FilmShader' => '/assets/lib/threejs/shaders/FilmShader.js',
            'dce-threejs-ColorifyShader' => '/assets/lib/threejs/shaders/ColorifyShader.js',
            'dce-threejs-VignetteShader' => '/assets/lib/threejs/shaders/VignetteShader.js',
            'dce-threejs-DigitalGlitch' => '/assets/lib/threejs/shaders/DigitalGlitch.js',
            'dce-threejs-PixelShader' => '/assets/lib/threejs/shaders/PixelShader.js',
            'dce-threejs-LuminosityShader' => '/assets/lib/threejs/shaders/LuminosityShader.js',
            'dce-threejs-SobelOperatorShader' => '/assets/lib/threejs/shaders/SobelOperatorShader.js',
            'dce-threejs-AsciiEffect' => '/assets/lib/threejs/effects/AsciiEffect.js',
            // WebGL Distortion
            'dce-data-gui' => '/assets/lib/threejs/libs/dat.gui.min.js',
            'dce-displacement-sketch' => '/assets/lib/threejs/sketch.js',
            // Dynamic Posts
            'dce-threejs-gridtofullscreeneffect' => '/assets/lib/threejs/GridToFullscreenEffect.js',
            'dce-threejs-TweenModule' => '/assets/lib/threejs/libs/tween.module.min.js',
            'dce-threejs-TrackballControls' => '/assets/lib/threejs/controls/TrackballControls.js',
            'dce-threejs-OrbitControls' => '/assets/lib/threejs/controls/OrbitControls.js',
            'dce-threejs-CameraControls' => '/assets/lib/threejs/controls/camera-controls.min.js',
            'dce-threejs-CSS3DRenderer' => '/assets/lib/threejs/renderers/CSS3DRenderer.js',
            // GSAP
            'dce-tweenMax-lib' => '/assets/lib/greensock/TweenMax.min.js',
            'dce-tweenLite-lib' => '/assets/lib/greensock/TweenLite.min.js',
            'dce-timelineLite-lib' => '/assets/lib/greensock/TimelineLite.min.js',
            'dce-timelineMax-lib' => '/assets/lib/greensock/TimelineMax.min.js',
            'dce-morphSVG-lib' => '/assets/lib/greensock/plugins/MorphSVGPlugin.min.js',
            'dce-splitText-lib' => '/assets/lib/greensock/utils/SplitText.min.js',
            'dce-textPlugin-lib' => '/assets/lib/greensock/plugins/TextPlugin.min.js',
            'dce-svgdraw-lib' => '/assets/lib/greensock/plugins/DrawSVGPlugin.min.js',
            'dce-gsap-lib' => '/assets/lib/greensock/gsap.min.js',
            'dce-ScrollToPlugin-lib' => '/assets/lib/greensock/plugins/ScrollToPlugin.min.js',
            'dce-ScrollTrigger-lib' => '/assets/lib/greensock/plugins/ScrollTrigger.min.js',
            // CANVAS
            'dce-bgcanvas-js' => '/assets/js/bg-canvas.js',
            'dce-rellaxjs-lib' => '/assets/lib/rellax/rellax.min.js',
            'dce-clipboard-js' => '/assets/lib/clipboard.js/clipboard.min.js',
            'dce-revealFx' => '/assets/lib/reveal/revealFx.js',
            'dce-scrollify' => '/assets/lib/scrollify/jquery.scrollify.js',
            'dce-inertia-scroll' => '/assets/lib/inertiaScroll/jquery-inertiaScroll.js',
            'dce-lax-lib' => '/assets/lib/lax/lax.min.js',
            'dce-google-maps-markerclusterer' => '/assets/lib/markerclustererplus/index.min.js',
            // MODULES
            'dce-google-modules_helpers' => '/assets/js/modules/google-api-module-helpers.js',
            'dce-google-maps' => ['path' => '/assets/js/dynamic-google-maps.js', 'deps' => ['dce-google-maps-markerclusterer', 'dce-google-maps-api']],
            'dce-dynamic-google-maps-directions' => ['path' => '/assets/js/dynamic-google-maps-directions.js', 'deps' => ['dce-google-maps-api']],
            'dce-stripe-js' => 'https://js.stripe.com/v3',
            'dce-popper' => '/assets/lib/popperjs/popper.min.js',
            'dce-tippy' => ['path' => '/assets/lib/tippy/tippy-bundle.umd.min.js', 'deps' => ['dce-popper']],
            'dce-jquery-color' => ['path' => '/assets/lib/jquery-color/jquery.color.min.js', 'deps' => ['jquery']],
            'dce-tinymce-js' => includes_url('js/tinymce/') . 'wp-tinymce.php',
            'dce-accordionjs' => '/assets/lib/accordionjs/accordion.min.js',
        ];
        if (!empty($google_maps_api)) {
            self::$vendor_js['dce-google-maps-api'] = ['deps' => ['dce-settings'], 'path' => "https://maps.googleapis.com/maps/api/js?key={$google_maps_api}&language={$locale2}&callback=initMap"];
        }
        if (!empty($paypal_client_id)) {
            self::$vendor_js['dce-paypal-sdk'] = "https://www.paypal.com/sdk/js?client-id={$paypal_client_id}&currency={$paypal_currency}";
        }
    }
    public static $scripts = array(
        'dce-pdf-button' => '/assets/js/pdf-button.js',
        'dce-visibility' => '/assets/js/visibility.js',
        'dce-form-address-autocomplete' => ['path' => '/assets/js/form-address-autocomplete.js', 'deps' => ['dce-google-maps-api']],
        'dce-dynamic-cookie' => '/assets/js/dynamic-cookie.js',
        'dce-osm-map' => ['path' => '/assets/js/osm-map.js', 'deps' => ['dce-leaflet']],
        'dce-dynamic-osm-map' => ['path' => '/assets/js/dynamic-osm-map.js', 'deps' => ['dce-leaflet', 'dce-leaflet-markercluster']],
        'dce-conditional-fields' => ['path' => '/assets/js/conditional-fields.js', 'deps' => ['dce-expressionlanguage']],
        'dce-pdf-viewer' => ['path' => '/assets/js/pdf-viewer.js', 'deps' => ['dce-pdf-js']],
        'dce-dynamic-countdown' => '/assets/js/dynamic-countdown.js',
        'dce-formatted-number' => ['path' => '/assets/js/formatted-number.js', 'deps' => ['jquery']],
        'dce-js-field' => ['path' => '/assets/js/js-field.js', 'deps' => ['jquery']],
        'dce-amount-field' => '/assets/js/amount-field.js',
        'dce-range' => '/assets/js/range.js',
        'dce-live-html' => '/assets/js/live-html.js',
        'dce-confirm-dialog' => ['path' => '/assets/js/confirm-dialog.js', 'deps' => ['dce-jquery-confirm', 'dce-live-html']],
        'dce-stripe' => ['path' => '/assets/js/stripe.js', 'deps' => ['dce-stripe-js']],
        'dce-paypal' => ['path' => '/assets/js/paypal.js', 'deps' => ['dce-paypal-sdk']],
        'dce-pdf-jsconv' => ['path' => '/assets/js/pdf-button-js-converter.js', 'deps' => ['dce-jspdf', 'dce-html2canvas']],
        'dce-discover-tokens' => ['path' => '/assets/js/discover-tokens.js', 'deps' => ['dce-clipboard-js', 'dce-tippy', 'dce-popper']],
        'dce-dynamic-select' => '/assets/js/dynamic-select.js',
        'dce-dynamic-charts' => '/assets/js/dynamic-charts.js',
        'dce-hidden-label' => '/assets/js/hidden-label.js',
        'dce-admin-js' => 'assets/js/admin.js',
        'dce-globalsettings-js' => ['path' => 'assets/js/global-settings.js', 'deps' => ['jquery']],
        'dce-script-editor-visibility' => ['path' => '/assets/js/dynamic-visibility.js', 'deps' => ['dce-script-editor']],
        'dce-acf' => '/assets/js/acf-fields.js',
        'dce-ajaxmodal' => '/assets/js/ajax-modal.js',
        'dce-cookie' => '/assets/js/cookie.js',
        'dce-settings' => ['path' => '/assets/js/settings.js', 'deps' => ['jquery']],
        'dce-fix-background-loop' => '/assets/js/fix-background-loop.js',
        'dce-animatetext' => '/assets/js/animated-text.js',
        'dce-modals' => '/assets/js/modals.js',
        'dce-acfgallery' => '/assets/js/acf-gallery.js',
        'dce-acfslider-js' => '/assets/js/acf-slider.js',
        'dce-parallax-js' => '/assets/js/parallax.js',
        'dce-360-slider' => '/assets/js/360-slider.js',
        'dce-views' => '/assets/js/views.js',
        'dce-before-after' => ['path' => '/assets/js/before-after.js', 'deps' => ['dce-imagesloaded']],
        'dce-tilt' => '/assets/js/tilt.js',
        'dce-dynamic-posts-old-version' => '/assets/js/dynamic-posts-old-version.js',
        'dce-icons-form' => '/assets/js/icons-form.js',
        // Dynamic Posts
        'dce-dynamicPosts-base' => '/assets/js/dynamic-posts-base.js',
        'dce-dynamicPosts-grid' => '/assets/js/dynamic-posts-skin-grid.js',
        'dce-dynamicPosts-accordion' => ['path' => '/assets/js/dynamic-posts-skin-accordion.js', 'deps' => ['dce-accordionjs']],
        'dce-dynamicPosts-grid-filters' => ['path' => '/assets/js/dynamic-posts-skin-grid-filters.js', 'deps' => ['dce-imagesloaded']],
        'dce-dynamicPosts-carousel' => '/assets/js/dynamic-posts-skin-carousel.js',
        'dce-dynamicPosts-timeline' => '/assets/js/dynamic-posts-skin-timeline.js',
        'dce-dynamicPosts-smoothscroll' => '/assets/js/dynamic-posts-skin-smoothscroll.js',
        'dce-dynamicPosts-gridtofullscreen3d' => '/assets/js/dynamic-posts-skin-grid-to-fullscreen-3d.js',
        'dce-dynamicPosts-crossroadsslideshow' => '/assets/js/dynamic-posts-skin-crossroads-slideshow.js',
        'dce-dynamicPosts-nextpost' => '/assets/js/dynamic-posts-skin-next-post.js',
        'dce-dynamicPosts-3d' => '/assets/js/dynamic-posts-skin-3d.js',
        'dce-acf-repeater-old' => '/assets/js/acf-repeater-old.js',
        'dce-acf-repeater' => '/assets/js/acf-repeater.js',
        'dce-content-js' => '/assets/js/content.js',
        'dce-dynamic_users' => '/assets/js/dynamic-users.js',
        'dce-acf_fields' => '/assets/js/acf-fields.js',
        'dce-modalwindow' => '/assets/js/fire-modal-window.js',
        'dce-nextPrev' => '/assets/js/next-prev.js',
        'dce-rellax' => '/assets/js/rellax.js',
        'dce-reveal' => '/assets/js/reveal.js',
        'dce-svgmorph' => '/assets/js/svg-morphing.js',
        'dce-svgdistortion' => '/assets/js/svg-distortion.js',
        'dce-svgfe' => '/assets/js/svg-filter-effects.js',
        'dce-svgblob' => '/assets/js/svg-blob.js',
        'dce-imagesdistortion-js' => ['path' => '/assets/js/distortion-image.js', 'deps' => ['dce-threejs-lib', 'dce-gsap-lib', 'dce-data-gui', 'dce-displacement-sketch', 'dce-distortion-lib']],
        'dce-scrolling' => '/assets/js/scrolling.js',
        'dce-animatedoffcanvasmenu-js' => '/assets/js/animated-off-canvas-menu.js',
        'dce-cursorTracker-js' => '/assets/js/cursor-tracker.js',
        'dce-advanced-video' => ['path' => '/assets/js/advanced-video.js', 'deps' => ['dce-plyr-js']],
        'dce-form-summary' => '/assets/js/multi-step-summary.js',
        'dce-signature' => '/assets/js/signature.js',
        'dce-tooltip' => '/assets/js/tooltip.js',
        'dce-add-to-favorites' => ['path' => '/assets/js/add-to-favorites.js', 'deps' => ['dce-cookie']],
    );
    public function __construct()
    {
        self::init_vendor_js();
        $this->init();
    }
    // This filter is needed if we want to change the attr of a registerd
    // script, for example by adding the module type.
    public function loader_tag_filter($tag, $handle, $src)
    {
        $modules_script = ['dce-live-html', 'dce-dynamic-google-maps-directions'];
        if (!\in_array($handle, $modules_script, \true)) {
            return $tag;
        }
        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        return $tag;
    }
    public function init()
    {
        add_filter('script_loader_tag', [$this, 'loader_tag_filter'], 10, 3);
        // Custom CSS and JS
        add_action('wp_head', [$this, 'dce_head']);
        add_action('wp_footer', [$this, 'dce_footer'], 100);
        // force jquery in head
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });
        // Admin Scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        // Dashboard
        add_action('admin_head', [$this, 'register_and_enqueue_dce_icons']);
        // Scripts
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        // Styles
        add_action('wp_enqueue_scripts', [$this, 'register_styles']);
        add_action('wp_enqueue_scripts', [$this, 'register_vendor_styles']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'dce_frontend_enqueue_style']);
        // Editor
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_enqueue']);
        add_action('elementor/preview/enqueue_styles', [$this, 'preview_enqueue']);
        // Frontend
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'frontend_enqueue']);
        // Global enqueue Script and Style
        add_action('wp_enqueue_scripts', [$this, 'dce_globals_stylescript']);
    }
    public static function dce_globals_stylescript()
    {
        // Fix for rare error: calling is_edit_mode on null
        try {
            $is_in_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        } catch (\Throwable $e) {
            $is_in_editor = \false;
        }
        $features = \DynamicContentForElementor\Plugin::instance()->features;
        // Global
        $smooth_enabled = $features->get_feature_info('gst_smooth_transition', 'status') === 'active';
        $theader_enabled = $features->get_feature_info('gst_tracker_header', 'status') === 'active';
        if ($smooth_enabled && (get_option('enable_smoothtransition') || $is_in_editor)) {
            // Global Settings CSS LIB
            wp_enqueue_style('animsition-base', DCE_URL . 'assets/lib/animsition/css/animsition.min.css', array(), DCE_VERSION);
            wp_enqueue_style('dce-animations');
            // Global Settings JS LIB
            wp_enqueue_script('dce-animsition-lib', DCE_URL . 'assets/lib/animsition/js/animsition.min.js', array('jquery'), DCE_VERSION);
        }
        if ($theader_enabled && (get_option('enable_trackerheader') || $is_in_editor)) {
            // Global Settings JS LIB
            wp_enqueue_script('dce-trackerheader-lib', DCE_URL . 'assets/lib/headroom/headroom.min.js', array('jquery'), DCE_VERSION);
        }
        if (($theader_enabled || $smooth_enabled) && (get_option('enable_trackerheader') || get_option('enable_smoothtransition') || $is_in_editor)) {
            wp_enqueue_script('dce-globalsettings-js');
            wp_enqueue_style('dce-globalsettings');
            $settings_controls = (new \DynamicContentForElementor\GlobalSettings())->dce_settings();
            wp_localize_script('dce-globalsettings-js', 'dceGlobalSettings', $settings_controls);
        }
    }
    public static function dce_frontend_enqueue_style()
    {
        wp_enqueue_style('dashicons');
    }
    public static function add_depends($element)
    {
        $w_styles = $element->get_style_depends();
        if (!empty($w_styles)) {
            self::$dce_styles = \array_merge(self::$dce_styles, $w_styles);
        }
        $w_scripts = $element->get_script_depends();
        if (!empty($w_scripts)) {
            self::$dce_scripts = \array_merge(self::$dce_scripts, $w_scripts);
        }
    }
    public function register_styles()
    {
        foreach (self::$styles as $name => $path) {
            $deps = [];
            // if the styles specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'];
                $path = $path['path'];
            }
            if ('dce-style' !== $name) {
                // Add dce-style dependency for all styles
                $deps[] = 'dce-style';
            }
            if ('http' !== \substr($path, 0, 4)) {
                if (!(WP_DEBUG || SCRIPT_DEBUG)) {
                    $path = \str_replace('.css', '.min.css', $path);
                }
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_style($name, $path, $deps, DCE_VERSION);
        }
    }
    public function register_vendor_styles()
    {
        foreach (self::$vendor_css as $name => $path) {
            // if the styles specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'];
                $path = $path['path'];
            } else {
                $deps = [];
            }
            if ('http' !== \substr($path, 0, 4)) {
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_style($name, $path, $deps, DCE_VERSION);
        }
    }
    public static function register_dce_scripts()
    {
        foreach (self::$scripts as $name => $path) {
            $deps = [];
            // if the script specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'];
                $path = $path['path'];
            }
            if ('dce-fix-background-loop' !== $name && 'dce-settings' !== $name) {
                // Add dce-fix-background-loop and dce-settings as dependencies for all scripts
                $deps[] = 'dce-fix-background-loop';
                $deps[] = 'dce-settings';
            }
            if ('http' !== \substr($path, 0, 4)) {
                if (!(WP_DEBUG || SCRIPT_DEBUG)) {
                    $path = \str_replace('.js', '.min.js', $path);
                }
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_script($name, $path, $deps, DCE_VERSION, \true);
        }
    }
    public static function register_vendor_scripts()
    {
        foreach (self::$vendor_js as $name => $js_info) {
            // if the script specifies dependencies or in_footer
            if (\is_array($js_info)) {
                $deps = $js_info['deps'] ?? [];
                $in_footer = $js_info['in_footer'] ?? \true;
                $path = $js_info['path'];
            } else {
                $deps = [];
                $in_footer = \true;
                $path = $js_info;
            }
            if (\substr($path, 0, 4) != 'http') {
                // Add DCE Path
                $path = plugins_url($path, DCE__FILE__);
                wp_register_script($name, $path, $deps, DCE_VERSION, $in_footer);
            } else {
                // version should stay null, paypal will complain about ver parameter otherwise:
                wp_register_script($name, $path, $deps, null, $in_footer);
            }
        }
    }
    public function register_scripts()
    {
        self::register_dce_scripts();
        self::register_vendor_scripts();
    }
    public function dce_head()
    {
        if (!\DynamicContentForElementor\Plugin::instance()->features->is_feature_active('wdg_panorama')) {
            // Exit early if Panorama widget is not active
            return;
        }
        $template_id = \DynamicContentForElementor\Elements::get_main_template_id();
        if ($template_id) {
            $widgets = get_post_meta($template_id, 'dce_widgets', \true);
            if (!empty($widgets) && !\Elementor\Plugin::$instance->editor->is_edit_mode() && isset($widgets['dyncontel-panorama'])) {
                echo '<script src="' . DCE_URL . 'assets/lib/aframe/aframe.min.js"></script>';
            }
        }
    }
    public function dce_footer()
    {
        if (!empty(\DynamicContentForElementor\Elements::$elements['widget']) && \DynamicContentForElementor\Plugin::instance()->features->is_feature_active('wdg_panorama')) {
            $template_id = \DynamicContentForElementor\Elements::get_main_template_id();
            if ($template_id) {
                update_post_meta($template_id, 'dce_widgets', \DynamicContentForElementor\Elements::$elements['widget']);
            }
        }
        self::add_footer_frontend_css();
        self::add_footer_frontend_js();
    }
    public static function dce_enqueue_script($handle, $js = '', $element_id = \false)
    {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            self::$dce_scripts[$handle] = $js;
            return '';
        } else {
            if (\is_array($js)) {
                $js = $js['script'];
            }
        }
        return $js;
    }
    public static function dce_enqueue_style($handle, $css = '', $element_id = \false)
    {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (empty(self::$dce_styles[$handle])) {
                self::$dce_styles[$handle] = $css;
            } else {
                self::$dce_styles[$handle] .= $css;
            }
            return '';
        }
        return $css;
    }
    public static function add_footer_frontend_js($inline = \true)
    {
        $js = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $script_keys = \array_keys(self::$scripts);
            $vendor_keys = \array_keys(self::$vendor_js);
            foreach (self::$dce_scripts as $skey => $ascript) {
                if (\is_numeric($skey) || \in_array($ascript, $script_keys) || \in_array($ascript, $vendor_keys)) {
                    unset(self::$dce_scripts[$skey]);
                }
            }
            if (!empty(self::$dce_scripts)) {
                if ($inline) {
                    foreach (self::$dce_scripts as $jkey => $jscript) {
                        $tmp = \explode('-', $jkey);
                        $element_id = \array_pop($tmp);
                        $element_type = \implode('-', $tmp);
                        $fnc = \str_replace('-', '_', $jkey);
                        $element_hook = $element_type . '.default';
                        if (\is_array($jscript)) {
                            $fnc = $jscript['type'] . '_' . $jscript['name'] . '_' . $jscript['id'];
                            if (!empty($jscript['sub'])) {
                                $fnc .= '_' . $jscript['sub'];
                            }
                            $js .= '<script id="dce-' . $jkey . '">
								( function( $ ) {
									var dce_' . $fnc . ' = function( $scope, $ ) {
										' . self::remove_script_wrapper($jscript['script']) . '
									};
									$( window ).on( \'elementor/frontend/init\', function() {
										elementorFrontend.hooks.addAction( \'frontend/element_ready/' . $jscript['name'] . '.default\', dce_' . $fnc . ' );
									} );
								} )( jQuery, window );
								</script>';
                        } else {
                            if (!empty(\strip_tags($jscript))) {
                                if (\strpos($jscript, '<script') !== \false) {
                                    if (\strpos($jscript, '<script>') !== \false) {
                                        $js .= \str_replace('<script', '<script id="dce-' . $jkey . '"', $jscript);
                                    } else {
                                        $js .= $jscript;
                                    }
                                } else {
                                    $js .= '<script id="' . $jkey . '">' . $jscript . '</script>';
                                }
                            }
                        }
                    }
                } else {
                    $post_id = \DynamicContentForElementor\Elements::get_main_template_id();
                    $upload_dir = wp_get_upload_dir();
                    $js_file = 'post-' . $post_id . '.js';
                    $js_dir = $upload_dir['basedir'] . '/elementor/js/';
                    $js_baseurl = $upload_dir['baseurl'] . '/elementor/js/';
                    $js_path = $js_dir . $js_file;
                    if (\is_file($js_path)) {
                        $file_modified_date = \filemtime($js_path);
                        if (get_the_modified_date('U', $post_id) > $file_modified_date) {
                            \unlink($js_path);
                        }
                    }
                    if (!\is_file($js_path)) {
                        // create folder (if not exist)
                        if (!\is_dir($js_dir)) {
                            \mkdir($js_dir, 0755, \true);
                        }
                        // create the file
                        $js_file_content = '';
                        foreach (self::$dce_scripts as $jkey => $jscript) {
                            if (\strpos($jscript, '<script') !== \false) {
                                $jscript = \str_replace('<script>', '', $jscript);
                                $jscript = \str_replace('</script>', '', $jscript);
                            }
                            if (!empty($jscript)) {
                                $js_file_content .= '// ' . $jkey . \PHP_EOL . $jscript;
                            }
                        }
                        if (!empty($js_file_content)) {
                            \file_put_contents($js_path, $js_file_content);
                        }
                    }
                    if (\is_file($js_path)) {
                        $js_url = $js_baseurl . $js_file;
                        echo '<script type="text/javascript" src="' . $js_url . '"></script>';
                    }
                }
            }
        }
        echo $js;
    }
    public static function add_footer_frontend_css($inline = \true)
    {
        $css = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $style_keys = \array_keys(self::$styles);
            $vendor_keys = \array_keys(self::$vendor_css);
            foreach (self::$dce_styles as $skey => $astyle) {
                if (\is_numeric($skey) || \in_array($astyle, $style_keys) || \in_array($astyle, $vendor_keys)) {
                    unset(self::$dce_styles[$skey]);
                }
            }
            if (!empty(self::$dce_styles)) {
                if ($inline) {
                    foreach (self::$dce_styles as $ckey => $cstyle) {
                        if ($cstyle) {
                            $css .= '<style id="dce-' . $ckey . '">' . self::remove_style_wrapper($cstyle) . '</style>';
                        }
                    }
                }
            }
        }
        echo $css;
    }
    public static function remove_script_wrapper($script)
    {
        $script = \str_replace('<script>', '', $script);
        $script = \str_replace('</script>', '', $script);
        $script = \str_replace('jQuery(', 'setTimeout(', $script);
        return $script;
    }
    public static function remove_style_wrapper($style)
    {
        $style = \str_replace('<style>', '', $style);
        $style = \str_replace('</style>', '', $style);
        return $style;
    }
    /**
     * Enqueue Admin Scripts
     *
     * @return void
     */
    public function enqueue_admin_scripts()
    {
        // select2
        wp_enqueue_style('dce-select2', plugins_url('assets/lib/select2/select2.min.css', DCE__FILE__), [], DCE_VERSION);
        wp_enqueue_script('dce-select2', plugins_url('assets/lib/select2/select2.full.min.js', DCE__FILE__), array('jquery'), DCE_VERSION, \true);
        // Enqueue Admin Script
        wp_enqueue_script('dce-admin-js', plugins_url('assets/js/admin.js', DCE__FILE__), [], DCE_VERSION, \true);
    }
    public function register_and_enqueue_dce_icons()
    {
        // Register styles
        wp_register_style('dce-style-icons', plugins_url('/assets/css/dce-icon.css', DCE__FILE__), [], DCE_VERSION);
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
    }
    /**
     * The following scripts and styles are registered here because when
     * loading the elementor editor the wp actions used for the registrations
     * are not run.
     */
    public function editor_enqueue()
    {
        // Register styles
        wp_register_style('dce-style-icons', plugins_url('/assets/css/dce-icon.css', DCE__FILE__), [], DCE_VERSION);
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
        // Register styles
        wp_register_style('dce-style-editor', plugins_url('/assets/css/editor.css', DCE__FILE__), [], DCE_VERSION);
        wp_enqueue_style('dce-style-editor');
        // JS for Editor
        wp_register_script('dce-script-editor', plugins_url('/assets/js/editor.js', DCE__FILE__), [], DCE_VERSION, \true);
        wp_enqueue_script('dce-script-editor');
        // Labels on Dynamic Collection
        wp_localize_script('dce-script-editor', 'posts_v2_item_label_localization', ['item_title' => '<i class="fa fa-font" aria-hidden="true"></i> ' . __('Title', 'dynamic-content-for-elementor'), 'item_image' => '<i class="eicon-featured-image" aria-hidden="true"></i> ' . __('Featured Image', 'dynamic-content-for-elementor'), 'item_date' => '<i class="fa fa-calendar" aria-hidden="true"></i> ' . __('Date', 'dynamic-content-for-elementor'), 'item_termstaxonomy' => '<i class="eicon-tags" aria-hidden="true"></i> ' . __('Terms', 'dynamic-content-for-elementor'), 'item_content' => '<i class="fa fa-align-left" aria-hidden="true"></i> ' . __('Content', 'dynamic-content-for-elementor'), 'item_author' => '<i class="eicon-user-circle-o" aria-hidden="true"></i> ' . __('Author', 'dynamic-content-for-elementor'), 'item_custommeta' => '<i class="eicon-custom" aria-hidden="true"></i> ' . __('Custom Meta Field', 'dynamic-content-for-elementor'), 'item_jetengine' => '<i class="icon-dce-jetengine" aria-hidden="true"></i> ' . __('JetEngine Field', 'dynamic-content-for-elementor'), 'item_metabox' => '<i class="icon-dce-metabox" aria-hidden="true"></i> ' . __('Meta Box Field', 'dynamic-content-for-elementor'), 'item_readmore' => '<i class="eicon-button" aria-hidden="true"></i> ' . __('Read More', 'dynamic-content-for-elementor'), 'item_posttype' => '<i class="eicon-post-info" aria-hidden="true"></i> ' . __('Post Type', 'dynamic-content-for-elementor'), 'item_productprice' => '<i class="eicon-product-price" aria-hidden="true"></i> ' . __('Product Price', 'dynamic-content-for-elementor'), 'item_sku' => '<i class="eicon-product-info" aria-hidden="true"></i> ' . __('Product SKU', 'dynamic-content-for-elementor'), 'item_addtocart' => '<i class="eicon-product-add-to-cart" aria-hidden="true"></i> ' . __('Add to Cart', 'dynamic-content-for-elementor')]);
        // Features by Collection 'dynamic-posts'
        wp_localize_script('dce-script-editor', 'dce_features_collection_dynamic_posts', $this->features_collection_dynamic_posts());
    }
    public function frontend_enqueue()
    {
        // Features by Collection 'dynamic-posts'
        wp_localize_script('dce-dynamicPosts-base', 'dce_features_collection_dynamic_posts', $this->features_collection_dynamic_posts());
    }
    /**
     * Features Collection Dynamic Posts
     *
     * @return array<string>
     */
    public function features_collection_dynamic_posts()
    {
        return \DynamicContentForElementor\Plugin::instance()->features->get_feature_info_by_array(\DynamicContentForElementor\Plugin::instance()->features->filter_by_collection('dynamic-posts'), 'name');
    }
    /**
     * Enqueue preview styles
     *
     * @since 1.0.3
     *
     * @access public
     */
    public function preview_enqueue()
    {
        // Enqueue DCE Elementor Style
        wp_enqueue_style('dce-preview');
    }
    /**
     * Enqueue DCE Icons
     *
     * @return void
     */
    public static function enqueue_dce_icons()
    {
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
    }
    public static function wp_print_styles($handle = \false, $print = \true)
    {
        $styles = '';
        if ($handle) {
            if (!empty(self::$styles[$handle])) {
                $styles .= '<link rel="stylesheet" id="' . $handle . '" href="' . DCE_URL . self::$styles[$handle] . '" type="text/css" media="all" />';
            }
        }
        if ($print) {
            echo $styles;
        }
        return $styles;
    }
}
