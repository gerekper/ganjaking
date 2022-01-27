<?php

/**
 *
 * @package WordPress
 * @subpackage agro
 * @since agro 1.0
 *
**/


/*************************************************
## GOOGLE FONTS
*************************************************/

if (! function_exists('agro_fonts_url')) {
    function agro_fonts_url()
    {
        $fonts_url = '';

        $opensans = _x('on', 'Open+Sans font: on or off', 'agro');
        $raleway = _x('on', 'Raleway font: on or off', 'agro');

        if ('off' !== $opensans or 'off' !== $raleway) {
            $font_families = array();

            if ('off' !== $opensans) {
                $font_families[] = 'Open Sans:300,400,500,600,700,800';
            }

            if ('off' !== $raleway) {
                $font_families[] = 'Raleway:100,400,400i,500,500i,700,700i,900';
            }

            $query_args = array(
                'family' => urlencode(implode('|', $font_families)),
                'subset' => urlencode('latin,latin-ext'),
            );

            $fonts_url = add_query_arg($query_args, "//fonts.googleapis.com/css");
        }

        return $fonts_url;
    }
}

/*************************************************
## STYLES AND SCRIPTS
*************************************************/


function agro_theme_scripts()
{

    $rtl = is_rtl() ? '-rtl' : '';
    // ## CSS
    // font families
    wp_register_style('ionicon', get_template_directory_uri() . '/css/ionicon-stylesheet.css', false, '1.0');
    wp_register_style('flaticon', get_template_directory_uri() . '/css/flaticon.css', false, '1.0');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/fontawesome.min.css', false, '1.0');
    wp_register_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.min.css', false, '1.0');
    // theme inner pages files
    wp_enqueue_style('agro-general-style', get_template_directory_uri() . '/css/framework-style'.$rtl.'.css', false, '1.0');
    wp_enqueue_style('nice-select', get_template_directory_uri() . '/css/nice-select.css', false, '1.0');

    // Agro csss
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap'.$rtl.'.min.css', false, '1.0');
    wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/js/magnific/magnific-popup.css', false, '1.0');
    wp_enqueue_style('magnific', get_template_directory_uri() . '/css/aos.css', false, '1.0');
    wp_enqueue_style('fancybox', get_template_directory_uri() . '/css/jquery.fancybox.css', false, '1.0');
    wp_enqueue_style('jarallax', get_template_directory_uri() . '/css/jarallax.css', false, '1.0');
    wp_enqueue_style('slick', get_template_directory_uri() . '/css/slick'.$rtl.'.css', false, '1.0');
    wp_enqueue_style('vegas-slider', get_template_directory_uri() . '/css/vegas-slider'.$rtl.'.css', false, '1.0');
    wp_enqueue_style('agro-critical', get_template_directory_uri() . '/css/critical'.$rtl.'.css', false, '1.0');
    wp_enqueue_style('agro-main', get_template_directory_uri() . '/css/style'.$rtl.'.css', false, '1.0');
    wp_enqueue_style('agro-update', get_template_directory_uri() . '/css/update'.$rtl.'.css', false, '1.0');


    // upload Google Webfonts
    wp_enqueue_style('agro-fonts', agro_fonts_url(), array(), '1.0');

    if( 'masonry' == agro_settings( 'blog_index_type', 'list' ) ) {
        wp_enqueue_script('imagesloaded-pkgd', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), '1.0', false);
    }

    // ## JS
    // theme inner page files
    wp_enqueue_script('smooth-scroll-polyfills', get_template_directory_uri() . '/js/smooth-scroll.polyfills.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('jarallax', get_template_directory_uri() . '/js/jarallax.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('agro-js-settings', get_template_directory_uri() . '/js/framework-settings.js', array('jquery'), '1.0', true);
    wp_enqueue_script('nice-select', get_template_directory_uri() . '/js/jquery.nice-select.min.js', array('jquery'), '1.0', true);
    wp_register_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/js/magnific/magnific-popup.min.js', array('jquery'), '1.0', true);

    // Agro js
    wp_enqueue_script('device', get_template_directory_uri() . '/js/device.min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('lazyload', get_template_directory_uri() . '/js/lazyload.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('aos', get_template_directory_uri() . '/js/aos.js', array('jquery'), '1.0', true);
    wp_enqueue_script('countTo', get_template_directory_uri() . '/js/jquery.countTo.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('fancybox', get_template_directory_uri() . '/js/jquery.fancybox.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('vegas-slider', get_template_directory_uri() . '/js/vegas-slider.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('agro-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0', true);
    wp_enqueue_script('agro-custom-editor', get_template_directory_uri() . '/js/custom-editor.js', array('jquery'), '1.0', true);


    // browser hacks
    wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.min.js', array('jquery'), '1,0', false);
    wp_script_add_data('modernizr', 'conditional', 'lt IE 9');
    wp_enqueue_script('respond', get_template_directory_uri() . '/js/respond.min.js', array('jquery'), '1.0', false);
    wp_script_add_data('respond', 'conditional', 'lt IE 9');
    wp_enqueue_script('html5shiv', get_template_directory_uri() . '/js/html5shiv.min.js', array('jquery'), '1.0', false);
    wp_script_add_data('html5shiv', 'conditional', 'lt IE 9');

    // comment form reply
    if (is_singular()) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'agro_theme_scripts');


/*************************************************
## ADMIN STYLE AND SCRIPTS
*************************************************/


function agro_admin_scripts()
{

    // Update CSS within in Admin
    wp_enqueue_script('agro-custom-admin', get_template_directory_uri() . '/js/framework-admin.js');

}
add_action('admin_enqueue_scripts', 'agro_admin_scripts');


/*************************************************
## THEME OPTION & METABOXES & SHORTCODES
*************************************************/

// Check Visual Composer Activation
if (function_exists('vc_set_as_theme')) {
    include get_template_directory() . '/vc_templates/vc-custom-param-settings.php';
    vc_set_as_theme($disable_updater = false);
    vc_is_updater_disabled();
}

// Check Metabox Plugin Activation
if (! function_exists('rwmb_meta')) {
    function rwmb_meta($key, $args = '', $post_id = null)
    {
        return false;
    }
}

// Theme post and page meta plugin for customization and more features
include get_template_directory() . '/inc/metaboxes.php';

require_once get_parent_theme_file_path( '/inc/merlin/admin-menu.php' );
// Template-functions
include get_template_directory() . '/inc/template-functions.php';

// Theme parts
include get_template_directory() . '/inc/template-parts/menu.php';
include get_template_directory() . '/inc/template-parts/post-formats.php';
include get_template_directory() . '/inc/template-parts/paginations.php';
include get_template_directory() . '/inc/template-parts/comment-parts.php';
include get_template_directory() . '/inc/template-parts/small-parts.php';
include get_template_directory() . '/inc/template-parts/header-parts.php';
include get_template_directory() . '/inc/template-parts/footer-parts.php';
include get_template_directory() . '/inc/template-parts/page-hero.php';
include get_template_directory() . '/inc/template-parts/breadcrumbs.php';

// Theme dynamic css setting file
include get_template_directory() . '/inc/custom-style.php';

// TGM plugin activation
include get_template_directory() . '/inc/class-tgm-plugin-activation.php';

// Redux theme options panel
include get_template_directory() . '/inc/theme-options/options.php';


// WooCommerce init
if (class_exists('woocommerce')) {
    include get_template_directory() . '/woocommerce/init.php';
}

/*************************************************
## THEME SETUP
*************************************************/


if (! isset($content_width)) {
    $content_width = 960;
}

function agro_theme_setup()
{


    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
    * Enable support for Post Thumbnails on posts and pages.
    *
    * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
    */
    add_theme_support('post-thumbnails');

    // custom thumbnail image sizes
    add_image_size('agro-120-hard', 120, 120, true); // Hard Crop Mode
    add_image_size('agro-120-soft', 120, 120); // Soft Crop Mode
    add_image_size('agro-240-hard', 240, 240, true); // Hard Crop Mode
    add_image_size('agro-240-soft', 240, 240); // Soft Crop Mode
    add_image_size('agro-360-hard', 360, 360, true); // Hard Crop Mode
    add_image_size('agro-360-soft', 360, 360); // Soft Crop Mode
    add_image_size('agro-480-hard', 480, 480, true); // Hard Crop Mode
    add_image_size('agro-480-soft', 480, 480); // Soft Crop Mode
    add_image_size('agro-820-hard', 820, 820, true); // Hard Crop Mode
    add_image_size('agro-820-soft', 820, 820); // Soft Crop Mode
    add_image_size('agro-1040-hard', 1040, 1040, true); // Hard Crop Mode
    add_image_size('agro-1040-soft', 1040, 1040); // Soft Crop Mode
    add_image_size('agro-1040-hard', 1040, 1040, true); // Hard Crop Mode
    add_image_size('agro-1040-soft', 1040, 1040); // Soft Crop Mode

    // theme supports
    add_theme_support('title-tag');
    add_theme_support('custom-background');
    add_theme_support('custom-header');
    add_theme_support('html5', array( 'search-form' ));

    if ( class_exists( 'woocommerce' ) ) {
    		add_theme_support( 'woocommerce' );
    	}

    // Make theme available for translation
    // Translations can be filed in the /languages/ directory
    load_theme_textdomain('agro', get_template_directory() . '/languages');

    register_nav_menus(array(
        'header_menu_1'  => esc_html__('Header Menu 1', 'agro'),
        'footer_menu_1'  => esc_html__('Footer Menu 1', 'agro'),
    ));

}
add_action('after_setup_theme', 'agro_theme_setup');


/*************************************************
## REGISTER SIDEBARS
*************************************************/

function agro_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Blog Sidebar', 'agro'),
        'id' => 'sidebar-1',
        'description' => esc_html__('These widgets for the Blog page.', 'agro'),
        'before_widget' => '<div class="nt-sidebar-inner-widget  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="nt-sidebar-inner-widget-title widget-title">',
        'after_title' => '</h4>'
    ));
    register_sidebar(array(
        'name' => esc_html__('Blog Single Sidebar', 'agro'),
        'id' => 'agro-single-sidebar',
        'description' => esc_html__('These widgets for the Blog single page.', 'agro'),
        'before_widget' => '<div class="nt-sidebar-inner-widget  %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="nt-sidebar-inner-widget-title widget-title">',
        'after_title' => '</h4>'
    ));
    // custom register footer widgets from theme optipons panel
    if (! empty(agro_settings('custom_widgets'))) {
        $sidebars = agro_settings('custom_widgets');
        foreach ($sidebars as $id => $column) {
            register_sidebar(array(
                'name' => sprintf(esc_html__('Custom Footer Widget %s', 'agro'), $id+1),
                'description' => sprintf('%s <b>%s</b> %s <b>%s</b>',
                esc_html__('This widget has custom column.You can change the column width from here:', 'agro'),
                esc_html__('Theme Options > Footer > Widget Area Customize.', 'agro'),
                esc_html__('Column width:', 'agro'),
                $column),
                'id' => 'custom-footer-widget-'.($id + 1),
                'before_widget' => '<div class="nt-sidebar-inner-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="nt-sidebar-inner-widget-title">',
                'after_title' => '</h5>'
            ));
        }
    }
}
add_action('widgets_init', 'agro_widgets_init');


/*************************************************
## INCLUDE THE TGM_PLUGIN_ACTIVATION CLASS.
*************************************************/


function agro_register_required_plugins()
{
    $plugins = array(
        array(
            'name' => esc_html__('Contact Form 7', "agro"),
            'slug' => 'contact-form-7',
            'required' => true
        ),
        array(
            'name' => esc_html__('Meta Box', "agro"),
            'slug' => 'meta-box',
            'required' => true
        ),
        array(
            'name' => esc_html__('WooCommerce', "agro"),
            'slug' => 'woocommerce',
            'required' => true
        ),
        array(
            'name' => esc_html__('Theme Options Panel', "agro"),
            'slug' => 'redux-framework',
            'required' => true
        ),
        array(
            'name' => esc_html__('Metabox Tabs', "agro"),
            'slug' => 'meta-box-tabs',
            'source' => 'https://ninetheme.com/documentation/plugins/meta-box-tabs.zip',
            'required' => true,
            'version' => '1.1.5',
        ),
        array(
            'name' => esc_html__('Metabox Show/Hide', "agro"),
            'slug' => 'meta-box-show-hide',
            'source' => 'https://ninetheme.com/documentation/plugins/meta-box-show-hide.zip',
            'required' => true,
            'version' => '1.3',
        ),
        array(
            'name' => esc_html__('Envato Auto Update Theme', "agro"),
            'slug' => 'envato-market',
            'source' => 'https://ninetheme.com/documentation/plugins/envato-market.zip',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Page Builder', "agro"),
            'slug' => 'js_composer',
            'source' => 'https://ninetheme.com/documentation/plugins/js_composer.zip',
            'required' => true,
        ),
        array(
            'name' => esc_html__('Revolution Slider', "agro"),
            'slug' => 'revslider',
            'source' => 'https://ninetheme.com/documentation/plugins/revslider.zip',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Wp Agro Shortcodes', "agro"),
            'slug' => 'nt-agro-shortcodes',
            'source' => trailingslashit( get_template_directory_uri() ) . 'plugins/nt-agro-shortcodes.zip',
            'required' => true,
            'version' => '2.2.0'
        )
    );

    $config = array(
        'id' => 'tgmpa',
        'default_path' => '',
        'menu' => 'tgmpa-install-plugins',
        'parent_slug' => apply_filters( 'ninetheme_parent_slug', 'themes.php' ),
        'has_notices' => true,
        'dismissable' => true,
        'dismiss_msg' => '',
        'is_automatic' => true,
        'message' => '',
    );

    tgmpa($plugins, $config);
}
add_action('tgmpa_register', 'agro_register_required_plugins');



/*************************************************
## THEME SETUP WIZARD
https://github.com/richtabor/MerlinWP
*************************************************/

require_once get_parent_theme_file_path( '/inc/merlin/class-merlin.php' );
require_once get_parent_theme_file_path( '/inc/demo-wizard-config.php' );

function merlin_local_import_files() {
    return array(
        array(
            'import_file_name'          => esc_html__( 'Demo Import', 'agro' ),
            'local_import_file'         => get_parent_theme_file_path( 'inc/merlin/demodata/data.xml' ),
            'local_import_widget_file'  => get_parent_theme_file_path( 'inc/merlin/demodata/widgets.wie' ),
            'import_redux'              => array(
                array(
                    'file_url' => trailingslashit( get_template_directory_uri() ) .  'inc/merlin/demodata/redux.json',
                    'option_name' => 'agro'
                )
            )
        )
    );
}
add_filter( 'merlin_import_files', 'merlin_local_import_files' );

function agro_disable_size_images_during_import() {
    add_filter( 'intermediate_image_sizes_advanced', function( $sizes ){
        unset( $sizes['agro-120-hard' ] );
        unset( $sizes['agro-120-soft'] );
        unset( $sizes['agro-240-hard'] );
        unset( $sizes['agro-240-soft'] );
        unset( $sizes['agro-360-hard'] );
        unset( $sizes['agro-360-soft'] );
        unset( $sizes['agro-480-hard'] );
        unset( $sizes['agro-480-soft'] );
        unset( $sizes['agro-820-hard'] );
        unset( $sizes['agro-820-soft'] );
        unset( $sizes['agro-1040-hard'] );
        unset( $sizes['agro-1040-soft'] );
        unset( $sizes['agro-1040-hard'] );
        unset( $sizes['agro-1040-soft'] );
        unset( $sizes['thumbnail'] );
        unset( $sizes['medium'] );
        unset( $sizes['medium_large'] );
        unset( $sizes['large'] );
        unset( $sizes['1536x1536'] );
        unset( $sizes['2048x2048'] );
        unset( $sizes['woocommerce_thumbnail'] );
        unset( $sizes['woocommerce_single'] );
        unset( $sizes['woocommerce_gallery_thumbnail'] );
        unset( $sizes['shop_catalog'] );
        unset( $sizes['shop_single'] );
        unset( $sizes['shop_thumbnail'] );
        return $sizes;
    });
}
add_action( 'import_start', 'agro_disable_size_images_during_import');

/**
* Execute custom code after the whole import has finished.
*/
function agro_merlin_after_import_setup() {
    // Assign menus to their locations.
    $primary = get_term_by( 'name', 'primary', 'nav_menu' );
    $footer = get_term_by( 'name', 'footer', 'nav_menu' );

    set_theme_mod(
        'nav_menu_locations', array(
            'header_menu_1' => $primary->term_id,
            'footer_menu_1' => $footer->term_id,
        )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home 1' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

}
add_action( 'merlin_after_all_import', 'agro_merlin_after_import_setup' );


function agro_vc_is_inline() {
    return function_exists( 'vc_is_inline' ) && vc_is_inline() ? true : false;
}
if(agro_vc_is_inline() == false){
    add_action('init', 'do_output_buffer'); function do_output_buffer() { ob_start(); }
}
//woocoomerce
add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );

/*************************************************
## CUSTOM POST CLASS
*************************************************/

if (! function_exists('agro_post_theme_class')) {
    function agro_post_theme_class($classes)
    {

        if ( is_single() ) {
            $classes[] =  has_blocks() ? 'nt-single-has-block' : '';
        }

        return $classes;
    }
    add_filter('post_class', 'agro_post_theme_class');
}
