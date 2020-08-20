jQuery(document).ready(function($) {

    var media_file_selector = function() {
        $('.mmm_image_selector').not('.loaded').each(function() {

            $(this).addClass('loaded');

            var image_selector = $(this);
            var image_src = image_selector.attr('data-src');
            var input_field = image_selector.attr('data-field');
            var input = $('input#' + input_field);
            var image = $('<img>').attr('src', image_src);

            if (image_src.length > 0) {
                image_selector.addClass('has_image');
            }

            var delete_icon = $('<span class="dashicons dashicons-trash"></span>').on('click', function() {
                image.attr('src', '');
                input.attr('value', '0');
                image_selector.removeClass('has_image')
            });

            var choose_icon = $('<span class="dashicons dashicons-edit"></span>').on('click', function(e) {
                e.preventDefault();

                mm_choose_icon_frame = wp.media.frames.file_frame = wp.media({
                    title: megamenu_pro.file_frame_title,
                    library: {type: 'image'}
                });

                // When an image is selected, run a callback.
                mm_choose_icon_frame.on('select', function() {

                    var selection = mm_choose_icon_frame.state().get('selection');

                    selection.map( function(attachment) {
                        attachment = attachment.toJSON();
                        attachment_id = attachment.id;

                        if(attachment.sizes){
                            if (attachment.sizes.thumbnail) {
                                attachment_url = attachment.sizes.thumbnail.url;
                            } else {
                                attachment_url = attachment.sizes.full.url;
                            }
                        } else {
                            attachment_url = attachment.url;
                        }
                    });

                    image.attr('src', attachment_url);
                    input.attr('value', attachment_id);
                    image_selector.addClass('has_image')
                });

                mm_choose_icon_frame.open();
            });

            image_selector.append(image).append(delete_icon).append(choose_icon);
        });
    }

    media_file_selector();

	/** Media File Selector **/
    $(document).on('toggle_block_content_loaded', function() {
        media_file_selector();
    });

    $(document).on('megamenu_content_loaded', function() {
        media_file_selector();
    });


    $(document).on('megamenu_content_loaded', function() {
        if (typeof wp.codeEditor !== 'undefined') {
            if ($('#codemirror').length) {
                wp.codeEditor.initialize($('#codemirror'), cm_settings);
            }
        }
    });

    // the form is never submitted, so the contents of codemirror never get copied back to the text area.
    // manually copy the contents over when the submit button is clicked.
    $(document).on('click', '.replacements .submit', function() {
        $(document).find('.CodeMirror').each(function(key, value) {
            $('#codemirror').text(value.CodeMirror.getValue());
        });
    });

    // Refresh CodeMirror
    $(document).on('click', '.replacements', function() {
        setTimeout( function() {
            $(document).find('.CodeMirror').each(function(key, value) {
                value.CodeMirror.refresh();
            });
        }, 160);
    });

    $(document).on('change', '#mega_replacement_type', function() {
        setTimeout( function() {
            $(document).find('.CodeMirror').each(function(key, value) {
                value.CodeMirror.refresh();
            });
        }, 160);
    });

    $(document).on("change", "select#mega_replacement_mode", function() {
        var select = $(this);
        var selected = $(this).val();
        select.next().children().hide();
        select.next().children('.' + selected).show();
    });

    $(document).on("change", 'select#mega_replacement_type', function() {
        var select = $(this);
        var selected = $(this).val();
        $(".replacements table tr").not(".type").hide();
        $(".replacements table tr." + selected).show();
    });

    $(document).on('click', '.mm_tab.replacements', function() {
        $(".mm_colorpicker").spectrum({
            preferredFormat: "rgb",
            showInput: true,
            showAlpha: true,
            clickoutFiresChange: true,
            change: function(color) {
                if (color.getAlpha() === 0) {
                    $(this).siblings('div.chosen-color').html('transparent');
                } else {
                    $(this).siblings('div.chosen-color').html(color.toRgbString());
                }
            }
        });

    });

	/** Roles **/
    $(document).on('change', '#mm_roles select', function() {

        var option = $(this);

        if (option.val() == 'by_role') {
            $('#mm_roles input[type=checkbox]').removeAttr('disabled');
        } else {
            $('#mm_roles input[type=checkbox]').attr('disabled', 'disabled');
        }
    });

    /** Style Overrides **/
    $(document).on('change', '.override_toggle_enabled', function() {
        var checkbox = $(this);
        var checked = checkbox.is(":checked");

        var inputs = checkbox.parent().siblings().find('input, select');

        inputs.each(function() {

            var name = $(this).attr('name');

            if (checked) {
                name = name.replace('disabled', 'enabled');
            } else {
                name = name.replace('enabled', 'disabled');
            }

            $(this).attr('name', name);

        });

        var parent = checkbox.parent().parent();

        parent.toggleClass('mega-enabled', 'mega-disabled');
    });

    $(document).on('blur', '#mm_custom_styles .mega-enabled .mega-value input[type=text]', function() {
        $( ".mega-enabled .mega-value input[type=text]", '#mm_custom_styles').each(function( index ) {
            if ($(this).val().length === 0 ) {
                $(this).val('0px');
            }
        });
    });

    $(document).on('click', '.mm_tab.styling', function() {
        $(".mm_colorpicker").spectrum({
            preferredFormat: "rgb",
            showInput: true,
            showAlpha: true,
            clickoutFiresChange: true,
            change: function(color) {
                if (color.getAlpha() === 0) {
                    $(this).siblings('div.chosen-color').html('transparent');
                } else {
                    $(this).siblings('div.chosen-color').html(color.toRgbString());
                }
            }
        });
    });

    $(document).on('change', '.megamenu_orientation_select', function() {
        var accordion_behaviour_row = $(this).parent().parent().parent().children('tr.megamenu_accordion_behaviour');

        if ( $(this).val() == 'accordion' ) {
            accordion_behaviour_row.show();
        } else {
            accordion_behaviour_row.hide();
        }
    });

    $(document).on('change', '.megamenu_sticky_enabled', function() {
        var sticky_behaviour_rows = $(this).parent().parent().parent().children('tr.megamenu_sticky_behaviour');
        var sticky_husu_rows = $(this).parent().parent().parent().children('tr.megamenu_sticky_husu');

        if ( $(this).is(":checked") ) {
            sticky_behaviour_rows.show();

            if ( $(".megamenu_sticky_husu_enabled").is(":checked") ) {
                sticky_husu_rows.show();
            } else {
                sticky_husu_rows.hide();
            }

            $.colorbox.remove();

            $.colorbox({
                html: "",
                initialWidth: '500',
                scrolling: true,
                fixed: true,
                top: '10%',
                initialHeight: '200',
                maxHeight: '530',
                className: "mmm_sticky_warning_lightbox"
            });

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: "mm_get_sticky_notes",
                    _wpnonce: megamenu.nonce
                },
                cache: false,
                beforeSend: function() {
                    $('#cboxLoadedContent').empty();
                    $('#cboxClose').empty();
                    $('#cboxLoadingGraphic').show();
                },
                complete: function() {
                    $('#cboxLoadingGraphic').hide();
                    $('#cboxLoadingOverlay').remove();
                },
                success: function(response) {
                    var json = $.parseJSON(response.data);
                    $('#cboxLoadedContent').css({'width': '100%', 'height': '100%', 'display':'block'});
                    $('#cboxLoadedContent').append(json);
                }
            });

        } else {
            sticky_behaviour_rows.hide();
            sticky_husu_rows.hide();
        }
    });

    $(document).on('change', '.megamenu_sticky_husu_enabled', function() {
        var sticky_husu_rows = $(this).parent().parent().parent().children('tr.megamenu_sticky_husu');

        if ( $(this).is(":checked") ) {
            console.log('checked');
            sticky_husu_rows.show();
        } else {
            console.log('unchecked');
            sticky_husu_rows.hide();
        }
    });

    $('body').on('toggle_block_content_loaded', function() {
        $('select.toggle_block_icon_dropdown').select2({
            containerCssClass: 'tpx-select2-container select2-container-sm select_2_toggle_block_icon_dropdown',
            dropdownCssClass: 'tpx-select2-drop',
            dropdownCssClass: 'toggle_icon_dropdown',
            minimumResultsForSearch: -1,
            formatResult: function(item) {
                if (item.id) {
                    return '<i class="' + $(item.element).attr('data-class') + '"></i>';
                } else {
                    return item.text;
                }
            },
            formatSelection: function (item) {
                return '<i class="' + $(item.element).attr('data-class') + '"></i>';
            }
        });
    });

    $("body.mega-menu_page_maxmegamenu_theme_editor").trigger('toggle_block_content_loaded');
});