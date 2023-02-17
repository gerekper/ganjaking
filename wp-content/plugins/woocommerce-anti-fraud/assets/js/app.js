jQuery(document).ready(function(){
    var high_risk_score = jQuery('#wc_settings_anti_fraud_higher_risk_threshold').val();
    var low_risk_score = jQuery('#wc_settings_anti_fraud_low_risk_threshold').val();

    jQuery('#wc_settings_anti_fraud_low_risk_threshold').attr({
        "max" : high_risk_score
    })
    jQuery('#wc_settings_anti_fraud_higher_risk_threshold').attr({
        "min" : low_risk_score
    })

    var isPayWhitelistEnabled = jQuery('#wc_af_enable_whitelist_payment_method').is(':checked');
    if(!isPayWhitelistEnabled){
        jQuery('#wc_settings_anti_fraud_whitelist_payment_method').attr('disabled', 'disabled');
    }

    var isRoleWhitelistEnabled = jQuery('#wc_af_enable_whitelist_user_roles').is(':checked');
    if(!isRoleWhitelistEnabled){
        jQuery('#wc_af_whitelist_user_roles').attr('disabled', 'disabled');
    }

    var isUpdateStatus = jQuery('#wc_af_fraud_update_state').is(':checked');
    if(!isUpdateStatus){
        jQuery('#wc_settings_anti_fraud_cancel_score').attr('disabled', 'disabled');
        jQuery('#wc_settings_anti_fraud_hold_score').attr('disabled', 'disabled');
    }

    jQuery('.forminp-checkbox input[type="checkbox"]').each(function(){
        var isEnabled = jQuery(this).is(':checked');
        var ele = jQuery(this);
        disableScoreSlider(ele, isEnabled);
    })

    jQuery('.forminp-checkbox input[type="checkbox"]').change(function(){
        var isEnabled = jQuery(this).is(':checked');
        var ele = jQuery(this);
        disableScoreSlider(ele, isEnabled);
    })

    jQuery('.forminp-number input[type="number"], .forminp-select select').change(function(){
        var multi_handle =  jQuery(this).parents('tr').find('.forminp-slider .score-slider').hasClass('multi-handle');
        if(!multi_handle){

            if(!jQuery(this).val()){
                var score = 0;
                jQuery(this).val(0)
            } else {
                var score = parseInt(jQuery(this).val());
            }
            var low_score = parseInt(jQuery(this).parents('tr').find('.forminp-slider .score-value').data('min-score'));
            var high_score = parseInt(jQuery(this).parents('tr').find('.forminp-slider .score-value').data('max-score'));

            var ele = jQuery(this).parents('tr');

            scoreSlider(low_score, high_score, score, ele);
            
        } else {

        }
    })

    function scoreSlider(low_score, high_score, score, ele){

        if(score > 0 && score <= low_score){
            jQuery(ele).find('.forminp-slider .score-value').css('border-color', 'rgba(90,198,125,1)');
        }else if(score >= low_score && score <= high_score){
            jQuery(ele).find('.forminp-slider .score-value').css('border-color', 'rgba(205,119,57,1)');
        } else if(score >= high_score){
            jQuery(ele).find('.forminp-slider .score-value').css('border-color', 'rgba(185,74,72,1)');
        } else {
            jQuery(ele).find('.forminp-slider .score-value').css('border-color', '#777777');
        }
        if(score == 0){
            jQuery(ele).find('.forminp-slider .score-bar').css('background', '#777777');
        } else {

            jQuery(ele).find('.forminp-slider .score-bar').css('background', 'linear-gradient(90deg, rgba(90,198,125,1) '+ (low_score - 25) +'%, rgba(205,119,57,1) '+(high_score)+'%, rgba(185,74,72,1) 100%)');
        }
        jQuery(ele).find('.forminp-slider .score-value').css('left', score+'%');
        jQuery(ele).find('.forminp-slider .score-value .score-text').html(score);
    }

    function disableScoreSlider($this, isChecked){
        var nextField = jQuery($this).parents('td').nextAll();

        if(isChecked == false){
            jQuery(nextField.get(0)).find('input').attr('disabled', 'disabled');
            if(jQuery(nextField.get(1)).hasClass('forminp-slider')){
                var score = 0
                var low_score = parseInt(jQuery(nextField.get(1)).find('.score-value').data('min-score'));
                var high_score = parseInt(jQuery(nextField.get(1)).find('.score-value').data('max-score'));
                var ele = jQuery(nextField.get(1)).parents('tr');
                scoreSlider(low_score, high_score, score, ele);
            }
        }else{
            jQuery(nextField.get(0)).find('input').attr('disabled', false);
            if(jQuery(nextField.get(1)).hasClass('forminp-slider')){
                var score = jQuery(nextField.get(0)).find('input').val();
                var low_score = parseInt(jQuery(nextField.get(1)).find('.score-value').data('min-score'));
                var high_score = parseInt(jQuery(nextField.get(1)).find('.score-value').data('max-score'));
                var ele = jQuery(nextField.get(1)).parents('tr');
                scoreSlider(low_score, high_score, score, ele);
            }
        }
    }

    jQuery('#wc_settings_anti_fraud_low_risk_threshold').change(function(){
        var minScore = jQuery(this).val();
        var maxScore =jQuery('#wc_settings_anti_fraud_higher_risk_threshold').val();

        var ele = jQuery(this).parents('tr');

        jQuery('#wc_settings_anti_fraud_higher_risk_threshold').attr({
            "min" : minScore
        });

        if(minScore == 0 && maxScore == 0){
            jQuery(ele).find('.forminp-slider .score-bar').css('background', '#777777');
        } else {
            jQuery(ele).find('.forminp-slider .score-bar').css('background', 'linear-gradient(90deg, rgba(90,198,125,1) '+ (minScore - 25) +'%, rgba(205,119,57,1) '+(maxScore)+'%, rgba(185,74,72,1) 100%)');
        }
        
        if(minScore > 0){
            jQuery(ele).find('.forminp-slider .score-value.min-score').css('border-color', 'rgba(90,198,125,1)');
        } else {
            jQuery(ele).find('.forminp-slider .score-value.min-score').css('border-color', '#777777');
        }
        jQuery(ele).find('.forminp-slider .score-value.min-score').css('left', minScore+'%');
        jQuery(ele).find('.forminp-slider .score-value.min-score .score-text.min-score').html(minScore);

    })

    jQuery('#wc_settings_anti_fraud_higher_risk_threshold').change(function(){
        var maxScore = jQuery(this).val();
        var minScore =jQuery('#wc_settings_anti_fraud_low_risk_threshold').val();

        var ele = jQuery(this).parents('tbody').find('.forminp-slider');
        jQuery('#wc_settings_anti_fraud_low_risk_threshold').attr({
            "max" : maxScore
        });

        if(minScore == 0 && maxScore == 0){
            jQuery(ele).find(' .score-bar').css('background', '#777777');
        } else {
            jQuery(ele).find(' .score-bar').css('background', 'linear-gradient(90deg, rgba(90,198,125,1) '+ (minScore - 25) +'%, rgba(205,119,57,1) '+(maxScore)+'%, rgba(185,74,72,1) 100%)');
        }

        if(maxScore > 0){
            jQuery(ele).find(' .score-value.max-score').css('border-color', 'rgba(205,119,57,1)');
        } else {
            jQuery(ele).find(' .score-value.max-score').css('border-color', '#777777');
        }
        jQuery(ele).find(' .score-value.max-score').css('left', maxScore+'%');
        jQuery(ele).find(' .score-value.max-score .score-text.max-score').html(maxScore);

    })

    jQuery('#wc_settings_anti_fraud_cancel_score, #wc_settings_anti_fraud_hold_score').change(function(){
        jQuery(this).parents('tr').find('.forminp-slider .score-slider');
        
        if(!jQuery(this).val()){
            var score = 0;
            jQuery(this).val(0)
        } else {
            var score = parseInt(jQuery(this).val());
        }

        var low_score = parseInt(jQuery(this).parents('tr').find('.forminp-slider .score-value').data('min-score'));
        var high_score = parseInt(jQuery(this).parents('tr').find('.forminp-slider .score-value').data('max-score'));

        var ele = jQuery(this).parents('tr');

        if(score > 0 && score <= low_score){
            jQuery(ele).find('.forminp-slider .score-bar-label').text('Low Risk Order');
        }else if(score >= low_score && score <= high_score){
            jQuery(ele).find('.forminp-slider .score-bar-label').text('Medium Risk Order');
        } else if(score >= high_score){
            jQuery(ele).find('.forminp-slider .score-bar-label').text('High Risk Order');
        } else {
            jQuery(ele).find('.forminp-slider .score-bar-label').text('Disabled');
        }



    })


    jQuery('#wc_af_enable_whitelist_payment_method').change(function(){
        var isChecked = jQuery(this).is(':checked');
        if(isChecked){
            jQuery('#wc_settings_anti_fraud_whitelist_payment_method').attr('disabled', false);
        } else {
            jQuery('#wc_settings_anti_fraud_whitelist_payment_method').attr('disabled', 'disabled');
        }
    })

    jQuery('#wc_af_enable_whitelist_user_roles').change(function(){
        var isChecked = jQuery(this).is(':checked');
        if(isChecked){
            jQuery('#wc_af_whitelist_user_roles').attr('disabled', false);
        } else {
            jQuery('#wc_af_whitelist_user_roles').attr('disabled', 'disabled');
        }
    })

    jQuery('#wc_af_fraud_update_state').change(function(){
        var isChecked = jQuery(this).is(':checked');
        if(isChecked){
            jQuery('#wc_settings_anti_fraud_cancel_score').attr('disabled', false);
            jQuery('#wc_settings_anti_fraud_hold_score').attr('disabled', false);
        } else {
            jQuery('#wc_settings_anti_fraud_cancel_score').attr('disabled', 'disabled');
            jQuery('#wc_settings_anti_fraud_hold_score').attr('disabled', 'disabled');
        }
    })

    /* Related to reCaptcha */

    var url = new URL(document.URL).searchParams;
    var tabname = url.get('section');
    
    if (tabname == 'minfraud_recaptcha_settings') {
        
        jQuery('#wc_af_enable_whitelist_payment_method').change(function(){
            var isChecked = jQuery(this).is(':checked');
            if(isChecked){
                jQuery('#wc_settings_anti_fraud_whitelist_payment_method').attr('disabled', false);
            } else {
                jQuery('#wc_settings_anti_fraud_whitelist_payment_method').attr('disabled', 'disabled');
            }
        })

        jQuery('#wc_af_enable_whitelist_user_roles').change(function(){
            var isChecked = jQuery(this).is(':checked');
            if(isChecked){
                jQuery('#wc_af_whitelist_user_roles').attr('disabled', false);
            } else {
                jQuery('#wc_af_whitelist_user_roles').attr('disabled', 'disabled');
            }
        })

        jQuery('#wc_af_fraud_update_state').change(function(){
            var isChecked = jQuery(this).is(':checked');
            if(isChecked){
                jQuery('#wc_settings_anti_fraud_cancel_score').attr('disabled', false);
                jQuery('#wc_settings_anti_fraud_hold_score').attr('disabled', false);
            } else {
                jQuery('#wc_settings_anti_fraud_cancel_score').attr('disabled', 'disabled');
                jQuery('#wc_settings_anti_fraud_hold_score').attr('disabled', 'disabled');
            }
        })

        jQuery('.form-table tr:nth-child(6)').css('display','none'); 
        jQuery('.form-table tr:nth-child(7)').css('display','none'); 
        jQuery('.form-table tr:nth-child(4)').css('display','none'); 
        jQuery('.form-table tr:nth-child(5)').css('display','none');
       
        if(jQuery('#wc_af_enable_v2_recaptcha').is(':checked')) {
            jQuery('.form-table tr:nth-child(6)').css('display','none'); 
            jQuery('.form-table tr:nth-child(7)').css('display','none'); 
            jQuery('.form-table tr:nth-child(4)').css('display','block'); 
            jQuery('.form-table tr:nth-child(5)').css('display','block');
            jQuery('.form-table tr:nth-child(4)').css('width','max-content'); 
            jQuery('.form-table tr:nth-child(5)').css('width','max-content');
        }

        if (jQuery('#wc_af_enable_v3_recaptcha').is(':checked')) {
            
            jQuery('.form-table tr:nth-child(6)').css('display','block'); 
            jQuery('.form-table tr:nth-child(7)').css('display','block'); 
            jQuery('.form-table tr:nth-child(4)').css('display','none'); 
            jQuery('.form-table tr:nth-child(5)').css('display','none');
            jQuery('.form-table tr:nth-child(6)').css('width','max-content'); 
            jQuery('.form-table tr:nth-child(7)').css('width','max-content');
        
        }


       jQuery('#wc_af_enable_v2_recaptcha').on('change', function() {
            
            jQuery('#wc_af_enable_v3_recaptcha').not(this).prop('checked', false);
            jQuery('.form-table tr:nth-child(6)').css('display','none'); 
            jQuery('.form-table tr:nth-child(7)').css('display','none'); 
            jQuery('.form-table tr:nth-child(4)').css('display','block'); 
            jQuery('.form-table tr:nth-child(5)').css('display','block');
            jQuery('.form-table tr:nth-child(4)').css('width','max-content'); 
            jQuery('.form-table tr:nth-child(5)').css('width','max-content');

        });
        jQuery('#wc_af_enable_v3_recaptcha').on('change', function() {
            
            jQuery('#wc_af_enable_v2_recaptcha').not(this).prop('checked', false);  
            jQuery('.form-table tr:nth-child(6)').css('display','block'); 
            jQuery('.form-table tr:nth-child(7)').css('display','block'); 
            jQuery('.form-table tr:nth-child(4)').css('display','none'); 
            jQuery('.form-table tr:nth-child(5)').css('display','none');
            jQuery('.form-table tr:nth-child(6)').css('width','max-content'); 
            jQuery('.form-table tr:nth-child(7)').css('width','max-content');
            
        });
    }
    /* Related to reCaptcha End */

})