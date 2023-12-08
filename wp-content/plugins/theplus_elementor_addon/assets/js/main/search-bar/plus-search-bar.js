/*Search Bar*/
(function($) {
    "use strict";
    var ajaxfilterMap = new Map();
    var WidgetSearchBarHandler = function($scope, $) {
        var container = $scope[0].querySelectorAll('.tp-search-bar'),
            main = $('.tp-search-bar', $scope),
            ajaxsearch = main.data("ajax_search"),
            acfData = main.data("acfdata"),
            Generic = main.data("genericfilter"),
            resultsinnerList = main.find('.tp-search-list-inner'),
            searcharea = main.find('.tp-search-area'),
            resultList = '.tp-search-slider tp-row',
            searchheader = main.find('.tp-search-header'),
            Rsetting = (container[0].dataset && container[0].dataset.resultSetting) ? JSON.parse(container[0].dataset.resultSetting) : [],
            pagesetting = (container[0].dataset && container[0].dataset.paginationData) ? JSON.parse(container[0].dataset.paginationData) : [],
            Defsetting = (container[0].dataset && container[0].dataset.defaultData) ? JSON.parse(container[0].dataset.defaultData) : [],
            effects = (Rsetting && Rsetting.animation_effects) ? Rsetting.animation_effects : 'no-animation';
            resultsinnerList.css('transform', 'translateX(0)');
        
        let OverlayBg = container[0].querySelectorAll('.tp-rental-overlay');
            if( OverlayBg.length > 0 ){
                tp_overlay_body($scope,searcharea);
            }

        let GetDropDown = container[0].querySelectorAll('.tp-sbar-dropdown');
            if(GetDropDown.length > 0){
                $('.tp-sbar-dropdown', $scope).on('click',function () {
                    $(this).attr('tabindex', 1).focus();
                    $(this).toggleClass('active');
                    $(this).find('.tp-sbar-dropdown-menu').slideToggle(300);
                });
                $('.tp-sbar-dropdown', $scope).focusout(function () {
                    $(this).removeClass('active');
                    $(this).find('.tp-sbar-dropdown-menu').slideUp(300);
                });
                $('.tp-sbar-dropdown .tp-sbar-dropdown-menu .tp-searchbar-li', $scope).on('click',function () {
                    $(this).parents('.tp-sbar-dropdown').find('span').text($(this).text());
                    $(this).parents('.tp-sbar-dropdown').find('input').attr('value', $(this).attr('id')).change();
                });
            }

        let rsearch = container[0].querySelectorAll('.tp-rsearch-tagname');
            if( rsearch.length > 0 ){
                rsearch.forEach(function(self) {
                    self.addEventListener("click", function(e){
                        e.preventDefault();
                        let getinput = container[0].querySelectorAll('.tp-search-input');
                            if(getinput.length > 0){
                                getinput[0].value = this.textContent;
                                $( container[0] ).change()
                            }
                    });
                });
            }

        var mousemoveajax = false;
        if(ajaxsearch.ajax == 'yes'){
            var $NavigationOn = (pagesetting.PNavigation) ? pagesetting.PNavigation : 0,
                $PostPer = (ajaxsearch && ajaxsearch.post_page) ? Number(ajaxsearch.post_page) : 3,
                SpecialCTP = (Defsetting && Defsetting.SpecialCTP) ? Defsetting.SpecialCTP : 0,
                $Searhlimit = (Defsetting && ajaxsearch.ajaxsearchCharLimit) ? Number(ajaxsearch.ajaxsearchCharLimit) : 0;

            let timeoutID = null;
            var tmpEvent = "";
            $(container[0]).on("change keyup", function(e){
                let EvnetType = e.type,
                    serText = $("input[name=s]", $scope).val(),
                    post = $("input[name=post_type]", $scope).val(),
                    tax = $("input[name=taxonomy]", $scope).val(),
                    cat = $("input[name=cat]", $scope).val();
                
                if( EvnetType == 'change' && mousemoveajax ){
                    return false;
                }

                if( EvnetType == 'keyup' ){
                    tmpEvent = EvnetType;
                }

                if( EvnetType == 'change' ){
                    if( tmpEvent == 'keyup' ){
                        tmpEvent = '';
                        return false;
                    }
                }

                if( (EvnetType == 'keyup' && serText.length >= $Searhlimit) 
                    || (EvnetType == 'change'
                        && ((post && SpecialCTP == 0) || (post && serText.length >= $Searhlimit) || (post=='' && serText.length >= $Searhlimit)) 
                        || (cat && SpecialCTP == 0) || (cat && serText.length >= $Searhlimit) || (cat=='' && serText.length >= $Searhlimit)) ){

                    container[0].querySelector('.tp-close-btn').style.cssText = "display:none";
                    container[0].querySelector('.tp-ajx-loading').style.cssText = "display:flex";
                   
                    tp_widget_searchbar(e.target.value);
                }else{
                    container[0].querySelectorAll('.tp-close-btn');
                }
            });

            function tp_widget_searchbar(search) {
                let serText = $("input[name=s]", $scope).val(),
                    tax = $("input[name=taxonomy]", $scope).val(),
                    post = $("input[name=post_type]", $scope).val();
                    resultsinnerList.html('');
                    searchheader.find('.tp-search-pagina', $scope).html('');

                let AjaxData = {
                    action : 'tp_search_bar',
                    searchData : $('.tp-search-form', $scope).serialize(),
                    text : serText,
                    postper : ajaxsearch.post_page,
                    GFilter : Generic,
                    ACFilter : acfData,
                    ResultData : pagesetting,
                    DefaultData : Defsetting,
                    nonce : ajaxsearch.nonce,
                };

                jQuery.ajax({
                    url: theplus_ajax_url,
                    method: 'post',
                    async: true,
                    data: AjaxData,  
                    beforeSend: function(jqXHR) {
                        if( ajaxfilterMap != null && ajaxfilterMap.size != 0 && ajaxfilterMap.size != 'undefined' && typeof ajaxfilterMap.abort !== "undefined"  ) {
							ajaxfilterMap.abort();
						}
						ajaxfilterMap = jqXHR;

                        if(pagesetting.Pagestyle == "pagination"){
                            container[0].querySelector('.tp-search-list-inner').style.cssText = "transform: translateX(0px)";
                        }
                    },
                    success: function(response) {
                        let ErrorHtml = container[0].querySelectorAll('.tp-search-error'),
                            Headerclass = container[0].querySelector('.tp-search-header'),
                            Resultclass = container[0].querySelector('.tp-search-list');

                            ErrorHtml[0].classList.remove('active');

                        if(response.data.error && ErrorHtml.length > 0){
                            ErrorHtml[0].innerHTML = response.data.message;
                            Headerclass.style.cssText = "display:none";
                            Resultclass.style.cssText = "display:none";

                            searcharea.slideDown(100);
                            return;
                        }else{
                            Headerclass.style.cssText = "display:flex";
                            Resultclass.style.cssText = "display:flex";
                            ErrorHtml[0].innerHTML = '';
                        }

                        var responseData = (response && response.data) ? response.data : '';
                        if (responseData && responseData.post_count !== 0) {
                            let posts = responseData.posts,
                                post = null,
                                outputHtml ='',
                                listHtml = '<div class="' + resultList.replace('.','') + '">%s</div>',
                                listItemHtml = '';
                                searcharea.slideDown(100);
                            
                                for (post in posts) {
                                    listItemHtml += getSearchhtml(posts[post]);
                                    if((parseInt(post) + 1) % responseData.limit_query == 0 || parseInt(post) === posts.length - 1) {
                                        outputHtml += listHtml.replace('%s', listItemHtml);
                                        listItemHtml = '';
                                    }
                                }

                                resultsinnerList.html(outputHtml);
                                if(effects != "no-animation"){
                                    tp_stagger_effect("flex");   
                                }

                                if(Rsetting.TotalResult){
                                    let ResultTxt = Rsetting.TotalResultTxt ? Rsetting.TotalResultTxt : '';
                                        searchheader.find('.tp-search-resultcount').html(responseData.post_count +' '+ResultTxt)
                                }

                                if(responseData.pagination) {
                                    container[0].querySelector('.tp-search-pagina').innerHTML = responseData.pagination;
                                    tp_pagination_ajax(resultList, resultsinnerList, responseData, AjaxData)
                                }else if(responseData.loadmore) {
                                    if( responseData.total_count > responseData.limit_query ){
                                        let BtnHtml = container[0].querySelectorAll('.ajax_load_more');
                                            if( BtnHtml.length > 0 ){
                                                BtnHtml[0].insertAdjacentHTML("afterbegin", responseData.loadmore);
                                            }
                                            tp_loadmore_ajax(responseData, AjaxData)
                                    }else{
                                        let Paginaclass = container[0].querySelectorAll('.tp-search-pagina');
                                        if(Paginaclass.length > 0){
                                            Paginaclass[0].innerHTML = responseData.loadmore_page;
                                        }
                                    }
                                }else if(responseData.lazymore) {
                                    if( responseData.total_count > responseData.limit_query ){
                                        container[0].querySelector('.ajax_lazy_load').innerHTML = responseData.lazymore;
                                        tp_lazymore_ajax(resultList, resultsinnerList, responseData, AjaxData)
                                    }
                                }
                        }else{
                            if( ErrorHtml[0] ){
                                ErrorHtml[0].classList.add('active');
                                ErrorHtml[0].innerHTML = Rsetting.errormsg;
                            }
                            Headerclass.style.cssText = "display:none";
                            Resultclass.style.cssText = "display:none";
                            searcharea.slideDown(400);
                            return;
                        }
                    },  
                    complete: function() {
                    },
                }).then(function(e) {
                    setTimeout(function(){ 
                        container[0].querySelector('.tp-ajx-loading').style.cssText = "display:none";
                        container[0].querySelector('.tp-close-btn').style.cssText = "display:flex";
                    }, 500);
                    tp_Close_result()
                });
            }
        }

        var getSearchhtml = function(data) {
            let output = '',
                Title = (data.title) ? data.title : '',
                Content = (data.content) ? data.content : '',
                LinkEnale = (Rsetting && Rsetting.ResultlinkOn) ? Rsetting.ResultlinkOn : '',
                Resultlinktarget = (LinkEnale && Rsetting && Rsetting.Resultlinktarget) ? `target="${Rsetting.Resultlinktarget}"` : '',
                Resultlink = (LinkEnale && data && data.link) ? `href="${data.link}"` : '';;
                
                if(Rsetting.textlimit){
                    if(Rsetting.TxtTitle){
                        let txtCount = (Rsetting.textcount) ? Rsetting.textcount : 100,
                            txtdot = (Rsetting.textdots) ? Rsetting.textdots : '';
                        if(Rsetting.texttype == "char"){
                            Title = Title.substring(0, txtCount) + txtdot; 
                        }else if(Rsetting.texttype == "word"){
                            Title = Title.split(" ", txtCount).toString().replace(/,/g, " ") + txtdot;
                        }
                    }

                    if(Rsetting.Txtcont){
                        let contcount = (Rsetting.ContCount) ? Rsetting.ContCount : 100,
                            txtdotc = (Rsetting.ContDots) ? Rsetting.ContDots : '';
                        if(Rsetting.ContType == "char"){
                            Content = Content.substring(0, contcount) + txtdotc;
                        }else if(Rsetting.ContType == "word"){
                            Content = Content.split(" ", contcount).toString().replace(/,/g, " ") + txtdotc;
                        }
                    }
                }

            output += `<div class="tp-ser-item ${ajaxsearch.styleColumn} animated-columns"><a class="tp-serpost-link" ${Resultlink} ${Resultlinktarget}>`;
    
                if(Rsetting.ONThumb && data.thumb){
                    output += `<div class="tp-serpost-thumb"><img class="tp-item-image" src="${data.thumb}""></div>`;
                }
    
                output += `<div class="tp-serpost-wrap">`;
                    if( (Rsetting.ONTitle && Title) || (Rsetting.ONPrice && data.Wo_Price) ){
                        output += `<div class="tp-serpost-inner-wrap">`;
                            if(Rsetting.ONTitle && Title){
                                output += `<div class="tp-serpost-title">${Title}</div>`;
                            }
                            if(Rsetting.ONPrice && data.Wo_Price){
                                output += `<div class="tp-serpost-price">${data.Wo_Price}</div>`;
                            }
                        output += `</div>`;
                    }
                    if(Rsetting.ONContent && Content){
                        output += `<div class="tp-serpost-excerpt">${Content}</div>`;
                    }
                    if(Rsetting.ONShortDesc && data.Wo_shortDesc){
                        output += `<div class="tp-serpost-shortDesc">${data.Wo_shortDesc}</div>`;
                    }

                output += `</div></a></div>`;
            return output;
        }

        var tp_loadmore_ajax = function(responseData, ajaxData) {
            let loadclass = container[0].querySelectorAll('.post-load-more'),
                Postclass = container[0].querySelector('.tp-search-slider'),
                Paginaclass = container[0].querySelectorAll('.tp-search-pagina');
                
                if(Paginaclass.length > 0 && responseData.loadmore_page){
                    Paginaclass[0].insertAdjacentHTML("beforeend", responseData.loadmore_page);
                }

                if(loadclass.length > 0){
                    loadclass[0].addEventListener("click", function(e){
                        let PageNum = Number(this.dataset.page),
                            NewNum = Number(PageNum + 1),
                            PostCount = container[0].querySelectorAll('.tp-ser-item');
                            ajaxData.offset = PostCount.length;
                            ajaxData.loadNumpost = pagesetting.loadnumber;

                            jQuery.ajax({
                                url: theplus_ajax_url,
                                method: 'post',
                                async: true,
                                data: ajaxData,
                                beforeSend: function() {
                                    loadclass[0].textContent = pagesetting.loadingtxt;
                                },
                                success: function (loadRes) {
                                    let posts = loadRes.data.posts,
                                        totalcount = loadRes.data.total_count,                                
                                        post = null,
                                        listItemHtml ='';

                                        for(post in posts){
                                            listItemHtml += getSearchhtml(posts[post]);
                                        }

                                        Postclass.insertAdjacentHTML("beforeend", listItemHtml);
                                        if(effects != "no-animation"){
                                            tp_stagger_effect("flex");
                                        }

                                        if( loadclass[0].dataset && loadclass[0].dataset.page ){
                                            loadclass[0].dataset.page = NewNum;
                                        }

                                        if(Paginaclass.length > 0){
                                            let PageCount = Paginaclass[0].querySelectorAll('.tp-load-number')
                                            if(PageCount.length > 0){
                                                PageCount[0].textContent = NewNum;
                                                tp_fadeIn(PageCount[0], 400)
                                            }
                                        }

                                        let postscount = container[0].querySelectorAll('.tp-ser-item');
                                        if( postscount.length >= totalcount ){
                                            loadclass[0].classList.add('tp-hide');
                                            loadclass[0].parentNode.insertAdjacentHTML("beforeend", `<div class="plus-all-posts-loaded">${pagesetting.loadedtxt}</div>`);
                                        }
                                },
                                complete: function() {
                                    loadclass[0].textContent = pagesetting.loadbtntxt;
                                },
                            });
                    });
                }
        }

        var tp_lazymore_ajax = function(listHtml, innerlist, responseData, ajaxData) {
            let loadclass = container[0].querySelectorAll('.post-lazy-load'),
                Postclass = container[0].querySelector('.tp-search-slider');
                
            var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
            var recalcValues = function() {
                windowWidth = $window.width();
                windowHeight = $window.height();
                documentHeight = $('body').height();
                containerHeight = $(".tp-search-area").height();
                containerOffset = $(".tp-search-area").offset().top + 50;
                setTimeout(function() {
                    containerHeight = $(".tp-search-area").height();
                    containerOffset = $(".tp-search-area").offset().top + 50;
                }, 50);
            };
                recalcValues();
                $window.resize(recalcValues);

            $window.bind('scroll', function(e) {
                e.preventDefault();
                    recalcValues();
                    scrollTop = $window.scrollTop();
                    containerHeight = $(".tp-search-area").height();
                    containerOffset = $(".tp-search-area").offset().top + 50;

                    var lazyFeed_click = $(".tp-search-area").find(".post-lazy-load"),
                        PostCount = container[0].querySelectorAll('.tp-ser-item');
                        ajaxData.offset = PostCount.length;
                        ajaxData.loadNumpost = pagesetting.loadnumber;
                
                        if ($(".tp-search-area").find(".post-lazy-load").length && scrollTop < documentHeight && (scrollTop + 60 > (containerHeight + containerOffset - windowHeight))) {
                            if (lazyFeed_click.data('requestRunning')) {
                                return;
                            }
                            lazyFeed_click.data('requestRunning', true);

                            jQuery.ajax({
                                url: theplus_ajax_url,
                                method: 'post',
                                async: true,
                                data: ajaxData,
                                beforeSend: function() {
                                },
                                success: function (loadRes) {
                                    let posts = loadRes.data.posts, 
                                        totalcount = loadRes.data.total_count,
                                        post = null,
                                        listItemHtml ='';

                                        for(post in posts){
                                            listItemHtml += getSearchhtml(posts[post]);
                                        }
                                        
                                        Postclass.insertAdjacentHTML("beforeend", listItemHtml);
                                        if(effects != "no-animation"){
                                            tp_stagger_effect("flex");
                                        }
                                        
                                        let postscount = container[0].querySelectorAll('.tp-ser-item');
                                        if(postscount.length == totalcount){
                                            loadclass[0].classList.add('tp-hide');
                                            loadclass[0].parentNode.insertAdjacentHTML("beforeend", `<div class="plus-all-posts-loaded">${pagesetting.loadedtxt}</div>`);
                                            $window.unbind('scroll');
                                        }
                                },
                                complete: function() {
                                    lazyFeed_click.data('requestRunning', false);
                                },
                            });
                        }
            });
        }

        var tp_pagination_ajax = function(listHtml, innerlist, responseData, ajaxData) {
            let Innerclass = container[0].querySelector('.tp-search-list-inner'),
                Buttonajax = container[0].querySelectorAll('.tp-pagelink.tp-ajax-page'),
                NextBtn = container[0].querySelectorAll('.tp-pagelink.next'),
                PrevBtn = container[0].querySelectorAll('.tp-pagelink.prev'),
                $counterOn = (pagesetting && pagesetting.Pcounter) ? pagesetting.Pcounter : 0,
                $Countlimit = (pagesetting && pagesetting.PClimit) ? pagesetting.PClimit : 3;

                if(effects != "no-animation"){
                    tp_stagger_pgination_effect();
                }

                if(Buttonajax.length > 0){
                    Buttonajax.forEach(function(self,idx) {
                        if(Number(self.dataset.page) == Number(1)){
                            let Findhtml = container[0].querySelectorAll('.tp-search-slider');
                            if(Findhtml.length > 0){
                                Findhtml[0].classList.add( 'ajax-'+Number(1) );
                            }
                        }else{
                            $(Innerclass).append('<div class="tp-search-slider tp-row ajax-'+ Number(idx+1) +'"></div>');
                        }

                        self.addEventListener("click", function(e){
                            if( Innerclass.classList.contains('animate-general') ){
                                Innerclass.style.opacity = 1;
                            }

                            let PageNumber = (this.dataset && this.dataset.page) ? Number(this.dataset.page) : 0,
                                Offset = (PageNumber * $PostPer) - ($PostPer),
                                Position = idx*100;
                                ajaxData.offset = Offset;

                                tp_pagination_active(Buttonajax,PageNumber)

                                if($NavigationOn){
                                    PrevBtn[0].setAttribute("data-prev", PageNumber);
                                    NextBtn[0].setAttribute("data-next", PageNumber);
                                }

                                let ajaxclass = Innerclass.querySelectorAll('.tp-search-slider.ajax-'+PageNumber);
                                    if(ajaxclass.length > 0){
                                        if(ajaxclass[0].querySelector('.tp-ser-item')){
                                            Innerclass.style.cssText = "transform: translateX("+ -(Position) +"%)";
                                            tp_pagination_hidden(responseData);

                                            if(effects != "no-animation"){
                                                tp_stagger_pgination_effect(ajaxclass);
                                            }
                                            return;
                                        }
                                    }

                                jQuery.ajax({
                                    url: theplus_ajax_url,
                                    method: 'post',
                                    async: true,
                                    data: ajaxData,
                                    beforeSend: function() {
                                    },
                                    success: function (res2) {
                                        let posts = res2.data.posts,
                                            post = null,
                                            listItemHtml ='';

                                            for(post in posts){
                                                listItemHtml += getSearchhtml(posts[post]);
                                            }

                                            $(ajaxclass[0]).append(listItemHtml);
                                            Innerclass.style.cssText = "transform: translateX("+ -(Position) +"%)";
                                            tp_pagination_hidden(responseData);
                                            if(effects != "no-animation"){
                                                tp_stagger_effect("flex");
                                            }
                                    },
                                    complete: function() {
                                        if(effects != "no-animation"){
                                            tp_stagger_pgination_effect();
                                        }
                                    },
                                });
                        });
                    });
                }

                if(NextBtn.length > 0){
                    NextBtn[0].addEventListener("click", function(e){
                        let PageNumber = (this.dataset && this.dataset.next) ? Number(this.dataset.next) : 0,
                            NewNumber = PageNumber + Number(1),
                            Position = -(PageNumber * Number(100)),
                            Offset = (NewNumber * $PostPer) - ($PostPer);
                            ajaxData.offset = Offset;

                            if($counterOn){
                                Buttonajax.forEach(function(self,idxi) {
                                    if(NewNumber == Number(self.dataset.page)){   
                                        if(self.classList.contains('tp-hide')){
                                            let one = Number(idxi+1 - $Countlimit);
                                                self.classList.remove('tp-hide');
                                                Buttonajax.forEach(function(self,idxii) {
                                                    if(one == idxii+1){
                                                        self.classList.add('tp-hide');
                                                    }
                                                });
                                        }
                                    }
                                });
                            }

                            tp_pagination_active(Buttonajax,NewNumber)

                            if($NavigationOn){
                                PrevBtn[0].setAttribute("data-prev", NewNumber);
                                NextBtn[0].setAttribute("data-next", NewNumber);
                            }

                            let ajaxclass = Innerclass.querySelectorAll('.tp-search-slider.ajax-'+NewNumber);
                                if(ajaxclass.length > 0){
                                    if(ajaxclass[0].querySelector('.tp-ser-item')){
                                        Innerclass.style.cssText = "transform: translateX("+ Position +"%)";
                                        tp_pagination_hidden(responseData);
                                        if(effects != "no-animation"){
                                            tp_stagger_pgination_effect(ajaxclass);
                                        }
                                        return;
                                    }
                                }

                                jQuery.ajax({
                                    url: theplus_ajax_url,
                                    method: 'post',
                                    async: true,
                                    data: ajaxData,
                                    beforeSend: function() {
                                    },
                                    success: function (nextres) {
                                        let posts = nextres.data.posts,
                                            post = null,
                                            listItemHtml ='';

                                            for(post in posts){
                                                listItemHtml += getSearchhtml(posts[post]);
                                            }

                                            $(ajaxclass[0]).append(listItemHtml);
                                            Innerclass.style.cssText = "transform: translateX("+ Position +"%)";
                                            tp_pagination_hidden(responseData);
                                            if(effects != "no-animation"){
                                                tp_stagger_effect("flex");
                                            }
                                    },
                                    complete: function() {
                                        if(effects != "no-animation"){
                                            tp_stagger_pgination_effect();
                                        }
                                    },
                                });
                    });
                }

                if(PrevBtn.length > 0){
                    PrevBtn[0].addEventListener("click", function(e){
                        if( Innerclass.classList.contains('animate-general') ){
                            Innerclass.style.opacity = 1;
                        }

                        let PageNumber = (this.dataset && this.dataset.prev) ? Number(this.dataset.prev) : 0,
                            OldNumber = PageNumber - Number(1),
                            Position = -(OldNumber * 100) + 100,
                            Offset = (OldNumber * $PostPer) - ($PostPer);
                            ajaxData.offset = Offset;

                            if($counterOn){
                                Buttonajax.forEach(function(self,idxi) {
                                    if(OldNumber == Number(self.dataset.page)){   
                                        if(self.classList.contains('tp-hide')){
                                            let one = Number(idxi+1) + Number($Countlimit);
                                                self.classList.remove('tp-hide');
                                                Buttonajax.forEach(function(self,idxii) {
                                                    if(one == idxii+1){
                                                        self.classList.add('tp-hide');
                                                    }
                                                });
                                        }
                                    }
                                });
                            }

                            tp_pagination_active(Buttonajax,OldNumber)

                            if($NavigationOn){
                                PrevBtn[0].setAttribute("data-prev", OldNumber);
                                NextBtn[0].setAttribute("data-next", OldNumber);
                            }

                            let ajaxclass = Innerclass.querySelectorAll('.tp-search-slider.ajax-'+OldNumber);
                                if(ajaxclass.length > 0){
                                    if(ajaxclass[0].querySelector('.tp-ser-item')){
                                        Innerclass.style.cssText = "transform: translateX("+ Position +"%)";
                                        tp_pagination_hidden(responseData);
                                        if(effects != "no-animation"){
                                            tp_stagger_pgination_effect(ajaxclass);
                                        }
                                        return;
                                    }
                                }

                                jQuery.ajax({
                                    url: theplus_ajax_url,
                                    method: 'post',
                                    async: true,
                                    data: ajaxData,
                                    beforeSend: function() {
                                    },
                                    success: function (Prevres) {
                                        let posts = Prevres.data.posts,
                                            post = null,
                                            listItemHtml ='';

                                            for(post in posts){
                                                listItemHtml += getSearchhtml(posts[post]);
                                            }

                                            $(ajaxclass[0]).append(listItemHtml);
                                            Innerclass.style.cssText = "transform: translateX("+ Position +"%)";
                                            tp_pagination_hidden(responseData);
                                            if(effects != "no-animation"){
                                                tp_stagger_effect("flex");
                                            }
                                    },
                                    complete: function() {
                                        if(effects != "no-animation"){
                                            tp_stagger_pgination_effect();
                                        }
                                    },
                                });


                    });
                }
        }

        var tp_pagination_hidden = function(responseData){
            if(responseData.columns){
                let PagelinkNext = container[0].querySelectorAll('.tp-pagelink.next'),
                    PagelinkPrev = container[0].querySelectorAll('.tp-pagelink.prev');
                
                if(PagelinkNext.length > 0){
                    let Next = (PagelinkNext[0].dataset && PagelinkNext[0].dataset.next) ? PagelinkNext[0].dataset.next : '';
                    if(parseInt(Next) == responseData.columns){
                       $('.tp-pagelink.next').hide();
                    }else{
                       $('.tp-pagelink.next').show();
                    }
                }
                
                if(PagelinkPrev.length > 0){
                    let Prev = (PagelinkPrev[0].dataset && PagelinkPrev[0].dataset.prev) ? PagelinkPrev[0].dataset.prev : '';
                    if(parseInt(Prev) == 1){
                        $('.tp-pagelink.prev').hide();
                    }else{
                        $('.tp-pagelink.prev').show();
                    }
                }
            }
        }

        var tp_pagination_active = function($class, $val){
            if($class.length > 0){
                $class.forEach(function(item) {
                    if($val == Number(item.dataset.page)){
                        item.classList.add('active');
                    }else if(item.classList.contains('active')){
                        item.classList.remove('active');
                    }
                });
            }
        }

        var tp_Close_result = function() {
            let Area = container[0].querySelector('.tp-search-area'),
                input= container[0].querySelector('input[name=s]'),
                overlay = $scope[0].querySelectorAll('.tp-rental-overlay'),
                closebtn = container[0].querySelector('.tp-close-btn');

                $('.tp-close-btn', $scope).on('click', function() {
                    input.value='';
                    $(this).hide();
                    $(Area).slideUp();

                    if(overlay.length > 0){
                        overlay[0].style.cssText = "visibility:hidden;opacity:0;";
                    }
                })

                main.keyup(function(e) {
                    if (e.key === "Escape") {
                        input.value='';
                        $(Area).slideUp();
                        closebtn.style.cssText = "display:none";
                    }
                })

        }

        var tp_fadeIn = function(element, duration=600) {
            element.style.display = '';
            element.style.opacity = 0;
            var last = +new Date();
            var tick = function() {
                element.style.opacity = +element.style.opacity + (new Date() - last) / duration;
                last = +new Date();
                if (+element.style.opacity < 1) {
                    (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
                }
            };
            tick();
        }

        var tp_stagger_effect = function(DStyle="auto") {
            let Findclass = container[0].querySelectorAll(".tp-search-list-inner.animate-general");

                if( Findclass.length > 0 && Findclass[0].dataset){
                    if( Findclass[0].dataset.animateColumns = "stagger" ){
                        let delay_time = (Findclass[0].dataset.animateDelay) ? Findclass[0].dataset.animateDelay : 50,
                            animation_stagger = (Findclass[0].dataset.animateStagger) ? Findclass[0].dataset.animateStagger : 150,
                            d = Findclass[0].dataset.animateType,
                            duration_time = (Findclass[0].dataset.animateDuration) ? Findclass[0].dataset.animateDuration : 50,
                            Findanimatclass = Findclass[0].querySelectorAll(".animated-columns:not(.animation-done)") ;

                            if( Findanimatclass.length > 0 ){
                                Findanimatclass.forEach(function(self,index){
                                    self.style.opacity = "0";
                                    self.style.display = "none";
                                    setTimeout(function(){
                                        if( !self.classList.contains('animation-done') ){
                                            self.classList.add('animation-done');
                                            jQuery(self).velocity( d, { delay: delay_time, display:DStyle, duration: duration_time, stagger: animation_stagger});
                                        }
                                    }, 500 * index);
                                })
                            }
                    }
                }
        }

        var tp_stagger_pgination_effect = function(ajaxclass="") {
            let Innerclass = container[0].querySelectorAll('.tp-search-list-inner');
                if( Innerclass.length > 0 ){
                    if( Innerclass[0].classList.contains('animate-general') ){
                        Innerclass[0].style.opacity = 1;
                    }
                }

            if(ajaxclass){
                ajaxclass.forEach(function(self){
                    let ADone = self.querySelectorAll(".tp-ser-item.animation-done") ;
                    if( ADone.length > 0 ){
                        ADone.forEach(function(item){
                            item.classList.remove('animation-done');
                        });
                    }
                })

                tp_stagger_effect("flex");
            }
        }
    
        document.addEventListener("click", function(event){
            if( event.target.closest('.tp-search-bar') == null ){
                if( ajaxfilterMap != null && ajaxfilterMap.size != 0 && ajaxfilterMap.size != 'undefined' && typeof ajaxfilterMap.abort !== "undefined"  ) {
                    ajaxfilterMap.abort();
                }

                /*@since 5.1.1*/
                if(container[0].querySelector('.tp-close-btn').style.display !== 'none' && container[0].querySelector('.tp-close-btn').style.display != ''){                   
                    container[0].querySelector('.tp-close-btn').click();
                }

                let arrayclass = [".tp-ajx-loading", ".tp-close-btn"];
                    arrayclass.forEach(function(item) {
                        if( container[0].querySelectorAll(item).length > 0 ){
                            container[0].querySelector(item).style.cssText = "display:none";        
                        }
                    });
            }
        });

        document.addEventListener("mouseover", function( event ) {
            let gettarget = event.target ? event.target : '',
                getinput = container[0].querySelectorAll('.tp-search-input');

                if( gettarget.closest('.tp-close-btn') || gettarget.closest('.tp-close-btn') == gettarget ){
                    if( getinput.length > 0 ){
                        getinput.forEach(function(item) {
                            item.blur();
                            container[0].querySelector('.tp-close-btn').style.cssText = "display:flex";
                            container[0].querySelector('.tp-ajx-loading').style.cssText = "display:none";
                        });
                    }
                }
                
                if( !gettarget.closest('.tp-input-field') ){
                    if( !gettarget.classList.contains('.tp-rsearch-tagname') || !gettarget.classList.contains('.tp-rsearch-tag') ){
                        return false;
                    }

                    if( getinput.length > 0 ){
                        mousemoveajax = true;
                        getinput[0].blur();
                    }
                }

        }, false);
    };

    function tp_overlay_body($scope,searcharea){
        let overlay = $scope[0].querySelector('.tp-rental-overlay'),
            textbox = $scope[0].querySelector('.tp-input-field');

        // Input    
        $(".tp-search-input", $scope).on({
            focus: function () {
                overlay.style.cssText = "visibility:visible;opacity:1;";
                textbox.style.cssText = "z-index:1000;";
            },
            focusout: function () {
                overlay.style.cssText = "visibility:hidden;opacity:0;";
                textbox.style.cssText = "z-index:0;";
            }
        });

        // select 
        $(".tp-select", $scope).on('click', function(e){
            overlay.style.cssText = "visibility:visible;opacity:1;";
        })
        $(".tp-rental-overlay", $scope).on('click', function(e){
            overlay.style.cssText = "visibility:hidden;opacity:0;";
            searcharea.slideUp();
        });

        // Esc ket to close
        $scope.keyup(function(e) {
            if (e.key === "Escape") {
                overlay.style.cssText = "visibility:hidden;opacity:0;";
            }
        })
    }

    window.addEventListener('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-search-bar.default', WidgetSearchBarHandler);
    });

})(jQuery);