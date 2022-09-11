<form method="post">
    <input type="hidden" name="setting_form" value="1"/>
    <div class="account_options<?php if ($account->custom_account): ?> custom_account<?php endif;?> account_type_<?php echo $account->account_type; ?>">
        <div class="panel panel-primary mt20">
            <div class="panel-heading">
                <h3 class="display-inline"><?php echo esc_html_x('Account settings', 'Setting title', 'ali2woo'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="field field_inline">
                    <div class="field__label">
                        <label>
                            <strong><?php esc_html_e('Use custom account', 'ali2woo');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('You can use your own Aliexpress API Keys if needed', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="field__input-wrap">
                        <input type="checkbox" class="field__input form-control float-left mr20" id="a2w_use_custom_account" name="a2w_use_custom_account" value="yes" <?php if ($account->custom_account): ?>checked<?php endif;?>/>
                        <div class="default_account">
                            <?php esc_html_e('You are using default account', 'ali2woo');?>
                        </div>
                    </div>
                </div>

                <div class="account_fields account_type_selector">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php esc_html_e('Account type', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Account type', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <select class="field__input form-control small-input" id="a2w_account_type" name="a2w_account_type">
                                <option value="aliexpress"<?php if ($account->account_type == 'aliexpress'): ?> selected="selected"<?php endif;?>>Aliexpress account</option>
                                <option value="admitad"<?php if ($account->account_type == 'admitad'): ?> selected="selected"<?php endif;?>>Admitad account</option>
                                <option value="epn"<?php if ($account->account_type == 'epn'): ?> selected="selected"<?php endif;?>>EPN account</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="account_fields account_fields_aliexpress">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php esc_html_e('APP Key', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('When you create the App, the AliExpress open platform will generate an appKey', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_appkey" name="a2w_appkey" value="<?php echo isset($account->account_data['aliexpress']['appkey']) ? $account->account_data['aliexpress']['appkey'] : ''; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="account_fields account_fields_aliexpress">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php esc_html_e('Secret Key', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('When you create the App, the AliExpress open platform will generate an secretKey', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_secretkey" name="a2w_secretkey" value="<?php echo isset($account->account_data['aliexpress']['secretkey']) ? $account->account_data['aliexpress']['secretkey'] : ''; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="account_fields account_fields_aliexpress">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label>
                                <strong><?php esc_html_e('TrackingId', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('The tracking ID of your account in the Portals platform', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_trackingid" name="a2w_trackingid" value="<?php echo isset($account->account_data['aliexpress']['trackingid']) ? $account->account_data['aliexpress']['trackingid'] : ''; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="account_fields account_fields_admitad">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label for="a2w_admitad_cashback_url">
                                <strong><?php esc_html_e('Cashback URL', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Cashback URL', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_admitad_cashback_url" name="a2w_admitad_cashback_url" value="<?php echo isset($account->account_data['admitad']['cashback_url']) ? $account->account_data['admitad']['cashback_url'] : ""; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_admitad">
                    <div class="col-md-12">
                        <div class="row-comments">
                        <?php echo _x('Enter your cashback url to get up to 12% cashback from each purchase on AliExpress. You will get the URL after registration with <a href="https://www.admitad.com/en/promo/?ref=1e8uh1z6nl">Admitad AliExpress</a> program', 'setting description', 'ali2woo'); ?>">
                        </div>
                    </div>
                </div>


                <div class="account_fields account_fields_epn">
                    <div class="field field_inline">
                        <div class="field__label">
                            <label for="a2wesc_html_epn_cashback_url">
                                <strong><?php esc_html_e('Cashback URL', 'ali2woo');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" data-title="<?php echo esc_html_x('Cashback URL', 'setting description', 'ali2woo'); ?>"></div>
                        </div>
                        <div class="field__input-wrap">
                            <input type="text" class="field__input form-control small-input" id="a2w_epn_cashback_url" name="a2w_epn_cashback_url" value="<?php echo isset($account->account_data['epn']['cashback_url']) ? $account->account_data['epn']['cashback_url'] : ""; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_epn">
                    <!--
                    <div class="col-md-12">
                        <div class="row-comments">
                            Enter your cashback url to get up to 12% cashback from each purchase on AliExpress. You will get the URL after registration with <a href="https://www.admitad.com/en/promo/?ref=1e8uh1z6nl">Admitad AliExpress</a> program
                        </div>
                    </div>
                    -->
                </div>

            </div>
        </div>



        <div class="panel panel-primary mt20">
            <div class="panel-heading">
                <h3 class="display-inline"><?php echo esc_html_x('Aliexpress API', 'Setting title', 'ali2woo'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <input id="a2w_get_access_token"class="btn btn-success" type="button" value="<?php esc_html_e('Get Access Token', 'ali2woo');?>"/>
                    </div>
                </div>

                <div class="row mt20">
                    <div class="col-xs-12">
                    <table class="table table-bordered a2w-tokens">
                        <thead>
                            <tr class="active">
                                <th scope="col">User name</th>
                                <th scope="col">Expire time</th>
                                <th scope="col" style="width: 100px">Default</th>
                                <th scope="col" style="width: 100px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tokens)): ?>
                                <tr><td colspan="4" style="text-align: center;"><?php esc_html_e('Press "Get Access Token" to add new aliexpress access token', 'ali2woo');?></td></tr>
                            <?php else: ?>
                                <?php foreach ($tokens as $token): ?>
                                    <tr>
                                        <td><?php echo esc_attr($token['user_nick']); ?></td>
                                        <td><?php echo esc_attr(date("F j, Y, H:i:s", round($token['expire_time'] / 1000))); ?></td>
                                        <td><input type="checkbox" class="default" value="yes" <?php if (isset($token['default']) && $token['default']): ?>checked<?php endif;?>/></td>
                                        <td><a href="#" data-token-id="<?php echo esc_attr($token['user_id']); ?>">Delete</a></td>
                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        </tbody>
                    </table>
                    </div>
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
    function ProcessChildMessage(message) {
	    console.log('ProcessChildMessage.message', message)
    }

    (function ($) {
        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

        $("#a2w_use_custom_account").change(function () {
            if ($(this).is(':checked')) {
                $(this).parents('.account_options').addClass('custom_account');
            } else {
                $(this).parents('.account_options').removeClass('custom_account');
            }
            return true;
        });

        $("#a2w_account_type").change(function () {
            $(this).parents('.account_options').removeClass('account_type_aliexpress').removeClass('account_type_admitad').removeClass('account_type_epn');
            $(this).parents('.account_options').addClass('account_type_'+$(this).val());
            return true;
        });

        // Auth
        $('#a2w_get_access_token').on('click', function (e) {
            let $button = $(this);

            $button.attr('disabled',true);

            e.preventDefault();

            $.post(ajaxurl, { action: 'a2w_build_aliexpress_api_auth_url' }).done(function (response) {
                var json = jQuery.parseJSON(response);

                if(json.state != 'ok'){
                    show_notification(json.message, true);
                }else{
                    window.open(json.url, "_blank", "width=868,height=686");

                    function  handleMessageEvent(event) {
                        const data = event.data;

                        if(event.data.state !== 'ok'){
                            console.log('data',data)
                            alert(data.message);
                        }else{
                            const token = event.data.data;
                            $.post(ajaxurl, { action: 'a2w_save_access_token', token }).done(function (response) {
                                response = jQuery.parseJSON(response);
                                $('.a2w-tokens tbody').html(response.data);
                            }).fail(function (xhr, status, error) {
                                alert('Can not save access token');
                            });
                        }
                        $button.removeAttr('disabled')

                        window.removeEventListener("message", handleMessageEvent);
                    }
                    window.addEventListener('message', handleMessageEvent)
                }
            }).fail(function (xhr, status, error) {
                console.log(error);
                $button.removeAttr('disabled')
            });

        });

        $('.a2w-tokens').on('click', 'a[data-token-id]', function (e) {
            $(this).parents('tr').remove();
            $.post(ajaxurl, { action: 'a2w_delete_access_token', id:$(this).attr('data-token-id') }).done(function (response) {
                var json = jQuery.parseJSON(response);
                if(json.state !== 'ok'){
                    alert(json.message);
                    console.log(json)
                }
            }).fail(function (xhr, status, error) {
                alert(error)
                console.log(error);
            });
            return false;
        });




    })(jQuery);
</script>
