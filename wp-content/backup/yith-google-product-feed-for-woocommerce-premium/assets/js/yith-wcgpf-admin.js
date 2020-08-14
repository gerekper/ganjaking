/**
 * yith-wcgpf-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

jQuery(document).ready( function($) {

    var text_tempate = $('#yith-wcgpf-type-template').text();

    var type = $('#yith-wcgpf-feed-type').val();
    var merchant = $('#yith-wcgpf-merchant').val();
    var show_templates = $('input#yith-wcgpf-check-template:checked').length;
    var post = $("#yith-div-make-feed").data('yithpost');
    displaytemplates(show_templates);



    $( document ).on('change','#yith-wcgpf-check-template',function(){
        var type = $('input#yith-wcgpf-check-template:checked').length;
        displaytemplates( type );
    });

    $( document ).on('change','#yith-wcgpf-select-template',function(){
        var type = $(this).val();
        $('#yith-wcgpf-template-create-feed').empty();
        var merchant = $('#yith-wcgpf-merchant').val();
        var post = $("#yith-div-make-feed").data('yithpost');
        var show_templates = $('input#yith-wcgpf-check-template:checked').length;
        showtemplate( type, merchant, post, show_templates );
    });


    function displaytemplates( showtemplates ) {
        
        var type = $('#yith-wcgpf-select-template').val();
        var merchant = $('#yith-wcgpf-merchant').val();
        var post = $("#yith-div-make-feed").data('yithpost');
        
        if (showtemplates) {
            $('.yith-wcgpf-template-feed').show();
            showtemplate(type,merchant,post, showtemplates);
        }else {
            $('.yith-wcgpf-template-feed').hide();
            showtemplate(type,merchant,post, showtemplates);

        }
    }

    function show_select_template(show_template) {
        if (show_template) {
            $('#yith-wcgpf-type-custom').hide();
            var type_template = $('#yith-wcgpf-select-template option:selected').text();
            $('#yith-wcgpf-type-template').text(text_tempate + type_template);
            $('#yith-wcgpf-type-template').show();
        } else {
            $('#yith-wcgpf-type-template').hide();
            $('#yith-wcgpf-type-custom').show();
        }
    }
   function showtemplate( template_id, merchant,post, showtemplates ) {
        show_select_template(showtemplates);
       //$(document).block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
        var post_data = {
            'template_id': template_id,
            'merchant': merchant,
            'data_post' : post,
            'show_templates': showtemplates,
            //security: object.search_post_nonce,
            action: 'yith_wcgpf_load_merchant_options'
        };

        $.ajax({
            type    : "POST",
            data    : post_data,
            url     : yith_wcgpf_adminjs.ajaxurl,
            success : function ( response ) {
                $('#yith-wcgpf-template-create-feed').empty();
                $('#yith-wcgpf-template-create-feed').append(response);
                //$(document).unblock();
            },
            complete: function () {
            }
        });

    }

    $(document).on('click', '#yith-wcgpf-add-new-row', function () {
        var tr = $("#yith-wcgpf-template-table tbody tr:first").clone();
        $(tr).appendTo('#yith-wcgpf-template-table tbody');
        $(tr).find("input:not('.button')").val('');
        //Unchecked checkbox  
        if($('#yith-wcgpf-check-template').prop('checked')) {
            $('#yith-wcgpf-check-template').prop('checked', false);
            $('.yith-wcgpf-template-feed').hide();
            show_select_template(false);
        }


    });

    $(document).on('click', '.yith-wcgpf-delete', function(event) {

        if ($('.yith_wcgpf_template_table  >tbody >tr').length > 1  ) {
            var $target = $(event.target);
            var $tr = $target.closest('tr');
            $($tr).remove();
        }
    });

    $( ".yith_wcgpf_template_table_thead_tbody" ).sortable();


    // Shared function by categories and tags
    var results = function (data) {
        var terms = [];
        if ( data ) {
            $.each( data, function( id, text ) {
                terms.push( { id: id, text: text } );
            });
        }
        return {
            results: terms
        };
    };

    var initSelection = function( element, callback ) {
        var data     = $.parseJSON( element.attr( 'data-selected' ) );
        var selected = [];

        $( element.val().split( ',' ) ).each( function( i, val ) {
            selected.push({
                id: val,
                text: data[ val ]
            });
        });
        return callback( selected );
    };
    var formatSelection = function( data ) {
        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
    };

    // Arguments for categories select2
    $( ':input.yith-wcgpf-category-search' ).filter( ':not(.enhanced)' ).each( function() {
       var ajax = {
            url: yith_wcgpf_adminjs.ajaxurl,
            dataType: 'json',
            quietMillis: 250,
            data: function (term) {
                return {
                    term: term,
                    action: 'yith_wcgpf_category_search',
                    security: yith_wcgpf_adminjs.search_categories_nonce
                };
            },
            cache: true
        };

        if ( yith_wcgpf_adminjs.before_3_0 ) {
            ajax.results = results;
        } else {
            ajax.processResults = results;
        }
        var select2_args = {
            initSelection: yith_wcgpf_adminjs.before_3_0 ? initSelection : null,
            formatSelection: yith_wcgpf_adminjs.before_3_0 ? formatSelection : null,
            multiple: $(this).data('multiple'),
            allowClear: $(this).data('allow_clear') ? true : false,
            placeholder: $(this).data('placeholder'),
            minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : '3',
            escapeMarkup: function (m) {
                return m;
            },
            ajax: ajax
        };
        $( this ).select2( select2_args ).addClass('enhanced').on( 'change', function () {
        
        } );
    });
    // Arguments for tags select2
    $( ':input.yith-wcgpf-tags-search' ).filter( ':not(.enhanced)' ).each( function() {
        var ajax = {
            url: yith_wcgpf_adminjs.ajaxurl,
            dataType: 'json',
            quietMillis: 250,
            data: function (term) {
                return {
                    term: term,
                    action: 'yith_wcgpf_tag_search',
                    security: yith_wcgpf_adminjs.search_tags_nonce
                };
            },
            cache: true
        };

        if ( yith_wcgpf_adminjs.before_3_0 ) {
            ajax.results = results;
        } else {
            ajax.processResults = results;
        }
        var select2_args = {
            initSelection: yith_wcgpf_adminjs.before_3_0 ? initSelection : null,
            formatSelection: yith_wcgpf_adminjs.before_3_0 ? formatSelection : null,
            multiple: $(this).data('multiple'),
            allowClear: $(this).data('allow_clear') ? true : false,
            placeholder: $(this).data('placeholder'),
            minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : '3',
            escapeMarkup: function (m) {
                return m;
            },
            ajax: ajax
        };
        $( this ).select2( select2_args ).addClass('enhanced').on( 'change', function () {

        } );

        $( document.body ).trigger( 'wc-enhanced-select-init' );
    });



});
