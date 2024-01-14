jQuery(function ($) {
    'use strict';
    
    if (typeof fp_admin_params === 'undefined') {
        return false;
    }

    var SRP_Admin_Tab = {
        init: function () {
            this.trigger_on_page_load( );
            jQuery('.gif_rs_sumo_reward_button_for_unsubscribe').css('display', 'none');
            $(document).on('click', '#rs_display_notice', this.display_notice);
            $(document).on('click', '#rs_enable_reward_program', this.save_reward_program_disable_option);
            $(document).on('change', '#changepagesizertemplates', this.pagination_for_templates);
            $('body').on('blur', fp_admin_params.field_ids, this.validation_in_product_settings_on_blur);
            $('body').on('keyup change', fp_admin_params.field_ids, this.validation_in_product_settings_on_keyup);
            $('body').on('click', 'body', this.validation_in_product_settings_on_body_click);
            $(document).on('click', '.rs-add-birthday-date-action', this.add_birthday_date_action_edit_user);
            $(document).on('change' , '.enable-rs-rule', this.reward_type_on_category_page);
            $(document).on('change' , '.enable-reward-system-category', this.enable_reward_points_on_category);
            
            $(document).on('change' , '.srp-enable-referral-system-category', this.enable_referral_points_on_category);
            $(document).on('change' , '.srp-referral-type', this.referral_type);
            $(document).on('change' , '.srp-getting-referred-type', this.getting_referred_type);
            $(document).on('change' , '.srp-enable-social-reward-category', this.enable_social_reward_points_on_category);
            $(document).on('change' , '.srp-facebook-like-reward-type', this.facebook_like_reward_type);
            $(document).on('change' , '.srp-facebook-share-reward-type', this.facebook_share_reward_type);
            $(document).on('change' , '.srp-twitter-tweet-reward-type', this.twitter_tweet_reward_type);
            $(document).on('change' , '.srp-twitter-follow-reward-type', this.twitter_follow_reward_type);
            $(document).on('change' , '.srp-google-plus-reward-type', this.google_plus_reward_type);
            $(document).on('change' , '.srp-vk-like-reward-type', this.vk_like_reward_type);
            $(document).on('change' , '.srp-instagram-reward-type', this.instagram_reward_type);
            $(document).on('change' , '.srp-ok-share-reward-type', this.ok_share_reward_type);
            $(document).on('change' , '.srp-point-price-enable-category' , this.enable_point_price_on_category ) ;
            $(document).on('change' , '.srp-point-pricing-display-type' , this.point_pricing_display_type ) ;
            $(document).on('change' , '.srp-point-price-type' , this.point_price_type ) ;
        },
        trigger_on_page_load: function () {
            SRP_Admin_Tab.initialize_progress_bar();
            //Display Upgrade percentage
            this.display_upgrade_percentage();
            SRP_Admin_Tab.toggle_reward_type_on_category_page( $('.enable-rs-rule') );
            SRP_Admin_Tab.toggle_enable_reward_points_on_category( $('.enable-reward-system-category') );
            SRP_Admin_Tab.toggle_enable_referral_points_on_category($('.srp-enable-referral-system-category'));
            SRP_Admin_Tab.toggle_enable_social_reward_points_on_category($('.srp-enable-social-reward-category'));
            SRP_Admin_Tab.toggle_enable_point_price_on_category($('.srp-point-price-enable-category'));
        },

        point_price_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_point_price_type(this) ;
        } ,

        toggle_point_price_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-point-price-fixed').closest('div').show();
                } else {
                    $(e).closest('form').find('.srp-point-price-fixed').closest('div').hide();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-point-price-fixed').closest('tr').show();
                } else {
                    $(e).closest('div').find('.srp-point-price-fixed').closest('tr').hide();
                }
            }
        },

        point_pricing_display_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_point_pricing_display_type(this) ;
        } ,

        toggle_point_pricing_display_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-point-price-type').closest('div').show();
                    SRP_Admin_Tab.toggle_point_price_type( $(e).closest('form').find('.srp-point-price-type') );
                } else {
                    $(e).closest('form').find('.srp-point-price-type').closest('div').hide();
                    $(e).closest('form').find('.srp-point-price-fixed').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-point-price-type').closest('tr').show();
                    SRP_Admin_Tab.toggle_point_price_type( $(e).closest('div').find('.srp-point-price-type') );
                } else {
                    $(e).closest('div').find('.srp-point-price-type').closest('tr').hide();
                    $(e).closest('div').find('.srp-point-price-fixed').closest('tr').show();
                }
            }
        },

        enable_point_price_on_category(e){
            e.preventDefault();
            SRP_Admin_Tab.toggle_enable_point_price_on_category( this );
        },

        toggle_enable_point_price_on_category(e){
            if ( 'yes' === $(e).val() ){
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-point-price-enable-category').closest('div').show();
                    SRP_Admin_Tab.toggle_point_pricing_display_type( $(e).closest('form').find('.srp-point-pricing-display-type') );
                } else {
                    $(e).closest('div').find('.srp-show-if-point-price-enable-category').closest('tr').show();
                    SRP_Admin_Tab.toggle_point_pricing_display_type( $(e).closest('div').find('.srp-point-pricing-display-type') );
                }
            } else {
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-point-price-enable-category').closest('div').hide();
                } else {
                    $(e).closest('div').find('.srp-show-if-point-price-enable-category').closest('tr').hide();
                }
            }
        },

        ok_share_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_ok_share_reward_type(this) ;
        } ,

        toggle_ok_share_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-ok-share-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-ok-share-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-ok-share-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-ok-share-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-ok-share-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-ok-share-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-ok-share-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-ok-share-percent').closest('tr').show();
                }
            }
        },

        instagram_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_instagram_reward_type(this) ;
        } ,

        toggle_instagram_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-instagram-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-instagram-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-instagram-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-instagram-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-instagram-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-instagram-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-instagram-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-instagram-percent').closest('tr').show();
                }
            }
        },

        vk_like_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_vk_like_reward_type(this) ;
        } ,

        toggle_vk_like_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-vk-like-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-vk-like-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-vk-like-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-vk-like-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-vk-like-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-vk-like-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-vk-like-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-vk-like-percent').closest('tr').show();
                }
            }
        },

        google_plus_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_google_plus_reward_type(this) ;
        } ,

        toggle_google_plus_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-google-plus-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-google-plus-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-google-plus-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-google-plus-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-google-plus-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-google-plus-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-google-plus-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-google-plus-percent').closest('tr').show();
                }
            }
        },

        twitter_follow_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_twitter_follow_reward_type(this) ;
        } ,

        toggle_twitter_follow_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-twitter-follow-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-twitter-follow-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-twitter-follow-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-twitter-follow-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-twitter-follow-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-twitter-follow-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-twitter-follow-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-twitter-follow-percent').closest('tr').show();
                }
            }
        },

        twitter_tweet_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_twitter_tweet_reward_type(this) ;
        } ,

        toggle_twitter_tweet_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-twitter-tweet-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-twitter-tweet-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-twitter-tweet-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-twitter-tweet-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-twitter-tweet-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-twitter-tweet-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-twitter-tweet-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-twitter-tweet-percent').closest('tr').show();
                }
            }
        },

        facebook_share_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_facebook_share_reward_type(this) ;
        } ,

        toggle_facebook_share_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-facebook-share-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-facebook-share-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-facebook-share-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-facebook-share-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-facebook-share-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-facebook-share-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-facebook-share-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-facebook-share-percent').closest('tr').show();
                }
            }
        },

        facebook_like_reward_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_facebook_like_reward_type(this) ;
        } ,

        toggle_facebook_like_reward_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-facebook-like-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-facebook-like-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-facebook-like-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-facebook-like-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-facebook-like-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-facebook-like-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-facebook-like-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-facebook-like-percent').closest('tr').show();
                }
            }
        },

        enable_social_reward_points_on_category(e){
            e.preventDefault();
            SRP_Admin_Tab.toggle_enable_social_reward_points_on_category( this );
        },

        toggle_enable_social_reward_points_on_category(e){
            if ( 'yes' === $(e).val() ){
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-social-reward-enable-category').closest('div').show();
                    SRP_Admin_Tab.toggle_facebook_like_reward_type( $(e).closest('form').find('.srp-facebook-like-reward-type') );
                    SRP_Admin_Tab.toggle_facebook_share_reward_type( $(e).closest('form').find('.srp-facebook-share-reward-type') );
                    SRP_Admin_Tab.toggle_twitter_tweet_reward_type( $(e).closest('form').find('.srp-twitter-tweet-reward-type') );
                    SRP_Admin_Tab.toggle_twitter_follow_reward_type( $(e).closest('form').find('.srp-twitter-follow-reward-type') );
                    SRP_Admin_Tab.toggle_google_plus_reward_type( $(e).closest('form').find('.srp-google-plus-reward-type') );
                    SRP_Admin_Tab.toggle_vk_like_reward_type( $(e).closest('form').find('.srp-vk-like-reward-type') );
                    SRP_Admin_Tab.toggle_instagram_reward_type( $(e).closest('form').find('.srp-instagram-reward-type') );
                    SRP_Admin_Tab.toggle_ok_share_reward_type( $(e).closest('form').find('.srp-ok-share-reward-type') );
                } else {
                    $(e).closest('div').find('.srp-show-if-social-reward-enable-category').closest('tr').show();
                    SRP_Admin_Tab.toggle_facebook_like_reward_type( $(e).closest('div').find('.srp-facebook-like-reward-type') );
                    SRP_Admin_Tab.toggle_facebook_share_reward_type( $(e).closest('div').find('.srp-facebook-share-reward-type') );
                    SRP_Admin_Tab.toggle_twitter_tweet_reward_type( $(e).closest('div').find('.srp-twitter-tweet-reward-type') );
                    SRP_Admin_Tab.toggle_twitter_follow_reward_type( $(e).closest('div').find('.srp-twitter-follow-reward-type') );
                    SRP_Admin_Tab.toggle_google_plus_reward_type( $(e).closest('div').find('.srp-google-plus-reward-type') );
                    SRP_Admin_Tab.toggle_vk_like_reward_type( $(e).closest('div').find('.srp-vk-like-reward-type') );
                    SRP_Admin_Tab.toggle_instagram_reward_type( $(e).closest('div').find('.srp-instagram-reward-type') );
                    SRP_Admin_Tab.toggle_ok_share_reward_type( $(e).closest('div').find('.srp-ok-share-reward-type') );
                    
                }
            } else {
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-social-reward-enable-category').closest('div').hide();
                } else {
                    $(e).closest('div').find('.srp-show-if-social-reward-enable-category').closest('tr').hide();
                }
            }
        },

        getting_referred_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_getting_referred_type(this) ;
        } ,

        toggle_getting_referred_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-getrefer-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-getrefer-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-getrefer-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-getrefer-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-getrefer-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-getrefer-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-getrefer-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-getrefer-percent').closest('tr').show();
                }
            }
        },

        referral_type(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_referral_type(this) ;
        } ,

        toggle_referral_type(e) {
            if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('.srp-referral-fixed').closest('div').show();
                    $(e).closest('form').find('.srp-referral-percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('.srp-referral-fixed').closest('div').hide();
                    $(e).closest('form').find('.srp-referral-percent').closest('div').show();
                }
            } else {
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('.srp-referral-fixed').closest('tr').show();
                    $(e).closest('div').find('.srp-referral-percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('.srp-referral-fixed').closest('tr').hide();
                    $(e).closest('div').find('.srp-referral-percent').closest('tr').show();
                }
            }
        },

        enable_referral_points_on_category(e){
            e.preventDefault();
            SRP_Admin_Tab.toggle_enable_referral_points_on_category( this );
        },

        toggle_enable_referral_points_on_category(e){
            if ( 'yes' === $(e).val() ){
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-referral-enable-category').closest('div').show();
                    SRP_Admin_Tab.toggle_referral_type( $(e).closest('form').find('.srp-referral-type') );
                    SRP_Admin_Tab.toggle_getting_referred_type( $(e).closest('form').find('.srp-getting-referred-type') );
                } else {
                    $(e).closest('div').find('.srp-show-if-referral-enable-category').closest('tr').show();
                    SRP_Admin_Tab.toggle_referral_type( $(e).closest('div').find('.srp-referral-type') );
                    SRP_Admin_Tab.toggle_getting_referred_type( $(e).closest('div').find('.srp-getting-referred-type') );
                }
            } else {
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-referral-enable-category').closest('div').hide();
                } else {
                    $(e).closest('div').find('.srp-show-if-referral-enable-category').closest('tr').hide();
                }
            }
        },

        enable_reward_points_on_category(e){
            e.preventDefault();
            SRP_Admin_Tab.toggle_enable_reward_points_on_category( this );
        },

        toggle_enable_reward_points_on_category(e){
            if ( 'yes' === $(e).val() ){
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-enable-reward-on-category').closest('div').show();
                    SRP_Admin_Tab.toggle_reward_type_on_category_page( $(e).closest('form').find('.enable-rs-rule') );
                } else {
                    $(e).closest('div').find('.srp-show-if-enable-reward-on-category').closest('tr').show();
                    SRP_Admin_Tab.toggle_reward_type_on_category_page( $(e).closest('div').find('.enable-rs-rule') );
                }
            } else {
                if ( 'form' === $(e).data('parent') ){
                    $(e).closest('form').find('.srp-show-if-enable-reward-on-category').closest('div').hide();
                } else {
                    $(e).closest('div').find('.srp-show-if-enable-reward-on-category').closest('tr').hide();
                }
            }
        },

        initialize_progress_bar: function (  ) {

            if (!$('.fp_prograssbar_wrapper').length) {
                return false;
            }

            var data = ({
                action: 'fp_progress_bar_status',
                method_value: $('.fp_method_value').val(),
                fp_srp_security: fp_admin_params.upgrade_nonce,
            });

            $.post(ajaxurl, data, function (res) {
                if (true === res.success) {
                    if (res.data.percentage < 100) {
                        $('#fp_currrent_status').html(res.data.percentage);
                        $('.fp-progress-bar').css("width", res.data.percentage + "%");
                        SRP_Admin_Tab.initialize_progress_bar();
                    } else {
                        $('#fp_uprade_label').css("display", "none");
                        $('.fp-progress-bar').css("width", "100%");
                        $('#fp_progress_status').html(res.data.response_msg);
                        window.location.href = res.data.upgrade_success_url;
                    }
                } else {
                    alert(res.data.error);
                }
            });
        },
        display_upgrade_percentage: function () {
            
            if (!$('div.rs_progress_bar_wrapper').length) {
                return;
            }
            
            var data = {
                action: 'progress_bar_action',
                action_scheduler_class_id:$('.rs-action-scheduler-action-id').val(),
                sumo_security: fp_admin_params.upgrade_nonce
            };

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                dataType: 'json',
            }).done(function (res) {
                if (true === res.success) {
                    if (res.data.completed === 'no') {
                        $('#rs_progress_bar_current_status').html(res.data.percentage);
                        $('.rs_progress_bar_inner').css("width", res.data.percentage + "%");
                        SRP_Admin_Tab.display_upgrade_percentage();
                    } else {
                        $('#rs_progress_bar_label').css("display", "none");
                        $('.rs_progress_bar_inner').css("width", res.data.percentage + "%");
                        $('#rs_progress_bar_status').html(res.data.msg);
                        $('.rs-settings-url').show();
                        $('.rs-action-scheduler-info').hide();
                        window.location.href = res.data.redirect_url;
                    }
                }
            });
        },
        pagination_for_templates: function (e) {
            e.preventDefault();
            var pageSize = jQuery(this).val();
            jQuery('.footable').data('page-size', pageSize);
            jQuery('.footable').trigger('footable_initialized');
        },
        validation_in_product_settings_on_blur: function () {
            $('.wc_error_tip').fadeOut('100', function () {
                $(this).remove();
            });
            return this;
        },
        validation_in_product_settings_on_keyup: function () {
            var value = $(this).val();
            var regex = new RegExp("[^\+0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+", "gi");
            var newvalue = value.replace(regex, '');

            if (value !== newvalue) {
                $(this).val(newvalue);
                if ($(this).parent().find('.wc_error_tip').size() == 0) {
                    $(this).after('<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + " Negative Values are not allowed" + '</div>');
                    $('.wc_error_tip')
                            .css('left', offset.left + $(this).width() - ($(this).width() / 2) - ($('.wc_error_tip').width() / 2))
                            .css('top', offset.top + $(this).height())
                            .fadeIn('100');
                }
            }
            return this;
        },
        validation_in_product_settings_on_body_click: function () {
            $('.wc_error_tip').fadeOut('100', function () {
                $(this).remove();
            });
            return this;
        },
        save_reward_program_disable_option: function () {
            if (jQuery('#rs_enable_reward_program').is(':checked') == false) {
                if (confirm('Are you sure you want to turn off this option? Please note, If You Turn Off this option ,then all the users on your site will be in part of SUMO Reward Points)')) {
                    return true;
                }
                return false;
            }
        },
        display_notice: function () {
                var data = {action: "rs_database_upgrade_process"};
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                }).done(function (response) {
                    window.location.href = fp_admin_params.redirect_url;
                });
            return false;
        },
        add_birthday_date_action_edit_user: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if (!confirm(fp_admin_params.birthday_confirm_msg)) {
                return false;
            }

            $this.closest('td').find('input[name="srp_birthday_date"]').removeAttr('readonly', false).val('');
            return false;
        },
        reward_type_on_category_page(e) {
            e.preventDefault();
            SRP_Admin_Tab.toggle_reward_type_on_category_page(this);

        }, 
        toggle_reward_type_on_category_page( e ) {
            if ( 'tr' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('div').find('#rs_category_points').closest('tr').show();
                    $(e).closest('div').find('#rs_category_percent').closest('tr').hide();
                } else {
                    $(e).closest('div').find('#rs_category_points').closest('tr').hide();
                    $(e).closest('div').find('#rs_category_percent').closest('tr').show();
                }
            } else if ( 'div' === $(e).data('parent') ){
                if ( '1' === $(e).val() ){
                    $(e).closest('form').find('#rs_category_points').closest('div').show();
                    $(e).closest('form').find('#rs_category_percent').closest('div').hide();
                } else {
                    $(e).closest('form').find('#rs_category_points').closest('div').hide();
                    $(e).closest('form').find('#rs_category_percent').closest('div').show();
                }
            }
        } ,

    };
    SRP_Admin_Tab.init();
});