<?php
/**
 * Functions which enhance the theme by hooking into WordPress
*/


/*************************************************
## ADMIN NOTICES
*************************************************/


function agro_theme_activation_notice()
{
    global $current_user;

    $user_id = $current_user->ID;

    if (!get_user_meta($user_id, 'agro_theme_activation_notice')) {
        ?>

		<div class="updated notice">
            <p>
               <?php
                   echo sprintf(
                   esc_html__('If you need help about demodata installation, please read docs and %s', 'agro'),
                   '<a target="_blank" href="' . esc_url('https://ninetheme.com/contact/') . '">
                   ' . esc_html__('Contact', 'agro') . '
                   </a> ' . esc_html__(' - ', 'agro') . ' <a href="?agro-ignore-notice">' . esc_html__('Dismiss', 'agro') . '</a>');
               ?>
            </p>
	    </div>

<?php
    }
}


add_action('admin_notices', 'agro_theme_activation_notice');

function agro_theme_activation_notice_ignore()
{
    global $current_user;

    $user_id = $current_user->ID;

    if (isset($_GET['agro-ignore-notice'])) {
        add_user_meta($user_id, 'agro_theme_activation_notice', 'true', true);
    }
}
add_action('admin_init', 'agro_theme_activation_notice_ignore');

/*************************************************
## DATA CONTROL FROM THEME-OPTIONS PANEL
*************************************************/


function agro_settings($opt_id, $def_value='')
{
    global $agro;
    $defval = '' != $def_value ? $def_value : false;
    $opt_id = trim($opt_id);
    $opt = isset($agro[$opt_id]) ? $agro[$opt_id] : $defval;

    return $opt;
}

/*************************************************
## SIDEBAR CONTROL
*************************************************/


function agro_sidebar_control($layout)
{
    global $agro;

    $layout = trim($layout);

    if (! is_active_sidebar('sidebar-1')) {
        $layout = 'col-md-12';
    } else {
        $layout = isset($agro[$layout]) && $agro[$layout] == 'full-width' ? 'col-md-12' : 'col-md-8';
    }

    return $layout;
}

/*************************************************
## SANITIZE MODIFIED VC-ELEMENTS OUTPUT
*************************************************/
/**
 * Adds bootstrap column class for sidebar layout.
 * @param string $layout
 * @return string
 */
if (!function_exists('agro_vc_sanitize_data')) {
    function agro_vc_sanitize_data($html_data)
    {
        return $html_data;
    }
}


function agro_clean_shortcodes($content){
$array = array (
    '<p>[' => '[',
    ']</p>' => ']',
    ']<br />' => ']'
);
$content = strtr($content, $array);
return $content;
}
add_filter('the_content', 'agro_clean_shortcodes');


/*************************************************
## VC COMPOSER PAGE CSS
*************************************************/
/*
*	get vc composer custom css from by page id
*	and add css to head by wp_head hook
*/
if( ! function_exists('agro_vc_inject_shortcode_css') )  {
    function agro_vc_inject_shortcode_css( $id ){
        $shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
        if ( ! empty( $shortcodes_custom_css ) ) {
            $shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
            echo '<style type="text/css" data-type="nt-shortcodes-custom-css-page-'.$id.'">';
            echo esc_attr( $shortcodes_custom_css );
            echo '</style>';
        }
    }
    add_action('wp_head', 'agro_vc_inject_shortcode_css');
}


/*************************************************
## CUSTOM BODY CLASS
*************************************************/
function agro_body_theme_classes($classes)
{
    $theme_name = wp_get_theme();
    $theme_version = 'nt-version-' . wp_get_theme()->get('Version');
    $theme_page = (! is_page_template('custom-page.php')) ? 'nt-body' : 'nt-body-not-custom-page';
    $product_related = class_exists('woocommerce') && is_product() ? agro_settings('shop_single_related_type', 'related-grid') : '';

    $classes[] = $theme_name;
    $classes[] = $theme_version;
    $classes[] = $theme_page;
    $classes[] = $product_related;

    return $classes;
}
add_filter('body_class', 'agro_body_theme_classes');


/*************************************************
## CUSTOM POST CLASS
*************************************************/
function agro_post_theme_class($classes)
{
    $class =  ' nt-post-class';

    $classes[] =  $class;

    return $classes;
}
add_filter('post_class', 'agro_post_theme_class');


/*************************************************
## HEADER SEARCH FORM
*************************************************/
if (! function_exists('agro_header_search_form')) {
    function agro_header_search_form()
    {
        $form = '<form role="search" method="get" id="header-searchform" class="searchform c-header-1-searcher-form" action="'.esc_url(home_url('/')).'" >
    		<input type="text" value="' . get_search_query() . '"  name="s" id="hs" class="c-header-1-searcher-field form-search sb-search-input" placeholder="'.esc_attr__('Start Typing &amp; Press Enter ...', 'agro').'">
    		<button type="button" class="c-header-1-searcher-close"><span class="ion-ios-close-empty"></span></button>
    	</form>';
        return $form;
    }
}
add_filter('get_search_form', 'agro_header_search_form');
## HEADER SEARCH FORM POPUP
if (! function_exists('agro_header_search_form_popup')) {
    function agro_header_search_form_popup()
    {
        $form = '<div class="header_search">
                    <div class="container">
                        <form class="header_search_form" role="search" method="get" id="header-widget-searchform" action="' . esc_url(home_url('/')) . '" >
                            <input class="header_search_input" type="text" value="' . get_search_query() . '" placeholder="'. esc_html__('Search...', 'agro') .'" name="s" id="hws">
                            <button class="header_search_button" id="headersearchsubmit" type="submit"><span class="fas fa-search is-search"></span></button>
                            <button class="header_search_close" type="button"><span class="fas fa-times is_close"></span></button>
                        </form>
                    </div>
                </div>';
        return $form;
    }
    add_filter('get_search_form', 'agro_header_search_form_popup');
}

/*************************************************
## THEME SEARCH FORM
*************************************************/


function agro_custom_search_form($form)
{
    $form = '<div class="nt-sidebar-inner-search center-block">
		<form class="nt-sidebar-inner-search-form searchform form--horizontal" role="search" method="get" id="widget-searchform"  action="' . esc_url(home_url('/')) . '" >
            <div class="input-wrp"><input class="nt-sidebar-inner-search-field textfield" type="text" value="' . get_search_query() . '" placeholder="'. esc_attr__('Search for...', 'agro') .'" name="s" id="ws" ></div>
			<button class="custom-btn custom-btn--tiny custom-btn--style-1" id="searchsubmit" type="submit">'. esc_html__('Find', 'agro') .'</button>
		</form>
	</div>';

    return $form;
}
add_filter('get_search_form', 'agro_custom_search_form');

/*************************************************
## THEME wOO SEARCH FORM
*************************************************/


function agro_woo_custom_search_form($form)
{
    $form = '<div class="nt-sidebar-inner-search center-block">
		<form class="nt-sidebar-inner-search-form searchform form--horizontal" role="search" method="get" id="widget-searchform"  action="' . esc_url(home_url('/')) . '" >
            <div class="input-wrp"><input class="nt-sidebar-inner-search-field textfield" type="text" value="' . get_search_query() . '" placeholder="'. esc_attr__('Search for...', 'agro') .'" name="s" id="ws" ></div>
			<button class="custom-btn custom-btn--tiny custom-btn--style-1" id="searchsubmit" type="submit">'. esc_html__('Find', 'agro') .'</button>
		</form>
	</div>';

    return $form;
}
add_filter('get_product_search_form', 'agro_woo_custom_search_form');


/*************************************************
## EXCERPT FILTER
*************************************************/


function agro_custom_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'agro_custom_excerpt_more');


/*************************************************
## EXCERPT LIMIT
*************************************************/


function agro_excerpt_limit($limit)
{
    $excerpt = explode(' ', get_the_excerpt(), $limit);

    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt) . '...';
    } else {
        $excerpt = implode(" ", $excerpt);
    }

    $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);

    return $excerpt;
}

/*************************************************
## DEFAULT CATEGORIES WIDGET
*************************************************/


function agro_add_span_cat_count($links)
{
    $links = str_replace('</a> (', '</a> <span class="widget-list-span">', $links);
    $links = str_replace(')', '</span>', $links);

    return $links;
}
//add_filter('wp_list_categories', 'agro_add_span_cat_count');


/*************************************************
## DEFAULT ARCHIVES WIDGET
*************************************************/


function agro_add_span_arc_count($links)
{
    $links = str_replace('</a>&nbsp;(', '</a> <span class="widget-list-span">', $links);
    $links = str_replace(')', '</span>', $links);

    return $links;
}
add_filter('get_archives_link', 'agro_add_span_arc_count');


/*************************************************
## PAGINATION CUSTOMIZATION
*************************************************/


function agro_sanitize_pagination($content)
{

    // remove role attribute
    $content = str_replace('role="navigation"', '', $content);

    // remove h2 tag
    $content = preg_replace('#<h2.*?>(.*?)<\/h2>#si', '', $content);

    return $content;
}
add_action('navigation_markup_template', 'agro_sanitize_pagination');


/*************************************************
## CUSTOM ARCHIVE TITLES
*************************************************/


function agro_archive_title()
{
    $title = '';
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_year()) {
        $title = get_the_date(_x('Y', 'yearly archives date format', 'agro'));
    } elseif (is_month()) {
        $title = get_the_date(_x('F Y', 'monthly archives date format', 'agro'));
    } elseif (is_day()) {
        $title = get_the_date(_x('F j, Y', 'daily archives date format', 'agro'));
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    } else {
        $title = esc_html__('Archives', 'agro');
    }

    return $title;
}
add_filter('get_the_archive_title', 'agro_archive_title');


/*************************************************
## POST THUMBNAIL CONTROL
*************************************************/


function agro_can_show_post_thumbnail()
{
    return apply_filters('agro_can_show_post_thumbnail', ! post_password_required() && ! is_attachment() && has_post_thumbnail());
}


/*************************************************
## CHECKS TO SEE IF WE'RE ON THE HOMEPAGE OR NOT.
*************************************************/


function agro_is_frontpage()
{
    return (is_front_page() && ! is_home());
}


/*************************************************
## CONVERT HEX TO RGB
*************************************************/


 if (! function_exists('agro_hex2rgb')) {
     function agro_hex2rgb($hex)
     {
         $hex = str_replace("#", "", $hex);

         if (strlen($hex) == 3) {
             $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
             $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
             $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
         } else {
             $r = hexdec(substr($hex, 0, 2));
             $g = hexdec(substr($hex, 2, 2));
             $b = hexdec(substr($hex, 4, 2));
         }
         $rgb = array($r, $g, $b);

         return $rgb; // returns an array with the rgb values
     }
 }


/**********************************
## THEME ALLOWED HTML TAG
/**********************************/


if (! function_exists('agro_allowed_html')) {
    function agro_allowed_html()
    {
        $allowed_tags = array(
        'a' => array(
            'class' => array(),
            'href'  => array(),
            'rel'   => array(),
            'title' => array(),
            'target' => array(),
        ),
        'abbr' => array(
            'title' => array(),
        ),
        'iframe' => array(
            'src' => array(),
        ),
        'b' => array(),
        'br' => array(),
        'blockquote' => array(
            'cite'  => array(),
        ),
        'cite' => array(
            'title' => array(),
        ),
        'code' => array(),
        'del' => array(
            'datetime' => array(),
            'title' => array(),
        ),
        'dd' => array(),
        'div' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'i' => array(
            'class'  => array(),
        ),
        'img' => array(
            'alt'    => array(),
            'class'  => array(),
            'height' => array(),
            'src'    => array(),
            'width'  => array(),
        ),
        'li' => array(
            'class' => array(),
        ),
        'ol' => array(
            'class' => array(),
        ),
        'p' => array(
            'class' => array(),
        ),
        'q' => array(
            'cite' => array(),
            'title' => array(),
        ),
        'span' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'strike' => array(),
        'strong' => array(),
        'ul' => array(
            'class' => array(),
        ),
    );

        return $allowed_tags;
    }
}

//   Redux Theme optionlara ekleneckler







/*************************************************
## CLASS FOR SAVED TEMPLATES SELECTION
*************************************************/
if ( !class_exists( 'Agro_Saved_Templates' ) ) {

    class Agro_Saved_Templates
    {

        /**
        * A reference to an instance of this class.
        */
        private static $instance;


        /**
        * Returns an instance of this class.
        */
        public static function get_instance()
        {
            if (self::$instance == null) {
                self::$instance = new self;
            }
            return self::$instance;
        }


        private function __construct()
        {

            add_action( 'wp_enqueue_scripts', [ $this,'print_css' ] );

        }


        /*
        ## WPbackery saved templates list
        # @return array
        */
        public static function get_vc_templates()
        {
            $options = array();
            $saved_templates = get_option( 'wpb_js_templates' );
            if ( !empty($saved_templates) ) {
                foreach ( $saved_templates as $template => $key ) {
                    $options[$template] = $key['name'];
                }
            }
            return $options;
        }


        /*
        ## WPbackery saved templates content
        # @return string
        */
        public static function vc_print_saved_template( $template_id )
        {
            if ( !empty( $template_id ) ) {
                $saved_templates = get_option( 'wpb_js_templates' );
                $content = trim( $saved_templates[ $template_id ][ 'template' ] );
                $content = str_replace( '\"', '"', $content );
                $pattern = get_shortcode_regex();
                $content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_shortcode', $content );

                echo do_shortcode( $content );

            } else {

                esc_html_e( 'No template exist.Please save your content from WPbackery Row settings.', 'agro' );

            }
        }


        /*
        ## Parse WPbackery saved template content css
        */
        public static function parse_shortcodes_template_css( $content )
        {
            $css = '';
            if ( ! preg_match( '/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content ) ) {
                return $css;
            }
            //WPBMap::addAllMappedShortcodes();
            preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );
            foreach ( $shortcodes[2] as $index => $tag ) {
                $shortcode = class_exists( 'WPBMap' ) ? WPBMap::getShortCode( $tag ) : '';
                $attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
                if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
                    foreach ( $shortcode['params'] as $param ) {
                        if ( isset( $param['type'] ) && 'css_editor' === $param['type'] && isset( $attr_array[ $param['param_name'] ] ) ) {
                            $css .= $attr_array[ $param['param_name'] ];
                        }
                    }
                }
            }
            foreach ( $shortcodes[5] as $shortcode_content ) {
                $css .= self::parse_shortcodes_template_css( $shortcode_content );
            }

            return $css;
        }


        /*
        ## Add WPbackery saved template content css to head
        */
        public function print_css( $template_id )
        {
            $saved_templates = get_option( 'wpb_js_templates' );

            $template_id = agro_settings('blog_after_content_saved_templates' );

            if ( $template_id ) {
                $content = trim( $saved_templates[ $template_id ][ 'template' ] );

                $content = str_replace( '\"', '"', $content );
                $pattern = get_shortcode_regex();
                $content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_shortcode', $content );

                $theCSS = self::parse_shortcodes_template_css( $content );

                wp_add_inline_style( 'agro-custom-style', $theCSS );
            }
        }
    }
    Agro_Saved_Templates::get_instance();
}

add_action('admin_notices', 'agro_notice_for_activation');
if (!function_exists('agro_notice_for_activation')) {
    function agro_notice_for_activation() {
        global $pagenow;

        if ( !get_option('envato_purchase_code_23274955') ) {

            echo '<div class="notice notice-warning">
                <p>' . sprintf(
                esc_html__( 'Enter your Envato Purchase Code to receive Agro Theme and plugin updates  %s', 'agro' ),
                '<a href="' . admin_url('admin.php?page=merlin&step=license') . '">' . esc_html__( 'Enter Purchase Code', 'agro' ) . '</a>') . '</p>
            </div>';
        }

    }
}


if ( !get_option('envato_purchase_code_23274955') ) {
    add_filter('auto_update_theme', '__return_false');
}

add_action('upgrader_process_complete', 'agro_upgrade_function', 10, 2);
if ( !function_exists('agro_upgrade_function') ) {
    function agro_upgrade_function($upgrader_object, $options) {
        $purchase_code =  get_option('envato_purchase_code_23274955');

        if (($options['action'] == 'update' && $options['type'] == 'theme') && !$purchase_code) {
            wp_redirect(admin_url('admin.php?page=merlin&step=license'));
        }
    }
}

if ( !function_exists( 'agro_is_theme_registered') ) {
    function agro_is_theme_registered() {
        $purchase_code = get_option('envato_purchase_code_23274955');
        $registered_by_purchase_code =  !empty($purchase_code);

        // Purchase code entered correctly.
        if ($registered_by_purchase_code) {
            return true;
        }
    }
}

function agro_deactivate_envato_plugin() {
    if (  function_exists( 'envato_market' ) && !get_option('envato_purchase_code_23274955') ) {
        deactivate_plugins('envato-market/envato-market.php');
    }
}
add_action( 'admin_init', 'agro_deactivate_envato_plugin' );
