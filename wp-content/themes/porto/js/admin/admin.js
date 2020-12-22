/*
 * jQuery.appear
 * https://github.com/bas2k/jquery.appear/
 * http://code.google.com/p/jquery-appear/
 * http://bas2k.ru/
 *
 * Copyright (c) 2009 Michael Hixson
 * Copyright (c) 2012-2014 Alexander Brovikov
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 */
(function($) {
    'use strict';
    $.fn.appear = function(fn, options) {
        var settings = $.extend({
            //arbitrary data to pass to fn
            data: undefined,
            //call fn only on the first appear?
            one: true,
            // X & Y accuracy
            accX: 0,
            accY: 0
        }, options);

        return this.each(function() {
            var t = $(this);
            //whether the element is currently visible
            t.appeared = false;
            if (!fn) {
                //trigger the custom event
                t.trigger('appear', settings.data);
                return;
            }
            var w = $(window);
            //fires the appear event when appropriate

            var check = function() {
                //is the element hidden?
                if (!t.is(':visible')) {
                    //it became hidden
                    t.appeared = false;
                    return;
                }

                //is the element inside the visible window?
                var a = w.scrollLeft();
                var b = w.scrollTop();
                var o = t.offset();
                var x = o.left;
                var y = o.top;
                var ax = settings.accX;
                var ay = settings.accY;
                var th = t.height();
                var wh = w.height();
                var tw = t.width();
                var ww = w.width();

                if (y + th + ay >= b &&
                    y <= b + wh + ay &&
                    x + tw + ax >= a &&
                    x <= a + ww + ax) {
                    //trigger the custom event
                    if (!t.appeared) t.trigger('appear', settings.data);
                } else {
                    //it scrolled out of view
                    t.appeared = false;
                }
            };

            //create a modified fn with some additional logic
            var modifiedFn = function() {
                //mark the element as visible
                t.appeared = true;

                //is this supposed to happen only once?
                if (settings.one) {
                    //remove the check
                    w.unbind('scroll', check);
                    var i = $.inArray(check, $.fn.appear.checks);
                    if (i >= 0) $.fn.appear.checks.splice(i, 1);
                }

                //trigger the original fn
                fn.apply(this, arguments);
            };

            //bind the modified fn to the element
            if (settings.one) t.one('appear', settings.data, modifiedFn);
            else t.bind('appear', settings.data, modifiedFn);
            //check whenever the window scrolls
            w.scroll(check);
            //check whenever the dom changes
            $.fn.appear.checks.push(check);
            //check now
            (check)();
        });
    };

    //keep a queue of appearance checks
    $.extend($.fn.appear, {
        checks: [],
        timeout: null,
        //process the queue
        checkAll: function() {
            var length = $.fn.appear.checks.length;
            if (length > 0) while (length--) {
                if (typeof $.fn.appear.checks[length] == 'function')
                    ($.fn.appear.checks[length])();
            }
        },
        //check the queue asynchronously
        run: function() {
            if ($.fn.appear.timeout) clearTimeout($.fn.appear.timeout);
            $.fn.appear.timeout = setTimeout($.fn.appear.checkAll, 20);
        }
    });

    //run checks when these methods are called
    /*$.each(['append', 'prepend', 'after', 'before', 'attr',
        'removeAttr', 'addClass', 'removeClass', 'toggleClass',
        'remove', 'css', 'show', 'hide'], function(i, n) {
        var old = $.fn[n];
        if (old) {
            $.fn[n] = function() {
                var r = old.apply(this, arguments);
                $.fn.appear.run();
                return r;
            }
        }
    });*/
})(jQuery);

// Easy Responsive Tabs Plugin
// Author: Samson.Onna <Email : samson3d@gmail.com>
(function ($) {
    'use strict';
    $.fn.extend({
        easyResponsiveTabs: function (options) {
            //Set the default values, use comma to separate the settings, example:
            var defaults = {
                type: 'default', //default, vertical, accordion;
                width: 'auto',
                fit: true,
                closed: false,
                activate: function(){}
            }
            //Variables
            options = $.extend(defaults, options);
            var opt = options, jtype = opt.type, jfit = opt.fit, jwidth = opt.width, vtabs = 'vertical', accord = 'accordion';
            var hash = window.location.hash;
            var historyApi = !!(window.history && history.replaceState);

            //Events
            $(this).bind('tabactivate', function(e, currentTab) {
                if(typeof options.activate === 'function') {
                    options.activate.call(currentTab, e);
                }
            });

            //Main function
            this.each(function () {
                var $respTabs = $(this);
                var $respTabsList = $respTabs.find('ul.resp-tabs-list');
                var respTabsId = $respTabs.attr('id');
                $respTabs.find('ul.resp-tabs-list li').addClass('resp-tab-item');
                $respTabs.css({
                    'display': 'block',
                    'width': jwidth
                });

                $respTabs.find('.resp-tabs-container > div').addClass('resp-tab-content');
                jtab_options();
                //Properties Function
                function jtab_options() {
                    if (jtype == vtabs) {
                        $respTabs.addClass('resp-vtabs');
                    }
                    if (jfit == true) {
                        $respTabs.css({ width: '100%' });
                    }
                    if (jtype == accord) {
                        $respTabs.addClass('resp-easy-accordion');
                        $respTabs.find('.resp-tabs-list').css('display', 'none');
                    }
                }

                //Assigning the h2 markup to accordion title
                var $tabItemh2;
                $respTabs.find('.resp-tab-content').before("<h2 class='resp-accordion' role='tab'><span class='resp-arrow'></span></h2>");

                var itemCount = 0;
                $respTabs.find('.resp-accordion').each(function () {
                    $tabItemh2 = $(this);
                    var $tabItem = $respTabs.find('.resp-tab-item:eq(' + itemCount + ')');
                    var $accItem = $respTabs.find('.resp-accordion:eq(' + itemCount + ')');
                    $accItem.append($tabItem.html());
                    $accItem.data($tabItem.data());
                    $tabItemh2.attr('aria-controls', 'tab_item-' + (itemCount));
                    itemCount++;
                });

                //Assigning the 'aria-controls' to Tab items
                var count = 0,
                    $tabContent;
                $respTabs.find('.resp-tab-item').each(function () {
                    var $tabItem = $(this);
                    $tabItem.attr('aria-controls', 'tab_item-' + (count));
                    $tabItem.attr('role', 'tab');

                    //Assigning the 'aria-labelledby' attr to tab-content
                    var tabcount = 0;
                    $respTabs.find('.resp-tab-content').each(function () {
                        $tabContent = $(this);
                        $tabContent.attr('aria-labelledby', 'tab_item-' + (tabcount));
                        tabcount++;
                    });
                    count++;
                });

                // Show correct content area
                var tabNum = 0;
                if(hash!='') {
                    var matches = hash.match(new RegExp(respTabsId+"([0-9]+)"));
                    if (matches!==null && matches.length===2) {
                        tabNum = parseInt(matches[1],10)-1;
                        if (tabNum > count) {
                            tabNum = 0;
                        }
                    }
                }

                //Active correct tab
                $($respTabs.find('.resp-tab-item')[tabNum]).addClass('resp-tab-active');

                //keep closed if option = 'closed' or option is 'accordion' and the element is in accordion mode
                if(options.closed !== true && !(options.closed === 'accordion' && !$respTabsList.is(':visible')) && !(options.closed === 'tabs' && $respTabsList.is(':visible'))) {
                    $($respTabs.find('.resp-accordion')[tabNum]).addClass('resp-tab-active');
                    $($respTabs.find('.resp-tab-content')[tabNum]).addClass('resp-tab-content-active').attr('style', 'display:block');
                }
                //assign proper classes for when tabs mode is activated before making a selection in accordion mode
                else {
                    $($respTabs.find('.resp-tab-content')[tabNum]).addClass('resp-tab-content-active resp-accordion-closed');
                }

                //Tab Click action function
                $respTabs.find("[role=tab]").each(function () {

                    var $currentTab = $(this);
                    $currentTab.on('click', function () {

                        var $currentTab = $(this);
                        var $tabAria = $currentTab.attr('aria-controls');

                        if ($currentTab.hasClass('resp-accordion') && $currentTab.hasClass('resp-tab-active')) {
                            $respTabs.find('.resp-tab-content-active').slideUp('', function () { $(this).addClass('resp-accordion-closed'); });
                            $currentTab.removeClass('resp-tab-active');
                            return false;
                        }
                        if (!$currentTab.hasClass('resp-tab-active') && $currentTab.hasClass('resp-accordion')) {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').slideUp().removeClass('resp-tab-content-active resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');

                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').slideDown().addClass('resp-tab-content-active');
                        } else {
                            $respTabs.find('.resp-tab-active').removeClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content-active').removeAttr('style').removeClass('resp-tab-content-active').removeClass('resp-accordion-closed');
                            $respTabs.find("[aria-controls=" + $tabAria + "]").addClass('resp-tab-active');
                            $respTabs.find('.resp-tab-content[aria-labelledby = ' + $tabAria + ']').addClass('resp-tab-content-active').attr('style', 'display:block');
                        }
                        //Trigger tab activation event
                        $currentTab.trigger('tabactivate', $currentTab);
                    });

                });

                //Window resize function
                $(window).resize(function () {
                    $respTabs.find('.resp-accordion-closed').removeAttr('style');
                });
            });
        }
    });
})(jQuery);

jQuery(document).ready(function($) {
    'use strict';

    // content type meta tab
    $('.porto-meta-tab').easyResponsiveTabs({
        type: 'vertical'//, //default, vertical, accordion;
    });

    // taxonomy meta tab
    $('.porto-tab-row').hide();
    $('.porto-tax-meta-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).attr('data-tab');
        $('.porto-tab-row[data-tab="' + tab + '"]').toggle();
        return false;
    });

    // color field
    $(document).on('plugin_init', '.porto-meta-color', function() {
        var $el = $(this),
            $c = $el.find('.porto-color-field'),
            $t = $el.find('.porto-color-transparency');

        $c.wpColorPicker({
            change: function( e, ui ) {
                $( this ).val( ui.color.toString() );
                $t.removeAttr( 'checked' );
            },
            clear: function( e, ui ) {
                $t.removeAttr( 'checked' );
            }
        });
        $t.on('click', function() {
            if ( $( this ).is( ":checked" ) ) {
                $c.attr('data-old-color', $c.val());
                $c.val( 'transparent' );
                $el.find( '.wp-color-result' ).css('background-color', 'transparent');
            } else {
                if ( $c.val() === 'transparent' ) {
                    var oc = $c.attr('data-old-color');
                    $el.find( '.wp-color-result' ).css('background-color', oc);
                    $c.val(oc);
                }
            }
        });
    });
    $('.porto-meta-color').each(function() {
        $(this).trigger('plugin_init');
    });

    // meta required filter
    var filters = ['.postoptions .metabox', '.form-table .form-field'];
    $.each(filters, function(index, filter) {
        $(filter + '[data-required]').each(function() {
            var $el = $(this),
                id = $el.data('required'),
                value = $el.data('value'),
                $required = $(filter + ' [name="' + id + '"]'),
                type = $required.attr('type');
            if ($required.prop('type') == 'select-one') {
                $required.on('change', function() {
                    if ($.inArray($required.val(), value.split(',')) !== -1) {
                        $el.show();
                    } else {
                        $el.hide();
                    }
                });
                $required.change();
            } else {
                if (type == 'checkbox') {
                    $required.on('change', function() {
                        if ($(this).is(':checked')) {
                            if (value) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        } else {
                            if (!value) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        }
                    });
                    $required.change();
                } else if (type == 'radio') {
                    $required.on('click', function() {
                        if ($(this).is(':checked')) {
                            if ($.inArray($(this).val(), value.split(',')) !== -1) {
                                $el.show();
                            } else {
                                $el.hide();
                            }
                        }
                    });
                    $(filter + ' [name="' + id + '"]:checked').click();
                }
            }
        });
    });

    // codemirror
    if (typeof CodeMirror != 'undefined') {
        if (document.getElementById("custom_css")) CodeMirror.fromTextArea(document.getElementById("custom_css"), { lineNumbers: true, mode: 'css' });
        if (document.getElementById("custom_js_head")) CodeMirror.fromTextArea(document.getElementById("custom_js_head"), { lineNumbers: true, mode: 'javascript' });
        if (document.getElementById("custom_js_body")) CodeMirror.fromTextArea(document.getElementById("custom_js_body"), { lineNumbers: true, mode: 'javascript' });
    }
});

(function() {
    'use strict';
    // Uploading files
    var file_frame;
    var clickedID;

    jQuery(document).off( 'click', '.button_upload_image').on( 'click', '.button_upload_image', function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( !file_frame ) {
            // Create the media frame.
            file_frame = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Use image'
                },
                multiple: false
            });
        }

        file_frame.open();
        
        clickedID = jQuery(this).data('id');
        
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON(),
                $obj = jQuery('#' + clickedID);

            $obj.val( attachment.url );
            if ($obj.attr('data-name'))
                $obj.attr('name', $obj.attr('data-name'));

            file_frame.close();
        });
    });

    jQuery(document).off( 'click', '.button_attach_image').on( 'click', '.button_attach_image', function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( !file_frame ) {
            // Create the media frame.
            file_frame = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Use image'
                },
                multiple: false
            });
        }

        file_frame.open();

        clickedID = jQuery(this).data('id');

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON(),
                $obj = jQuery('#' + clickedID);

            $obj.val( attachment.id );
            jQuery('#' + clickedID + '_thumb').html('<img src="' + attachment.url + '"/>');
            if ($obj.attr('data-name'))
                $obj.attr('name', $obj.attr('data-name'));

            file_frame.close();
        });
    });

    jQuery(document).on( 'click', '.button_remove_image', function( event ){
        
        var clickedID = jQuery(this).data('id');
        jQuery('#' + clickedID).val( '' );
        jQuery('#' + clickedID + '_thumb').html('');

        return false;
    });
})();

jQuery(function($) {
    'use strict';
    function updatePortoMenuOptions(elem, shift) {
        var current_elem = elem;
        var depth_shift = shift;
        var classNames = current_elem.attr('class').split(' ');

        for (var i = 0; i < classNames.length; i+=1) {
            if (classNames[i].indexOf('menu-item-depth-') >= 0) {
                var depth = classNames[i].split('menu-item-depth-');
                var id = current_elem.attr('id');

                depth = parseInt(depth[1]) + depth_shift;
                id = id.replace('menu-item-', '');

                if (depth == 0) {
                    current_elem.find('.edit-menu-item-level1-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level0-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else if (depth == 1) {
                    current_elem.find('.edit-menu-item-level0-' + id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).show().find('select, input[type="text"], textarea').each(function() {
                        if ($(this).val()) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).find('input[type="checkbox"]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).attr('name', $(this).attr('data-name'));
                        } else {
                            $(this).removeAttr('name');
                        }
                    });
                } else {
                    current_elem.find('.edit-menu-item-level0-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level1-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                    current_elem.find('.edit-menu-item-level01-'+id).hide().find('select, input, textarea').each(function() {
                        $(this).removeAttr('name');
                    });
                }
            }
        }
    }

    $(document).on('change', '.menu-item select, .menu-item textarea, .menu-item input[type="text"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).val();
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $(document).on('change', '.menu-item input[type="checkbox"]', function() {
        var that = $('body #' + $(this).attr('id'));
        var value = $(this).is(':checked');
        var name = $(this).attr('data-name');
        if (value) {
            that.attr('name', name);
        } else {
            that.removeAttr('name');
        }
    });

    $('#update-nav-menu').bind('click', function(e) {
        if ( e.target && e.target.className ) {
            if ( -1 != e.target.className.indexOf('item-delete') ) {
                var clickedEl = e.target;
                var itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);
                var menu_item = $('#menu-item-' + itemID);
                var children = menu_item.childMenuItems();
                children.each(function() {
                    updatePortoMenuOptions($(this), -1);
                });
            }
        }
    });

    $( "#menu-to-edit" ).on( "sortstop", function( event, ui ) {
        var menu_item = ui.item;
        setTimeout(function() {
            updatePortoMenuOptions(menu_item, 0);
            var children = menu_item.childMenuItems();
            children.each(function() {
                updatePortoMenuOptions($(this), 0);
            });
        }, 200);
    } );

    // Remove import success values
    if ($('#redux-form-wrapper').length) {
        var $referer = $('#redux-form-wrapper input[name="_wp_http_referer"]');
        var value = $referer.val();
        value = value.replace('&import_success=true', '');
        value = value.replace('&import_masterslider_success=true', '');
        value = value.replace('&import_widget_success=true', '');
        value = value.replace('&import_options_success=true', '');
        value = value.replace('&compile_theme_success=true', '');
        value = value.replace('&compile_theme_rtl_success=true', '');
        value = value.replace('&compile_plugins_success=true', '');
        value = value.replace('&compile_plugins_rtl_success=true', '');
        $referer.val(value);
    }

    function alertLeavePage(e) {
        var dialogText = "Are you sure you want to leave?";
        e.returnValue = dialogText;
        return dialogText;
    }

    function addAlertLeavePage() {
        $('.porto-import-yes.btn-primary').attr('disabled', 'disabled');
        $('.mfp-bg, .mfp-wrap').unbind('click');
        $(window).bind('beforeunload', alertLeavePage);
    }

    function removeAlertLeavePage() {
        $('.porto-import-yes.btn-primary').removeAttr('disabled');
        $('.mfp-bg, .mfp-wrap').bind('click', function(e) {
            if ($(e.target).is('.mfp-wrap .mfp-content *')) {
                return;
            }
            e.preventDefault();
            $.magnificPopup.close();
        });
        $(window).unbind('beforeunload', alertLeavePage);
    }

    function showImportMessage(selected_demo, message, count, index) {
        var html = '';
        if (selected_demo) {
            html += '<h3 class="porto-demo-install"><i class="porto-ajax-loader"></i> Installing ' + jQuery('#porto-install-demo-type').data('title') + '</h3>';
        }
        if (message) {
            html += '<strong>' + message + '</strong>';
        }
        if (count && index) {
            var percent = index / count * 100;
            if (percent > 100)
                percent = 100;
            html += '<div class="import-progress-bar"><div style="width:' + percent + '%;"></div></div>';
        }
        $('.porto-install-demo #import-status').stop().show().html(html);
    }

    $(window).on('load', function() {
        // filter demos
        if ($('#theme-install-demos').length) {
            var $install_demos = $('#theme-install-demos').isotope(),
                $demos_filter = $('.demo-sort-filters');

            $demos_filter.find('.sort-source li').on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    filter = $this.data('filter-by');
                $install_demos.isotope({
                    filter: (filter == '*' ? filter : ('.' + filter))
                });
                $demos_filter.find('.sort-source li').removeClass('active');
                $this.addClass('active');
                return false;
            });
            $demos_filter.find('.sort-source li[data-active="true"]').click();
        }

        // porto studio
        if ($('.blocks-wrapper .blocks-list .block').length) {
            var porto_blocks_cur_page = 1;
            $('.blocks-wrapper .category-list a').on('click', function(e, cur_page, demo_filter) {
                e.preventDefault();
                if ($('.blocks-wrapper').hasClass('loading')) {
                    return false;
                }
                if (typeof cur_page != 'undefined') {
                    porto_blocks_cur_page = parseInt(cur_page, 10);
                } else {
                    porto_blocks_cur_page = 1;
                }
                var $this = $(this),
                    cat = $this.data('filter-by'),
                    limit = $this.data('limit'),
                    loaddata = { action: 'porto_studio_filter_category', category_id: cat, count_per_page: limit, wpnonce: porto_studio.wpnonce, page: porto_blocks_cur_page };
                if (typeof demo_filter != 'undefined') {
                    loaddata.demo_filter = demo_filter;
                }
                if ($(document.body).hasClass('elementor-editor-active') && $('#elementor-preview').length) {
                    loaddata.type = 'e'; // Elementor
                } else if ($(document.body).hasClass('vcv-wb-editor') && $('#vcv-editor-iframe').length) {
                    loaddata.type = 'c'; // Visual Composer
                } else {
                    loaddata.type = 'v'; // WPBakery
                }
                $('.blocks-wrapper').addClass('loading');
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'html',
                    data: loaddata,
                    success: function(response) {
                        if ('error' == response) {
                            $('.blocks-wrapper').removeClass('loading').removeClass('infiniteloading');
                            return;
                        }
                        if (porto_blocks_cur_page === 1) {
                            $('.blocks-wrapper .blocks-list').isotope('remove', $('.blocks-wrapper .blocks-list').children());
                        }
                        var newItems = $(response).find('.blocks-list').children();
                        $('.blocks-wrapper .blocks-list').append(newItems);
                        $('.blocks-wrapper .blocks-list').isotope('appended', newItems);
                        $('.blocks-wrapper .category-list a').removeClass('active');
                        $this.addClass('active');
                        if (typeof demo_filter != 'undefined') {
                            var total_page = $(response).find('.category-list li:hidden a').data('total-page');
                            if (total_page) {
                                $this.data('total-page', parseInt(total_page, 10));
                            } else {
                                $this.data('total-page', 1);
                            }
                        }
                        $('.blocks-wrapper .blocks-list').waitForImages(function() {
                            $('.blocks-wrapper .blocks-list').isotope('layout');
                            $('.blocks-wrapper').removeClass('loading').removeClass('infiniteloading');
                            $('.mfp-wrap.blocks-cont').trigger('scroll');
                        });
                    }
                });
            });
            $('body:not(.vcv-wb-editor) .blocks-wrapper .blocks-list').on('click', '.import', function(e) {
                e.preventDefault();
                var $this = $(this),
                    block_id = $this.data('id');
                $this.attr('disabled', 'disabled');
                $this.closest('.block').addClass('importing');

                var importdata = { action: 'porto_studio_import', block_id: block_id, wpnonce: porto_studio.wpnonce };
                if ($(document.body).hasClass('elementor-editor-active')) {
                    importdata.type = 'e'; // Elementor
                } else {
                    importdata.type = 'v'; // WPBakery
                }
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: importdata,
                    success: function(response) {
                        //$this.removeAttr('disabled');
                        //$this.closest('.block').removeClass('importing');
                        if (response && response.content) {
                            if (typeof vc != 'undefined' && vc.storage) { // WPBakery backend editor
                                vc.storage.append(response.content);
                                vc.shortcodes.fetch({
                                    reset: !0
                                }), _.delay(function() {
                                    window.vc.undoRedoApi.unlock();
                                }, 50);
                            } else if (window.vc_iframe_src) { // WPBakery frontend editor
                                var render_data = { action: 'vc_frontend_load_template', block_id: block_id, content: response.content, wpnonce: porto_studio.wpnonce, template_unique_id: '1', template_type: 'my_templates', vc_inline: true, _vcnonce: window.vcAdminNonce };
                                if (response.meta) {
                                    render_data.meta = response.meta;
                                }
                                $.ajax({
                                    url: window.vc_iframe_src.replace(/&amp;/g, '&'),
                                    type: 'post',
                                    data: render_data,
                                    success: function(html) {
                                        var template, data;
                                        _.each($(html), function(element) {
                                            if ('vc_template-data' === element.id) {
                                                try {
                                                    data = JSON.parse(element.innerHTML);
                                                } catch(err) {}
                                            }
                                            if ('vc_template-html' === element.id) {
                                                template = element.innerHTML;
                                            }
                                        });
                                        if (template && data) {
                                            vc.builder.buildFromTemplate(template, data);
                                            vc.closeActivePanel();
                                        }
                                    },
                                }).always(function() {
                                    $this.removeAttr('disabled');
                                    $this.closest('.block').removeClass('importing');
                                });
                            } else if (typeof elementor != 'undefined') { // Elementor editor
                                elementor.getPreviewView().addChildModel(response.content, {});
                                // active save button or save elementor
                                if ( elementor.saver && elementor.saver.footerSaver && elementor.saver.footerSaver.activateSaveButtons ) {
                                    elementor.saver.footerSaver.activateSaveButtons(document, 'publish');
                                } else {
                                    $e.run('document/save/publish');
                                }
                            }
                        }
                        if (response && response.meta) {
                            for(var key in response.meta) {
                                var value = response.meta[key].replace('/<script.*?\/script>/s', '');
                                if (typeof vc != 'undefined' && vc.storage && $('[name="' + key + '"]').length) {
                                    switch($('[name="' + key + '"]')[0].tagName.toLowerCase()) {
                                        case 'input':
                                            var input_type = $('[name="' + key + '"]').attr('type').toLowerCase();
                                            if ('text' == input_type || 'hidden' == input_type) {
                                                $('[name="' + key + '"]').val(value);
                                            } else if ('checkbox' == input_type || 'radio' == input_type) {
                                                $('[name="' + key + '"]').removeProp('checked');
                                                $('[name="' + key + '"]').each(function() {
                                                    if ($(this).val() == value) {
                                                        $(this).prop('checked', true);
                                                    }
                                                });
                                            }
                                            break;
                                        case 'select':
                                            $('[name="' + key + '"] option').removeProp('selected');
                                            $('[name="' + key + '"] option[value="' + value + '"]').prop('selected', 'selected');
                                            $('[name="' + key + '"]').val(value);
                                            break;
                                        default:
                                            $('[name="' + key + '"]').each(function() {
                                                $(this).val($(this).val() + value);
                                            });
                                    }
                                } else if (window.vc_iframe_src) {
                                    if (typeof porto_studio['meta_fields'] == 'undefined') {
                                        porto_studio['meta_fields'] = {};
                                    }
                                    if (typeof porto_studio['meta_fields'][key] == 'undefined') {
                                        porto_studio['meta_fields'][key] = '';
                                    }
                                    if (porto_studio['meta_fields'][key].indexOf(value) === -1) {
                                        porto_studio['meta_fields'][key] += value;
                                    }
                                } else if (typeof elementor != 'undefined') {
                                    key = 'porto_' + key;
                                    var key_data = elementor.settings.page.model.get(key);
                                    if (typeof key_data == 'undefined') {
                                        key_data = '';
                                    }
                                    if (!key_data || key_data.indexOf(value) === -1) {
                                        elementor.settings.page.model.set(key, key_data + value);
                                    }
                                    if ('porto_custom_css' == key) {
                                        elementorFrontend.hooks.doAction('refresh_dynamic_css', value, block_id);
                                    }
                                }
                            }
                        }
                        if (response && response.error) {
                            alert(response.error);
                        }
                    },
                    failure: function() {
                        alert('There was an error when importing block. Please try again later!');
                    }
                }).always(function() {
                    //if (vc.storage) {
                        $this.removeAttr('disabled');
                        $this.closest('.block').removeClass('importing');
                    //}
                });
            });
            // porto studio in vc front-end editor
            $('#vc_button-update').on('click', function(e) {
                if (porto_studio['meta_fields'] && vc_post_id) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        dataType: 'json',
                        data: {action: 'porto_studio_save', post_id: vc_post_id, nonce: porto_studio.wpnonce, fields: porto_studio['meta_fields']}
                    });
                }
            });
            // porto studio demo filters
            $('.blocks-wrapper .demo-filter-trigger').on('click', function(e) {
                e.preventDefault();
                $(this).parent().toggleClass('active');
            });
            $('.blocks-wrapper .demo-filter .filter1').on('change', function(e) {
                if ('all' != $(this).val()) {
                    $('.blocks-wrapper .btn').removeAttr('disabled');
                    $('.blocks-wrapper .demo-filter .filter2 option').removeAttr('selected').hide();
                    $('.blocks-wrapper .demo-filter .filter2 option[data-filter*="' + $(this).val() + '"]').show();
                    $('.blocks-wrapper .demo-filter .filter2 option:first-child').attr('selected', 'selected').show();
                } else {
                    $('.blocks-wrapper .btn').attr('disabled', 'disabled');
                    $('.blocks-wrapper .demo-filter .filter2 option').removeAttr('selected').show();
                }
            });
            $('.blocks-wrapper .demo-filter .filter2').on('change', function(e) {
                if ($(this).val()) {
                    $('.blocks-wrapper .btn').removeAttr('disabled');
                } else {
                    $('.blocks-wrapper .btn').attr('disabled', 'disabled');
                }
            });
            $('.blocks-wrapper .demo-filter .btn').on('click', function(e, cur_page) {
                e.preventDefault();
                var $this = $(this),
                    filter = [];
                if (typeof cur_page == 'undefined') {
                    cur_page = 1;
                }
                if ($this.closest('.demo-filter').find('.filter2').val()) {
                    filter[0] = $this.closest('.demo-filter').find('.filter2').val();
                } else {
                    var filter1 = $this.closest('.demo-filter').find('.filter1').val()
                    $this.closest('.demo-filter').find('.filter2 option[data-filter*="' + filter1 + '"]').each(function() {
                        filter.push($(this).val());
                    });
                }
                $('.blocks-wrapper .category-list li:first-child a').trigger('click', [cur_page, filter]);
            });
        }

        $(document.body).on('click', '#porto-studio-editor-button, #porto-elementor-panel-porto-studio, #vce-porto-studio-trigger', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.hasClass('disabled')) {
                return false;
            }
            $this.addClass('disabled');
            $('.blocks-wrapper img[data-original]').each(function() {
                $(this).attr('src', $(this).data('original'));
                $(this).removeAttr('data-original');
            });
            $('.blocks-wrapper').waitForImages(function() {
                $this.removeClass('disabled');
                $.magnificPopup.open({
                    items: {
                        src: '.blocks-wrapper'
                    },
                    type: 'inline',
                    mainClass: 'blocks-cont mfp-fade',
                    removalDelay: 160,
                    preloader: false,
                    //fixedContentPos: false,
                    callbacks: {
                        change: function() {
                            setTimeout(function() {
                                if (!$('.blocks-wrapper .blocks-list').hasClass('initialized')) {
                                    $('.blocks-wrapper .blocks-list').addClass('initialized').isotope({
                                        itemSelector: '.block',
                                        layoutMode: 'masonry'
                                    });

                                    $('.mfp-wrap.blocks-cont').on('scroll', function() {
                                        var $this = $(this),
                                            top = $this.find('.blocks-wrapper').offset().top - $this.offset().top + $this.find('.blocks-wrapper').height() - $this.height();
                                        if (top <= 10 && !$this.find('.blocks-wrapper').hasClass('loading') && parseInt($this.find('.blocks-wrapper .category-list a.active').data('total-page'), 10) >= porto_blocks_cur_page + 1) {
                                            if (parseInt($this.find('.blocks-wrapper .category-list a.active').data('filter-by'), 10)) {
                                                $this.find('.blocks-wrapper .category-list a.active').trigger('click', [porto_blocks_cur_page + 1]);
                                            } else {
                                                $this.find('.blocks-wrapper .demo-filter .btn').trigger('click', [porto_blocks_cur_page + 1]);
                                            }
                                            $this.find('.blocks-wrapper').addClass('infiniteloading');
                                        }
                                    });

                                    $('.mfp-wrap.blocks-cont').trigger('scroll');
                                }
                                $('.blocks-wrapper .blocks-list').isotope('layout');
                            }, 100);
                        }
                    }
                });
            });
        });
    });

    // cancel import button
    $('#porto-import-no').on('click', function() {
        $.magnificPopup.close();
        removeAlertLeavePage();
    });

    // import
    $('.porto-import-yes').on('click', function() {
        addAlertLeavePage();
        var demo = $('#porto-install-demo-type').val(),
            options = {
                demo: demo,
                reset_menus: $('#porto-reset-menus').is(':checked'),
                reset_widgets: $('#porto-reset-widgets').is(':checked'),
                import_dummy: $('#porto-import-dummy').is(':checked'),
                import_shortcodes: $('#porto-import-shortcodes').is(':checked'),
                import_widgets: $('#porto-import-widgets').is(':checked'),
                //import_sliders: $('#porto-import-sliders').is(':checked'),
                import_options: $('#porto-import-options').is(':checked'),
                import_icons: $('#porto-import-icons').is(':checked'),
                override_contents: $('#porto-override-contents').is(':checked')
            };
        if ($(this).hasClass('alternative')) {
            options.dummy_action = 'porto_import_dummy_step_by_step';
        } else {
            options.dummy_action = 'porto_import_dummy';
        }

        if (options.demo) {
            showImportMessage(demo, '');
            var data = {'action': 'porto_download_demo_file', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            $.post(ajaxurl, data, function(response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {}
                if (response && response.process && response.process == 'success') {
                    porto_import_options(options);
                } else if (response && response.process && response.process == 'error') {
                    porto_import_failed(demo, response.message);
                } else {
                    porto_import_failed(demo);
                }
            }).fail(function(response) {
                porto_import_failed(demo);
            });
        }
        $('#porto-install-options').slideUp();
    });

    // import options
    function porto_import_options(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_options) {
            var demo = options.demo,
                data = {'action': 'porto_import_options', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing theme options');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_reset_menus(options);
            }).fail(function(response) {
                porto_reset_menus(options);
            });
        } else {
            porto_reset_menus(options);
        }
    }

    // reset_menus
    function porto_reset_menus(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.reset_menus) {
            var demo = options.demo,
                data = {'action': 'porto_reset_menus', 'import_shortcodes': options.import_shortcodes, 'wpnonce': porto_setup_wizard_params.wpnonce};

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_reset_widgets(options);
            }).fail(function(response) {
                porto_reset_widgets(options);
            });
        } else {
            porto_reset_widgets(options);
        }
    }

    // reset widgets
    function porto_reset_widgets(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.reset_widgets) {
            var demo = options.demo,
                data = {'action': 'porto_reset_widgets', 'wpnonce': porto_setup_wizard_params.wpnonce};

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_dummy(options);
            }).fail(function(response) {
                porto_import_dummy(options);
            });
        } else {
            porto_import_dummy(options);
        }
    }

    // import dummy content
    var dummy_index = 0, dummy_count = 0, dummy_process = 'import_start';
    function porto_import_dummy(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_dummy) {
            var demo = options.demo,
                data = {'action': options.dummy_action, 'process':'import_start', 'demo': demo, 'override_contents': options.override_contents, 'wpnonce': porto_setup_wizard_params.wpnonce};
            dummy_index = 0;
            dummy_count = 0;
            dummy_process = 'import_start';
            porto_import_dummy_process(options, data);
            showImportMessage(demo, 'Importing posts');
        } else {
            porto_import_widgets(options);
        }
    }

    // import dummy content process
    function porto_import_dummy_process(options, args) {
        var demo = options.demo;
        $.post(ajaxurl, args, function(response) {
            if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
                replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                response = $.parseJSON(response);
                if (response.process != 'complete') {
                    var requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    if (response.process) requests.process = response.process;
                    if (response.index) requests.index = response.index;

                    requests.demo = demo;
                    requests.override_contents = options.override_contents;
                    porto_import_dummy_process(options, requests);

                    dummy_index = response.index;
                    dummy_count = response.count;
                    dummy_process = response.process;

                    showImportMessage(demo, response.message, dummy_count, dummy_index);
                } else {
                    showImportMessage(demo, response.message);
                    porto_import_revsliders(options);
                }
            } else {
                porto_import_failed(demo);
            }
        }).fail(function(response) {
            if (args.action == 'porto_import_dummy') {
                porto_import_failed(demo);
            } else {
                var requests;
                if (dummy_index < dummy_count) {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.index = ++dummy_index;
                    requests.demo = demo;

                    porto_import_dummy_process(options, requests);
                } else {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.demo = demo;

                    porto_import_dummy_process(options, requests);
                }
            }
        });
    }

    // import rev sliders
    function porto_import_revsliders(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_dummy) {
            var demo = options.demo,
                data = {'action': 'porto_import_revsliders', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            if (options.import_options) {
                data.import_options_too = 'true';
            }

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_widgets(options);
            }).fail(function(response) {
                porto_import_widgets(options);
            });
        } else {
            porto_import_widgets(options);
        }
    }

    // import widgets
    function porto_import_widgets(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_widgets) {
            var demo = options.demo,
                data = {'action': 'porto_import_widgets', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing widgets');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_icons(options);
            }).fail(function(response) {
                porto_import_icons(options);
            });
        } else {
            porto_import_icons(options);
        }
    }

    // import icons
    function porto_import_icons(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_icons) {
            var demo = options.demo,
                data = {'action': 'porto_import_icons', 'wpnonce': porto_setup_wizard_params.wpnonce};

            showImportMessage(demo, 'Importing icons');

            $.post(ajaxurl, data, function(response) {
                if (response) showImportMessage(demo, response);
                porto_import_shortcodes(options);
            }).fail(function(response) {
                porto_import_shortcodes(options);
            });
        } else {
            porto_import_shortcodes(options);
        }
    }

    // import shortcode pages
    function porto_import_shortcodes(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        if (options.import_shortcodes) {
            var demo = options.demo,
                data = {'action': options.dummy_action, 'process':'import_start', 'demo': 'shortcodes', 'wpnonce': porto_setup_wizard_params.wpnonce};

            dummy_index = 0;
            dummy_count = 0;
            dummy_process = 'import_start';
            var data_download = {'action': 'porto_download_demo_file', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
            $.post(ajaxurl, data_download, function(response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {}
                if (response && response.process && response.process == 'success') {
                    porto_import_shortcodes_process(options, data);
                } else if (response && response.process && response.process == 'error') {
                    porto_import_failed(demo, response.message);
                } else {
                    porto_import_failed(demo);
                }
            }).fail(function(response) {
                porto_import_failed(demo);
            });
        } else {
            porto_import_finished(options);
        }
    }

    function porto_import_shortcodes_process(options, args) {
        var demo = options.demo;
        $.post(ajaxurl, args, function(response) {
            if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
                replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                response = $.parseJSON(response);
                if (response.process != 'complete') {
                    var requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    if (response.process) requests.process = response.process;
                    if (response.index) requests.index = response.index;

                    requests.demo = 'shortcodes';
                    porto_import_shortcodes_process(options, requests);

                    dummy_index = response.index;
                    dummy_count = response.count;
                    dummy_process = response.process;

                    showImportMessage(demo, "Importing element pages");
                } else {
                    porto_import_finished(options);
                }
            } else {
                porto_delete_tmp_dir('shortcodes');
                porto_import_failed(demo);
            }
        }).fail(function(response) {
            if (args.action == 'porto_import_dummy') {
                porto_import_failed(demo);
            } else {
                var requests;
                if (dummy_index < dummy_count) {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.index = ++dummy_index;
                    requests.demo = 'shortcodes';

                    porto_import_shortcodes_process(options, requests);
                } else {
                    requests = {'action': args.action, 'wpnonce': porto_setup_wizard_params.wpnonce};
                    requests.process = dummy_process;
                    requests.demo = 'shortcodes';

                    porto_import_shortcodes_process(options, requests);
                }
            }
        });
    }

    function porto_delete_tmp_dir(demo) {
        var data = {'action': 'porto_delete_tmp_dir', 'demo': demo, 'wpnonce': porto_setup_wizard_params.wpnonce};
        $.post(ajaxurl, data, function(response) {
        });
    }

    function porto_import_failed(demo, message) {
        porto_delete_tmp_dir(demo);
        if (typeof message == 'undefined') {
            showImportMessage(demo, 'Failed importing! Please check the <a href="' + window.location.href.replace('?page=porto-demos', '?page=porto-system') + '" target="_blank">"System Status"</a> tab to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red. If your server provider does not allow to update settings, please try using alternative import mode.');
        } else {
            showImportMessage(demo, message);
        }
        removeAlertLeavePage();
        jQuery('.porto-install-demo .porto-demo-install .porto-ajax-loader').remove();
        jQuery('.porto-install-demo .porto-demo-install').html(jQuery("#porto-install-options .theme-name").text() + ' installation is failed!');
        jQuery('.porto-install-demo .porto-demo-install').css('padding-left', 0);
        jQuery('#porto-install-options').show();
    }

    // import finished
    function porto_import_finished(options) {
        if (!options.demo) {
            removeAlertLeavePage();
            return;
        }
        var demo = options.demo;
        porto_delete_tmp_dir(demo);
        setTimeout(function() {
            if (jQuery('#wp-admin-bar-view-site').length) {
                showImportMessage(demo, '<a href="'+ jQuery('#wp-admin-bar-view-site a').attr('href') +'" target="_blank">Visit your site.</a>');
            } else if (jQuery('#current_site_url').length) {
                showImportMessage(demo, '<a href="'+ jQuery('#current_site_url').val() +'" target="_blank">Visit your site.</a>');
            } else {
                showImportMessage(demo, '');
            }
            jQuery('.porto-install-demo .porto-demo-install .porto-ajax-loader').remove();
            jQuery('.porto-install-demo .porto-demo-install').html(jQuery('#porto-install-demo-type').data('title') + ' installation is finished!');
            jQuery('.porto-install-demo .porto-demo-install').css('padding-left', 0);
            removeAlertLeavePage();
        }, 3000);
    }

    if ( jQuery( 'body' ).hasClass( 'porto_page_porto-plugins' ) ) {
        var $confirm;
        jQuery( '.porto-install-plugins .theme-actions .button-primary.disabled' ).on( 'click', function( e ) {
            e.preventDefault();
            $confirm = window.alert( 'ERROR:\n\nThis plugin can only be installed or updated, after you have successfully completed the Porto Registration on the "Registration" tab.' );
        });
    }

    jQuery('body').on('click', '.button-load-plugins', function(e) {
        e.preventDefault();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').hide();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').fadeIn();
        jQuery(this).closest('.porto-setup-wizard-plugins').children('.hidden').removeClass('hidden');
        jQuery(this).hide();
    });

    // import demos
    jQuery(document).on('change', '.pagebuilder-selector input[type="radio"]', function() {
        var $o = $(this).closest('.porto-install-section').find('.message-section');
        $o.addClass('d-none').children('div').addClass('d-none');
        if ($(this).parent('.radio').hasClass('notinstalled')) {
            $o.removeClass('d-none').children('.' + $(this).val()).removeClass('d-none').siblings('div');
            $(this).closest('.porto-install-section').find('.btn-actions').slideUp();
        } else {
            $(this).closest('.porto-install-section').find('.btn-actions').slideDown();
        }

        var live_urls = jQuery('#porto-install-options').data('live_urls');
        if ( live_urls[ $(this).val() ] ) {
            jQuery('#porto-install-options .live-site').attr('href', live_urls[ $(this).val() ]);
            if ( 'js_composer' == $(this).val() ) {
                jQuery('#porto-install-demo-type').val(jQuery('#porto-install-demo-type').data('o'));
            } else {
                jQuery('#porto-install-demo-type').val( $(this).val() + '-' + jQuery('#porto-install-demo-type').data('o'));
            }
        }
    });
    jQuery(document).on('click', '.porto-install-demos .theme .theme-wrapper', function(e) {
        e.preventDefault();
        if (jQuery(this).closest('.theme').hasClass('open-classic')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="classic"] a').click();
        } else if (jQuery(this).closest('.theme').hasClass('open-shop')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="shop"] a').click();
        } else if (jQuery(this).closest('.theme').hasClass('open-blog')) {
            jQuery(this).closest('.porto-install-demos').find('.demo-sort-filters [data-filter-by="blog"] a').click();
        } else {
            var $wrap = jQuery(this).closest('.porto-install-demos'),
                live_urls = jQuery(this).find('.theme-name').data('live-url'),
                live_url = live_urls.js_composer,
                $o = jQuery('#porto-install-options .pagebuilder-selector'),
                active_p = $o.data('active-p');

            if (jQuery(this).parent().hasClass('elementor') || jQuery(this).parent().hasClass('vc') || jQuery(this).parent().hasClass('gutenberg')) {
                if ('gutenberg' == active_p && !jQuery(this).parent().hasClass('gutenberg')) {
                    active_p = 'js_composer';
                } else if ('elementor' == active_p && !jQuery(this).parent().hasClass('elementor')) {
                    active_p = 'js_composer';
                } else if ('vc' == active_p && !jQuery(this).parent().hasClass('vc')) {
                    active_p = 'js_composer';
                }
                live_url = live_urls[active_p];
                $o.show();
                $o.find('.radio:not(.js_composer)').hide();
                if (jQuery(this).parent().hasClass('elementor')) {
                    $o.find('.radio.elementor').show();
                }
                if (jQuery(this).parent().hasClass('gutenberg')) {
                    $o.find('.radio.gutenberg').show();
                }
                if (jQuery(this).parent().hasClass('vc')) {
                    $o.find('.radio.vc').show();
                }
                $o.find('.radio.' + active_p + ' > input').prop('checked', true);
                $o.find('.message-section').addClass('d-none').children('div').addClass('d-none');
                if ($o.find('.radio.' + active_p).hasClass('notinstalled')) {
                    $o.find('.message-section').removeClass('d-none').find('.' + active_p).removeClass('d-none');
                    $o.next('.btn-actions').hide();
                } else {
                    $o.next('.btn-actions').show();
                }
            } else {
                jQuery('#porto-install-options .pagebuilder-selector').hide();
            }

            jQuery('#porto-install-options').show().data('live_urls', live_urls);
            $wrap.find('.porto-install-demo .theme-img').html(jQuery(this).find('.theme-screenshot').children().clone());
            $wrap.find('.porto-install-demo .theme-name').html(jQuery(this).find('.theme-name').text());
            $wrap.find('.porto-install-demo .live-site').attr('href', live_url);
            $wrap.find('.porto-install-demo .more-options').removeClass('opened');
            $wrap.find('.porto-install-demo .porto-install-options-section').hide();
            $wrap.find('.porto-install-demo .plugins-used').remove();

            var demo_id = jQuery(this).find('.theme-name').attr('id'),
                demo_type = 'js_composer' != active_p && typeof live_urls[active_p] != 'undefined' && live_urls[active_p] ? active_p + '-' + demo_id : demo_id;
            jQuery('#porto-install-demo-type').val(demo_type).data('o', demo_id).data('title', jQuery('#' + demo_id).html());
            if (jQuery(this).find('.plugins-used').length) {
                jQuery(this).find('.plugins-used').clone().insertAfter($wrap.find('.porto-install-section'));
                $wrap.find('.porto-install-demo .porto-install-section').hide();
                $wrap.find('.porto-install-demo .more-options').hide();
            } else {
                $wrap.find('.porto-install-demo .porto-install-section').show();
                $wrap.find('.porto-install-demo .more-options').show();
            }
            if (jQuery('.porto-import-yes:not(:disabled)').length) {
                jQuery('.porto-install-demo #import-status').html('');
            }
            jQuery.magnificPopup.open({
                items: {
                    src: '.porto-install-demo'
                },
                type: 'inline',
                mainClass: 'mfp-with-zoom',
                zoom: {
                    enabled: true,
                    duration: 300
                }
            });
        }
    });
    jQuery('.porto-install-demo .more-options').on('click', function(e) {
        e.preventDefault();
        jQuery(this).toggleClass('opened');
        jQuery(this).closest('.porto-install-demo').find('.porto-install-options-section').stop().toggle('slide');
    });

    // init theme options
    jQuery(document).ready(function($) {
        'use strict';
        function portoAdminLazyLoadImages(element) {
            var $element = $(element);
            if ($element.hasClass('lazy-load-active')) return;
            var src = $element.data('original');
            if (src) $element.attr('src', src);
            $element.addClass('lazy-load-active').removeAttr('data-original');
        }
        $('.redux-container .redux-group-tab:visible').find('.redux-image-select img[data-original]').each(function(index, element) {
            if ($.fn.waypoint) {
                $(element).waypoint(function (direction) {
                  portoAdminLazyLoadImages(element);
                }, { offset: '140%' });
            } else {
                portoAdminLazyLoadImages(element);
            }
        });
        $('.redux-group-tab-link-a').on('click', function() {
            if (typeof $(this).data('rel') == 'undefined') {
                return;
            }
            var id = $(this).data( 'rel' ) + '_section_group';
            if (!$('#' + id).length) {
                return;
            }
            $('#' + id).find('.redux-image-select img[data-original]').each(function(index, element) {
                if ($.fn.waypoint) {
                    $(element).waypoint(function (direction) {
                      portoAdminLazyLoadImages(element);
                    }, { offset: '140%' });
                } else {
                    portoAdminLazyLoadImages(element);
                }
            });
        });

        // add search box
        if ($('.redux-container').length && $.fn.typeWatch) {
            $('<input type="text" class="porto-theme-options-search" placeholder="' + js_porto_admin_vars.options_search_text + '" />').insertAfter('.redux-container #redux_save_sticky');
            $('.porto-theme-options-search').on('keypress', function(e) {
                var c = e.charCode || e.keyCode;
                if (13 == c) {
                    return false;
                }
            });
            var $options_container = $('.redux-container'),
                $options_extra_tabs = $options_container.find('.redux-section-field, .redux-info-field:not(.field_move), .redux-notice-field, .redux-container-group, .redux-section-desc, .redux-group-tab h3, .redux-accordion-field'),
                $search_targets = $options_container.find('.form-table tr, .redux-group-tab'),
                $options_menu_items = $('.redux-group-menu .redux-group-tab-link-li');
            $options_container.find('.redux-sidebar a').on('click', function() {
                if (!$('.porto-theme-options-search').val()) {
                    return;
                }
                var tab_id = '#' + ($(this).data('rel') + ($(this).parent().hasClass('hasSubSections empty_section') ? 1 : 0)) + '_section_group';
                $options_container.removeClass('porto-redux-search');
                $options_container.find('.redux-main .redux-group-tab').not(tab_id).hide();
                $options_extra_tabs.show();
                $options_container.find('.form-table tr').show();
                $options_container.find('.form-table tr.hide').hide();
                $options_container.find('.redux-notice-field.hide').hide();
                $options_container.find('.porto-theme-options-search').val('').trigger('change');
            });
            $('.porto-theme-options-search').typeWatch({
                wait: 600,
                highlight: false,
                captureLength: 0,
                callback: function(str) {
                    str = str.toLowerCase();
                    if (str && str.length > 2) {
                        var search = str.split(',');

                        $options_container.addClass('porto-redux-search');
                        $options_extra_tabs.hide();
                        $search_targets.hide();

                        $search_targets.filter(function () {
                            var $this = $(this),
                                found = true,
                                text = $this.find('.redux_field_th').text().toLowerCase();
                            if ($this.find('.description.field-desc').length) {
                                text += ' ' + $this.find('.description.field-desc').text().toLowerCase();
                            }
                            if ( ! text ) {
                                return false;
                            }

                            $.each(search, function(i, s) {
                                if (-1 == text.indexOf(s)) {
                                    found = false;
                                }
                            });

                            if (found) {
                                $this.show();
                            }

                            return found;
                        }).show();

                        var $group_menu = $options_container.find('.redux-sidebar .redux-group-menu');
                        $group_menu.find('li').removeClass('activeChild').removeClass('active');
                        $group_menu.find('.submenu, .subsection' ).hide();

                        $.redux.initFields();
                        $('.redux-main .redux-image-select img[data-original]:visible').each(function(index, element) {
                            if ($.fn.waypoint) {
                                $(element).waypoint(function (direction) {
                                  portoAdminLazyLoadImages(element);
                                }, { offset: '140%' });
                            } else {
                                portoAdminLazyLoadImages(element);
                            }
                        });
                    } else {
                        $options_container.removeClass('porto-redux-search');
                        $options_container.find('.redux-group-tab').hide();
                        $options_container.find('.form-table tr').show();
                        $options_container.find('.form-table tr.hide').hide();
                        $options_container.find('.redux-notice-field.hide').hide();
                        $options_extra_tabs.show();
                        var tab = $.cookie('redux_current_tab');
                        if (tab) {
                            var $current_li = $('#' + tab + '_section_group_li');
                            if ($current_li.parent().hasClass('redux-group-menu') && $current_li.hasClass('hasSubSections')) {
                                $current_li.find('.submenu, .subsection').show();
                            } else {
                                var $parent = $current_li.parents('.hasSubSections');
                                if ($parent.length) {
                                    $parent.addClass('activeChild').find('.submenu, .subsection').show();
                                }
                            }
                            $current_li.addClass('active');
                            $('#' + tab + '_section_group').show();
                        }
                    }
                }
            });
        }

        // header type make 2 columns
        if (!$('#customize-controls').length) {
            $('#porto_settings-header-type ul.redux-image-select').append('<li class="header-types-split classic-demos"></li><li class="header-types-split ecommerce-demos"></li>');
            $('#porto_settings-header-type ul.redux-image-select').children().each(function(index) {
                var $this = $(this);
                if ($this.hasClass('classic-demos') || $this.hasClass('ecommerce-demos')) {
                    return;
                }
                if (index < 8) {
                    $this.appendTo($this.closest('ul.redux-image-select').children('.classic-demos'));
                } else {
                    $this.appendTo($this.closest('ul.redux-image-select').children('.ecommerce-demos'));
                }
            });
        }
    });

    jQuery(document).ready(function($) {
        $('.porto-admin-nav .nolink').on('click', function(e) {
            e.preventDefault();
        });
        // images / colors swatch
        $('#porto_swatches').on('change', 'select.swatch_option_type', function () {
            var $parent = $(this).closest('.porto_swatches_section');
            $parent.find('[class*="swatch_field_"]').hide();
            $parent.find('.swatch_field_' + $(this).val()).show();
        });

        $('#porto_swatches').on('click', '.remove_swatch_image_button', function (e) {
            e.preventDefault();

            $(this).parent().find('.upload_image_id').val('');
            $(this).closest('td').find('img').attr('src', porto_swatches_params.placeholder_src);
        });

        var frame = null;

        $('#porto_swatches').on('click', '.upload_swatch_image_button', function (e) {
            e.preventDefault();
            var $button = $(this);

            if (frame) {
                frame.porto_swatches_btn = $button;
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Select or Upload an Image',
                button: {
                    text: 'Use this media'
                },
                multiple: false
            });

            frame.porto_swatches_btn = $button;

            frame.on('select', function () {
                if (frame.porto_swatches_btn) {
                    var attachment = frame.state().get('selection').first().toJSON(),
                        $input = frame.porto_swatches_btn.parent().find('.upload_image_id');
                    $input.val(attachment.id);
                    frame.porto_swatches_btn.closest('td').find('img').attr('src', attachment.url);
                }
            });

            frame.open();
            return false;
        });

        $('#woocommerce-product-data').on('woocommerce_variations_loaded', function() {
            var wrapper = $('#porto_swatches');
            if (!wrapper.length) {
                return;
            }
            wrapper.block({
                message: null,
                overlayCSS: {
                    opacity: 0.1
                }
            });
            $.ajax({
                url: porto_swatches_params.ajax_url,
                data: {
                    action:     'porto_load_swatches',
                    wpnonce:   porto_swatches_params.wpnonce,
                    product_id: porto_swatches_params.post_id
                },
                type: 'POST',
                success: function( response ) {
                    wrapper.empty().append( response );
                    wrapper.find('.porto-meta-color').each(function() {
                        $(this).trigger('plugin_init');
                    });
                    $('.woocommerce-help-tip', wrapper).tipTip({
                        'attribute': 'data-tip',
                        'fadeIn':    50,
                        'fadeOut':   50,
                        'delay':     200
                    });
                }
            });
        });


        // switch theme options panel
        $('body').on('click', '.switch-live-option-panel', function(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) {
                return false;
            }
            var type = $(this).hasClass('porto-theme-link') ? 'customizer' : 'redux';
            $(this).attr('disabled', 'disabled').addClass('disabled');
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {action: 'porto_switch_theme_options_panel', type: type },
                success: function() {
                    if ('customizer' == type) {
                        window.location.href = ajaxurl.replace('admin-ajax.php', 'customize.php');
                    } else {
                        window.location.href = ajaxurl.replace('admin-ajax.php', 'themes.php?page=porto_settings');
                    }
                }
            });
        });

        // set default options for header types
        if (typeof js_porto_admin_vars != 'undefined' && typeof js_porto_admin_vars.header_default_options != 'undefined') {
            js_porto_admin_vars.header_default_options = $.parseJSON(js_porto_admin_vars.header_default_options);
            $('#porto_settings-header-type input[type="radio"]').on('change', function() {
                var selected_header = $('#porto_settings-header-type input[type="radio"]:checked').val();
                $.each(js_porto_admin_vars.header_default_options, function(key, default_options) {
                    if ($.inArray(selected_header, key.replace(/ /g, '').split(',')) != -1) {
                        $.each(default_options, function(name, value) {
                            if ($('#porto_settings-' + name).length) {
                                if (value) {
                                    $('#porto_settings-' + name).find('input[value="' + value + '"]').trigger('click');
                                } else {
                                    $('#porto_settings-' + name).find('input[value]:first-child').trigger('click');
                                }
                            }
                        });
                        return false;
                    }
                });
            });
        }

        $('body').on('change', '[data-vc-shortcode="porto_blog"] select[name="post_layout"]', function(e) {
            var $trigger = $('[data-vc-shortcode="porto_blog"] select[name="post_style"]');
            if (!$trigger.length || $trigger.is(':hidden')) {
                return;
            }
            $trigger.children().removeAttr('disabled');
            if ( 'creative' == $(this).val() ) {
                $trigger.children().attr('disabled', 'disabled');
                $trigger.children('.default, .hover_info, .hover_info2').removeAttr('disabled');
            } else if ( 'timeline' == $(this).val() || 'masonry-creative' == $(this).val() ) {
                $trigger.children('.grid, .list, .widget').attr('disabled', 'disabled');
            }
        });

        // porto products filter
        $('body').on('click', '#filter_areas-price', function(e) {
            $(this).closest('.vc_shortcode-param').next('.vc_shortcode-param')[$(this).is(':checked') ? 'show' : 'hide']();
        });

        // extends gutenberg editor
        if ($(document.body).hasClass('block-editor-page')) {
            if ($('#custom_css').length && $('#custom_css').val() && !$('head > style#porto_custom_css').length) {
                $('<style></style>').attr('id', 'porto_custom_css').appendTo('head').html($('#custom_css').val().replace('/<script.*?\/script>/s', ''));
            }
            $('#custom_css').on('change', function(e) {
                if (!$('head > style#porto_custom_css').length) {
                    $('<style></style>').attr('id', 'porto_custom_css').appendTo('head');
                }
                $('style#porto_custom_css').html($(this).val().replace('/<script.*?\/script>/s', ''));
            });
        }

        // Pre-Order
        var porto_pre_order = {
            init: function() {
                $(document.body).on('change', 'input.variable_is_preorder', function(e) {
                    if ($(this).is(':checked')) {
                        $(this).closest('.woocommerce_variation').find('.show_if_pre_order').show();
                    } else {
                        $(this).closest('.woocommerce_variation').find('.show_if_pre_order').hide();
                    }
                });

                $(document.body).on('change', '#_porto_pre_order', function(e) {
                    if ($(this).is(':hidden')) {
                        return;
                    }
                    if ($(this).is(':checked')) {
                        $(this).closest('#woocommerce-product-data').find('.show_if_pre_order').show();
                        $(this).closest('#woocommerce-product-data').find('.general_options > a').click();
                    } else {
                        $(this).closest('#woocommerce-product-data').find('.show_if_pre_order').hide();
                    }
                });
                this.variations_loaded(null);

                $(document.body).on('woocommerce_variations_added', this.variation_added);
                $('#woocommerce-product-data').on('woocommerce_variations_loaded', this.variations_loaded);
            },
            variations_loaded: function(event, updated) {
                updated = updated || false;
                if (!updated) {
                    $('#woocommerce-product-data').find('input.variable_is_preorder, #_porto_pre_order').change();
                }

                $('#woocommerce-product-data .pre_order_available_date').datepicker({
                    defaultDate:     '',
                    dateFormat:      'yy-mm-dd',
                    numberOfMonths:  1,
                    showButtonPanel: true,
                    minDate        : new Date(),
                });
            },
            variation_added: function(e, qty) {
                if (1 === qty) {
                    porto_pre_order.variations_loaded(null);
                }
            }
        };
        if ($('#woocommerce-product-data').length) {
            porto_pre_order.init();
        }

        // WPBakery Frontend Editor
        if ($('body').hasClass('vc_editor') && window.vc && window.vc.events) {
            // extends tabs frontend editor
            if (window.InlineShortcodeView) {
                var origin_func = window.InlineShortcodeView.prototype.destroy;
                window.InlineShortcodeView.prototype.destroy = function(e) {
                    e && e.preventDefault && e.preventDefault(), e && e.stopPropagation && e.stopPropagation(), vc.showMessage(window.sprintf(window.i18nLocale.inline_element_deleted, this.model.setting("name"))), this.model.destroy()
                    vc.events.trigger("shortcodeView:destroy", this.model)
                }
            }
            if (window.InlineShortcodeView_vc_tabs) {
                window.InlineShortcodeView_vc_tabs.prototype.buildControlHtml = function(model) {
                    var params = model.get( 'params' ),
                        $tab = $( '<li data-m-id="' + model.get( 'id' ) + '" class="nav-item"><a class="nav-link" href="#tab-' + model.getParam( 'tab_id' ) + '"></a></li>' );
                    $tab.data( 'model', model );
                    $tab.find( '> a' ).text( model.getParam( 'title' ) );
                    return $tab;
                }
            }
            if (window.InlineShortcodeView_vc_column) {
                window.InlineShortcodeView_vc_column.prototype.customCssClassReplace = function() {
                    var css_classes, css_regex, class_match;
                    css_classes = this.$el.find( '.wpb_column' ).eq(0).attr( 'class' );
                    css_regex = /.*(vc_custom_\d+).*/;
                    class_match = css_classes && css_classes.match ? css_classes.match( css_regex ) : false;
                    if ( class_match && class_match[ 1 ] ) {
                        this.$el.addClass( class_match[ 1 ] );
                        this.$el.find( '.wpb_column' ).eq(0).attr( 'class', css_classes.replace( class_match[ 1 ], '' ).trim() );
                    }
                };
            }
            window.vc.events.on('shortcodeView:ready', function(e) {
                if (!e.view || !e.view.$el) {
                    return;
                }
                e.view.$el.find('.vc_controls-bc .vc_control-btn-append').each(function() {
                    $(this).insertAfter($(this).closest('.vc_controls').find('.vc_control-btn-prepend'));
                });
                var shortcode = e.attributes.shortcode;
                if ('porto_price_box' == shortcode) {
                    if (!e.view.$el.children('.porto-price-box').length) {
                        var c = e.view.$el.children().eq(0).attr('class');
                        e.view.$el.children().eq(0).removeAttr('class');
                        e.view.$el.addClass(c);
                    }
                }
                if (e.attributes.params && e.attributes.params.el_class) {
                    var cls = e.attributes.params.el_class.split(' '),
                        c_arr = ['d-inline-block', 'd-sm-inline-block', 'd-md-inline-block', 'd-lg-inline-block', 'd-xl-inline-block', 'd-none', 'd-sm-none', 'd-md-none', 'd-lg-none', 'd-xl-none', 'd-sm-block', 'd-md-block', 'd-lg-block', 'd-xl-block', 'd-md-flex', 'd-lg-flex', 'd-xl-flex', 'col-auto', 'col-md-auto', 'col-lg-auto', 'col-xl-auto', 'flex-1', 'ml-auto', 'mr-auto', 'mx-auto', 'h-100', 'h-50'];
                    cls.forEach(function(v, i) {
                        v = jQuery.trim(v);
                        if (!v) {
                            return;
                        }
                        if (0 === v.indexOf('mb-') || 0 === v.indexOf('m-b-')) {
                            e.view.$el.addClass(v);
                            var $c = e.view.$el.children().eq(0);
                            if ($c.length) {
                                $c.removeClass(v).addClass('mb-0');
                            }
                        } else if (-1 !== c_arr.indexOf(v) || 0 === v.indexOf('col-sm-') || 0 === v.indexOf('order-') || 0 === v.indexOf('align-self-') || 0 === v.indexOf('pc-') || 0 === v.indexOf('pull-') || ((('position-static' == v && -1 === cls.indexOf('porto-ibanner-layer')) || (/^p-(0|1|2|3|4|5|none)$/.test(v) && (!e.attributes.params.is_section || 'yes' != e.attributes.params.is_section)) || 'overflow-hidden' == v || 'col-half-section' == v || 0 === v.indexOf('background-color-')) && ('vc_column' == shortcode || 'vc_column_inner' == shortcode)) || ('vc_column' != shortcode && 'vc_column_inner' != shortcode && /^col-(\w{2})-(\d{1,2})$/.test(v))) {
                            if (-1 === v.indexOf('col-sm-')) {
                                e.view.$el.addClass(v);
                            }
                            if ('h-100' == v) {
                                return;
                            }
                            var $c = e.view.$el.children().eq(0);
                            if ($c.hasClass('porto-sicon-box-link')) {
                                $c = $c.children().eq(0);
                            }
                            if ($c.length) {
                                $c.removeClass(v);
                            }
                        }
                    });
                }
            });
        }
    });
});
