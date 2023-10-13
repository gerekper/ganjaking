<form method="post">

    <input type="hidden" name="setting_form" value="1"/>
    
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php echo esc_html_x('Purchase Settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="row-comments">
                    <?php esc_html_e('You can find the purchase code in your CodeCanyon account, then go to the "Downloads" page and locate the plugin there. Click "License certificate & purchase code" (available as PDF or text file).', 'ali2woo');?>
                    </div>
                </div>
            </div>
            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_item_purchase_code">
                        <strong><?php echo esc_html_x('Item Purchase Code', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title='<?php esc_html_e('Need for everything.', 'ali2woo');?>'></div>
                </div>
                <div class="field__input-wrap">
                    <input type="<?php echo (a2w_check_defined('A2W_HIDE_KEY_FIELDS') ? 'password' : 'text'); ?>" class="field__input field__input form-control medium-input" id="a2w_item_purchase_code" name="a2w_item_purchase_code" value="<?php echo esc_attr(a2w_get_setting('item_purchase_code')); ?>"/>
                </div>
            </div>

            <div class="a2w_purchase_code_info" style="display:none">
                <div class="field field_inline">
                    <div class="field__label">
                        <strong><?php esc_html_e('CodeCanyon support', 'ali2woo');?></strong>
                        <div class="info-box" data-toggle="tooltip" data-title='<?php esc_html_e('CodeCanyon support', 'ali2woo');?>'></div>
                    </div>
                    <div class="field__input-wrap">
                        <div class="form-group input-block no-margin">
                            <span class="supported_until"></span> <a href="https://help.market.envato.com/hc/en-us/articles/207886473-Extending-and-Renewing-Item-Support" target="_blank"><?php esc_html_e('extend/renew your support.', 'ali2woo');?></a>
                        </div>
                    </div>
                </div>
                <div class="field field_inline">
                    <div class="field__label">
                        <strong><?php esc_html_e('Package', 'ali2woo');?></strong>
                        <div class="info-box" data-toggle="tooltip" data-title='<?php esc_html_e('Information about the current package.', 'ali2woo');?>'></div>
                    </div>
                    <div class="field__input-wrap">
                        <div class="form-group input-block no-margin">
                            <?php $purchase_code_param = a2w_check_defined('A2W_HIDE_KEY_FIELDS') ? "" : "?purchase_code=" . esc_attr(a2w_get_setting('item_purchase_code'));?>
                            <span class="tariff_name"></span>, <a href="https://ali2woo.com/packages/<?php echo $purchase_code_param; ?>" target="_blank"><?php esc_html_e('change package.', 'ali2woo');?></a>
                        </div>
                        <div class="daily_limits form-group input-block no-margin"></div>
                        <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('It shows the daily usage quota. It`s reset every day.', 'ali2woo');?>"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php echo esc_html_x('Import Settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">

            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Language', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("It's applied to Product title, description, attributes and reviews", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <?php $cur_language = a2w_get_setting('import_language');?>
                    <select name="a2w_import_language" id="a2w_import_language" class="field__input form-control small-input">
                    <?php foreach ($languages as $code => $text): ?>
                        <option value="<?php echo $code; ?>" <?php if ($cur_language == $code): ?>selected="selected"<?php endif;?>><?php echo $text; ?></option>
                     <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Currency', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Default currency that used on a product import", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $cur_a2w_local_currency = strtoupper(a2w_get_setting('local_currency'));?>
                        <select name="a2w_local_currency" id="a2w_local_currency" class="field__input form-control small-input">
                            <?php foreach ($currencies as $code => $name): ?><option value="<?php echo $code; ?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif;?>><?php echo $name; ?></option><?php endforeach;?>
                            <?php if (!empty($custom_currencies)): ?>
                            <?php foreach ($custom_currencies as $code => $name): ?><option value="<?php echo $code; ?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif;?>><?php echo $name; ?></option><?php endforeach;?>
                            <?php endif;?>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_default_product_type">
                        <strong><?php echo esc_html_x('Default product type', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Default product type", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $default_product_type = a2w_get_setting('default_product_type');?>
                        <select name="a2w_default_product_type" id="a2w_default_product_type" class="field__input form-control small-input">
                            <option value="simple" <?php if ($default_product_type == "simple"): ?>selected="selected"<?php endif;?>><?php echo esc_html_x('Simple/Variable Product', 'Setting option', 'ali2woo'); ?></option>
                            <option value="external" <?php if ($default_product_type == "external"): ?>selected="selected"<?php endif;?>><?php echo esc_html_x('External/Affiliate Product', 'Setting option', 'ali2woo'); ?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_default_product_status">
                        <strong><?php echo esc_html_x('Default product status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Default product type", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $default_product_status = a2w_get_setting('default_product_status');?>
                        <select name="a2w_default_product_status" id="a2w_default_product_status" class="field__input form-control small-input">
                            <option value="publish" <?php if ($default_product_status == "publish"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Publish');?></option>
                            <option value="draft" <?php if ($default_product_status == "draft"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Draft');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_not_import_attributes">
                        <strong><?php esc_html_e('Not import specifications', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Not import specifications', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <input type="checkbox" class="field__input form-control" id="a2w_not_import_attributes" name="a2w_not_import_attributes" value="yes" <?php if (a2w_get_setting('not_import_attributes')): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_not_import_description">
                        <strong><?php esc_html_e('Not import description', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Not import description', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <input type="checkbox" class="field__input form-control" id="a2w_not_import_description" name="a2w_not_import_description" value="yes" <?php if (a2w_get_setting('not_import_description')): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_not_import_description_images">
                        <strong><?php esc_html_e("Don't import images from the description", 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e("Don't import images from the description", 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <input type="checkbox" class="field__input form-control" id="a2w_not_import_description_images" name="a2w_not_import_description_images" value="yes" <?php if (a2w_get_setting('not_import_description_images')): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_use_external_image_urls">
                        <strong><?php echo esc_html_x('Use external image urls', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Use external image urls', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="field__input form-control" id="a2w_use_external_image_urls" name="a2w_use_external_image_urls" value="yes" <?php if (a2w_get_setting('use_external_image_urls')): ?>checked<?php endif;?>/>
                    </div>
                    <div id="a2w_load_external_image_block" class="form-group input-block no-margin" <?php if (a2w_get_setting('use_external_image_urls')): ?>style="display: none;"<?php endif;?>>
                        <input class="btn btn-default load-images" disabled="disabled" type="button" value="<?php esc_html_e('Load images', 'ali2woo');?>"/>
                        <div id="a2w_load_external_image_progress"></div>
                    </div>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_use_random_stock">
                        <strong><?php echo esc_html_x('Use random stock value', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Use random stock value', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="field__input form-control" id="a2w_use_random_stock" name="a2w_use_random_stock" value="yes" <?php if (a2w_get_setting('use_random_stock')): ?>checked<?php endif;?>/>
                    </div>
                    <div id="a2w_use_random_stock_block" class="field__fill form-group input-block no-margin" <?php if (!a2w_get_setting('use_random_stock')): ?>style="display: none;"<?php endif;?>>
                        <?php esc_html_e('From', 'ali2woo');?> <input type="text" style="max-width: 60px;" class="field__input form-control" id="a2w_use_random_stock_min" name="a2w_use_random_stock_min" value="<?php echo esc_attr(a2w_get_setting('use_random_stock_min')); ?>">
                        <?php esc_html_e('To', 'ali2woo');?> <input type="text" style="max-width: 60px;" class="field__input form-control" id="a2w_use_random_stock_max" name="a2w_use_random_stock_max" value="<?php echo esc_attr(a2w_get_setting('use_random_stock_max')); ?>">
                    </div>
                </div>
            </div>
            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_background_import">
                        <strong><?php echo esc_html_x('Import in the background', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Products will be imported in the background mode, make sure you CRON is enabled.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <input type="checkbox" class="field__input form-control" id="a2w_background_import" name="a2w_background_import" value="yes" <?php if (a2w_get_setting('background_import')): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_allow_product_duplication">
                        <strong><?php echo esc_html_x('Allow product duplication', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Allow the import of an already imported product. This can be useful when you want to overload a product with the same product.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <input type="checkbox" class="field__input form-control" id="a2w_allow_product_duplication" name="a2w_allow_product_duplication" value="yes" <?php if (a2w_get_setting('allow_product_duplication')): ?>checked<?php endif;?>/>
                </div>
            </div>



            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_convert_attr_case">
                        <strong><?php echo esc_html_x('Convert case of attributes and their values', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Products may come with different text case of attributes and their values. ', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <?php $convert_attr_case = a2w_get_setting('convert_attr_case');?>
                        <select name="a2w_convert_attr_case" id="a2w_convert_attr_case" class="field__input form-control small-input">
                            <option value="original" <?php if ($convert_attr_case == "original"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Keep original case');?></option>
                            <option value="lower" <?php if ($convert_attr_case == "lower"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Lower case');?></option>
                            <option value="sentence" <?php if ($convert_attr_case == "sentence"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Sentence case');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_remove_ship_from">
                        <strong><?php echo esc_html_x('Remove "Ship From" attribute', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Remove Ship from attribute during product import.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="field__input form-control" id="a2w_remove_ship_from" name="a2w_remove_ship_from" value="yes" <?php if (a2w_get_setting('remove_ship_from')): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div id="a2w_remove_ship_from_block" class="field field_inline" <?php if (!a2w_get_setting('remove_ship_from')): ?>style="display: none;"<?php endif;?>>
                <div class="field__label">
                    <label for="a2w_default_ship_from">
                        <strong><?php echo esc_html_x('Default "Ship From" country', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Only product variations that contain Ship from selected country will be imported.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">

                        <?php $cur_a2w_default_ship_from = a2w_get_setting('default_ship_from');?>
                        <select name="a2w_default_ship_from" id="a2w_default_ship_from" class="field__input form-control small-input country_list">
                            <option value=""><?php _e('N/A', 'ali2woo');?></option>
                            <?php foreach ($shipping_countries as $code => $country): ?>
                                <option value="<?php echo $code; ?>"<?php if ($cur_a2w_default_ship_from == $code): ?> selected<?php endif;?>>
                                    <?php echo $country; ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <div class="field__fill input-block"><?php echo esc_html_x('Note! If the "Ship From" attribute does not contain the selected country, then China will be used as the "Ship from" country.', 'setting description', 'ali2woo'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php echo esc_html_x('Order Fulfillment settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_delivered_order_status">
                        <strong><?php echo esc_html_x('Delivered Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Change order status when all order items have been delivered.", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $delivered_order_status = a2w_get_setting('delivered_order_status');?>
                        <select name="a2w_delivered_order_status" id="a2w_delivered_order_status" class="field__input form-control small-input">
                            <option value=""><?php echo esc_html_x('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach ($order_statuses as $os_key => $os_value): ?>
                            <option value="<?php echo $os_key; ?>" <?php if ($delivered_order_status == $os_key): ?>selected="selected"<?php endif;?>><?php echo $os_value; ?></option>
                            <?php endforeach;?>
                        </select>
                </div>
            </div>

              <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_tracking_code_order_status">
                        <strong><?php echo esc_html_x('Shipped Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Change order status when all order items have been shipped.", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $tracking_code_order_status = a2w_get_setting('tracking_code_order_status');?>
                        <select name="a2w_tracking_code_order_status" id="a2w_tracking_code_order_status" class="field__input form-control small-input">
                            <option value=""><?php echo esc_html_x('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach ($order_statuses as $os_key => $os_value): ?>
                            <option value="<?php echo $os_key; ?>" <?php if ($tracking_code_order_status == $os_key): ?>selected="selected"<?php endif;?>><?php echo $os_value; ?></option>
                            <?php endforeach;?>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_placed_order_status">
                        <strong><?php echo esc_html_x('Placed Order Status', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("Change order status when order is placed with the Chrome extension", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $placed_order_status = a2w_get_setting('placed_order_status');?>
                        <select name="a2w_placed_order_status" id="a2w_placed_order_status" class="field__input form-control small-input">
                            <option value=""><?php echo esc_html_x('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                            <?php foreach ($order_statuses as $os_key => $os_value): ?>
                            <option value="<?php echo $os_key; ?>" <?php if ($placed_order_status == $os_key): ?>selected="selected"<?php endif;?>><?php echo $os_value; ?></option>
                            <?php endforeach;?>
                        </select>
                </div>
            </div>

            
            <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('Default shipping method', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('If the option is available, the plugin will automatically select this shipping method during the order fulfillment process. If not, it will either choose the shipping method selected by the user or the cheapest available option.', 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                <?php $cur_a2w_fulfillment_prefship = a2w_get_setting('fulfillment_prefship', 'CAINIAO_PREMIUM');?>
                <select name="a2w_fulfillment_prefship" id="a2w_fulfillment_prefship" class="field__input form-control small-input" >
                    <option value="" <?php if ($cur_a2w_fulfillment_prefship === ""): ?>selected="selected"<?php endif;?>>Default (not override)</option>
                    <?php foreach ($shipping_options as $shipping_option): ?>
                        <option value="<?php echo $shipping_option['value'] ?>"<?php if ($cur_a2w_fulfillment_prefship === $shipping_option['value']): ?> selected<?php endif;?>>
                            <?php echo $shipping_option['label']; ?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('Override phone number', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('This will be used instead of a customer phone number.', 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                <div class="field__input form-group input-block no-margin">
                    <input type="text" placeholder="code" style="max-width: 60px;" class="field__input form-control" id="a2w_fulfillment_phone_code" maxlength="5" name="a2w_fulfillment_phone_code" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_code')); ?>" />
                    <input type="text" placeholder="phone" class="field__input form-control small-input" id="a2w_fulfillment_phone_number" maxlength="16" name="a2w_fulfillment_phone_number" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_number')); ?>" />
                </div>
            </div>
        </div>

        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('CPF meta field', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("The order meta field that a 3rd party plugin uses to store customer's CPF field.This is used only for Customers from Brazil. If empty, billing company will be used as CPF when fulfilling AliExpress orders.", 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                    <input type="text" placeholder="" class="field__input form-control small-input" id="a2w_fulfillment_cpf_meta_key" name="a2w_fulfillment_cpf_meta_key" value="<?php echo esc_attr(a2w_get_setting('fulfillment_cpf_meta_key')); ?>" />
            </div>
        </div>

        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('RUT meta field', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x("The order meta field that a 3rd party plugin uses to store customer's RUT number. RUT number is required when you fulfill orders of Customers from Chile.", 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                    <input type="text" placeholder="" class="field__input form-control small-input" id="a2w_fulfillment_rut_meta_key" name="a2w_fulfillment_rut_meta_key" value="<?php echo esc_attr(a2w_get_setting('fulfillment_rut_meta_key')); ?>" />
            </div>
        </div>

        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('Custom note', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('A note to the supplier on the Aliexpress checkout page.', 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                    <textarea placeholder="<?php esc_html_e('note for aliexpress order', 'ali2woo');?>" maxlength="1000" rows="5" class="field__input form-control" id="a2w_fulfillment_custom_note" name="a2w_fulfillment_custom_note" cols="50"><?php echo esc_attr(a2w_get_setting('fulfillment_custom_note')); ?></textarea>
            </div>
        </div>

        <?php $a2w_order_translitirate = a2w_get_setting('order_translitirate');?>
        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('Transliteration', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Enable the auto-transliteration for AliExpress order details.', 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                    <input type="checkbox" class="field__input form-control" id="a2w_order_translitirate" name="a2w_order_translitirate" value="yes" <?php if ($a2w_order_translitirate): ?>checked<?php endif;?>/>
            </div>
        </div>
        <?php $a2w_order_third_name = a2w_get_setting('order_third_name');?>
        <div class="field field_inline">
            <div class="field__label">
                <label>
                    <strong><?php echo esc_html_x('Middle name field', 'Setting title', 'ali2woo'); ?></strong>
                </label>
                <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Adds the Middle name field to WooCommerce checkout page and then uses it during an order-fulfillment process on AliExpress.', 'setting description', 'ali2woo'); ?>"></div>
            </div>
            <div class="field__input-wrap">
                    <input type="checkbox" class="field__input form-control" id="a2w_order_third_name" name="a2w_order_third_name" value="yes" <?php if ($a2w_order_third_name): ?>checked<?php endif;?>/>
            </div>
        </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php echo esc_html_x('Schedule settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>
        
        <div class="panel-body _a2wfv">
            <?php $a2w_auto_update = a2w_get_setting('auto_update');?>
            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Aliexpress Sync', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Enable auto-update features', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="field__input form-control" id="a2w_auto_update" name="a2w_auto_update" value="yes" <?php if ($a2w_auto_update): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_on_not_available_product">
                        <strong><?php esc_html_e('When product is no longer available', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Choose an action when one of your products is no longer available from Aliexpress. Applies to all existing products.', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $on_not_available_product = a2w_get_setting('on_not_available_product');?>
                        <select class="field__input form-control small-input" name="a2w_on_not_available_product" id="a2w_on_not_available_product" <?php if (!$a2w_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_not_available_product == "nothing"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Do Nothing', 'ali2woo');?></option>
                            <option value="trash" <?php if ($on_not_available_product == "trash"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Move to trash', 'ali2woo');?></option>
                            <option value="zero" <?php if ($on_not_available_product == "zero"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Set Quantity To Zero', 'ali2woo');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_on_not_available_variation">
                        <strong><?php esc_html_e('When variant is no longer available', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Choose an action when one of the product’s variants is no longer available from Aliexpress.', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $on_not_available_variation = a2w_get_setting('on_not_available_variation');?>
                        <select class="field__input form-control small-input" name="a2w_on_not_available_variation" id="a2w_on_not_available_variation" <?php if (!$a2w_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_not_available_variation == "nothing"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Do Nothing', 'ali2woo');?></option>
                            <option value="trash" <?php if ($on_not_available_variation == "trash"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Remove variant', 'ali2woo');?></option>
                            <option value="zero" <?php if ($on_not_available_variation == "zero"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Set Quantity To Zero', 'ali2woo');?></option>
                            <option value="zero_and_disable" <?php if ($on_not_available_variation == "zero_and_disable"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Set Quantity To Zero and Disable', 'ali2woo');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_on_new_variation_appearance">
                        <strong><?php esc_html_e('When a new variant has appeared', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Choose an action when new of the product’s variants is an appearance on Aliexpress.', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $on_new_variation_appearance = a2w_get_setting('on_new_variation_appearance');?>
                        <select class="field__input form-control small-input" name="a2w_on_new_variation_appearance" id="a2w_on_new_variation_appearance" <?php if (!$a2w_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_new_variation_appearance == "nothing"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Do Nothing', 'ali2woo');?></option>
                            <option value="add" <?php if ($on_new_variation_appearance == "add"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Add variant', 'ali2woo');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_on_price_changes">
                        <strong><?php esc_html_e('When the price changes', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Choose an action when the price of your product changes.', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $on_price_changes = a2w_get_setting('on_price_changes');?>
                        <select class="field__input form-control small-input" name="a2w_on_price_changes" id="a2w_on_price_changes" <?php if (!$a2w_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_price_changes == "nothing"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Do Nothing', 'ali2woo');?></option>
                            <option value="update" <?php if ($on_price_changes == "update"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Update price', 'ali2woo');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label"> 
                    <label for="a2w_on_stock_changes">
                        <strong><?php esc_html_e('When inventory changes', 'ali2woo');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php esc_html_e('Choose an action when the inventory level of a particular product changes.', 'ali2woo');?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $on_stock_changes = a2w_get_setting('on_stock_changes');?>
                        <select class="field__input form-control small-input" name="a2w_on_stock_changes" id="a2w_on_stock_changes" <?php if (!$a2w_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_stock_changes == "nothing"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Do Nothing', 'ali2woo');?></option>
                            <option value="update" <?php if ($on_stock_changes == "update"): ?>selected="selected"<?php endif;?>><?php esc_html_e('Update automatically', 'ali2woo');?></option>
                        </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_untrash_product">
                        <strong><?php echo esc_html_x('Restore products from the trash', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Enable restore products from the trash during the sync process.', 'setting description', 'ali2woo'); ?>"></div>

                </div>
                <div class="field__input-wrap">
                        <?php $a2w_untrash_product = a2w_get_setting('untrash_product');?>
                        <input type="checkbox" class="field__input form-control" id="a2w_untrash_product" name="a2w_untrash_product" value="yes" <?php if ($a2w_untrash_product): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_email_alerts">
                        <strong><?php echo esc_html_x('Send email alerts', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Enable email notifications about product changes (every 30 minutes). It watches for product price change, stock change, and monitors new product variant appearance. ', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                        <?php $a2w_email_alerts = a2w_get_setting('email_alerts');?>
                        <input type="checkbox" class="field__input form-control" id="a2w_email_alerts" name="a2w_email_alerts" value="yes" <?php if ($a2w_email_alerts): ?>checked<?php endif;?>/>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Email address', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title='Input the email address you want to receive the notification emails on.'></div>
                </div>
                <div class="field__input-wrap">
                        <input type="email" class="field__input form-control small-input" id="a2w_email_alerts_email" name="a2w_email_alerts_email" value="<?php echo esc_attr(a2w_get_setting('email_alerts_email')); ?>" required <?php if (!$a2w_email_alerts): ?>disabled<?php endif;?> />
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row pt20 border-top">
            <div class="col-sm-12">
                <input class="btn btn-success js-main-submit" type="submit" value="<?php esc_html_e('Save settings', 'ali2woo');?>"/>
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
        $("#a2w_local_currency").select2();
        $("#a2w_import_language").select2();
        $("#a2w_fulfillment_prefship").select2();

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

        jQuery("#a2w_remove_ship_from").change(function () {
            jQuery("#a2w_remove_ship_from_block").toggle();
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
                    this_el.after("<span class='help-block'><?php esc_html_e('Please enter a integer greater than or equal to 0', 'ali2woo');?></span>");
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
                    this_el.parents('.form-group').append("<span class='help-block'><?php esc_html_e('Please enter Numbers. Between 1 - 5 characters.', 'ali2woo');?></span>");
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
                    this_el.parents('.form-group').append("<span class='help-block'><?php esc_html_e('Please enter Numbers. Between 5 - 16 characters.', 'ali2woo');?></span>");
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

        if($.trim($('#a2w_item_purchase_code').val()) !== ''){
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

                    $('.a2w_purchase_code_info .daily_limits').html('<?php esc_html_e('Daily limits', 'ali2woo');?>: '+
                    '<span'+(reviews_count/reviews_limit > 0.9?' class="warn"':'')+'>reviews ('+reviews_count+'/'+reviews_limit+')</span>')
                    
                    $('.a2w_purchase_code_info').show()
                }
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        }

    })(jQuery);


</script>
