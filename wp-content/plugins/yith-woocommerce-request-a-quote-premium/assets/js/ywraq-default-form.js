var rqa_captcha;

function ywraq_recaptcha(){
   var raq_recaptcha = jQuery('form[name="yith-ywraq-default-form"]').find('.g-recaptcha');
    if (typeof grecaptcha != "undefined" &&  raq_recaptcha.length > 0 ) {
        rqa_captcha = grecaptcha.render('recaptcha_quote', {'sitekey' : raq_recaptcha.data('sitekey')});
    }
}

jQuery(document).ready(function ($) {
    "use strict";

    var ywraq_default_form_init = function (trigger) {

        var select = $('.ywraq-multiselect-type, select.select'),
            datepicker = $('.ywraq-datepicker-type'),
            timepicker = $('.ywraq-timepicker-type'),
            ywraq_default_form = $('form[name="yith-ywraq-default-form"]'),
            ajax_loader = (typeof ywraq_form !== 'undefined') ? ywraq_form.block_loader : false,
            submit_button = $('.raq-send-request'),
            error = '<span class="ywraq_error"></span>',

            ywraq_ismail = function (val) {
                /* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
                var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
                return pattern.test(val);
            },
            ywraq_upload_has_value = function (elem) {
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    if (typeof elem[0].files[0] != 'undefined') {
                        return true;
                    }
                    return false;
                }
            },
            ywraq_is_valid_upload = function (elem) {
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    //get the file size and file type from file input field
                    var msg = '';
                    if (typeof elem[0].files[0] != 'undefined') {

                        var max_extension = elem.data('max-size'),
                            type_allowed = elem.data('allowed'),
                            type_allowed_array = type_allowed.split(','),
                            fsize = elem[0].files[0].size,
                            fname = elem[0].files[0].name,
                            ext = fname.split('.').pop().toLowerCase();

                        //do something if file size more than 1 mb (1048576)
                        if (fsize > parseInt(max_extension) * 1048576) {
                            msg = ywraq_form.err_msg_upload_filesize + max_extension + 'MB';
                        } else if (!type_allowed_array.includes(ext)) {
                            msg = ywraq_form.err_msg_allowed_extension + type_allowed;
                        }
                    }

                    return msg;
                }
            },
            ywraq_error = function (elem, msg) {

                if (!elem.next('.ywraq_error').length) {
                    elem.after(error);
                }
                // add error
                elem.next('.ywraq_error').html(msg);
            },
            ywraq_validate_field = function () {
                var t = $(this),
                    parent = t.closest('p.form-row'),
                    value = t.val(),
                    msg = '';

                if (!value && parent.hasClass('validate-required') && !t.is('[type="file"]')) {
                    msg = ywraq_form.err_msg;
                    ywraq_error(t, msg);
                } else if (value && parent.hasClass('validate-email') && !ywraq_ismail(value)) {
                    ywraq_error(t, ywraq_form.err_msg_mail);
                } else if (t.is('[type="file"]')) {
                    if (parent.hasClass('validate-required') && !ywraq_upload_has_value(t)) {
                        msg = ywraq_form.err_msg;
                    }
                    else if (parent.hasClass('validate-file')) {
                        msg = ywraq_is_valid_upload(t);
                    }
                    ywraq_error(t, msg);
                }
                else {
                    t.next('.ywraq_error').remove();
                }
            },
            scroll_to_notices = function () {
                var scrollElement = $('.woocommerce-error, .woocommerce-message'),
                    isSmoothScrollSupported = 'scrollBehavior' in document.documentElement.style;

                if (!scrollElement.length) {
                    scrollElement = ywraq_default_form;
                }

                if (scrollElement.length) {
                    if (isSmoothScrollSupported) {
                        scrollElement[0].scrollIntoView({
                            behavior: 'smooth'
                        });
                    } else {
                        $('html, body').animate({
                            scrollTop: (scrollElement.offset().top - 100)
                        }, 1000);
                    }
                }
            },
            ywraq_submit_form = function (e) {
                e.preventDefault();

                var data = ywraq_default_form.ywraq_serialize_files();

                data.append('context', 'frontend');

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    contentType: false,
                    processData: false,
                    url: ywraq_form.ajaxurl.toString().replace('%%endpoint%%', 'ywraq_submit_default_form'),
                    beforeSend: function () {
                        submit_button.prop('disabled', true).after(' <img src="' + ajax_loader + '" class="ywraq-loader" >');
                    },
                    complete: function () {
                        submit_button.prop('disabled', false).next().remove();
                    },
                    success: function (response) {
                        if ('success' === response.result) {
                            submit_button.prop('disabled', true);
                            window.location.href = response.redirect;
                        }
                        if ('failure' === response.result) {

                            // Remove notices from all sources
                            $('.woocommerce-error, .woocommerce-message').remove();

                            // Add new errors returned by this event
                            if (response.messages) {
                                ywraq_default_form.prepend('<div class="woocommerce-error woocommerce-message">' + response.messages + '</div>');
                            } else {
                                ywraq_default_form.prepend(response);
                            }

                            // Lose focus for all fields
                            ywraq_default_form.find('.input-text, select, input:checkbox').trigger('validate').blur();
                            scroll_to_notices();
                            if (typeof grecaptcha != "undefined" && typeof rqa_captcha != "undefined") {
                                grecaptcha.reset( rqa_captcha );
                            }
                        }
                    }
                });

                return false;
            },

            ywraq_toggle_create_account = function () {
                $('div.create-account').hide();

                if ($(this).is(':checked')) {
                    // Ensure password is not pre-populated.
                    $('#account_password').val('').change();
                    $('div.create-account').slideDown();
                }
            };



        $.fn.ywraq_serialize_files = function () {
            var obj = $(this);
            /* ADD FILE TO PARAM AJAX */
            var formData = new FormData();
            $.each($(obj).find("input[type='file']"), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });

            var params = $(obj).serializeArray();

            $.each(params, function (i, val) {
                if (val.name) {
                    formData.append(val.name, val.value);
                }
            });

            return formData;
        };

        if (select && typeof $.fn.select2 != 'undefined') {

            $.each(select, function () {
                var s = $(this),
                    sid = s.attr('id');

                if ($('#s2id_' + sid).length) {
                    return;
                }

                s.select2({
                    placeholder: s.data('placeholder'),
                    dropdownCssClass: 'ywraq-select2'
                });
            });
        }


            $(document).on('change', '#billing_state', function(){
               var $this = $(this);
               if( $this.val() ) {
                   $this.next().next('.ywraq_error').remove();
               }
            });



        if (typeof $.fn.datepicker != 'undefined' && datepicker) {
            $.each(datepicker, function () {

                var dptop = $(this).offset().top,
                    dpleft = $(this).offset().left;
                $(this).datepicker({
                    dateFormat: $(this).data('format') || "dd-mm-yy",
                    beforeShow: function () {
                        setTimeout(function () {
                            if (!trigger) {
                                $('#ui-datepicker-div').wrap('<div class="yith_datepicker"></div>').css(
                                    {
                                        'z-index': 99999999999999,
                                        'top': dptop + 45,
                                        'left': dpleft
                                    });

                            }


                            $('#ui-datepicker-div').show();
                        }, 0);
                    },
                    onClose: function () {
                        $('#ui-datepicker-div').hide();
                        $('#ui-datepicker-div').unwrap();
                    }
                });
            });
        }

        if (typeof $.fn.timepicki != 'undefined' && timepicker) {
            $.each(timepicker, function () {
                $(this).timepicki({
                    reset: true,
                    disable_keyboard_mobile: true,
                    show_meridian: ywraq_form.time_format,
                    max_hour_value: ywraq_form.time_format ? '12' : '23',
                    min_hour_value: ywraq_form.time_format ? '1' : '0',
                    overflow_minutes: true,
                    increase_direction: 'up'
                });
            });

            $(document).on('click', '.reset_time', function (ev) {
                ev.preventDefault();
            });
        }

        ywraq_default_form.on('blur', '.input-text', ywraq_validate_field);
        ywraq_default_form.on('click', '.ywraq-upload-type', ywraq_validate_field);
        ywraq_default_form.on('change', 'select, input:checkbox', ywraq_validate_field);
        ywraq_default_form.on('click', '.select2-selection', function(){
            $(document).find('.select2-container--open').addClass('ywraq-select2');
        });

        ywraq_default_form.on('submit', ywraq_submit_form);
        $(document).find('input#createaccount').on('change', ywraq_toggle_create_account).change();
    };



    ywraq_default_form_init(false);
    $(document).on('yith_wacp_popup_after_opening', ywraq_default_form_init);



});

/*
 * Author: @senthil2rajan
 * plugin: timepicker
 * website: senthilraj.github.io/Timepicki
 */

!function (i) {
    i.fn.timepicki = function (t) {
        var e = {
            format_output: function (i, t, e) {
                return n.show_meridian ? i + " : " + t + " : " + e : i + " : " + t
            },
            increase_direction: "down",
            custom_classes: "",
            min_hour_value: 1,
            max_hour_value: 12,
            show_meridian: !0,
            step_size_hours: "1",
            step_size_minutes: "1",
            overflow_minutes: !1,
            disable_keyboard_mobile: !1,
            reset: !1,
            on_change: null
        }, n = i.extend({}, e, t);
        return this.each(function () {
            function t(t) {
                return i.contains(m[0], t[0]) || m.is(t)
            }

            function e(i, t) {
                var e = f.find(".ti_tx input").val(), a = f.find(".mi_tx input").val(), r = "";
                n.show_meridian && (r = f.find(".mer_tx input").val()), 0 === e.length || 0 === a.length || n.show_meridian && 0 === r.length || (l.attr("data-timepicki-tim", e), l.attr("data-timepicki-mini", a), n.show_meridian ? (l.attr("data-timepicki-meri", r), l.val(n.format_output(e, a, r))) : l.val(n.format_output(e, a))), null !== n.on_change && n.on_change(l[0]), t && s()
            }

            function a() {
                r(n.start_time), f.fadeIn();
                var t = f.find("input:visible").first();
                t.focus();
                var e = function (n) {
                    if (9 === n.which && n.shiftKey) {
                        t.off("keydown", e);
                        var a = i(":input:visible:not(.timepicki-input)"), s = a.index(l), r = a.get(s - 1);
                        r.focus()
                    }
                };
                t.on("keydown", e)
            }

            function s() {
                f.fadeOut()
            }

            function r(i) {
                var t, e, a, s;
                l.is("[data-timepicki-tim]") ? (e = Number(l.attr("data-timepicki-tim")), a = Number(l.attr("data-timepicki-mini")), n.show_meridian && (s = l.attr("data-timepicki-meri"))) : "object" == typeof i ? (e = Number(i[0]), a = Number(i[1]), n.show_meridian && (s = i[2])) : (t = new Date, e = t.getHours(), a = t.getMinutes(), s = "AM", e > 12 && n.show_meridian && (e -= 12, s = "PM")), 10 > e ? f.find(".ti_tx input").val("0" + e) : f.find(".ti_tx input").val(e), 10 > a ? f.find(".mi_tx input").val("0" + a) : f.find(".mi_tx input").val(a), n.show_meridian && (10 > s ? f.find(".mer_tx input").val("0" + s) : f.find(".mer_tx input").val(s))
            }

            function o(i, t) {
                var e = "time", a = Number(f.find("." + e + " .ti_tx input").val()), s = Number(n.min_hour_value),
                    r = Number(n.max_hour_value), o = Number(n.step_size_hours);
                if (i && i.hasClass("action-next") || "next" === t) if (a + o > r) {
                    var d = s;
                    d = 10 > d ? "0" + d : String(d), f.find("." + e + " .ti_tx input").val(d)
                } else a += o, 10 > a && (a = "0" + a), f.find("." + e + " .ti_tx input").val(a); else if (i && i.hasClass("action-prev") || "prev" === t) {
                    var u = Number(n.min_hour_value);
                    if (u > a - o) {
                        var l = r;
                        l = 10 > l ? "0" + l : String(l), f.find("." + e + " .ti_tx input").val(l)
                    } else a -= o, 10 > a && (a = "0" + a), f.find("." + e + " .ti_tx input").val(a)
                }
            }

            function d(i, t) {
                var e = "mins", a = Number(f.find("." + e + " .mi_tx input").val()), s = 59,
                    r = Number(n.step_size_minutes);
                i && i.hasClass("action-next") || "next" === t ? a + r > s ? (f.find("." + e + " .mi_tx input").val("00"), n.overflow_minutes && o(null, "next")) : (a += r, 10 > a ? f.find("." + e + " .mi_tx input").val("0" + a) : f.find("." + e + " .mi_tx input").val(a)) : (i && i.hasClass("action-prev") || "prev" === t) && (-1 >= a - r ? (f.find("." + e + " .mi_tx input").val(s + 1 - r), n.overflow_minutes && o(null, "prev")) : (a -= r, 10 > a ? f.find("." + e + " .mi_tx input").val("0" + a) : f.find("." + e + " .mi_tx input").val(a)))
            }

            function u(i, t) {
                var e = "meridian", n = null;
                n = f.find("." + e + " .mer_tx input").val(), i && i.hasClass("action-next") || "next" === t ? "AM" == n ? f.find("." + e + " .mer_tx input").val("PM") : f.find("." + e + " .mer_tx input").val("AM") : (i && i.hasClass("action-prev") || "prev" === t) && ("AM" == n ? f.find("." + e + " .mer_tx input").val("PM") : f.find("." + e + " .mer_tx input").val("AM"))
            }

            var l = i(this), c = l.outerHeight();
            c += 10, i(l).wrap("<div class='time_pick'>");
            var m = i(this).parents(".time_pick"),
                v = "down" === n.increase_direction ? "<div class='prev action-prev'></div>" : "<div class='prev action-next'></div>",
                p = "down" === n.increase_direction ? "<div class='next action-next'></div>" : "<div class='next action-prev'></div>",
                _ = i("<div class='timepicker_wrap " + n.custom_classes + "'><div class='arrow_top'></div><div class='time'>" + v + "<div class='ti_tx'><input type='text' class='timepicki-input'" + (n.disable_keyboard_mobile ? "readonly" : "") + "></div>" + p + "</div><div class='mins'>" + v + "<div class='mi_tx'><input type='text' class='timepicki-input'" + (n.disable_keyboard_mobile ? "readonly" : "") + "></div>" + p + "</div>");
            n.show_meridian && _.append("<div class='meridian'>" + v + "<div class='mer_tx'><input type='text' class='timepicki-input' readonly></div>" + p + "</div>"), n.reset && _.append("<div><a href='#' class='reset_time'>Reset</a></div>"), m.append(_);
            var f = i(this).next(".timepicker_wrap"), h = (f.find("div"), m.find("input"));
            i(".reset_time").on("click", function (i) {
                l.val(""), s()
            }), i(".timepicki-input").keydown(function (t) {
                var e = i(this).val().length;
                -1 !== i.inArray(t.keyCode, [46, 8, 9, 27, 13, 110, 190]) || 65 == t.keyCode && t.ctrlKey === !0 || t.keyCode >= 35 && t.keyCode <= 39 || ((t.shiftKey || t.keyCode < 48 || t.keyCode > 57) && (t.keyCode < 96 || t.keyCode > 105) || 2 == e) && t.preventDefault()
            }), i(document).on("click", function (n) {
                if (!i(n.target).is(f) && "block" == f.css("display") && !i(n.target).is(i(".reset_time"))) if (i(n.target).is(l)) {
                    var s = 0;
                    f.css({top: c + "px", left: s + "px"}), a()
                } else e(n, !t(i(n.target)))
            }), l.on("focus", a), h.on("focus", function () {
                var t = i(this);
                t.is(l) || t.select()
            }), h.on("keydown", function (t) {
                var e, a = i(this);
                38 === t.which ? e = "down" === n.increase_direction ? "prev" : "next" : 40 === t.which && (e = "down" === n.increase_direction ? "next" : "prev"), a.closest(".timepicker_wrap .time").length ? o(null, e) : a.closest(".timepicker_wrap .mins").length ? d(null, e) : a.closest(".timepicker_wrap .meridian").length && n.show_meridian && u(null, e)
            }), h.on("blur", function () {
                setTimeout(function () {
                    var n = i(document.activeElement);
                    n.is(":input") && !t(n) && (e(), s())
                }, 0)
            });
            var x = f.find(".action-next"), k = f.find(".action-prev");
            i(k).add(x).on("click", function () {
                var t = i(this);
                "time" == t.parent().attr("class") ? o(t) : "mins" == t.parent().attr("class") ? d(t) : n.show_meridian && u(t)
            })
        })
    }
}(jQuery);
