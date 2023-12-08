/*OffCanvas*/
;(function() {
	 PlusOffcanvas = function( a ) {
    "use strict";
		(this.node = a),
        (this.wrap = a.find(".plus-offcanvas-wrapper")),
        (this.content = a.find(".plus-canvas-content-wrap")),
        (this.button = a.find(".offcanvas-toggle-btn")),
        (this.settings = this.wrap.data("settings")),
        (this.id = this.settings.content_id),
        (this.transition = this.settings.transition),
        (this.esc_close = this.settings.esc_close),
        (this.body_click_close = this.settings.body_click_close),
        (this.direction = this.settings.direction),
		(this.trigger = this.settings.trigger),
		(this.tpageload = this.settings.tpageload),
		(this.tscroll = this.settings.tscroll),
		(this.texit = this.settings.texit),
		(this.tinactivity = this.settings.tinactivity),
		(this.tpageviews = this.settings.tpageviews),
		(this.tprevurl = this.settings.tprevurl),
		(this.textraclick = this.settings.textraclick),		
		(this.scrollHeight = this.settings.scrollHeight),
		(this.previousUrl = this.settings.previousUrl),
		(this.extraId = this.settings.extraId),
		(this.extraIdClose = this.settings.extraIdClose),
		(this.inactivitySec = this.settings.inactivitySec),
        (this.duration = 500),
		(this.time = 0),
		(this.flag = true),
        this.destroy(),
        this.init();
};
PlusOffcanvas.prototype = {
    id: "",
    node: "",
    wrap: "",
    content: "",
    button: "",
    settings: {},
    transition: "",
    delaytimeout: "",
    duration: 400,
    initialized: !1,
    animations: ["slide", "slide-along", "reveal", "push", "popup"],
    init: function () {
        this.wrap.length &&
            (jQuery("html").addClass("plus-offcanvas-content-widget"),
            0 === jQuery(".plus-offcanvas-container").length && (jQuery("body").wrapInner('<div class="plus-offcanvas-container" />'), this.content.insertBefore(".plus-offcanvas-container")),
            0 < this.wrap.find(".plus-canvas-content-wrap").length &&
                (0 < jQuery(".plus-offcanvas-container > .plus-" + this.id).length && jQuery(".plus-offcanvas-container > .plus-" + this.id).remove(),
                0 < jQuery("body > .plus-" + this.id).length && jQuery("body > .plus-" + this.id).remove(),
                jQuery("body").prepend(this.wrap.find(".plus-canvas-content-wrap"))),
            this.bindEvents());
    },
    destroy: function () {
        this.close(),
            this.animations.forEach(function (b) {
                jQuery("html").hasClass("plus-" + b) && jQuery("html").removeClass("plus-" + b);
            }),
            jQuery("body > .plus-" + this.id).length;
    },
    bindEvents: function () {		
		(this.trigger && this.trigger == 'yes') && this.button.on("click", jQuery.proxy(this.toggleContent, this)),
        ((this.textraclick && this.textraclick == 'yes') && this.extraId && this.extraId != '' && this.triggerClick()),
        ((this.textraclick && this.textraclick == 'yes') && this.extraIdClose && this.extraIdClose != '' && this.triggerECClick()),
        ((this.tpageload && this.tpageload == 'yes') || (this.tinactivity && this.tinactivity == 'yes') || (this.tprevurl && this.tprevurl == 'yes') ) && this.loadShow(),
        jQuery(window).on("scroll", jQuery.proxy(this.scrollShow, this)),
        jQuery(document).on("mouseleave", jQuery.proxy(this.exitInlet, this)),
        jQuery("body").delegate(".plus-canvas-content-wrap .plus-offcanvas-close", "click", jQuery.proxy(this.close, this)),
        "yes" === this.esc_close && this.closeESC(),
        "yes" === this.body_click_close && this.closeClick();
    },
	triggerClick: function () {
        if((this.textraclick && this.textraclick == 'yes') && this.extraId && this.extraId != '' && this.flag) {
            jQuery('.'+this.extraId).on("click", jQuery.proxy(this.toggleContent, this));
        }
    },
	triggerECClick: function () {
        if((this.textraclick && this.textraclick == 'yes') && this.extraIdClose && this.extraIdClose != '' && this.flag) {
            jQuery('.'+this.extraIdClose).on("click", jQuery.proxy(this.toggleContent, this));
        }
    },
    toggleContent: function (e) {
		e.preventDefault();
        jQuery("html").hasClass("plus-open") ? this.close() : this.show();
    },
	exitInlet: function () {
        ((this.texit && this.texit == 'yes') && this.flag) ? (this.show(), this.flag = false) : "";
    },
	 loadShow: function () {
        if((this.tpageload && this.tpageload == 'yes')  && this.flag) {
            setTimeout(() => {
                this.show(), this.flag = false
            }, 500);
        }
        if((this.tinactivity && this.tinactivity == 'yes') && this.flag && this.inactivitySec && this.inactivitySec != '') {
            var timeout;
            if(this.flag) {
                function resetTimer(el) {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        el.show(); el.flag = false;
                    }, el.inactivitySec);
                }
            }
            document.onmousemove = resetTimer(this);
            document.onkeypress = resetTimer(this);
        }
        if(((this.tprevurl && this.tprevurl == 'yes') && this.previousUrl && document.referrer) && this.previousUrl == document.referrer && this.flag) {
            setTimeout(() => {
                this.show();
            }, 500);
        }
    },
	scrollShow: function () {
        var scrollHeight = this.scrollHeight;
        var scroll = jQuery(window).scrollTop();
        ((this.tscroll && this.tscroll == 'yes') && this.flag && (scroll >= scrollHeight)) ? (this.show(), this.flag = false) : "";
    },
    show: function () {
        jQuery(".plus-" + this.id).addClass("plus-visible"),
            jQuery("html").addClass("plus-" + this.transition),
            jQuery("html").addClass("plus-" + this.direction),
            jQuery("html").addClass("plus-open"),
            jQuery("html").addClass("plus-" + this.id + "-open"),
            jQuery("html").addClass("plus-reset"),
            this.button.addClass("plus-is-active");
    },
    close: function () {
        jQuery(".plus-" + this.id).hasClass("plus-slide-along") ? ((this.delaytimeout = 0), jQuery(".plus-" + this.id).removeClass("plus-visible")) : (this.delaytimeout = 500),
            jQuery("html").removeClass("plus-open"),
            jQuery("html").removeClass("plus-" + this.id + "-open"),
            setTimeout(
                jQuery.proxy(function () {
                    jQuery("html").removeClass("plus-reset"),
                        jQuery("html").removeClass("plus-" + this.transition),
                        jQuery("html").removeClass("plus-" + this.direction),
                        jQuery(".plus-" + this.id).hasClass("plus-slide-along") || jQuery(".plus-" + this.id).removeClass("plus-visible");
                }, this),
                this.delaytimeout
            ),
            this.button.removeClass("plus-is-active");
    },
    closeESC: function () {
        var a = this;
        "" !== a.settings.esc_close &&
            jQuery(document).on("keydown", function (c) {
                27 === c.keyCode && a.close();
            });
    },
    closeClick: function () {        
		
		 var c = this;
		 var exccl = '';
		if((this.textraclick && this.textraclick == 'yes') && this.extraId && this.extraId != '' && this.flag) {
			exccl = '.'+this.extraId;
		}else{
			exccl ='.offcanvas-toggle-btn';
		}
        jQuery(document).on("click", function (a) {
            jQuery(a.target).is(".plus-canvas-content-wrap") ||
                0 < jQuery(a.target).parents(".plus-canvas-content-wrap").length ||
                jQuery(a.target).is(".offcanvas-toggle-btn") ||
                0 < jQuery(a.target).parents(".offcanvas-toggle-btn").length ||
				jQuery(a.target).is(exccl) || jQuery(a.target).parents(exccl).length > 0 ||
                c.close();
        });
    },
};
})(jQuery);
(function (b) {
        var c = function (c, d) {
			var offcanvas_con = c.find('.plus-offcanvas-wrapper'); 
			if(offcanvas_con.length > 0){
				var setting = offcanvas_con.data("settings"),
					namePageView = 'pageViewsCount-'+setting.content_id,
					nameXTimeView = 'pageXTimeView-'+setting.content_id,
					spflag = true;
				if(setting.tpageviews!=undefined && setting.tpageviews == 'yes' && setting.tpageviewscount!=undefined && setting.tpageviewscount!=''){
					var getItemPageView = localStorage.getItem(namePageView);
					if (getItemPageView){
						var value = Number(getItemPageView) + 1;
						localStorage.setItem(namePageView, value);
					}else{
						localStorage.setItem(namePageView, 1);
					}
					
					if(Number(localStorage.getItem(namePageView)) >= Number(setting.tpageviewscount)){
						spflag = true;
					}else{	
						spflag = false
					}
				}else{
					if(localStorage.getItem(namePageView)){
						localStorage.removeItem(namePageView);
					}
				}
				if(setting.sr!=undefined && setting.sr == 'yes' && setting.srxtime!='' && setting.srxdays!=''){
					var getItemPageView = localStorage.getItem(nameXTimeView);
					getItemPageView = jQuery.parseJSON(getItemPageView);
					if (getItemPageView!=undefined && getItemPageView.xtimeView!=undefined){
						var value = Number(getItemPageView.xtimeView) + 1;
						localStorage.setItem(nameXTimeView,  JSON.stringify(Object.assign({}, getItemPageView, {"xtimeView" : value })));
					}else{
						localStorage.setItem(nameXTimeView, '{ "xtimeView": 1 }');
					}
					
					if(Number(jQuery.parseJSON(localStorage.getItem(nameXTimeView)).xtimeView) <= Number(setting.srxtime)){
						spflag = true;
					}else{
						var cdate = new Date();
						var endDate = new Date();
						var expired_date = endDate.setDate(cdate.getDate()+ Number(setting.srxdays));						
						var getItemPageView = localStorage.getItem(nameXTimeView);
						getItemPageView = jQuery.parseJSON(getItemPageView);
						
						var store_date = Object.assign({}, getItemPageView, {"Xdate" : expired_date});
						if(getItemPageView!=undefined && getItemPageView.Xdate==undefined){
							localStorage.setItem(nameXTimeView, JSON.stringify(store_date));
						}
						
						spflag = false
						
						var getData = localStorage.getItem(nameXTimeView);
						getData = jQuery.parseJSON(getData);
						
						if(getData!=undefined && getData.Xdate!=undefined && (new Date(Number(cdate)) > new Date(Number(getData.Xdate)))){
							localStorage.removeItem(nameXTimeView);
							spflag = true;
						}
					}
				}else{
					if(localStorage.getItem(nameXTimeView)){
						localStorage.removeItem(nameXTimeView);
					}
				}
				
				if(spflag){
					offcanvas_con.removeAttr("style");
					 new PlusOffcanvas(c);
				}else{
					offcanvas_con.html('');
					return false;
				}
			}
            var e = c.find(".plus-offcanvas-wrapper.scroll-view"),
                b = c.find(".offcanvas-toggle-btn.position-fixed");
            0 < e.length &&
                b &&
                d(window).on("scroll", function () {
                    var f = d(this).scrollTop();
                    e.each(function () {
                        var e = d(this).data("scroll-view"),
                            a = d(this).data("canvas-id"),
                            b = d("." + a);
                        f > e ? b.addClass("show") : b.removeClass("show");
                    });
                });
        };
        b(window).on("elementor/frontend/init", function () {
            elementorFrontend.hooks.addAction("frontend/element_ready/tp-off-canvas.default", c);
        });
    })(jQuery);