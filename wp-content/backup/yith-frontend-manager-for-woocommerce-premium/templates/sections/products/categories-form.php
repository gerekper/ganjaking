<?php

defined( 'ABSPATH' ) or exit;

$edit = isset( $_GET['edit'] ) ? $_GET['edit'] : ( isset( $_POST['edit'] ) ?  $_POST['edit'] : '' );
$category = $edit != '' ? get_term_by( 'slug', $edit, 'product_cat') : NULL;

$category_id = isset( $category->term_id ) ? $category->term_id : '';
$category_name = isset( $category->name ) ? $category->name : '';
$category_slug = isset( $category->slug ) ? $category->slug : '';
$category_parent = isset( $category->parent ) ? $category->parent : '';
$category_description = isset( $category->description ) ? $category->description : '';
// $category_display_type = isset( $category->display_type ) ? $category->display_type : '';
// $category_thumbnail = isset( $category->thumbnail ) ? $category->thumbnail : '';

?>

<h2><?php echo $category ? __('Edit Product Category', 'yith-frontend-manager-for-woocommerce') : __('Add New Product Category', 'yith-frontend-manager-for-woocommerce'); ?></h2>

<form name="post" action="<?php echo $section_uri ?>" method="post" id="post">

    <?php if ( $category_slug != '' ) : ?>

        <input type="hidden" name="id" value="<?php echo $category_id; ?>">
        <input type="hidden" name="act" value="edit">
        <input type="hidden" name="edit" value="<?php echo $category_slug; ?>">

    <?php else : ?>

        <input type="hidden" name="act" value="new">

    <?php endif; ?>

    <div class="options_group">

        <p class="form-field">
            <label for="category_name"><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="category_name" value="<?php echo isset( $category_name ) ? $category_name : ''; ?>" id="category_name"><br />
            <i><?php echo __('Enter the name as you want it to appear on your site.', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <p class="form-field">
            <label for="category_slug"><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="category_slug" value="<?php echo isset( $category_slug ) ? $category_slug : ''; ?>" id="category_slug"><br />
            <i><?php echo __('The URL-friendly version of the name. It usually contains only lowercase letters, numbers, and hyphens.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>
    
        <p class="form-field">
            <label><?php echo __('Parent', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <select name="category_parent"><option value="-1">None</option><?php
                $args = array(
                    'taxonomy'     => 'product_cat',
                    'orderby'      => 'name',
                    'show_count'   => 0,
                    'pad_counts'   => 0,
                    'hierarchical' => 1,
                    'title_li'     => '',
                    'hide_empty'   => 0
                );
                $all_categories = get_categories( $args );
                foreach ( $all_categories as $cat ) :
                    if ( $cat->category_parent == 0 ) : ?>
                        <option value="<?php echo $cat->term_id; ?>" <?php selected( $cat->term_id, $category_parent ); ?>><?php echo $cat->name; ?></option>
                        <?php
                        $args2 = array(
                            'taxonomy'     => 'product_cat',
                            'child_of'     => 0,
                            'parent'       => $cat->term_id,
                            'orderby'      => 'name',
                            'show_count'   => 0,
                            'pad_counts'   => 0,
                            'hierarchical' => 1,
                            'title_li'     => '',
                            'hide_empty'   => 0
                        );
                        $sub_cats = get_categories( $args2 );
                        if ( $sub_cats ) :
                            foreach( $sub_cats as $sub_category ) : ?>
                                <option value="<?php echo $sub_category->term_id; ?>" <?php selected( $sub_category->term_id, $category_parent ); ?>>&#8212; <?php echo $sub_category->name; ?></option>
                            <?php endforeach;
                        endif;
                    endif;
                endforeach;
            ?></select>
        </p>

        <p class="form-field">
            <label for="category_description"><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <textarea id="category_description" name="category_description" cols="5" rows="2" placeholder="Description (optional)"><?php echo isset( $category_description ) ? $category_description : ''; ?></textarea><br />
            <i><?php echo __('The description is not prominent by default; however, some themes may show it.', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>
    
        <p class="form-field">
            <label><?php echo __('Display type', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <select id="display_type" name="display_type" class="postform">
                <option value=""><?php echo __('Default', 'yith-frontend-manager-for-woocommerce'); ?></option>
                <option value="products"><?php echo __('Products', 'yith-frontend-manager-for-woocommerce'); ?></option>
                <option value="subcategories"><?php echo __('Subcategories', 'yith-frontend-manager-for-woocommerce'); ?></option>
                <option value="both"><?php echo __('Both', 'yith-frontend-manager-for-woocommerce'); ?></option>
            </select>
        </p>
    
        <p class="form-field">
            <label><?php echo __('Product Image', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <span id="upload_image_input">
                <?php echo YITH_Frontend_Manager_Media::upload_image_input( 'term', $category_id ); ?>
            </span>
            <span class="clear"></span>
        </p>

    </div>

    <input type="submit" value="Save" />

</form>
