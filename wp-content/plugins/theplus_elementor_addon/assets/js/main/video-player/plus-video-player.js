!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p>"}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});

function initFluidVids(){
	"use strict";
	fluidvids.init({ selector: ['iframe:not(.pt-plus-bg-video)'],players: ['www.youtube.com', 'player.vimeo.com']});	
}
( function( $ ) {
	'use strict';
	if($('iframe').length){
		$(document).ready(function(){
			initFluidVids();
		});
		$('body').on('post-load', initFluidVids);
	}
})(jQuery);

! function(e) {
    "use strict";
	
    function t(t) {
        var a = t.find("video"),
		n = t.find(".ts-video-lazyload");
        if (t.is("[data-grow]") && t.css("max-width", "none"), t.find(".ts-video-title, .ts-video-description, .ts-video-play-btn, .ts-video-thumbnail").addClass("ts-video-hidden"), n.length) {
            var i = n.data();
            e("<iframe></iframe>").attr(i).insertAfter(n)
		}
        a.length && a.get(0).play()
	}
	
    function a() {
        e(".ts-video-wrapper[data-inview-lazyload]").one("inview", function(a, n) {
            n && t(e(this))
		})
	}
    e(document).on("click", '[data-mode="lazyload"] .ts-video-play-btn', function(a) {
        a.preventDefault(), t(e(this).closest(".ts-video-wrapper"))
		}), a(), e(document).ajaxComplete(function() {
        a()
		}), e(document).on("lity:open", function() {
        
		}), e(document).on("lity:ready", function(t, a) {
        var n = a.element(),
		i = n.find("video"),
		r = n.find(".ts-video-lazyload");
        if (e(".lity-wrap").attr("id", "ts-video"), r.length) e("<iframe></iframe>").attr(r.data()).insertAfter(r);
        i.length && i.get(0).play()
		}), e(document).on("lity:close", function(t, a) {
        a.element().find("video").length && a.element().find("video").get(0).pause(), e(".ts-video-lity-container .pt-plus-video-frame").remove(), e("[data-hidden-fixed]").removeClass("ts-video-hidden")
		}), e(document).ready(function() {
        e(".ts-video-lightbox-link").off()
	})
}(jQuery);