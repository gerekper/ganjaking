<?php defined( 'ABSPATH' ) or exit; ?>

<a href="<?php echo add_query_arg( array( 'edit' => '' ), $section_uri ); ?>" class="button">
    <?php echo __('Add new category', 'yith-frontend-manager-for-woocommerce'); ?>
</a>

<table class="table">
    <tr>
        <th><?php echo __('Image', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Count', 'yith-frontend-manager-for-woocommerce'); ?></th>
    </tr>

    <?php

    $args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'name',
        'show_count'   => 0,
        'pad_counts'   => 0,
        'hierarchical' => 1,
        'title_li'     => '',
        'hide_empty'   => 0,
        'parent'       => 0
    );

    $all_categories = array();

    $ancestors = get_categories( $args );

    foreach( $ancestors as $ancestor ){
        $all_categories[] = $ancestor;

        $args = array(
            'taxonomy'     => 'product_cat',
            'child_of'     => 0,
            'parent'       => $ancestor->term_id,
            'orderby'      => 'name',
            'show_count'   => 0,
            'pad_counts'   => 0,
            'hierarchical' => 1,
            'title_li'     => '',
            'hide_empty'   => 0
        );

        $sub_categories = get_categories( $args );

        if( ! empty( $sub_categories ) ){
            $all_categories = array_merge( $all_categories, $sub_categories );
        }
    }

    if( count( $all_categories ) > 0 ) :
        foreach ( $all_categories as $cat ) :
            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
            $image = wp_get_attachment_url( $thumbnail_id );

            $actions_uri = array(
                'edit_uri'   => add_query_arg( array( 'edit' => $cat->slug, ), $section_uri),
                'delete_uri' => add_query_arg( array('act' => 'delete', 'term_id' => $cat->term_id, 'taxonomy' => 'product_cat' ), $section_uri),
                'view_uri'   => get_term_link( $cat, $cat->taxonomy )
            );
            ?>

            <tr>
                <td>
                    <a href="<?php echo $actions_uri['edit_uri']; ?>">
                        <?php echo $image ? '<img src="' . $image . '" alt="" />' :  wc_placeholder_img( 40 ); ?>
                    </a>
                </td>
                <td>
                    <a href="<?php echo $actions_uri['edit_uri']; ?>">
                        <?php if( $cat->category_parent != 0 ) : echo '&#8212;'; endif; ?>
                        <?php echo $cat->name; ?>
                    </a>
                    <?php yith_wcfm_add_inline_action( $actions_uri ); ?>
                </td>
                <td><?php echo $cat->description; ?></td>
                <td><?php echo $cat->slug; ?></td>
                <td><?php echo $cat->count; ?></td>
            </tr>
    <?php
    endforeach;
    else : ?>

        <tr><td colspan="5"><?php echo __('No Categories found', 'yith-frontend-manager-for-woocommerce'); ?></td></tr>

    <?php endif; ?>

</table>
