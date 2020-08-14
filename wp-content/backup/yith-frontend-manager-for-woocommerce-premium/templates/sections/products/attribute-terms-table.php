<?php defined( 'ABSPATH' ) or exit; ?>

<?php $attribute = isset( $_REQUEST['attribute'] ) ?  $_REQUEST['attribute'] : ''; ?>

<a href="<?php echo add_query_arg( array( 'attribute' => $attribute, 'edit' => '', ), $section_uri ); ?>" class="button"><?php echo __('Add new term', 'yith-frontend-manager-for-woocommerce'); ?></a>

<table class="table">
    <thead>
        <tr>
            <th><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></th>
            <th><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></th>
            <th><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></th>
            <th><?php echo __('Count', 'yith-frontend-manager-for-woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>

    <?php
    $taxonomy       = wc_attribute_taxonomy_name( $attribute );
    $taxonomy_obj   = get_taxonomy( $taxonomy );
    
    $args = array(
        'orderby'    => 'name',
        'order'      => 'asc',
        'hide_empty' => 0,
        'include'    => '',
        'taxonomy'   => $taxonomy
    );

    $all_attribute_terms = get_terms( $args );

    if( empty( $all_attribute_terms ) ){
        $no_items = sprintf( '%s %s %s',
            _x( 'No', '[Part of] No items found', 'yith-frontend-manager-for-woocommerce' ),
            $attribute,
            _x( 'found', '[Part of] No items found', 'yith-frontend-manager-for-woocommerce' )
        );
        printf( '<tr><td colspan="4">%s</td></tr>', $no_items );
    }

    else {
        foreach ( $all_attribute_terms as $term ) :
            if ( $term->parent == 0 ) :
                $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                $image = wp_get_attachment_url( $thumbnail_id );

                $actions_uri = array(
                    'edit_uri'   => add_query_arg( array( 'attribute' => $attribute, 'edit' => $term->slug, ), $section_uri),
                    'delete_uri' => add_query_arg( array('act' => 'delete', 'term_id' => $term->term_id, 'taxonomy' => $taxonomy, 'attribute' => $attribute ), $section_uri),
                    'view_uri'   => $taxonomy_obj->public ? get_term_link( $term, $taxonomy ) : false
                );

                ?>

                <tr>
                    <td>
                        <a href="<?php echo $actions_uri['edit_uri']; ?>">
                            <?php echo $term->name; ?>
                        </a>
                        <?php yith_wcfm_add_inline_action( $actions_uri ); ?>
                    </td>
                    <td><?php echo $term->description; ?></td>
                    <td><?php echo $term->slug; ?></td>
                    <td><?php echo $term->count; ?></td>
                </tr>

                <?php

                $args2 = array(
                    'orderby'    => 'name',
                    'order'      => 'asc',
                    'hide_empty' => 0,
                    'include'    => '',
                    'parent'     => $term->term_id,
                    'taxonomy'   => $taxonomy
                );

                $sub_terms = get_terms( $args2 );

                if ( $sub_terms ) :
                    foreach( $sub_terms as $sub_term ) :

                        $thumbnail_id = get_term_meta( $sub_term->term_id, 'thumbnail_id', true );
                        $image = wp_get_attachment_url( $thumbnail_id );

                        ?>

                        <tr>
                            <td>
                                <a href="<?php echo $actions_uri['edit_uri']; ?>">
                                    &#8212; <?php echo $sub_term->name; ?>
                                </a>
                                <br />
                                <?php yith_wcfm_add_inline_action( $actions_uri ); ?>
                            </td>
                            <td><?php echo $sub_term->description; ?></td>
                            <td><?php echo $sub_term->slug; ?></td>
                            <td><?php echo $sub_term->count; ?></td>
                        </tr>

                        <?php

                    endforeach;
                endif;
            endif;
        endforeach;
    }
    ?>
    </tbody>
</table>
