(function ($) {

    $('input[name="savewidget"]').on('click', function (){
        setTimeout(function (){
            $(".isw-tabs").lightTabs();
        },1500);

    })

    $(document).ready(function ($) {
        $(".isw-tabs").lightTabs();

        var template = $('.jr-container select[id$="template"]')
        if (template.val() === 'thumbs' || template.val() === 'thumbs-no-border' || template.val() === 'slider' || template.val() === 'slider-overlay') {
            hideClosestSetting(template, 'select[id$="images_link"] option[value="popup"]');
        } else {
            showClosestSetting(template,'select[id$="images_link"] option[value="popup"]');
        }

        // Hide Custom Url if image link is not set to custom url
        $('body').on('change', '.jr-container select[id$="images_link"]', function (e) {
            var images_link = $(this);
            if (images_link.val() != 'custom_url') {
                images_link.closest('.jr-container').find('input[id$="custom_url"]').val('').parent().animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            } else {
                images_link.closest('.jr-container').find('input[id$="custom_url"]').parent().animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            }
        });

        $('body').on('change', '.jr-container input[id$="keep_ratio"]', function (e) {
            var keep_ratio = $(this);
            if (keep_ratio.is(":checked")){
                showClosestSetting(keep_ratio, '.slick_img_size');
            } else {
                hideClosestSetting(keep_ratio, '.slick_img_size');
            }
        });

        $('body').on('change', '.jr-container input[id$="m_keep_ratio"]', function (e) {
            var keep_ratio = $(this);
            if (keep_ratio.is(":checked")){
                showClosestSetting(keep_ratio, '.m_slick_img_size');
            } else {
                hideClosestSetting(keep_ratio, '.m_slick_img_size');
            }
        });

        // Modify options based on template selections

        $('body').on('change', '.jr-container .desk_settings select[id$="template"]', function (){
            modifySettings(this, false);
        });
        $('body').on('change', '.jr-container .mob_settings select[id$="m_template"]', function (){
            modifySettings(this, true);
        });

        function modifySettings(this_object, is_mob){
            var template = $(this_object);
            var prefix = "";
            if (is_mob){
                prefix = "m_";
            }
            if (template.val() === 'thumbs' || template.val() === 'thumbs-no-border') {
                hideClosestSetting(template, '.' + prefix + 'jr-slider-options');
                template.closest('.jr-container').find('input[id$="' + prefix +'columns"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else {
                showClosestSetting(template, '.' + prefix + 'jr-slider-options');
                template.closest('.jr-container').find('input[id$="' + prefix +'columns"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }
            if (template.val() != 'masonry') {
                hideClosestSetting(template, '.' + prefix + 'masonry_settings');
                hideClosestSetting(template, '.' + prefix + 'masonry_notice');
            } else {
                showClosestSetting(template, '.' + prefix + 'masonry_settings');
                showClosestSetting(template, '.' + prefix + 'masonry_notice');
            }
            if (template.val() != 'slick_slider') {
                hideClosestSetting(template, '.' + prefix + 'slick_settings');
            } else {
                showClosestSetting(template, '.' + prefix + 'slick_settings');
            }
            if (template.val() != 'highlight') {
                hideClosestSetting(template, '.' + prefix + 'highlight_settings');
            } else {
                showClosestSetting(template, '.' + prefix + 'highlight_settings');
            }

            if (template.val() != 'showcase'){
                hideClosestSetting(template, '.' + prefix + 'shopifeed_settings');
                $('.isw-linkto').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
            } else {
                showClosestSetting(template, '.' + prefix + 'shopifeed_settings');
                $('.isw-linkto').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
            }

            if (template.val() != 'slider' && template.val() != 'slider-overlay') {
                hideClosestSetting(template, '.' + prefix + 'slider_normal_settings');
            } else {
                showClosestSetting(template, '.' + prefix + 'slider_normal_settings');
            }
            if (template.val() === 'highlight' || template.val() === 'slick_slider' || template.val() === 'thumbs' || template.val() === 'thumbs-no-border') {
                hideClosestSetting(template, '.' + prefix + 'words_in_caption');
            } else {
                showClosestSetting(template, '.' + prefix + 'words_in_caption');
            }

            if (template.val() === 'thumbs' || template.val() === 'thumbs-no-border' || template.val() === 'slider' || template.val() === 'slider-overlay') {
                hideClosestSetting(template, 'select[id$="' + prefix + 'images_link"] option[value="popup"]');

                window.image_link_val = template.closest('.jr-container').find('select[id$="' + prefix + 'images_link"]').val();
            } else {
                showClosestSetting(template, 'select[id$="' + prefix + 'images_link"] option[value="popup"]');
            }
        }

        function showClosestSetting(closestFor, selector){
            closestFor.closest('.jr-container').find(selector).animate({
                opacity: 'show',
                height: 'show'
            }, 200);
        }

        function hideClosestSetting(closestFor, selector){
            closestFor.closest('.jr-container').find(selector).animate({
                opacity: 'hide',
                height: 'hide'
            }, 200);
        }

        // Modfiy options when search for is changed
        $('body').on('change', '.jr-container input:radio[id$="search_for"]', function (e) {
            var search_for = $(this);
            if (search_for.val() === 'hashtag') {
                search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="user_url"]');
                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="attachment"]');
                hideClosestSetting(search_for, 'select[id$="description"] option[value="username"]');

                search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="blocked_words"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="show_feed_header"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                $('#img_to_show').animate({opacity: 'hide', height: 'hide'}, 200);


            } else if (search_for.val() === 'username') {
                search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);

                showClosestSetting(search_for, 'select[id$="images_link"] option[value="user_url"]');
                showClosestSetting(search_for, 'select[id$="images_link"] option[value="attachment"]');
                showClosestSetting(search_for, 'select[id$="description"] option[value="username"]');

                search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="blocked_words"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="show_feed_header"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                $('#img_to_show').animate({opacity: 'hide', height: 'hide'}, 200);

            } else if (search_for.val() === 'account') {
                search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="user_url"]');
                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="attachment"]');
                hideClosestSetting(search_for, 'select[id$="description"] option[value="username"]');

                search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="blocked_words"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="show_feed_header"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                hideClosestSetting(search_for, 'select[id$="orderby"] option[value="popular-ASC"]');
                hideClosestSetting(search_for, 'select[id$="orderby"] option[value="popular-DESC"]');

                $('#img_to_show').animate({opacity: 'show', height: 'show'}, 200);

            } else if (search_for.val() === 'account_business') {
                search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);

                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="user_url"]');
                hideClosestSetting(search_for, 'select[id$="images_link"] option[value="attachment"]');
                hideClosestSetting(search_for, 'select[id$="description"] option[value="username"]');

                search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="blocked_words"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);
                search_for.closest('.jr-container').find('input[id$="show_feed_header"]').closest('p').animate({
                    opacity: 'show',
                    height: 'show'
                }, 200);

                showClosestSetting(search_for, 'select[id$="orderby"] option[value="popular-ASC"]');
                showClosestSetting(search_for, 'select[id$="orderby"] option[value="popular-DESC"]');

                $('#img_to_show').animate({opacity: 'show', height: 'show'}, 200);

            }
        });

        // Hide blocked images if not checked attachments
        $('body').on('change', '.jr-container [id$="attachment"]:checkbox', function (e) {
            var attachment = $(this);
            if (this.checked) {
                showClosestSetting(attachment, 'select[id$="images_link"] option[value="attachment"]')
            } else {
                hideClosestSetting(attachment, 'select[id$="images_link"] option[value="attachment"]')
            }
        });

        // Toggle advanced options
        $('body').on('click', '.jr-advanced', function (e) {
            e.preventDefault();
            var advanced_container = $(this).parent().next();

            if (advanced_container.is(':hidden')) {
                $(this).html('[ - Close ]');
            } else {
                $(this).html('[ + Open ]');
            }
            advanced_container.toggle();
        });

        // Remove blocked images with ajax
        $('body').on('click', '.jr-container .jr-delete-instagram-dupes', function (e) {
            e.preventDefault();
            var $this = $(this),
                username = $(this).data("username"),
                ajaxNonce = $(this).closest('.jr-container').find('input[name=delete_insta_dupes_nonce]').val();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'jr_delete_insta_dupes',
                    username: username,
                    _ajax_nonce: ajaxNonce
                },
                beforeSend: function () {
                    $this.prop('disabled', true);
                    $this.closest('.jr-container').find('.jr-spinner').addClass('spinner').css({
                        'visibility': 'visible',
                        'float': 'none'
                    });
                },
                success: function (data, textStatus, XMLHttpRequest) {
                    $this.closest('.jr-container').find('.deleted-dupes-info').text('Removed Duplicates: ' + data.deleted);
                },
                complete: function () {
                    $this.prop('disabled', false);
                    $this.closest('.jr-container').find('.jr-spinner').addClass('spinner').css({
                        'visibility': 'hidden',
                        'float': 'none'
                    });
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {

                }
            });
        });
        // Delete account with ajax
        $('.wis-delete-account').on('click', function (e) {
            e.preventDefault();

            var c = confirm(wis.remove_account);

            if (!c) {
                return false;
            }

            var $item = $(this),
                $tr = $item.closest('tr'),
                $spinner = $('#wis-delete-spinner-' + $item.data('item_id'));

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'wis_delete_account',
                    item_id: $item.data('item_id'),
                    is_business: $item.data('is_business'),
                    _ajax_nonce: wis.nonce
                },
                beforeSend: function () {
                    $spinner.addClass('is-active');
                },
                success: function (response) {
                    if (response.success) {
                        $tr.fadeOut();
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

        $('.wis-not-working').on('click', function (e) {
            e.preventDefault();
            $('#wis-add-token').animate({opacity: 'show', height: 'show'}, 200)
        });

        /*
        * FACEBOOK API modal
        */

        var modal = jQuery('#wis_accounts_modal');
        var modalOverlay = jQuery('#wis_modal_overlay');
        var spinOverlay = jQuery('.wis-overlay-spinner');

        modalOverlay.on("click", function () {
            var conf = confirm("You haven't finished adding an account. Are you sure you want to close the window?");
            if (conf) {
                modal.toggleClass("wis_closed");
                modalOverlay.toggleClass("wis_closed");
                spinOverlay.toggleClass("is-active");
            }
        });

        //BUSINESS INSTAGRAM
        jQuery('.wis_modal_content #wis-instagram-row').on('click', function (e) {
            modal.toggleClass("wis_closed");
            spinOverlay.addClass('is-active');
            wis_account = $(this).attr('data-account');
            jQuery.post(ajaxurl, {
                action: 'wis_add_account_by_token',
                account: wis_account,
                _ajax_nonce: add_account_nonce.nonce
            }).done(function (html) {
                console.log(html);
                window.location.reload();
            });
        });

        //FACEBOOK
        jQuery('.wis_modal_content #wis-facebook-row').on('click', function (e) {
            modal.toggleClass("wis_closed");
            spinOverlay.addClass('is-active');
            wis_account = $(this).attr('data-account');
            jQuery.post(ajaxurl, {
                action: 'wis_add_facebook_page_by_token',
                account: wis_account,
                _ajax_nonce: add_account_nonce.nonce
            }).done(function (html) {
                window.location.reload();
            });
        });

        /*
        * Chose API to add account
        * */
        var modal2 = jQuery('#wis_add_account_modal');
        var modal2Overlay = jQuery('#wis_add_account_modal_overlay');

        modal2Overlay.on("click", function () {
            var conf = confirm("You haven't finished adding an account. Are you sure you want to close the window?");
            if (conf) {
                modal2.toggleClass("wis_closed");
                modal2Overlay.toggleClass("wis_closed");
            }
        });

        jQuery('#wis-add-account-button .wis-btn-instagram-account').on('click', function (e) {
            e.preventDefault();
            modal2.removeClass('wis_closed');
            modal2Overlay.removeClass('wis_closed');
        });

        jQuery('span.wis_demo_pro').on('click', function (e) {
            e.preventDefault();
            window.open('https://cm-wp.com/instagram-slider-widget/pricing/', '_blank');
        });

    }); // Document Ready

    jQuery.fn.lightTabs = function(options){

        var createTabs = function(){
            tabs = this;
            data_widget_id = tabs.getAttribute("data-widget-id")
            slider_id = data_widget_id.split('-')[1]
            i = slider_id;

            showPage = function(i, device){

                if(device === 'desk'){
                    $('#desk_tab_content_' + i).show();
                    $('#desk_tab_' + i).addClass("active");

                    $('#mob_tab_content_' + i).hide();
                    $('#mob_tab_' + i).removeClass("active");
                }

                if(device === 'mob') {
                    $('#mob_tab_content_' + i).show();
                    $('#mob_tab_' + i).addClass("active");

                    $('#desk_tab_content_' + i).hide();
                    $('#desk_tab_' + i).removeClass("active");
                }
            }

            showPage(i, 'desk');

            $(".desk_tab").click(function (){
               let desk_tab_id = this.getAttribute('data-tab-id');
               showPage(desk_tab_id, 'desk')

            });
            $(".mob_tab").click(function (){
                let mob_tab_id = this.getAttribute('data-tab-id');
                showPage(mob_tab_id, 'mob')
            });
        };
        return this.each(createTabs);
    };

})(jQuery);
