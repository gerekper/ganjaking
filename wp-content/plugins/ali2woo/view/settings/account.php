<form method="post">
    <input type="hidden" name="setting_form" value="1"/>
    <div class="account_options<?php if ($account->custom_account): ?> custom_account<?php endif; ?> account_type_<?php echo $account->account_type; ?>">
        <div class="panel panel-primary mt20">
            <div class="panel-heading">
                <h3 class="display-inline"><?php _ex('Account settings', 'Setting title', 'ali2woo'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <label>
                            <strong><?php _e('Use custom account', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('You can use your own Aliexpress API Keys if needed', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <div class="form-group input-block no-margin clearfix">
                            <input type="checkbox" class="form-control float-left mr20" id="a2w_use_custom_account" name="a2w_use_custom_account" value="yes" <?php if ($account->custom_account): ?>checked<?php endif; ?>/>
                            <div class="default_account">
                                <?php _e('You are using default account', 'ali2woo'); ?>
                            </div>
                        </div>                                                                     
                    </div>
                </div>
                
                <div class="row account_fields account_type_selector">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('Account type', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('Account type', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <select class="form-control small-input" id="a2w_account_type" name="a2w_account_type">
                                <option value="aliexpress"<?php if($account->account_type=='aliexpress'):?> selected="selected"<?php endif;?>>Aliexpress account</option>
                                <option value="admitad"<?php if($account->account_type=='admitad'):?> selected="selected"<?php endif;?>>Admitad account</option>
                                <option value="epn"<?php if($account->account_type=='epn'):?> selected="selected"<?php endif;?>>EPN account</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_aliexpress">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('APP Key', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('When you create the App, the AliExpress open platform will generate an appKey', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="a2w_appkey" name="a2w_appkey" value="<?php echo isset($account->account_data['aliexpress']['appkey'])?$account->account_data['aliexpress']['appkey']:''; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_aliexpress">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('Secret Key', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('When you create the App, the AliExpress open platform will generate an secretKey', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="a2w_secretkey" name="a2w_secretkey" value="<?php echo isset($account->account_data['aliexpress']['secretkey'])?$account->account_data['aliexpress']['secretkey']:''; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_aliexpress">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('TrackingId', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('The tracking ID of your account in the Portals platform', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="a2w_trackingid" name="a2w_trackingid" value="<?php echo isset($account->account_data['aliexpress']['trackingid'])?$account->account_data['aliexpress']['trackingid']:''; ?>"/>
                        </div>
                    </div>
                </div>
                
                <div class="row account_fields account_fields_admitad">
                    <div class="col-sm-4">
                        <label for="a2w_admitad_cashback_url">
                            <strong><?php _e('Cashback URL', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('Cashback URL', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="a2w_admitad_cashback_url" name="a2w_admitad_cashback_url" value="<?php echo isset($account->account_data['admitad']['cashback_url'])?$account->account_data['admitad']['cashback_url']:""; ?>"/>
                        </div>
                    </div>
                </div>
                
                <div class="row account_fields account_fields_admitad">
                    <div class="col-md-12">
                        <div class="row-comments">
                            Enter your cashback url to get up to 12% cashback from each purchase on AliExpress. You will get the URL after registration with <a href="https://www.admitad.com/en/promo/?ref=1e8uh1z6nl">Admitad AliExpress</a> program
                        </div>
                    </div>
                </div>


                <div class="row account_fields account_fields_epn">
                    <div class="col-sm-4">
                        <label for="a2w_epn_cashback_url">
                            <strong><?php _e('Cashback URL', 'ali2woo'); ?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('Cashback URL', 'setting description', 'ali2woo'); ?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="a2w_epn_cashback_url" name="a2w_epn_cashback_url" value="<?php echo isset($account->account_data['epn']['cashback_url'])?$account->account_data['epn']['cashback_url']:""; ?>"/>
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
    })(jQuery);
</script>
