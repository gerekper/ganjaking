(function ($, api) {

    api.bind('ready', function () {
        var editor_open_flag = false;

        $(document).on('click', '.mo-tinymce-expanded-editor-btn', function (e) {
            e.preventDefault();
            var content_editor,
                editor,
                control_id,
                editor_button = $(this),
                editor_button_icon = editor_button.find('span.dashicons');

            editor_open_flag = !editor_open_flag;

            if (editor_open_flag === true) {

                control_id = editor_button.data('control-id');
                content_editor = '<div class="mo-tinymce-expanded-editor"><textarea style="height:200px" id="mo-tinymce-expanded-textarea" data-control-id="' + control_id + '">' + api(control_id).get() + '</textarea></div>';
                $('.wp-full-overlay').prepend(content_editor);

                $('#mo-tinymce-expanded-textarea').mo_wp_editor();
                editor = tinymce.get('mo-tinymce-expanded-textarea');

                editor.on('keyup change undo redo SetContent NodeChange', function () {
                    this.save();
                    $('#mo-tinymce-expanded-textarea').val(this.getContent()).trigger('change');
                });

                $(document).on('change', '#mo-tinymce-expanded-textarea', _.debounce(function () {
                        api($(this).data('control-id')).set($(this).val());
                    }, 300)
                );

                editor_button_icon.removeClass('dashicons-edit').addClass('dashicons-hidden');
                editor_button.find('span:not(.dashicons)').text(moTinyMceExpandedEditor.button_close_text);
            } else {
                editor_button_icon.removeClass('dashicons-hidden').addClass('dashicons-edit');
                editor_button.find('span:not(.dashicons)').text(moTinyMceExpandedEditor.button_open_text);

                $('.mo-tinymce-expanded-editor').remove();
            }
        })
    });
})(jQuery, wp.customize);