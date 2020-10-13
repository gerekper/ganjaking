<?php
namespace WeDevs\PM_Pro\Core\Rewrites;

use WeDevs\PM\Core\WP\Enqueue_Scripts;
use WeDevs\PM\Core\WP\Register_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Pro_Enqueue_Scripts;
use WeDevs\PM_Pro\Core\WP\Register_Scripts as Pro_Register_Scripts;

/**
 * pm Dashboard Rewrites Class
 */
class Rewrite {

    /**
     * This plugin's instance.
     *
     * @var CoBlocks_Accordion_IE_Support
     */
    private static $instance;

    /**
     * Registers the plugin.
     */
    public static function instance() {
         if ( !self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * pm_Dashboard_Rewrites constructor.
     */
    function __construct() {
        add_action( 'init', [ $this, 'add_rewrite_rules' ] );
        add_filter( 'query_vars', [ $this, 'register_query_var' ] );
        add_action( 'template_redirect', [ $this, 'rewrite_templates' ] );
        add_action( 'pm_after_save_settings', [ $this, 'flush_permalink' ]);

        //add_filter( 'print_admin_styles', '__return_true' );

    }

    /**
     * Add the required rewrite rules
     *
     * @return void
     */
    function add_rewrite_rules() {
        $dashboard_slug = pm_frontend_slug();
        $query_var      = pm_register_query_var();

        add_rewrite_rule( '^' . $dashboard_slug . '/?$', "index.php?{$query_var}={$dashboard_slug}", 'top' );
    }

    /**
     * Register our query vars
     *
     * @param  array $vars
     *
     * @return array
     */
    function register_query_var( $vars ) {
        $vars[] = pm_register_query_var();

        return $vars;
    }

    /**
     * Load our template on our rewrite rule
     *
     * @return void
     */
    public function rewrite_templates() {


        if ( pm_frontend_slug() == get_query_var( pm_register_query_var() ) ) {

            //check if user is logged in otherwise redirect to login page
            if ( ! is_user_logged_in() ) {
                auth_redirect();

                exit();
                return;
            }

            echo pm_root_element();

            $this->wp_admin_styles();
            $this->scripts();

            _wp_footer_scripts();

            exit;
        }


    }

    public function wp_admin_styles() {
        global $wp_styles;

        $allow_styles = [
            'list-tables',
            'common',
            'edit',
            'forms',
            'buttons'
        ];

        //pmpr($wp_styles->registered); die();
        foreach ( $wp_styles->registered as $handle => $dependency ) {

            if ( in_array( $handle, $allow_styles ) ) {
                wp_enqueue_style( $dependency->handle );
            }
        }
    }

    public function scripts() {

        wp_enqueue_script(
            'pm-hooks',
            pm_config('frontend.assets_url') . 'vendor/wp-hooks/pm-hooks.js',
            '',
            false,
            false
        );

        wp_enqueue_style(
            'pm-rewrite',
            pm_pro_config('define.url') . 'views/assets/css/rewrite.css',
            false,
            false,
            'all'
        );

        //Register pro scripts
        Pro_Register_Scripts::scripts();
        Pro_Register_Scripts::styles();


        //Register free scripts
        Register_Scripts::scripts();
        Register_Scripts::styles();

        do_action( "pm_load_shortcode_script" );

        wp_enqueue_style( 'pm-frontend-style' );
        wp_enqueue_script('pm-frontend-scripts');

        //pro scripts
        Pro_Enqueue_Scripts::scripts();
        Pro_Enqueue_Scripts::styles();


        // free scripts
        Enqueue_Scripts::scripts();
        Enqueue_Scripts::styles();
    }

    /**
     * Get the slug of pm dashboard page
     *
     * @since 1.0.0
     * @return string
     */
    protected function get_frontend_slug() {
        return pm_frontend_slug();
    }

    /**
     * Get pm dashboard page url
     *
     * @since 1.0.0
     * @return string
     */
    protected function get_frontend_url() {
        return pm_frontend_url();
    }

    /**
     * Flush permalink
     *
     * @since 1.0.0
     */
    public function flush_permalink(){
        flush_rewrite_rules();
    }
}
