<?php

/**
 * Extension-Boilerplate
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * gt3 Header Builder - Modified For ReduxFramework
 *
 * @package     gt3 Header Builder - Extension for building header
 * @author      gt3themes
 * @version     1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_extension_gt3_header_builder' ) ) {


    /**
     * Main ReduxFramework custom_field extension class
     *
     * @since       3.1.6
     */
    class ReduxFramework_extension_gt3_header_builder extends ReduxFramework {

        // Protected vars
        protected $parent;
        public $extension_url;
        public $extension_dir;
        public static $theInstance;

        /**
        * Class Constructor. Defines the args for the extions class
        *
        * @since       1.0.0
        * @access      public
        * @param       array $sections Panel sections.
        * @param       array $args Class constructor arguments.
        * @param       array $extra_tabs Extra panel tabs.
        * @return      void
        */
        public function __construct( $parent ) {

            $this->parent = $parent;
            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
            }
            $this->field_name = 'gt3_header_builder';

            self::$theInstance = $this;

            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/class/'.$this->field_name, array( &$this, 'overload_field_path' ) ); // Adds the local field

            //Adds Importer section to panel
            $this->add_gt3_header_section();

        }

        public function getInstance() {
            return self::$theInstance;
        }

        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path($field) {
            return dirname(__FILE__).'/'.$this->field_name.'/field_'.$this->field_name.'.php';
        }

        public function getMenuList(){
            $menus = wp_get_nav_menus();
            $menu_list = array();

            foreach ($menus as $menu => $menu_obj) {
                $menu_list[$menu_obj->slug] = $menu_obj->name;
            }
            return $menu_list;
        }

        public function add_gt3_header_section() {
            // Checks to see if section was set in config of redux.
            for ( $n = 0; $n <= count( $this->parent->sections ); $n++ ) {
                if ( isset( $this->parent->sections[$n]['id'] ) && $this->parent->sections[$n]['id'] == 'gt3_header_builder_section' ) {
                    return;
                }
            }

            if (function_exists('gt3_header_presets')) {
                $presets = gt3_header_presets();
                $header_preset_1 = $presets['header_preset_1'];
            }

            $options = array(
                array(
                    'id'   => 'gt3_header_builder_id',
                    'type' => 'gt3_header_builder',
                    'full_width' => true,
                    'options' => array(
                        'all_item' => array(
                            'title' => 'All Item',
                            'layout' => 'all',
                            'content' => array(
                                'search' => array(
                                    'title' => 'Search',
                                    'has_settings' => false,
                                ),
                                'login' => array(
                                    'title' => 'Login',
                                    'has_settings' => false,
                                ),
                                'cart' => array(
                                    'title' => 'Cart',
                                    'has_settings' => false,
                                ),
                                'burger_sidebar' => array(
                                    'title' => 'Burger Sidebar',
                                    'has_settings' => true,
                                ),
                                'text1' => array(
                                    'title' => 'Text/HTML 1',
                                    'has_settings' => true,
                                ),
                                'text2' => array(
                                    'title' => 'Text/HTML 2',
                                    'has_settings' => true,
                                ),

                                'text3' => array(
                                    'title' => 'Text/HTML 3',
                                    'has_settings' => true,
                                ),
                                'text4' => array(
                                    'title' => 'Text/HTML 4',
                                    'has_settings' => true,
                                ),

                                'text5' => array(
                                    'title' => 'Text/HTML 5',
                                    'has_settings' => true,
                                ),
                                'text6' => array(
                                    'title' => 'Text/HTML 6',
                                    'has_settings' => true,
                                ),
                                'delimiter1' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter2' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter3' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter4' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter5' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter6' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'empty_space1' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space2' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space3' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space4' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space5' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                            ),
                        ),
                        'top_left' => array(
                            'title' => 'Top Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'top_center' => array(
                            'title' => 'Top Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'top_right' => array(
                            'title' => 'Top Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'middle_left' => array(
                            'title' => 'Middle Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds clear-item',
                            'content' => array(
                                'logo' => array(
                                    'title' => 'Logo',
                                    'has_settings' => true,
                                ),
                            ),
                        ),
                        'middle_center' => array(
                            'title' => 'Middle Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(

                            ),
                        ),
                        'middle_right' => array(
                            'title' => 'Middle Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                                'menu' => array(
                                    'title' => 'Menu',
                                    'has_settings' => true,
                                ),
                            ),
                        ),
                        'bottom_left' => array(
                            'title' => 'Bottom Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds clear-item',
                            'content' => array(

                            ),
                        ),
                        'bottom_center' => array(
                            'title' => 'Bottom Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(

                            ),
                        ),
                        'bottom_right' => array(
                            'title' => 'Bottom Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(

                            ),
                        ),
                    ),
                    'default' => array(
                        'all_item' => array(
                            'title' => 'All Item',
                            'layout' => 'all',
                            'content' => array(
                                'search' => array(
                                    'title' => 'Search',
                                    'has_settings' => false,
                                ),
                                'login' => array(
                                    'title' => 'Login',
                                    'has_settings' => false,
                                ),
                                'cart' => array(
                                    'title' => 'Cart',
                                    'has_settings' => false,
                                ),
                                'burger_sidebar' => array(
                                    'title' => 'Burger Sidebar',
                                    'has_settings' => true,
                                ),
                                'text1' => array(
                                    'title' => 'Text/HTML 1',
                                    'has_settings' => true,
                                ),
                                'text2' => array(
                                    'title' => 'Text/HTML 2',
                                    'has_settings' => true,
                                ),

                                'text3' => array(
                                    'title' => 'Text/HTML 3',
                                    'has_settings' => true,
                                ),
                                'text4' => array(
                                    'title' => 'Text/HTML 4',
                                    'has_settings' => true,
                                ),

                                'text5' => array(
                                    'title' => 'Text/HTML 5',
                                    'has_settings' => true,
                                ),
                                'text6' => array(
                                    'title' => 'Text/HTML 6',
                                    'has_settings' => true,
                                ),
                                'delimiter1' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter2' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter3' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter4' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter5' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'delimiter6' => array(
                                    'title' => '|',
                                    'has_settings' => true,
                                ),
                                'empty_space1' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space2' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space3' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space4' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                                'empty_space5' => array(
                                    'title' => '&#8592;&#8594;',
                                    'has_settings' => false,
                                ),
                            ),
                        ),
                        'top_left' => array(
                            'title' => 'Top Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'top_center' => array(
                            'title' => 'Top Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'top_right' => array(
                            'title' => 'Top Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                        'middle_left' => array(
                            'title' => 'Middle Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds clear-item',
                            'content' => array(
                                'logo' => array(
                                    'title' => 'Logo',
                                    'has_settings' => true,
                                ),
                            ),
                        ),
                        'middle_center' => array(
                            'title' => 'Middle Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(

                            ),
                        ),
                        'middle_right' => array(
                            'title' => 'Middle Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                                'menu' => array(
                                    'title' => 'Menu',
                                    'has_settings' => true,
                                ),
                            ),
                        ),
                        'bottom_left' => array(
                            'title' => 'Bottom Left',
                            'has_settings' => true,
                            'layout' => 'one-thirds clear-item',
                            'content' => array(

                            ),
                        ),
                        'bottom_center' => array(
                            'title' => 'Bottom Center',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(

                            ),
                        ),
                        'bottom_right' => array(
                            'title' => 'Bottom Right',
                            'has_settings' => true,
                            'layout' => 'one-thirds',
                            'content' => array(
                            ),
                        ),
                    ),
                ),

                //HEADER TEMPLATES
                // MAIN HEADER SETTINGS
                array(
                    'id'       => 'header_templates-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Header Templates', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),

                array(
                    'id'         => 'opt-presets',
                    'type'       => 'image_select',
                    'presets'    => true,
                    'full_width' => true,
                    'title'      => esc_html__( 'Preset', 'gt3_themes_core' ),
                    'subtitle'   => esc_html__( 'This allows you to set default header layout.', 'gt3_themes_core' ),
                    'default'    => 0,
                    'options'    => array(
                        '1' => array(
                            'alt'     => 'Header 1',
                            'img'     => esc_url(ReduxFramework::$_url) . 'assets/img/header_1.jpg',
                            'presets' => $header_preset_1
                        ),
                    ),
                ),
                array(
                    'id'     => 'header_templates-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),



                // MAIN HEADER SETTINGS
                array(
                    'id'       => 'main_header_settings-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Header Main Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'header_full_width',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Full Width Header', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set header content in full width layout', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'header_sticky',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Sticky Header', 'gt3_themes_core' ),
                    'default'  => true,
                ),
                array(
                    'id'       => 'header_sticky_appearance_style',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Sticky Appearance Style', 'gt3_themes_core' ),
                    'options'  => array(
                        'classic' => esc_html__( 'Classic', 'gt3_themes_core' ),
                        'scroll_top' => esc_html__( 'Appearance only on scroll top', 'gt3_themes_core' ),
                    ),
                    'required' => array( 'header_sticky', '=', '1' ),
                    'default'  => 'classic'
                ),
                array(
                    'id'       => 'header_sticky_appearance_from_top',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Sticky Header Appearance From Top of Page', 'gt3_themes_core' ),
                    'options'  => array(
                        'auto' => esc_html__( 'Auto', 'gt3_themes_core' ),
                        'custom' => esc_html__( 'Custom', 'gt3_themes_core' ),
                    ),
                    'required' => array( 'header_sticky', '=', '1' ),
                    'default'  => 'auto'
                ),
                array(
                    'id'             => 'header_sticky_appearance_number',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Set the distance from the top of the page', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 300,
                    ),
                    'required' => array( 'header_sticky_appearance_from_top', '=', 'custom' ),
                ),
                array(
                    'id'       => 'header_sticky_shadow',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Sticky Header Bottom Shadow', 'gt3_themes_core' ),
                    'default'  => true,
                    'required' => array( 'header_sticky', '=', '1' ),
                ),
                array(
                    'id'     => 'main_header_settings-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),



                // TOP LEFT SIDE SETTINGS
                array(
                    'id'       => 'top_left-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Top Left Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'top_left-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'left',
                ),
                array(
                    'id'     => 'top_left-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // TOP CENTER SIDE SETTINGS
                array(
                    'id'       => 'top_center-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Top Center Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'top_center-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'center',
                ),
                array(
                    'id'     => 'top_center-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // TOP RIGHT SIDE SETTINGS
                array(
                    'id'       => 'top_right-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Top Right Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'top_right-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'right',
                ),
                array(
                    'id'     => 'top_right-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // MIDDLE LEFT SIDE SETTINGS
                array(
                    'id'       => 'middle_left-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Middle Left Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'middle_left-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'left',
                ),
                array(
                    'id'     => 'middle_left-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // MIDDLE CENTER SIDE SETTINGS
                array(
                    'id'       => 'middle_center-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Middle Center Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'middle_center-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'center',
                ),
                array(
                    'id'     => 'middle_center-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // MIDDLE RIGHT SIDE SETTINGS
                array(
                    'id'       => 'middle_right-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Top Middle Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'middle_right-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'right',
                ),
                array(
                    'id'     => 'middle_right-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),


                // BOTTOM LEFT SIDE SETTINGS
                array(
                    'id'       => 'bottom_left-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Bottom Left Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'bottom_left-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'left',
                ),
                array(
                    'id'     => 'bottom_left-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // BOTTOM CENTER SIDE SETTINGS
                array(
                    'id'       => 'bottom_center-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Bottom Center Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'bottom_center-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'center',
                ),
                array(
                    'id'     => 'bottom_center-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // BOTTOM RIGHT SIDE SETTINGS
                array(
                    'id'       => 'bottom_right-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Bottom Right Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'bottom_right-align',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Item Align', 'gt3_themes_core' ),
                    'options'  => array(
                        'left' => esc_html__( 'Left', 'gt3_themes_core' ),
                        'center' => esc_html__( 'Center', 'gt3_themes_core' ),
                        'right' => esc_html__( 'Right', 'gt3_themes_core' ),
                    ),
                    'default'  => 'right',
                ),
                array(
                    'id'     => 'bottom_right-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),





                //LOGO SETTINGS
                array(
                    'id'       => 'logo-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Logo Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'header_logo',
                    'type'     => 'media',
                    'title'    => esc_html__( 'Header Logo', 'gt3_themes_core' ),
                ),
                array(
                    'id'       => 'logo_height_custom',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Enable Logo Height', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'             => 'logo_height',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Set Logo Height' , 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 100,
                    ),
                    'required' => array( 'logo_height_custom', '=', '1' ),
                ),
                array(
                    'id'       => 'logo_max_height',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Don\'t limit maximum height', 'gt3_themes_core' ),
                    'default'  => false,
                    'required' => array( 'logo_height_custom', '=', '1' ),
                ),
                array(
                    'id'             => 'sticky_logo_height',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Set Sticky Logo Height' , 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => '',
                    ),
                    'required' => array(
                        array( 'logo_height_custom', '=', '1' ),
                        array( 'logo_max_height', '=', '1' ),
                    ),
                ),
                array(
                    'id'       => 'logo_sticky',
                    'type'     => 'media',
                    'title'    => esc_html__( 'Sticky Logo', 'gt3_themes_core' ),
                ),
                array(
                    'id'       => 'logo_mobile',
                    'type'     => 'media',
                    'title'    => esc_html__( 'Mobile Logo', 'gt3_themes_core' ),
                ),
                array(
                    'id'     => 'logo-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // MENU
                array(
                    'id'       => 'menu-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Menu Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'menu_select',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Select Menu', 'gt3_themes_core' ),
                    'options'  => $this->getMenuList(),
                    'default'  => 'left',
                ),
                array(
                    'id'       => 'menu_ative_top_line',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Enable Active Menu Item Marker', 'gt3_themes_core' ),
                    'default'  => false,
                ),
				array(
					'id'            => 'menu_ative_top_color',
					'type'          => 'color',
					'title'         => __( 'Active Menu Item Marker Color', 'gt3_themes_core' ),
					'subtitle'      => __( 'Set the active menu item marker color', 'gt3_themes_core' ),
					'default'       => '#ffffff',
					'transparent'   => false,
					'required' => array( 'menu_ative_top_line', '=', '1' ),
				),
                array(
                    'id'       => 'sub_menu_background',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Sub Menu Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set sub menu background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#f7f8f8',
                        'alpha' => '1',
                        'rgba'  => 'rgba(247,248,248,1)'
                    ),
                    'mode'     => 'background',
                ),
				array(
                    'id'       => 'sub_menu_background_hover',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Sub Menu Background on Hover and Current', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set the background color for sub menu on hover and Current menu', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '1',
                        'rgba'  => 'rgba(255,255,255,1)'
                    ),
                    'mode'     => 'background',
                ),
                array(
                    'id'       => 'sub_menu_color',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Sub Menu Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set sub menu header text color', 'gt3_themes_core' ),
                    'default'  => '#000000',
                    'transparent' => false,
                ),
                array(
                    'id'     => 'menu-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // BURGER SIDEBAR
                array(
                    'id'       => 'burger_sidebar-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Burger Sidebar Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'burger_sidebar_select',
                    'type'     => 'select',
                    'title'    => esc_html__( 'Select Sidebar', 'gt3_themes_core' ),
                    'data'     => 'sidebars',
                ),
                array(
                    'id'     => 'burger_sidebar-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),


                //TOP SIDE
                array(
                    'id'       => 'side_top-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Top Header Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'side_top_background',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#f5f5f5',
                        'alpha' => '1',
                        'rgba'  => 'rgba(245,245,245,1)'
                    ),
                    'mode'     => 'background',
                ),
                array(
                    'id'       => 'side_top_color',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#94958d',
                    'transparent' => false,
                ),
                array(
                    'id'             => 'side_top_height',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 40,
                    )
                ),
                array(
                    'id'       => 'side_top_border',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Set Bottom Border', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'side_top_border_color',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Border Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set border color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '.15',
                        'rgba'  => 'rgba(255,255,255,0.15)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_top_border', '=', '1' ),
                ),
                array(
                    'id'       => 'side_top_sticky',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Sticky Header?', 'gt3_themes_core' ),
                    'default'  => false,
                    'required' => array( 'header_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_top_background_sticky',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Sticky Header Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#f5f5f5',
                        'alpha' => '1',
                        'rgba'  => 'rgba(245,245,245,1)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_top_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_top_color_sticky',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Sticky Header Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#94958d',
                    'transparent' => false,
                    'required' => array( 'side_top_sticky', '=', '1' ),
                ),
                array(
                    'id'             => 'side_top_height_sticky',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Sticky Header Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 38,
                    ),
                    'required' => array( 'side_top_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_top_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Mobile Header?', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'side_top-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),


                // Middle SIDE
                array(
                    'id'       => 'side_middle-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Middle Header Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'side_middle_background',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '1',
                        'rgba'  => 'rgba(255,255,255,1)'
                    ),
                    'mode'     => 'background',
                ),
                array(
                    'id'       => 'side_middle_color',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#000000',
                    'transparent' => false,
                ),
                array(
                    'id'             => 'side_middle_height',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 130,
                    )
                ),
                array(
                    'id'       => 'side_middle_border',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Set Bottom Border', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'side_middle_border_color',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Border Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set border color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '.15',
                        'rgba'  => 'rgba(255,255,255,0.15)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_middle_border', '=', '1' ),
                ),
                array(
                    'id'       => 'side_middle_sticky',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Sticky Header?', 'gt3_themes_core' ),
                    'default'  => true,
                    'required' => array( 'header_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_middle_background_sticky',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Sticky Header Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '1',
                        'rgba'  => 'rgba(255,255,255,1)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_middle_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_middle_color_sticky',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Sticky Header Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#000000',
                    'transparent' => false,
                    'required' => array( 'side_middle_sticky', '=', '1' ),
                ),
                array(
                    'id'             => 'side_middle_height_sticky',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Sticky Header Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 90,
                    ),
                    'required' => array( 'side_middle_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_middle_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Mobile Header?', 'gt3_themes_core' ),
                    'default'  => true,
                ),
                array(
                    'id'     => 'side_middle-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),


                // Bottom SIDE
                array(
                    'id'       => 'side_bottom-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Bottom Header Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'       => 'side_bottom_background',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '1',
                        'rgba'  => 'rgba(255,255,255,1)'
                    ),
                    'mode'     => 'background',
                ),
                array(
                    'id'       => 'side_bottom_color',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#000000',
                    'transparent' => false,
                ),
                array(
                    'id'             => 'side_bottom_height',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 38,
                    )
                ),
                array(
                    'id'       => 'side_bottom_border',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Set Bottom Border', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'side_bottom_border_color',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Border Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set border color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '.15',
                        'rgba'  => 'rgba(255,255,255,0.15)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_bottom_border', '=', '1' ),
                ),
                array(
                    'id'       => 'side_bottom_sticky',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Sticky Header?', 'gt3_themes_core' ),
                    'default'  => false,
                    'required' => array( 'header_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_bottom_background_sticky',
                    'type'     => 'color_rgba',
                    'title'    => esc_html__( 'Sticky Header Background', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set background color', 'gt3_themes_core' ),
                    'default'  => array(
                        'color' => '#ffffff',
                        'alpha' => '1',
                        'rgba'  => 'rgba(255,255,255,1)'
                    ),
                    'mode'     => 'background',
                    'required' => array( 'side_bottom_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_bottom_color_sticky',
                    'type'     => 'color',
                    'title'    => esc_html__( 'Sticky Header Text Color', 'gt3_themes_core' ),
                    'subtitle' => esc_html__( 'Set text color', 'gt3_themes_core' ),
                    'default'  => '#000000',
                    'transparent' => false,
                    'required' => array( 'side_bottom_sticky', '=', '1' ),
                ),
                array(
                    'id'             => 'side_bottom_height_sticky',
                    'type'           => 'dimensions',
                    'units'          => false,
                    'units_extended' => false,
                    'title'          => esc_html__( 'Sticky Header Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'default'        => array(
                        'height' => 38,
                    ),
                    'required' => array( 'side_bottom_sticky', '=', '1' ),
                ),
                array(
                    'id'       => 'side_bottom_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Show Section in Mobile Header?', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'side_bottom-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),


                //TEXT SETTINGS
                array(
                    'id'       => 'text1-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 1 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text1_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text1_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text1_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text1_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text1-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                //2
                array(
                    'id'       => 'text2-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 2 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text2_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text2_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text2_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text2_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text2-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                //3
                array(
                    'id'       => 'text3-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 3 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text3_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text3_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text3_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text3_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text3-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                //4
                array(
                    'id'       => 'text4-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 4 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text4_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text4_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text4_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text4_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text4-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                //5
                array(
                    'id'       => 'text5-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 5 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text5_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text5_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text5_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text5_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text5-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                //6
                array(
                    'id'       => 'text6-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Text / HTML 6 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'      => 'text6_editor',
                    'type'    => 'editor',
                    'title'   => esc_html__( 'Text Editor', 'gt3_themes_core' ),
                    'default' => '',
                    'args'    => array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_rows' => 2,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ),
                ),
                array(
                    'id'       => 'text6_hide_on_desktop',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Desktop', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text6_hide_on_tablet',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Tablet', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'       => 'text6_hide_on_mobile',
                    'type'     => 'switch',
                    'title'    => esc_html__( 'Hide on Mobile', 'gt3_themes_core' ),
                    'default'  => false,
                ),
                array(
                    'id'     => 'text6-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                // Delimiters
                array(
                    'id'       => 'delimiter1-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter1 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter1_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter1'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter1-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                array(
                    'id'       => 'delimiter2-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter2 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter2_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter2'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter2-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                array(
                    'id'       => 'delimiter3-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter3 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter3_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter3'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter3-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                array(
                    'id'       => 'delimiter4-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter4 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter4_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter4'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter4-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                array(
                    'id'       => 'delimiter5-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter5 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter5_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter5'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter5-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),

                array(
                    'id'       => 'delimiter6-start',
                    'type'     => 'gt3_section',
                    'title'    => esc_html__( 'Delimiter6 Settings', 'gt3_themes_core' ),
                    'indent'   => false,
                    'section_role' => 'start'
                ),
                array(
                    'id'             => 'delimiter6_height',
                    'type'           => 'dimensions',
                    'units'          => array('em','px','%'),
                    'title'          => esc_html__( 'Delimiter Height', 'gt3_themes_core' ),
                    'height'         => true,
                    'width'          => false,
                    'output'         => array('height' => '.gt3_delimiter6'),
                    'default'        => array(
                        'height'    => 1,
                        'units'     => 'em',
                    )
                ),
                array(
                    'id'     => 'delimiter6-end',
                    'type'   => 'gt3_section',
                    'indent' => false,
                    'section_role' => 'end'
                ),
                // Delimiters end

            );
            $this->parent->sections[] = array(
                'id'     => 'gt3_header_builder_section',
                'title'  =>  esc_html__( 'gt3 Header Builder', 'gt3_themes_core' ),
                'desc'   => esc_html__( 'This is gt3 Header Builder', 'gt3_oconnor_core' ),
                'icon'   => 'el el-screen',
                'fields' => $options
            );
        }

    } // class



} // if
