/**
 * Created by Vitaly on 08.12.2016.
 */

adswShow = null;
adswRemove = null;

jQuery(function($) {

    window.adsModal = {
        init: function () {
            if(!$('#ads-modal').length){
                $('body').append($('#ads-tmpl-modal').html());
            }

        },
        clear: function () {
            $('#ads-modal .modal-title').html('');
            $('#ads-modal .modal-body').html('');
            $('#ads-modal .modal-footer').html('');
            $('#ads-modal .modal-dialog').removeClass('modal-md').removeClass('modal-lg');
        },
        hide:function () {
            window.adsModal.clear();
            $('#ads-modal').modal('hide');
        },
        show: function (head, body, footer, size) {
            window.adsModal.clear();

            head = head || '';
            body = body || '';
            footer = footer || '';
            size = size || 'lg';

            $('#ads-modal .modal-title').html(head);
            $('#ads-modal .modal-body').html(body);
            $('#ads-modal .modal-footer').html(footer);
            $('#ads-modal .modal-dialog').addClass('modal-' + size);

            $('#ads-modal').modal('show');

            setTimeout(window.ADS.switchery($('#ads-modal')), 300);
        }
    };

    window.adsModal.init();

    function aShow(e){
        var $p = $( e.target ).parent(),
            $attr = $('#adsw-attribute'),
            name = $p.parent().text().replace('×', '');

        var mod = $p.data('target'),
            $td = $p.parents('td');

        var t = $('.adsw-this-end');

        if( t.length )
            t.removeClass('adsw-this-end');

        $attr.html('');

        $td.addClass('adsw-this-end');

        var head = $('#tmpl-edit-attribute-head').html();
        var head_name = {
            name: name
        };

        //$('#adsw-title').text(name);

        var selected = '';
        var values = [];

        $td.find('select option').each(function(){

            values.push( { value: $(this).attr('value') ,title: $(this).text() } );

            if ($(this).text() === name){
                selected = $(this).attr('value');
            }
        });

        var response = {
            values_adsw_attribute: values,
            adsw_attribute: selected,
        };

        var tmpl = $('#tmpl-edit-attribute').html();

        window.adsModal.show( window.ADS.objTotmpl( head, head_name ), window.ADS.objTotmpl( tmpl, response ) , '', 'md' );
    }
    adswShow = aShow;

    function aDelete(e){
        var $p = $( e.target ).parents('li'),
            name = $p.text().replace('×', ''),
            $td = $p.parents('td');

        $td.find('select option').each(function(){
            if( $(this).text() === name ) {
                $(this).prop("selected", false);
            }
        });

        $p.remove();
    }
    adswRemove = aDelete;

    $('body').on('click', '#adsw-apply', function(){

        var old   = $('body').find('#adsw_title').text(), //старое название аттрибутов
            $attr = $('.adsw-this-end'), //текущая select2
            $new  = $('#adsw_attribute'),
            taxonomy = $attr.parents('.woocommerce_attribute').data('taxonomy'); //таксономия

        $attr.find('ul li span').each(function(){

            if( $(this).text() === old )
                $(this).text( $new.find(":selected").text() );
        });

        $attr.find('select.attribute_values option[value="'+$new.val()+'"]').attr('selected', true);

        $.ajaxQueue( {
            url     : ajaxurl,
            data    : {
                action : 'adsw_save_adswattrib',
                data   : {
                    old      : old,
                    taxonomy : taxonomy,
                    term     : $new.val(),
                    post_ID  : $('#post_ID').val()
                }
            },
            type    : "POST",
            success : function ( response ) {

                var data = ADS.tryJSON(response);

                if( typeof data.success === 'undefined' ) {
                    ADS.notify(data.error);
                }
                else{
                    ADS.notify(data.success);
                }
                window.adsModal.hide();
                $( '#variable_product_options' ).trigger( 'reload' );
            }
        } );
    });
});

function adswShowModal () {
    adswShow(event);
}

function adswDelete() {
    adswRemove(event);
}

/*
 * Reset attributes
 */
jQuery(function($) {

    var ResetAttributes = new ( function() {
        var self = this;
        var post_id = $('#post_ID').val();

        this.resetAttributes =  function () {
            $('.adsw_update_attributes').on('click', function(e) {
                e.preventDefault();

                let url = $(this).data('product');

                if( window.ADS.isURL(url) ) {

                    window.ADS.coverShow();

                    setTimeout(function() {
                        var params = {
                            post_id : post_id,
                            url     : url
                        };

                        window.ADS.aliExtension.productAli( url ).then(function (e) {

                            if( e.code && e.code === 404){
                                setTimeout(function () {
                                    window.ADS.aliExtension.productAli( url ).then(function (e) {
                                        self.sendAttributes(e, params);
                                    } );
                                }, 3000);
                            }else{
                                self.sendAttributes(e, params);
                            }

                        });
                    }, 200);



                }
                else{
                    window.ADS.coverHide();
                    window.ADS.notify( $('#noUrlMessage').val() , 'warning' );
                }
            });
        };

        this.sendAttributes = function(e){

            var product = e.product;
            var data = {};

            if(e.code === false){
                ADS.notify( 'Unknown error.' );
                return;
            }

            if( typeof product === 'undefined' || e.code && e.code === 404){
                product = {};
                product.available_product = false;
            } else {
                product.description = '';
                product.available_product = true;
            }

            data.action       = 'adsw_update_product';
            data.ads_action   = 'reset_attributes';
            data.args         = {};
            data.args.product = ADS.b64EncodeUnicode( JSON.stringify( product ) );
            data.args.post_id = post_id;

            $.ajaxQueue({
                url     : ajaxurl,
                data    : data,
                type    : "POST",
                success : function (response) {

                    window.ADS.coverHide();

                    response = ADS.tryJSON(response);
                    if (response !== false) {

                        if(typeof response.error != 'undefined') {
                            window.ADS.notify( response.error , 'warning' );
                        } else {
                            window.ADS.notify( response.success, 'success');
                            setTimeout(function(){ window.location.reload() }, 1000);
                        }
                    }
                }
            });
        };

        this.init = function () {
            self.resetAttributes();
        };

    } )();

    ResetAttributes.init();
});
