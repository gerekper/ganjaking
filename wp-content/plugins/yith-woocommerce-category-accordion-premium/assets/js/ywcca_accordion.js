jQuery(document).ready(function ($) {

    var widget = $('.ywcca_container');


    widget.each(function () {


        var main_ul = $(this).find('ul.category_accordion'),
            menu_ul = $(this).find('ul.category_menu_accordion'),
            highlight = $(this).find('.ywcca_category_accordion_widget').data('highlight_curr_cat') == 'on',
            icon_class = $(this).find('.ywcca_category_accordion_widget').data('ywcca_style');


        var linkOver = function () {

                var ul = $(this).find('> ul.yith-children');
                toggle(ul, $(this));

            },
            linkOut = function () {
                var ul = $(this).find('> ul.yith-children'),
                    link = $(this).find('> a');

                if ($(this).hasClass('opened')) {
                    $(this).removeClass('opened').addClass('closed');
                    ul.hide();
                    $(this).find('>i').removeClass('icon-minus_' + icon_class).addClass('icon-plus_' + icon_class);
                }
            },
            menuOver = function () {
            },
            menuOut = function () {
            },
            toggle = function (ul, main) {

                ul.slideToggle(parseInt(ywcca_params.accordion_speed));

                if (main.hasClass('closed')) {
                    main.removeClass('closed').addClass('opened');
                    main.find('>i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                }
                else {
                    main.removeClass('opened').addClass('closed');
                    main.find('>i').removeClass('icon-minus_' + icon_class).addClass('icon-plus_' + icon_class);

                }
            },
            highlight_cat = function () {

                var start_element =  $(document).find('.ywcca_category_accordion_widget li.current-cat ');

                if( !start_element.length ){

                    start_element =  $(document).find('.ywcca_category_accordion_widget li.current-cat-parent.opened ');
                }
                var level = start_element.data('cat_level');
                start_element.find('> a').addClass('highlight_cat');

                while( level > 0 ){

                    level--;
                    var parent = start_element.parents('[data-cat_level="' + level + '"]');

                    parent.find('> a').addClass('highlight_cat');

                }

                $(document).find('.current-menu-item >a').addClass('highlight_cat');
                $(document).find('.current-menu-parent >a').addClass('highlight_cat');

            },
            highlight_menu = function(){

             var current_menu = $(document).find('.current-menu-item.open'),
                 current_parent_menu = current_menu.parents('.current-menu-ancestor.open');


             if( current_menu.length ){
                 current_menu.find( '>a').addClass('highlight_cat');
             }
             while( current_parent_menu.length ){
                 current_parent_menu.find( '>a').addClass('highlight_cat');
                 current_parent_menu = current_parent_menu.parents('.current-menu-ancestor.open');
             }

            },
            open_current_cat = function () {

                var curr_parent_cat = main_ul.find('.current-cat-parent, .active'),
                    current_cat = main_ul.find('.current-cat' ),
                    level = current_cat.data('cat_level');

                if(curr_parent_cat.length) {


                    curr_parent_cat.removeClass('closed').addClass('opened');
                    curr_parent_cat.find('li.current-cat i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                    curr_parent_cat.find('li.current-cat').removeClass('closed').addClass('opened');
                    current_cat.parent('ul.yith-children').show();

                    current_cat.find('ul.yith-children').show();
                    current_cat.find('i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                    current_cat.find('li').removeClass('closed').addClass('opened');
            
                    level = level - 1;


                    while (level >= 0) {


                       var parent = current_cat.parents('[data-cat_level="' + level + '"]');

                        parent.removeClass('closed').addClass('opened');
                        parent.find('>i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                        parent.find('>ul.yith-children').show();
                        level--;
                    }
                }
                else {

                    var current_cat = main_ul.find('.current-cat');
                    $(current_cat).removeClass('closed').addClass('opened');
                    $(current_cat).find('i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);

                    current_cat.find('>ul.yith-children').each(function () {

                        $(this).show();
                        // $(this).find('li').removeClass('closed').addClass('opened');
                        $(this).find('i').removeClass('icon-minus_' + icon_class).addClass('icon-plus_' + icon_class);


                    });
                }
            },
            open_current_menu_cat = function () {


            var current_menu = $(document).find('.ywcca_category_accordion_widget .current-menu-item');

                if( current_menu.length ){

                    var submenu = current_menu.find('>ul.ywcca-sub-menu');

                    current_menu.find('>i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                    current_menu.removeClass('closed').addClass('open');
                    submenu.show();

                    //check if has parent

                    var parent_menu = current_menu.parents('.current-menu-ancestor');

                    parent_menu.find('>i').removeClass('icon-plus_' + icon_class).addClass('icon-minus_' + icon_class);
                    parent_menu.removeClass('closed').addClass('open');
                    parent_menu.find('>ul').show();
                }
               };



        if (main_ul.length) {
            var dropdown_widget_nav = function () {

                var orderby = main_ul.data('ywcca_orderby'),
                    order = main_ul.data('ywcca_order');

                if (orderby == 'count') {
                    main_ul.find('>li').sort(function (a, b) {

                        var c1 = $(a).find('span:eq(0)'),
                            c2 = $(b).find('span:eq(0)'),
                            order_n = order == 'asc' ? 1 : -1;

                        c1 = c1.text().replace(/[^0-9\.]/g, '');
                        c2 = c2.text().replace(/[^0-9\.]/g, '');
                        c1 = c1 * 1;
                        c2 = c2 * 1;
                        if (c1 < c2)
                            return -1 * order_n;
                        else if (c1 > c2)
                            return 1 * order_n;
                        else return 0;

                    }).appendTo(main_ul);
                }
                main_ul.find('li').each(function () {

                    var main = $(this),
                        link = main.find('> a'),
                        ul = main.find('> ul.yith-children');

                    if (ul.length) {
                        //init widget


                        if ( ywcca_params.accordion_close ) {
                            main.removeClass('opened').addClass('closed');
                        }

                        if (main.hasClass('closed')) {
                            ul.hide();
                            link.before('<i class="icon-plus_' + icon_class + '"></i>');

                        }
                        else if (main.hasClass('opened')) {
                            link.before('<i class="icon-minus_' + icon_class + '"></i>');
                            ul.show();
                        }
                        else {
                            main.addClass('opened');
                            link.before('<i class="icon-minus_' + icon_class + '"></i>');
                            ul.show();
                        }

                        if (ywcca_params.event_type == 'click') {
                            main.find('i').on('click', function (e) {
                                toggle(ul, main);
                                e.stopImmediatePropagation();

                            });

                            main.on('click', function (e) {


                                if( $(e.target).filter('a').length ) {
                                    return;
                                }
                               
                                if( ywcca_params.toggle_always ) {
                                    toggle(ul, main);

                                }else{
                                  var a =$(e.target ).find('a');

                                  if( a.length ){
                                      window.location.href = a.attr('href');
                                  }
                                }
                                e.stopImmediatePropagation();

                            });
                        }

                        else {
                            var time_hov = 1*ywcca_params.accordion_speed;
                            var config = {
                                sensitivity: 5, // number = sensitivity threshold (must be 1 or higher)
                                interval: time_hov, // number = milliseconds for onMouseOver polling interval
                                over: linkOver, // function = onMouseOver callback (REQUIRED)
                                timeout: time_hov, // number = milliseconds delay before onMouseOut
                                out: linkOut // function = onMouseOut callback (REQUIRED)
                            };

                            $(main).hoverIntent(config);

                            var configMenu = {
                                sensitivity: 2, // number = sensitivity threshold (must be 1 or higher)
                                interval: time_hov, // number = milliseconds for onMouseOver polling interval
                                over: menuOver, // function = onMouseOver callback (REQUIRED)
                                timeout: time_hov, // number = milliseconds delay before onMouseOut
                                out: menuOut // function = onMouseOut callback (REQUIRED)
                            };

                            $(main_ul).hoverIntent(configMenu);
                        }
                    }

                });


            };

            dropdown_widget_nav();
        }

        if (menu_ul.length) {


            menu_ul.find('>ul.ywcca-menu li').each(function () {

                var main = $(this),
                    link = main.find('> a'),
                    ul = main.find('> ul.ywcca-sub-menu');

                if (ul.length) {
                    //init widget

                    if (ywcca_params.accordion_close)
                        main.removeClass('opened').addClass('closed');

                    if (main.hasClass('closed')) {
                        ul.hide();
                        link.before('<i class="icon-plus_' + icon_class + '"></i>');

                    }
                    else if (main.hasClass('opened')) {
                        link.before('<i class="icon-minus_' + icon_class + '"></i>');
                    }
                    else {
                        main.addClass('opened');
                        link.before('<i class="icon-minus_' + icon_class + '"></i>');
                    }

                    if (ywcca_params.event_type == 'click') {
                        main.find('i').on('click', function (e) {

                            toggle(ul, main);
                            e.stopImmediatePropagation();

                        });

                        main.on('click', function (e) {
                          
                         
                            if( $(e.target).filter('a').length ) {
                                return;
                            }
                            
                            if( ywcca_params.toggle_always ) {
                                toggle(ul, main);
                            }else{
                                var a =$(e.target ).find('a');

                                if( a.length ){
                                    window.location.href = a.attr('href');
                                }
                            }

                            e.stopImmediatePropagation();
                        });
                    }

                    else {
                        var config = {
                            sensitivity: 5, // number = sensitivity threshold (must be 1 or higher)
                            interval: 1000, // number = milliseconds for onMouseOver polling interval
                            over: linkOver, // function = onMouseOver callback (REQUIRED)
                            timeout: 1000, // number = milliseconds delay before onMouseOut
                            out: linkOut // function = onMouseOut callback (REQUIRED)
                        };

                        $(main).hoverIntent(config);

                        var configMenu = {
                            sensitivity: 2, // number = sensitivity threshold (must be 1 or higher)
                            interval: 1000, // number = milliseconds for onMouseOver polling interval
                            over: menuOver, // function = onMouseOver callback (REQUIRED)
                            timeout: 1000, // number = milliseconds delay before onMouseOut
                            out: menuOut // function = onMouseOut callback (REQUIRED)
                        };

                        $(main_ul).hoverIntent(configMenu);
                    }
                }

            });
        }

        if (ywcca_params.open_sub_cat_parent) {
            open_current_cat();
            open_current_menu_cat();
        }


        if (highlight) {
            highlight_cat();
            highlight_menu();
        }
    });
});
