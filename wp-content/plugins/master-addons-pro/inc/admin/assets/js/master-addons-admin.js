(function($){
    "use strict";

    jQuery(document).ready(function($) {
    'use strict';


        // Save Button reacting on any changes
        var saveHeaderAction = $( '.master-addons-el-dashboard-header-wrapper .master-addons-el-btn' );
        $('.master-addons-dashboard-tab input').on( 'click', function() {
            saveHeaderAction.addClass( 'master-addons-el-save-now' );
            saveHeaderAction.removeAttr('disabled').css('cursor', 'pointer');
        } );
        //API & White Label Input Fields Change
        $('#jltma-api-forms-settings input, #jltma-white-label-settings input').on( 'keyup', function() {
            saveHeaderAction.addClass( 'master-addons-el-save-now' );
            saveHeaderAction.removeAttr('disabled').css('cursor', 'pointer');
        } );

        //White Label Checkbox Fields Change
        // $('#textbox1').val(this.checked);
        $('#jltma-white-label-settings input[type="checkbox"]').on( 'change', function() {
            saveHeaderAction.addClass( 'master-addons-el-save-now' );
            saveHeaderAction.removeAttr('disabled').css('cursor', 'pointer');
        } );

        // Enable All Elements
        $('#master-addons-elements .addons-enable-all, a.jltma-wl-plugin-logo, a.jltma-remove-button').on("click",function (e) {
            e.preventDefault();

            $("#master-addons-elements .master_addons_feature_switchbox input:enabled").each(function (i) {
                $(this).prop("checked", true).change();
            });
            saveHeaderAction
                .addClass("master-addons-el-save-now")
                .removeAttr("disabled")
                .css("cursor", "pointer");
        });

        // Disable All Elements
        $('#master-addons-elements .addons-disable-all').on("click",function (e) {
            e.preventDefault();

            $("#master-addons-elements .master_addons_feature_switchbox input:enabled").each(function (i) {
                $(this).prop("checked", false).change();
            });

            saveHeaderAction
                .addClass("master-addons-el-save-now")
                .removeAttr("disabled")
                .css("cursor", "pointer");
        });

        // Enable All Extensions
        $('#master-addons-extensions .addons-enable-all').on("click",function (e) {
            e.preventDefault();

            $("#master-addons-extensions .master_addons_feature_switchbox input:enabled").each(function (i) {
                $(this).prop("checked", true).change();
            });
            saveHeaderAction
                .addClass("master-addons-el-save-now")
                .removeAttr("disabled")
                .css("cursor", "pointer");
        });

        // Disable All Elements
        $('#master-addons-extensions .addons-disable-all').on("click",function (e) {
            e.preventDefault();

            $("#master-addons-extensions .master_addons_feature_switchbox input:enabled").each(function (i) {
                $(this).prop("checked", false).change();
            });

            saveHeaderAction
                .addClass("master-addons-el-save-now")
                .removeAttr("disabled")
                .css("cursor", "pointer");
        });

        // Dashboard widget links target
        $('.master-addons-posts a.rsswidget').attr('target', '_blank');

        //Navigation Tabs
        $('.master_addons_navbar a').on('click',function(event){
            event.preventDefault(); // Limit effect to the container element.

            var context = $(this).closest('.master_addons_navbar').parent();
            var url = $(this).attr('href'),
                target = $(this).attr('target');

            if(target == '_blank') {
                window.open(url, target);
            } else {
                $('.master_addons_navbar li', context).removeClass('wp-tab-active');
                $(this).closest('li').addClass('wp-tab-active');
                $('.master_addons_contents .wp-tab-panel', context).hide();
                $( $(this).attr('href'), context ).show();
            }
        });

        // Make setting wp-tab-active optional.
        $('.master_addons_navbar').each(function(){
            if ( $('.wp-tab-active', this).length )
                $('.wp-tab-active', this).click();
            else
                $('a', this).first().click();
        });



        // Go Pro Modal
        $('.ma-el-pro:parent').on('click',function(event){
                event.preventDefault();
                swal({
                    title: "Go Pro",
                    text: 'Upgrade to <a href="https://master-addons.com/go/upgrade-pro/" target="_blank"> Pro Version </a> for ' +
                    ' Unlock more Features ',
                    type: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonClass: 'btn-success',
                    confirmButtonText: 'Okay'
                }, function () {
                    setTimeout(function () {
                        $('.ma-el-pro').fadeOut('slow');
                    }, 2000);
                })
                .catch(swal.noop);
        });

        // White Label Logo/Icon Upload on button click
        $('body').on( 'click', '.jltma-wl-plugin-logo', function(e){
            e.preventDefault();
            var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                    type : 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false
            }).on('select', function() { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.html('<img src="' + attachment.url + '">').next().show();
                $('.jltma-whl-selected-image').val(attachment.id);
            }).open();

        });

        // on remove button click
        $('body').on('click', '.jltma-remove-button', function(e){
            e.preventDefault();
            var button = $(this);
            button.next().val(''); // emptying the hidden field
            button.hide().prev().html('<i class="dashicons dashicons-cloud-upload"></i> <span>Upload image</span>');
        });

        //Tracking purchases with Google Analytics and Facebook for Freemius Checkout
        var purchaseCompleted = function( response ) {
            var trial = response.purchase.trial_ends !== null,
                total = trial ? 0 : response.purchase.initial_amount.toString(),
                productName = 'Product Name',
                storeUrl = 'https://master-addons.com',
                storeName = 'Master Addons';

            if ( typeof fbq !== "undefined" ) {
                fbq( 'track', 'Purchase', { currency: 'USD', value: response.purchase.initial_amount } );
            }

            if ( typeof ga !== "undefined" ) {
                ga( 'send', 'event', 'plugin', 'purchase', productName, response.purchase.initial_amount.toString()         );

                ga( 'require', 'ecommerce' );

                ga( 'ecommerce:addTransaction', {
                    'id': response.purchase.id.toString(), // Transaction ID. Required.
                    'affiliation': storeName, // Affiliation or store name.
                    'revenue': total, // Grand Total.
                    'shipping': '0', // Shipping.
                    'tax': '0' // Tax.
                } );

                ga( 'ecommerce:addItem', {
                    'id': response.purchase.id.toString(), // Transaction ID. Required.
                    'name': productName, // Product name. Required.
                    'sku': response.purchase.plan_id.toString(), // SKU/code.
                    'category': 'Plugin', // Category or variation.
                    'price': response.purchase.initial_amount.toString(), // Unit price.
                    'quantity': '1' // Quantity.
                } );

                ga( 'ecommerce:send' );

                ga( 'send', {
                    hitType: 'pageview',
                    page: '/purchase-completed/',
                    location: storeUrl + '/purchase-completed/'
                } );
            }
        };


        // Saving Data With Ajax Request
        $( '.master-addons-el-js-element-save-setting' ).on( 'click', function(e) {
            e.preventDefault();

            let $this = $(this);

            if( $(this).hasClass('master-addons-el-save-now') ) {

                // Master Addons Elemements
                $.ajax( {
                    url: js_maad_el_settings.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'master_addons_save_elements_settings',
                        security: js_maad_el_settings.ajax_nonce,
                        fields: $( '#master-addons-el-settings' ).serialize(),
                    },
                    success: function( response ) {

                        swal({
                            title: "Saved",
                            text: "Your Changes has been Saved",
                            type: "success",
                            showLoaderOnConfirm: true,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonClass: 'btn-success',
                            confirmButtonText: 'Okay'

                        }, function () {
                            setTimeout(function () {
                                $('.master-addons-el-settings-saved').fadeOut('fast');
                            }, 2000);
                        });

                        $this.html('Save Settings');
                        $('.master-addons-el-dashboard-header-right').prepend('<span' +
                            ' class="master-addons-el-settings-saved"></span>').fadeIn('slow');

                        saveHeaderAction.removeClass( 'master-addons-el-save-now' );
                    },
                    error: function() {}
                } );

                // Master Addons Extensions
                $.ajax( {
                    url: js_maad_el_settings.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'master_addons_save_extensions_settings',
                        security: js_maad_el_settings.ajax_extensions_nonce,
                        fields: $( '#master-addons-el-extensions-settings' ).serialize(),
                    },
                    success: function( response ) {

                        swal({
                            title: "Saved",
                            text: "Your Changes has been Saved",
                            type: "success",
                            showLoaderOnConfirm: true,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonClass: 'btn-success',
                            confirmButtonText: 'Okay'
                        });

                        $this.html('Save Settings');
                        $('.master-addons-el-dashboard-header-right').prepend('<span' +
                            ' class="master-addons-el-settings-saved"></span>').fadeIn('slow');

                        saveHeaderAction.removeClass( 'master-addons-el-save-now' );

                        setTimeout(function(){
                            $('.master-addons-el-settings-saved').fadeOut('slow');
                            swal.close();
                        }, 1200);

                    },
                    error: function() {}
                } );


                // Master Addons API Extensions
                $.ajax( {
                    url: js_maad_el_settings.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'jltma_save_api_settings',
                        security: js_maad_el_settings.ajax_api_nonce,
                        fields: $( '#jltma-api-forms-settings' ).serializeArray(),
                    },
                    success: function( response ) {
                        swal({
                            title: "Saved",
                            text: "Your Changes has been Saved",
                            type: "success",
                            showLoaderOnConfirm: true,
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonClass: 'btn-success',
                            confirmButtonText: 'Okay'
                        });

                        $this.html('Save Settings');
                        $('.master-addons-el-dashboard-header-right').prepend('<span' +
                            ' class="master-addons-el-settings-saved"></span>').fadeIn('slow');

                        saveHeaderAction.removeClass( 'master-addons-el-save-now' );

                        setTimeout(function(){
                            $('.master-addons-el-settings-saved').fadeOut('slow');
                            swal.close();
                        }, 1200);
                    },
                    error: function() {}
                } );


                // Master Addons White Label Ajax Call
                if ( 'valid' === $(this).data("lic") ) {
                    $.ajax( {
                        url: js_maad_el_settings.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'jltma_save_white_label_settings',
                            security: js_maad_el_settings.ajax_api_nonce,
                            fields: $( 'form#jltma-white-label-settings' ).serialize(),
                        },
                        success: function( response ) {
                            swal({
                                title: "Saved",
                                text: "Your Changes has been Saved",
                                type: "success",
                                showLoaderOnConfirm: true,
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonClass: 'btn-success',
                                confirmButtonText: 'Okay'
                            });

                            $this.html('Save Settings');
                            $('.master-addons-el-dashboard-header-right').prepend('<span' +
                                ' class="master-addons-el-settings-saved"></span>').fadeIn('slow');

                            saveHeaderAction.removeClass( 'master-addons-el-save-now' );

                            setTimeout(function(){
                                $('.master-addons-el-settings-saved').fadeOut('slow');
                                swal.close();
                            }, 1200);
                        },
                        error: function() {
                            swal(
                            'Oops...',
                            'Something Wrong!',
                            );
                        }
                    } );
                }


            } else {
                $(this).attr('disabled', 'true').css('cursor', 'not-allowed');
            }


        } );




        $( '.jltma-rollback-button' ).on( 'click', function( event ) {
            event.preventDefault();

            var $this = $( this ),
                dialogsManager = new DialogsManager.Instance();

            dialogsManager.createWidget( 'confirm', {
                headerMessage: js_maad_el_settings.rollback.rollback_to_previous_version,
                message: js_maad_el_settings.rollback.rollback_confirm,
                strings: {
                    cancel: js_maad_el_settings.rollback.cancel,
                    confirm: js_maad_el_settings.rollback.yes,
                },
                onConfirm: function() {
                    $this.addClass( 'loading' );

                    location.href = $this.attr( 'href' );
                }
            } ).show();
        } );

        // Copy to Clipboard Section
        (function(n) {
            n.fn.copiq = function(e) {
                var t = n.extend({
                    parent: "body",
                    content: "",
                    onSuccess: function() {},
                    onError: function() {}
                }, e);
                return this.each(function() {
                    var e = n(this);
                    e.on("click", function() {
                        var n = e.parents(t.parent).find(t.content);
                        var o = document.createRange();
                        var c = window.getSelection();
                        o.selectNodeContents(n[0]);
                        c.removeAllRanges();
                        c.addRange(o);
                        try {
                            var r = document.execCommand("copy");
                            var a = r ? "onSuccess" : "onError";
                            t[a](e, n, c.toString())
                        } catch (i) {}
                        c.removeAllRanges()
                    })
                })
            }
        })(jQuery);

        $('.jltma-copy-btn').copiq({
            parent: '.copy-section',
            content: '.api-element-inner',
            onSuccess: function($element, source, selection) {
                $('span', $element).text($element.attr("data-text-copied"));
                setTimeout(function() {
                    $('span', $element).text($element.attr("data-text"));
                }, 2000);
            }
        });


});

})(jQuery);
