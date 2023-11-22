(function (window, document, $, undefined) {

    'use strict';

    const bdtTemplateLibrary = {
        //Initializing properties and methods
        init: function (e) {
            bdtTemplateLibrary.GlobalProps();
            bdtTemplateLibrary.methods();
        },

        //global properties
        GlobalProps: function (e) {
            this._window = $(window);
            this._document = $(document);
            this._body = $('body');
            this._html = $('html');
            this.librayWrapper = $(document);
            // this.librayWrapper = $('.bdt-template-library');

        },
        //methods
        methods: function (e) {
            bdtTemplateLibrary.clickDoc();
            bdtTemplateLibrary.scrollingLoading();
        },

        scrollingLoading:function(){
            (this._window).on('scroll', function () {
                var totalGridHeight = $(this._document).height() - $(this._window).height();
                if ( $(this._window).scrollTop() >= (totalGridHeight - 600) ) {
                    var clickedLi = $('div.bdt-template-library .bdt-list-divider').find('li.bdt-active');
                    if ( clickedLi.length == 1 ) {
                        $('.bdt-template-grid-container').find('a.load_more_btn').trigger('click');
                    }
                }
            });
        },
        clickDoc:function(){
            var importModalDataLoad, importDemoData, loadMoreDemoItem, switchCategoryItem, switchDemoTypeData,
                sortByDate, sortByTitle, searchDemoData, resetDemoData, reportDemoImportingError;

            importModalDataLoad = function (e) {
                var _this             = $(this);
                var modalSelector     = '#demo-importer-modal-section';
                var demo_id           = _this.data('demo-id');
                var json_url          = _this.data('demo-url');
                var demoTitle         = _this.data('demo-title');
                var plugins           = _this.parents('.demo-importer-template-item').find('.plugin-content-item').html();
                var sendReportBtnHtml = '<span class="dashicons dashicons-warning"></span> Report Problem';

                $(modalSelector).find('.bdt-template-report-button').html(sendReportBtnHtml);
                $(modalSelector).find('.demo-importer-form').removeClass('bdt-hidden');
                $(modalSelector).find('.demo-importer-callback').addClass('bdt-hidden');
                $(modalSelector).find('.demo-importer-loading').addClass('bdt-hidden');
                $(modalSelector).find('.demo-importer-callback .edit-page').html('');
                $(modalSelector).find('.demo-importer-callback .callback-message').html('');
                $(modalSelector).find('.required-plugin-list').html('');
                $(modalSelector).find('.required-plugin-list').html(plugins);


                $(modalSelector).find('.demo_id').val(demo_id);
                $(modalSelector).find('.demo_json_url').val(json_url);
                $(modalSelector).find('.default_page_title').val(demoTitle);
                $(modalSelector).find('.page_title').val('');
                bdtUIkit.modal(modalSelector).show();
            }

            importDemoData = function (e) {
                e.preventDefault();
                var modalSelector    = $('#demo-importer-modal-section');
                var demo_id          = modalSelector.find('.demo_id').val();
                var json_url         = modalSelector.find('.demo_json_url').val();
                var admin_url        = modalSelector.find('.admin_url').val();
                var import_type      = '';
                var page_title       = modalSelector.find('.page_title').val();
                var defaultPageTitle = modalSelector.find('.default_page_title').val();

                var template_import = modalSelector.find('input[name=template_import]:checked').val();

                if ( template_import == 'library' ) {
                    import_type = 'library';
                } else {
                    import_type = 'page';
                }

                $.ajax({
                    url       : ajaxurl,
                    data      : {
                        'action'            : 'ep_elementor_demo_importer_data_import',
                        'demo_url'          : json_url,
                        'demo_id'           : demo_id,
                        'demo_import_type'  : import_type,
                        'page_title'        : page_title,
                        'default_page_title': defaultPageTitle
                    },
                    dataType  : 'JSON',
                    beforeSend: function () {
                        $(modalSelector).find('.demo-importer-form').addClass('bdt-hidden');
                        $(modalSelector).find('.demo-importer-callback').removeClass('bdt-hidden');
                        $(modalSelector).find('.demo-importer-loading').removeClass('bdt-hidden');
                    },
                    success   : function (data) {
                        if ( data.success ) {
                            $(modalSelector).find('.demo-importer-callback .callback-message').html('Successfully <strong>' + defaultPageTitle + '</strong> has been imported.');
                            var page_url = admin_url + '/post.php?post=' + data.id + '&action=elementor';
                            $(modalSelector).find('.demo-importer-callback .edit-page').html('<a href="' + page_url + '" class="bdt-button bdt-button-secondary" target="_blank">' + data.edittxt + '</a>');
                        } else {
                            $(modalSelector).find('.demo-importer-callback .callback-message').text(data.edittxt);
                        }
                    },
                    complete  : function (data) {
                        $(modalSelector).find('.demo-importer-loading').addClass('bdt-hidden');
                    },
                    error     : function (errorThrown) {
                        $(modalSelector).find('.demo-importer-loading').addClass('bdt-hidden');
                    }
                });
            }

            loadMoreDemoItem = function (e) {
                var _this        = $(this);
                var _paged       = _this.data('paged');
                var _total_paged = _this.data('total');
                var clicked = _this.data('clicked');

                if (_paged < _total_paged){
                    if (clicked==0){
                        $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-paged').val(_paged);
                        $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-is-load-more').val(1);
                        bdtTemplateLibrary.getDemoData();
                        _this.data('clicked',1);
                    }
                }else{
                    _this.addClass('bdt-hidden');
                }
            }

            switchCategoryItem = function (e) {
                var _this    = $(this);
                var TermSlug = _this.data('demo');

                if ( !TermSlug ) {
                    return false;
                }
                $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-category-slug').val(TermSlug);

                $('.bdt-template-library .template-category-item').removeClass('bdt-active');
                $(this).addClass('bdt-active');

                bdtTemplateLibrary.resetLibraryParams()
                bdtTemplateLibrary.showLoader();
                bdtTemplateLibrary.getDemoData();
            }

            switchDemoTypeData = function(e){
                e.preventDefault();
                var _this  = $(this);
                var filter = _this.data('filter');

                $('.bdt-template-library .pro-free-nagivation-item').removeClass('bdt-active');
                $(this).addClass('bdt-active');

                $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-type-filter').val(filter);
                bdtTemplateLibrary.showLoader();
                bdtTemplateLibrary.getDemoData();
            }

            sortByDate = function (e) {
                var SortType = $(this).val();
                $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-sort-by-date').val(SortType);
                bdtTemplateLibrary.showLoader();
                bdtTemplateLibrary.getDemoData();
            }

            sortByTitle = function (e) {
                var SortType = $(this).val();
                $('.bdt-template-library #bdt-template-library-params').find('.bdt-template-sort-by-title').val(SortType);
                bdtTemplateLibrary.showLoader();
                bdtTemplateLibrary.getDemoData();
            }


            var searchTimer = null, searchDelaySec = 2000;
            searchDemoData = function (e) {
                e.preventDefault();
                var _this           = $(this);
                var searchVal       = _this.val();
                // clear previous timer
                clearTimeout(searchTimer);

                // start new timer
                searchTimer = setTimeout(function() {
                    bdtTemplateLibrary.showLoader();
                    bdtTemplateLibrary.getDemoData();
                }, searchDelaySec);
            }



            resetDemoData = function (e) {
                e.preventDefault();
                $(this).find('span').addClass('ep-tmpl-loading');
                $.ajax({
                    url       : ajaxurl,
                    data      : {
                        'action': 'ep_elementor_demo_importer_data_sync_demo_with_server'
                    },
                    dataType  : 'JSON',
                    beforeSend: function () {},
                    success   : function (response) {
                        window.location.href = window.location.href;
                    },
                    error     : function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }

            reportDemoImportingError = function (e) {
                e.preventDefault();
                var modalSelector = '#demo-importer-modal-section';
                var demo_id       = $(modalSelector).find('.demo_id').val();
                var demo_json_url = $(modalSelector).find('.demo_json_url').val();
                var _this         = $(this);

                $(_this).find('span').removeClass('dashicons-warning');
                $(_this).find('span').addClass('dashicons-update loading');


                $.ajax({
                    url       : ajaxurl,
                    type      : 'post',
                    data      : {
                        'action'       : 'ep_elementor_demo_importer_send_report',
                        'demo_id'      : demo_id,
                        'demo_json_url': demo_json_url
                    },
                    dataType  : 'JSON',
                    beforeSend: function () {
                    },
                    success   : function (response) {
                        //console.log(response.success);
                        if ( response.success ) {
                            _this.html('Report has been sent!');
                        } else {
                            _this.html('Fail to sent report!');
                            window.location.href = window.location.href;
                        }

                    },
                    error     : function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }


            this.librayWrapper.on('click', '.demo-template-action a.import-demo-btn', importModalDataLoad)
            this.librayWrapper.on('click', '#demo-importer-modal-section .import-into-library, #demo-importer-modal-section .import-into-page',importDemoData);
            this.librayWrapper.on('click', '.bdt-template-grid-container .load_more_btn',loadMoreDemoItem);
            this.librayWrapper.on('click', 'li.template-category-item',switchCategoryItem);
            this.librayWrapper.on('click', '.pro-free-nagivation-item',switchDemoTypeData);
            this.librayWrapper.on('change', '.bdt-template-library-sort select.sort-by-date',sortByDate);
            this.librayWrapper.on('change', '.bdt-template-library-sort select.sort-by-title',sortByTitle);
            this.librayWrapper.on('keyup', '.bdt-template-library .search-demo-template-value',searchDemoData);
            this.librayWrapper.on('click', '#sync_demo_template_btn',resetDemoData);
            this.librayWrapper.on('click', '#demo-importer-modal-section .bdt-template-report-button', reportDemoImportingError);
        },
        resetLibraryParams: function  (){
            var pararmsSelector = $('.bdt-template-library #bdt-template-library-params');
            pararmsSelector.find('.bdt-template-type-filter').val('*');
            pararmsSelector.find('.bdt-template-sort-by-title').val('');
            pararmsSelector.find('.bdt-template-sort-by-date').val('desc');
            pararmsSelector.find('.bdt-template-paged').val(0);
            pararmsSelector.find('.bdt-template-is-load-more').val(0);

            $('.bdt-template-library .search-demo-template-value').val('')
            $('.bdt-template-library .pro-free-nagivation-item').removeClass('bdt-active').find('.bdt-first-column').addClass('bdt-active')
            $('.bdt-template-library .pro-free-nagivation-item.bdt-first-column').addClass('bdt-active')
            $('.bdt-template-library .search-demo-template-value').val('')
            $('.bdt-template-library-sort select.sort-by-date').val('')
            $('.bdt-template-library-sort select.sort-by-title').val('')
        },

        resetPagination: function (){
            var pararmsSelector = $('.bdt-template-library #bdt-template-library-params');
            pararmsSelector.find('.bdt-template-paged').val(0);
            pararmsSelector.find('.bdt-template-is-load-more').val(0);
        },

        showLoader: function(){
            var loaderHtml = $('#bdt-template-library-content-loader p').clone();
            $('.bdt-template-library #bdt-template-library-content-body').html(loaderHtml);
            bdtTemplateLibrary.resetPagination()
        },

    // get data
        getDemoData: function(){

            var contentSelector = $('.bdt-template-library #bdt-template-library-content-body');
            var pararmsSelector = $('.bdt-template-library #bdt-template-library-params');

            var libraryCategory     = pararmsSelector.find('.bdt-template-category-slug').val();
            var libraryTypeFilter   = pararmsSelector.find('.bdt-template-type-filter').val();
            var librarySortTitle    = pararmsSelector.find('.bdt-template-sort-by-title').val();
            var librarySortDate     = pararmsSelector.find('.bdt-template-sort-by-date').val();
            var libraryPage         = pararmsSelector.find('.bdt-template-paged').val();
            var is_load_more        = pararmsSelector.find('.bdt-template-is-load-more').val();
            var moreBtnSelector     = $('.bdt-template-grid-container .load_more_btn');

            if (is_load_more == 1){
                is_load_more = true;
            }else{
                is_load_more = false;
            }

            $.ajax({
                url: ajaxurl,
                data: {
                    'action'        : 'ep_elementor_demo_importer_data_loading',
                    's'             : $('.bdt-template-library .search-demo-template-value').val(),
                    'term_slug'     : libraryCategory,
                    'demo_type'     : libraryTypeFilter,
                    'sort_By_title' : librarySortTitle,
                    'sort_By_date'  : librarySortDate,
                    'paged'         : libraryPage
                },
                dataType  : 'JSON',
                beforeSend: function () {
                },
                success   : function (response) {
                    if ( response.success ) {
                        if (is_load_more){
                            contentSelector.append(response.data)
                        }else{
                            contentSelector.html(response.data);
                        }

                        var _paged = response.paged;
                        var _total_paged = response.total_page;

                        moreBtnSelector.data('paged',_paged);
                        moreBtnSelector.data('total',_total_paged);

                        if (_paged < _total_paged){
                            moreBtnSelector.removeClass('bdt-hidden');
                        }else{
                            moreBtnSelector.addClass('bdt-hidden');
                        }
                    } else {
                        $(contentSelector).find('p').text(response.data);
                    }
                },
                error     : function (errorThrown) {
                    console.log(errorThrown);
                },
                complete:function () {
                    moreBtnSelector.data('clicked',0);
                }
            });
        }

    }
    bdtTemplateLibrary.init();

})(window, document, jQuery);
