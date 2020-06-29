jQuery(document).ready(function () {

    var custom_uploader;


    jQuery('#default_background_img_upload_button').click(function (e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#default_background_img').val(attachment.url);
            jQuery('.default_background_img_src').show();
            jQuery('.default_background_img_src').attr("src", attachment.url);
            jQuery('#default_background_img_remove_button').show();
        });

        //Open the uploader dialog
        custom_uploader.open();

    });

    jQuery('#default_background_img_remove_button').click(function (e) {

        e.preventDefault();

        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_default_background_img_remove',
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                jQuery('#default_background_img').val('');
                jQuery('#default_background_img_remove_button').hide();
                jQuery('.default_background_img_src').remove();
            }
        });


    });

    jQuery('.userpro-datepicker').datepicker({

        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        yearRange: 'c-65:c+0'

    });
    setTimeout(function () {
        jQuery('.userpro-rate-me-bubble').show();
        jQuery('.userpro-rate-me-bubble').addClass('animated');
        jQuery('.userpro-rate-me-bubble').addClass('bounceInUp');

    }, 500);

    jQuery(document).on('click', '.up-rating-close', function () {
        jQuery('.userpro-rate-me-bubble').fadeOut();
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_disable_rate_me',
            type: 'POST',
            success: function (data) {
            }
        });
    });

    jQuery(document).on('click', '.up-copy-mail-template', function () {
        up_process_email_template(this, 'save');
    });

    jQuery(document).on('click', '.up-preview-template', function () {
        up_preview_email(this);
    });

    jQuery(document).on('click', '.up-delete-email-template', function () {
        $res = window.confirm("Are you sure you want to delete this template ?");
        if ($res) {
            up_process_email_template(this, 'delete');
        }
    });

    jQuery(document).on('change', '#enable_html_notifications', function () {
        if (jQuery(this).val() == 1) {
            jQuery('.up-email-content').hide();
            jQuery('.up-html-template-link').show();
        } else {
            jQuery('.up-email-content').show();
            jQuery('.up-html-template-link').hide();
        }
    });

    jQuery('#up-service-submit').click(function (e) {
        e.preventDefault();
        jQuery('.up-service-message').hide();
        var err_message = '';
        var flag = 0;
        var email = jQuery('#email').val();
        var description = jQuery('#detailed_description').val();
        if (email == '' || !isValidEmailAddress(email)) {
            err_message = 'Please enter valid email address';
            flag = 1;
        } else if (description == '') {
            err_message = "Please enter some description";
            flag = 1;
        }

        if (!flag) {
            var form = jQuery('#up-service-contact');
            jQuery('.up-service-loading').show();
            jQuery.ajax({
                url: ajaxurl,
                data: form.serialize() + '&action=userpro_service_request',
                type: 'POST',
                success: function (data) {
                    jQuery('.up-service-message').html(data);
                    jQuery('.up-service-message').show();
                    document.getElementById('up-service-contact').reset();
                    jQuery('.up-service-loading').hide();
                }
            });
        } else {
            jQuery('.up-service-message').html(err_message);
            jQuery('.up-service-message').show();
        }
    })
});

jQuery(document).ready(function () {

    /* Toggle selection of roles in post screen */
    jQuery(document).on('click', 'input[name=userpro_edit_restrict]', function () {
        if (jQuery(this).val() == 'roles') {
            jQuery('p.restrict_roles').show(function () {
                jQuery("p.restrict_roles select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
                jQuery("p.restrict_roles *[class*=chzn], p.restrict_roles .chosen-container").remove();
                jQuery("p.restrict_roles .chosen-select").chosen({
                    disable_search_threshold: 10
                });
            });
        } else {
            jQuery('p.restrict_roles').hide();
        }
    });

    if (jQuery('input[name=userpro_edit_restrict]:checked').val() == 'roles') {
        jQuery('p.restrict_roles').show(function () {
            jQuery("p.restrict_roles select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
            jQuery("p.restrict_roles *[class*=chzn], p.restrict_roles .chosen-container").remove();
            jQuery("p.restrict_roles .chosen-select").chosen({
                disable_search_threshold: 10
            });
        });
    } else {

    }

    /* Toggle admin screen headings */
    if (jQuery('div.userpro-admin h3').length <= 3) {
        var tab = jQuery('div.userpro-admin h3:first');

        tab.addClass('selected');
        tab.next('table.form-table, .upadmin-panel').show();

        jQuery('table[data-type=conditional]').hide();
        jQuery('table[rel=' + jQuery('#badge_method').val() + ']').show();
        jQuery(".userpro-admin select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
        jQuery(".userpro-admin *[class*=chzn], .userpro-admin .chosen-container").remove();
        jQuery(".userpro-admin .chosen-select").chosen({
            disable_search_threshold: 10
        });
    }

    /* Expand table under h3 */
    jQuery(document).on('click', 'div.userpro-admin h3:not(.selected)', function () {
        jQuery(this).addClass('selected');
        jQuery(this).next('table.form-table, .upadmin-panel').show();
        jQuery('table[data-type=conditional]').hide();
        jQuery('table[rel=' + jQuery('#badge_method').val() + ']').show();
        jQuery(".userpro-admin select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
        jQuery(".userpro-admin *[class*=chzn], .userpro-admin .chosen-container").remove();
        jQuery(".userpro-admin .chosen-select").chosen({
            disable_search_threshold: 10
        });
    });

    /* Collapse table under h3 */
    jQuery(document).on('click', 'div.userpro-admin h3.selected', function () {
        jQuery(this).removeClass('selected');
        jQuery(this).next('table.form-table, .upadmin-panel').hide();
    });

    /* deny user registration */
    jQuery(document).on('click', '.upadmin-user-deny', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-pending-verify');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_admin_user_deny&user_id=' + jQuery(this).data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.fadeOut();
                if (data.count === '0' || data.count == '' || !data.count) {
                    jQuery('.upadmin-bubble-new').remove();
                } else {
                    jQuery('.upadmin-bubble-new').html(data.count);
                }
                jQuery('.toplevel_page_userpro').find('span.update-count').html(data.count);
            }
        });
        return false;
    });

     /* deny ALL users registrations */
     jQuery(document).on('click', '.upadmin-user-deny-all', function (e) {
        e.preventDefault();
        var thisElement = jQuery(this);
        denyApproveActionsWithUser(thisElement, 'deny' );
    });

    jQuery(document).on('change', '#userpro_sortby_verified', function (e) {

        var e = document.getElementById("userpro_sortby_verified");
        var selectedval = e.options[e.selectedIndex].value;

        if (selectedval == "descending") {

            var $divs = jQuery("div.boxv");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() < jQuery(b).find("p").text();
            });
            jQuery("#containerv").html(alphabeticallyOrderedDivs);
        }
        if (selectedval == "ascending") {

            var $divs = jQuery("div.boxv");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() > jQuery(b).find("p").text();
            });
            jQuery("#containerv").html(alphabeticallyOrderedDivs);

        }
        if (selectedval == "default") {


            var alphabeticallyOrderedDivs = jQuery("#containerv").find('.boxv').sort(function (a, b) {
                return parseInt(a.id) > parseInt(b.id);
            });
            jQuery("#containerv").html(alphabeticallyOrderedDivs);

        }


    });


    jQuery(document).on('change', '#userpro_sortby_manual', function (e) {

        var e = document.getElementById("userpro_sortby_manual");
        var selectedval = e.options[e.selectedIndex].value;

        if (selectedval == "descending") {

            var $divs = jQuery("div.box");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() < jQuery(b).find("p").text();
            });
            jQuery("#container").html(alphabeticallyOrderedDivs);
        }
        if (selectedval == "ascending") {

            var $divs = jQuery("div.box");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() > jQuery(b).find("p").text();
            });
            jQuery("#container").html(alphabeticallyOrderedDivs);

        }
        if (selectedval == "default") {


            var alphabeticallyOrderedDivs = jQuery("#container").find('.box').sort(function (a, b) {
                return parseInt(a.id) > parseInt(b.id);
            });
            jQuery("#container").html(alphabeticallyOrderedDivs);

        }


    });

    jQuery(document).on('change', '#userpro_sortby_email', function (e) {

        var e = document.getElementById("userpro_sortby_email");
        var selectedval = e.options[e.selectedIndex].value;

        if (selectedval == "descending") {


            var $divs = jQuery("div.boxe");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() < jQuery(b).find("p").text();
            });
            jQuery("#containere").html(alphabeticallyOrderedDivs);
        }
        if (selectedval == "ascending") {

            var $divs = jQuery("div.boxe");
            var alphabeticallyOrderedDivs = $divs.sort(function (a, b) {
                return jQuery(a).find("p").text() > jQuery(b).find("p").text();
            });
            jQuery("#containere").html(alphabeticallyOrderedDivs);

        }
        if (selectedval == "default") {


            var alphabeticallyOrderedDivs = jQuery("#containere").find('.boxe').sort(function (a, b) {
                return parseInt(a.id) > parseInt(b.id);
            });
            jQuery("#containere").html(alphabeticallyOrderedDivs);

        }


    });


    /* approve user registration */
    jQuery(document).on('click', '.upadmin-user-approve', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-pending-verify');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_admin_user_approve&user_id=' + jQuery(this).data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.fadeOut();
                if (data.count === '0' || data.count == '' || !data.count) {
                    jQuery('.upadmin-bubble-new').remove();
                } else {
                    jQuery('.upadmin-bubble-new').html(data.count);
                }
                jQuery('.toplevel_page_userpro').find('span.update-count').html(data.count);
            }
        });
        return false;
    });

    /* approve ALL users registration */
    jQuery(document).on('click', '.upadmin-user-approve-all', function (e) {
        e.preventDefault();
        var thisElement = jQuery(this);
        denyApproveActionsWithUser(thisElement, 'approve' );
    });

    /* Verify user */
    jQuery(document).on('click', '.upadmin-verify-u', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-verify-v2');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_verify_user&user_id=' + link.data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.hide().html(data.admin_tpl).fadeIn();
            }
        });
        return false;
    });

    /* Unverify user */
    jQuery(document).on('click', '.upadmin-unverify-u', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-verify-v2');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_unverify_user&user_id=' + link.data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.hide().html(data.admin_tpl).fadeIn();
            }
        });
        return false;
    });

    /**
     * Invite user to website
     */
    jQuery(function(){
        jQuery('#invite').click(function(e){
            e.preventDefault();
            jQuery('#invite').prop('disabled', true);
            var emails = jQuery('#userpro_invite_emails').val().toLowerCase();
            emails = emails.replace(/\s/g,'');
            var ccEmails = jQuery('#userpro_cc_invite_emails').val().toLowerCase();
            ccEmails = ccEmails.replace(/\s/g,'');
            var data = {
                'action': 'userpro_user_invite',
                'emails': emails ,
                'cc_emails': ccEmails
            };

            jQuery.ajax({
                url: ajaxurl,
                data: data,
                dataType: 'JSON',
                type: 'POST',
                success: function (response) {
                    jQuery('#invite').prop('disabled', false);
                    jQuery('#userpro_invite_emails').val('');
                    jQuery('#userpro_cc_invite_emails').val('');
                    if(response.data[0].status === 'warning'){
                        upNotification(response.data[0].message, 'warning')
                    }else{

                        upNotification(response.data, 'success')
                    }

                },
                error: function(request, status, error){
                    jQuery('#invite').prop('disabled', false);
                    upNotification(request.responseJSON.data, 'error')
                }
            });
        });
    });

    /**
     * Remove invited user
     *
     * @action userpro_user_invite_delete
     */
    jQuery(document).on('click', '.up-invitation__buttons a', function (e) {
        e.preventDefault();

        var button = jQuery(this);
        var action = button.data('action');

        var email = button.parents('.up-invitation__buttons').data('user-email');
        var parent = button.parents('.up-invitation__user-block');
        var counter = parent.closest('.up-invitation').closest('.upadmin-panel').prev().find('span');

        var data = {
            action: action,
            email: email,
        };

        jQuery.ajax({
            url: ajaxurl,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            success: function (response) {
                if (response.success === true) {
                    if(action === 'up_delete_invitation'){
                        parent.slideUp();
                        counter.html(response.data.counter);
                        upNotification(response.data.message, 'success')
                    }

                    if(action === 'up_resend_invitation'){
                        upNotification(response.data.message, 'success')
                    }
                }
            }
        });

    });

    /**
     * Resend invitation email
     */
    /* Verification invite */
    jQuery(document).on('click', '.upadmin-invite-u', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-verify-v2');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_verify_invite&user_id=' + link.data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.hide().html(data.admin_tpl).fadeIn();
            }
        });
        return false;
    });

    /* Verify user */
    jQuery(document).on('click', '.upadmin-verify', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-pending-verify');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_verify_user&user_id=' + jQuery(this).data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.fadeOut();
                if (data.count === '0' || data.count == '' || !data.count) {
                    jQuery('.upadmin-bubble-new').remove();
                } else {
                    jQuery('.upadmin-bubble-new').html(data.count);
                }
                jQuery('.toplevel_page_userpro').find('span.update-count').html(data.count);
            }
        });
        return false;
    });

    /* Verify all users */
    jQuery(document).on('click', '.upadmin-verify-all', function (e) {
        e.preventDefault();
        var thisElement = jQuery(this);
        denyApproveActionsWithUser(thisElement, 'verify' );
    });
    

    /* Unverify user */
    jQuery(document).on('click', '.upadmin-unverify', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-pending-verify');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_unverify_user&user_id=' + jQuery(this).data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.fadeOut();
                if (data.count === '0' || data.count == '' || !data.count) {
                    jQuery('.upadmin-bubble-new').remove();
                } else {
                    jQuery('.upadmin-bubble-new').html(data.count);
                }
                jQuery('.toplevel_page_userpro').find('span.update-count').html(data.count);
            }
        });
        return false;
    });

    /* Unverify all users */
    jQuery(document).on('click', '.upadmin-unverify-all', function (e) {
        e.preventDefault();
        var thisElement = jQuery(this);
        denyApproveActionsWithUser(thisElement, 'unverify' );
    });

    /*  Block user */
    jQuery(document).on('click', '.upadmin-block-u', function (e) {
        e.preventDefault();
        $res = window.confirm("Are you sure you want to block this user ?");
        if (!$res) {
            return;
        }
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-block-v2');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_block_account&user_id=' + link.data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.hide().html(data.admin_tpl).fadeIn();
            }
        });
        return false;
    });

    /* Unblock user */
    jQuery(document).on('click', '.upadmin-unblock-u', function (e) {
        e.preventDefault();
        var link = jQuery(this);
        var parent = jQuery(this).parents('.upadmin-block-v2');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_unblock_account&user_id=' + link.data('user'),
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                parent.hide().html(data.admin_tpl).fadeIn();
            }
        });
        return false;
    });
    /* Mouseenter/leave verify user */
    jQuery(document).on('mouseenter', '.upadmin-unverify,.upadmin-verify', function (e) {
        jQuery(this).find('span').show();
    })

    jQuery(document).on('mouseleave', '.upadmin-unverify,.upadmin-verify', function (e) {
        jQuery(this).find('span').hide();
    });

    /* cancel field editing */
    jQuery(document).on('click', '.upadmin-field-zone-cancel', function (e) {
        e.preventDefault();
        jQuery(this).parents('.upadmin-field-zone').hide();
        return false;
    });

    /* chosen select */
    jQuery(".chosen-select").chosen({
        disable_search_threshold: 10
    });

    /* Setup field options (multi choice) */
    jQuery(document).on('change', '#upadmin_n_type', function (e) {
        var type = jQuery(this).val();
        if (type == 'select' || type == 'multiselect' || type == 'radio' || type == 'radio-full' || type == 'checkbox' || type == 'checkbox-full') {
            jQuery('.choicebased').show();
        } else {
            jQuery('.choicebased').hide();
        }
        if (type == 'file') {
            jQuery('.filetypes').show();
        } else {
            jQuery('.filetypes').hide();
        }
    });

    /* Custom input show/hide */
    if (jQuery('#dashboard_redirect_users').val() == 2) {
        jQuery('#dashboard_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display': 'block'});
    }
    if (jQuery('#profile_redirect_users').val() == 2) {
        jQuery('#profile_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display': 'block'});
    }
    if (jQuery('#register_redirect_users').val() == 2) {
        jQuery('#register_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display': 'block'});
    }
    if (jQuery('#login_redirect_users').val() == 2) {
        jQuery('#login_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display': 'block'});
    }
    jQuery('#dashboard_redirect_users,#profile_redirect_users,#register_redirect_users,#login_redirect_users').change(function () {
        if (jQuery(this).val() == 2) {
            jQuery(this).parents('td').find('.userpro-admin-hide-input').css({'display': 'block'});
        } else {
            jQuery(this).parents('td').find('.userpro-admin-hide-input').css({'display': 'none'});
        }
    });

    /* the main field list actions */
    jQuery(document).on('click', '#upadmin-sortable-fields .upadmin-field-actions a', function (e) {
        e.preventDefault();
        var act = jQuery(this).attr('class');
        var field = jQuery(this).parents('li').attr('id').replace('upadmin-', '');
        var load = jQuery(this).parents('.upadmin-fieldlist').find('.upadmin-loader');

        if (act == 'upadmin-field-action-remove') {
            if (!confirm('Are you sure you want to delete field from your fields list?')) return false;
            load.addClass('loading');
            jQuery(this).parents('li').fadeOut();
            jQuery.ajax({
                url: ajaxurl,
                data: 'action=userpro_delete_field&field=' + field,
                dataType: 'JSON',
                type: 'POST',
                success: function (data) {
                    load.removeClass('loading');
                    jQuery('span.upadmin-ajax-fieldcount').html(data.count);
                }
            });
        }

        if (act == 'upadmin-field-action-edit') {
            jQuery(this).parents('li').find('.upadmin-field-zone').toggle();
        }

        return false;
    });

    /* blur field edit */
    jQuery(document).on('change', '#upadmin-sortable-fields .upadmin-field-zone input, #upadmin-sortable-fields .upadmin-field-zone select, #upadmin-sortable-fields .upadmin-field-zone textarea', function (e) {

        var load = jQuery(this).parents('.upadmin-fieldlist').find('.upadmin-loader');
        load.addClass('loading');
        var field = jQuery(this).parents('li').attr('id').replace('upadmin-', '');
        var str = '';
        jQuery(this).parents('li').find('input[type=text]').each(function () {
            str = str + '&' + jQuery(this).attr('id').replace(field + '-', '') + '=' + jQuery(this).val();
        });
        jQuery(this).parents('li').find('textarea').each(function () {
            str = str + '&' + jQuery(this).attr('id').replace(field + '-', '') + '=' + jQuery(this).val();
        });

        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_update_field&field=' + field + str,
            //dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                load.removeClass('loading');
            }
        });

    });

    /* click on action of field */
    jQuery(document).on('click', '.upadmin-groups .upadmin-field-actions a', function (e) {

        e.preventDefault();
        var form = jQuery(this).parents('.upadmin-tpl').find('form');
        var act = jQuery(this).attr('class');
        var proc = jQuery(this).data('proc');
        if (act == 'upadmin-field-action-remove') {
            if (!confirm('Are you sure you want to delete field from this group?')) return false;
            jQuery(this).parents('li').fadeOut(function () {
                jQuery(this).remove();
                form.trigger('submit');
            });
        }
        if (act == 'upadmin-field-action-edit') {
            jQuery(this).parents('li').find('.upadmin-field-zone').toggle();
        }
        if (act == 'upadmin-field-action upadmin-field-action-hideable off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-hideable on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-hidden off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-hidden on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-required off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-required on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-locked off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-locked on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-private off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-private on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-html off') {
            jQuery(this).removeClass('off').addClass('on');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);
            form.trigger('submit');
        }
        if (act == 'upadmin-field-action upadmin-field-action-html on') {
            jQuery(this).removeClass('on').addClass('off');
            jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);
            form.trigger('submit');
        }
        return false;
    });

    /* blur field edit */
    jQuery(document).on('change', '.upadmin-groups .upadmin-field-zone input, .upadmin-groups .upadmin-field-zone select, .upadmin-groups .upadmin-field-zone textarea', function (e) {
        var form = jQuery(this).parents('.upadmin-tpl').find('form');
        form.trigger('submit');
    });

    /* toggle adding new field */
    jQuery(document).on('click', '.upadmin-toggle-new', function (e) {
        e.preventDefault();
        var new_field = jQuery('.upadmin-new');
        if (new_field.is(':hidden')) {
            new_field.show();

            /* chosen dropdowns */
            jQuery(".upadmin-new select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
            jQuery(".upadmin-new *[class*=chzn], .upadmin-new .chosen-container").remove();
            jQuery(".upadmin-new .chosen-select").chosen({
                disable_search_threshold: 10
            });

        } else {
            new_field.hide();
        }
        return false;
    });

    /* icon clicks */
    jQuery(document).on('click', '.upadmin-icon-abs a:not(.upadmin-noajax)', function (e) {
        e.preventDefault();
        return false;
    });

    /* toggle/un-toggle field groups */
    jQuery(document).on('click', '.upadmin-icon-abs a.max', function (e) {
        var tpl = jQuery(this).parents('.upadmin-tpl');
        tpl.find('.upadmin-tpl-body').removeClass('max').addClass('min');
        tpl.find('.upadmin-tpl-head').removeClass('max').addClass('min');
        jQuery(this).removeClass('max').addClass('min');
    });

    jQuery(document).on('click', '.upadmin-icon-abs a.min', function (e) {
        var tpl = jQuery(this).parents('.upadmin-tpl');
        tpl.find('.upadmin-tpl-body').removeClass('min').addClass('max');
        tpl.find('.upadmin-tpl-head').removeClass('min').addClass('max');
        jQuery(this).removeClass('min').addClass('max');
    });

    /* cancel new field div */
    jQuery(document).on('click', '#upadmin_n_cancel', function (e) {
        e.preventDefault();
        var new_field = jQuery('.upadmin-new');
        new_field.hide();
        return false;
    });

    /* reset original fields */
    jQuery(document).on('click', '.upadmin-reset-fields', function (e) {

        e.preventDefault();
        form = jQuery(this).parents('.upadmin-fieldlist');
        if (!confirm('This will restore original plugin fields. Are you sure?')) return false;
        form.find('.upadmin-loader').addClass('loading');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_restore_builtin_fields',
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                form.find('.upadmin-loader').removeClass('loading');
                jQuery('span.upadmin-ajax-fieldcount').html(data.count);
                jQuery('ul#upadmin-sortable-fields').html(data.html);
            }
        });
        return true;
    });

    /* reset all groups */
    jQuery(document).on('click', '.upadmin-reset-groups', function (e) {
        e.preventDefault();
        form = jQuery(this).parents('.upadmin-groups');
        if (!confirm('This will restore original fields for ALL groups. Are you sure?')) return false;
        form.find('.upadmin-loader').addClass('loading');
        jQuery.ajax({
            url: ajaxurl,
            data: 'action=userpro_restore_builtin_groups',
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                form.find('.upadmin-loader').removeClass('loading');
                jQuery('.upadmin-groups-view').html(data.html);
                jQuery('.upadmin-tpl-body ul').sortable({
                    receive: function (e, ui) {
                        copyHelper = null;
                    }
                });
            }
        });
        return true;
    });

    /* Publish new field */
    jQuery(document).on('submit', '.upadmin-new form', function (e) {
        e.preventDefault();
        form = jQuery(this);
        form.find('span.error-text').remove();
        form.find('input').removeClass('error');
        form.parents('.upadmin-fieldlist').find('.upadmin-loader').addClass('loading');
        jQuery.ajax({
            url: ajaxurl,
            data: form.serialize() + '&action=userpro_create_field',
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                form.parents('.upadmin-fieldlist').find('.upadmin-loader').removeClass('loading');
                if (data.error) {
                    jQuery.each(data.error, function (i, v) {
                        jQuery('#' + i).addClass('error').focus().after('<span class="error-text">' + v + '</span>');
                    });
                } else {
                    form.find('input').removeClass('error');
                    jQuery('ul#upadmin-sortable-fields').prepend(data.html);
                    jQuery('span.upadmin-ajax-fieldcount').html(data.count);
                }
            }
        });
        return false;
    });

    /* reset single group */
    jQuery(document).on('click', '.upadmin-tpl a.resetgroup', function (e) {
        e.preventDefault();
        if (!confirm('This will restore original fields for this GROUP. Are you sure?')) return false;
        var form = jQuery(this).parents('.upadmin-tpl').find('form');
        var role = form.data('role');
        form.find('.upadmin-tpl-head').append('<img src="' + form.data('loading') + '" alt="" class="upadmin-miniload" />');
        jQuery.ajax({
            url: ajaxurl,
            data: form.serialize() + '&action=userpro_reset_group&role=' + role,
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                form.find('.upadmin-miniload').remove();
                form.parents('.upadmin-tpl').replaceWith(data.html);
                form.parents('.upadmin-tpl').find('.upadmin-tpl-body ul').sortable({
                    receive: function (e, ui) {
                        copyHelper = null;
                    }
                });
            }
        });
        return false;
    });

    /* Save forms */
    jQuery(document).on('click', '.upadmin-tpl a.saveform', function (e) {
        form = jQuery(this).parents('.upadmin-tpl').find('form');
        form.trigger('submit');
    });

    jQuery(document).on('submit', '.upadmin-tpl form', function (e) {
        e.preventDefault();
        form = jQuery(this);
        var role = jQuery(this).data('role');
        var group = jQuery(this).data('group');

        form.find('.upadmin-tpl-head').append('<img src="' + form.data('loading') + '" alt="" class="upadmin-miniload" />');

        jQuery.ajax({
            url: ajaxurl,
            data: form.serialize() + '&action=userpro_save_group&role=' + role + '&group=' + group,
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                form.find('.upadmin-miniload').remove();
            }
        });
        return false;
    });

    /* The groups that will receive fields */
    jQuery('.upadmin-tpl-body ul').sortable({
        receive: function (e, ui) {
            copyHelper = null;
            var list = jQuery(this).parents('.upadmin-tpl-body');
            jQuery.each(list.find("li[data-special^='newsection'],input[data-special^='newsection'],select[data-special^='newsection']"), function (i, v) {
                section_word = 'newsection' + i;
                jQuery(this).data('special', section_word);
                jQuery(this).find('input').each(function () {
                    jQuery(this).attr('name', jQuery(this).attr('name').replace('newsection', section_word));
                    jQuery(this).attr('id', jQuery(this).attr('id').replace('newsection', section_word));
                });
                jQuery(this).find('select').each(function () {
                    jQuery(this).attr('name', jQuery(this).attr('name').replace('newsection', section_word));
                    jQuery(this).attr('id', jQuery(this).attr('id').replace('newsection', section_word));
                });
            });
        }
    });

    /* Add new section field */
    jQuery('ul#upadmin-newsection').sortable({
        connectWith: ".upadmin-tpl-body ul",
        forcePlaceholderSize: false,
        helper: function (e, li) {
            copyHelper = li.clone().insertAfter(li);
            return li.clone();
        },
        stop: function () {
            copyHelper && copyHelper.remove();
        }
    });

    /* Moving out field/sorting between fields */
    var itemList = jQuery('ul#upadmin-sortable-fields');
    itemList.sortable({
        connectWith: ".upadmin-tpl-body ul",
        forcePlaceholderSize: false,
        helper: function (e, li) {
            copyHelper = li.clone().insertAfter(li);
            return li.clone();
        },
        stop: function () {
            copyHelper && copyHelper.remove();
        },
        update: function (event, ui) {
            opts = {
                url: ajaxurl,
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data: {
                    action: 'userpro_field_sort',
                    order: itemList.sortable('toArray').toString()
                },
                success: function (data) {
                    return;
                },
                error: function (xhr, textStatus, e) {
                    return;
                }
            };
            jQuery.ajax(opts);
        }
    });

    jQuery('#reset-options').click(function (e) {

        e.preventDefault();

        swal({
            title: "Are you sure you want to reset the settings to default ?",
            text: "You will reset all UserPro settings",
            icon: "warning",
            buttons: [
                'No, cancel it!',
                'Yes, I am sure!'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {
                var data = {
                  'action': 'userpro_reset_option',
                   'reset_options': true,
                };
                upAjaxRequest(data);

                setTimeout(function(){
                    window.location.reload();
                },3000);
            } else {
                return false;
            }
        });
    });
});

function upAjaxRequest(data, successAction) {

    jQuery.ajax({
        url: ajaxurl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        success: function (response) {
            upNotification(response.data.message, response.data.messageType)
        }
    });
}


/***************** Ajax call for save email template ************************/

function up_process_email_template(elm, type) {
    var template = jQuery(elm).data('template');
    jQuery.ajax({
        url: ajaxurl,
        data: {'action': 'userpro_' + type + '_email_template', 'template': template},
        dataType: 'JSON',
        type: 'POST',
        success: function (data) {
            jQuery(elm).parents('.up-html-template-link').html(data.output);
        }
    });
}

function up_preview_email(elm) {
    var template = jQuery(elm).data('template');
    if (jQuery('body').find('.userpro-overlay').length == 0) {
        jQuery('body').append('<div class="userpro-overlay"/><div class="userpro-overlay-inner" style="width:auto;"/>');
    }
    jQuery.ajax({
        url: ajaxurl,
        data: {'action': 'userpro_preview_email', 'template': template},
        dataType: 'JSON',
        type: 'POST',
        success: function (data) {
            jQuery('.userpro-overlay-inner').append(data.output);
            userpro_overlay_center('.userpro-overlay-inner');
        }
    });
}


function userpro_overlay_center(container) {
    if (container.length) {
        jQuery(container).animate({
            'top': jQuery(window).innerHeight() / 2,
            'margin-top': '-' + jQuery(container).find('.userpro-preview-container').innerHeight() / 2 + 'px'
        });
    }
}

jQuery(document).on('click', '.userpro-overlay, a.userpro-close-popup', function (e) {
    jQuery('.userpro-overlay').fadeOut(function () {
        jQuery('.userpro-overlay').remove()
    });
    jQuery('.userpro-overlay-inner').fadeOut(function () {
        jQuery('.userpro-overlay-inner').remove()
    });
});


jQuery(document).on('click', '.up-notification__close',function (e) {

    jQuery(this).closest('.up-notification').addClass('up-notification__slide-out');

});

function upNotification(message, status){

    var html = "";
    var notification = jQuery('.up-notification');
    var holder = jQuery('body');

    notification.remove();

    html += "<div class=\"up-notification up-notification--"+ status + "\">\n" +
        "<div class=\"up-notification__icon\">\n";

    switch (status) {

        case "success":

         html += "<i class=\"fas fa-check\"></i>";

            break;

        case "error":

            html += "<i class=\"fas fa-times\"></i>";

            break;

        case "warning":

            html += "<i class=\"fas fa-exclamation\"></i>";

            break;

    }

    html += "</div>\n" +
        "<div class=\"up-notification__body\">\n" +
        "<p> " + message + "</p>\n" +
        "</div>\n" +
        "<button class=\"up-notification__close\"><i class=\"fa fa-times\"></i></button>\n" +
        "</div>";


    jQuery( document ).ready(function() {

       holder.append(html);

        setTimeout(function(){
            jQuery('.up-notification').addClass('up-notification__slide-out');
        }, 4000);

    });

}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
};

function denyApproveActionsWithUser(thisElement, actionType){
    var containerElements = thisElement.parent().parent().children(":eq(1)").children();
    var users_ids = thisElement.data('user'); 
    var data = {
        'action': 'userpro_verifyUnverifyAllUsers',
        'user_id': users_ids,
        'action_type': actionType,
    };
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        success: function (response) {
            containerElements.fadeOut();
            jQuery('.toplevel_page_userpro').find('span.update-count').html(response.data.count);
            upNotification(response.data.message, response.data.messageType);
        }
    });
    return false;
}

