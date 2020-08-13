<?php

class BetterDocs_MetaBox {

    public $type = 'betterdocs';

    public static $args;
    public static $prefix = 'betterdocs_meta_';
    public static $post_id;
    public static $object_types;

    public $defaults = array(
        'id'            => '',
        'title'         => '',
        'object_types'  => array(),
        'context'       => 'normal',
        'priority'      => 'low',
        'show_header'   => true,
        'prefix' => ''
    );


    public static function render_meta_field( $key = '', $field = [], $value = '', $idd = null ) {
        global $pagenow;
        $post_id   = self::$post_id;
        $attrs = $wrapper_attrs = '';
        if( ! is_null( $idd ) ){
            $post_id   = $idd;
        }
        $name      = self::$prefix . $key;
        $field_id  = $name;
        $id        = self::get_row_id( $key );
        $file_name = isset( $field['type'] ) ? $field['type'] : '';
        
        if( 'template' === $file_name ) {
            $default = isset( $field['defaults'] ) ? $field['defaults'] : [];
        } else {
            $default = isset( $field['default'] ) ? $field['default'] : '';
        }

        if( empty( $value ) ) {
            if( metadata_exists( 'post', $post_id, "_{$name}" ) ) {
                $value = get_post_meta( $post_id, "_{$name}", true );
            } else {
                $value = $default;
            }
        } else {
            $value = $value;
        }

        $default_attr = is_array( $default ) ? json_encode( $default ) : $default;

        if( ! empty( $default_attr ) ) {
            $attrs .= ' data-default="' . esc_attr( $default_attr ) . '"';
        }

        $class  = 'betterdocs-meta-field';
        $row_class = self::get_row_class( $file_name );

        if( isset( $field['class'] ) && ! empty( $field['class'] ) ) {
            $row_class .= ' ' . $field['class'];
        }
        $row_class .= ' betterdocs-' . $key;
                
        $attrs .= ' data-key="' . esc_attr( $key ) . '"';

        if( isset( $field['tab'] ) && $file_name == 'select' ) {
            $attrs .= ' data-tab="' . esc_attr( json_encode( $field['tab'] ) ) . '"';
        }

        if( isset( $field['builder_hidden'] ) && $field['builder_hidden'] && $pagenow == 'admin.php' ) {
            $row_class .= ' betterdocs-builder-hidden';
        }

        include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-field-display.php';
    }
    /**
     * Get the row id ready
     *
     * @param string $key
     * @return string
     */
    public static function get_row_id( $key ) {
        return str_replace( '_', '-', self::$prefix ) . $key;
    }
    /**
     * Get the row id ready
     *
     * @param string $key
     * @return string
     */
    public static function get_row_class( $file ) {
        $prefix = str_replace( '_', '-', self::$prefix );

        switch( $file ) {
            case 'group':
                $row_class = $prefix .'group-row';
                break;
            case 'colorpicker':
                $row_class = $prefix .'colorpicker-row';
                break;
            case 'message':
                $row_class = $prefix . 'info-message-wrapper';
                break;
            case 'theme':
                $row_class = $prefix . 'theme-field-wrapper';
                break;
            default :
                $row_class = $prefix . $file;
                break;
        }

        return $row_class;
    }

    public static function get_metabox_fields( $prefix = '' ) {
        $args = self::get_args();
        $tabs = $args['tabs'];

        $new_fields = [];

        foreach( $tabs as $tab ) {
            $sections = $tab['sections'];
            foreach( $sections as $section ) {
                $fields = $section['fields'];
                foreach( $fields as $id => $field ) {
                    $new_fields[ $prefix . $id ] = $field;
                }    
            }
        }

        return apply_filters('betterdocs_meta_fields', $new_fields );
    }
}