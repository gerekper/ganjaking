jQuery(function($){
    let price_format;
    let convert_value;

    function convertPrice( price ) {
        price = parseFloat(price) * convert_value;
        return price.toFixed(2);
    }

    function ads_formatMoney(n, c, d, t) {

        c = isNaN(c = Math.abs(c)) ? 2 : c;
        d = d === undefined ? "." : d;
        t = t === undefined ? "," : t;

        let s = n < 0 ? "-" : "",
            i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
            j = (i.length) > 3 ? i.length % 3 : 0;

        let price = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
            (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");

        return price_format.pos === 'before' ? price_format.symbol + '' + price : price + '' + price_format.symbol;
    }

    let pricesHandlebars = {
        init : function () {
            Handlebars.registerHelper( 'numberFormat', function( value, options ) {

                value = parseFloat(value);

                let dl = options.hash['decimalLength'] || 2,
                    ts = options.hash['thousandsSep'] || ',',
                    ds = options.hash['decimalSep'] || '.';

                let re = '\\d(?=(\\d{3})+' + (dl > 0 ? '\\D' : '$') + ')';
                let num = value.toFixed(Math.max(0, ~~dl));

                return (ds ? num.replace('.', ds) : num).replace(new RegExp(re, 'g'), '$&' + ts);
            });
            Handlebars.registerHelper( 'format_price', function( value, options ) {

                value = parseFloat(value);

                let dl = options.hash['decimalLength'] || 2,
                    ts = options.hash['thousandsSep'] || ',',
                    ds = options.hash['decimalSep'] || '.';

                let re = '\\d(?=(\\d{3})+' + (dl > 0 ? '\\D' : '$') + ')';
                let num = value.toFixed(Math.max(0, ~~dl));

                let price = (ds ? num.replace('.', ds) : num).replace(new RegExp(re, 'g'), '$&' + ts);

                return price_format.pos === 'before' ? price_format.symbol + '' + price : price + '' + price_format.symbol;
            });

            Handlebars.registerHelper("math_format", function( lvalue, operator, rvalue ) {
                lvalue = parseFloat(lvalue);
                rvalue = parseFloat(rvalue);

                return {
                    "+": ads_formatMoney(lvalue + rvalue),
                    "-": ads_formatMoney(lvalue - rvalue),
                    "*": ads_formatMoney(lvalue * rvalue),
                    "/": ads_formatMoney(lvalue / rvalue)
                }[operator];
            });

            Handlebars.registerHelper( 'image', function ( url, option ) {

                if( option === 1 )
                    return url.replace('_640x640.jpg', '_50x50.jpg');
                else
                    return url.replace('_640x640.jpg', '');
            } );

            Handlebars.registerHelper( 'stars', function ( rate ) {

                let foo = '',
                    r = rate.toString(),
                    n = r.split('.'),
                    h = false;

                for( let i = 1; i <= 5; i++) {

                    if( n[0] >= i ) {
                        foo += '<i class="fa fa-star"></i>';
                    } else if( ! h ) {
                        h = true;
                        if( n[1] > 7 ) {
                            foo += '<i class="fa fa-star"></i>';
                        } else if( n[1] < 3 ) {
                            foo += '<i class="fa fa-star-o"></i>';
                        } else if( n[1] >= 3 && n[1] <= 7 ) {
                            foo += '<i class="fa fa-star-half-o"></i>';
                        } else {
                            foo += '<i class="fa fa-star-o"></i>';
                        }
                    } else {
                        foo += '<i class="fa fa-star-o"></i>';
                    }
                }

                return foo;
            } );

            Handlebars.registerHelper( 'lovercase', function ( str ) {

                return str.toLowerCase();
            } );
        }
    };
    pricesHandlebars.init();

    let obj = {
            main    : '#dm-main-result',
            form    : '#dm-container',
            import  : '#dm-container-import',
            results : '#dm-container-result',
            filters : '#dm-container-filter'
        },
        tmpl = {
            form     : '#tmpl-form',
            results  : '#tmpl-search-result',
            details  : '#tmpl-view-details',
            notfound : '#tmpl-not-found',
            import   : '#tmpl-form-import',
            reviews  : '#tmpl-reviews-list',
            filters  : '#tmpl-form-filter'
        },
        form = {
            catImport : '#categoryImport'
        },
        el = {
            cat               : '#categoryId',
            subcat            : '#subCategoryId',
            page              : '#page',
            supplier          : '#supplier',
            sort              : '#sort',
            sortBy            : '#sortby',
            keywords          : '#keywords',
            errorKeywords     : '#errorKeyword',
            item              : '.product-item-list',
            free              : '#free',
            warehouse         : '#warehouse',
            to                : '#to',
            company           : '#company',
            originalPriceFrom : '#originalPriceFrom',
            originalPriceTo   : '#originalPriceTo',
            volumeFrom        : '#volumeFrom',
            volumeTo          : '#volumeTo',
        },
        act = {
            details     : '.js-details',
            supplier    : '.js-set_supplier',
            btn         : '.js-import-product',
            apply       : '.js-import-selected',
            apply_range : '.js-apply-range'
        };

    let words;

    let SearchForm = {

        request : function( action, args, callback ) {

            args = args !== '' && args instanceof jQuery ? window.ADS.serialize(args) : args;

            $.ajaxQueue( {
                url     : ajaxurl,
                data: { action: 'ads_dpme_api', ads_action: action, args: args },
                type    : 'POST',
                dataType: 'json',
                success : callback
            });
        },

        adsrequest: function (action, args, callback) {

            args = args !== '' && typeof args === 'object' ? window.ADS.serialize(args) : args;

            $.ajaxQueue({
                url: ajaxurl,
                data: { action: 'ads_action_request', ads_action: action, args: args },
                type: 'POST',
                dataType: 'json',
                success: callback
            });
        },

        searchFormRender : function ( response ) {

            let template       = $(tmpl.form).html(),
                target         = $(obj.form),
                tmpl_filtes    = $(tmpl.filters).html(),
                target_filters = $(obj.filters),
                tmpl_import    = $(tmpl.import).html(),
                target_import  = $(obj.import);
            if( response ) {

                if( response.hasOwnProperty( 'error' ) ) {
                    window.ADS.notify( response.error, 'danger' );
                    //window.ADS.screenUnLock();
                } else {
                    convert_value = response.convert_value;

                    target.html( window.ADS.objTotmpl( template, response ) );
                    target_filters.html( window.ADS.objTotmpl( tmpl_filtes, response ) );
                    target_import.html( window.ADS.objTotmpl( tmpl_import, response ) );
                    target_import.closest('.main-results').addClass('box-shadow-element');

                    setTimeout( function(){
                        window.ADS.switchery( target );
                        window.ADS.switchery( target_filters );
                        window.ADS.switchery( target_import );

                        if( response.hasOwnProperty( 'create_cat' ) && parseInt( response.create_cat ) === 0 ) {
                            $('#cat_status').closest('.form-group').hide();
                        } else {
                            $(form.catImport).closest('.multi-select-full').hide();
                            $('#categoryImportDM').show();
                        }

                        if( response.hasOwnProperty( 'cat_status' ) &&  parseInt( response.cat_status ) > 0 ) {
                            $('#cat_status').val(response.cat_status).selectpicker('refresh');
                        }

                        $( form.catImport ).multiselect('destroy');
                        $( form.catImport ).multiselect( {
                            nonSelectedText: $('#textCategoryImport').text(),
                            includeSelectAllOption : true,
                            enableFiltering        : true,
                            enableCaseInsensitiveFiltering : true,
                            templates              : {
                                filter :
                                    '<li class="multiselect-item multiselect-filter">' +
                                    '<i class="fa fa-search"></i> <input class="form-control" type="text">' +
                                    '</li>'
                            },
                            onSelectAll            : function () {
                                $.uniform.update();
                            },
                            onDeselectAll          : function () {
                                $.uniform.update();
                            },
                            onChange : function( obj ) {},
                            onInitialized : function (obj) {
                                let btn = obj.parent().find('button');

                                btn.attr( 'title', btn.attr('title').replace(/(\r\n|\n|\r|\t)/gm,"") );
                            }
                        } ).parent().find( '.multiselect-container input[type="checkbox"]' ).uniform();
                    }, 300 );

                    $(document).trigger('search:request');
                }
            }
        },

        searchForm : function () {

            //window.ADS.screenLock();

            this.request( 'search_form', '', this.searchFormRender );
        },

        subCatRender : function ( response ) {

            let target = $(el.subcat),
                search_col = $('.search-col');

            if( response ) {

                if( response.hasOwnProperty( 'error' ) ) {
                    window.ADS.notify( response.error, 'danger' );

                    target.closest('.sub-cat-col').hide();
                    search_col.removeClass('col-lg-34 col-sm-28').addClass('col-lg-47 col-sm-44')
                    //window.ADS.screenUnLock();
                } else if( response.hasOwnProperty( 'success' ) ) {
                    target.closest('.sub-cat-col').hide();
                    search_col.removeClass('col-lg-34 col-sm-28').addClass('col-lg-47 col-sm-44');
                    $(document).trigger('search:request');
                } else {

                    let layout = '';

                    $.each( response, function( i, v ) {
                        layout += '<option value="'+v.key+'">'+v.val+'</option>';
                    } );

                    target.html(layout).each( function () {

                        if ( $( this ).find( 'option' ).length > 20 )
                            $( this ).data( 'live-search', 'true' );

                    } ).selectpicker('refresh');

                    target.closest('.sub-cat-col').show();
                    search_col.removeClass('inline-categories col-lg-47 col-md-44').addClass('col-lg-34 col-sm-28');

                    $(document).trigger('search:request');
                }
            }
        },

        subcat : function() {
            this.request( 'subcat', $(obj.main), this.subCatRender );
        },

        searchRender : function ( response ) {

            let target  = $(obj.results),
                found_text = 0;

            if( response ) {
                if( response.hasOwnProperty( 'error' ) ) {
                    window.ADS.notify( response.error, 'danger' );

                    if( response.hasOwnProperty('total') ) {
                        target.html( window.ADS.objTotmpl( $(tmpl.notfound).html(), response ) );
                        window.ADS.createJQPagination( obj.import, 0, 1, 40);
                    }
                } else {

                    let f = $('.tab-nav-filters');
                    if( f.hasClass('d-none'))
                        f.removeClass('d-none');

                    found_text    = response.total;
                    price_format  = response.currency_format;

                    if( response.hasOwnProperty( 'notavailable' ) ) {
                        $( '#items-notfounded' ).html(response.notavailable);
                    }

                    target.html( window.ADS.objTotmpl( $(tmpl.results).html(), response ) );

                    if( target.find('.pagination-menu').length ){
                        window.ADS.createJQPagination( '#'+target.attr('id'), response.total, response.page, 40);
                        window.ADS.createJQPagination( obj.import, response.total, response.page, 40);
                    }
                    $(el.sortBy).val( $(el.sort).val() ).selectpicker('refresh');
                    setTimeout( window.ADS.switchery( target ), 300 );

                    if( response.hasOwnProperty('breadcrumbs') && response.breadcrumbs ){
                        let c = response.breadcrumbs.length,
                            layout = $('.breadcrumb-list');

                        layout.html( '' );
                        $.each( response.breadcrumbs, function(i, v){
                            layout.append(' > ');

                            if( i+1 === c )
                                layout.append(v.title + ' ');
                            else {
                                let a = $('<a/>', {
                                    href: 'javascript:;',
                                    text: v.title
                                }).attr({
                                    'data-cat': v.id,
                                    'class': 'color-blue',
                                    'data-selector' : (i === 0) ? 'categoryId' : 'subCategoryId'
                                });

                                layout.append(a);
                            }
                        } );
                    } else {
                        $('.breadcrumb-list').html('');
                    }
                }
                let outfound_text = (found_text+'').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1,');
                $('#items-founded').text(outfound_text);

                let dt = $('#results-text');
                let tr = parseInt(outfound_text) === 1 ? dt.data('one') : dt.data('multiple');

                dt.text(tr);

                let d = $('.js-has_deposit'),
                    p = d.closest('span'),
                    t = $('#title-imports-left');

                d.text( response.deposit );

                response.deposit = parseInt(response.deposit);

                if( response.deposit === 1 ) {
                    t.html(p.data('one'));
                } else {
                    t.html(p.data('multiple'));
                }

                //window.ADS.screenUnLock();
            }
        },

        searchRequest : function() {
            // window.ADS.screenLock();

            if( $(el.cat).length ){
                this.request( 'search', $(obj.main), this.searchRender );
            } else {
                this.request( 'search_my', $(obj.main), this.searchRender );
            }
        },

        showDetails : function ( product ) {

            let info = $('#supplier-'+product.data.id );

            $('#product-'+product.data.id).hide();

            let to = $(el.to).val();

            info.html( window.ADS.objTotmpl( $( tmpl.details ).html(), product.data ) );

            setTimeout( window.ADS.switchery( info ), 300 );

            setTimeout( function () {
                info.find('#ship_country_' + product.data.productId).val(to).trigger('change');
            }, 400 );

            info.find('.carousel').carousel().swipe({
                swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                    if (direction === 'left') $(this).carousel('next');
                    if (direction === 'right') $(this).carousel('prev');
                },
                allowPageScroll:"vertical"
            });

            $('[data-id="'+product.data.id+'"]').addClass('opened');
            //window.ADS.screenUnLock();
        },

        checker : function () {

            let a = '#checkAll';
            let count_check = '#count_check';

            $(document).on('change', a, function(){
                $(obj.results).find('[type="checkbox"]').prop('checked', $(this).prop("checked"));
                $.uniform.update();

                let count_checked = $(obj.results).find('[type="checkbox"]:checked').length;

                $(count_check).html(
                    (count_checked > 0) ? ' ('+count_checked+')' : ''
                );
            });

            $(obj.results).on('change', '[type="checkbox"]', function(){

                let count_checked = $(obj.results).find('[type="checkbox"]:checked').length;

                $(count_check).html(
                    (count_checked > 0) ? ' ('+count_checked+')' : ''
                );

                if(false === $(this).prop('checked')){
                    $(a).prop('checked', false).uniform.update();
                }

                if( $(obj.results).find('[type="checkbox"]:checked').length === $(obj.results).find('[type="checkbox"]').length ){
                    $(a).prop('checked', true).uniform.update();
                }
            });
        },

        reportRender: function( response ) {

            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
            }

            if( response.hasOwnProperty( 'success' ) ) {
                window.ADS.notify( response.success, 'success' );
                $('#reportModal').modal('hide');
            }
        },

        activateRender: function( response ) {

            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
            }

            if( response.hasOwnProperty( 'message' ) ) {
                window.ADS.notify( response.message, 'success' );
                $('#activatePackage').modal('hide');

                if( response.deposit ) {
                    $('.js-has_deposit').text(response.deposit);
                }
                $('[name="package-code"]').val('');
            }
        },

        renderProgress: function( response ) {

            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
            }

            if( response.hasOwnProperty( 'success' ) ) {
                window.ADS.progress( '#checker-progress', response.total, response.current );
            }
        },

        checkNot : function(p) {
            this.request('check_not', 'page='+p, this.renderProgress);
        },

        savedImportSettins: function( response ) {
            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
            }

            if( response.hasOwnProperty( 'message' ) ){
                window.ADS.notify( response.message, 'success' );
            }
        },

        prepareForm : function( el ){

            let items = el.find(':input, textarea');
            let layout = 'om=nom';

            items.each(function ( index, element ) {

                let th = $(this);
                let name = th.attr('name');

                if( typeof name !== undefined ) {
                    switch (element.type) {
                        case 'checkbox':
                        case 'radio':

                            let checked = th.attr("checked") === 'checked' ? th.val() : 0;
                            layout += '&'+name+'='+checked;
                            break;
                        case 'hidden':
                        case 'text' :
                        case 'textarea' :
                        case 'select' :
                        case 'select-one' :
                            layout += '&'+name+'='+th.val();
                            break;
                    }
                }
            });

            return layout;
        },

        handler : function() {

            let $this = this;

            $(document).on('search:request', function(){
                //window.ADS.screenLock();

                let d = $(document).find('.jqpagination');
                if( d.find('a').length )
                    d.jqPagination('destroy');

                $this.searchRequest();
            });

            $(document).on('change', el.cat, function(){
                $(el.page).val(1);
                $this.subcat();
            });

            $(document).on('change', el.subcat, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('change', el.warehouse, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('change', el.to, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('change', el.company, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('change', el.free, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('click', act.apply_range, function(){
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('click', act.supplier, function(){
                $(el.supplier).val( $(this).data('supplier') );
                $(document).trigger('search:request');
            });

            $(document).on('click', '.clear_all', function(){
                $(el.page).val(1);
                $(el.supplier).val('')
                $(el.originalPriceFrom).val('');
                $(el.originalPriceTo).val('');
                $(el.volumeFrom).val('');
                $(el.volumeTo).val('');
                $(el.sort).val('volumeDown');
                $(el.sortBy).val('volumeDown').selectpicker('refresh');
                $(el.keywords).val('');
                $(el.warehouse).val('').selectpicker('refresh');
                $(el.to).val('US').selectpicker('refresh');
                $(el.company).val('9999').selectpicker('refresh');
                $(el.free).prop('checked', false).uniform.update();
                $(document).trigger('search:request');
            });

            $(document).on('click', '#create_cat', function(){

                let cat = $(form.catImport).closest('.multi-select-full'),
                    dm  = $('#categoryImportDM'),
                    cs  = $('#cat_status').closest('.form-group');

                if( ! $(this).is(':checked') ) {
                    cat.show();
                    cs.hide();
                    dm.hide();
                } else {
                    cat.hide();
                    cs.show();
                    dm.show();
                }

                $this.adsrequest('save_adsw_import_settings', $this.prepareForm( $('#collapseSettings') ), $this.savedImportSettins);
            });

            $(document).on('change', '#publish,#attributes,#cat_status', function(){
                $this.adsrequest('save_adsw_import_settings', $this.prepareForm( $('#collapseSettings') ), $this.savedImportSettins);
            } );

            $(document).on("keypress keyup blur", '#originalPriceFrom, #originalPriceTo',function (event) {
                $(this).val($(this).val().replace(/[^0-9\.]/g,''));
                if ((event.which !== 46 || $(this).val().indexOf('.') !== -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
            $(document).on("keypress keyup blur", '#volumeFrom, #volumeTo',function (event) {
                $(this).val($(this).val().replace(/[^0-9\.]/g,''));
                if ((event.which !== 46 || $(this).val().indexOf('.') !== -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
            $(document).on( 'keyup', '#originalPriceFrom, #originalPriceTo, #volumeFrom, #volumeTo', function (e) {

                let code;
                if (e.key !== undefined) {
                    code = e.key;
                } else if (e.keyIdentifier !== undefined) {
                    code = e.keyIdentifier;
                } else if (e.keyCode !== undefined) {
                    code = e.keyCode;
                }

                if( code !== 13 && code !== 'Enter' )
                    return false;
                $(el.page).val(1);
                $(document).trigger('search:request');
            });
            $(document).on('change', el.sortBy, function(e){
                $(el.page).val(1);
                $(el.sort).val( $(this).val() );
                $(document).trigger('search:request');
            });

            $(document).on('click', act.details, function () {

                let p = $(this).closest( el.item ),
                    id = p.data('id');

                if( p.hasClass('opened') ) {
                    if( $(this).data('action') === 'show' ) {
                        $('#product-' +id).hide();
                        $('#supplier-' +id).show();
                        p.addClass('opened_mobile');
                    } else {
                        $('#product-' +id).show();
                        $('#supplier-' +id).hide();
                        p.removeClass('opened_mobile');
                        $('body').removeClass('mobile-overflow');
                    }
                } else {
                    //window.ADS.screenLock();
                    $this.request( 'info', 'id='+id, $this.showDetails );
                }
            });

            $(document).on('click', '#search-btn', function() {

                if( $(el.keywords).val().length <= 2 && $(el.keywords).val() !== '') {
                    window.ADS.notify( $( '#errorKeyword' ).val(), 'danger' );
                    return false;
                }
                $(el.page).val(1);
                $(document).trigger('search:request');
            });
            $(document).on( 'focusout', '#keywords', function (e) {

                let key_val = $(el.keywords).val();

                if( ( key_val === '' || key_val.length >= 3 ) && key_val !== words ) {
                    $(el.page).val(1);
                    $(document).trigger('search:request');
                }
                else if( key_val.length <= 2 && key_val !== '' ){
                    window.ADS.notify( $( '#errorKeyword' ).val(), 'danger' );
                    return false;
                }
            });
            $(document).on( 'keyup', '#keywords', function (e) {

                var code;
                if (e.key !== undefined) {
                    code = e.key;
                } else if (e.keyIdentifier !== undefined) {
                    code = e.keyIdentifier;
                } else if (e.keyCode !== undefined) {
                    code = e.keyCode;
                }

                if( code !== 13 && code !== 'Enter' )
                    return false;

                if( $(this).val().length <= 2 && $('#keywords').val() !== '') {
                    window.ADS.notify( $( '#errorKeyword' ).val(), 'danger' );
                    return false;
                }
                $(el.page).val(1);
                $(document).trigger('search:request');
            });

            $(document).on('pagination:click', function(e){

                let p = parseInt( $(el.page).val() );
                if( p !== parseInt(e.page) ) {
                    $(el.page).val(e.page);
                    $(document).trigger('search:request');
                }
            });

            $(document).on( 'click', '.breadcrumbs-content a', function(){
                let s = $(this).data('selector');
                if( s ) {
                    if( $(this).data('cat') )
                        $('#'+$(this).data('selector')).val($(this).data('cat')).trigger('change');
                    else
                        $(el.cat).val(0).trigger('change');

                    if( s === 'categoryId' ) {
                        $(el.subcat).val(0);
                    }
                }
            } );

            $(document).on( 'click', '.report-this a', function() {

                let $mod = $('#reportModal');
                $('#report-id').val($(this).data('id'));
                window.ADS.switchery( $mod );
                $mod.modal('show');
                $mod.on('shown.bs.modal', function () {
                    $('#report-message').val('').focus();
                })
            } );

            $('#reportModal').on('click', 'button.ads-no', function() {

                let m = $('#report-message').val().trim();
                let rt = $('#report-type').val();
                if( m.length < 5 ) {
                    window.ADS.notify( 'Please enter a comment', 'danger' );
                } else if ( rt === '0' ) {
                    window.ADS.notify( 'Please select a reason', 'danger' );
                } else {
                    $this.request('send_report', $('#reportModal'), $this.reportRender);
                }
            });

            $('#js-startCheck').on('click', function() {
                $this.checkNot(1);
            });

            $(document).on( 'click', '.js-moreProducts', function() {

                let $mod = $('#activatePackage');
                window.ADS.switchery( $mod );
                $mod.modal('show');
            } );

            $('#activatePackage').on('click', 'button.ads-no', function() {

                let m = $('[name="package-code"]').val().trim();

                if( m.length < 5 ) {
                    window.ADS.notify( 'Please enter a comment', 'danger' );
                } else {
                    $this.request('send_package', $('#activatePackage'), $this.activateRender);
                }
            });

        },

        init: function () {

            if( ! $('.dm-maintenance').length && ! $('#dm-more-products').length ) {

                if( $('#dm-not-container').length ) {

                } else {
                    this.checker();
                    this.searchForm();
                }

                this.handler();
            }

            if( $('.dm-maintenance').length ) {
                $('.top-info').hide();
            }
        }
    };
    SearchForm.init();

    function str_replace(search, replace, subject) {
        return subject.split(search).join(replace);
    }

    function parseUrl(url) {

        let chipsUrl = url.split('?'),
            hostName = chipsUrl[0],
            paramsUrl = chipsUrl[1],
            chipsParamsUrl = paramsUrl.split('&'),
            urlArray = {};

        $.each(chipsParamsUrl, function(i, value) {
            let tempChips = value.split('=');
            urlArray[tempChips[0]] = tempChips[1];
        });

        return {
            'hostName' : hostName,
            'urlArray' : urlArray
        };
    }

    function changeUrl(url, params) {

        if (typeof params === 'undefined') {
            return false;
        }

        let result = parseUrl(url);

        $.each(params, function(key, value) {
            result.urlArray[key] = value;
        });

        return buildUrl(result.hostName, result.urlArray);
    }

    function buildUrl(hostName, urlArray) {
        let url = hostName + '?';
        let urlParams = [];

        $.each(urlArray, function(index, value) {
            if (typeof value === 'undefined') {
                value = '';
            }
            urlParams.push(index + '=' + value);
        });

        url += urlParams.join('&');
        return url;
    }

    function sortDatasKeys( obj, sub_title, time ) {

        let dates = getDates( new Date(Date.now() - 1296000000), Date.now() - 172800000 );
        let foo = [],
            f = '',
            t = '';

        $.each( dates, function( i, v ) {
            let d = new Date(v);
            let formated_date =
                d.getFullYear() + '/' +
                ('0' + (d.getMonth() + 1)).slice(-2) + '/' +
                ('0' + d.getDate()).slice(-2);

            if( i === 0 )
                f = v;

            t = v;

            let item = ( obj.hasOwnProperty( v ) ) ? obj[v] : 0;

            foo.push({
                type  : 'value',
                date  : formated_date,
                value : item
            });
        });

        return {
            sub_title: sub_title,
            time : time,
            data : foo,
            from : f,
            to   : t
        };
    }

    let getDates = function(startDate, endDate) {

        let dates = [],
            currentDate = startDate,
            addDays = function(days) {
                let date = new Date(this.valueOf());
                date.setDate(date.getDate() + days);
                return date;
            };

        while (currentDate <= endDate) {

            let formated_date =
                currentDate.getFullYear() + '-' +
                ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + currentDate.getDate()).slice(-2);

            dates.push(formated_date);
            currentDate = addDays.call(currentDate, 1);
        }

        return dates;
    };

    let Review = {

        getStar : function(width) {
            let star;
            width = parseInt( width.replace( /[^0-9]/g, '' ) );

            star = 0;
            if (width > 0) {
                star = parseInt( 5 * width / 100 );
            }

            return star;
        },

        pastReview : function( target, response ){

            let $obj    = response,
                review  = {
                    'flag'     : '',
                    'author'   : '',
                    'star'     : '',
                    'feedback' : '',
                    'date'     : ''
                },
                $feedbackList = $obj.find( '.feedback-list-wrap .feedback-item' ),
                feedList = {
                    list : []
                };

            if ( $feedbackList.length !== 0 ) {

                $feedbackList.each( function ( i, e ) {

                    let images = [];

                    let ratePercent = parseFloat( Review.getStar($(this).find('.star-view span').attr('style')) );

                    review = {};

                    review.feedback = $(this).find('.buyer-feedback').text();
                    if($(this).find('.buyer-feedback .r-time-new').length){
                        review.feedback = $(this).find('.buyer-feedback span:not(.r-time-new)').text();
                    }
                    review.feedback = review.feedback.replace('seller', 'store');
                    review.flag     = $(this).find('.css_flag').text().toLowerCase();
                    review.author   = $(this).find('.user-name').text();
                    review.star     = ratePercent;
                    review.ratePercent = parseInt( ratePercent * 20 );

                    $(this).find('.pic-view-item').each(function(index, value) {
                        images.push($(value).data('src'));
                    });

                    let dateBox = $(this).find('.r-time');

                    if($(this).find('.r-time-new').length){
                        dateBox = $(this).find('.r-time-new');
                    }
                    review.date = dateBox.text();

                    review.images = images;
                    feedList.list.push(review);
                });

                if ( feedList.list.length !== 0 ) {
                    target.html( window.ADS.objTotmpl( $(tmpl.reviews).html(), feedList ) );
                } else {
                    target.html( 'No Feedback' );
                }
            } else {
                target.html( 'No Feedback' );
            }

            //window.ADS.screenUnLock();
        },
        addTask : function ( feedbackUrl, target ) {

            //window.ADS.screenLock();

            let url = changeUrl(
                'https:'+feedbackUrl,
                {
                    'evaStarFilterValue' :   'all+Stars',
                    'evaSortValue'           : 'sortdefault%40feedback',
                    'page'                   : 1,
                    'currentPage'            : 1,
                    'v'                      : 2,
                    'withPictures'           : false,
                    'withPersonalInfo'       : false,
                    'withAdditionalFeedback' : false,
                    'onlyFromMyCountry'      : false,
                    'version'                : '',
                    'isOpened'               : true,
                    'translate'              : '+Y+',
                    'jumpToTop'              : false

                }
            );

            url = str_replace('&amp;', '&', url);

            $.ajax({
                url: url,
                type: "GET",
                dataType: 'html',
                success: function ( data ) {

                    let div = $( '<div></div>' );

                    Review.pastReview( target, $( div ).append( data ) );
                },
                fail : function (jqXHR, textStatus) {
                    //console.log(textStatus);
                }
            });
        },
        addAnalysisTask : function ( productId, target, sub_title, time ) {
            window.DMADS.aliExtension
                .getPage('https://home.aliexpress.com/dropshipper/item_analysis_ajax.htm?productId='+productId)
                .then( function (value) {
                    if( typeof value.html === 'object') {

                        if( value.html.hasOwnProperty('data') && value.html.data !== null && value.html.data.hasOwnProperty('saleVolume') ) {

                            let response = value.html.data;

                            response.imageUrl = response.imageUrl.replace('http:','');

                            $(target).find('.logistic-box').html( window.ADS.objTotmpl( $('#tmpl-analysis-table').html(), response ) );

                            $(target).find('.aliexpress-info').remove();

                            let too = [];

                            if( Object.keys(response.saleVolume).length ) {
                                too = response.saleVolume;
                            }

                            let foo = sortDatasKeys( too, sub_title, time );

                            window.adsChart.chartData( '#chart-'+productId, foo, 256 );
                        }
                    } else {
                        $(target).html( $('#tmpl-alert-nologin').html() );
                    }

                    //window.ADS.screenUnLock();
                } );
        },
        handler : function() {

            let $this = this;

            $(document).on('click', el.item + ' a.nav-link', function(){
                if( $(this).data('url') ) {
                    $this.addTask( $(this).data('url'), $( $(this).attr('href') ) );
                } else if( $(this).data('analysis') ) {

                    let tab = $( $(this).attr('href') );

                    if( $( 'body' ).hasClass('no-ali-extension') ) {
                        tab.html( $('#tmpl-alert-extension').html() );
                    } else {
                        if( ! tab.hasClass( 'has-data' ) ) {
                            //window.ADS.screenLock();
                            tab.addClass( 'has-data' );
                        }

                        $this.addAnalysisTask(
                            $(this).data('analysis'),
                            $(this).attr('href'),
                            $(this).data('sub_title'),
                            $(this).data('time')
                        );
                    }
                }
            });

            $( 'body' ).on( 'test:extensions', function ( e ) {
                if ( e.active ) {} else {
                    $( 'body' ).addClass('no-ali-extension');
                }
            } );
        },
        init: function(){
            if( ! $('.dm-maintenance').length ) {
                this.handler();
            }
        }
    };
    Review.init();

    let path = 'https://freight.aliexpress.com/ajaxFreightCalculateService.htm?currencyCode=USD&province=&city=&count=1&f=d';
    let pid;
    let Shipping = {

        requestShipping : function( product_id, country ) {

            window.ADS.aliExtension.sendAliExtension('getShippingProduct', {
                price: 1,
                productid: product_id,
                countryList: [{value: country}]
            }).then(function (data) {
                let shipping = {};

                for (let i in data) {
                    let params = data[i];

                    if (!params) {
                        continue;
                    }

                    let response = params.response;
                    let country = params.country;


                    shipping[country] = response.body && response.body.freightResult ? response.body.freightResult.map((item) => {
                        return {
                            company   : item.company,
                            country   : item.sendGoodsCountryFullName,
                            total     : convertPrice( item.standardFreightAmount.value ),
                            price     : convertPrice( item.standardFreightAmount.value ),
                            s         : false, //parseFloat( item.saveMoney ) === 0
                            time      : item.time,
                            isTracked : item.tracking,
                            free      : item.discount === 100,
                            processingTime : item.processingTime //время обработки
                        }
                    }) : [];
                }



                let args = [];

                args['list'] = [];
                $.each( shipping, function( i, item ) {

                    $.each( item, function (t, p) {
                        args['list'].push(p);
                    } )
                } );
                if( args['list'].length ) {

                    $('#list-shipping-'+pid).html( window.ADS.objTotmpl( $('#tmpl-shipping-view').html(), args ) );
                } else {
                    //вывод ошибки
                }

                return shipping;

            });

        },

        init : function () {

            let $this = this;

            if( ! $('.dm-maintenance').length ) {
                $(document).on('click', el.item + ' a.nav-link', function(){
                    if( $(this).data('shipping') ) {
                        pid = $(this).data('shipping');
                        $this.requestShipping( pid, $('#ship_country_'+pid).val() );
                    }
                });

                $(document).on('change', '.shipping-list-tab select.bootstrap-select', function () {
                    pid = $(this).parents('.shipping-list-tab').data('shipping');
                    $(this).selectpicker('refresh');
                    $this.requestShipping( pid, $(this).val() );
                });
            }
        }
    };
    Shipping.init();

    let ImportProducts = {

        request : function( action, args, callback ) {

            args = args !== '' && args instanceof jQuery ? window.ADS.serialize(args) : args;

            $.ajaxQueue( {
                url     : ajaxurl,
                data: { action: 'ads_dpme_api', ads_action: action, args: args },
                type    : 'POST',
                dataType: 'json',
                success : callback
            });
        },

        importProduct : function( id, cat, cc, pp, at, cs ) {
            this.request(
                'import_product',
                'id='+id+'&cat_status='+cs+'&cat='+cat+'&create='+cc+'&publish='+pp+'&attributes='+at,
                this.importRender
            );
        },

        importRender : function( response ) {

            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
                $(document).find( act.btn ).find('.import-product-spin').removeClass('fa-spin');
            } else {

                if( response.hasOwnProperty( 'success' ) ) {
                    window.ADS.notify( response.success, 'success' );
                }

                if( response.hasOwnProperty( 'deposit' ) ) {

                    let d = $('.js-has_deposit'),
                        p = d.closest('span'),
                        t = $('#title-imports-left');

                    d.text( response.deposit );

                    response.deposit = parseInt(response.deposit);

                    if( response.deposit === 1 ) {
                        t.html(p.data('one'));
                    } else {
                        t.html(p.data('multiple'));
                    }
                }

                ImportProducts.importImages( response );
            }
        },

        importImages : function( args ) {
            //console.log('importImages');
            this.request( 'import_images', window.Base64.encode( JSON.stringify( args ) ), this.imagesRender );
        },

        imagesRender : function( response ) {

            if( response.hasOwnProperty( 'error' ) ) {
                window.ADS.notify( response.error, 'danger' );
            }

            if( response.hasOwnProperty( 'success' ) ) {
                window.ADS.notify( response.success, 'success' );

                if( response.hasOwnProperty( 'product' ) ) {
                    let item = $(obj.results).find('[data-id="'+response.product+'"]');

                    item.find(act.btn).attr('disabled', 'disabled').text('Imported');
                    item.find('.progress-bar').addClass('progress-bar-green').css( 'width', '100%');
                    setTimeout(function () {
                        item.find('.progress').hide();
                    }, 3000)
                }
            }

            if( response.hasOwnProperty( 'message' ) ) {

                if( response.hasOwnProperty( 'product' ) ) {

                    $(obj.results)
                        .find('[data-id="'+response.product+'"]')
                        .find('.progress-bar')
                        .css( 'width', response.percent+'%');
                }

                setTimeout( function() {
                    ImportProducts.request(
                        'import_images',
                        window.Base64.encode( JSON.stringify( response ) ),
                        ImportProducts.imagesRender
                    );
                }, 500 );
            }
        },

        handler : function() {

            let $this = this;

            $(document).on('click', act.btn, function(){

                let th  = $(this).closest(el.item),
                    id  = th.data('id'),
                    c   = $(form.catImport),
                    cc  = $('#create_cat').is(':checked') ? 1 : 0,
                    at  = $('#attributes').is(':checked') ? 1 : 0,
                    pp  = $('#publish').is(':checked') ? 1 : 0,
                    cs  = $('#cat_status').val(),
                    cat = c.val() ? c.val() : 0;

                th.find(act.btn).addClass( 'disabled' ).find( '.import-product-spin' ).addClass( 'fa-spin' );
                th.find('.progress').show();
                $this.importProduct(id, cat, cc, pp, at ,cs);
            });

            $(document).on( 'click', act.apply, function () {

                let items = $(obj.results).find( el.item +' input:checkbox:checked');

                if ( items.length === 0 ) return;

                let c   = $(form.catImport),
                    cc  = $('#create_cat').is(':checked') ? 1 : 0,
                    at  = $('#attributes').is(':checked') ? 1 : 0,
                    pp  = $('#publish').is(':checked') ? 1 : 0,
                    cs  = $('#cat_status').val(),
                    cat = c.length ? c.val() : 0;

                items.each( function () {

                    let item  = $( this ).closest(el.item),
                        btn   = item.find( '.first-btn' ),
                        title = item.find( '.product-title h3' ).html();
                    let attr  = btn.attr('disabled');

                    if( btn.hasClass('disabled') || ( typeof attr !== 'undefined' && attr !== false ) ) {
                        window.ADS.notify( '<strong>'+title+'</strong> is already imported', 'danger' );
                        return true;
                    }

                    item.find(act.btn).addClass( 'disabled' ).find( '.import-product-spin' ).addClass( 'fa-spin' );
                    item.find('.progress').show();
                    $this.importProduct( item.data( 'id' ), cat, cc, pp, at, cs);

                } );

            });
        },
        init: function () {

            if( ! $('.dm-maintenance').length ) {
                this.handler();
            }
        }
    };
    ImportProducts.init();

    let InitImport = (function () {
        let $this, $body;

        let template = {
            alertBrowser   : $( '#tmpl-alertBrowser' ),
            alertExpansion : $( '#tmpl-alertExpansion' )
        };

        let obj = {
            form : $( '#ali-alert' )
        };

        return {

            init : function () {
                $this = this;
                $body = $( 'body' );

                $body.on( 'test:chrome', function ( e ) {
                    if ( !e.active ) {
                        $this.showAlertBrowser();
                    }
                } );

                $body.on( 'test:extensions:start', function ( e ) {
                    //ADS.coverShow();
                } );

                $body.on( 'test:extensions', function ( e ) {
                    if ( e.active ) {
                    } else {
                        $this.showAlertExpansion();
                    }
                    //ADS.coverHide();
                } );

            },

            showAlertBrowser   : function () {
                let tmpl = template.alertBrowser.html();
                obj.form.html( tmpl );
            },
            showAlertExpansion : function () {
                let tmpl = template.alertExpansion.html();
                obj.form.html( tmpl );

            }
        }
    })();

    InitImport.init();
});