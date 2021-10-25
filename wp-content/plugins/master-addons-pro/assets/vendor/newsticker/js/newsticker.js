/*!
 * Newsticker
 */

(function($) {

    $.fn.breakingNews = function(params){
        var defaults={
            speed:    20,
            width     :'100%',
            modul     :'breakingnews',
            color     :'default',
            border      :false,
            effect      :'fade',
            fontstyle   :'normal',
            autoplay    :false,
            timer     :2000,
            feed      :false,
            feedlabels    :false
        };
        var feeds=[];
        var labels=[];
        var params=$.extend(defaults,params);

        return this.each(function(){
            //Variables------------------------------------
            params.modul=$("#"+$(this).attr("id"));
            var timername=params.modul;
            var active=0;
            var previous=0;
            var count=params.modul.find("ul li").length;
            var changestate=true;

            if (params.feed=false) {
                // getRSS();
                params.modul.find("ul li").eq(active).fadeIn();
            }

            resizeEvent();

            if (params.autoplay) {
                timername=setInterval(function(){autoPlay()},params.timer);
                $(params.modul).on("mouseenter",function (){
                    clearInterval(timername);
                });

                $(params.modul).on("mouseleave",function (){
                    timername=setInterval(function(){autoPlay()},params.timer);
                });
            } else{
                clearInterval(timername);
            }

            if (!params.border){
                params.modul.addClass("bn-bordernone");
            }

            if (params.fontstyle=="italic")
                params.modul.addClass("bn-italic");

            if (params.fontstyle=="bold")
                params.modul.addClass("bn-bold");

            if (params.fontstyle=="bold-italic")
                params.modul.addClass("bn-bold bn-italic");

            params.modul.addClass("bn-"+params.color);

            //Events---------------------------------------
            $(window).on("resize",function (){
                resizeEvent();
            });

            params.modul.find(".ma-el-ticker-nav span").on("click",function(){
                if (changestate){
                    changestate=false;
                    if ($(this).index()==0){
                        active--;
                        if (active<0)
                            active=count-1;

                        changeNews();
                    } else {
                        active++;
                        if (active==count)
                            active=0;

                        changeNews();
                    }
                }
            });

            //functions------------------------------------
            function resizeEvent() {
                if (params.modul.width()<480){
                    params.modul.find(".ma-el-ticker-heading span").css({"display":"none"});
                    params.modul.find(".ma-el-ticker-heading").css({"width":10});
                    params.modul.find("ul").css({"left":30});
                } else {
                    params.modul.find(".ma-el-ticker-heading span").css({"display":"inline-block"});
                    params.modul.find(".ma-el-ticker-heading").css({"width":"auto"});
                    params.modul.find("ul").css({"left":$(params.modul).find(".ma-el-ticker-heading").width()+30});
                }
            }

            function autoPlay() {
                active++;
                if (active==count)
                    active=0;

                changeNews();
            }

            function changeNews() {
                if (params.effect=="fade") {
                    params.modul.find("ul li").css({"display":"none"});
                    params.modul.find("ul li").eq(active).fadeIn("normal",function (){
                        changestate=true;
                    });
                } else if (params.effect=="scroll-h") {

                }else if (params.effect=="slide-h") {
                    params.modul.find("ul li").eq(previous).animate({width:0},function(){
                        $(this).css({"display":"none","width":"100%"});
                        params.modul.find("ul li").eq(active).css({"width":0,"display":"block","opacity":"1"});
                        params.modul.find("ul li").eq(active).animate({width:"100%"},function(){
                            changestate=true;
                            previous=active;
                        });
                    });
                } else if (params.effect=="slide-v") {
                    if (previous<=active) {
                        params.modul.find("ul li").eq(previous).animate({top:-100});
                        params.modul.find("ul li").eq(active).css({top:100,"display":"block","opacity":"1"});
                        params.modul.find("ul li").eq(active).animate({top:0},function(){
                            previous=active;
                            changestate=true;
                        })
                    } else {
                        params.modul.find("ul li").eq(previous).animate({top:100});
                        params.modul.find("ul li").eq(active).css({top:-100,"display":"block","opacity":"1"});
                        params.modul.find("ul li").eq(active).css({}).animate({top:0},function(){
                            previous=active;
                            changestate=true;
                        })
                    }
                }
            }



        });


    };
    })(jQuery);