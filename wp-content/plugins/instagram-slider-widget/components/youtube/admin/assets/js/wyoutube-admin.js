(function ($) {

    $(document).ready(function ($) {

        // Modfiy options when search for is changed
        $('.wyt_feed_type').on('change', function (e) {
            var type = $(this);
            if (type.val() === 'hashtag') {
                $('input#wyt_feed_hashtag').animate({opacity: 'show', height: 'show'}, 0);
                $('input#wyt_feed_account').animate({opacity: 'hide', height: 'hide'}, 0);
            } else if (type.val() === 'account') {
                $('input#wyt_feed_hashtag').animate({opacity: 'hide', height: 'hide'}, 0);
                $('input#wyt_feed_account').animate({opacity: 'show', height: 'show'}, 0);
            }
        });


        $('.wyt-close-icon').on('click', function (e){
            e.preventDefault();
            let data = $(this).data();

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'wyt_delete_account',
                    id: data.id,
                    nonce: wyt.nonce
                },
                beforeSend: function () {

                },
                success: function (response) {
                    window.location.reload();
                },
                complete: function () {

                },
                error: function (jqXHR, textStatus) {

                }
            });
        });

        //------OLD
        // Modfiy options when search for is changed
        var search = $('.wyt-container select[id$="search"]')
        var search_opt = $("option[value$='"+search.val()+"']");
        if (search_opt.data('type') === 'hashtag') {
            search.closest('.wyt-container').find('input[id$="blocked_users"]').closest('p').animate({
                opacity: 'show',
                height: 'show'
            }, 200);
        } else if (search_opt.data('type') === 'account') {
            search.closest('.wyt-container').find('input[id$="blocked_users"]').closest('p').animate({
                opacity: 'hide',
                height: 'hide'
            }, 200);
        }

        var template = $('.wyt-container select[id$="template"]')
        if (template.val() == 'default' || template.val() == 'default-no-border' || template.val() == 'slider' || template.val() == 'slider-overlay') {
            template.closest('.wyt-container').find('select[id$="yimages_link"] option[value="popup"]').animate({
                opacity: 'hide',
                height: 'hide'
            }, 200);

            //window.image_link_val = template.closest('.wyt-container').find('select[id$="yimages_link"]').val();
            //template.closest('.wyt-container').find('select[id$="yimages_link"]').val("image_link");
        } else {
            template.closest('.wyt-container').find('select[id$="yimages_link"] option[value="popup"]').animate({
                opacity: 'show',
                height: 'show'
            }, 200);
            //template.closest('.wyt-container').find('select[id$="yimages_link"]').val(window.image_link_val);
        }

        // Hide Custom Url if image link is not set to custom url
        $('body').on('change', '.wyt-container select[id$="yimages_link"]', function (e) {
            var yimages_link = $(this);
            if (yimages_link.val() != 'custom_url') {
                yimages_link.closest('.wyt-container').find('input[id$="custom_url"]').val('').parent().animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                yimages_link.closest('.wyt-container').find('input[id$="custom_url"]').parent().animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
        });

        // Modify options based on template selections
        $('body').on('change', '.wyt-container select[id$="template"]', wyt_template);

        function wyt_template (e) {
            var template = $(this);
            if (template.val() == 'default' || template.val() == 'default-no-border') {
                template.closest('.wyt-container').find('.wyt-slider-options').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                template.closest('.wyt-container').find('input[id$="columns"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.wyt-slider-options').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                template.closest('.wyt-container').find('input[id$="columns"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }
            if (template.val() != 'masonry') {
                template.closest('.wyt-container').find('.masonry_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                template.closest('.wyt-container').find('.masonry_notice').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.masonry_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                template.closest('.wyt-container').find('.masonry_notice').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'slick_slider') {
                template.closest('.wyt-container').find('.slick_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.slick_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'highlight') {
                template.closest('.wyt-container').find('.highlight_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.highlight_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() != 'slider' && template.val() != 'slider-overlay') {
                template.closest('.wyt-container').find('.slider_normal_settings').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.slider_normal_settings').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
            if (template.val() == 'highlight' || template.val() == 'slick_slider' || template.val() == 'default' || template.val() == 'default-no-border') {
                template.closest('.wyt-container').find('.words_in_caption').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                template.closest('.wyt-container').find('.words_in_caption').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }

            if (template.val() == 'default' || template.val() == 'default-no-border' || template.val() == 'slider' || template.val() == 'slider-overlay') {
                template.closest('.wyt-container').find('select[id$="yimages_link"] option[value="popup"]').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                window.image_link_val = template.closest('.wyt-container').find('select[id$="yimages_link"]').val();
                //template.closest('.wyt-container').find('select[id$="yimages_link"]').val("image_link");
            } else {
                template.closest('.wyt-container').find('select[id$="yimages_link"] option[value="popup"]').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                //template.closest('.wyt-container').find('select[id$="yimages_link"]').val(window.image_link_val);
            }
        }
        // Modfiy options when search for is changed
        $('body').on('change', '.wyt-container select[id$="search"]', function (e) {
            var search_for = $(this);
            var search_opt = $("option[value$='"+$(this).val()+"']");
            if (search_opt.data('type') === 'hashtag') {
                search_for.closest('.wyt-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else if (search_opt.data('type') === 'account') {
                search_for.closest('.wyt-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }
        });

        // Toggle advanced options
        $('body').on('click', '.wyt-advanced', function (e) {
            e.preventDefault();
            var advanced_container = $(this).parent().next();

            if (advanced_container.is(':hidden')) {
                $(this).html('[ - Close ]');
            } else {
                $(this).html('[ + Open ]');
            }
            advanced_container.toggle();
        });

        // Delete account with ajax
        $('.wyt-delete-account').on('click', function (e) {
            e.preventDefault();

            var c = confirm(wyt.remove_account);

            if (!c) {
                return false;
            }

            var $item = $(this),
                $tr = $item.closest('tr'),
                $spinner = $('#wyt-delete-spinner-' + $item.data('item_id'));

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'wyt_delete_account',
                    item_id: $item.data('item_id'),
                    is_business: $item.data('is_business'),
                    nonce: wyt.nonce
                },
                beforeSend: function () {
                    $spinner.addClass('is-active');
                },
                success: function (response) {
                    if (response.success) {
                        $tr.fadeOut();
                        //window.location.reload();
                    } else {
                        alert(response.data);
                    }
                },
                complete: function () {
                    $spinner.removeClass('is-active');
                },
                error: function (jqXHR, textStatus) {
                    console.log(textStatus);
                }
            });
        });

        jQuery('span.wyt_demo_pro').on('click', function (e) {
            e.preventDefault();
            window.open('https://cm-wp.com/yft/pricing/', '_blank');
        });

    }); // Document Ready

})(jQuery);
