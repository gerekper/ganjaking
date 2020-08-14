<?php

defined( 'ABSPATH' ) or exit;

$edit = isset( $_REQUEST['edit'] ) ? $_REQUEST['edit'] : '';
$attribute = isset( $_REQUEST['attribute'] ) ?  $_REQUEST['attribute'] : '';
$attribute_term = $edit != '' ? get_term_by( 'slug', $edit, 'pa_'.$attribute ) : NULL;

$taxonomy = "pa_{$attribute}";

$attribute_term_id = isset( $attribute_term->term_id ) ? $attribute_term->term_id : '';
$attribute_term_name = isset( $attribute_term->name ) ? $attribute_term->name : '';
$attribute_term_slug = isset( $attribute_term->slug ) ? $attribute_term->slug : '';
$attribute_term_parent = isset( $attribute_term->parent ) ? $attribute_term->parent : '';
$attribute_term_description = isset( $attribute_term->description ) ? $attribute_term->description : '';

?>

<h2><?php echo $attribute_term ? __('Edit product attribute terms', 'yith-frontend-manager-for-woocommerce') : __('Add new product attribute terms',
        'yith-frontend-manager-for-woocommerce'); ?></h2>

<form name="post" action="<?php echo add_query_arg( array( 'attribute' => $attribute, ), $section_uri ); ?>" method="post" id="post">

    <?php if ( $attribute_term_slug != '' ) : ?>

        <input type="hidden" name="id" value="<?php echo $attribute_term_id; ?>">
        <input type="hidden" name="act" value="edit_term">
        <input type="hidden" name="edit" value="<?php echo $attribute_term_slug; ?>">

    <?php else : ?>

        <input type="hidden" name="act" value="new_term">

    <?php endif; ?>

    <div class="options_group">

        <p class="form-field">
            <label for="term_name"><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="term_name" value="<?php echo isset( $attribute_term_name ) ? $attribute_term_name : ''; ?>" id="term_name"><br />
            <i><?php echo __('Enter the name as you want it to appear on your site.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <p class="form-field">
            <label for="term_slug"><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="term_slug" value="<?php echo isset( $attribute_term_slug ) ? $attribute_term_slug : ''; ?>" id="term_slug"><br />
            <i><?php echo __('A URL-friendly version of the name. It usually contains only lowercase letters, numbers, and hyphens.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <?php if( is_taxonomy_hierarchical( $taxonomy ) ) : ?>
            <p class="form-field">
            <label><?php echo __('Parent', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <select name="term_parent"><option value="-1">None</option><?php
                $args = array(
                    'orderby'    => 'name',
                    'order'      => 'asc',
                    'hide_empty' => 0,
                    'include'    => ''
                );

                $all_attribute_terms = get_terms( 'pa_' . $attribute, $args );
                foreach ( $all_attribute_terms as $term ) :
                    if ( $term->term_parent == 0 ) : ?>
                        <option value="<?php echo $term->term_id; ?>" <?php selected( $term->term_id, $attribute_term_parent ); ?>><?php echo $term->name; ?></option>
                        <?php
                        $args2 = array(
                            'orderby'    => 'name',
                            'order'      => 'asc',
                            'hide_empty' => 0,
                            'include'    => '',
                            'parent'     => $term->term_id,
                        );

                        $sub_terms = get_terms( 'pa_' . $attribute, $args2 );
                        if ( $sub_terms ) :
                            foreach( $sub_terms as $sub_term ) : ?>
                                <option value="<?php echo $sub_term->term_id; ?>" <?php selected( $sub_term->term_id, $attribute_term_parent ); ?>>&#8212; <?php echo $sub_term->name; ?></option>
                            <?php endforeach;
                        endif;
                    endif;
                endforeach;
            ?></select>
        </p>
        <?php endif; ?>

        <p class="form-field">
            <label for="term_description"><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <textarea id="term_description" name="term_description" cols="5" rows="2" placeholder="Description (optional)"><?php echo isset( $attribute_term_description ) ? $attribute_term_description : ''; ?></textarea><br />
            <i><?php echo __('The description is not prominent by default; however, some themes may show it.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

    </div>

    <?php do_action( "{$taxonomy}_add_form_fields", $taxonomy ); ?>

    <input type="submit" value="Save" />

</form>
