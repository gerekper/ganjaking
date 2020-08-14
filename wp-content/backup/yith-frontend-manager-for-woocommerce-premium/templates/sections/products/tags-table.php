<?php defined( 'ABSPATH' ) or exit; ?>

<a href="<?php echo add_query_arg( array( 'edit' => '' ), $section_uri ); ?>" class="button"><?php echo __('Add new tag', 'yith-frontend-manager-for-woocommerce'); ?></a>

<table class="table">
    <tr>
        <th><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Count', 'yith-frontend-manager-for-woocommerce'); ?></th>
    </tr>

    <?php

    $args = array(
        'orderby'    => 'name',
        'order'      => 'asc',
        'hide_empty' => 0,
        'include'    => '',
    );

    $all_tags = get_terms( 'product_tag', $args );

    if( count( $all_tags ) > 0 ) :
        foreach ( $all_tags as $tag ) :
        $actions_uri = array(
            'edit_uri'   => add_query_arg( array( 'edit' => $tag->slug, ), $section_uri),
            'delete_uri' => add_query_arg( array('act' => 'delete', 'term_id' => $tag->term_id, 'taxonomy' => 'product_tag' ), $section_uri),
            'view_uri'   => get_term_link( $tag, $tag->taxonomy )
        );
        ?>

        <tr>
            <td>
                <a href="<?php echo $actions_uri['edit_uri']; ?>"><?php echo $tag->name; ?></a>
                <?php yith_wcfm_add_inline_action( $actions_uri ); ?>
            </td>
            <td><?php echo $tag->description; ?></td>
            <td><?php echo $tag->slug; ?></td>
            <td><?php echo $tag->count; ?></td>
        </tr>

    <?php endforeach;
    else : ?>

        <tr><td colspan="4"><?php echo __('No Tags found', 'yith-frontend-manager-for-woocommerce'); ?></td></tr>

    <?php endif; ?>

</table>