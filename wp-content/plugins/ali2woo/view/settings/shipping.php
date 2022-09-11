<?php
$a2w_local_currency = strtoupper(a2w_get_setting('local_currency'));
?>
<form method="post" enctype='multipart/form-data'>
    <input type="hidden" name="setting_form" value="1"/>
    <div class="panel panel-primary mt20">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo esc_html_x('Shipping settings', 'Setting title', 'ali2woo'); ?></h3>
            <span class="pull-right">
                <a href="#" class="reset-shipping-meta btn _a2wfv"><?php echo esc_html_x('Reset product shipping meta', 'Setting title', 'ali2woo'); ?><div class="info-box" data-placement="left" data-toggle="tooltip" data-title="<?php echo esc_html_x('It clears the shipping methods cache, use this feature if you believe the shipping cost is changed on AliExpress.', 'Setting tip', 'ali2woo'); ?>"></div></a>
            </span>
        </div>

        <div class="panel-body">
            <div class="field field_inline _a2wfv">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Default shipping class', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Specific shipping class for WooCommerce, that get all products imported via Ali2Woo.', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <?php $default_shipping_class = a2w_get_setting('default_shipping_class');?>
                    <select name="a2w_default_shipping_class" id="a2w_default_shipping_class" class="field__input form-control small-input">
                        <option value=""><?php echo esc_html_x('Do nothing', 'Setting option', 'ali2woo'); ?></option>
                        <?php foreach ($shipping_class as $sc): ?>
                        <option value="<?php echo $sc->term_id; ?>" <?php if ($default_shipping_class == $sc->term_id): ?>selected="selected"<?php endif;?>><?php echo $sc->name; ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="field field_inline">
                <div class="field__label">
                    <label>
                        <strong><?php echo esc_html_x('Default Shipping Country', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('This is for the frontend (Cart, Checkout, Product page) and for the backend Ali2Woo`s pages (Search, Import List, etc.).', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="field__input-wrap">
                    <?php $cur_a2w_aliship_shipto = a2w_get_setting('aliship_shipto');?>
                    <select name="a2w_aliship_shipto" id="a2w_aliship_shipto" class="field__input form-control small-input country_list">
                        <option value=""><?php _e('N/A', 'ali2woo');?></option>
                        <?php foreach ($shipping_countries as $code => $country): ?>
                            <option value="<?php echo $code; ?>"<?php if ($cur_a2w_aliship_shipto == $code): ?> selected<?php endif;?>>
                                <?php echo $country; ?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default mt20">
        
        <div class="panel-body _a2wfv"">
            <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Use Aliexpress Shipping', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('It enables all options below and show the shipping selection interface on the cart and checkout page.', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                                <input type="checkbox" class="field__input form-control small-input" id="a2w_aliship_frontend" name="a2w_aliship_frontend" <?php if (a2w_get_setting('aliship_frontend')): ?>value="yes" checked<?php endif;?> />
                            <p><?php esc_html_e('All options below will only work if this option is enabled', 'ali2woo')?></p>
                        </div>
            </div>

            <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Shipping selection type', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Choose how the shipping method appears on the cart and checkout page: Popup or Select', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <?php $cur_a2w_aliship_selection_type = a2w_get_setting('aliship_selection_type');?>
                                <select name="a2w_aliship_selection_type" id="a2w_aliship_selection_type" class="field__input form-control small-input">
                                    <?php foreach ($shipping_selection_types as $selection_type): ?>
                                        <option value="<?php echo $selection_type; ?>"<?php if ($cur_a2w_aliship_selection_type == $selection_type): ?> selected<?php endif;?>>
                                            <?php echo ucfirst($selection_type); ?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                        </div>
            </div>

            <div class="field field_inline">
                        <div class="field__label">
                            <label for="a2w_aliship_shipping_option_text">
                                <strong><?php echo esc_html_x('AliExpress shipping option text', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                                    <input type="text" class="field__input form-control large-input" id="a2w_aliship_shipping_option_text" name="a2w_aliship_shipping_option_text" value="<?php echo esc_attr(a2w_get_setting('aliship_shipping_option_text')); ?>"/>

                            <?php A2W_Shipping::table_of_placeholders(array(
    'shipping_cost' => esc_html__('Shipping cost', 'ali2woo'),
    'shipping_company' => esc_html__('Shipping Company', 'ali2woo'),
    'delivery_time' => esc_html__('Delivery time', 'ali2woo'),
    'country' => esc_html__('Shipping country', 'ali2woo'),
));?>
                        </div>
            </div>

            <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Shipping calculation', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Shipping packages are cached so if you change this option, you`ll need to update your existing cart to make changes apply.', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <?php $cur_a2w_aliship_shipping_type = a2w_get_setting('aliship_shipping_type');?>
                            <select name="a2w_aliship_shipping_type" id="a2w_aliship_shipping_type" class="field__input form-control large-input">
                                <?php foreach ($shipping_types as $key => $shipping_type): ?>
                                    <option value="<?php echo $key; ?>"<?php if ($cur_a2w_aliship_shipping_type == $key): ?> selected<?php endif;?>>
                                        <?php echo $shipping_type; ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
            </div>

            <div class="field field_inline">
                        <div class="field__label">
                            <label for="a2w_aliship_shipping_label">
                                <strong><?php echo esc_html_x('Shipping Label', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title='Label of added shipping method in cart/checkout'></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_aliship_shipping_label" name="a2w_aliship_shipping_label" value="<?php echo esc_attr(a2w_get_setting('aliship_shipping_label')); ?>"/>
                        </div>
            </div>

            <div class="field field_inline">
                        <div class="field__label">
                            <label for="a2w_aliship_free_shipping_label">
                                <strong><?php echo esc_html_x('Free Shipping label', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title='Label of added free shipping method in cart/checkout'></div>
                        </div>
                        <div class="field__input-wrap">
                                <input type="text" class="field__input form-control small-input" id="a2w_aliship_free_shipping_label" name="a2w_aliship_free_shipping_label" value="<?php echo esc_attr(a2w_get_setting('aliship_free_shipping_label')); ?>"/>
                        </div>
            </div>

            <div class="panel panel-default mt20">
                <div class="panel-body _a2wfv">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Show on Product page', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Show shipping selection on the product page', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                                <input type="checkbox" class="field__input form-control small-input" id="a2w_aliship_product_enable" name="a2w_aliship_product_enable" <?php if (a2w_get_setting('aliship_product_enable')): ?>value="yes" checked<?php endif;?> />
                        </div>
                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Shipping not available message', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                                <input type="text" class="field__input form-control large-input" id="a2w_aliship_product_not_available_message" name="a2w_aliship_product_not_available_message" value="<?php echo esc_attr(a2w_get_setting('aliship_product_not_available_message')); ?>"/>
                            <?php A2W_Shipping::table_of_placeholders(array('country' => esc_html__('Shipping country', 'ali2woo')));?>
                        </div>
                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Position of shipping selection on Product page', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                            <?php $cur_a2w_aliship_product_position = a2w_get_setting('aliship_product_position');?>
                                <select name="a2w_aliship_product_position" id="a2w_aliship_product_position" class="field__input form-control small-input">
                                    <?php foreach ($selection_position_types as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"<?php if ($cur_a2w_aliship_product_position == $key): ?> selected<?php endif;?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default mt20">
                <div class="panel-body _a2wfv">
                    <div class="field field_inline">
                            <div class="field__label">
                                <label>
                                    <strong><?php echo esc_html_x('Remove items that shipping is not available', 'Setting title', 'ali2woo'); ?></strong>
                                    <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('When customers go to checkout, remove all items which are not available to ship to customers` country. During a customer session, items removed for this reason will be restored automatically if customer changes billing/shipping country to which the items are available to ship.', 'Setting description', 'ali2woo'); ?>"></div>
                                </label>
                            </div>
                            <div class="field__input-wrap">
                                    <input type="checkbox" class="field__input form-control" id="a2w_aliship_not_available_remove" name="a2w_aliship_not_available_remove" <?php if (a2w_get_setting('aliship_not_available_remove')): ?>value="yes" checked<?php endif;?> />
                            </div>

                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Default message for items that shipping is not available', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                                <input type="text" class="field__input form-control large-input" id="a2w_aliship_not_available_message" name="a2w_aliship_not_available_message" value="<?php echo esc_attr(a2w_get_setting('aliship_not_available_message')); ?>"/>
                            <p><?php esc_html_e('Below placeholders can only be used if the "Remove items that shipping is not available" option is disabled. Remove placeholders from the message if you disable that feature.', 'ali2woo')?></p>
                                <?php A2W_Shipping::table_of_placeholders(array(
    'shipping_cost' => esc_html__('Shipping cost', 'ali2woo'),
    'delivery_time' => esc_html__('Delivery time', 'ali2woo'),
    'country' => esc_html__('Shipping country', 'ali2woo'),
));?>
                        </div>
                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Default shipping cost', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                            <div class="field__input input-group input-block no-margin large-input">
                                <span class="input-group__input input-group__input_addon" id="a2w_aliship_not_available_cost_addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                <input type="number" min="0" step="any" class="input-group__input form-control" id="a2w_aliship_not_available_cost" name="a2w_aliship_not_available_cost"  value="<?php echo esc_attr(a2w_get_setting('aliship_not_available_cost')); ?>" aria-describedby="a2w_aliship_not_available_cost_addon" />
                            </div>
                            <p><?php echo esc_html_x('Apply this shipping cost for items that shipping is not available. 0 means free shipping', 'Setting title', 'ali2woo'); ?></p>

                        </div>
                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Default min delivery time', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                            <div class="field__input input-group input-block no-margin large-input">
                                <input type="number" min="0" step="any" class="input-group__input form-control" id="a2w_aliship_not_available_time_min" name="a2w_aliship_not_available_time_min"  value="<?php echo esc_attr(a2w_get_setting('aliship_not_available_time_min')); ?>" aria-describedby="a2w_aliship_not_available_time_min_addon" />
                                <span class="input-group__input input-group__input_addon" id="a2w_aliship_not_available_time_min_addon"><?php echo esc_html_x('Day(s)', 'Setting title', 'ali2woo'); ?></span>
                            </div>
                            <p><?php echo esc_html_x('Min delivery time shown for items that shipping is not available', 'Setting title', 'ali2woo'); ?></p>

                        </div>
                    </div>
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php echo esc_html_x('Default max delivery time', 'Setting title', 'ali2woo'); ?></strong>
                            </label>
                        </div>
                        <div class="field__input-wrap">
                            <div class="field__input input-group input-block no-margin large-input">
                                <input type="number" min="0" step="any" class="input-group__input form-control" id="a2w_aliship_not_available_time_max" name="a2w_aliship_not_available_time_max"  value="<?php echo esc_attr(a2w_get_setting('aliship_not_available_time_max')); ?>" aria-describedby="a2w_aliship_not_available_time_max_addon" />
                                <span class="input-group__input input-group__input_addon" id="a2w_aliship_not_available_time_max_addon"><?php echo esc_html_x('Day(s)', 'Setting title', 'ali2woo'); ?></span>
                            </div>
                            <p><?php echo esc_html_x('Max delivery time shown for items that shipping is not available', 'Setting title', 'ali2woo'); ?></p>

                        </div>
                    </div>
                </div>
            </div>

            <div class="global-pricing mt20">
                <div class="panel panel-primary mt20 _a2wfv">
                    <div class="panel-heading">
                        <h3 class="display-inline"><?php echo esc_html_x('Global shipping rules', 'Setting title', 'ali2woo'); ?><div class="info-box" data-placement="left" data-toggle="tooltip" data-title="<?php echo esc_html_x('Please note that you can disable Global rules for specific shipping methods if needed. Just go to "Shipping List" page, then choose "specific method" and set  "Enable price rule" to "no".', 'Setting tip', 'ali2woo'); ?>"></div></h3>
                    </div>

                    <div class="panel-body js-default-prices">
                        <div class="grid grid_default grid_center">

                            <div class="grid__col vertical-align">
                                <svg class="icon-pricechanged">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-pricechanged"></use>
                                </svg>

                            </div>


                            <div class="grid__col vertical-align">
                                <h3>Shipping cost</h3>
                            </div>

                            <div class="grid__col vertical-align">
                                <svg class="sign <?php if ($default_formula->sign == '+' || $default_formula->sign == '*'): ?>icon-plus <?php endif;?><?php if ($default_formula->sign == '*'): ?>icon-rotate45<?php endif;?> <?php if ($default_formula->sign == '='): ?>icon-equal<?php endif;?>">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#<?php if ($default_formula->sign == '+' || $default_formula->sign == '*'): ?>icon-plus<?php else: ?>icon-equal<?php endif;?>"></use>

                                </svg>
                            </div>
                            <div class="grid__col grid__col_jcenter vertical-align">
                                <input name="default_rule[sign]" type="hidden" value="<?php echo $default_formula->sign; ?>">
                                <div class="input-group price-dropdown-group">
                                    <input name="default_rule[value]" type="text" class="input-group__input field__input form-control value" value="<?php echo $default_formula->value; ?>" <?php if (!a2w_get_setting('aliship_frontend')): ?> disabled <?php endif;?>>

                                    <div class="input-group__input">
                                        <button type="button" class="input-group__input-inner btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (!a2w_get_setting('aliship_frontend')): ?> disabled <?php endif;?>>
                                            <?php if ($default_formula->sign == '+'): ?>Fixed Markup<?php endif;?>
                                            <?php if ($default_formula->sign == '='): ?>Custom Price<?php endif;?>
                                            <?php if ($default_formula->sign == '*'): ?>Multiplier<?php endif;?>  <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right sign">
                                            <li data-sign = "+" <?php if ($default_formula->sign == '+'): ?>style="display: none;"<?php endif;?>><a>Fixed Markup</a></li>
                                            <li data-sign = "=" <?php if ($default_formula->sign == '='): ?>style="display: none;"<?php endif;?>><a>Custom Price</a></li>
                                            <li data-sign = "*" <?php if ($default_formula->sign == '*'): ?>style="display: none;"<?php endif;?>><a>Multiplier</a></li>
                                        </ul>
                                    </div><!-- /btn-group -->
                                </div>
                            </div>
                            <div class="grid__col vertical-align">
                                <svg class="icon-full-arrow-right">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-full-arrow-right"></use>
                                </svg>
                            </div>
                            <div class="grid__col vertical-align">
                                <h3 style="width: 135px;">Shipping price</h3>
                            </div>
                            <div class="grid__col vertical-align">
                                <div class="info-box" data-placement="left" data-toggle="tooltip" data-title="Todo"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row pt20">
            <div class="col-sm-12">
                <input class="btn btn-success" type="submit" value="<?php esc_html_e('Save settings', 'ali2woo');?>"/>
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

        $('.a2w-placeholder-value').on('click', function () {
            $(this).select();
        });
        $('.a2w-placeholder-value-copy').on('click', function () {
            let $container = $(this).closest('.a2w-placeholder-value-container');
            $container.find('.a2w-placeholder-value').select();
            document.execCommand('copy');
        });

        $(".reset-shipping-meta").on("click", function () {
            if(!$(".reset-shipping-meta").hasClass('processing')){
                $(".reset-shipping-meta").addClass('processing');
                var data = {'action': 'a2w_reset_shipping_meta'};
                jQuery.post(ajaxurl, data).done(function (response) {
                    $(".reset-shipping-meta").removeClass('processing');
                    var json = jQuery.parseJSON(response);
                    if(json.state==='ok'){
                        show_notification('Reset product shipping meta Done');
                    }else{
                        show_notification(json.message, true);
                    }
                }).fail(function (xhr, status, error) {
                    $(".reset-shipping-meta").removeClass('processing');
                    show_notification('Applying pricing rules failed.', true);
                });
            }

            return false;
        });

        function get_el_sign_value(el) {
            return el.children('li')
                    .filter(function () {
                        return $(this).css('display') === 'none'
                    })
                    .attr('data-sign');
        }

        function get_value(compared) {
            var s_class = 'compared_value';
            if (typeof compared == "undefined")
                s_class = 'value';

            return $('.js-default-prices .' + s_class).val();
        }

        function rule_info_box_calculation(str_tmpl, sign, value) {

            var def_value = 1, result = value;
            if (sign == "+")
                result = def_value + Number(value);
            if (sign == "*")
                result = def_value * Number(value);

            return sprintf(str_tmpl, def_value, result, def_value, sign, value, result)

        }

        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

        //info content
        $(".js-default-prices div.info-box").on("mouseover", function () {
            $(this).attr('title', rule_info_box_calculation("E.g., A product shipping that costs %d <?php echo $a2w_local_currency; ?> would have its price set to %d <?php echo $a2w_local_currency; ?> (%d %s %d = %d).", get_el_sign_value($('.js-default-prices ul.sign')), get_value()));
            if(jQuery.fn.tooltip) { $(this).tooltip('fixTitle').tooltip('show'); }
        });



        //default rule dropdown
        $(".global-pricing .dropdown").on("click", function () {
            $(this).next().slideToggle();
        });
        $(".global-pricing .dropdown-menu li").click("click", function (e) {
            e.preventDefault();
            $(this).trigger('change');
            var sign = $(this).attr('data-sign'),
                    svg = $(this).closest('.input-group').prev('svg'),
                    svg = svg.length > 0 ? svg : $(this).closest('td').prev('td').find("svg"),
                    svg = svg.length > 0 ? svg : $(this).closest('.row').find('svg.sign');

            $('input[name="default_rule[sign]"]').val(sign);

            if (sign == '=') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-equal');
                svg.children('use').attr('xlink:href', '#icon-equal');
            }
            else if (sign == '*') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-plus icon-rotate45');
                svg.children('use').attr('xlink:href', '#icon-plus');
            }
            else if (sign == '+') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-plus');
                svg.children('use').attr('xlink:href', '#icon-plus');
            }

            $(this).hide().siblings().each(function () {
                $(this).show()
            });
            $(this).parent().fadeOut().prev().html($(this).text());
        });

        $('.a2w-content form').on('submit', function () {

            if ($(this).find('.has-error').length > 0)
                return false;
        });

        $("#a2w_aliship_frontend").change(function () {

            return true;
        });

        $("#a2w_aliship_product_enable").change(function () {

            var checked_status = $(this).is(':checked');

             $("#a2w_aliship_product_not_available_message").closest('.row').toggle(checked_status);
             $("#a2w_aliship_product_position").closest('.row').toggle(checked_status);

            return true;
        });

        $("#a2w_aliship_not_available_remove").change(function () {
            var checked_status = !$(this).is(':checked');

            $("#a2w_aliship_not_available_cost").closest('.row').toggle(checked_status);
            $("#a2w_aliship_not_available_time_min").closest('.row').toggle(checked_status);
            $("#a2w_aliship_not_available_time_max").closest('.row').toggle(checked_status);


            return true;
        });


       //set init states:

       $("#a2w_aliship_frontend").trigger('change');

        if ( !$("#a2w_aliship_product_enable").is(':checked') ) {
            $("#a2w_aliship_product_enable").trigger('change');
        }

        if ( $("#a2w_aliship_not_available_remove").is(':checked') ) {
            $("#a2w_aliship_not_available_remove").trigger('change');
        }

    })(jQuery);




</script>
