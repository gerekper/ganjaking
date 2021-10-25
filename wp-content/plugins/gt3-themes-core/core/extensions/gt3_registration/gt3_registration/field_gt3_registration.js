/* global redux_change, wp */

(function($) {
    "use strict";
    $.redux = $.redux || {};
    $(document).ready(function() {
        $.redux.gt3_registration();
        $.redux.gt3_account();
        $.redux.gt3_activation_refresh();
    });
    $.redux.gt3_registration = function() {

        var jqOnError = function(xhr, textStatus, errorThrown ) {
            console.log('was 500 (Internal Server Error) but we try to load import again');
            setTimeout(function() {
                location.reload();
            }, 3000);

        };

        function ajaxFunc(data, parent, callback) {
            jQuery.post(ajaxurl,
                {
                    action: "gt3_registration",
                    nonce: data.nonce,
                    type: data.type,
                    code: data.code,
                    field_id: data.field_id/*
                    wbc_import:data.wbc_import,
                    content:i*/
                }
                , function (response) {
                    callback.call(this, response, parent, data)
                }).fail(jqOnError)
        }

        function activate_deactivate_callback(response, parent, data) {
            /*parent.parents('fieldset.wbc_importer.redux-field').append(response);*/
            var element = parent.find('.gt3_register__process');
            element.removeClass('msg_error').removeClass('msg_success');

            if (response.msg != 'Congrats! Your purchase code has been activated successfully.' || !!+response.already_linked) {
                element.html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>' + response.msg).addClass('animation_done').addClass('msg_' + response.msg_type).removeClass('animation_begin');
            } else {
                element.removeClass('animation_begin').addClass('animation_done').addClass('msg_clear');
                element.slideUp('300');
            }
            if (response.action_done == 'register_active') {
                if (!!+response.already_linked == true) {
                    element.parents('.gt3_register__popup_container').find('.gt3_info_container').slideUp('300');
                    setTimeout(function () {

                        location.reload();
                    }, 3000);
                } else {
                    element.parents('.gt3_register__popup_container').find('.gt3_account_submit_container').slideDown('300');
                    element.parents('.gt3_register__popup_container').find('.gt3_register__key .key').html(data.code);
                }
                parent.addClass('gt3_register_active');
                parent.children('.gt3_info_container').slideUp('300');
                parent.find('input.regular-text')[0].setAttribute("readonly", "readonly");
                jQuery('.redux-action_bar').find('input').each(function () {
                    jQuery(this).removeAttr('disabled');
                })
            }
            if (response.action_done == 'register_deactive') {
                parent.removeClass('gt3_register_active');
                element.parents('.gt3_register__popup_container').find('.gt3_info_container').slideUp('3000');
                parent.children('.gt3_info_container').slideUp('3000');
                parent.find('input.regular-text').val('').removeAttr('readonly');
                jQuery('.redux-action_bar').find('input').each(function () {
                    this.setAttribute("disabled", "disabled");
                })
                setTimeout(function () {
                    location.reload();
                }, 3000);
            }
        }

        function check_code_callback(response, parent, data) {
            /*parent.parents('fieldset.wbc_importer.redux-field').append(response);*/
            var element = parent.find('.gt3_register__process');
            element.removeClass('msg_error').removeClass('msg_success');

            if (response.msg_type === 'error') {
                element.html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>' + response.msg).addClass('animation_done').addClass('msg_error').removeClass('animation_begin');
            } else {
                if (!!+response.already_linked === true) {
                    parent.find('.gt3_register__submit').click();
                } else {
                    element.slideUp(100);
                    // parent.find('.gt3_register__process').remove();
                    element.parents('.gt3_register__popup_container').find('.gt3_account_submit_container').slideDown('300');
                    element.parents('.gt3_register__popup_container').find('.gt3_register__key .key').html(data.code);
                }
                // parent.addClass('gt3_register_active');
                parent.children('.gt3_info_container').slideUp('300');
                parent.find('input.regular-text')[0].setAttribute("readonly", "readonly");
                jQuery('.redux-action_bar').find('input').each(function () {
                    jQuery(this).removeAttr('disabled');
                })
            }
        }

        $('.gt3_register_container .gt3_register__check').on('click', function () {
            var parent = $(this).parents('.gt3_register_container');
            var value = parent.find('input.regular-text').val();
            value = value.trim();
            if (value.length !== 22 && value.length !== 36) return;


            var data = $(this).data();
            data.nonce = parent.attr("data-nonce");
            parent.children('.gt3_account_submit_container').remove();

            data.type = 'check_code';
            if (parent.find('.gt3_register__process').length) {
                parent.find('.gt3_register__process').html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>Checking process is running...').removeClass('animation_done').removeClass('msg_clear').addClass('animation_begin').slideDown('300');
                parent.find('.gt3_register__popup').addClass('popup_start');
            } else {
                parent.find('.gt3_register__popup_container').prepend('<div class="gt3_register__process animation_begin"><div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>Checking process is running...</div>');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }
            data.code = parent.find('input.regular-text').val();
            data.field_id = parent.parent()[0].getAttribute('data-id');
            ajaxFunc(data, parent, check_code_callback);
        });

        $('.gt3_register_container .gt3_register__submit,.gt3_register_container .gt3_register__deregister').on('click',function(){
            var data = $(this).data();
            var parent = $(this).parents('.gt3_register_container');
            data.nonce = parent.attr("data-nonce");

            parent.children('.gt3_account_submit_container').remove();

            if ($(this).hasClass('gt3_register__deregister')) {
                data.type = 'deregister';
            }else{
                data.type = 'register_not_active';
            }
            if (parent.find('.gt3_register__process').length) {
                parent.find('.gt3_register__process').html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(data.type == 'deregister' ? 'Deactivation' : 'Activation')+' process is running...').removeClass('animation_done').removeClass('msg_clear').addClass('animation_begin').slideDown('300');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }else{
                parent.find('.gt3_register__popup_container').prepend('<div class="gt3_register__process animation_begin"><div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(data.type == 'deregister' ? 'Deactivation' : 'Activation')+' process is running...</div>');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }
            data.code = parent.find('input.regular-text').val();
            data.field_id = parent.parent()[0].getAttribute('data-id');
            ajaxFunc (data,parent, activate_deactivate_callback);
        })


    };
    $.redux.gt3_account = function() {

        var jqOnError = function(xhr, textStatus, errorThrown ) {
            console.log('was 500 (Internal Server Error)');
            setTimeout(function() {
                location.reload();
            }, 3000);
        };

        function ajaxFunc (data,parent){
            jQuery.post(ajaxurl,
                {
                    action:"gt3__account_registration",
                    nonce:data.nonce,
                    code:data.code,
                    email:data.email,
                    field_id:data.field_id/*
                    wbc_import:data.wbc_import,
                    content:i*/
                }
                , function(response){
                    /*parent.parents('fieldset.wbc_importer.redux-field').append(response);*/
                    if (response.length > 0 && response != '0') {
                        response = JSON.parse(response);
                        var element = parent.find('.gt3_register__process');
                        element.removeClass('msg_error').removeClass('msg_success').removeClass('msg_clear');
                        element.html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>' + response.msg).addClass('animation_done').addClass('msg_'+response.msg_type).removeClass('animation_begin');

                        parent.children('.gt3_account_submit_container').remove();

                        if (response.msg_type == 'error') {
                            element.parents('.gt3_register__popup_container').find('.gt3_account_submit_container').slideDown('300');
                        }

                        if (response.msg_type == 'success') {
                            parent.find('.gt3_account_submit_success_container').find('.gt3_account_emai_holder').html(data.email);
                            parent.find('.gt3_account_submit_success_container').slideDown('300');
                        }

                        setTimeout(function () {
                            location.reload();
                        }, 3000);

                    } else {

                    }


            }).fail(jqOnError)
        }

        $('.gt3_account_submit_container .gt3_account_submit').on('click',function(){
            var data = $(this).data();
            var parent = $(this).parents('.gt3_register_container');
            data.nonce = parent.attr("data-nonce");

            if (parent.find('.gt3_register__process').length) {
                parent.find('.gt3_account_submit_container').slideUp(200);
                parent.find('.gt3_register__process').html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(/*data.type == 'deregister' ? 'Deactivation' : */'Registration')+' process is running...').removeClass('animation_done').addClass('animation_begin').removeClass('msg_clear').slideDown(300);
                parent.find('.gt3_register__popup').addClass('popup_start');
            }else{
                parent.find('.gt3_account_submit_container').slideUp(200);
                parent.find('.gt3_register__popup_container').prepend('<div class="gt3_register__process animation_begin"><div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(/*data.type == 'deregister' ? 'Deactivation' :*/ 'Registration')+' process is running...</div>');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }
            data.code = parent.find('input.regular-text').val();
            data.email = parent.find('input.regular-text.gt3_account').val();
            data.field_id = parent.parent()[0].getAttribute('data-id');
            ajaxFunc (data,parent);
        })

        $('.gt3_register__popup_close').on('click',function(){
            var popup = $('.gt3_register__popup');
            if (popup.hasClass('popup_start')) {
                popup.removeClass('popup_start')
                popup.find('.gt3_info_container').slideUp('300');
                popup.find('.gt3_register__process').removeClass('msg_error').removeClass('msg_success');
            }
        })

        $('.gt3_account_submit_success_button').on('click',function(){
            var popup = $('.gt3_register__popup');
            if (popup.hasClass('popup_start')) {
                popup.find('.gt3_register__popup_container').slideUp('300');
                setTimeout(function() {
                    location.reload();
                }, 250);
            }
        })

    };

    $.redux.gt3_activation_refresh = function() {

        var jqOnError = function(xhr, textStatus, errorThrown ) {
            console.log('was 500 (Internal Server Error)');
            setTimeout(function() {
                location.reload();
            }, 3000);
        };

        function ajaxFunc (data,parent){
            jQuery.post(ajaxurl,
                {
                    action:"gt3__activation_refresh",
                    nonce:data.nonce,
                    code:data.code,
                    email:data.email,
                    field_id:data.field_id/*
                    wbc_import:data.wbc_import,
                    content:i*/
                }
                , function(response){
                    /*parent.parents('fieldset.wbc_importer.redux-field').append(response);*/
                    console.log(response);
                    if (response.length > 0 && response != '0') {
                        response = JSON.parse(response);
                        console.log(response.msg)
                        var element = parent.find('.gt3_register__process');
                        element.removeClass('msg_error').removeClass('msg_success');
                        element.html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>' + response.msg).addClass('animation_done').addClass('msg_'+response.msg_type).removeClass('animation_begin');

                        if (response.action_done == 'register_deactive') {
                            parent.removeClass('gt3_register_active');
                            element.parents('.gt3_register__popup_container').find('.gt3_info_container').slideUp('300');
                            parent.children('.gt3_info_container').slideUp('300');
                            parent.find('input.regular-text').val('').removeAttr('readonly');
                            jQuery('.redux-action_bar').find('input').each(function(){
                                this.setAttribute("disabled", "disabled");
                            })
                        }

                        setTimeout(function() {
                            location.reload();
                        }, 3000);

                    } else {

                    }


            }).fail(jqOnError)
        }

        $('.gt3_register_container .gt3_activation_refresh').on('click',function(){
            var data = $(this).data();
            var parent = $(this).parents('.gt3_register_container');
            data.nonce = parent.attr("data-nonce");

            if (parent.find('.gt3_register__process').length) {
                parent.find('.gt3_register__process').html('<div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(/*data.type == 'deregister' ? 'Deactivation' : */'Refresh')+' process is running...').removeClass('animation_done').addClass('animation_begin');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }else{
                parent.find('.gt3_register__popup_container').prepend('<div class="gt3_register__process animation_begin"><div class="gt3_circle_loader"><div class="gt3_circle_loader__checkmark draw"></div></div>'+(/*data.type == 'deregister' ? 'Deactivation' :*/ 'Refresh')+' process is running...</div>');
                parent.find('.gt3_register__popup').addClass('popup_start');
            }
            data.code = parent.find('input.regular-text').val();
            data.email = parent.find('input.regular-text.gt3_account').val();
            data.field_id = parent.parent()[0].getAttribute('data-id');
            ajaxFunc (data,parent);
        })

    };


    var reg_wrapper = document.querySelector('.gt3_register_container');
    if (reg_wrapper) {
        var reg_input = document.getElementById('gt3_registration_id_purchase_code');
        if (reg_wrapper.classList.contains('gt3_register_active') && reg_input && reg_input.value.trim().length === 0) {
            jQuery('.redux-action_bar').find('input').each(function(){
                this.setAttribute("disabled", "disabled");
            })
        }
    }


})(jQuery);
