<?php
    use WPDeveloper\BetterDocs\Utils\Helper;
    $_defined_vars = get_defined_vars();
    $_params       = isset( $_defined_vars['params'] ) ? $_defined_vars['params'] : [];

    $attributes = [
        'data-id' => get_the_ID(),
        'class'   => ['betterdocs-sidebar-list betterdocs-sidebar-layout-6']
    ];

    if ( is_single() && ( $term->term_id === $current_queried_object_id || ( (bool) $nested_subcategory && in_array( $term->term_id, $ancestors ) ) ) ) {
        $attributes['class'][] = 'active';
    } elseif ( Helper::get_tax() == 'doc_category' && $term->term_id === $current_queried_object_id ) {
        $attributes['class'][] = 'active';
    }

    if ( isset( $wrapper_class ) && is_array( $wrapper_class ) && ! empty( $wrapper_class ) ) {
        $attributes['class'] = array_merge( $attributes['class'], $wrapper_class );
    }

    $attributes = betterdocs()->template_helper->get_html_attributes( $attributes );
?>

<article
    <?php echo $attributes; ?>>
	<div class="betterdocs-sidebar-list-inner">
        <?php
            if ( $show_header ) {
                $view_object->get( 'layout-parts/header', $_params );
            }

            if ( $show_list ) {
                echo '<div class="betterdocs-body">';
                $view_object->get( 'template-parts/category-list', $_params );
                echo '</div>';
            }
        ?>
    </div>
</article>
