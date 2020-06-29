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

<img <?php echo $custom_attributes . $data_html; ?> class="help_tip <?php echo $class; ?>" heigth="16" width="16" data-tip="<?php echo $value; ?>"
                                       src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png"/>