var ba_Container;
var ba_ContainerId = 0;
var ba_obj;
var ba_sep_obj;
var ba_sep_Image;
var before_obj;
var after_obj;
var beforeImage;
var afterImage;
var ba_type;
var ba_sep_show;
var ba_show_mode;
var changing_this = !1;
var Playing_this = !1;
var sep_Size;
var indSize = 10;
var fpsPlay = 60;
var TouchDevice = !1;

function setba_Container(objFrom) {
    container = jQuery(objFrom).closest(".pt_plus_before_after");
    containerId = container.data("id");
    hide_separator_image(container);
    if (ba_Container && ba_ContainerId == containerId) return;
    if (Playing_this) {
        stop_animation();
        if (ba_sep_show) ba_sep_obj.show()
    }
    ba_Container = container;
    ba_ContainerId = containerId;
    ba_sep_obj = ba_Container.find(".before-after-sep");
    ba_obj = ba_Container.find(".before-after-inner");
    before_obj = ba_Container.find(".image-before");
    after_obj = ba_Container.find(".image-after");
    beforeImage = ba_Container.find(".image-before > img");
    afterImage = ba_Container.find(".image-after > img");
    ba_type = ba_Container.data("type");
    ba_sep_show = !0;
    if (ba_Container.data("separate_switch") == 'false')
        ba_sep_show = !1;
    if (ba_sep_show == !0) ba_sep_obj.show();
    else ba_sep_obj.hide()
}

function play_animation(curPx, deltaPx, x, y, sizePx, frameDelay) {
    if (!Playing_this) return;
    curPx += deltaPx;
    if (ba_type == "vertical")
        onMouseMove(x, y + curPx);
    else onMouseMove(x + curPx, y);
    if (curPx <= sizePx + 1) setTimeout(play_animation, frameDelay, curPx, deltaPx, x, y, sizePx, frameDelay);
    else stop_animation()
}

function stop_animation() {
    if (!Playing_this) return;
    Playing_this = !1;
    changing_this = !0;
    ba_sep_obj.hide()
}

function onMouseMove(x, y,parent_attr) {
    if (changing_this && ba_sep_show) ba_sep_obj.show();
    if (position_changing(x, y)) return;
	var container_class=jQuery("."+parent_attr);
	sep_Size=container_class.data("separate_width");
    if (ba_type == "horizontal") {
	var Id = jQuery("."+containerId).offset();
		var abc = Id.left;
        //pos = x - ba_Container.offset().left;
        pos = x - abc;
		
        if (pos >= ba_obj.width()) pos = ba_obj.width();
		
	//ba_sep_obj.css("left", pos - (sep_Size / 2));
        jQuery("."+containerId+" .before-after-sep").css("left", pos - (sep_Size / 2));
        if (ba_show_mode != 0) {
            ba_sep_Image.css("left", pos)
        }
        //before_obj.width(pos);
        jQuery("."+containerId+" .before-after-image.image-before").width(pos);
		var before_label=before_obj.find('.before_after_label.before_label_text');
		if((before_label.width()+50) < pos){
			before_obj.find('.before_after_label.before_label_text').css("opacity",'1');		
		}else{
			before_obj.find('.before_after_label.before_label_text').css("opacity",'0');
		}
    } else if (ba_type == "vertical") {
		var Id = jQuery("."+containerId).offset();
		var abc = Id.top;
		 //pos = y - ba_Container.offset().top;
         pos = y - abc;
		
        if (pos >= ba_obj.height()) pos = ba_obj.height();
		
		//ba_sep_obj.css("top", pos - (sep_Size / 2));        
		jQuery("."+containerId+" .before-after-sep").css("top", pos - (sep_Size / 2));
        if (ba_show_mode != 0) {
            ba_sep_Image.css("top", pos)
        }
		
        //before_obj.height(pos);
		//var before_label=before_obj.find('.before_after_label.before_label_text');
		//var bf_obj=ba_obj.height();
		//bf_obj=bf_obj/2;
		 jQuery("."+containerId+" .before-after-image.image-before").height(pos);
		var before_label=before_obj.find('.before_after_label.before_label_text');		
		if((before_label.height()+50) < pos){
			before_obj.find('.before_after_label.before_label_text').css("opacity",'1');		
		}else{
			before_obj.find('.before_after_label.before_label_text').css("opacity",'0');
		}
    } else if (ba_type == "cursor") {
		pos = x - ba_Container.offset().left;
        if (pos >= ba_obj.width()) pos = ba_obj.width();
        ba_sep_obj.css("left", pos - (indSize / 2));
        rat = pos / ba_obj.width();
        beforeImage.css("opacity", 1 - rat);
		var before_label=before_obj.find('.before_after_label.before_label_text');
		if((1 - rat) > 0.3 ){
			before_obj.find('.before_after_label.before_label_text').css("opacity",'1');		
		}else{
			before_obj.find('.before_after_label.before_label_text').css("opacity",'0');
		}
    }
}

function size_Elements() {
    jQuery(".before-after-image > img").imgLoad(function() {
        img = jQuery(this);
        container = img.closest(".pt_plus_before_after");
		container=container.data("id");
		container=jQuery("."+container);
        style = container.data("type");
        if (style == "show") return;
        img.css("min-width", "none");
        img.css("max-width", "none");
        sbsShrinked = !1;
        if (container.data("responsive") == "yes") {
            p = container.parent();
            parentWidth = p.width();
            while (!p || parentWidth == 0) {
                p = p.parent();
                parentWidth = p.width()
            }
            if ((!img.css("max-width") || img.css("max-width") == "none") && container.data("full_width") == "yes") {
                img.css("max-width", parentWidth)
                
            }else if(!img.css("max-width") || img.css("max-width") == "none"){
				img.css("max-width", img.width())
			}
			if(container.data("full_width") == "yes"){
				img.css("width", parentWidth)
			}else{
				img.css("width", img.width())
			}
        }
        if (container.data("width")) {
            img.css("width", container.data("width"))
        }
        if (container.data("max-width")) {
            img.css("max-width", container.data("max-width"))
        }
        initRatio = container.data("separate_position") / 100;
        if (img.hasClass('image-before-wrap')) {
            container.css("visibility", "visible");
            width = img.width();
            height = img.height();
            container.find(".before-after-image").width("auto");
            container.find(".before-after-inner").width(width);
            container.find(".before-after-inner").height(height);
            container.find(".before-after-bottom-separate").width(width);
            container.width(width);
            if (style == "horizontal")
                container.find(".image-before").width(img.width() * initRatio);
            else if (style == "vertical")
                container.find(".image-before").height(img.height() * initRatio);
            separator = container.find(".before-after-sep");
            if (style == "horizontal") {
                if (container.data("separator_style") == 'middle') {
					sep_Size = container.data("separate_width");
                    separator.width(sep_Size);
                    separator.height(img.height());
                    sp = container.find(".image-before").width() - sep_Size / 2;
                    separator.css("left", sp);
                    separator.css("cursor", "ew-resize")
                } else {
                    sep_Size = container.data("separate_width");
                    separator.height(sep_Size);
                    separator.width(15);
                    separator.css("left", (img.width() * initRatio) + 'px');
                    var h = container.find(".image-before").height();
                    separator.css("top", h);
                    separator.css("cursor", "ew-resize");
                    container.find(".before-after-bottom-separate").height(sep_Size);
                    container.find(".before-after-bottom-separate").show()
                }
            } else if (style == "vertical") {
                if (container.data("separator_style") == 'middle') {
					sep_Size = container.data("separate_width");
                    separator.height(sep_Size);
                    separator.width(img.width());
                    sp = container.find(".image-before").height() - sep_Size / 2;
                    separator.css("top", sp);
                    separator.css("cursor", "ns-resize")
                }
            } else if (style == "cursor") {
                if (container.data("separate_switch") == !0) {
                    sep_Size = container.data("separate_width");
                    var h = container.find(".image-before").height();
                    container.find(".before-after-bottom-separate").show()
                }
            }
        }
    })
}

function hide_separator_image(container) {
    ba_sep_Image = container.find(".before-after-sep-icon");
    ba_show_mode = 0;
    if (container.data("separate_image"))
        ba_show_mode = container.data("separate_image")
}

function show_separator_image() {
    if (ba_show_mode == 1) {
        ba_sep_Image.show()
    }
}

function full_After() {
    w = ba_obj.width() - indSize;
    ba_sep_obj.css("left", w);
    beforeImage.css("opacity", "0")
}

function zero_After() {
    ba_sep_obj.css("left", 0);
    beforeImage.css("opacity", "1")
}

function position_changing(pageX, pageY) {
    if (!(changing_this || Playing_this)) return !1;
    if (Playing_this) return !1;
    aligned = !1;
    if (ba_type == "horizontal") {
		//alert(ba_obj.width());
		var Id = jQuery("."+containerId).offset();
		var abc = Id.left;
        //if (pageX >= ba_obj.width() + ba_Container.offset().left) {
        if (pageX >= ba_obj.width() + abc) {
            sep_Right();
            aligned = !0
        } else if (pageX <= abc) {
            sep_Left();
            aligned = !0
        }
    } else if (ba_type == "vertical") {
		var Id = jQuery("."+containerId).offset();
		var abc = Id.top;
        if (pageY >= ba_obj.height() + abc) {
            sep_Bottom();
            aligned = !0
        } else if (pageY <= abc) {
            sep_Top();
            aligned = !0
        }
    } else if (ba_type == "cursor") {
		 if (pageX + indSize / 2 >= ba_obj.width() + ba_Container.offset().left) {
            full_After();
            aligned = !0
        } else if (pageX - indSize / 2 <= ba_Container.offset().left) {
            zero_After();
            aligned = !0
        }
    }
    if (aligned && ba_type != "cursor") {
        if (!Playing_this)
            ba_sep_obj.hide()
    }
    return aligned
}(function($) {
    $.fn.imgLoad = function(callback) {
        return this.each(function() {
            if (callback) {
                if (this.complete) {
                    callback.apply(this)
                } else {
                    $(this).on('load', function() {
                        callback.apply(this)
                    })
                }
            }
        })
    }
})(jQuery);

function sep_Right() {
    w = ba_obj.width();
    ba_sep_obj.css("left", w - sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("left", w)
    }
    before_obj.width(w)
}

function sep_Left() {
    ba_sep_obj.css("left", 0);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("left", 0)
    }
    before_obj.width(0)
}

function sep_Top() {
    ba_sep_obj.css("top", -sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("top", 0)
    }
    before_obj.height(0)
}

function sep_Bottom() {
    h = ba_obj.height();
    ba_sep_obj.css("top", h - sep_Size / 2);
    if (ba_show_mode != 0) {
        ba_sep_Image.css("top", h)
    }
    before_obj.height(h)
}

function ba_init() {
    jQuery('.pt_plus_before_after').on('dragstart', function(event) {
        event.preventDefault()
    });
    contId = 0;
    jQuery(".pt_plus_before_after").each(function() {
        var $container = jQuery(this);
		container= $container.data("id");
		var container= jQuery('.'+container);
        container.css("visibility", "hidden");
        contId++;
        container.attr("data-before-after-id", contId);
        configType = container.data("type");
        sep_Size = container.data("separate_width");
        indSize = container.data("bottom-separator-size");
        show = container.data("show");
        if (!show) return;
        else if (configType == "cursor") {
            initRatio = container.data("separate_position");
            container.find(".before-after-image").css("position", "absolute");
            container.find(".before-after-sep-icon").css("left", "" + initRatio + "%");
            container.find(".before-after-image").css("position", "absolute");
            container.find(".before-after-sep-icon").show();
            container.find(".before-after-inner").on("mouseout", function(event) {
                show_separator_image();
                position_changing(event.pageX, event.pageY)
            })
        } else {
            initRatio = container.data("separate_position");
            if (configType == "horizontal") {
                container.find(".before-after-sep-icon").css("left", "" + initRatio + "%")
            } else if (configType == "vertical") {
                container.find(".before-after-sep-icon").css("top", "" + initRatio + "%")
            }
            container.find(".before-after-image").css("position", "absolute");
            container.find(".before-after-sep-icon").show();
            container.find(".before-after-inner").on("mouseout", function(event) {
                show_separator_image();
                position_changing(event.pageX, event.pageY)
            })
        }
        if (container.data("separate_switch") == 'false') {
            container.find(".before-after-bottom-separate").css("display", "none");
            container.find(".before-after-sep").css("display", "none");
            container.find(".before-after-sep-icon").css("display", "none")
        }
        if (configType != "show") {
            container.on("touchstart", function(event) {
                setba_Container(this);
                TouchDevice = !0;
                changing_this = !0
            });
            container.on("touchend", function() {
                sep = jQuery(".pt_plus_before_after").find(".before-after-sep");
                changing_this = !1;
                show_separator_image()
            });
            container.on("touchcancel", function() {
                sep = jQuery(".pt_plus_before_after").find(".before-after-sep");
                changing_this = !1;
                show_separator_image()
            })
        }
        if (container.data("click_hover_move") == 'on') {
            container.find(".before-after-inner, .before-after-bottom-separate").on("mouseover", function(event) {
                setba_Container(this);
                if (Playing_this) return;
                changing_this = !0;
                if (ba_type == "horizontal") {
                    pos = event.pageX - ba_Container.offset().left;
                    jQuery(this).find(".separator").css("left", pos - (sep_Size / 2));
                    jQuery(this).find(".image-before").width(pos);
                    container.find(".before-after-inner").css("cursor", "ew-resize")
                } else if (ba_type == "vertical") {
                    pos = event.pageY - ba_Container.offset().top - (sep_Size / 2);
                    jQuery(this).find(".separator").css("top", pos);
                    jQuery(this).find(".image-before").height(pos);
                    container.find(".before-after-inner").css("cursor", "ns-resize")
                } else if (ba_type == "cursor") {
                    pos = event.pageX - ba_Container.offset().left;
                    jQuery(this).find(".separator").css("left", pos - (indSize / 2))
                }
            });
            container.find(".before-after-inner, .before-after-bottom-separate").on("mouseout", function(event) {
                position_changing(event.pageX, event.pageY);
                changing_this = !1
            })
        } else {
            container.find(".before-after-inner, .before-after-bottom-separate").on("mousedown", function(event) {
                if (TouchDevice) return;
                if (ba_sep_obj && ba_sep_show) ba_sep_obj.show();
                setba_Container(this);
				var parent_class=jQuery(this).parent(".pt_plus_before_after");
		var parent_attr=parent_class.data("id");
                onMouseMove(event.pageX, event.pageY,parent_attr)
            });
            container.find(".before-after-inner, .before-after-bottom-separate").on("mouseenter", function(event) {
                if (ba_sep_obj && ba_sep_show) ba_sep_obj.show();
                setba_Container(this);
                if (!event.which) {
                    changing_this = !1;
                    return
                }
            });
            container.find(".before-after-sep,.before-after-sep-icon").on("mousedown", function(event) {
                if (TouchDevice) return;
                setba_Container(this);
                changing_this = !0
            });
            container.find(".before-after-sep,.before-after-sep-icon").on("mouseover", function(event) {
                setba_Container(this)
            });
            container.find(".before-after-sep,.before-after-sep-icon").on("mouseup", function() {
                changing_this = !1
            });
            container.find(".before-after-inner, .before-after-bottom-separate").on("mouseup", function() {
                changing_this = !1
            })
        }
        container.find(".before-after-inner").on("mousedown", function(event) {
            if (TouchDevice) return;
            setba_Container(this);
            stop_animation()
        })
    });
    size_Elements();
	
    jQuery(".before-after-inner").on("mousemove", function(event) {
		var parent_class=jQuery(this).parent(".pt_plus_before_after");
		var parent_attr=parent_class.data("id");
        if (changing_this && !Playing_this) onMouseMove(event.pageX, event.pageY,parent_attr)
    });
    jQuery(".before-after-bottom-separate").on("mousemove", function(event) {
	var parent_class=jQuery(this).parent(".pt_plus_before_after");
		var parent_attr=parent_class.data("id");
        if (changing_this && !Playing_this) onMouseMove(event.pageX, event.pageY,parent_attr)
    });
    jQuery(".before-after-inner").on("touchmove", function(event) {
        event.preventDefault();
        touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
		var parent_class=jQuery(this).parent(".pt_plus_before_after");
		var parent_attr=parent_class.data("id");
        if (changing_this && !Playing_this) onMouseMove(touch.pageX, touch.pageY,parent_attr)
    });
    jQuery(".before-after-bottom-separate").on("touchmove", function(event) {
        event.preventDefault();
        touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
		var parent_class=jQuery(this).parent(".pt_plus_before_after");
		var parent_attr=parent_class.data("id");
        if (changing_this && !Playing_this) onMouseMove(touch.pageX, touch.pageY,parent_attr)
    });
    jQuery(window).on('resize', function() {
        size_Elements()
    })
}

(function($) {
	"use strict";
    var WidgetBeforeAfterHandler = function($scope, $) {
        jQuery(document).ready(function() {
            ba_init()
        })
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-before-after.default', WidgetBeforeAfterHandler);
        if (elementorFrontend.isEditMode()) {
            elementorFrontend.hooks.addAction('frontend/element_ready/tp-before-after.default', WidgetBeforeAfterHandler);
        }
    });
})(jQuery);