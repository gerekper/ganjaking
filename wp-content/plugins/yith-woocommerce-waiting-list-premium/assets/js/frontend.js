/**

 * frontend.js

 *

 * @author Your Inspiration Themes

 * @package YITH WooCommerce Waiting List

 * @version 1.0.0

 */



jQuery(document).ready(function ($) {
    "use strict";


    var addUrlParameter = function (key, value, t) {

            key = encodeURI(key);
            value = encodeURI(value);

            var url_elem = t.parents('#yith-wcwtl-output').find('a.button, form'),

                prop = typeof url_elem.attr('action') != 'undefined' ? 'action' : 'href',
                url = url_elem.attr(prop),
                kvp = url.split('&');


            var i = kvp.length;
            var x;

            // if exists change it!

            while (i--) {
                x = kvp[i].split('=');
                if (x[0] == key) {
                    x[1] = value;
                    kvp[i] = x.join('=');
                    break;
                }

            }

            // if not exists add

            if (i < 0) {
                kvp[kvp.length] = [key, value].join('=');
            }


            url_elem.prop(prop, kvp.join('&'));

        },

        getUrlParameter = function (sURL, data) { // function to get param from url

            var sURLVariables = sURL.split('?')[1].split('&'),
                sParameterName,
                i;


            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');
                data.push({name: sParameterName[0], value: sParameterName[1]});
            }


            return data;

        },

        submit = function (url, button) {

            // build Data

            var data = [
                    {name: 'action', value: 'yith_wcwtl_submit'},
                    {name: 'context', value: 'frontend'}
                ],

                form = button.closest('#yith-wcwtl-output');
            data = getUrlParameter(url, data);

            form.find('.button').addClass('loading');


            $.ajax({

                url: woocommerce_params.ajax_url,
                data: $.param(data),
                method: 'POST',
                dataType: 'json',
                error: function (e) {
                    console.log(e);
                },

                success: function (res) {
                    // add message
                    $(document).find('.yith-wcwtl-ajax-message').remove();
                    form.parents('div.product').before( res.msg );
                    // replace form
                    form.replaceWith(res.form);

                    $('html, body').animate({
                        scrollTop: ($(document).find('.yith-wcwtl-ajax-message').offset().top) - 100
                    }, 500);

                }

            });

        };


    if (ywcwtl.ajax === 'yes') {
        $(document).on('click', '#yith-wcwtl-output a.button', function (ev) {
            ev.preventDefault();
            submit($(this).attr('href'), $(this));
        });


        $(document).on('submit', '#yith-wcwtl-output form', function (ev) {
            ev.preventDefault();
            submit($(this).attr('action'), $(this));
        });

    }


    $(document).on('input', '#yith-wcwtl-email', function () {

        var t = $(this),
            val = t.val(),
            name = t.attr('name');

        addUrlParameter(name, val, t);

    });


    $(document).on('change', '#yith-wcwtl-policy-check', function () {

        var t = $(this),
            name = t.attr('name'),
            val = t.is(':checked') ? 'yes' : 'no';

        addUrlParameter(name, val, t);

    });

});