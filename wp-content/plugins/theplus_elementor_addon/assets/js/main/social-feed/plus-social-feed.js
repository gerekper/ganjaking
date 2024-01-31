/*social feed*/
(function($) {
    "use strict";
    var WidgetSocialFeedHandler = function($scope, $) {
        let container = $scope[0].querySelectorAll('.tp-social-feed'),
            GetId = (container[0].dataset && container[0].dataset.id) ? container[0].dataset.id : '',
            Getscroll = (container[0].dataset && container[0].dataset.scrollNormal) ? JSON.parse(container[0].dataset.scrollNormal) : '',
            FeedData = (container[0].dataset && container[0].dataset.feedData) ? JSON.parse(container[0].dataset.feedData) : '',
            Getids = (container[0].dataset && container[0].dataset.ids) ? JSON.parse(container[0].dataset.ids) : [];

        tp_readbtn_click();
        
        let FindLoadmore = container[0].querySelectorAll('.ajax_load_more .post-load-more');
        if( FindLoadmore.length > 0 ){
            FindLoadmore[0].addEventListener("click", tp_feed_loadmore);
        }

        let FindLazyload = container[0].querySelectorAll(`.ajax_lazy_load`);
        if( FindLazyload.length > 0 ){
            tp_feed_lazyload();
        }

        function tp_readbtn_click() {
            let Getreadbtn = $scope[0].querySelectorAll('.tp-social-feed .readbtn');
            if( Getreadbtn.length > 0 ){
                Getreadbtn.forEach(function(self){
                    let GetPerent = self.closest('.tp-message').parentElement;
                    if( GetPerent && GetPerent.classList.contains('tp-fcb-contant') ){
                        self.addEventListener("click", tp_fancybox_readbtn);
                    }else{
                        self.addEventListener("click", tp_readbtn);
                    }
                });
            }

            if ( (Getscroll.ScrollOn || Getscroll.FancyScroll) && (Getscroll.TextLimit == false) ) {
                tp_massage_scroll();
            }

            tp_fancybox_popup();
        }
        
        function tp_fancybox_popup() {
            let Setting = (container[0].dataset && container[0].dataset.fancyOption) ? JSON.parse(container[0].dataset.fancyOption) : '',
                Getalbum = container[0].querySelectorAll('.grid-item.feed-Facebook');
                
            if(Getids.length > 0){
                Getids.forEach(function(self) {
                    $().fancybox({
                        selector : `[data-fancybox="${self}"]`,
                        buttons: Setting.button,
                        image: { preload: 0 },
                        loop: Setting.loop,
                        infobar: Setting.infobar,
                        animationEffect: Setting.animationEffect,
                        animationDuration: Setting.animationDuration,
                        transitionEffect: Setting.transitionEffect,
                        transitionDuration: Setting.transitionDuration,
                        arrows: Setting.arrows,
                        clickContent: Setting.clickContent,
                        clickSlide: Setting.slideclick,
                        dblclickContent: false,
                        dblclickSlide: false,
                        smallBtn: false,
                        iframe : {  preload : 0 },
                        youtube : { autoplay : 0 },
                        vimeo : { autoplay : 0 },
                        mp4 : { autoplay : 0 },
                        video: { autoStart: 0 },

                        beforeLoad: function() {
                            let $self = this;
                            let $array = ["youtube", "vimeo"];
                            if( !$array.includes($self.contentSource) ){
                                let FeedType = ($self && $self.src) ? document.querySelector($self.src).dataset.fancyfeedtype : '';

                                if( FeedType == "Instagram" ){
                                    let Getslider = $($self.src).find('#IGGP-slider');
                                        if( Getslider.length > 0 ){
                                            Getslider[0].style.cssText = "display: none";
                                        }
                                }
                            }
                        },
                        afterShow: function() {
                            let $self = this;
                            let $array = ["youtube", "vimeo"];
                            if( !$array.includes($self.contentSource) ){
                                let FeedType = document.querySelector($self.src).dataset.fancyfeedtype;
                            
                                if( FeedType == "Instagram" ){
                                    let Getslider = $($self.src).find('#IGGP-slider');
                                        if( Getslider.length > 0 ){
                                            Getslider[0].style.cssText = "display: flex";
                                            Getslider.slick({
                                                speed: 1000,
                                                prevArrow:'<a href="#" class="slick-prev IGGP-slide-arrow">&#8249;</a>',
                                                nextArrow:'<a href="#" class="slick-next IGGP-slide-arrow">&#8250;</a>',
                                            });
                                        }
                                }
                            }
                        },
                    });
                });
            }

                if( Getalbum.length > 0 ){
                    Getalbum.forEach(function(self) {
                        let itemindex = (self.dataset && self.dataset.index) ? self.dataset.index : '';
                        if(Getids.length > 0){
                            Getids.forEach(function(item) {
                                $().fancybox({
                                    selector : `[data-fancybox="album-${itemindex}-${item}"]`,
                                    buttons: Setting.button,
                                    image: { 
                                        preload: true
                                    },
                                    loop: Setting.loop,
                                    infobar: Setting.infobar,
                                    animationEffect: Setting.animationEffect,
                                    animationDuration: Setting.animationDuration,
                                    transitionEffect: Setting.transitionEffect,
                                    transitionDuration: Setting.transitionDuration,
                                    arrows: Setting.arrows,
                                    clickContent: Setting.clickContent,
                                    clickSlide: Setting.slideclick,
                                    dblclickContent: false,
                                    dblclickSlide: false,
                                    smallBtn: false,
                                });
                            });
                        }
                    });
                }
        }

        let i = 0;
        function tp_feed_lazyload() {
            var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
            var recalcValues = function() {
                windowWidth = $window.width();
                windowHeight = $window.height();
                documentHeight = $('body').height();
                containerHeight = $("."+GetId+".list-isotope").height();
                containerOffset = $("."+GetId+".list-isotope").offset().top + 50;
                setTimeout(function() {
                    containerHeight = $("."+GetId+".list-isotope").height();
                    containerOffset = $("."+GetId+".list-isotope").offset().top + 50;
                }, 50);
            };

            recalcValues();
            $window.resize(recalcValues);
            $window.bind('scroll', function(e) {
                e.preventDefault();
                recalcValues();
                scrollTop = $window.scrollTop();
                $("."+GetId+".list-isotope").each(function() {
                    containerHeight = $(this).height();
                    containerOffset = $(this).offset().top + 50;
                    if ($(this).find(".post-lazy-load").length && scrollTop < documentHeight && (scrollTop + 60 > (containerHeight + containerOffset - windowHeight))) {
                        let $this = this,
                            lazyFeed_click = $(this).find(".post-lazy-load"),
                            lazyFeed = lazyFeed_click.data('lazyattr'),
                            loadFview = lazyFeed_click.data('lazyview'),
                            loadclass = lazyFeed_click.data('lazyclass'),
                            loadlayout = lazyFeed_click.data('lazylayout'),
                            loadloadingtxt = lazyFeed_click.data('loadingtxt'),
                            current_text = lazyFeed_click.text();
                            
                        if (lazyFeed_click.data('requestRunning')) { return; }
                            lazyFeed_click.data('requestRunning', true);

                        $.ajax({
                            type: 'POST',
                            data: `action=tp_feed_load&view=${i}&feedshow=${loadFview}&loadattr=${lazyFeed}`,
                            async: true,
                            url: theplus_ajax_url,
                            beforeSend: function() {
                                $(lazyFeed_click).text(loadloadingtxt);
                            },
                            success: function(data) {
                                let Data = data,
                                    HtmlData = (Data && Data.HTMLContent) ? Data.HTMLContent : '',
                                    totalFeed = (Data && Data.totalFeed) ? Data.totalFeed : '',
                                    FilterStyle = (Data && Data.FilterStyle) ? Data.FilterStyle : '',
                                    maximumposts = (Data && Data.maximumposts) ? Data.maximumposts : '',
                                    Allposttext = (Data && Data.allposttext) ? Data.allposttext : '';

                                if (Data == '') {
                                    $(lazyFeed_click).addClass("hide");
                                    return;
                                } else {
                                    let BlockClass = '.' + loadclass,
                                        CategoryClass = $(BlockClass + " .all .all_post_count"),
                                        PostLoopClass = $(BlockClass + " .post-inner-loop");
                                        PostLoopClass.append(HtmlData);

                                    let Totalcount = $(BlockClass).find('.grid-item').length;
                                        $(CategoryClass).html("").append(Totalcount);

                                    if (FilterStyle == 'style-2' || FilterStyle == 'style-3') {
                                        let Categoryload = $(BlockClass + ' .category-filters li .filter-category-list').not('.all');

                                        $.each(Categoryload, function(key, value) {
                                            let span2 = $(value).find('span:nth-child(2)').data('hover'),
                                                Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;
                                                $(value).find('span:nth-child(1).all_post_count').html("").append(Toatal2);
                                        });
                                    }

                                    if (Number(Totalcount) >= Number(totalFeed)) {
                                        if(lazyFeed_click.next('.plus-all-posts-loaded').length == 0 ){
                                            $(lazyFeed_click).addClass("hide");
                                            $(lazyFeed_click).parent(".ajax_lazy_load").append('<div class="plus-all-posts-loaded">' + Allposttext + '</div>');
                                            $(window).unbind();
                                        }
                                    }else if( Totalcount >= Number(maximumposts) ){
                                        $(lazyFeed_click).addClass("hide");
                                        $(lazyFeed_click).parent(".ajax_lazy_load").append('<div class="plus-all-posts-loaded">' + Allposttext + '</div>');
                                        $(window).unbind();
                                        
                                        tp_maximum_posts( maximumposts );
                                    }else {
                                        $(lazyFeed_click).text(current_text);
                                    }
                                }
                                
                                i = Number(i) + Number(loadFview);
                            },
                            complete: function() {
                                tp_readbtn_click();
                                Resizelayout(loadlayout, 500);
                                EqualHeightlayout();
                                lazyFeed_click.data('requestRunning', false);
                            }
                        })
                    }
                });
            });
        }

        let loadmore_i = 0;
        function tp_feed_loadmore() {
            let $this = this,
                loadFeed_click = $(this),
                loadFeed = $this.dataset.loadattr,
                loadFview = $this.dataset.loadview,
                loadclass = $this.dataset.loadclass,
                loadlayout = $this.dataset.layout,
                loadloadingtxt = $this.dataset.loadingtxt,
                current_text = $this.innerHTML;

                if (loadFeed_click.data('requestRunning')) {  return;  }                
                    loadFeed_click.data('requestRunning', true);   

                $.ajax({
                    type: 'POST',
                    data: `action=tp_feed_load&view=${loadmore_i}&feedshow=${loadFview}&loadattr=${loadFeed}`,
                    url: theplus_ajax_url,
                    async: true,
                    beforeSend: function() {
                        $this.innerHTML = loadloadingtxt;
                    },
                    success: function(response) {
                        if (response == '') {
                            $this.classList.add('hide');
                            return;
                        }

                        if (response) {
                            let Data = response,
                                HtmlData = (Data && Data.HTMLContent) ? Data.HTMLContent : '',
                                totalFeed = (Data && Data.totalFeed) ? Data.totalFeed : '',
                                FilterStyle = (Data && Data.FilterStyle) ? Data.FilterStyle : '',
                                Allposttext = (Data && Data.allposttext) ? Data.allposttext : '',
                                maximumposts = (Data && Data.maximumposts) ? Data.maximumposts : '',
                                CategoryClass = container[0].querySelectorAll(".all .all_post_count"),
                                Getpost = container[0].querySelectorAll(".post-inner-loop");

                                if( Getpost.length > 0 ){
                                    Getpost[0].insertAdjacentHTML("beforeend", HtmlData);
                                }

                            let Totalcount = container[0].querySelectorAll(".grid-item").length;
                                if( CategoryClass.length > 0 ){
                                    CategoryClass[0].innerHTML = Number(Totalcount);
                                }

                                if (FilterStyle == 'style-2' || FilterStyle == 'style-3') {
                                    let BlockClass = '.' + loadclass,
                                        Categoryload = $(BlockClass + ' .category-filters li .filter-category-list').not('.all');

                                        $.each(Categoryload, function(key, value) {
                                            let span2 = $(value).find('span:nth-child(2)').data('hover'),
                                                Toatal2 = $(BlockClass).find('.grid-item.' + span2).length;
                                                $(value).find('span:nth-child(1).all_post_count').html("").append(Toatal2);
                                        });
                                }

                                if ( Number(Totalcount) >= Number(totalFeed) ) {
                                    $this.classList.add('hide');
                                    $this.parentElement.insertAdjacentHTML("beforeend", `<div class="plus-all-posts-loaded">${Allposttext}</div>`);
                                }else if( Totalcount >= Number(maximumposts) ){
                                    $this.classList.add('hide');
                                    $this.parentElement.insertAdjacentHTML("beforeend", `<div class="plus-all-posts-loaded">${Allposttext}</div>`);
                                    tp_maximum_posts( maximumposts );
                                }else {
                                    $this.innerHTML = current_text;
                                }

                            loadmore_i = Number(loadmore_i) + Number(loadFview);
                        }
                    },
                    complete: function() {
                        tp_readbtn_click();
                        Resizelayout(loadlayout, 500);
                        EqualHeightlayout();
                        loadFeed_click.data('requestRunning', false);
                    }
                })
        }

        function tp_massage_scroll() {
            let Getsscroll = $scope[0].querySelectorAll('.tp-social-feed .tp-message');
            if( Getsscroll.length > 0 ){
                Getsscroll.forEach(function(self){
                    let GetPerent = self.parentElement;
                    if( GetPerent && GetPerent.classList.contains('tp-fcb-contant') ){
                        if( Getscroll.FancyScroll ){
                            self.classList.add(Getscroll.Fancyclass);
                            self.style.cssText = `height: ${Getscroll.FancyHeight}px`;
                        }
                    }else{
                        if( Getscroll.ScrollOn ){
                            if (GetPerent.clientHeight >= Getscroll.Height) {
                                self.classList.add(Getscroll.className);
                                self.style.cssText = `height: ${Getscroll.Height}px`;
                            }
                        }
                    }
                });
            }
        }

        function tp_readbtn() {
            let $this = this,
                Getmessage = $this.closest('.tp-message'),
                Gettext = $this.closest('.showtext'),
                ShowMoreTxt = FeedData.TextMore,
                TextLessTxt = FeedData.TextLess;
            
                if ( Getmessage.classList.contains('show-text') ) {
                    Getmessage.classList.remove('show-text');
                    $this.innerHTML = ShowMoreTxt;

                    let GetDot = Getmessage.querySelectorAll('.sf-dots');
                    if( GetDot.length > 0 ){
                        GetDot[0].style.cssText = "display: inline";
                    }

                    if ( Getscroll.ScrollOn && Getscroll.TextLimit ) {
                        Gettext.classList.remove(Getscroll.className);
                        Gettext.removeAttribute('style');
                    }
                } else {
                    Getmessage.classList.add('show-text');
                    $this.innerHTML = TextLessTxt;
                    
                    let GetDot = Getmessage.querySelectorAll('.sf-dots');
                    if( GetDot.length > 0 ){
                        GetDot[0].style.cssText = "display: none";
                    }

                    if ( Getscroll.ScrollOn && Getscroll.TextLimit ) {
                        if ( Gettext.clientHeight >= Getscroll.Height ) {
                            Gettext.classList.add(Getscroll.className);
                            Gettext.style.cssText = `height: ${Getscroll.Height}px`;
                        }
                    }
                }

                Resizelayout('grid', 0)
        }

        function tp_fancybox_readbtn() {
            let $this = this,
                Getmsg = $this.closest('.tp-message'),
                GetDot = Getmsg.querySelectorAll('.sf-dots'),
                ShowMoreTxt = FeedData.TextMore,
                TextLessTxt = FeedData.TextLess;
              
                if ( Getmsg.classList.contains('show-text') ) {
                    Getmsg.classList.remove('show-text');
                    $this.innerHTML = ShowMoreTxt;

                    if( GetDot.length > 0 ){
                        GetDot[0].style.cssText = "display: inline";
                    }

                    if ( Getscroll.FancyScroll && Getscroll.TextLimit ) {
                        Getmsg.classList.remove(Getscroll.Fancyclass);
                        Getmsg.removeAttribute('style');
                    }
                } else {
                    Getmsg.classList.add('show-text');
                    $this.innerHTML = TextLessTxt;

                    if( GetDot.length > 0 ){
                        GetDot[0].style.cssText = "display: none";
                    }

                    if ( Getscroll.FancyScroll && Getscroll.TextLimit ) {          
                        if ( Getmsg.clientHeight >= Getscroll.FancyHeight) {
                            Getmsg.classList.add(Getscroll.Fancyclass);
                            Getmsg.style.cssText = `height: ${Getscroll.FancyHeight}px`;
                        }
                    }
                }
        }

        function tp_maximum_posts(Num) {
            let TPEnable = false;
            let FindlastGrid = container[0].querySelectorAll(`.grid-item:nth-child(${Num})`),
                AllGrid = container[0].querySelectorAll(".grid-item");

                if( AllGrid.length > 0 && FindlastGrid.length > 0 ){
                    AllGrid.forEach(function(item) {
                        if(TPEnable){
                            item.style.cssText = "display : none";
                            return;
                        }
                        if(FindlastGrid[0] == item){
                            TPEnable = true;
                        }
                    });
                }
        }

        function Resizelayout( loadlayout, duration=500 ) {
            if (loadlayout == 'grid' || loadlayout == 'masonry') {
                let FindGrid = container[0].querySelectorAll(`.list-isotope .post-inner-loop`);
                if( FindGrid.length ){
                    setTimeout(function(){
                        $(FindGrid[0]).isotope('reloadItems').isotope();
                    }, duration);
                }
            }
        }

        /**compatibility with Equal Height*/
        function EqualHeightlayout() {
			var Equalcontainer = jQuery('.elementor-element[data-tp-equal-height-loadded]');
			if( Equalcontainer.length > 0 ){
				EqualHeightsLoadded();
			}
		}

        if( elementorFrontend.isEditMode() ){
            Resizelayout('grid', 4000)

            $(".tp-social-feed .tp-sf-copy-icon").unbind().click(function() {
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
        }
    };

    window.addEventListener('elementor/frontend/init', (event) => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-social-feed.default', WidgetSocialFeedHandler);
    });
})(jQuery);