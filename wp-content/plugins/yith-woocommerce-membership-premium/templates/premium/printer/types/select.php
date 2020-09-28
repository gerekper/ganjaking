<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>

<select <?php echo $id_html . $name_html . $class_html . $custom_attributes . $data_html; ?> >
    <?php
    foreach ( $options as $option_value => $option_title ) {
        if ( is_array( $option_title ) ) {
            ?>
            <optgroup label="<?php echo isset( $option_title[ 'title' ] ) ? $option_title[ 'title' ] : ''; ?>"> <?php

                if ( isset( $option_title[ 'options' ] ) && is_array( $option_title[ 'options' ] ) ) {
                    foreach ( $option_title[ 'options' ] as $sub_option_value => $sub_option_title ) {
                        ?>
                        <option value="<?php echo $sub_option_value; ?>" <?php selected( $sub_option_value, $value ) ?> ><?php echo $sub_option_title; ?></option>
                        <?php
                    }
                }

                ?> </optgroup> <?php
        } else {
            ?>
            <option value="<?php echo $option_value; ?>" <?php selected( $option_value, $value ) ?> ><?php echo $option_title; ?></option> <?php
        }
    }
    ?>
</select>