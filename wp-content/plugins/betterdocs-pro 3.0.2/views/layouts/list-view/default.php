<?php 

$attributes = [
    'href'    => esc_url( $permalink ),
    'id'      => 'cat-id-' . get_the_ID(),
    'class'   => ['docs-single-cat-wrap']
];

if ( isset( $wrapper_class ) && is_array( $wrapper_class ) && ! empty( $wrapper_class ) ) {
    $attributes['class'] = array_merge( $attributes['class'], $wrapper_class );
}

$attributes = betterdocs()->template_helper->get_html_attributes( $attributes );
?>

<a
<?php echo $attributes; ?>>
    <?php
        $_defined_vars = get_defined_vars();
        $_header_vars  = apply_filters( 'betterdocs_filter_header_vars', $_defined_vars['params'] );
        
        betterdocs()->template_helper->category_icon($_header_vars);
        betterdocs()->template_helper->category_title($_header_vars);
        betterdocs()->template_helper->category_description($_header_vars);
    ?>
</a>
