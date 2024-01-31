<div class="modal-overlay modal-override-product">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Select an overriding product', 'ali2woo');?></h3>
            <a class="modal-btn-close" href="#"></a>
        </div>
        <div class="modal-body">
            <div class="modal-override-product-loader a2w-load-container" style="padding:80px 0;"><div class="a2w-load-speeding-wheel"></div></div>
            <div class="modal-override-product-content">
                <div class="override-option" style="display:none">
                  <input type="checkbox" id="a2w-change-product-supplier" class="form-control">
                  <label for="a2w-change-product-supplier">
                    <strong><?php _e('Change product supplier', 'ali2woo');?></strong>
                    <div>If you select this option, all old variations will be kept and we will suggest that you change the supplier for each of the existing variations.</div>
                  </label>
                </div>
                <div class="a2w-warning" id="a2w-override-warning">Override will DELETE ALL of your existing product variants and replace them with variants from a new supplier.</div>
                <div class="override-error"></div>
                <div class="override-items">
                  <div class="override-item">
                    <div class="item-title" style="font-weight: bold;">Existing product</div>
                    <div class="item-body" style="font-weigth:bold">
                      <div class="input-block">
                        <select id="a2w-override-select-products" style="width:100%" class="form-control" data-placeholder="<?php _e('Search products', 'ali2woo');?>"></select>
                      </div>
                    </div>
                  </div>
                  <div class="a2w-icon-arrow-right override-delimiter"></div>
                  <div class="override-item">
                    <div class="item-title">Override with</div>
                    <div class="item-body override-with">

                    </div>
                  </div>
                </div>
                <div class="override-options" style="display:none">
                  <div class="override-option">
                    <input type="checkbox" id="a2w-override-title-description" class="form-control">
                    <label for="a2w-override-title-description">
                      <strong><?php _e('Override Title and Description', 'ali2woo');?></strong>
                      <div>If you select this option, we will replace your existing product title and description with the title/description from the overriding product.</div>
                    </label>
                  </div>
                  <div class="override-option">
                    <input type="checkbox" id="a2w-override-images" class="form-control">
                    <label for="a2w-override-images">
                      <strong><?php _e('Override Images', 'ali2woo');?></strong>
                      <div>If you select this option, we will DELETE ALL of your existing product images and will replace them with the images from the overriding product.</div>
                    </label>
                  </div>
                </div>
                <div class="override-order-variations" style="display:none">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default modal-close" type="button"><?php _e('Cancel', 'ali2woo');?></button>
            <button class="btn btn-success btn-icon-left do-override-product" type="button">
              <span class="title"><?php _e('Override', 'ali2woo');?></span>
              <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span>
              <div class="btn-loader-wrap"><div class="a2w-loader"></div></div>
            </button>
        </div>
    </div>
</div>

