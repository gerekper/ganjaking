/* global redux_change, wp */

(function($) {
    "use strict";
    $.redux = $.redux || {};
    $(document).ready(function() {
        $.redux.wbc_importer();
    });
    $.redux.wbc_importer = function() {

        var jqOnError = function(xhr, textStatus, errorThrown ) {
            console.log('was 500 (Internal Server Error) but we try to load import again');
            if (typeof this.tryCount !== "number") {
              this.tryCount = 1;
            }
            if (this.tryCount < 10) {  /* hardcoded number */
                this.tryCount++;
                //try again
                jQuery.post(this).fail(jqOnError)
                return;
            }else{
                alert('There was an error importing demo content. Please reload page and try again.');
            }

        };

        function ajaxFunc (parent,data,i,reimport){
            jQuery.post(ajaxurl,
                {
                    action:"redux_partial_importer",
                    demo_import_id:data.demo_import_id,
                    nonce:data.nonce,
                    type:data.type,
                    wbc_import:data.wbc_import,
                    content:i
                }
                , function(response){
                    /*parent.parents('fieldset.wbc_importer.redux-field').append(response);*/
                    console.log(response)
                    if (response.length > 0 && response.match(/gt3_mark/gi)) {
                        parent.parents('fieldset.wbc_importer.redux-field').find('.importer_status #progressbar .progressbar_condition').css('width',i*10+'%');
                        parent.parents('fieldset.wbc_importer.redux-field').find('.importer_status #progressbar #progressbar_val').css('left',i*10+'%');
                        parent.parents('fieldset.wbc_importer.redux-field').find('.importer_status #progressbar_val').html(i*10+"%");
                        i++
                        if (i > 10) {
                            console.log(i > 10)
                            parent.find('.wbc_image').css('opacity', '1');
                            parent.find('.spinner').css('display', 'none');
                            if (reimport == false) {
                                parent.addClass('rendered').find('.wbc-importer-buttons .importer-button').removeClass('import-demo-data');

                                var reImportButton = '<div id="wbc-importer-reimport" class="wbc-importer-buttons button-primary import-demo-data importer-button">Re-Import</div>';
                                parent.find('.theme-actions .wbc-importer-buttons').append(reImportButton);
                            }
                            parent.find('.importer-button:not(#wbc-importer-reimport)').removeClass('button-primary').addClass('button').text('Imported').show();
                            parent.find('.importer-button').attr('style', '');
                            parent.addClass('imported active').removeClass('not-imported');
                            parent.parents('fieldset.wbc_importer.redux-field').find('.redux-success').show('slow');
                            window.onbeforeunload = null;
                            location.assign(location.href);
                        }else{
                            ajaxFunc (parent,data,i);
                        }
                    } else {
                        parent.find('.import-demo-data').show();

                        if (reimport == true) {
                            parent.find('.importer-button:not(#wbc-importer-reimport)').removeClass('button-primary').addClass('button').text('Imported').show();
                            parent.find('.importer-button').attr('style', '');
                            parent.addClass('imported active').removeClass('not-imported');
                        }

                        alert('There was an error importing demo content: \n\n' + response.replace(/(<([^>]+)>)/gi, ""));
                    }


            }).fail(jqOnError)
        }

        $('.wrap-importer.theme.not-imported, #wbc-importer-reimport').unbind('click').on('click', function(e) {
            e.preventDefault();

            var parent = jQuery(this);
            parent.parents('fieldset.wbc_importer.redux-field').find('.importer_status').css('opacity','1');

            var reimport = false;
            var message = 'Import Demo Content?';
            if (e.target.id == 'wbc-importer-reimport') {
                reimport = true;
                message = 'Re-Import Content?';
                if (!jQuery(this).hasClass('rendered')) {
                    parent = jQuery(this).parents('.wrap-importer');
                }
            }
            if (parent.hasClass('imported') && reimport == false) return;
            var r = confirm(message);
            if (r == false) return;
            if (reimport == true) {
                parent.removeClass('active imported').addClass('not-imported');
            }
            parent.find('.spinner').css('display', 'inline-block');

            parent.removeClass('active imported');

            parent.find('.importer-button').hide();

            var data = jQuery(this).data();

            data.action = "redux_wbc_importer";
            data.demo_import_id = parent.attr("data-demo-id");
            data.nonce = parent.attr("data-nonce");
            data.type = 'import-demo-content';
            data.wbc_import = (reimport == true) ? 're-importing' : ' ';
            parent.find('.wbc_image').css('opacity', '0.5');
            var i = 1;
            ajaxFunc (parent,data,i,reimport)

            return false;
        });
    };
})(jQuery);
