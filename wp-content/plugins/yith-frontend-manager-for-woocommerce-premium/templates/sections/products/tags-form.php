<?php

defined( 'ABSPATH' ) or exit;

$edit = isset( $_GET['edit'] ) ? $_GET['edit'] : ( isset( $_POST['edit'] ) ?  $_POST['edit'] : '' );
$tag = $edit != '' ? get_term_by( 'slug', $edit, 'product_tag') : NULL;

$tag_id = isset( $tag->term_id ) ? $tag->term_id : '';
$tag_name = isset( $tag->name ) ? $tag->name : '';
$tag_slug = isset( $tag->slug ) ? $tag->slug : '';
$tag_description = isset( $tag->description ) ? $tag->description : '';

?>

<h2><?php echo $tag ? __('Edit Product Tag', 'yith-frontend-manager-for-woocommerce') : __('Add New Product Tag', 'yith-frontend-manager-for-woocommerce'); ?></h2>

<form name="post" action="<?php echo $section_uri; ?>" method="post" id="post">

    <?php if ( $tag_slug != '' ) : ?>

        <input type="hidden" name="id" value="<?php echo $tag_id; ?>">
        <input type="hidden" name="act" value="edit">
        <input type="hidden" name="edit" value="<?php echo $tag_slug; ?>">

    <?php else : ?>

        <input type="hidden" name="act" value="new">

    <?php endif; ?>

    <div class="options_group">

        <p class="form-field">
            <label for="tag_name"><?php echo __('Name', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="tag_name" value="<?php echo isset( $tag_name ) ? $tag_name : ''; ?>" id="tag_name"><br />
            <i><?php echo __('Enter the name as you want it to appear on your site..', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <p class="form-field">
            <label for="tag_slug"><?php echo __('Slug', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <input type="text" name="tag_slug" value="<?php echo isset( $tag_slug ) ? $tag_slug : ''; ?>" id="tag_slug"><br />
            <i><?php echo __('The “slug” is the URL-friendly version of the name. It usually contains only lowercase letters, numbers, and hyphens.',
                    'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

        <p class="form-field">
            <label for="tag_description"><?php echo __('Description', 'yith-frontend-manager-for-woocommerce'); ?></label>
            <textarea id="tag_description" name="tag_description" cols="5" rows="2" placeholder="Description (optional)"><?php echo isset( $tag_description ) ? $tag_description : ''; ?></textarea><br />
            <i><?php echo __('The description is not prominent by default; however, some themes may show it.', 'yith-frontend-manager-for-woocommerce'); ?></i>
        </p>

    </div>

    <input type="submit" value="Save" />

</form>