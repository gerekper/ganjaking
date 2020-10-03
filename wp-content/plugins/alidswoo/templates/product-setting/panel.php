<?php
/** @var ads\module\ProductSetting $view */
?>

<div id="setting-product-sidebar" class="setting-product-sidebar">
    <div class="close-setting">&times;</div>
    <input type="hidden" name="adsw_post_id" value="<?php echo $view->post_id; ?>">
    <div class="setting-product-panel">
        <iframe id="ff" src="/setting?post_id=<?php echo $view->post_id;?>" frameborder="0" style="height: 100%;">
        </iframe>
        <div class="setting-product-footer">
            <a href="javascript:;" class="js-product-trash"><i class="trash"></i><?php _e('Move to trash', 'adsw');?></a>
        </div>
    </div>
    <div class="setting-product-active"></div>
</div>
<div id="setting-product-overlay"></div>