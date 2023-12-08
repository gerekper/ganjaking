document.addEventListener("DOMContentLoaded", function() {
    var e = !1
      , t = document.querySelectorAll(".plus_row_scroll");
	 var sec_offset_left=0;
	 var mob_off_disable='';
    Array.prototype.forEach.call(t, function(t) {
			e = !0;
			var r = t;
			
			r.classList.add("plus_row_scroll_parent"),			
			"true" === t.getAttribute("data-body-overflow") && document.querySelector("body").classList.add("plus_row_scroll_overflow");
			if(t.getElementsByClassName("elementor-section-stretched")){
				sec_offset_left=t.style.left;
			}
			var mob_off= t.getAttribute("data-row-mobile-off");
			if(mob_off!='' && mob_off=='true'){
				mob_off_disable ='disable';
			}		
    });
	if(t.length && (mob_off_disable!='disable' || !navigator.userAgent.match(/(Mobi|Android|iPhone)/))){
		e && setTimeout(function() {
		   skrollr.init({
				forceHeight: !1,
				smoothScrolling:true,
				mobileDeceleration:0.009,
				mobileCheck: function(){
					if((/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera)){
						if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container-fluid")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".container-fluid").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-inner")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-inner").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-section-wrap")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest(".elementor-section-wrap").attr('id', 'skrollr-body');
						}else if(jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest("body")){
							jQuery(".plus_row_scroll_parent.elementor-section-stretched").closest("body").attr('id', 'skrollr-body');
						}
					}
				}
			})
		}, 1e3);
	}
    window.refreshSkrollables = function() {
        if (t.length && document.querySelectorAll(".skrollable") && (mob_off_disable!='disable' || !navigator.userAgent.match(/(Mobi|Android|iPhone)/))) {			
            var e = skrollr.get();
            "undefined" != typeof e && e.refresh(document.querySelectorAll(".skrollable"));			
        }
		jQuery(".plus_row_scroll_parent.elementor-section-stretched").each(function(){
			var $this=jQuery(this);
			var sec_entrance=$this.data("row-entrance");
			var sec_exit=$this.data("row-exit");
			var exit_row=new Array("fly-left", "fly-right", "scale-smaller", "carousel", "stick-fly-left", "stick-fly-right","rotate-back");
			var entrance_row=new Array("fly-left", "fly-right", "scale-smaller", "carousel","rotate-back");
			if(jQuery.inArray( sec_entrance, entrance_row ) !== -1 || jQuery.inArray( sec_exit, exit_row ) !== -1){
				$this.css("left",sec_offset_left);
			}
		});
    },
    window.addEventListener("resize", function() {
        setTimeout(window.refreshSkrollables, 0.2)
    })
}),
"undefined" != typeof jQuery && jQuery(document).ready(function(e) {
    e(window).on("grid:items:added", function() {
        window.refreshSkrollables()
    })
});
