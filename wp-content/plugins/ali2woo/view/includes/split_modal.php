<div class="modal-overlay modal-split-product">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Split Product', 'ali2woo');?></h3>
            <a class="modal-btn-close" href="#"></a>
        </div>
        <div class="modal-body">
            <div class="modal-split-product-loader a2w-load-container" style="padding:80px 0;"><div class="a2w-load-speeding-wheel"></div></div>
            <div class="modal-split-product-content">
                <div class="split-title">
                    <div class="split-name">Select which option you want to use for splitting the product</div>
                    <div>...or <a href="#" class="split-mode">Split manually</a></div>
                </div>
                <div class="split-content">
                    <b>Split by</b>:
                    <div class="split-attributes"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default modal-close" type="button"><?php _e('Cancel', 'ali2woo');?></button>
            <button style="display:none" class="btn btn-success do-split-product attributes" type="button">
                <?php _e('Split to <span class="btn-split-count">0</span> Products', 'ali2woo');?>
            </button>
            <button style="display:none" class="btn btn-success do-split-product manual" type="button">
                <?php _e('Split product', 'ali2woo');?>
            </button>
        </div>
    </div>
</div>

