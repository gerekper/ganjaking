<?php

/** @var adsw\module\ProductSetting $view */
$view = adsw\module\Create::$productSetting;
$view->setPostId( intval($_GET['post_id'] ) );
?>
<script type="text/javascript">
    /* <![CDATA[ */
    var alidAjax = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
    /* ]]> */
</script>
<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/icons/fontawesome/style.css" rel="stylesheet">
<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/bootstrap/bootstrap<?php echo ADSW_MIN; ?>.css" rel="stylesheet">
<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/bootstrap/bootstrap-select<?php echo ADSW_MIN; ?>.css" rel="stylesheet">
<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/datepicker/date<?php echo ADSW_MIN; ?>.css" rel="stylesheet">

<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/alids-global<?php echo ADSW_MIN; ?>.css" rel="stylesheet">
<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/alids-main<?php echo ADSW_MIN; ?>.css" rel="stylesheet">

<link href="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>css/front/setting-product/setting<?php echo ADSW_MIN; ?>.css" rel="stylesheet">

<script type="text/javascript" src="<?php echo get_site_url();?>/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/bootstrap/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/bootstrap/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/datepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/datepicker/daterangepicker<?php echo ADSW_MIN; ?>.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/global/uniform.min.js"></script>
<script type="text/javascript" src="<?php echo ADSW_URL . ADSW_ASSETS_PATH; ?>js/front/setting-product/setting<?php echo ADSW_MIN; ?>.js"></script>

<div class="setting-product-body">
    <input type="hidden" name="adsw_post_id" value="<?php echo $view->post_id; ?>">

    <div class="box box-title">
        <label for=""><?php _e('Product title', 'adsw');?>:</label>
        <div class="box box-flex">
            <input type="text" value="<?php echo $view->title(); ?>" id="product_title" name="product_title">
            <button type="button" class="new-btn js-toggle-title"><?php _e('Save', 'adsw');?></button>
        </div>
    </div>

    <div class="box box-permalink">
        <label for=""><?php _e('Permalink', 'adsw');?>:</label>
        <div class="box box-flex">
            <input type="text" value="<?php echo $view->permalink(); ?>" id="product_permalink" name="product_permalink">
            <button type="button" class="new-btn js-toggle-permalink"><?php _e('Save', 'adsw');?></button>
        </div>
    </div>

    <div class="box group-status">
        <label for=""><?php _e('Status', 'adsw');?>:</label>
        <select name="post_status" id="post_status">
            <?php $view->post_status(); ?>
        </select>
    </div>

    <div class="box">
        <label for=""><?php _e('Published on', 'adsw');?>:</label>
        <div class="">
            <div class="setting-product-date-published">
                <span><?php echo $view->publishedTimeFormat(); ?></span><i class="calendar"></i>
            </div>
            <input class="setting-product-published" style="display: none" type="text" value="<?php echo $view->publishedTime(); ?>">
        </div>
    </div>

    <div class="box">
        <label for=""><?php _e('Product category', 'adsw');?>:</label>
        <div class="box box-flex">
            <div class="multi-select-full flex-1">
                <?php echo $view->categoryMulti('product_category'); ?>
            </div>
            <button type="button" class="new-btn js-toggle-new"><?php _e('New', 'adsw');?></button>
        </div>

        <div class="box-new-cat hidden">
            <div class="box category-name">
                <label for=""><?php _e('Category name', 'adsw');?>:</label>
                <input type="text" value="" id="new_product_category" name="new_product_category">
            </div>
            <div class="box box-parent_product_category">
                <label for=""><?php _e('Parent category', 'adsw');?>:</label>
                <div class="box-flex">
                    <div class="flex-1">
                        <?php echo $view->category('parent_product_category', ''); ?>
                    </div>
                </div>
                <button type="button" class="js-add-new-cat add-new-cat flex-1"><?php _e('Add a new category', 'adsw');?></button>
                <div class="box-new-cat-close">
                    <a href="javascript:;" class="js-new-cat-close new-cat-close"><?php _e('Hide', 'adsw');?></a>
                </div>

            </div>
        </div>

    </div>
</div>
