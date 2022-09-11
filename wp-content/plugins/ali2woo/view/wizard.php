<div class="a2w-content">    
    <div class="panel panel-primary">
        <div class="panel-heading panel-heading_column">
            <div style="padding:20px 0"><h2><?php  echo esc_html_x('Welcome to Ali2Woo!', 'Wizard', 'ali2woo'); ?></h2></div>
            <h3 class="display-inline"><?php echo esc_html_x('Based on your selection, our setup wizard will set optimal settings.', 'Wizard', 'ali2woo'); ?></h3>
            <p><?php echo esc_html_x('You need to click "Save" at the bottom of the page to apply recomendations. Please note, this setup wizard can overwrite your changes in the plugin settings.', 'Wizard', 'ali2woo'); ?></p>
        </div>
        <div class="panel-body">
            <form method="post">
            <input type="hidden" name="wizard_form" value="1"/> 
            
            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_item_purchase_code" class="<?php echo isset($errors['a2w_item_purchase_code']) ? "has-error" : ""; ?>">                 
                        <strong><?php echo esc_html_x('Input your Purchase code', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php esc_html_e('You can find the purchase code in your CodeCanyon account, then go to the "Downloads" page and locate the plugin there. Click "License certificate & purchase code" (available as PDF or text file).', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <div class="field__input form-group input-block no-margin <?php echo isset($errors['a2w_item_purchase_code']) ? "has-error" : ""; ?>">
                        <input placeholder="<?php esc_html_e('CodeCanyon purchase code', 'ali2woo') ?>" type="<?php echo ( a2w_check_defined('A2W_HIDE_KEY_FIELDS') ? 'password' : 'text'); ?>" class="field__input form-control large-input" id="a2w_item_purchase_code" name="a2w_item_purchase_code" value="<?php echo esc_attr(a2w_get_setting('item_purchase_code')); ?>"/>
                        <span class="help-block"><?php echo isset($errors['a2w_item_purchase_code']) ? $errors['a2w_item_purchase_code'] : ""; ?></span>
                    </div>             
                </div>
            </div>
            

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_import_language">
                        <strong><?php echo esc_html_x('Set language', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('You will import AliExpress product specifications, title, descriptions and products reviews in the preferred language.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <?php $cur_language = a2w_get_setting('import_language'); ?>
                        <select name="a2w_import_language" id="a2w_import_language" class="field__input form-control small-input">
                            <?php foreach ( $languages as $code => $text) : ?>
                                <option value="<?php echo $code; ?>" <?php if ($cur_language == $code): ?>selected="selected"<?php endif; ?>><?php echo $text; ?></option>
                            <?php endforeach; ?>
                        </select>                         
                    </div>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_local_currency">
                        <strong><?php echo esc_html_x('Set currency', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('Set currency of prices you import from AliExpress. Please note: Woocommerce currency will be changed too.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <div class="field__input form-group input-block no-margin">
                        <?php 
                            $cur_a2w_local_currency = strtoupper(a2w_get_setting('local_currency')); 
                        ?>
                        <select name="field__input a2w_local_currency" id="a2w_local_currency" class="form-control small-input">
                            <?php foreach($currencies as $code=>$name):?><option value="<?php echo $code;?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option><?php endforeach; ?>
                            <?php if(!empty($custom_currencies)):?>
                            <?php foreach($custom_currencies as $code=>$name):?><option value="<?php echo $code;?>" <?php if ($cur_a2w_local_currency == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option><?php endforeach; ?>
                            <?php endif; ?>
                        </select>     
                    </div>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_description_import_mode">
                        <strong><?php echo esc_html_x('What to do with product description?', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('Usually sellers add few or no text in the products on AliExpress. Instead they include images containing their promo infromation. That`s why a good technique is using product specifications instead of description and do not import images from description too.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <select name="a2w_description_import_mode" id="a2w_description_import_mode" class="field__input form-control large-input">
                        <?php foreach($description_import_modes as $code=>$name):?>
                            <option value="<?php echo $code;?>" <?php if ("use_spec" == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('What pricing model do you want to use?', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('Pricing rules define your profit. This setup wizard can add basic pricing rules for you, use them as an idea for your unique pricing strategy.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <select name="a2w_pricing_rules" id="a2w_pricing_rules" class="field__input form-control large-input">
                        <?php foreach($pricing_rule_sets as $code=>$name):?>
                            <option value="<?php echo $code;?>" <?php if ("low-ticket-fixed-3000" == $code): ?>selected="selected"<?php endif; ?>><?php echo $name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_add_shipping_to_product">
                        <strong><?php  echo esc_html_x('Include shipping cost to the product prices', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('Regardless of your pricing model, including the shipping cost to your product price is a good method to protect your profit from shipping expenses.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <input type="checkbox" class="field__input form-control" id="a2w_add_shipping_to_product" name="a2w_add_shipping_to_product" value="yes" checked />
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_remove_unwanted_phrases">
                        <strong><?php echo esc_html_x('Remove unwanted phrases from AliExpress products', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('The plugin will remove words like: "AliExpress, China, etc." - from the products which you import into your store.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <input type="checkbox" class="field__input form-control" id="a2w_remove_unwanted_phrases" name="a2w_remove_unwanted_phrases" value="yes" checked />
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label class="<?php echo isset($errors['a2w_fulfillment_phone_block']) ? "has-error" : ""; ?>">
                        <strong><?php echo esc_html_x('Replace buyer phone with your number', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('When your supplier see your order, he may want to contact you. The best practice is leaving your phone number in the order note on AliExpress.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <div class="field__input form-group input-block no-margin <?php echo isset($errors['a2w_fulfillment_phone_block']) ? "has-error" : ""; ?>">
                        <input type="text" placeholder="code" style="max-width: 60px;" class="field__input form-control" id="a2w_fulfillment_phone_code" maxlength="5" name="a2w_fulfillment_phone_code" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_code')); ?>" />
                        <input type="text" placeholder="phone" class="field__input form-control large-input" id="a2w_fulfillment_phone_number" maxlength="16" name="a2w_fulfillment_phone_number" value="<?php echo esc_attr(a2w_get_setting('fulfillment_phone_number')); ?>" />
                        <span class="field__input help-block"><?php echo isset($errors['a2w_fulfillment_phone_block']) ? $errors['a2w_fulfillment_phone_block'] : ""; ?></span>
                    </div>
                </div>

            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label for="a2w_import_reviews">
                        <strong><?php echo esc_html_x('Do you want to import reviews?', 'Wizard', 'ali2woo'); ?></strong>
                    </label>
                    <p><?php echo esc_html_x('Reviews allow you to increase the conversion in your store.', 'Wizard', 'ali2woo'); ?></p>
                </div>
                <div class="field__input-wrap">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_import_reviews" name="a2w_import_reviews" value="yes" checked />
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row pt20 border-top">
                    <div class="col-sm-12">
                            <input class="btn btn-success js-main-submit" type="submit" value="<?php esc_html_e('Save settings', 'ali2woo'); ?>"/>
                            <input class="btn btn-default" id="close_setup_wizard" type="button" value="<?php esc_html_e('Close', 'ali2woo'); ?>"/>  
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Close the Setup Wizard to prevent changes in the settings.', 'Wizard', 'ali2woo'); ?>"></div>
                    </div>
                </div>
            </div>

            </form>
        </div>

        
    </div>
</div>

<script>
    (function ($) {
        if($.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

        $('#close_setup_wizard').on('click', function(){
            window.location.href = "<?php echo $close_link; ?>";
        });

        $("#a2w_pricing_rules").change(function () {

            if ($(this).val() == "no"){
                $("#a2w_add_shipping_to_product").prop('checked', false);
            }
             else {
                $("#a2w_add_shipping_to_product").prop('checked', true);     
             }

            return true;
        });
    })(jQuery);
</script>

