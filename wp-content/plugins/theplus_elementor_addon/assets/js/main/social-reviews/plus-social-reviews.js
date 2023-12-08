/*Social Reviews*/
(function ($) {
	"use strict";
	var WidgetSocialReviewsHandler = function($scope, $) {
        var container = $scope.find('.tp-social-reviews');

    	container.each( function() {
            var e = $(this),
                BoxID = e.data("id"),
                Get_SN = e.data("scroll-normal"),
                Get_TL = e.data("textlimit");
				
                if(Get_SN.ScrollOn == true && Get_SN.TextLimit == false){
                    let SF_Text = e.find('.showtext');
                        SF_Text.each( function() {
                            if($(this)[0].clientHeight >= Get_SN.Height){
                                $(this).addClass(Get_SN.className);
                                $(this).css('height', Get_SN.Height);
                            }
                        });
                }

                $(document).on( 'click', "."+BoxID+".tp-social-reviews .readbtn", function() {
                    var div = $(this).closest('.tp-message'),
                        container = div.closest('.list-isotope .post-inner-loop'),
                        Scroll = div.closest('.tp-social-reviews').data("scroll-normal"),
                        S = div.find('.showtext');   

                        if(div.hasClass('show-text')){
                            div.removeClass('show-text show-less');
                            $(this).html(Get_TL.showmoretxt)
                            div.find('.sf-dots').css('display','inline');

                            if(Scroll.ScrollOn == true && Scroll.TextLimit == true){
                                S.removeClass(Scroll.className);
                                S.removeAttr('style');
                            }
                        }else{
                            div.addClass('show-text show-less');
                            $(this).html(Get_TL.showlesstxt)
                            div.find('.sf-dots').css('display','none');

                            let SF_Text = $('.tp-social-reviews').find(S);
                            if(Scroll.ScrollOn == true && Scroll.TextLimit == true){
                                SF_Text.each( function() {
                                    if($(this)[0].clientHeight >= Scroll.Height){
                                        S.addClass(Scroll.className);
                                        S.css('height', Scroll.Height);
                                    }
                                });
                            }
                        }
                        container.isotope({
                            itemSelector: ".grid-item",
                            resizable: !0,
                            sortBy: "original-order"
                        });
                });

                $( "."+BoxID ).on( "click", ".batch-btn-no", function(p) {
                    p.preventDefault();
                    this.closest(".tp-batch-recommend").style.display = "none";
                });

                var i = 0;
                if($("."+BoxID+".tp-social-reviews").find('.ajax_load_more').length > 0){
                    $("."+BoxID+" .post-load-more").on('click', function(e) {
                        e.preventDefault();
                        var loadFeed_click = $(this),
                            loadFeed = loadFeed_click.data('loadattr'),
                            loadFview = loadFeed_click.data('loadview'),
                            loadclass = loadFeed_click.data('loadclass'),
                            loadlayout = loadFeed_click.data('layout'),
                            loadloadingtxt = loadFeed_click.data('loadingtxt'),
                            current_text = loadFeed_click.text();
                            if ( loadFeed_click.data('requestRunning') ) { 
                                return; 
                            }

                        loadFeed_click.data('requestRunning', true);
                            $.ajax({
                                type:'POST',
                                data:'action=tp_reviews_load&view='+i+'&feedshow='+loadFview+'&loadattr='+loadFeed,
                                url:theplus_ajax_url,
                                beforeSend: function() {
                                    $(loadFeed_click).text(loadloadingtxt);
                                },
                                success: function(data) {
                                    let Data = JSON.parse(data),
                                        HtmlData = (Data && Data.HTMLContent) ? Data.HTMLContent : '',
                                        TotalReview = (Data && Data.TotalReview) ? Data.TotalReview : '',
                                        FilterStyle = (Data && Data.FilterStyle) ? Data.FilterStyle : '',
                                        Allposttext = (Data && Data.allposttext) ? Data.allposttext : '';

                                    if(Data == ''){
                                        $(loadFeed_click).addClass("hide");
                                    }else{
                                        let BlockClass = '.'+loadclass,
                                            CategoryClass = $(BlockClass + " .all .all_post_count"),
                                            PostLoopClass = $(BlockClass + " .post-inner-loop");
                                            PostLoopClass.append(HtmlData);

                                        let Totalcount = $(BlockClass).find('.grid-item').length;
                                            $(CategoryClass).html("").append(Totalcount);

                                        if(FilterStyle == 'style-2' || FilterStyle == 'style-3'){
                                            let Categoryload = $(BlockClass +' .category-filters li .filter-category-list').not('.all');

                                                $.each( Categoryload, function( key, value ) {
                                                    let span2 = $(value).find('span:nth-child(2)').data('hover'),
                                                        Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;      
                                                    $(value).find('span:nth-child(1).all_post_count').html("").append(Toatal2);
                                                });
                                        }
                                        if(loadlayout == 'grid' || loadlayout == 'masonry'){
                                            if($(BlockClass).hasClass("list-isotope")){
                                                $(PostLoopClass).isotope( 'layout' ).isotope( 'reloadItems' ); 
                                            }
                                        }
                                        if(Totalcount >= TotalReview){ 
                                            $(loadFeed_click).addClass("hide");
                                            $(loadFeed_click).parent(".ajax_load_more").append('<div class="plus-all-posts-loaded">'+Allposttext+'</div>');
                                        }else{
                                            $(loadFeed_click).text(current_text);
                                        }
                                    }
                                    i=i+loadFview;
                                },
                                complete: function() {
                                    loadFeed_click.data('requestRunning', false); 
                                    EqualHeightlayout();
                                }
                            }).then(function(){ 
                                if($("."+loadclass).hasClass("list-isotope")){
                                    if(loadlayout == 'grid' || loadlayout == 'masonry'){
                                        var container = $("."+loadclass+' .post-inner-loop')
                                            container.isotope({
                                                itemSelector: ".grid-item",
                                                resizable: !0,
                                                sortBy: "original-order"
                                            });					
                                    }
                                }
                            });
                    });
                }

                if($('body').find("."+BoxID+" .ajax_lazy_load").length){
                    var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
                    var recalcValues = function() {
                        windowWidth = $window.width();
                        windowHeight = $window.height();
                        documentHeight = $('body').height();
                        containerHeight = $("."+BoxID+".list-isotope").height();
                        containerOffset = $("."+BoxID+".list-isotope").offset().top+50;
                        setTimeout(function(){
                            containerHeight = $("."+BoxID+".list-isotope").height();
                            containerOffset = $("."+BoxID+".list-isotope").offset().top+50;
                        }, 50);
                    };              
                    recalcValues();
                    $window.resize(recalcValues);
                    $window.bind('scroll', function(e) {
                        e.preventDefault();
                        recalcValues();
                        scrollTop = $window.scrollTop();
                        $("."+BoxID+".list-isotope").each(function() {
                            containerHeight = $(this).height();
                            containerOffset = $(this).offset().top+50;

                            if($(this).find(".post-lazy-load").length && scrollTop < documentHeight && (scrollTop+60 > (containerHeight + containerOffset - windowHeight)) ){
                                var lazyFeed_click = $(this).find(".post-lazy-load"),
                                    lazyFeed = lazyFeed_click.data('lazyattr'),
                                    loadFview = lazyFeed_click.data('lazyview'),
                                    loadclass = lazyFeed_click.data('lazyclass'),
                                    loadlayout = lazyFeed_click.data('lazylayout'),
                                    loadloadingtxt = lazyFeed_click.data('loadingtxt'),
                                    current_text = lazyFeed_click.text();

                                if ( lazyFeed_click.data('requestRunning') ) { return; }
                                lazyFeed_click.data('requestRunning', true);
                                // if(parseInt(loadFeed.totalFeed) > parseInt(loadFeed.postview)){
                                    $.ajax({
                                        type:'POST',
                                        data:'action=tp_reviews_load&view='+i+'&feedshow='+loadFview+'&loadattr='+lazyFeed,
                                        url:theplus_ajax_url,
                                        beforeSend: function() {
                                            $(lazyFeed_click).text(loadloadingtxt);
                                        },
                                        success: function(data) {
                                            let Data = JSON.parse(data),
                                                HtmlData = (Data && Data.HTMLContent) ? Data.HTMLContent : '',
                                                TotalReview = (Data && Data.TotalReview) ? Data.TotalReview : '',
                                                FilterStyle = (Data && Data.FilterStyle) ? Data.FilterStyle : '',
                                                Allposttext = (Data && Data.allposttext) ? Data.allposttext : '';

                                            if(Data == ''){
                                                $(loadFeed_click).addClass("hide");
                                            }else{
                                                let BlockClass = '.'+loadclass,
                                                    CategoryClass = $(BlockClass + " .all .all_post_count"),
                                                    PostLoopClass = $(BlockClass + " .post-inner-loop");
                                                    PostLoopClass.append(HtmlData);

                                                let Totalcount = $(BlockClass).find('.grid-item').length;
                                                    $(CategoryClass).html("").append(Totalcount);

                                                if(FilterStyle == 'style-2' || FilterStyle == 'style-3'){
                                                    let Categoryload = $(BlockClass +' .category-filters li .filter-category-list').not('.all');

                                                    $.each( Categoryload, function( key, value ) {
                                                        let span2 = $(value).find('span:nth-child(2)').data('hover'),
                                                            Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;      
                                                            $(value).find('span:nth-child(1).all_post_count').html("").append(Toatal2);
                                                    });
                                                }
                                                if(loadlayout == 'grid' || loadlayout == 'masonry'){
                                                    if($(BlockClass).hasClass("list-isotope")){
                                                        $(PostLoopClass).isotope( 'layout' ).isotope( 'reloadItems' ); 
                                                    }
                                                }
                                                if(Totalcount >= TotalReview){ 
                                                    $(lazyFeed_click).addClass("hide");
                                                    $(lazyFeed_click).parent(".ajax_load_more").append('<div class="plus-all-posts-loaded">'+Allposttext+'</div>');
                                                    $window.unbind('scroll');
                                                }else{
                                                    $(lazyFeed_click).html('<div class="tp-spin-ring"><div></div><div></div><div></div></div>');
                                                }
                                            }
                                            i=i+loadFview;
                                        },
                                        complete: function() {
                                            lazyFeed_click.data('requestRunning', false);
                                            
                                            EqualHeightlayout();
                                        }
                                    }).then(function(){
                                        if($("."+loadclass).hasClass("list-isotope")){
                                            if(loadlayout == 'grid' || loadlayout == 'masonry'){
                                                let container = $("."+loadclass+' .post-inner-loop')
                                                    container.isotope({
                                                        itemSelector: ".grid-item",
                                                        resizable: !0,
                                                        sortBy: "original-order"
                                                    });					
                                            }
                                        }
                                    });
                                // }else{
                                //     $(lazyFeed_click).addClass("hide");
                                // }
                            }
                        });
                    });
                }

                $(".tp-social-reviews .tp-SR-copy-icon").unbind().click(function(){
                    let self = this,
                        store = document.createElement('textarea');
                        store.value = self.dataset.copypostid;
                        document.body.appendChild(store);
                        store.select();
                        document.execCommand('copy');
                        document.body.removeChild(store);
                        
                        self.innerHTML = '<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve" ><path fill="#fff" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite" /></path></svg>';
                        setTimeout(function(){
                            self.innerHTML = '<i class="far fa-copy CopyLoading"></i>';
                        }, 500);
                });

                /**compatibility with Equal Height*/
                function EqualHeightlayout() {
                    var Equalcontainer = jQuery('.elementor-element[data-tp-equal-height-loadded]');
                    if( Equalcontainer.length > 0 ){
                        EqualHeightsLoadded();
                    }
                }
        });
	};	
 
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-social-reviews.default', WidgetSocialReviewsHandler);
	});
})(jQuery);