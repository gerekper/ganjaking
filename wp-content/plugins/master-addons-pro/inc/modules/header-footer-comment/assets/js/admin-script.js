/*
* Master Header & Footer
*/
; (function ($) {
    "use strict";

    var Master_Header_Footer = {
        
        Url_Param_Replace: function(url, paramName, paramValue) {

            if (paramValue == null) {
                paramValue = '';
            }
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
            if (url.search(pattern)>=0) {
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            url = url.replace(/[?#]$/,'');
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;

        },

        JLTMA_Template_Editor: function(data) {
            try { 
                (function($) {

                    // set the form data
                    $('.jltma_hf_modal-title').val(data.title);
                    $('.jltma_hf_modal-jltma_hf_conditions').val(data.jltma_hf_conditions);
                    $('.jltma_hf_modal-jltma_hfc_singular').val(data.jltma_hfc_singular);
                    $('.jltma_hf_modal-jltma_hfc_singular_id').val(data.jltma_hfc_singular_id);
                    $('.jltma_hfc_type').val(data.type);

                    var activation_input = $('.jltma-enable-switcher');
                    if (data.activation == 'yes') {
                        activation_input.attr('checked', true);
                    } else {
                        activation_input.removeAttr('checked');
                    }

                    $('.jltma-enable-switcher, .jltma_hfc_type, .jltma_hf_modal-jltma_hf_conditions, .jltma_hf_modal-jltma_hfc_singular')
                        .trigger('change');

                    var el = $('.jltma_hf_modal-jltma_hfc_singular_id');
                    
                    $.ajax({
                        url: window.masteraddons.resturl + 'select2/singular_list',
                        dataType: 'json',
                        data: {
                            ids: String(data.jltma_hfc_singular_id)
                        }
                    }).then(function (data) {

                        if (data !== null && data.results.length > 0) {
                            el.html(' ');
                            $.each(data.results, function (i, v) {
                                var option = new Option(v.text, v.id, true, true);
                                el.append(option).trigger('change');
                            });
                            el.trigger({
                                type: 'select2:select',
                                params: {
                                    data: data
                                }
                            });
                        }
                    });

                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },


        Modal_Singular_List: function() {
            $('.jltma_hf_modal-jltma_hfc_singular_id').select2({
                ajax: {
                    url: window.masteraddons.resturl + 'select2/singular_list',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            s: params.term,
                        }
                        return query;
                    }
                },
                cache: true,
                placeholder: "--",
                dropdownParent: $('#jltma_hf_modal_body')
            });
        },

        Modal_Submit: function() {
            try { 
                (function($) {

                    $('#jltma_hf_modal_form').on('submit', function (e) {
                        e.preventDefault();

                        var modal = $('#jltma_hf_modal');
                        modal.addClass('loading');
                    
                        var form_data = $(this).serialize(),
                            id = $(this).attr('data-jltma-hf-id'),
                            open_editor = $(this).attr('data-open-editor'),
                            admin_url = $(this).attr('data-editor-url'),
                            nonce = $(this).attr('data-nonce');

                        $.ajax({
                            url: window.masteraddons.resturl + 'ma-template/update/' + id,
                            data: form_data,
                            type: 'get',
                            headers: {
                                'X-WP-Nonce': nonce
                            },
                            dataType: 'json',
                            success: function (output) {
                                
                                setTimeout(function() {
                                    modal.removeClass('loading');
                                }, 1500 );
                    
                                var row = $('#post-' + output.data.id);
                                
                                if(row.length > 0){
                                    row.find('.column-type')
                                        .html(output.data.type_html);
                    
                                    row.find('.column-condition')
                                        .html(output.data.cond_text);
                    
                                    row.find('.row-title')
                                        .html(output.data.title)
                                        .attr('aria-label', output.data.title);
                                }
                    
                                if (open_editor == '1') {
                                    window.location.href = admin_url + '?post=' + output.data.id + '&action=elementor';
                                }else if(id == '0'){
                                    location.reload();
                                }
                            }
                        });
                    
                    });
                    
                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

        Open_Editor: function() {
            try { 
                (function($) {

                    $('.jltma-btn-editor').on('click', function () {
                        var form = $('#jltma_hf_modal_form');
                        form.attr('data-open-editor', '1');
                        form.trigger('submit');
                    });

                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

        Choose_Template_Singular_Condition: function() {
            try { 
                (function($) {

                    $('.jltma_hf_modal-jltma_hfc_singular').on('change', function () {
                        var jltma_hfc_singular = $(this).val();
                        var inputs = $('.jltma_hf_modal-jltma_hfc_singular_id-container');

                        if (jltma_hfc_singular == 'selective') {
                            inputs.show();
                        } else {
                            inputs.hide();
                        }
                    });

                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

        Choose_Template_Conditions: function() {
            try { 
                (function($) {

                    $('.jltma_hf_modal-jltma_hf_conditions').unbind().on('change', function () {
                        var jltma_hf_conditions = $(this).val(),
                            inputs = $('.jltma_hf_modal-jltma_hfc_singular-container');

                        if (jltma_hf_conditions == 'singular') {
                            inputs.show();
                        } else if( jltma_hf_conditions=='jltma-hfc-single-pro' || jltma_hf_conditions=='jltma-hfc-archive-pro'){
                            $('.jltma-hfc-popup-upgade').remove();
                            $('.jltma_hf_modal-jltma_hf_conditions').after('<div class="jltma-hfc-popup-upgade"> ' + masteraddons.upgrade_pro + '</div>');
                        } else {
                            inputs.hide();
                            $('.jltma-hfc-popup-upgade').hide();
                        }
                    });

                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

        Choose_Template_Type: function() {
            try { 
                (function($) {

                    $('.jltma_hfc_type').on('change', function () {
                        
                        var type    = $(this).val(),
                            label   = $('.jltma-hfc-hide-item-label'),
                            inputs  = $('.jltma_hf_options_container');

                        if ( type == 'section' || type == 'comment') {
                            inputs.hide();
                            label.hide();
                        } else {
                            label.show();
                            inputs.show();
                        }
                    });

                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

        Modal_Add_Edit: function() {
            try { 
                (function($) {

                    $('.row-actions .edit a, .page-title-action, .column-title .row-title').on('click', function (e) {
                        e.preventDefault();
                        var id = 0, 
                            modal = $('#jltma_hf_modal'),
                            parent = $(this).parents('.column-title');

                        modal.addClass('loading');
                        modal.modal('show');

                        if (parent.length > 0) {
                            id = parent.find('.hidden').attr('id').split('_')[1];

                            $.get(window.masteraddons.resturl + 'ma-template/get/' + id, function (data) {
                                Master_Header_Footer.JLTMA_Template_Editor( data );
                                modal.removeClass('loading');
                            });

                        } else {
                            var data = {
                                title               : '',
                                type                : 'header',
                                jltma_hf_conditions : 'entire_site',
                                jltma_hfc_singular  : 'all',
                                activation          : '',
                            };

                            Master_Header_Footer.JLTMA_Template_Editor( data );
                            modal.removeClass('loading');
                        }

                        modal.find('form').attr('data-jltma-hf-id', id);
                    });
                })(jQuery);
            } catch(e) {  //We can also throw from try block and catch it here
                //e.preventDefault();
            } 
        },

    };



    jQuery(document).ready(function($) {
        "use strict"; 

        Master_Header_Footer.Modal_Add_Edit();
        Master_Header_Footer.Choose_Template_Type();
        Master_Header_Footer.Choose_Template_Conditions();
        Master_Header_Footer.Choose_Template_Singular_Condition();
        Master_Header_Footer.Open_Editor();
        Master_Header_Footer.Modal_Submit();
        Master_Header_Footer.Modal_Singular_List();


        var tab_container = $('.wp-header-end'),
            tabs = '',
            filter_types = {
                'all': 'All',
                'header': 'Header',
                'footer': 'Footer',
                'comment': 'Comment',
                // 'popup': 'Popups',
            },
            url = new URL(window.location.href),
            s = url.searchParams.get("master_template_type_filter");

        s = (s == null) ? 'all' : s;

        $.each( filter_types, function(k, v){
            var url = Master_Header_Footer.Url_Param_Replace(window.location.href, 'master_template_type_filter', k);
            var jlma_class = (s == k) ? 'master_type_filter_active nav-tab-active' : ' ';
            tabs += `
                <a href="${url}" class="${jlma_class} master_type_filter_tab_item nav-tab">${v}</a>
            `;
            tabs += "\n";
        });
        tab_container.after('<div class="master_type_filter_tab_container nav-tab-wrapper">'+ tabs +'</div><br/>');

    }); //document.ready

})(jQuery);