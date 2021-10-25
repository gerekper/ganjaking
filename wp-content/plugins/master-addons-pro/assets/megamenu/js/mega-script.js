jQuery(document).ready(function(e) {
    "use strict";

    e(".jltma-menu-wpcolor-picker").wpColorPicker();

    var megamenu_enable = 'megamenu_enable',
        enable_megamenu = jQuery("#jltma-menu-metabox-input-is-enabled").is(":checked") ? 1 : 0,
        t=e(".icon-picker").iconPicker(),

        JLTMA_Mega_Menu ={

            Enable_Mega_Menu: function() {

                e(this).is(":checked")?e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled"):e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");

                var i = e(this).parent().find(".spinner");
                    i.addClass("loading");

                var enable_megamenu = jQuery("#jltma-menu-metabox-input-is-enabled").is(":checked") ? 1 : 0;

                e('.jltma-notice').css("display","block");

                e.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: {
                        action: 'jltma_save_megamenu_options',
                        is_enabled: enable_megamenu
                    },
                }).done( function( response ) {
                    i.removeClass("loading");
                    response = jQuery.parseJSON( response );


                    if( response['status'] == "success"){

                        if(enable_megamenu){
                            e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled")
                        }else{
                            e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled")
                        }
                        localStorage.setItem( 'megamenu_enable', enable_megamenu);

                        e( '.jltma-notice' ).html( response['message'] );
                        setTimeout(function(){
                            e('.jltma-notice').fadeOut('slow')
                        }, 2000);
                    }

                }).fail(function(error){
                  console.log(error);
                });

                return false;

            },

            Menu_Item_Settings_Save: function() {
                var t=e("#jltma-menu-metabox-input-is-enabled:checked").length,
                    n=e("#jltma-menu-metabox-input-menu-id").val(),
                    i=e(this).parent().find(".spinner"),
                    m= {
                        is_enabled: t,
                        menu_id:n
                    };
                    i.addClass("loading"),
                    e.get(window.masteraddons.resturl+"megamenu/save_megamenu_settings", m).done(function(e) {
                        i.removeClass("loading");
                    });
            },

            Menu_Item_Save: function() {
                var t=e(this).parent().find(".spinner"),
                    n= {
                        settings:  {
                            menu_id: e("#jltma-menu-modal-menu-id").val(),
                            menu_has_child:e("#jltma-menu-modal-menu-has-child").val(),
                            menu_enable:e("#jltma-menu-item-enable:checked").val(),
                            menu_label_enable:e("#mega-menu-hide-item-label:checked").val(),
                            // menu_transition:e("#mega-menu-transition-effect").val(),
                            menu_disable_description:e("#jltma-menu-disable-description:checked").val(),
                            menu_icon:e("#jltma-menu-icon-field").val(),
                            menu_trigger_effect:e("#mega-menu-trigger-effect").val(),
                            menu_icon_color:e("#jltma-menu-icon-color-field").val(),
                            menu_badge_text:e("#jltma-menu-badge-text-field").val(),
                            menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
                            menu_badge_color:e("#jltma-menu-badge-color-field").val(),
                            // menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
                            menu_badge_background:e("#jltma-menu-badge-background-field").val(),

                        }
                    };


                t.addClass("loading"), e.get(window.masteraddons.resturl+"megamenu/jltma_save_menuitem_settings", n).done(function(n) {
                    t.removeClass("loading"), e("#jltma-menu-item-settings-modal").modal("hide");
                });
            },

            Menu_Trigger: function() {
                var t=e("#jltma-menu-modal-menu-id").val(),
                    n=window.masteraddons.resturl+"mastermega-content/jltma_content_editor/megamenu/menuitem"+t;

                    e("#jltma-menu-builder-iframe").attr("src", n);
            },
    };

    // Need to Work on this section
    e(".jltma-menu-settings-save").on("click", ()=>JLTMA_Mega_Menu.Menu_Item_Settings_Save(e));

    // e(".jltma-menu-item-save").on("click", ()=>JLTMA_Mega_Menu.Menu_Item_Save(e));

    e(".jltma-menu-item-save").on("click", function() {
        var t=e(this).parent().find(".spinner"),
            n= {
                settings:  {
                    menu_id: e("#jltma-menu-modal-menu-id").val(),
                    menu_has_child:e("#jltma-menu-modal-menu-has-child").val(),
                    menu_disable_description:e("#jltma-menu-disable-description:checked").val(),
                    menu_enable:e("#jltma-menu-item-enable:checked").val(),
                    menu_icon:e("#jltma-menu-icon-field").val(),
                    menu_trigger_effect:e("#mega-menu-trigger-effect").val(),
                    menu_icon_color:e("#jltma-menu-icon-color-field").val(),
                    menu_label_enable:e("#mega-menu-hide-item-label:checked").val(),
                    // menu_transition:e("#mega-menu-transition-effect").val(),
                    menu_badge_text:e("#jltma-menu-badge-text-field").val(),
                    menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
                    menu_badge_color:e("#jltma-menu-badge-color-field").val(),
                    // menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
                    menu_badge_background:e("#jltma-menu-badge-background-field").val(),

                }
            };

        t.addClass("loading"), e.get(window.masteraddons.resturl+"megamenu/jltma_save_menuitem_settings", n).done(function(n) {
            t.removeClass("loading"), e("#jltma-menu-item-settings-modal").modal("hide");
        });
    }),

    e("#jltma-menu-builder-trigger").on("click", ()=>JLTMA_Mega_Menu.Menu_Trigger(e));

    e("body").on("DOMSubtreeModified", "#menu-to-edit", function() {
        setTimeout(function() {
            e("#menu-to-edit li.menu-item").each(function() {
            var t=e(this);
                t.find(".jltma_menu_trigger").length<1&&e(".item-title", t).append("<a data-toggle='modal' data-target='#jltma_megamenu_modal' href='#' class='jltma_menu_trigger'>Master Mega</a> ");
            });
        }, 200);
    }),

    e("#menu-to-edit").trigger("DOMSubtreeModified"), e("#menu-to-edit").on("click", ".jltma_menu_trigger", function(n) {
        n.preventDefault();

        var i=e("#jltma_megamenu_modal"),
            m=e(this).parents("li.menu-item"),
            l=parseInt(m.attr("id").match(/[0-9]+/)[0], 10);

        m.find(".menu-item-title").text(), m.attr("class").match(/\menu-item-depth-(\d+)\b/)[1];

        if(
            e(".jltma_menu_control_nav > li").removeClass("active"),
            e(".tab-pane").removeClass("active"),
            1==e(this).parent().find(".is-submenu").is(":hidden")) {
                var a=0;
                i.removeClass("jltma-menu-has-child"),
                e("#content_nav").addClass("active"),
                e("#content_tab").addClass("active");
        }else {
            a=1;
            i.addClass("jltma-menu-has-child"), e("#general_nav").addClass("active"), e("#general_tab").addClass("active show");
        }

        e("#jltma-menu-modal-menu-id").val(l), e("#jltma-menu-modal-menu-has-child").val(a);

        var o= { menu_id: l };

        e.get(window.masteraddons.resturl+"megamenu/get_menuitem_settings", o).done(function(n) {
           e("#jltma-menu-item-enable").prop("checked", !1),
           e("#mega-menu-trigger-effect").val(n.menu_trigger_effect),
        //    e("#jltma-mobile-submenu-type").val(n.menu_mobile_submenu_content_type),
           e("#jltma-menu-icon-color-field").wpColorPicker("color", n.menu_icon_color),
           e("#jltma-menu-icon-field").val(n.menu_icon),
        //    e("#mega-menu-transition-effect").val(n.menu_transition),
           e("#mega-menu-hide-item-label").prop("checked", !1),
           void 0!==typeof n.menu_label_enable&&1==n.menu_label_enable?e("#mega-menu-hide-item-label").prop("checked", !0): e("#mega-menu-hide-item-label").prop("checked", !1),
           e("#jltma-menu-badge-text-field").val(n.menu_badge_text),
           e("#jltma-mobile-submenu-type").val(n.menu_mobile_submenu_content_type),
           e("#jltma-menu-disable-description").prop("checked", !1),
           void 0!==typeof n.menu_disable_description&&1==n.menu_disable_description?e("#jltma-menu-disable-description").prop("checked", !0): e("#jltma-menu-disable-description").prop("checked", !1),
           e("#jltma-menu-badge-color-field").wpColorPicker("color", n.menu_badge_color),
           e("#jltma-menu-badge-background-field").wpColorPicker("color", n.menu_badge_background),
           void 0!==typeof n.menu_enable&&1==n.menu_enable?e("#jltma-menu-item-enable").prop("checked", !0): e("#jltma-menu-item-enable").prop("checked", !1),
        //    void 0!==typeof n.menu_mobile_submenu_content_type&&1==n.menu_mobile_submenu_content_type?e("#menu_mobile_submenu_content_type").prop("checked", !0): e("#menu_mobile_submenu_content_type").prop("checked", !1),
        //    e("#menu_mobile_submenu_content_type input").prop("checked", !1), void 0===typeof n.menu_mobile_submenu_content_type||"builder_content"==n.menu_mobile_submenu_content_type?e("#menu_mobile_submenu_content_type input[value=builder_content]").prop("checked", !0):e("#menu_mobile_submenu_content_type input[value=submenu_list]").prop("checked", !0),
           e("#jltma-menu-item-enable").trigger("change"),
           setTimeout(function() {
                i.removeClass("jltma-menu-modal-loading");
            }, 500);
        });

    });

    e("#jltma-menu-item-enable").on("change", function() {
        e(this).is(":checked")?(e("#jltma-menu-builder-trigger").prop("disabled", !1), e("#jltma-menu-builder-wrapper").addClass("is_enabled")):(e("#jltma-menu-item-enable").prop("checked", !1), e("#jltma-menu-builder-wrapper").removeClass("is_enabled"), e("#jltma-menu-builder-trigger").prop("disabled", !0));
    });


    e("#jltma-mega-menu-settings").on('change', "#jltma-menu-metabox-input-is-enabled", ()=>JLTMA_Mega_Menu.Enable_Mega_Menu(e));


    e.ajax({
        url: ajaxurl,
        type: 'get',
        data: {
            action: 'jltma_get_megamenu_options'
        },
    }).done( function( response ) {
        response = jQuery.parseJSON( response );
        if( enable_megamenu && localStorage.getItem( 'megamenu_enable' ) == "1"){
            e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled")
        }else{
            e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled")
        }
    });


});