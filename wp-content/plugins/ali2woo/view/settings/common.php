<form method="post">

    <input type="hidden" name="setting_form" value="1"/>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Purchase Settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="row-comments">
                        You need to log into your CodeCanyon account and go to your "Downloads" page. Locate this plugin you purchased in your "Downloads" list and click on the "License Certificate" link next to the download link. After you have downloaded the certificate you can open it in a text editor such as Notepad and copy the Item Purchase Code.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_item_purchase_code">
                        <strong><?php _ex('Item Purchase Code', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title='Need for everything.'></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="<?php echo ( a2w_check_defined('A2W_HIDE_KEY_FIELDS') ? 'password' : 'text'); ?>" class="form-control small-input" id="a2w_item_purchase_code" name="a2w_item_purchase_code" value="<?php echo esc_attr(a2w_get_setting('item_purchase_code')); ?>"/>
                    </div>
                </div>
            </div>

            <div class="a2w_purchase_code_info" style="display:none">
                <div class="row">
                    <div class="col-md-4">
                        <strong><?php _e('CodeCanyon support', 'ali2woo'); ?></strong>
                        <div class="info-box" data-toggle="tooltip" title='CodeCanyon support'></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group input-block no-margin">
                            <span class="supported_until"></span> <a href="https://help.market.envato.com/hc/en-us/articles/207886473-Extending-and-Renewing-Item-Support" target="_blank">extend/renew your support</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <strong><?php _e('Package', 'ali2woo'); ?></strong>
                        <div class="info-box" data-toggle="tooltip" title='Information about the current package.'></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group input-block no-margin">
                            <?php $purchase_code_param = a2w_check_defined('A2W_HIDE_KEY_FIELDS')?"":"?purchase_code=".esc_attr(a2w_get_setting('item_purchase_code')); ?>
                            <span class="tariff_name"></span>, <a href="https://ali2woo.com/packages/<?php echo $purchase_code_param; ?>" target="_blank">change package</a>
                        </div>
                        <div class="daily_limits form-group input-block no-margin"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Import Settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Language', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("It's applied to Product title, description, attributes and reviews", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $cur_language = a2w_get_setting('import_language'); ?>
                        <select name="a2w_import_language" id="a2w_import_language" class="form-control small-input">
                            <option value="en" <?php if ($cur_language == "en"): ?>selected="selected"<?php endif; ?>>English</option>
                            <option value="ar" <?php if ($cur_language == "ar"): ?>selected="selected"<?php endif; ?>>Arabic</option>
                            <option value="de" <?php if ($cur_language == "de"): ?>selected="selected"<?php endif; ?>>German</option>
                            <option value="es" <?php if ($cur_language == "es"): ?>selected="selected"<?php endif; ?>>Spanish</option>
                            <option value="fr" <?php if ($cur_language == "fr"): ?>selected="selected"<?php endif; ?>>French</option>
                            <option value="it" <?php if ($cur_language == "it"): ?>selected="selected"<?php endif; ?>>Italian</option>
                            <option value="pl" <?php if ($cur_language == "pl"): ?>selected="selected"<?php endif; ?>>Polish</option>
                            <option value="ja" <?php if ($cur_language == "ja"): ?>selected="selected"<?php endif; ?>>Japanese</option>
                            <option value="ko" <?php if ($cur_language == "ko"): ?>selected="selected"<?php endif; ?>>Korean</option>
                            <option value="nl" <?php if ($cur_language == "nl"): ?>selected="selected"<?php endif; ?>>Notherlandish (Dutch)</option>
                            <option value="pt" <?php if ($cur_language == "pt"): ?>selected="selected"<?php endif; ?>>Portuguese (Brasil)</option>
                            <option value="ru" <?php if ($cur_language == "ru"): ?>selected="selected"<?php endif; ?>>Russian</option>
                            <option value="th" <?php if ($cur_language == "th"): ?>selected="selected"<?php endif; ?>>Thai</option>    
                            <option value="id" <?php if ($cur_language == "id"): ?>selected="selected"<?php endif; ?>>Indonesian</option>            
                            <option value="he" <?php if ($cur_language == "he"): ?>selected="selected"<?php endif; ?>>Hebrew</option>    
                            <option value="tr" <?php if ($cur_language == "tr"): ?>selected="selected"<?php endif; ?>>Turkish</option>
                            <option value="vi" <?php if ($cur_language == "vi"): ?>selected="selected"<?php endif; ?>>Vietnamese</option>
                        </select>                         
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Currency', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default currency that used on a product import", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $cur_a2w_local_currency = strtoupper(a2w_get_setting('local_currency')); ?>
                        <select name="a2w_local_currency" id="a2w_local_currency" class="form-control small-input">
                            <?php foreach($currencies as $code=>$name):?><option value="<?php echo $code;?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option><?php endforeach; ?>
                            <?php if(!empty($custom_currencies)):?>
                            <?php foreach($custom_currencies as $code=>$name):?><option value="<?php echo $code;?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option><?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_default_product_type">
                        <strong><?php _ex('Default product type', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default product type", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $default_product_type = a2w_get_setting('default_product_type'); ?>
                        <select name="a2w_default_product_type" id="a2w_default_product_type" class="form-control small-input">
                            <option value="simple" <?php if ($default_product_type == "simple"): ?>selected="selected"<?php endif; ?>><?php _ex('Simple/Variable Product', 'Setting option', 'ali2woo'); ?></option>
                            <option value="external" <?php if ($default_product_type == "external"): ?>selected="selected"<?php endif; ?>><?php _ex('External/Affiliate Product', 'Setting option', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_default_product_status">
                        <strong><?php _ex('Default product status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default product type", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $default_product_status = a2w_get_setting('default_product_status'); ?>
                        <select name="a2w_default_product_status" id="a2w_default_product_status" class="form-control small-input">
                            <option value="publish" <?php if ($default_product_status == "publish"): ?>selected="selected"<?php endif; ?>><?php _e('Publish'); ?></option>
                            <option value="draft" <?php if ($default_product_status == "draft"): ?>selected="selected"<?php endif; ?>><?php _e('Draft'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_not_import_attributes">
                        <strong><?php _e('Not import specifications', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Not import specifications', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_not_import_attributes" name="a2w_not_import_attributes" value="yes" <?php if (a2w_get_setting('not_import_attributes')): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_not_import_description">
                        <strong><?php _e('Not import description', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Not import description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_not_import_description" name="a2w_not_import_description" value="yes" <?php if (a2w_get_setting('not_import_description')): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_not_import_description_images">
                        <strong><?php _e("Don't import images from the description", 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e("Don't import images from the description", 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_not_import_description_images" name="a2w_not_import_description_images" value="yes" <?php if (a2w_get_setting('not_import_description_images')): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_use_external_image_urls">
                        <strong><?php _ex('Use external image urls', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Use external image urls', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_use_external_image_urls" name="a2w_use_external_image_urls" value="yes" <?php if (a2w_get_setting('use_external_image_urls')): ?>checked<?php endif; ?>/>
                    </div>
                    <div id="a2w_load_external_image_block" class="form-group input-block no-margin" <?php if (a2w_get_setting('use_external_image_urls')): ?>style="display: none;"<?php endif; ?>>
                        <input class="btn btn-default load-images" disabled="disabled" type="button" value="<?php _e('Load images', 'ali2woo'); ?>"/>
                        <div id="a2w_load_external_image_progress"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_use_random_stock">
                        <strong><?php _ex('Use random stock value', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Use random stock value', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_use_random_stock" name="a2w_use_random_stock" value="yes" <?php if (a2w_get_setting('use_random_stock')): ?>checked<?php endif; ?>/>
                    </div>
                    <div id="a2w_use_random_stock_block" class="form-group input-block no-margin" <?php if (!a2w_get_setting('use_random_stock')): ?>style="display: none;"<?php endif; ?>>
                        <?php _e('From', 'ali2woo'); ?> <input type="text" style="max-width: 60px;" class="form-control" id="a2w_use_random_stock_min" name="a2w_use_random_stock_min" value="<?php echo esc_attr(a2w_get_setting('use_random_stock_min')); ?>">
                        <?php _e('To', 'ali2woo'); ?> <input type="text" style="max-width: 60px;" class="form-control" id="a2w_use_random_stock_max" name="a2w_use_random_stock_max" value="<?php echo esc_attr(a2w_get_setting('use_random_stock_max')); ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">  
                    <label for="a2w_background_import">
                        <strong><?php _ex('Import in the background', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Products will be imported in the background mode, make sure you CRON is enabled.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_background_import" name="a2w_background_import" value="yes" <?php if (a2w_get_setting('background_import')): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">  
                    <label for="a2w_convert_attr_case">
                        <strong><?php _ex('Convert case of attributes and their values', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Products may come with different text case of attributes and their values. ', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $convert_attr_case = a2w_get_setting('convert_attr_case'); ?>
                        <select name="a2w_convert_attr_case" id="a2w_convert_attr_case" class="form-control small-input">
                            <option value="original" <?php if ($convert_attr_case == "original"): ?>selected="selected"<?php endif; ?>><?php _e('Keep original case'); ?></option>
                            <option value="lower" <?php if ($convert_attr_case == "lower"): ?>selected="selected"<?php endif; ?>><?php _e('Lower case'); ?></option>
                            <option value="sentence" <?php if ($convert_attr_case == "sentence"): ?>selected="selected"<?php endif; ?>><?php _e('Sentence case'); ?></option>
                        </select>
                    </div>
                </div>
            </div>



        </div>
    </div>
   
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Order Fulfillment settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_delivered_order_status">
                        <strong><?php _ex('Delivered Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Change order status when all order items have been delivered.", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $delivered_order_status = a2w_get_setting('delivered_order_status'); ?>
                        <select name="a2w_delivered_order_status" id="a2w_delivered_order_status" class="form-control small-input">
                            <option value=""><?php _ex('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach($order_statuses as $os_key=>$os_value):?>
                            <option value="<?php echo $os_key;?>" <?php if ($delivered_order_status == $os_key): ?>selected="selected"<?php endif; ?>><?php echo $os_value;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

              <div class="row">
                <div class="col-md-4">
                    <label for="a2w_tracking_code_order_status">
                        <strong><?php _ex('Shipped Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Change order status when all order items have been shipped.", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $tracking_code_order_status = a2w_get_setting('tracking_code_order_status'); ?>
                        <select name="a2w_tracking_code_order_status" id="a2w_tracking_code_order_status" class="form-control small-input">
                            <option value=""><?php _ex('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach($order_statuses as $os_key=>$os_value):?>
                            <option value="<?php echo $os_key;?>" <?php if ($tracking_code_order_status == $os_key): ?>selected="selected"<?php endif; ?>><?php echo $os_value;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_placed_order_status">
                        <strong><?php _ex('Placed Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Change order status when order is placed with the Chrome extension", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $placed_order_status = a2w_get_setting('placed_order_status'); ?>
                        <select name="a2w_placed_order_status" id="a2w_placed_order_status" class="form-control small-input">
                            <option value=""><?php _ex('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach($order_statuses as $os_key=>$os_value):?>
                            <option value="<?php echo $os_key;?>" <?php if ($placed_order_status == $os_key): ?>selected="selected"<?php endif; ?>><?php echo $os_value;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Schedule settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        
        <div class="panel-body _a2wfv">
            <?php $a2w_auto_update = a2w_get_setting('auto_update'); ?>
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Aliexpress Sync', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable auto-update features', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_auto_update" name="a2w_auto_update" value="yes" <?php if ($a2w_auto_update): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_on_not_available_product">
                        <strong><?php _e('When product is no longer available', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when one of your products is no longer available from Aliexpress. Applies to all existing products.', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_not_available_product = a2w_get_setting('on_not_available_product'); ?>
                        <select class="form-control small-input" name="a2w_on_not_available_product" id="a2w_on_not_available_product" <?php if (!$a2w_auto_update): ?>disabled<?php endif; ?>>
                            <option value="nothing" <?php if ($on_not_available_product == "nothing"): ?>selected="selected"<?php endif; ?>><?php _e('Do Nothing', 'ali2woo'); ?></option>
                            <option value="trash" <?php if ($on_not_available_product == "trash"): ?>selected="selected"<?php endif; ?>><?php _e('Move to trash', 'ali2woo'); ?></option>
                            <option value="zero" <?php if ($on_not_available_product == "zero"): ?>selected="selected"<?php endif; ?>><?php _e('Set Quantity To Zero', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_on_not_available_variation">
                        <strong><?php _e('When variant is no longer available', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when one of the product’s variants is no longer available from Aliexpress.', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_not_available_variation = a2w_get_setting('on_not_available_variation'); ?>
                        <select class="form-control small-input" name="a2w_on_not_available_variation" id="a2w_on_not_available_variation" <?php if (!$a2w_auto_update): ?>disabled<?php endif; ?>>
                            <option value="nothing" <?php if ($on_not_available_variation == "nothing"): ?>selected="selected"<?php endif; ?>><?php _e('Do Nothing', 'ali2woo'); ?></option>
                            <option value="trash" <?php if ($on_not_available_variation == "trash"): ?>selected="selected"<?php endif; ?>><?php _e('Remove variant', 'ali2woo'); ?></option>
                            <option value="zero" <?php if ($on_not_available_variation == "zero"): ?>selected="selected"<?php endif; ?>><?php _e('Set Quantity To Zero', 'ali2woo'); ?></option>
                            <option value="zero_and_disable" <?php if ($on_not_available_variation == "zero_and_disable"): ?>selected="selected"<?php endif; ?>><?php _e('Set Quantity To Zero and Disable', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_on_new_variation_appearance">
                        <strong><?php _e('When a new variant has appeared', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when new of the product’s variants is an appearance on Aliexpress.', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_new_variation_appearance = a2w_get_setting('on_new_variation_appearance'); ?>
                        <select class="form-control small-input" name="a2w_on_new_variation_appearance" id="a2w_on_new_variation_appearance" <?php if (!$a2w_auto_update): ?>disabled<?php endif; ?>>
                            <option value="nothing" <?php if ($on_new_variation_appearance == "nothing"): ?>selected="selected"<?php endif; ?>><?php _e('Do Nothing', 'ali2woo'); ?></option>
                            <option value="add" <?php if ($on_new_variation_appearance == "add"): ?>selected="selected"<?php endif; ?>><?php _e('Add variant', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_on_price_changes">
                        <strong><?php _e('When the price changes', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when the price of your product changes.', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_price_changes = a2w_get_setting('on_price_changes'); ?>
                        <select class="form-control small-input" name="a2w_on_price_changes" id="a2w_on_price_changes" <?php if (!$a2w_auto_update): ?>disabled<?php endif; ?>>
                            <option value="nothing" <?php if ($on_price_changes == "nothing"): ?>selected="selected"<?php endif; ?>><?php _e('Do Nothing', 'ali2woo'); ?></option>
                            <option value="update" <?php if ($on_price_changes == "update"): ?>selected="selected"<?php endif; ?>><?php _e('Update price', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_on_stock_changes">
                        <strong><?php _e('When inventory changes', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when the inventory level of a particular product changes.', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_stock_changes = a2w_get_setting('on_stock_changes'); ?>
                        <select class="form-control small-input" name="a2w_on_stock_changes" id="a2w_on_stock_changes" <?php if (!$a2w_auto_update): ?>disabled<?php endif; ?>>
                            <option value="nothing" <?php if ($on_stock_changes == "nothing"): ?>selected="selected"<?php endif; ?>><?php _e('Do Nothing', 'ali2woo'); ?></option>
                            <option value="update" <?php if ($on_stock_changes == "update"): ?>selected="selected"<?php endif; ?>><?php _e('Update automatically', 'ali2woo'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Send email alerts', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable email notifications about product changes (every 30 minutes). It watches for product price change, stock change, and monitors new product variant appearance. ', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $a2w_email_alerts = a2w_get_setting('email_alerts'); ?>
                        <input type="checkbox" class="form-control" id="a2w_email_alerts" name="a2w_email_alerts" value="yes" <?php if ($a2w_email_alerts): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Email address', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title='Input the email address you want to receive the notification emails on.'></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="email" class="form-control small-input" id="a2w_email_alerts_email" name="a2w_email_alerts_email" value="<?php echo esc_attr(a2w_get_setting('email_alerts_email')); ?>" required <?php if (!$a2w_email_alerts): ?>disabled<?php endif; ?> />
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Chrome Extension settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Default shipping method', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('If possible, we will auto-select this shipping method during the checkout on AliExpress.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $cur_a2w_fulfillment_prefship = a2w_get_setting('fulfillment_prefship', 'EMS_ZX_ZX_US'); ?>
                        <select name="a2w_fulfillment_prefship" id="a2w_fulfillment_prefship" class="form-control small-input" >
                            <option value="" <?php if ($cur_a2w_fulfillment_prefship === ""): ?>selected="selected"<?php endif; ?>>Default (not override)</option>
                            <option value="CAINIAO_STANDARD" <?php if ($cur_a2w_fulfillment_prefship == "CAINIAO_STANDARD"): ?>selected="selected"<?php endif; ?>>AliExpress Standard Shipping</option>
                            <option value="CPAM" <?php if ($cur_a2w_fulfillment_prefship == "CPAM"): ?>selected="selected"<?php endif; ?>>China Post Registered Air Mail</option>
                            <option value="EMS" <?php if ($cur_a2w_fulfillment_prefship == "EMS"): ?>selected="selected"<?php endif; ?>>EMS</option>
                            <option value="EMS_ZX_ZX_US" <?php if ($cur_a2w_fulfillment_prefship == "EMS_ZX_ZX_US"): ?>selected="selected"<?php endif; ?>>ePacket</option>
                            <option value="DHL" <?php if ($cur_a2w_fulfillment_prefship == "DHL"): ?>selected="selected"<?php endif; ?>>DHL</option>
                            <option value="FEDEX" <?php if ($cur_a2w_fulfillment_prefship == "FEDEX"): ?>selected="selected"<?php endif; ?>>FedEx</option>
                            <option value="SGP" <?php if ($cur_a2w_fulfillment_prefship == "SGP"): ?>selected="selected"<?php endif; ?>>Singapore Post</option>
                            <option value="TNT" <?php if ($cur_a2w_fulfillment_prefship == "TNT"): ?>selected="selected"<?php endif; ?>>TNT</option>
                            <option value="UPS" <?php if ($cur_a2w_fulfillment_prefship == "UPS"): ?>selected="selected"<?php endif; ?>>UPS</option>
                            <option value="USPS" <?php if ($cur_a2w_fulfillment_prefship == "USPS"): ?>selected="selected"<?php endif; ?>>USPS</option> 
                            <option value="CAINIAO_PREMIUM" <?php if ($cur_a2w_fulfillment_prefship == "CAINIAO_PREMIUM"): ?>selected="selected"<?php endif; ?>>AliExpress Premium Shipping</option>            
                            <option value="YANWEN_AM" <?php if ($cur_a2w_fulfillment_prefship == "YANWEN_AM"): ?>selected="selected"<?php endif; ?>>Special Line-YW</option>
                            <option value="CAINIAO_CONSOLIDATION_SA" <?php if ($cur_a2w_fulfillment_prefship == "CAINIAO_CONSOLIDATION_SA"): ?>selected="selected"<?php endif; ?>>Aliexpress Direct</option>
                            <option value="CAINIAO_CONSOLIDATION_BR" <?php if ($cur_a2w_fulfillment_prefship == "CAINIAO_CONSOLIDATION_BR"): ?>selected="selected"<?php endif; ?>>Aliexpress Direct (BR)</option>
                            <option value="Other" <?php if ($cur_a2w_fulfillment_prefship == "Other"): ?>selected="selected"<?php endif; ?>>Seller's Shipping Method</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Override phone number', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('This will be used instead of a customer phone number.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="text" placeholder="code" style="max-width: 60px;" class="form-control" id="a2w_fulfillment_phone_code" maxlength="5" name="a2w_fulfillment_phone_code" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_code')); ?>" />
                        <input type="text" placeholder="phone" class="form-control small-input" id="a2w_fulfillment_phone_number" maxlength="16" name="a2w_fulfillment_phone_number" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_number')); ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Custom note', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('A note to the supplier on the Aliexpress checkout page.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <textarea placeholder="note for aliexpress order" maxlength="1000" rows="5" class="form-control" id="a2w_fulfillment_custom_note" name="a2w_fulfillment_custom_note" cols="50"><?php echo esc_attr(a2w_get_setting('fulfillment_custom_note')); ?></textarea>
                    </div>
                </div>
            </div>
            
            <?php $a2w_order_translitirate = a2w_get_setting('order_translitirate'); ?>
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Transliteration', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable the auto-transliteration for AliExpress order details.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_order_translitirate" name="a2w_order_translitirate" value="yes" <?php if ($a2w_order_translitirate): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>
            <?php $a2w_order_third_name = a2w_get_setting('order_third_name'); ?>
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Middle name field', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Adds the Middle name field to WooCommerce checkout page and then uses it during an order-fulfillment process on AliExpress.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_order_third_name" name="a2w_order_third_name" value="yes" <?php if ($a2w_order_third_name): ?>checked<?php endif; ?>/>
                    </div>
                </div>
            </div>
            <?php $a2w_order_autopay = a2w_get_setting('order_autopay'); ?>
            <?php $a2w_order_awaiting_payment= a2w_get_setting('order_awaiting_payment'); ?>
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Automatic payments', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Allow the Chrome extension to pay automatically for each order during an order fulfillment process. Also please make sure that you`ve selected your credit card as the payment method on AlIExpress.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                       <?php /* <input type="checkbox" class="form-control" id="a2w_order_autopay" name="a2w_order_autopay" value="yes" <?php if ($a2w_order_autopay): ?>checked<?php endif; ?>/> */ ?>
                        <input type="radio" class="form-check-input"  name="a2w_order_awaiting_payment" id="a2w_order_awaiting_payment1" value="no" <?php if (!$a2w_order_awaiting_payment): ?>checked<?php endif; ?>>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Place order to Awaiting payment list', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Allow the Chrome extension to place each order to the Awaiting payment list on AlIExpress. It gives ability to pay for all orders at a time.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                   <?php /*     <input type="checkbox" class="form-control" id="a2w_order_awaiting_payment" name="a2w_order_awaiting_payment" value="yes" <?php if ($a2w_order_awaiting_payment): ?>checked<?php endif; ?>/> */?>
                        <input type="radio" class="form-check-input"  name="a2w_order_awaiting_payment" id="a2w_order_awaiting_payment2" value="yes" <?php if ($a2w_order_awaiting_payment): ?>checked<?php endif; ?>>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row pt20 border-top">
            <div class="col-sm-12">
                <input class="btn btn-success js-main-submit" type="submit" value="<?php _e('Save settings', 'ali2woo'); ?>"/>
            </div>
        </div>
    </div>

</form>   

<script>

    function a2w_isInt(value) {
        return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
    }


    (function ($) {
        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

        jQuery("#a2w_auto_update").change(function () {
            jQuery("#a2w_on_not_available_product").prop('disabled', !jQuery(this).is(':checked'));
            jQuery("#a2w_on_not_available_variation").prop('disabled', !jQuery(this).is(':checked'));
            jQuery("#a2w_on_new_variation_appearance").prop('disabled', !jQuery(this).is(':checked'));
            jQuery("#a2w_on_price_changes").prop('disabled', !jQuery(this).is(':checked'));
            jQuery("#a2w_on_stock_changes").prop('disabled', !jQuery(this).is(':checked'));
            jQuery("#a2w_email_alerts").prop('disabled', !jQuery(this).is(':checked'));
            
            return true;
        });

        jQuery("#a2w_email_alerts").change(function () {

            jQuery("#a2w_email_alerts_email").prop('disabled', !jQuery(this).is(':checked'));

            return true;
        });

        

        jQuery("#a2w_use_random_stock").change(function () {
            jQuery("#a2w_use_random_stock_block").toggle();
            return true;
        });

        var a2w_import_product_images_limit_keyup_timer = false;

        $('#a2w_import_product_images_limit').on('keyup', function () {
            if (a2w_import_product_images_limit_keyup_timer) {
                clearTimeout(a2w_import_product_images_limit_keyup_timer);
            }

            var this_el = $(this);

            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_import_product_images_limit_keyup_timer = setTimeout(function () {
                if (!a2w_isInt(this_el.val()) || this_el.val() < 0) {
                    this_el.after("<span class='help-block'>Please enter a integer greater than or equal to 0</span>");
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);
        });

        var a2w_fulfillment_phone_code_keyup_timer = false;

        $('#a2w_fulfillment_phone_code').on('keyup', function () {

            if (a2w_fulfillment_phone_code_keyup_timer) {
                clearTimeout(a2w_fulfillment_phone_code_keyup_timer);
            }

            var this_el = $(this);

            this_el.removeClass('has-error');
            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_fulfillment_phone_code_keyup_timer = setTimeout(function () {
                if (this_el.val() != '' && (!a2w_isInt(this_el.val()) || this_el.val().length < 1 || this_el.val().length > 5)) {
                    this_el.parents('.form-group').append("<span class='help-block'>Please enter Numbers. Between 1 - 5 characters.</span>");
                    this_el.addClass('has-error');
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);

            //$(this).removeClass('error_input');
        });

        var a2w_fulfillment_phone_number_keyup_timer = false;

        $('#a2w_fulfillment_phone_number').on('keyup', function () {

            if (a2w_fulfillment_phone_number_keyup_timer) {
                clearTimeout(a2w_fulfillment_phone_number_keyup_timer);
            }

            var this_el = $(this);

            this_el.removeClass('has-error');
            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_fulfillment_phone_number_keyup_timer = setTimeout(function () {
                if (this_el.val() != '' && (!a2w_isInt(this_el.val()) || this_el.val().length < 5 || this_el.val().length > 16)) {
                    this_el.parents('.form-group').append("<span class='help-block'>Please enter Numbers. Between 5 - 16 characters.</span>");
                    this_el.addClass('has-error');
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);

            //$(this).removeClass('error_input');
        });

        //form submit
        $('.a2w-content form').on('submit', function () {
            if ($(this).find('.has-error').length > 0)
                return false;
        })

        $.post(ajaxurl, {action: 'a2w_purchase_code_info'}).done(function (response) {
            let json = $.parseJSON(response);
            if (json.state !== 'ok') { 
                console.log(json);
            } else{
                const product_count = json.count.product?json.count.product:0                
                const product_limit = json.limits.product?json.limits.product:0
                const sync_count = json.count.sync_product?json.count.sync_product:0
                const sync_limit = json.limits.sync_product?json.limits.sync_product:0
                const reviews_count = json.count.reviews?json.count.reviews:0
                const reviews_limit = json.limits.reviews?json.limits.reviews:0
                const shipping_count = json.count.shipping?json.count.shipping:0
                const shipping_limit = json.limits.shipping?json.limits.shipping:0
                
                $('.a2w_purchase_code_info .supported_until').text(json.supported_until)
                $('.a2w_purchase_code_info .tariff_name').text(json.tariff_name)
                $('.a2w_purchase_code_info .daily_limits').html('Daily limits: '+
                '<span'+(product_count/product_limit > 0.9?' class="warn"':'')+'>products ('+product_count+'/'+product_limit+')</span>, '+
                '<span'+(sync_count/sync_limit > 0.9?' class="warn"':'')+'>product updates ('+sync_count+'/'+sync_limit+')</span>, '+
                '<span'+(reviews_count/reviews_limit > 0.9?' class="warn"':'')+'>reviews ('+reviews_count+'/'+reviews_limit+')</span>, '+
                '<span'+(shipping_count/shipping_limit > 0.9?' class="warn"':'')+'>shipping ('+shipping_count+'/'+shipping_limit+')</span>')

                $('.a2w_purchase_code_info').show()
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
        });

    })(jQuery);


</script>
