<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_gt3_presets' ) ) {

    class ReduxFramework_gt3_presets {

        /**
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                if (trailingslashit( str_replace( '\\', '/', ABSPATH ) ) == '/') {
                    $this->extension_url = site_url( $this->extension_dir );
                }else{
                    /*$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );*/
                }
                $this->extension_url = plugin_dir_url(__FILE__);
            }
        }

        public function get_buttons($x) {
            echo "<div class='gt3_presets__operation'>";
                echo "<div class='gt3_presets__operation_item operation_clone' title='".esc_html__('Clone','gt3_themes_core')."'><i class='fa fa-clone' aria-hidden='true'></i></div>";
                if ($x != 0) {                                
                    echo "<div class='gt3_presets__operation_item operation_edit' title='".esc_html__('Edit','gt3_themes_core')."'><i class='fa fa-pencil' aria-hidden='true'></i></div>";
                    echo "<div class='gt3_presets__operation_item operation_remove' title='".esc_html__('Remove','gt3_themes_core')."'><i class='fa fa-times' aria-hidden='true'></i></div>";
                }
            echo "</div>";

        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            if ( ! empty( $this->field['options'] ) || ! empty($this->field['default']) ) {
                $current_active = 0;
                if ( isset ( $this->value ) && 
                    is_array ( $this->value ) && 
                    !empty ( $this->value )
                ) {
                    $val = $this->value;
                    if (isset($val['current_active'])) {
                        $current_active = $val['current_active'];
                    }
                }

                echo '<div class="redux-gt3_preset-accordion" data-new-content-title="'.esc_attr__('New Preset','gt3_themes_core').'">';
                    echo "<div class='gt3_preset__container'>";
                $x = 0;

                if ( isset ( $this->value ) && 
                    is_array ( $this->value ) && 
                    !empty ( $this->value ) 
                ) {

                    $val = $this->value;

                    if (isset($val['current_active'])) {
                        unset($val['current_active']);
                    }
                    if (isset($val['def_preset'])) {
                        unset($val['def_preset']);
                    }
                    if (isset($val['items_count'])) {
                        unset($val['items_count']);
                    }

                    foreach ($val as $key => $item) {
                        echo '<div class="gt3_preset__wrapper'.($current_active == $key ? ' active' : '').'">';
                            echo '<fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '">';
                                $hide = ' hide';
                                $title_option = !empty($item) && !empty($item['title']) ? $item['title'] : esc_attr__('Preset','gt3_themes_core') . ' - '. $key;
                                $preset_option = !empty($item) && !empty($item['preset']) ? $item['preset'] : ''; 
                                
                                if (!empty($preset_option)) {
                                    $preset_option = json_decode( $preset_option, true );
                                }

                                if (!empty($preset_option) && is_array($preset_option)) {
                                    $preset_option['redux-backup'] = 1;
                                }
                                
                                echo '<div class="gt3_preset__item_content">';
                                    echo "<div class='gt3_preset__item_action'></div>";
                                    echo '<input type="text" id="' . $this->field[ 'id' ] . '-title_' . $key . '" name="' . $this->field[ 'name' ] . '[' . $key . '][title]" value="'.esc_attr($title_option).'" readonly="readonly" class="gt3_preset_item_title" />';
                                    echo '<input type="hidden" data-presets-id="'.$key.'" id="' . $this->field[ 'id' ] . '-preset_' . $key . '" name="' . $this->field[ "name" ] .'[' . $key . '][preset]" value="'.htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' ).'" data-presets="'.htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' ).'" class="gt3_preset_item_value" />';
                                    $this->get_buttons($key);
                                echo '</div>';
                            echo '</fieldset>';
                        echo '</div>';
                        $x = $key ++;
                    }
                    
                }else{
                    echo '<div class="gt3_preset__wrapper">';
                        echo '<fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '">';
                            $hide = ' hide';
                            $def_option = $this->field['default'];
                            $def_option = !empty($def_option[0]) ? $def_option[0] : array();
                            $title_option = !empty($def_option) && !empty($def_option['title']) ? $def_option['title'] : esc_attr__('Default','gt3_themes_core');
                            $preset_option = !empty($def_option) && !empty($def_option['preset']) ? $def_option['preset'] : ''; 

                            if (!empty($preset_option)) {
                                    $preset_option = json_decode( $preset_option, true );
                                }
                            if (!empty($preset_option) && is_array($preset_option)) {
                                $preset_option['redux-backup'] = 1;
                            }

                            echo '<div class="gt3_preset__item_content">';
                                echo '<input type="text" id="' . $this->field[ 'id' ] . '-title_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][title]" value="'.esc_attr($title_option).'" readonly="readonly" class="gt3_preset_item_title" />';
                                echo '<input type="hidden" id="' . $this->field[ 'id' ] . '-preset_' . $x . '" name="' . $this->field[ "name" ] .'[' . $x . '][preset]" value="'.htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' ).'" class="gt3_preset_item_value" />';
                                $this->get_buttons($x);                                    
                            echo '</div>';
                        echo '</fieldset>';
                    echo '</div>';
                }


                echo "<div class='gt3_preset_def_options'>";
                    $def_option = $this->field['default'];
                    $def_option = !empty($def_option[0]) ? $def_option[0] : array(); 
                    $preset_option = !empty($def_option) && !empty($def_option['preset']) ? $def_option['preset'] : '';

                    if (!empty($preset_option)) {
                        $preset_option = json_decode( $preset_option, true );
                    }
                    $preset_option = array(
                        'gt3_header_builder_id' => $preset_option
                    );
                    if (!empty($preset_option) && is_array($preset_option)) {
                        $preset_option['redux-backup'] = 1;
                    }
                    echo '<input type="hidden" id="' . $this->field[ 'id' ] . '-def_preset" name="' . $this->field[ 'name' ] . '[def_preset]" value="'.htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' ).'" class="gt3_preset_def_preset" />';
                    echo '<input type="hidden" id="' . $this->field[ 'id' ] . '-current_active" name="' . $this->field[ 'name' ] . '[current_active]" value="'.esc_attr($current_active).'" class="current_active_preset" />';
                    echo '<input type="hidden" id="' . $this->field[ 'id' ] . '-items_count" name="' . $this->field[ 'name' ] . '[items_count]" value="'.esc_attr($x).'" class="items_count" />';
                echo "</div>";


                // Add New
                echo "<div class='gt3_preset_add_new'>";
                    echo "<div class='gt3_preset_add_new_button'>";
                        echo esc_html__('Add New','gt3_themes_core');
                    echo "</div>";
                echo "</div>";
                // End Add New
                
                echo '</div>'; // end gt3_preset__container

                // Add new Container
                echo "<div class='gt3_preset_add_new_container'>";
                $this->field;
                $templates = $this->field['templates'];
                if (!empty($templates) && is_array($templates)) {
                    foreach ($templates as $template) {
                        $preset_option = $template['presets'];
                        if (!empty($preset_option)) {
                                $preset_option = json_decode( $preset_option, true );
                            }
                        if (!empty($preset_option) && is_array($preset_option)) {
                            $preset_option['redux-backup'] = 1;
                        }

                        echo "<div class='gt3_preset_add_new__template'>";
                        if (!empty($template['img'])) {
                            echo "<img src='".$template['img']."' class='gt3_preset_add_new__template_holder' alt='".$template['alt']."' data-preset='".htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' )."'>";
                        }else{
                            echo "<div class='gt3_preset_add_new__template_holder' data-preset='".$template['presets']."'>".htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' )."</div>";
                        }
                        echo "</div>";
                    }
                }
                echo "<div class='gt3_preset_add_new__template active default'>";
                    $preset_option = !empty($def_option) && !empty($def_option['preset']) ? $def_option['preset'] : '';
                    if (!empty($preset_option)) {
                        $preset_option = json_decode( $preset_option, true );
                    }
                    if (!empty($preset_option) && is_array($preset_option)) {
                        $preset_option['redux-backup'] = 1;
                    }
                    echo "<div class='gt3_preset_add_new__template_holder' data-preset='".htmlspecialchars( json_encode( $preset_option ), ENT_QUOTES, 'UTF-8' )."'>".esc_html__('Default','gt3_themes_core')."</div>";
                echo "</div>";

                echo "<div class='gt3_preset_add_new__submit'>";
                    echo "<input type='text' placeholder='".esc_html__('Name','gt3_themes_core')."' class='gt3_preset_add_new__submit_name' value='".esc_html__('New Preset','gt3_themes_core')."'>";
                    echo "<div class='gt3_preset_add_new__submit_button'>".esc_html__('Submit','gt3_themes_core')."</div>";
                echo "</div>";

                echo "</div>";
                // end Add new Container 
                
                echo '</div>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            wp_enqueue_style(
                'redux-field-gt3_presets-css',
                $this->extension_url  . 'field_gt3_presets.css',
                time(),
                true
            );

            wp_enqueue_script(
                'redux-field-gt3_presets-js',
                $this->extension_url . '/field_gt3_presets.js',
                array( 'jquery' ),
                time(),
                true
            );

           /* wp_enqueue_script(
                'redux-field-gt3_presets-js',
                ReduxFramework::$_url . 'inc/fields/image_select/field_image_select' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-image-select-css',
                    ReduxFramework::$_url . 'inc/fields/image_select/field_image_select.css',
                    array(),
                    time(),
                    'all'
                );
            }*/
        }

        public function getCSS( $mode = '' ) {
            $css   = '';
            $value = $this->value;

            $output = '';
            if ( ! empty( $value ) && ! is_array($value) ) {
                switch ( $mode ) {
                    case 'background-image':
                        $output = "background-image: url('" . $value . "');";
                        break;

                    default:
                        $output = $mode . ": " . $value . ";";
                }
            }

            $css .= $output;

            return $css;
        }

        public function output() {
            $mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'background-image' );

            if ( ( ! isset( $this->field['output'] ) || ! is_array( $this->field['output'] ) ) && ( ! isset( $this->field['compiler'] ) ) ) {
                return;
            }

            $style = $this->getCSS( $mode );

            if ( ! empty( $style ) ) {

                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $keys  = implode( ",", $this->field['output'] );
                    $style = $keys . "{" . $style . '}';
                    $this->parent->outputCSS .= $style;
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $keys  = implode( ",", $this->field['compiler'] );
                    $style = $keys . "{" . $style . '}';
                    $this->parent->compilerCSS .= $style;
                }
            }
        }
    }
}