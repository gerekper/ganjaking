<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : "";
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
$html_tag          = !empty( $section_html_tag ) ? $section_html_tag : 'p';

foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>

<<?php echo $html_tag . $id_html . $name_html . $class_html . $custom_attributes . $data_html; ?>>