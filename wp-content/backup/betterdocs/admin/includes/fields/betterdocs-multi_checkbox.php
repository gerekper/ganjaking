<?php 
    $name .= "[]";

    if( isset( $field['disable'] ) && $field['disable'] == true ) {
        $attrs .= ' disabled';
    }

    if( ! empty( $field['options'] ) ) :
        foreach( $field['options'] as $opt_id => $option ) {
            if( is_array( $value ) ) {
                $selected = in_array( $opt_id, $value ) ? 'checked="checked"' : '';
            }
            echo '<label><input class="'.esc_attr( $class ).'" type="checkbox" id="'. $field_id .'_'. $opt_id .'" name="'.$name.'" value="'. $opt_id .'" '. $selected .'/>'. $option .'</label><br>';
        }
    endif;
?>