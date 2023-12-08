(function ($) {
    const megaMenu = {
        body: $('body'),
        element: document.getElementById('menu-to-edit'),
        settingModal: $('#bdt-ep-megamenu-modal'),
        init() {
            this.initBodyEvent();
            this.initPlugins();
            this.megaMenuSettingEvent();
            this.onSaveSettings();
            this.openElementorEditor();
            this.closeElementorEditor();
        },
        megaMenuModal(selector = '') {
            return $('#bdt-ep-megamenu-modal ' + selector);
        },
        elementorEditor(selector = '') {
            return $('#ep-megamenu-content ' + selector);
        },
        elementorEditorConfirmation(selector = '') {
            return $('#ep-megamenu-editor-confirmation ' + selector);
        },
        initPlugins() {
            //color picker
            megaMenu.megaMenuModal('.ep-menu-colorpicker').wpColorPicker();
            AestheticIconPicker({
                selector: '#icon-picker-wrap', onClick: '#select-icon',
            });
        },
        megaMenuSettingEvent() {
            // Add class on active Item
            megaMenu.element.addEventListener('cardupdate', function (megamenu) {
                if (megamenu.detail.isEnabled) {
                    $('#menu-item-' + megamenu.detail.menuID).addClass('ep-megamenu-active');
                } else {
                    $('#menu-item-' + megamenu.detail.menuID).removeClass('ep-megamenu-active');
                }
            });

            $.each(megaMenuBuilder.items, function (megamenu, menuItem) {
                $(menuItem).length && $(menuItem).addClass('ep-megamenu-active');
            });

            megaMenu.megaMenuModal('#bdt-item-enable')
                .on("change", function () {
                    if ($(this).is(':checked')) {
                        megaMenu.megaMenuModal('#ep-content-trigger').prop('disabled', !1);
                        megaMenu.megaMenuModal('#ep-megamenu-toggler').addClass('ep_megamenu_enabled');
                    } else {
                        megaMenu.megaMenuModal('#ep-megamenu-toggler').removeClass('ep_megamenu_enabled');
                        megaMenu.megaMenuModal('#ep-content-trigger').prop('disabled', !0);
                    }
                });

            megaMenu.megaMenuModal('#ep-content-width-type')
                .on('change', function () {
                    if (this.value === 'custom_width') {
                        megaMenu.megaMenuModal('#ep-megamenu-custom-content').show();
                    } else {
                        megaMenu.megaMenuModal('#ep-megamenu-custom-content').hide();
                    }
                });
        },

        onSaveSettings() {
            // save menu item
            megaMenu.megaMenuModal('.ep-item-save')
                .on('click', function () {

                    var spinner = $(this).parent().find('.spinner'),
                        dataSettings = {
                            action: 'ep_save_menu_item_settings', settings: {
                                menu_id: megaMenu.megaMenuModal('#ep-modal-menu-id').val(),
                                menu_has_child: megaMenu.megaMenuModal('#ep-has-child').val(),
                                menu_enable: megaMenu.megaMenuModal('#bdt-item-enable:checked').val(),
                                menu_trigger_effect: megaMenu.megaMenuModal('#mega-menu-trigger-effect').val(),
                                menu_mobile_content_type: megaMenu.megaMenuModal('#mobile_submenu_content_type').val(),
                                menu_width_type: megaMenu.megaMenuModal('#ep-content-width-type').val(),
                                custom_menu_position: megaMenu.megaMenuModal('#ep-megamenu-custom-position-value').val(),
                                menu_badge_label: megaMenu.megaMenuModal('#ep-badge-text-field').val(),
                                menu_custom_width: megaMenu.megaMenuModal('#ep-custom-width-value').val(),
                                megamenu_badge_color: megaMenu.megaMenuModal('#ep-badge-text-color').val(),
                                menu_badge_bgcolor: megaMenu.megaMenuModal('#ep-badge-text-bgcolor').val(),
                                megamenu_icon: megaMenu.megaMenuModal('#icon_value').val(),
                                megamenu_icon_library: megaMenu.megaMenuModal('#icon_library').val(),
                                megamenu_icon_color: megaMenu.megaMenuModal('#ep-icon-color').val(),
                            },
                            nocache: Math.floor(Date.now() / 1e3),
                        };

                    spinner.addClass('loading');

                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: dataSettings,
                        success: function (response) {
                            megaMenu.megaMenuModal('.ep-save-notice').fadeIn('slow');
                            megaMenu.megaMenuModal('.ep-save-notice').html(response['message']);

                            setTimeout(function () {
                                megaMenu.megaMenuModal('.ep-save-notice').fadeOut('slow')
                            }, 1500);

                            spinner.removeClass('loading');
                        },
                    });
                    dataSettings = {
                        menuID: $('#ep-modal-menu-id').val(),
                        isEnabled: $('#bdt-item-enable:checked').val(),
                    };

                    megaMenu.element.dispatchEvent(new CustomEvent('cardupdate', {detail: dataSettings}));
                });
        },

        openElementorEditor() {
            // OPEN ELEMENTOR EDITOR ON IFRAME
            megaMenu.megaMenuModal('#ep-content-trigger')
                .on("click", function () {
                    var iframeURL, menu_id = megaMenu.megaMenuModal('#ep-modal-menu-id').val();
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'ep_get_content_editor',
                            key: menu_id
                        },
                        success: function (response) {
                            iframeURL = response;
                            megaMenu.elementorEditor('#ep-megamenu-iframe').empty();
                            megaMenu.elementorEditor('#ep-megamenu-iframe').attr('src', iframeURL);
                            bdtUIkit.modal(megaMenu.elementorEditor()).show();
                        },
                    });
                });
        },

        closeElementorEditor() {
            megaMenu.elementorEditor('.close-mega-menu-modal')
                .on('click', function (e) {
                    e.preventDefault();
                    const megamenuIframe = document.getElementById('ep-megamenu-iframe');
                    const elementorContent = megamenuIframe.contentWindow || megamenuIframe.contentDocument;

                    if (!elementorContent.jQuery('#elementor-panel-saver-button-publish').hasClass('elementor-disabled')) {
                        bdtUIkit.modal(megaMenu.elementorEditorConfirmation(), {stack: true}).show();
                    } else {
                        setTimeout(function () {
                            elementorContent.jQuery(elementorContent).off('beforeunload');
                            bdtUIkit.modal(megaMenu.elementorEditorConfirmation(), {stack: true}).hide();
                            bdtUIkit.modal(megaMenu.elementorEditor()).hide();
                        }, 400)
                    }
                });


            megaMenu.elementorEditorConfirmation('.confirmation-ok')
                .on('click', function (e) {
                    e.preventDefault();
                    const megamenuIframe = document.getElementById('ep-megamenu-iframe');
                    const elementorContent = megamenuIframe.contentWindow || megamenuIframe.contentDocument;
                    elementorContent.jQuery('#elementor-panel-saver-button-publish').trigger('click');

                    setTimeout(function () {
                        elementorContent.jQuery(elementorContent).off('beforeunload');
                        bdtUIkit.modal(megaMenu.elementorEditorConfirmation(), {stack: true}).hide();
                        bdtUIkit.modal(megaMenu.elementorEditor()).hide();
                    }, 1000)
                });

            megaMenu.elementorEditorConfirmation('.confirmation-cancel').on('click', function (e) {
                e.preventDefault();
                const megamenuIframe = document.getElementById('ep-megamenu-iframe');
                const elementorContent = megamenuIframe.contentWindow || megamenuIframe.contentDocument;

                setTimeout(function () {
                    elementorContent.jQuery(elementorContent).off('beforeunload');
                    bdtUIkit.modal(megaMenu.elementorEditorConfirmation(), {stack: true}).hide();
                    bdtUIkit.modal(megaMenu.elementorEditor()).hide();
                }, 400);
            });
        },

        initBodyEvent() {
            // Enable or Disable Mega Menu
            megaMenu.body.on('DOMSubtreeModified', '#nav-menu-header', function () {
                setTimeout(function () {
                    if ($('#ep-is-metabox-enabled').is(':checked')) {
                        megaMenu.body.removeClass('ep_megamenu_disabled').addClass('ep_megamenu_enabled')
                    } else {
                        megaMenu.body.removeClass('ep_megamenu_enabled').addClass('ep_megamenu_disabled');
                    }
                }, 200);
            });

            // insert modal btn
            megaMenu.body.on('DOMSubtreeModified', '#menu-to-edit', function () {
                $('#menu-to-edit li.menu-item').each(function () {
                    var megamenu = $(this);
                    megamenu.find('.ep-menu-trigger-btn').length < 1 && $('.item-title', megamenu).append(
                        '<a id="bdthemes-element-pack-trigger-btn" class="ep-menu-trigger-btn" href="#bdt-ep-megamenu-modal">' +
                        '<span class="ep-megeamenu">Edit</span>' +
                        '<span class="ep-megeamenu-edit">Edit</span>' +
                        '</a>');
                });
            });

            $('#nav-menu-header').on('change.ekit', '#ep-is-metabox-enabled', function () {
                if ($(this).is(':checked')) {
                    megaMenu.body.addClass('ep_megamenu_enabled').removeClass('ep_megamenu_disabled')
                } else {
                    megaMenu.body.removeClass('ep_megamenu_enabled').addClass('ep_megamenu_disabled');
                }
            });

            $(window.ep_mega_menu_trigger_button).insertAfter('#nav-menu-header #menu-name')
                .parent().find('#ep-megamenu-switcher')
                .trigger('change.ekit');

            // trigger meganeu btn
            $('#menu-to-edit').trigger('DOMSubtreeModified');

            $('#menu-to-edit').on('click', '.ep-menu-trigger-btn', function (m) {
                m.preventDefault();

                var count;
                (m = $(this).parents("li.menu-item")), (menu_id = parseInt(m.attr("id").match(/[0-9]+/)[0], 10)), (m = (m.find(".menu-item-title").text(), m.attr("class").match(/\menu-item-depth-(\d+)\b/)[1], $(".ep_menu_control_nav > li").removeClass("attr-active"), $(".attr-tab-pane").removeClass("attr-active"), $(this).parents(".menu-item").hasClass("menu-item-depth-0") ? ((count = 0), megaMenu.settingModal.removeClass("ep-menu-has-child"), $("#attr_content_nav").addClass("attr-active"), $("#attr_content_tab").addClass("attr-active")) : ((count = 1), megaMenu.settingModal.addClass("ep-menu-has-child"), $("#attr_icon_nav").addClass("attr-active"), $("#attr_icon_tab").addClass("attr-active")), $("#ep-modal-menu-id").val(menu_id), $("#ep-has-child").val(count), {
                    action: "ep_get_menu_item_settings",
                    menu_id: menu_id,
                    menu_width_type: megaMenu.megaMenuModal("#ep-content-width-type").val(),
                    custom_menu_position: megaMenu.megaMenuModal("#ep-megamenu-custom-position-value").val(),
                    menu_badge_label: megaMenu.megaMenuModal("#ep-badge-text-field").val(),
                    menu_custom_width: megaMenu.megaMenuModal("#ep-custom-width-value").val(),
                    megamenu_badge_color: megaMenu.megaMenuModal("#ep-badge-text-color").val(),
                    menu_badge_bgcolor: megaMenu.megaMenuModal("#ep-badge-text-bgcolor").val(),
                    megamenu_icon: megaMenu.megaMenuModal("#icon_value").val(),
                    megamenu_icon_library: megaMenu.megaMenuModal("#icon_library").val(),
                    megamenu_icon_color: megaMenu.megaMenuModal("#ep-icon-color").val(),
                    nocache: Math.floor(Date.now() / 1e3),
                }));

                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: m,
                    dataType: "json",
                    success: function (response) {
                        megaMenu.megaMenuModal("#ep-badge-text-field").val(response.menu_badge_label);
                        megaMenu.megaMenuModal("#ep-custom-width-value").val(response.menu_custom_width);
                        megaMenu.megaMenuModal("#ep-badge-text-color").val(response.megamenu_badge_color);
                        megaMenu.megaMenuModal("#ep-badge-text-bgcolor").val(response.menu_badge_bgcolor);
                        megaMenu.megaMenuModal("#icon_value").val(response.megamenu_icon);
                        megaMenu.megaMenuModal("#icon_library").val(response.megamenu_icon_library);
                        megaMenu.megaMenuModal("#ep-icon-color").val(response.megamenu_icon_color);
                        megaMenu.megaMenuModal("#mega-menu-trigger-effect").val(response.menu_trigger_effect);
                        megaMenu.megaMenuModal("#mobile_submenu_content_type").val(response.menu_mobile_content_type);

                        if (typeof response.menu_width_type === "undefined") {
                            megaMenu.megaMenuModal("#ep-content-width-type").val("default_width").change();
                        } else {
                            megaMenu.megaMenuModal("#ep-content-width-type").val(response.menu_width_type).change();
                        }

                        if (typeof response.custom_menu_position === "undefined") {
                            megaMenu.megaMenuModal("#ep-megamenu-custom-position-value").val("bottom-left").change();
                        } else {
                            megaMenu.megaMenuModal("#ep-megamenu-custom-position-value").val(response.custom_menu_position).change();
                        }
                        megaMenu.megaMenuModal("#bdt-item-enable").prop("checked", !1), (void 0 !== typeof response.menu_enable && 1 == response.menu_enable) ? megaMenu.megaMenuModal("#bdt-item-enable").prop("checked", !0) : megaMenu.megaMenuModal("#bdt-item-enable").prop("checked", !1), megaMenu.megaMenuModal("#bdt-item-enable").trigger("change");
                    },
                });
                bdtUIkit.modal(megaMenu.settingModal).show();
                return false;
            });
        }
    }

    megaMenu.init();

})(jQuery, bdtUIkit);


