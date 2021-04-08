(function (api, $) {
    var _this;

    wp.customize.controlConstructor["mailoptin-email-content"] = wp.customize.Control.extend({

        ready: function () {
            "use strict";

            _this = this;

            wp.customize.section('mailoptin_newsletter_content', function (section) {
                section.expanded.bind(function (isExpanded) {
                    if (isExpanded) {
                        $('.mo-email-content-elements-wrapper').hide();
                        $('.mo-email-content-widget.mo-email-content-element-settings').hide();
                        $('.mo-email-content-wrapper').find('.mo-email-content-widget-wrapper').show();
                        // queue this so it makes going back quick
                        setTimeout(function () {
                            $('#mo-email-content-settings-area').remove();
                        }, 100);
                    } else {
                        $('body').removeClass('mo-email-content-element-settings-open');
                    }
                });
            });

            this.render_saved_elements();
            this.dimension_field_init();
            this.sortable_init();

            $.fn.color_picker_init = function () {
                $(this).find('.mo-color-picker-hex').wpColorPicker({
                    change: function (event, ui) {
                        $(document).trigger('mo_save_changes_on_dirty');
                    }
                });
                return this;
            };

            $(document).on('click', '.element-bar .mo-email-content-widget-title, .element-bar .mo-email-content-widget-action', this.revealSettings);
            $(document).on('click', '.mo-add-new-email-element', this.reveal_add_elements_ui);
            $(document).on('click', '.mo-email-content-go-back a', this.go_back);
            $(document).on('keyup change search', '.mo-email-content-elements-wrapper .search-form input', this.search_elements);

            $(document).on('click', '.mo-email-content-modal-motabs .motabs .motab', this.toggle_settings_tab);

            $(document).on('click', '.mo-select-image-btn a', this.media_upload);

            $(document).on('click', '.mo-email-builder-add-element', this.add_new_element);

            $(document).on('click', '.mo-email-content-footer-link.mo-delete', this.remove_element);

            $(document).on('click', '.mo-email-content-footer-link.mo-duplicate', this.duplicate_element);

            $(document).on('click', '.mo-email-content-footer-wrap .mo-apply', this.save_changes_on_apply);

            wp.customize.previewer.bind('ready', this.reveal_settings_on_contextual_click);

            $(document).on('mo_sort_elements_index', this.sort_elements_index);
        },

        reveal_settings_on_contextual_click: function () {

            var cache = $('#customize-preview iframe').contents();

            cache.find(".mo-email-builder-element").click(function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var self = this;

                wp.customize.section('mailoptin_newsletter_content').focus({
                    completeCallback: function () {
                        var id = typeof $(self).attr('id') === 'undefined' ? $(self).data('id') : $(self).attr('id');
                        $(document).find('.element-bar[data-element-id="' + id + '"] .mo-email-content-widget-title').click();
                    }
                });
            });
        },

        save_changes_on_dirty: function () {
            $('.mo-email-content-element-field, .mo-email-content-element-field .mo-border-input').on('change', _.debounce(function () {
                _this.save_changes();
            }, 500));

            // we are adding a delay so the color input field is updated before changes are saved.
            $(document).on('mo_save_changes_on_dirty', _.debounce(function () {
                _this.save_changes();
            }, 500));

            $('.mo-email-content-field-tinymce-wrap textarea.wp-editor-area').each(function () {

                tinymce.get($(this).attr('id')).on('keyup change undo redo SetContent NodeChange', function () {
                    _this.save_changes();
                });
            });
        },

        save_changes: function () {
            var settings = JSON.parse(_this.setting.get());
            var element_id = $('#mo-email-content-settings-area').data('element-id');
            var data = _.findWhere(settings, {id: element_id});

            $('#mo-email-content-settings-area .mo-email-content-element-field').each(function () {
                var cache = $(this);
                var field_type = cache.data('field-type');
                var name = cache.attr('name');

                if ('dimension' === field_type) {
                    data['settings'][name] = {
                        top: cache.find('.mo-border-input.motop').val(),
                        right: cache.find('.mo-border-input.moright').val(),
                        bottom: cache.find('.mo-border-input.mobottom').val(),
                        left: cache.find('.mo-border-input.moleft').val(),
                    }
                } else if ('checkbox' === field_type) {
                    data['settings'][name] = this.checked;
                } else {
                    data['settings'][name] = cache.val();
                }
            });

            $('#mo-email-content-save-field').val(JSON.stringify(settings)).change();
        },

        save_changes_on_apply: function (e) {
            e.preventDefault();
            _this.save_changes();
            _this.render_saved_elements();
            _this.go_back();
        },

        sort_elements_index: function () {
            var settings = JSON.parse(_this.setting.get());
            $('#mo-email-content-element-bars-wrap .element-bar').each(function (index) {
                var element_id = $(this).data('element-id');
                var data = _.findWhere(settings, {id: element_id});
                data.sortID = index;
            });

            settings = _.sortBy(settings, 'sortID');

            $('#mo-email-content-save-field').val(JSON.stringify(settings)).change();
        },

        sortable_init: function () {
            $("#mo-email-content-element-bars-wrap").sortable({
                cursor: "move",
                containment: "ul[id*='mailoptin_newsletter_content']",
                axis: 'y',
                scrollSensitivity: 40,
                placeholder: 'mo-email-content-blocks-sortable-placeholder',
                update: function (event, ui) {
                    ui.item.trigger('mo_sort_elements_index');
                }
            });
        },

        generate_unique_id: function () {
            return Math.random().toString(36).substring(2) + (new Date()).getTime().toString(36);
        },

        duplicate_element: function (e) {
            e.preventDefault();
            var element_id = $(this).parents('#mo-email-content-settings-area').data('element-id');
            var settings = JSON.parse(_this.setting.get());

            var data = _.clone(_.findWhere(settings, {id: element_id}));
            data.id = _this.generate_unique_id();
            settings.push(data);

            $('#mo-email-content-save-field').val(JSON.stringify(settings)).change();

            _this.render_saved_elements();

            $(this).trigger('mo_sort_elements_index');

            _this.go_back();
        },

        remove_element: function (e) {
            e.preventDefault();
            var element_id = $(this).parents('#mo-email-content-settings-area').data('element-id');

            var data = _.reject(JSON.parse(_this.setting.get()), function (settings) {
                return settings.id === element_id;
            });

            $('#mo-email-content-save-field').val(JSON.stringify(data)).change();

            _this.render_saved_elements();

            $(this).trigger('mo_sort_elements_index');

            _this.go_back();
        },

        add_new_element: function () {
            var type = $(this).data('element-type');
            var id = _this.generate_unique_id();

            var data = JSON.parse(_this.setting.get());
            data.push({
                'id': id,
                'type': type,
                'settings': mo_email_content_builder_elements_defaults[type]
            });

            $('#mo-email-content-save-field').val(JSON.stringify(data)).change();

            _this.render_saved_elements();

            $(this).trigger('mo_sort_elements_index');

            _this.go_back();
        },

        render_saved_elements: function () {
            $('#mo-email-content-element-bars-wrap *').remove();
            _.each(JSON.parse(_this.setting.get()), function (element, index) {
                var template = wp.template('mo-email-content-element-bar');
                $('#mo-email-content-element-bars-wrap').append(template(element));
            });
        },

        revealSettings: function (e) {
            e.preventDefault();
            $(this).parents('.mo-email-content-widget-wrapper').hide();
            $('body').addClass('mo-email-content-element-settings-open');

            var element_type = $(this).data('element-type');
            var element_id = $(this).data('element-id');

            if (typeof element_type === 'undefined' || typeof element_id === 'undefined') {
                element_type = $(this).parents('.element-bar').data('element-type');
                element_id = $(this).parents('.element-bar').data('element-id');
            }

            if ($('#mo-email-content-settings-area').length > 0) {
                $('#mo-email-content-settings-area').remove();
            }

            var template = wp.template('mo-email-content-element-' + element_type);
            var template_data = _.findWhere(JSON.parse(_this.setting.get()), {id: element_id});

            if (typeof template_data !== 'undefined') {
                template_data = template_data.settings;
                template_data['element_id'] = element_id;
            }

            $('.mo-email-content-widget.mo-email-content-element-settings').append(template(template_data)).show().color_picker_init();
            _this.tinymce_field_init();
            _this.range_field_init();
            _this.select2_field_init();
            _this.save_changes_on_dirty();
            $('.mo-email-content-modal-motabs .motabs .motab').eq(0).click();
        },

        toggle_settings_tab: function () {
            $('.mo-email-content-modal-motabs .motabs .motab').removeClass('is-active');
            $(this).addClass('is-active');
            $('.mo-email-content-blocks').hide();
            $('.mo-email-content-widget-form .' + $(this).data('tab-id')).show();
        },

        search_elements: function (e) {
            var term = this.value;
            var cache = $('.mo-email-content-elements-wrapper li.element--box');
            if (term === '') {
                cache.show();
            } else {
                cache.hide().each(function () {
                    var content = $(this).text().replace(/\s/g, '');

                    if (new RegExp('^(?=.*' + term + ').+', 'i').test(content) === true) {
                        $(this).show();
                    }
                });
            }
        },

        go_back: function (e) {
            if (typeof e !== 'undefined') {
                e.preventDefault();
            }
            $('.mo-email-content-elements-wrapper').hide();
            $('.mo-email-content-widget.mo-email-content-element-settings').hide();
            $('body').removeClass('mo-email-content-element-settings-open');

            $('.mo-email-content-widget-wrapper').show();
            // queue this so it makes going back quick
            setTimeout(function () {
                $('#mo-email-content-settings-area').remove();
            }, 100);
        },

        reveal_add_elements_ui: function (e) {
            e.preventDefault();
            $(this).parents('.mo-email-content-widget-wrapper').hide();
            $(this).parents('.mo-email-content-wrapper').find('.mo-email-content-elements-wrapper').show("slide", {direction: "right"}, 300);
        },

        select2_field_init: function () {
            $('#mo-email-content-settings-area .mo-multiple-select').each(function () {
                var selectDropdown = $(this);

                var options = selectDropdown.data('select2-options');

                if (typeof options !== 'undefined' && _.isObject(options) && this.id === 'post_list') {
                    options.ajax.data = function (params) {
                        return {
                            action: 'mailoptin_ecb_fetch_post_type_posts',
                            search: params.term,
                            post_type: $('#posts_post_type').val(),
                            nonce: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                        };
                    }
                }

                selectDropdown.select2(options);

                var settings = JSON.parse(_this.setting.get());
                var element_id = $('#mo-email-content-settings-area').data('element-id');
                var data = _.findWhere(settings, {id: element_id});

                // return here skips to next iteration
                if (this.id !== 'post_list' || typeof data.settings.post_list === 'undefined' || data.settings.post_list.length === 0) return;

                // disable selection
                selectDropdown.prop("disabled", true);

                var selected_posts = data.settings.post_list;

                // see https://select2.org/programmatic-control/add-select-clear-items#preselecting-options-in-an-remotely-sourced-ajax-select2
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'mailoptin_ecb_fetch_post_type_posts',
                        nonce: $("input[data-customize-setting-link*='[ajax_nonce]']").val(),
                        default_selections: selected_posts
                    },
                }).then(function (response) {
                    if (_.isArray(response) && response.length > 0) {
                        _.each(response, function (element) {
                            var option = new Option(element.text, element.id, true, true);
                            selectDropdown.append(option).trigger('change');
                        });

                        selectDropdown.trigger({
                            type: 'select2:select',
                            params: {
                                data: response
                            }
                        });

                        // enable back again.
                        selectDropdown.prop("disabled", false);
                    }
                });
            });
        },

        media_upload: function (e) {

            e.preventDefault();

            let frame, _this = $(e.target);

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media.frames.file_frame = wp.media({
                frame: 'select',
                multiple: false,
                library: {
                    type: 'image' // limits the frame to show only images
                },
            });

            frame.on('select', function () {
                let attachment = frame.state().get('selection').first().toJSON();
                _this.parents('.mo-email-content-blocks').find('.mo-select-image-field input').val(attachment.url).change();

            });

            frame.open();
        },

        range_field_init: function () {
            var range,
                range_input,
                value,
                this_input,
                input_default,
                mo_range_input_number_timeout;

            // Update the text value
            $('input[type=range]').on('mousedown mousemove', function () {
                range = $(this);
                range_input = range.parent().children('.mo-range-input');
                value = range.val();

                range_input.val(value).change();
            });

            // Auto correct the number input
            function mo_autocorrect_range_input_number(input_number, timeout) {

                var range_input = input_number,
                    range = range_input.parent().find('input[type="range"]'),
                    value = parseFloat(range_input.val()),
                    reset = parseFloat(range.attr('data-reset_value')),
                    step = parseFloat(range_input.attr('step')),
                    min = parseFloat(range_input.attr('min')),
                    max = parseFloat(range_input.attr('max'));

                clearTimeout(mo_range_input_number_timeout);

                mo_range_input_number_timeout = setTimeout(function () {

                    if (isNaN(value)) {
                        range_input.val(reset);
                        range.val(reset).trigger('change');
                        return;
                    }

                    if (step >= 1 && value % 1 !== 0) {
                        value = Math.round(value);
                        range_input.val(value);
                        range.val(value);
                    }

                    if (value > max) {
                        range_input.val(max);
                        range.val(max).trigger('change');
                    }

                    if (value < min) {
                        range_input.val(min);
                        range.val(min).trigger('change');
                    }

                }, timeout);

                range.val(value).trigger('change');

            }

            // Change the text value
            $('input.mo-range-input').on('change keyup', function () {
                mo_autocorrect_range_input_number($(this), 1000);

            }).on('focusout', function () {
                mo_autocorrect_range_input_number($(this), 0);
            });

            // Handle the reset button
            $('.mo-reset-slider').on('click', function () {

                this_input = $(this).parent('.control-wrap').find('input');
                input_default = this_input.data('reset_value');

                this_input.val(input_default);
                this_input.change();

            });
        },

        dimension_field_init: function () {
            // Connected button
            $(document).on('click', '.mo-border-connected', function () {

                // Remove connected class
                $(this).parent().parent('.mo-border-wrapper').find('input').removeClass('connected').attr('data-element-connect', '');

                // Remove class
                $(this).parent('.mo-border-input-item-link').removeClass('disconnected');

            });

            // Disconnected button
            $(document).on('click', '.mo-border-disconnected', function () {

                // Add connected class
                $(this).parent().parent('.mo-border-wrapper').find('input').addClass('connected');

                // Add class
                $(this).parent('.mo-border-input-item-link').addClass('disconnected');

            });

            // Values connected inputs
            $(document).on('input', '.mo-border-input-item .connected', function () {

                var currentFieldValue = $(this).val();

                $(this).parent().parent('.mo-border-wrapper').find('.connected').each(function (key, value) {
                    $(this).val(currentFieldValue).change();
                });

            });
        },

        tinymce_field_init: function () {
            var options = {mode: 'tmce'};
            options.mceInit = {
                "theme": "modern",
                "skin": "lightgray",
                "language": "en",
                "formats": {
                    "alignleft": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "left"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["alignleft"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "aligncenter": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "center"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["aligncenter"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "alignright": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "right"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["alignright"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "strikethrough": {"inline": "del", "deep": true, "split": true}
                },
                "relative_urls": false,
                "remove_script_host": false,
                "convert_urls": false,
                "browser_spellcheck": true,
                "fix_list_elements": true,
                "entities": "38,amp,60,lt,62,gt",
                "entity_encoding": "raw",
                "keep_styles": false,
                "paste_webkit_styles": "font-weight font-style color",
                "preview_styles": "font-family font-size font-weight font-style text-decoration text-transform",
                "wpeditimage_disable_captions": false,
                "wpeditimage_html5_captions": false,
                "plugins": "charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview,image",
                "content_css": moWPEditor_globals.includes_url + "css/dashicons.css?ver=3.9," + moWPEditor_globals.includes_url + "js/mediaelement/mediaelementplayer.min.css?ver=3.9," + moWPEditor_globals.includes_url + "js/mediaelement/wp-mediaelement.css?ver=3.9," + moWPEditor_globals.includes_url + "js/tinymce/skins/wordpress/wp-content.css?ver=3.9",
                "selector": "#moWPEditor",
                "resize": "vertical",
                "menubar": false,
                "wpautop": true,
                "indent": false,
                // "fontsize_formats": "9px 10px 12px 14px 16px 18px 24px 30px 36px 48px 60px 72px",
                "toolbar1": "formatselect,bold,italic,strikethrough,bullist,numlist,hr,alignjustify,alignleft,aligncenter,alignright,link,unlink,underline,forecolor,wp_adv",
                "toolbar2": "removeformat,charmap,undo,redo", // fontsizeselect
                "toolbar3": "",
                "toolbar4": "",
                "tabfocus_elements": ":prev,:next",
                "body_class": "moWPEditor",
                'branding': false
            };
            var cache = $('.mo-email-content-field-tinymce');
            if (cache.length > 0) {
                cache.each(function () {
                    var id = $(this).attr('id');
                    $('#' + id).mo_wp_editor(options);

                    tinymce.get(id).on('keyup change undo redo SetContent', function () {
                        this.save();
                    });
                });
            }

            return this;
        }
    });

})(wp.customize, jQuery);