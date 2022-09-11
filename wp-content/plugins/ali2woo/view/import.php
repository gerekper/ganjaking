<div class="a2w-content">    
    <div class="container">
        
        <?php include_once A2W()->plugin_path() . '/view/chrome_notify.php'; ?>        
        
        <?php if (A2W_Account::getInstance()->is_activated()):?>
        <div class="a2w-pc-warn"><p>You didn't activate Ali2Woo! Please open the Ali2Woo plugin <a href="<?php echo admin_url('admin.php?page=a2w_setting') ?>">settings</a> and input your purchase key.</p></div>
        <?php endif; ?>
        
        
        <div id="a2w-import-empty" class="panel panel-default margin-top"<?php if ($serach_query || count($product_list) !== 0): ?> style="display:none;"<?php endif; ?>>
            <div class="panel-body">
                <?php _e('Your Import List is Empty.', 'ali2woo'); ?>
                <br/>
                <br/>
                <?php _e('You can add products to this list from the “Search Products” page, or import products while browsing AliExpress using free chrome extension.', 'ali2woo'); ?>
            </div>
        </div>


        <div id="a2w-import-content"<?php if (!$serach_query && count($product_list) === 0): ?> style="display:none;"<?php endif; ?>>
            <div id="a2w-import-filter">
                <div class="grid grid_default grid_import-filter">
                    <div class="grid__col">
                        <h3><?php _e('Import List', 'ali2woo'); ?></h3>
                    </div>
                    <div class="grid__col grid__col_to-right">
                        <form method="GET" action="<?php echo admin_url('admin.php'); ?>">
                            <input type="hidden" name="page" value="a2w_import"/>
                            <table class="float-right">
                                <tr>
                                    <td class="padding-small-right">
                                        <select class="form-control" name="o" style="padding-right: 25px;">
                                            <?php foreach ($sort_list as $k => $v): ?><option value="<?php echo $k; ?>"<?php if ($sort_query === $k): ?> selected="selected"<?php endif; ?>><?php echo $v; ?></option><?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="padding-small-right"><input type="search" name="s" class="form-control" value="<?php echo $serach_query; ?>"></td>
                                    <td><input type="submit" class="btn btn-default" value="<?php _e('Search products', 'ali2woo'); ?>"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div id="a2w-import-actions">
                <div class="grid grid_default grid_import-actions">
                    <div class="grid__col">
                        <div class="container-flex" style="height: 32px;">
                            <div class="margin-right">
                                <input type="checkbox" class="check-all form-control"><span class="space-small-left"><strong><?php _e('Select All Products', 'ali2woo'); ?></strong></span>
                            </div>
                            <div class="action-with-check" style="display: none;">
                                <select class="form-control">
                                    <option value="0">Bulk Actions (0 selected)</option>
                                    <option value="remove"><?php _e('Remove from Import List', 'ali2woo'); ?></option>
                                    <option value="push"><?php _e('Push Products to Shop', 'ali2woo'); ?></option>
                                    <option value="link-category"><?php _e('Link to category', 'ali2woo'); ?></option>
                                </select>
                                <div class="loader"><div class="a2w-loader"></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="grid__col grid__col_to-right">
                        <a href="#" class="btn btn-default link_category_all"><?php _e('Link All products to category', 'ali2woo'); ?></a>
                        <a href="<?php echo admin_url('admin.php?page=' . $_REQUEST['page']) . '&action=delete_all'; ?>" class="btn btn-danger margin-small-left delete_all"><?php _e('Remove All Products', 'ali2woo'); ?></a>
                        <button type="button" class="btn btn-success no-outline btn-icon-left margin-small-left push_all">
                            <div class="btn-loader-wrap"><div class="a2w-loader"></div></div>
                            <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span><?php _e('Push All Products to Shop', 'ali2woo'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="panel panel-default margin-top"<?php if (count($product_list) !== 0): ?> style="display:none;"<?php endif; ?>>
                <div class="panel-body">
                    <?php _e('No products found.', 'ali2woo'); ?>
                </div>
            </div>

            <div class="a2w-product-import-list">
                <?php foreach ($product_list as $ind => $product): ?>
                    
                    <div class='row space-top'>
                        <div class='col-xs-12'>                            
                            <div class='product<?php echo isset($product['shipping_cost'])?" shiping_loaded":""?>' data-id="<?php echo $product['import_id']; ?>" data-country_from_list="<?php echo empty($product['shipping_from_country_list'])?'':implode(";",$product['shipping_from_country_list']); ?>" data-country_from="<?php echo isset($product['shipping_from_country'])?$product['shipping_from_country']:''; ?>" data-country_to="<?php echo isset($product['shipping_to_country'])?$product['shipping_to_country']:''; ?>">
                                <div class="a2w-row">
                                    <ul class="nav nav-tabs">
                                        <li class="select darker-background"><span class="for-checkbox"><input type="checkbox" class="form-control" value="<?php echo $product['import_id']; ?>"></span></li>
                                        <li class="active"><a href="#" rel="product"><?php _e('Product', 'ali2woo'); ?></a></li>
                                        <li> <a href="#" rel="description"><?php _e('Description', 'ali2woo'); ?></a></li>
                                        <li> <a href="#" rel="specs"><?php _e('Specs', 'ali2woo'); ?></a></li>
                                        <li <?php if (a2w_check_defined('A2W_DO_NOT_IMPORT_VARIATIONS')): ?>style="display:none;"<?php endif; ?>> <a href="#" rel="variants"><?php _e('Variants', 'ali2woo'); ?> <span class="badge badge-tab margin-small-left variants-count"><?php echo count($product['sku_products']['variations']) ?></span></a></li>
                                        <li> <a href="#" rel="images"><?php _e('Images', 'ali2woo'); ?></a></li> 
                                    </ul>
                                    <div class="actions">
                                        <span class="margin-small-right"><?php _e('External Id', 'ali2woo'); ?>: <b><?php echo $product['id']; ?></b></span>
                                        <div class="btn-group margin-small-right">
                                            <button type="button" class="btn btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php _e('Action', 'ali2woo'); ?> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if(empty($product['override_product_id'])):?>
                                                    <li><a href="#" class="product-card-override-product"><?php _e('Select Product to Override', 'ali2woo'); ?></a></li>
                                                <?php endif;?>
                                                <?php if(count($product['sku_products']['variations'])>1):?>
                                                    <li><a href="#" class="product-card-split-product"><?php _e('Split Product', 'ali2woo'); ?></a></li>
                                                <?php endif;?>
                                                <li><a href="<?php echo admin_url('admin.php?page=' . $_REQUEST['page']) . '&delete_id=' . $product['import_id']; ?>"><?php _e('Remove Product', 'ali2woo'); ?></a></li>
                                            </ul>
                                        </div>

                                        <button type="button" class="btn btn-success no-outline btn-icon-left margin-right post_import">
                                            <div class="btn-loader-wrap"><div class="a2w-loader"></div></div>
                                            <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span>
                                            <span class="btn-title"><?php echo isset($product['override_product_id'])?__('Override', 'ali2woo'):__('Push to Shop', 'ali2woo');?></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="tabs-content active" rel="product">
                                    <div class="product-info-container">
                                        <?php if(isset($product['override_product_id'])):?>
                                            <div class="a2w-warning">
                                                <?php echo $override_model->override_message($product['override_product_id'],$product['override_product_title']);?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="grid grid_default grid_product-info">
                                        <div class="grid__col grid__col_image">
                                            <div class="product-img">
                                                <img class="border-img lazy" src="<?php echo A2W()->plugin_url() . '/assets/img/blank_image.png'; ?>" data-id="<?php echo md5($product['thumb']);?>" data-original="<?php echo isset($product['tmp_edit_images'][md5($product['thumb'])]['attachment_url']) ? $product['tmp_edit_images'][md5($product['thumb'])]['attachment_url'] : a2w_image_url($product['thumb']); ?>" alt="<?php echo $product['title']; ?>">
                                                <?php if (isset($product['is_affiliate']) && $product['is_affiliate']): ?><div class="affiliate-icon"></div><?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="grid__col">
                                            <div class="container-flex">
                                                <div class="mr10 no-shrink ali-supplier"></div>
                                                <h3>
                                                    <a class="blue-color" href="<?php echo $product['url']; ?>" target="_blank">
                                                        <?php echo  isset($product['original_title'])?$product['original_title']:$product['title']; ?>
                                                    </a>
                                                    <span class="red-color"></span>
                                                </h3>
                                            </div>
                                            <?php if (!empty($product['seller_url'])):?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        by <a class="blue-color" href="<?php echo $product['seller_url']; ?>" target="_blank"><?php echo $product['seller_name']; ?></a>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                            <div class="product-edit">
                                                <div class="grid grid_form grid_3">
                                                    <div class="grid__col grid__col_fill">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('Change name', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <input type="text" class="field__input form-control title" maxlength="255" value="<?php echo $product['title']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid__col">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('SKU', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <input type="text" class="field__input form-control sku" maxlength="255" value="<?php echo empty($product['sku'])?$product['id']:$product['sku']; ?>"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid__col">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('Status', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <select class="field__input form-control select2 status" data-placeholder="<?php _e('Choose Status', 'ali2woo'); ?>">
                                                                    <option value="publish" <?php if ($product['product_status'] == "publish"): ?>selected="selected"<?php endif; ?>><?php _e('Publish'); ?></option>
                                                                    <option value="draft" <?php if ($product['product_status'] == "draft"): ?>selected="selected"<?php endif; ?>><?php _e('Draft'); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid__col">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('Type', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <select class="field__input form-control select2 type" data-placeholder="<?php _e('Choose Type', 'ali2woo'); ?>">
                                                                    <option value="simple" <?php if ($product['product_type'] == "simple"): ?>selected="selected"<?php endif; ?>><?php _ex('Simple/Variable Product', 'Setting option', 'ali2woo'); ?></option>
                                                                    <option value="external" <?php if ($product['product_type'] == "external"): ?>selected="selected"<?php endif; ?>><?php _ex('External/Affiliate Product', 'Setting option', 'ali2woo'); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid__col">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('Categories', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <select class="field__input form-control select2 categories" data-placeholder="<?php _e('Choose Categories', 'ali2woo'); ?>" multiple="multiple">
                                                                    <option></option>
                                                                    <?php foreach ($categories as $c): ?>
                                                                        <option value="<?php echo $c['term_id']; ?>"<?php if (in_array($c['term_id'], $product['categories'])): ?> selected="selected"<?php endif; ?>><?php echo str_repeat('- ', $c['level'] - 1) . $c['name']; ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid__col">
                                                        <div class="field field_default">
                                                            <label for="" class="field__label">
                                                                <?php _e('Tags', 'ali2woo'); ?>:
                                                            </label>
                                                            <div class="field__input-wrap">
                                                                <select name="tags" class="field__input form-control select2-tags tags" data-placeholder="<?php _e('Enter Tags', 'ali2woo'); ?>" multiple="multiple">
                                                                    <?php foreach ($product['tags'] as $tag): ?>
                                                                        <option value="<?php echo $tag; ?>" selected="selected"><?php echo $tag; ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-content" rel="description">
                                    <?php
                                    wp_editor($product['description'], $product['import_id'], array('editor_class' => 'a2w_description_editor', 'media_buttons' => false, 'editor_height' => 360/* , 'default_editor'=>'html' */));
                                    ?>
                                </div>
                                <div class="tabs-content" rel="specs">
                                    <?php $attribute_values_separator = a2w_get_setting('attribute_values_separator'); ?>
                                    <div class="specs-wrap">
                                        <table class="specs-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><?php _e('Name', 'ali2woo'); ?></th>
                                                    <th><?php _e('Value', 'ali2woo'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="specs-sortable">
                                            <?php foreach ($product['attribute'] as $i => $attr): ?>
                                                <tr class="checked-spec">
                                                    <td class="align-right"><input type="checkbox" value="1" class="check-spec-delete form-control" checked="checked"></td>
                                                    <td class="column-handle ui-sortable-handle"></td>
                                                    <td class="spec-name"><input type="text" class="form-control" value="<?php echo $attr['name']; ?>"></td>
                                                    <td class="spec-value"><input type="text" class="form-control" value="<?php echo implode($attribute_values_separator." ", $attr['value']); ?>"></td>
                                                    <td class="spec-actions"><a href="#" class="del-spec close-icon"></a></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <div class="specs-actions">
                                            <a href="#" class="add-spec">+ Add spec</a>
                                            <span class="btn btn-danger margin-small-left btn-delete-specs" data-product-id="<?php echo $product['import_id']; ?>">DELETE</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-content" rel="variants" <?php if (a2w_check_defined('A2W_DO_NOT_IMPORT_VARIATIONS')): ?>style="display:none;"<?php endif; ?>>
                                    <div id="variants-images-container-<?php echo $product['import_id']; ?>" class="variants-wrap">
                                        <div class="variants-actions">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <label for="disable-price-change-<?php echo $product['import_id']; ?>"><?php _e('Prevent product price from auto-updating', 'ali2woo'); ?></label>
                                                        <input class="form-control disable-var-price-change" type="checkbox" id="disable-price-change-<?php echo $product['import_id']; ?>" <?php if (isset($product['disable_var_price_change']) && $product['disable_var_price_change']): ?> checked="checked"<?php endif; ?>>
                                                        <div class="info-box" data-toggle="tooltip" title="If you choose to prevent product price from auto-updating, it will not be changed regardless of the auto-updating settings in your account, or price changes made by the supplier. You will only be able to change your price manually."></div>
                                                    </td>
                                                    <td>
                                                        <label for="disable-quantity-change-<?php echo $product['import_id']; ?>"><?php _e('Prevent product quantity from auto-updating', 'ali2woo'); ?></label>
                                                        <input class="form-control disable-var-quantity-change" type="checkbox" id="disable-quantity-change-<?php echo $product['import_id']; ?>" <?php if (isset($product['disable_var_quantity_change']) && $product['disable_var_quantity_change']): ?> checked="checked"<?php endif; ?>>
                                                        <div class="info-box" data-toggle="tooltip" title="If you choose to prevent product quantity from auto-updating, it will not be changed regardless of the auto-updating settings in your account, or quantity changes made by the supplier. You will only be able to change your quantity manually."></div>
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>        

                                        <table class="variants-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="nowrap"><input type="checkbox" class="check-all-var form-control" <?php if (!$product['skip_vars']): ?> checked="checked"<?php endif; ?>><?php _e('Use all', 'ali2woo'); ?></th>
                                                    <th><?php _e('SKU', 'ali2woo'); ?></th>
                                                    <?php foreach ($product['sku_products']['attributes'] as $attr): ?>
                                                        <th>
                                                            <input type="text" class="form-control attr-name" data-id="<?php echo $attr['id']; ?>" value="<?php echo $attr['name']; ?>">
                                                        </th>
                                                    <?php endforeach; ?>
                                                    <th><?php _e('Cost', 'ali2woo'); ?></th>                                                    
                                                    <th><?php _e('Shipping', 'ali2woo'); ?></th>                                                    
                                                    <th><?php _e('Price', 'ali2woo'); ?></th>
                                                    <th><?php _e('Regular Price', 'ali2woo'); ?></th>
                                                    <th><?php _e('Profit', 'ali2woo'); ?></th>
                                                    <th><?php _e('Inventory', 'ali2woo'); ?></th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <?php foreach ($product['sku_products']['attributes'] as $attr): ?>
                                                        <td data-attr-id="<?php echo $attr['id']; ?>">
                                                            <div class="price-edit-selector edit-price large rename-attr">
                                                                <div class="price-box-top">
                                                                    <div class="container-flex">
                                                                        <div>
                                                                            <div class="slt"><select class="form-control"></select></div>
                                                                        </div>
                                                                        <div>
                                                                            <input type="text" class="form-control" placeholder="New name">
                                                                        </div>
                                                                        <div>
                                                                            <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                        </div>
                                                                        <div>
                                                                            <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <?php _e('Change Attr', 'ali2woo'); ?> <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-sm">
                                                                    <li><a href="javascript:void(0)" class="rename-attr-value"><?php _e('Rename attributes', 'ali2woo'); ?></a></li>
                                                                </ul>
                                                            </div>  
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td></td>
                                                    <!-- <td colspan="<?php echo (4 + count($product['sku_products']['attributes'])); ?>"></td> -->
                                                    <td>
                                                        <a href="#" class="shipping-country"><?php echo isset($product['shipping_to_country'])? (isset($product['shipping_to_country_name'])?$product['shipping_to_country_name']:$product['shipping_to_country']):__('Select country', 'ali2woo'); ?></a>
                                                    </td>
                                                    <td>
                                                        <div class="price-edit-selector edit-price">
                                                            <div class="price-box-top">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control" placeholder="Enter Value">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Prices', 'ali2woo'); ?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm">
                                                                <li><a href="javascript:void(0)" class="set-new-value"><?php _e('Set New Value', 'ali2woo'); ?></a></li>
                                                                <li><a href="javascript:void(0)" class="multiply-by-value"><?php _e('Multiply by', 'ali2woo'); ?></a></li>
                                                            </ul>
                                                        </div>  
                                                    </td>
                                                    <td>
                                                        <div class="price-edit-selector edit-regular-price">
                                                            <div class="price-box-top">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control" placeholder="Enter Value">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Prices', 'ali2woo'); ?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm">
                                                                <li><a href="javascript:void(0)" class="set-new-value"><?php _e('Set New Value', 'ali2woo'); ?></a></li>
                                                                <li><a href="javascript:void(0)" class="multiply-by-value"><?php _e('Multiply by', 'ali2woo'); ?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <div class="price-edit-selector edit-quantity">
                                                            <div class="price-box-top price-box-right">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control simple-value" placeholder="<?php _e('Enter Value', 'ali2woo'); ?>">
                                                                        <input type="text" class="form-control random-from" placeholder="<?php _e('From', 'ali2woo'); ?>">
                                                                        <input type="text" class="form-control random-to" placeholder="<?php _e('To', 'ali2woo'); ?>">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Inv.', 'ali2woo'); ?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm dropdown-right">
                                                                <li><a href="javascript:void(0)" class="set-new-quantity"><?php _e('Set New Value', 'ali2woo'); ?></a></li>
                                                                <li><a href="javascript:void(0)" class="random-value"><?php _e('Random Value', 'ali2woo'); ?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($product['sku_products']['variations'] as $i => $var): ?>
                                                    <tr data-id="<?php echo $var['id']; ?>" class="var_data">
                                                        <td>
                                                            <input type="checkbox" value="1" class="check-var form-control" <?php if (!in_array($var['id'], $product['skip_vars'])): ?> checked="checked"<?php endif; ?>>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($var['image'])): ?><img class="border-img lazy-in-container" style="max-width: 100px; max-height: 100px; margin: 5px" src="<?php echo A2W()->plugin_url() . '/assets/img/blank_image.png'; ?>" data-id="<?php echo md5($var['image']);?>" data-original="<?php echo isset($product['tmp_edit_images'][md5($var['image'])]['attachment_url']) ? $product['tmp_edit_images'][md5($var['image'])]['attachment_url'] : a2w_image_url($var['image']); ?>" data-img="<?php echo isset($product['tmp_edit_images'][md5($var['image'])]['attachment_url']) ? $product['tmp_edit_images'][md5($var['image'])]['attachment_url'] : a2w_image_url($var['image']); ?>" data-toggle="popover-hover"><?php endif; ?>

                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control sku" value="<?php echo $var['sku']; ?>">
                                                        </td>
                                                        <?php foreach ($var['attributes'] as $j => $av): ?>
                                                            <td data-attr-id="<?php echo explode(":", $av)[0]; ?>"><input type="text" class="form-control attr" data-id="<?php echo $av; ?>" value="<?php echo isset($var['attributes_names'][$j]) ? $var['attributes_names'][$j] : ''; ?>"></td>
                                                        <?php endforeach; ?>
                                                        <td style="white-space: nowrap;" class="external-price" data-value="<?php echo $var['price']; ?>"><?php echo $localizator->getLocaleCurr($var['currency']); ?><?php echo $var['price']; ?></td>
                                                        <td style="white-space: nowrap;" class="external-shipping" data-value="<?php echo isset($product['shipping_cost'])?floatval($product['shipping_cost']):"0"?>" data-currency="<?php echo $localizator->getLocaleCurr($var['currency']); ?>"><?php echo isset($product['shipping_cost'])?$localizator->getLocaleCurr($var['currency']).floatval($product['shipping_cost']):"N/A"?></td>
                                                        <td>
                                                            <input type="text" class="form-control price" value="<?php echo $var['calc_price']; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control regular_price" value="<?php echo $var['calc_regular_price']; ?>">
                                                        </td>
                                                        <td class="profit"><?php echo $localizator->getLocaleCurr($var['currency']); ?><span class="value"><?php echo round($var['calc_price'] - $var['price'], 2); ?></span></td>
                                                        <td><input type="text" class="form-control quantity" value="<?php echo $var['quantity']; ?>"></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <script>
                                        (function ($) {
                                            $(".variants-wrap img.lazy-in-container").lazyload({effect: "fadeIn", skip_invisible: true, container: $("#variants-images-container-<?php echo $product['import_id']; ?>")});
                                        })(jQuery);
                                    </script>

                                </div>
                                <div class="tabs-content" rel="images">
                                    <div id="images-container-<?php echo $product['import_id']; ?>" class="images-wrap">
                                        <?php if (!empty($product['gallery_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-gallery-image-id-<?php echo $product['import_id'];?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-gallery-image-id-<?php echo $product['import_id'];?>"><?php _e('Gallery images', 'ali2woo'); ?></label>
                                            </div>
                                            <div class="grid grid_default grid_4 gallery_images">
                                                <?php foreach ($product['gallery_images'] as $img_id => $image): ?>
                                                    <div class="grid__col">
                                                        <div id="<?php echo $img_id; ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif; ?>">
                                                            <img class="lazy-in-container" src="<?php echo A2W()->plugin_url() . '/assets/img/blank_image.png'; ?>" data-id="<?php echo $img_id;?>" data-original="<?php echo isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : a2w_image_url($image); ?>"/>
                                                            <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                            <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif; ?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                            <?php if (isset($product['tmp_move_images'][$img_id])): ?>
                                                                <div class="cancel-image-action"><a href="#" data-action="move#<?php echo $product['tmp_move_images'][$img_id]; ?>"><?php _e('Cancel move', 'ali2woo'); ?></a></div>
                                                            <?php elseif (isset($product['tmp_copy_images'][$img_id])): ?>
                                                                <div class="cancel-image-action"><a href="#" data-action="copy#<?php echo $product['tmp_copy_images'][$img_id]; ?>"><?php _e('Cancel copy', 'ali2woo'); ?></a></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($product['variant_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-variant-image-id-<?php echo $product['import_id'];?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-variant-image-id-<?php echo $product['import_id'];?>"><?php _e('Variations images', 'ali2woo'); ?></label>
                                            </div>
                                            <div class="grid grid_default grid_4 variant_images">
                                                <?php foreach ($product['variant_images'] as $img_id => $image): ?>
                                                    <div class="grid__col">
                                                        <div id="<?php echo $img_id; ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif; ?>">
                                                            <img class="lazy-in-container" src="<?php echo A2W()->plugin_url() . '/assets/img/blank_image.png'; ?>" data-id="<?php echo $img_id;?>" data-original="<?php echo isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : a2w_image_url($image); ?>"/>
                                                            <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                            <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif; ?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($product['description_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-description-image-id-<?php echo $product['import_id'];?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-description-image-id-<?php echo $product['import_id'];?>"><?php _e('Description images', 'ali2woo'); ?></label>
                                                <div class="images-action">
                                                    <select class="form-control description-images-action">
                                                        <option value="" disabled="disabled" selected="selected"><?php _e('Action', 'ali2woo'); ?></option>
                                                        <option value="move"><?php _e('Move checked images to gallery', 'ali2woo'); ?></option>
                                                        <option value="copy"><?php _e('Copy checked images to gallery', 'ali2woo'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid_default grid_4 description_images">
                                                <?php foreach ($product['description_images'] as $img_id => $image): ?>
                                                    <?php if (!isset($product['tmp_move_images'][$img_id])): ?>
                                                        <div class="grid__col">
                                                            <div id="<?php echo $img_id; ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif; ?>">
                                                                <img class="lazy-in-container" src="<?php echo A2W()->plugin_url() . '/assets/img/blank_image.png'; ?>" data-id="<?php echo $img_id;?>" data-original="<?php echo isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : a2w_image_url($image); ?>"/>
                                                                <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                                <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif; ?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <script>
                                        (function ($) {
                                            $(".images-wrap img.lazy-in-container").lazyload({effect: "fadeIn", skip_invisible: true, container: $("#images-container-<?php echo $product['import_id']; ?>")});
                                        })(jQuery);
                                    </script>
                                </div>
                            </div>                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php include_once 'includes/confirm_modal.php'; ?>
            <?php include_once 'includes/shipping_modal.php'; ?>
            <?php include_once 'includes/category_modal.php'; ?>
            <?php include_once 'includes/split_modal.php'; ?>
            <?php include_once 'includes/override_modal.php'; ?>
        </div>


        <?php if ($paginator['total_pages'] > 1): ?>
            <div class="pagination">
                <div class="pagination__wrapper">
                    <ul class="pagination__list">
                        <li <?php if (1 == $paginator['cur_page']): ?>class="disabled"<?php endif; ?>><a href="<?php echo admin_url('admin.php?page=a2w_import&cur_page=' . ($paginator['cur_page'] - 1)) ?>">«</a></li>
                        <?php foreach ($paginator['pages_list'] as $p): ?>
                            <?php if ($p): ?>
                                <?php if ($p == $paginator['cur_page']): ?>
                                    <li class="active"><span><?php echo $p; ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo admin_url('admin.php?page=a2w_import&cur_page=' . $p) ?>"><?php echo $p; ?></a></li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li class="disabled"><span>...</span></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <li <?php if ($paginator['total_pages'] <= $paginator['cur_page']): ?>class="disabled"<?php endif; ?>><a href="<?php echo admin_url('admin.php?page=a2w_import&cur_page=' . ($paginator['cur_page'] + 1)) ?>">»</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        (function ($) {
            if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); } 

            $(function () {
                $( ".specs-sortable" ).sortable({handle: ".column-handle"});
                $( ".specs-sortable" ).disableSelection();
            });
            
            $(".product .select2").select2();
            $('.product .select2-tags').select2({tags: true, tokenSeparators: [','], ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            action: 'a2w_search_tags',
                            search: params.term,
                            page: params.page || 1
                        }
                        return query;
                    }
                }});
            //$('.dropdown-toggle').dropdown();
            $(".set-category-dialog .select2").select2({width: '100%'});

            $('.product .nav-tabs a').click(function () {
                $(this).parents('.product').children("div.tabs-content").removeClass("active");
                $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').addClass("active");

                $(this).parents('.product').find(".nav-tabs li").removeClass("active");
                $(this).parents('.product').find('.nav-tabs li a[rel="' + $(this).attr("rel") + '"]').closest('li').addClass("active");

                if ($(this).attr("rel") === 'images') {
                    $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').find('img.lazy-in-container').lazyload();
                }
                
                if ($(this).attr("rel") === 'variants') {
                    $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').find('img.lazy-in-container').lazyload();
                    if(!$(this).parents(".product").hasClass('shiping_loaded')){
                        a2w_update_product_shipping_info($(this).parents(".product").attr('data-id'));
                        $(this).parents(".product").addClass('shiping_loaded');
                    }
                }
                return false;
            });
        })(jQuery);
    </script>
    
</div>

