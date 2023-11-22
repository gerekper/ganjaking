<?php
/**
 * Plugin Name: WP Post Modules for Elementor
 * Author:      SaurabhSharma
 * Author URI: 	http://codecanyon.net/user/saurabhsharma
 * Version:     2.4.0
 * Text Domain: wppm-el
 * Domain Path: /languages/
 * Description: Create WordPress Post Modules in different styles for Blog, Magazine and Newspaper websites.
 * Elementor tested up to: 3.17.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Post_Modules_El' ) ) {
    /**
     * Main WP_Post_Modules_El Class
     *
     */
    final class WP_Post_Modules_El {

        function __construct() {

            // Ajax actions for tabs
            add_action( 'wp_ajax_wppm_tabs_action', array( &$this,'wppm_tabs_action_callback' ) );
            add_action( 'wp_ajax_nopriv_wppm_tabs_action', array( &$this,'wppm_tabs_action_callback' ) );

            // Ajax actions for nav links
            add_action( 'wp_ajax_wppm_ajaxnav_action', array( &$this,'wppm_ajaxnav_action_callback' ) );
            add_action( 'wp_ajax_nopriv_wppm_ajaxnav_action', array( &$this,'wppm_ajaxnav_action_callback' ) );
        } // construct


        private static $instance;

        /**
         * Main WP_Post_Modules_El Instance
         *
         */
        public static function instance() {

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Post_Modules_El ) ) {

                self::$instance = new WP_Post_Modules_El;

                self::$instance->wppm_includes();

                self::$instance->wppm_hooks();

            }
            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Not allowed.', 'wppm-el' ), '1.0' );
        }

        /**
         * Disable unserializing of the class
         *
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Unserializing forbidden.', 'wppm-el' ), '1.0' );
        }

        // Return ajax tab content
        public function wppm_tabs_action_callback() {
            $content = isset( $_POST ) && $_POST['wppm_tab_content'] ? $_POST['wppm_tab_content'] : '';
            echo wppm_el_clean( stripslashes( $content ) );
            wp_die();
        }

        // Return ajax next/prev content
        public function wppm_ajaxnav_action_callback() {
            require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/widget-wppm.php';
            $content = isset( $_POST ) && $_POST['wppm_ajaxnav_content'] ? $_POST['wppm_ajaxnav_content'] : '';
            $wppm_shortcode_obj = new WP_Post_Modules_El\Widgets\Widget_WP_Post_Modules_El;
            echo $wppm_shortcode_obj->wppm_shortcode( $content );
            wp_die();
        }


        /**
         * Load Plugin Text Domain
         *
         */
        public function load_plugin_textdomain() {

            $lang_dir = apply_filters( 'wppm_el_lang_dir', trailingslashit( plugin_dir_path(__FILE__) . 'languages' ) );

            // Native WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'wppm-el' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'wppm-el', $locale );

            // Paths to current locale file
            $mofile_local = $lang_dir . $mofile;

            if ( file_exists( $mofile_local ) ) {
                // wp-content/plugins/wp-post-modules-el/languages/ folder
                load_textdomain( 'wppm-el', $mofile_local );
            }
            else {
                // Load default language files
                load_plugin_textdomain( 'wppm-el', false, $lang_dir );
            }

            return false;
        }

        public function wppm_includes() {
            $plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
            require_once( $plugin_dir . 'includes/BFI_Thumb.php' );
            require_once( $plugin_dir . 'includes/wppm.functions.php' );
        }

        /**
         * Setup the default hooks and actions
         */
        private function wppm_hooks() {

            add_action( 'plugins_loaded', array( self::$instance, 'load_plugin_textdomain' ) );

            add_action( 'elementor/widgets/register', array( self::$instance, 'include_widgets') );

            add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts'), 10 );

            add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ), 10 );

            add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_frontend_styles' ), 10 );

        }

        public function include_widgets( $widgets_manager ) {
            require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/widget-wppm.php';
            $widgets_manager->register( new \WP_Post_Modules_El\Widgets\Widget_WP_Post_Modules_El() );
        }

    /**
     * Load Frontend Scripts
     *
     */
    public function register_frontend_scripts() {

            // JavaScript files
            wp_register_script( 'wppm-el-plugin-functions', plugin_dir_url( __FILE__ ) . 'assets/js/wppm-el.frontend.min.js', array( 'jquery' ), '', true );
            wp_register_script( 'wppm-jq-easing', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.easing.min.js', array( 'jquery' ), '', true );
            wp_register_script( 'wppm-jq-owl-carousel', plugin_dir_url( __FILE__ ) . 'assets/js/owl.carousel.min.js', array( 'jquery' ), '', true );
            wp_register_script( 'wppm-jq-marquee', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.marquee.min.js', array( 'jquery' ), '', true );

            // Localize text strings and variables used in wppm.plugin.js file
            $localization = array(
                'ajax_err'      => __( '<a href="%url%">The content</a> could not be loaded.' , 'wppm-el' ),
                'ajax_url'      => admin_url( 'admin-ajax.php' )
            );

            wp_localize_script( 'wppm-el-plugin-functions', 'wppm_el_localize', $localization );
        }

        /**
         * Load Frontend Styles
         *
         */
        public function register_frontend_styles() {

                wp_register_style( 'wppm-el-plugin-css', plugin_dir_url( __FILE__ ) . 'assets/css/wppm-el.frontend.css', array(), null );
                wp_register_style( 'wppm-el-fontawesome-css', plugin_dir_url( __FILE__ ) . 'assets/css/all.min.css', array(), null );

                // RTL CSS
                if ( is_rtl() ) {
                    wp_register_style( 'wppm-el-plugin-rtl', plugin_dir_url( __FILE__ ) . 'assets/css/wppm-el.rtl.frontend.css', array(), null );
                }

        }

        // Load Frontend Styles
        public function enqueue_frontend_styles() {

            wp_enqueue_style( 'wppm-el-plugin-css' );
            wp_enqueue_style( 'wppm-el-fontawesome-css' );

            if ( is_rtl() ) {
                wp_enqueue_style( 'wppm-el-plugin-rtl' );
            }

        }

        // Posts Shortcode
        public function wppm_shortcode( $opts = array() ) {
            extract( shortcode_atts( array(
                // WP Query Specific
                'author_name'           => null,
                'author__in'            => null,
                'cat'                   => null,
                'category_name'         => null,
                'tag'                   => null,
                'tag_id'                => null,
                'taxonomy'              => 'category',
                'relation'              => 'OR',
                'operator'              => 'IN',
                'terms'                 => '',
                'p'                     => null,
                'name'                  => null,
                'page_id'               => null,
                'pagename'              => null,
                'post__in'              => null,
                'post__not_in'          => null,
                'post_type'             => 'post',
                'post_status'           => 'publish',
                'num'                   => 6,
                'offset'                => 0,
                'ignore_sticky_posts'   => false,
                'order'                 => 'DESC',
                'orderby'               => 'date',
                'year'                  => null,
                'monthnum'              => null,
                'w'                     => null,
                'day'                   => null,
                'meta_key'              => null,
                'meta_value'            => null,
                'meta_value_num'        => null,
                'meta_compare'          => '=',
                's'                     => null,

                // Date parameters
                'year'                  => '',
                'month'                 => '',
                'week'                  => '',
                'day'                   => '',
                'before'                => '', // Date before
                'after'                 => '', // Date after
                'date_query'            => null,

                'single_term_filter'     => false,
                'author_archive_filter' => false,
                'taxonomy_optional '    => '',
                'no_posts_text'         => __( 'No posts found.', 'wppm_el' ),
                'hide_current_post'     => false,
                'blog_id'               => null,
                'template'              => 'grid',
                'masonry'               => false,
                'sub_type'              => 's1',
                'sub_type_grid'         => 's1',
                'list_split'            => '25',
                'grid_split'            => '33',
                'list_sep'              => 'content-border',
                'content_left'          => false,
                'counter'               => false,
                'mobile_wide'           => false,
                'circle_img'            => false,
                'columns'               => '3',
                'list_columns'          => '1',
                'excerpt_length'        => '10',
                'h_length'              => '',
                'readmore'              => false,
                'readmore_text'         => __( 'Read more', 'wppm-el' ),
                'cat_limit'             => 3,
                'show_cats'             => 'true',
                'show_author'           => 'true',
                'show_date'             => 'true',
                'show_excerpt'          => 'true',
                'show_comments'         => 'true',
                'show_views'            => 'true',
                'show_reviews'          => 'true',
                'show_thumbnail'        => 'true',
                'post_format_icon'      => 'true',
                'show_avatar'           => false,
                'use_native_thumbs'     => false,
                'enable_captions'       => false,
                'imgsize'               => 'full',
                'imgwidth'              => '',
                'imgheight'             => '',
                'imgcrop'               => 'true',
                'bfi'                   => 'true',
                'imgquality'            => '80',
                'imggrayscale'          => false,
                'date_format'           => '',
                'htag'                  => 'h2',
                'ptag'                  => 'p',
                'content_pos'           => 'bl',
                'show_overlay'          => 'always',
                'image_effect'          => 'none',

                // Slider params
                'slider_type'           => 'grid',
                'enable_slider'         => '',
                'items'                 => 3,
                'items_tablet'          => 2,
                'items_mobile'          => 1,
                'loop'                  => 'false',
                'speed'                 => '300',
                'slide_margin'          => 24,
                'slide_margin_tablet'   => 20,
                'slide_margin_mobile'   => 0,
                'autoplay'              => 'true',
                'timeout'               => '5000',
                'autoheight'            => 'false',
                'nav'                   => 'true',
                'dots'                  => 'false',
                'animatein'             => false,
                'animateout'            => false,
                'stagepadding'          => 0,
                'data_props'            => false,

                // Schema Params
                'enable_schema'         => false,
                'container_type'        => 'BlogPosting',
                'container_prop'        => 'blogPost',
                'heading_prop'          => 'headline mainEntityOfPage',
                'excerpt_prop'          => 'text',
                'datecreated_prop'      => 'datePublished',
                'datemodified_prop'     => 'dateModified',
                'publisher_type'        => 'Organization',
                'publisher_prop'        => 'publisher',
                'publisher_name'        => esc_attr( get_bloginfo( 'name' ) ),
                'publisher_logo'        => plugin_dir_url( __FILE__ ) . 'assets/images/wppm.svg',
                'authorbox_type'        => 'Person',
                'authorbox_prop'        => 'author',
                'authorname_prop'       => 'name',
                'authoravatar_prop'     => 'image',
                'category_prop'         => 'about',
                'commentcount_prop'     => 'commentCount',
                'commenturl_prop'       => 'discussionUrl',
                'ratingbox_type'        => 'Rating',
                'rating_prop'           => 'ratingValue',

                // Misc
                'ext_link'              => false,
                'ajaxnav'               => false,
                'nav_status'            => false,
                'nav_status_text'       => '%current% of %total%',
                'ajaxloadmore'          => false,
                'loadmore_text'         => __( 'Load more', 'wppm-el' ),
                'sharing'               => false,
                'share_btns'            => '',
                'share_style'           => 'popup',
                'show_embed'            => false,
                'custom_meta'           => false,
                'meta_format'           => '',
                'meta_pos'              => 1,
                'psource'               => 'excerpt',
                'meta_box'              => '',
                'cust_field_key'        => '',
                'allowed_tags'          => 'p,br,a,em,i,strong,b',
                'content_filter'        => false,
                'ad_list'               => '',
                'ad_offset'             => '3',
                'new_tab'               => false,

                // News Ticker
                'title_length'      => '10',
                'ticker_label'      => __( 'Breaking News', 'wppm-el' ),
                'duration'          => 15000,
                'ticker_clr'        => '',
                'ticker_bg'         => ''
            ), $opts ) );

            // Filter terms for single posts
            if ( is_single() && ! is_page() ) {
                if ( $single_term_filter ) {
                    global $post;
                    $taxonomy = isset($taxonomy_optional) && '' != $taxonomy_optional ? explode(',', $taxonomy_optional) : ( isset( $taxonomy ) && is_array( $taxonomy ) && ! empty( $taxonomy ) ? $taxonomy : array( 'category' ) );

                    if (isset($taxonomy) && is_array($taxonomy)) {
                        foreach ($taxonomy as $tax) {
                            $post_terms = get_the_terms($post->id, $tax);
                            if (isset($post_terms) && is_array($post_terms)) {
                                foreach ($post_terms as $t) {
                                    $terms[] = $t->slug;
                                }
                            }
                        }
                    }
                }

                if ( $hide_current_post ) {
                    if ( '' != $post__not_in ) {
                        $post__not_in .= ',' . get_the_id();
                    } else {
                        $post__not_in = get_the_id();
                    }
                    $atts['post__not_in'] = $post__not_in;
                }
            }

            // Sanitize WP Query args
            $author__in                 = $author__in ? explode( ',', $author__in ) : null;
            $post__in                   = $post__in ? explode( ',', $post__in ) : null;
            $post__not_in               = $post__not_in ? explode( ',', $post__not_in ) : null;
            $terms                      = isset ( $terms ) ? $terms : null;
            $post_type                  = isset ( $post_type ) ? $post_type : null;
            $taxonomy                   = isset ( $taxonomy ) ? $taxonomy : null;
            $tax_query                  = null;

            if ( $taxonomy && $terms ) {
                $tax_query = array( 'relation' => $relation );

                if ( is_array( $taxonomy ) ) {
                    foreach( $taxonomy as $tax ) {
                        $tax_query[] = array(
                            'taxonomy'  => $tax,
                            'field'     => 'slug',
                            'terms'     => $terms,
                            'operator'  => $operator // Allowed values AND, IN, NOT IN
                        );
                    }
                }
            }


            // Date Params
            if ( $year || $month || $week || $day || $before || $after ) {
                $date_arr = array();
                if ( $year ) {
                    $date_arr['year'] = $year;
                }

                if ( $month ) {
                    $date_arr['month'] = $month;
                }

                if ( $week ) {
                    $date_arr['week'] = $week;
                }

                if ( $day ) {
                    $date_arr['day'] = $day;
                }

                if ( $before ) {
                    $date_arr['before'] = $before;
                }

                if ( $after ) {
                    $date_arr['after'] = $after;
                }

                $date_query = array( $date_arr  );
            }

            // Author archive filtering
            if ( $author_archive_filter && is_author() ) {
                $author = get_queried_object();
                $author_name = $author->user_nicename;
            } else {
                $author_name = null;
            }

            // Allowed args in WP Query
            $custom_args = array(
                'author_name'           => $author_name,
                'author__in'            => $author__in,
                'cat'                   => $cat,
                'category_name'         => $category_name,
                'tag'                   => $tag,
                'tag_id'                => $tag_id,
                'tax_query'             => $tax_query,
                'p'                     => $p,
                'name'                  => $name,
                'page_id'               => $page_id,
                'pagename'              => $pagename,
                'post__in'              => $post__in,
                'post__not_in'          => $post__not_in,
                'post_type'             => $post_type,
                'post_status'           => $post_status,
                'posts_per_page'        => $num,
                'offset'                => $offset,
                'ignore_sticky_posts'   => $ignore_sticky_posts,
                'order'                 => $order,
                'orderby'               => $orderby,
                'meta_key'              => $meta_key,
                'meta_value'            => $meta_value,
                'meta_value_num'        => $meta_value_num,
                's'                     => $s,
                'date_query'            => $date_query
            );

            $new_args = array();

            // Set args which are provided by user
            foreach ( $custom_args as $key => $value ) {
                if ( isset( $value ) )
                    $new_args[ $key ] = $value;
            }

            // Switch to blog id if multisite
            if ( is_multisite() ) {
                switch_to_blog( $blog_id );
            }

            $custom_query = new WP_Query( $new_args );

            // Set global count for ajax post container ID
            if ( isset( $GLOBALS['wppm_ajax_container_count'] ) ) {
                $GLOBALS['wppm_ajax_container_count']++;
            }
            else {
                $GLOBALS['wppm_ajax_container_count'] = 0;
            }


            // Start the loop
            if ( $custom_query->have_posts() ) :

                // Limit image dimensions between 4px to 4000px
                $imgwidth = (int)$imgwidth < 4 ? '' : $imgwidth;
                $imgwidth = (int)$imgwidth > 4000 ? 4000 : $imgwidth;
                $imgheight = (int)$imgheight < 4 ? '' : $imgheight;
                $imgheight = (int)$imgheight > 4000 ? 4000 : $imgheight;

                // Publisher logo
                $publisher_logo = wp_get_attachment_image_src ( $publisher_logo );
                if ( isset ( $publisher_logo ) && is_array( $publisher_logo ) ) {
                    $publisher_logo = $publisher_logo[0];
                }

                // Set default template
                if ( '' == $template ) {
                    $template = 'grid';
                }

                // Set default heading and p tags
                $htag = $htag == '' ? 'h2' : $htag;
                $ptag = $ptag == '' ? 'p' : $ptag;

                // Set sub template value
                if ( ( $template != 'tile' && $template != 'slider' ) && isset( ${ 'sub_type_' . $template } ) ) {
                    $sub_type = ${ 'sub_type_' . $template };
                }

                if ( $allowed_tags ) {
                    $tags_arr = explode( ',', $allowed_tags );
                    if ( isset( $tags_arr ) && is_array( $tags_arr ) ) {
                        $allowed_tags = '';
                        foreach( $tags_arr as $tag ) {
                            $allowed_tags .= '<' . $tag . '>';
                        }
                    }
                }

                ob_start();

                $template_path = apply_filters( 'wppm_sc_template_path',  '/includes/wppm-templates/' );

                // Load slider template
                if ( $enable_slider ) {
                    if ( locate_template( $template_path . 'slider.php' ) ) {
                        require( get_stylesheet_directory() . $template_path . 'slider.php' );
                    }
                    else {
                        require( dirname( __FILE__ ) . $template_path . 'slider.php' );
                    }
                }

                else {  // Load other templates
                    if ( locate_template( $template_path . esc_attr( $template ) . '.php' ) ) {
                        require( get_stylesheet_directory() . $template_path . esc_attr( $template ) . '.php' );
                    }

                    else {
                        require( dirname( __FILE__ ) . $template_path . esc_attr( $template ) . '.php' );
                    }
                }

                $out = ob_get_contents();

                ob_end_clean();

                wp_reset_query();
                wp_reset_postdata();

                if ( is_multisite() ) {
                    restore_current_blog();
                }
                return $out;
            else :
                return esc_html( $no_posts_text );
            endif;
        }
    }

} // If not class exists


/**
 * Generate Instance
 */
function wppm_el() {
    return WP_Post_Modules_El::instance();
}

wppm_el();