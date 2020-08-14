<?php defined( 'ABSPATH' ) or exit; ?>

<a href="<?php echo add_query_arg( array( 'edit' => '' ), $section_uri ); ?>" class="button">
    <?php echo __('Add new attribute', 'yith-frontend-manager-for-woocommerce'); ?>
</a>

<?php

global $manage_attribute_result;

if ( is_wp_error( $manage_attribute_result ) ) {
    echo '<div class="error">' . $manage_attribute_result->get_error_message() . '</div>';
}

?>

<table class="table">
    <tr>
        <th><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Type', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Order by', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th><?php echo __('Terms', 'yith-frontend-manager-for-woocommerce'); ?></th>
        <th></th>
    </tr>

    <?php
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    if ( count( $attribute_taxonomies ) > 0 ) :
        foreach ( $attribute_taxonomies as $tax ) :

            $actions_uri = array(
                'edit_uri'   => add_query_arg( array( 'edit' => $tax->attribute_name, ), $section_uri),
                'delete_uri' => add_query_arg( array('act' => 'delete', 'term_id' => $tax->attribute_id, 'taxonomy' => 'product_attribute' ), $section_uri),
            );  ?>

            <tr>
                <td>
                    <a href="<?php echo $actions_uri['edit_uri']; ?>">
                        <?php echo $tax->attribute_label; ?>
                    </a>
                    <?php yith_wcfm_add_inline_action( $actions_uri ); ?>
                </td>
                <td><?php echo $tax->attribute_name; ?></td>
                <td><?php echo $tax->attribute_type; ?></td>
                <td><?php echo $tax->attribute_orderby; ?></td>
                <td>
                    <?php
                    $taxonomy_terms = $out = array();
                    $taxonomy_terms[ $tax->attribute_name ] = get_terms( wc_attribute_taxonomy_name( $tax->attribute_name ), 'orderby=' . $tax->attribute_orderby . '&hide_empty=0' );
                    if ( is_wp_error( $taxonomy_terms[ $tax->attribute_name ] ) && $manage_attribute_result != NULL ) {
                        $taxonomy_terms[ $tax->attribute_name ] = get_terms( wc_attribute_taxonomy_name( $manage_attribute_result ), 'orderby=' . $tax->attribute_orderby . '&hide_empty=0' );
                    }
                    if ( ! is_wp_error( $taxonomy_terms[ $tax->attribute_name ] ) ) {
                        foreach ( $taxonomy_terms[ $tax->attribute_name ] as $term ) { array_push( $out, $term->name); }
                    }
                    echo implode( ', ', $out );
                    ?>
                </td>
                <td><a href="<?php echo add_query_arg( array( 'attribute' => sanitize_title( $tax->attribute_name ), ), $section_uri ); ?>" class="button"><span class="dashicons dashicons-admin-generic"></span></a></td>
            </tr>
        <?php endforeach;
        else : ?>

            <tr><td colspan="4"><?php echo __('No Attributes found', 'yith-frontend-manager-for-woocommerce'); ?></td></tr>

        <?php endif; ?>

</table>