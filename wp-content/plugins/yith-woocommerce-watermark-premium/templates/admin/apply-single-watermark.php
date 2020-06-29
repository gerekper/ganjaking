<?php
global $post;
?>
<input type="button" class="button button-secondary" id="ywcwat_apply_product" data-product_id="<?php echo $post->ID;?>" value="<?php _e( 'Apply Watermark','yith-woocommerce-watermark' );?>"/>
<img src="<?php echo YWCWAT_ASSETS_URL;?>/images/icon-loading.gif" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />