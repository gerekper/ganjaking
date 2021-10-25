<?php
    namespace MasterAddons\Inc\Classes;
    use MasterAddons\Inc\Helper\Master_Addons_Helper;

	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 8/6/20
	 */

	class Jltma_Reset_Themes_Conflicts{

        private static $instance = null;

        public function __construct() {
            add_action( 'wp_enqueue_scripts', array( $this, 'jltma_reset_theme_conflicts' ) );
            // add_action( 'wp_print_scripts', 'jltma_reset_theme_dequeue_script', 100 );
        }

        function jltma_reset_theme_dequeue_script() {
            // wp_dequeue_style( 'scriptname' );
            // wp_dequeue_script( 'scriptname' );
        }

        public function jltma_reset_theme_conflicts(){

            $jltma_custom_css = "";

            // gets the current theme
            $theme = wp_get_theme();

            //Twenty Twelve
            if ( 'Twenty Twelve' == $theme->name || 'Twenty Twelve' == $theme->parent_theme ) {
                // Twenty Twelve is the current active theme or parent theme
            }

            //Airi Theme Reset Styles
            if ( 'Airi' == $theme->name || 'Airi' == $theme->parent_theme ) {
                $jltma_custom_css .= "
                @media (max-width: 1199px){
                    .header-mobile-menu .mobile-menu-toggle{ outline: none; }
                    .main-navigation { position: fixed !important; max-width: 100% !important; }
                }";

                wp_add_inline_style('airi-bootstrap', $jltma_custom_css);
            }

        }

        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self;
            }
            return self::$instance;
        }
    }

    Jltma_Reset_Themes_Conflicts::get_instance();