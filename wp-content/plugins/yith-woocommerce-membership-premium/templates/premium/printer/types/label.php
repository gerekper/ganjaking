<?php
$for_html          = !empty( $for ) ? " for='{$for}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>

<label <?php echo $for_html . $class_html . $custom_attributes . $data_html; ?> >
    <?php echo $value ?>
</label>