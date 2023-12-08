(function ($) {
	"use strict";
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-morphing-layouts.default', WidgetMorphingLayoutHandler);
	});
	$(window).on('load resize',function(){
		var win_w=window.innerWidth,wrap=$('.plus-morphing-svg-wrapper');		
		if(wrap.length > 0){
			var viewbox_morph=document.querySelectorAll('.plus-morphing-svg-wrapper');
			for (var i = 0; i < viewbox_morph.length; i++) {
				var viewbox_attr=viewbox_morph[i],
				morph_width=viewbox_attr.getAttribute('data-morph-width'),
				morph_width_tab=viewbox_attr.getAttribute('data-morph-wt'),
				morph_width_mob=viewbox_attr.getAttribute('data-morph-wm'),
				morph_height=viewbox_attr.getAttribute('data-morph-height'),
				morph_height_tab=viewbox_attr.getAttribute('data-morph-ht'),
				morph_height_mob=viewbox_attr.getAttribute('data-morph-hm');
				var viewbox_width=viewbox_attr.getAttribute('data-viewbox-width'),
				viewbox_width_tab=viewbox_attr.getAttribute('data-viewbox-wt'),
				viewbox_width_mob=viewbox_attr.getAttribute('data-viewbox-wm'),
				viewbox_height=viewbox_attr.getAttribute('data-viewbox-height'),
				viewbox_height_tab=viewbox_attr.getAttribute('data-viewbox-ht'),
				viewbox_height_mob=viewbox_attr.getAttribute('data-viewbox-hm');
				var svg_morph=viewbox_attr.children[0];
				if(win_w >= 1025){
					svg_morph.setAttribute("width",morph_width);
					svg_morph.setAttribute("height",morph_height);
					svg_morph.setAttribute('viewBox','0 0 '+viewbox_width+' '+viewbox_height);
				}
				if(win_w <= 1024 && win_w >= 768){
					if(morph_width_tab!='' && morph_width_tab!=null){
						svg_morph.setAttribute("width",morph_width_tab);
					}else{
						svg_morph.setAttribute("width",morph_width);
					}
					if(morph_height_tab!='' && morph_height_tab!=null){
						svg_morph.setAttribute("height",morph_height_tab);
					}else{
						svg_morph.setAttribute("height",morph_height);
					}
					if(viewbox_width_tab!='' && viewbox_width_tab!=null && viewbox_height_tab!='' && viewbox_height_tab!=null){
						svg_morph.setAttribute('viewBox','0 0 '+viewbox_width_tab+' '+viewbox_height_tab);
					}else{
						svg_morph.setAttribute('viewBox','0 0 '+viewbox_width+' '+viewbox_height);
					}
				}
				if(win_w <= 767){
					if(morph_width_mob!='' && morph_width_mob!=null){
						svg_morph.setAttribute("width",morph_width_mob);
					}else if(morph_width_tab!='' && morph_width_tab!=null){
						svg_morph.setAttribute("width",morph_width_tab);
					}else{
						svg_morph.setAttribute("width",morph_width);
					}
					if(morph_height_mob!='' && morph_height_mob!=null){
						svg_morph.setAttribute("height",morph_height_mob);
					}else if(morph_height_tab!='' && morph_height_tab!=null){
						svg_morph.setAttribute("height",morph_height_tab);
					}else{
						svg_morph.setAttribute("height",morph_height);
					}
					if(viewbox_width_mob!='' && viewbox_width_mob!=null && viewbox_height_mob!='' && viewbox_height_mob!=null){
						svg_morph.setAttribute('viewBox','0 0 '+viewbox_width_mob+' '+viewbox_height_mob);
					}else if(viewbox_width_tab!='' && viewbox_width_tab!=null && viewbox_height_tab!='' && viewbox_height_tab!=null){
						svg_morph.setAttribute('viewBox','0 0 '+viewbox_width_tab+' '+viewbox_height_tab);
					}else{
						svg_morph.setAttribute('viewBox','0 0 '+viewbox_width+' '+viewbox_height);
					}
				}
			}
		}
	});
	var WidgetMorphingLayoutHandler = function($scope, $) {
		var morphing_wrap = $scope.find('.plus-morphing-svg-wrapper'),
		morph_fixed_bg = $scope.find('.plus-morph-fixed-scroll-bg'),
		morph_fixed_bg_id = morph_fixed_bg.data("morph-fixed"),
		morph_fixed= morphing_wrap.data('morphfixed'),
		morph_row= morphing_wrap.data('morphrow'),
		morph_column= morphing_wrap.data('morphcolumn'),
		ids=morphing_wrap.data("id"),
		morph_id=morphing_wrap.data("morph-id");
		const morphJSON = morphing_wrap.data('morph');
		const MorphImage = morphing_wrap.data('morphimage');
		
		
		var wid_sec=$scope.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');		
		if(wid_sec.length){
			//fixed Morph
			if($scope.closest('.elementor').find('> [data-morph-id="'+morph_id+'"]').length > 0){
				var remove_morph=$scope.closest('.elementor').find("> .plus-morphing-svg-wrapper");
				remove_morph.remove();
			}
			//fixed bg Morph
			if($scope.closest('.elementor').find('> [data-morph-fixed="'+morph_fixed_bg_id+'"]').length > 0){
				var remove_morph=$scope.closest('.elementor').find("> .plus-morph-fixed-scroll-bg");
				remove_morph.remove();
			}
			//sections Morph
			if($scope.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con').find('> [data-morph-id="'+morph_id+'"]').length > 0){
				var remove_morph=$scope.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con').find("> .plus-morphing-svg-wrapper");
				remove_morph.remove();
			}
			//column Morph
			if($scope.closest('.elementor-column').find('> [data-morph-id="'+morph_id+'"]').length > 0){
				var remove_morph=$scope.closest('.elementor-column').find("> .plus-morphing-svg-wrapper");
				remove_morph.remove();
			}
		}
		
		if(morph_fixed=='yes' && morph_fixed!=undefined){
			morphing_wrap.closest('.elementor').prepend(morphing_wrap);
			if(morph_fixed_bg.length > 0){
				morph_fixed_bg.closest('.elementor').prepend(morph_fixed_bg);
				morph_fixed_bg.closest('.elementor').css("position",'inherit');
			}
		}
		if(morph_row=='yes' && morph_row!=undefined){
			morphing_wrap.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con').prepend(morphing_wrap);
		}
		if(morph_column=='yes' && morph_column!=undefined){
			morphing_wrap.closest('.elementor-column').prepend(morphing_wrap);
		}
		class MorphingLayouts {
			constructor(el) {
				this.DOM = {};
				this.DOM.el = el;				
				this.DOM.svg = this.DOM.el.querySelector('.morph');
				this.DOM.path = this.DOM.svg.querySelector('path');
				this.DOM.morph = this.DOM.el.dataset.morph;
				this.DOM.morph = JSON.parse(this.DOM.morph);
				
				//Fixed Scroll
				this.DOM.fixed = this.DOM.el.dataset.morphfixed;
				this.DOM.parent_id = this.DOM.el.parentNode.getAttribute("data-elementor-id");
				
				if($('.elementor .elementor-inner').length){
					this.DOM.contentElems = Array.from(document.querySelectorAll('.elementor-'+this.DOM.parent_id+'>.elementor-inner > .elementor-section-wrap > .elementor-element'));
				}else if($('.elementor .elementor-section-wrap').length){
                    this.DOM.contentElems = Array.from(document.querySelectorAll('.elementor-'+this.DOM.parent_id+'> .elementor-section-wrap > .elementor-element'));
                }else{
					this.DOM.contentElems = Array.from(document.querySelectorAll('.elementor-'+this.DOM.parent_id+'> .elementor-element'));
				}	
				
				this.DOM.contentElemsTotal = this.DOM.contentElems.length;
				this.DOM.PathAltData = [];
				this.DOM.AnimationData = [];
				if(this.DOM.fixed=='yes' && this.DOM.morph.pathAlt!=undefined){					
					for (var i = 0; i < this.DOM.morph.pathAlt.length; ++i) {						
						if(this.DOM.morph.pathAlt[i]!=undefined && this.DOM.morph.pathAlt[i]!=''){
							var obj = { 
								value: this.DOM.morph.pathAlt[i],
								duration:this.DOM.morph.animation.path.duration[i],
							};
							this.DOM.PathAltData.push(obj);
						}
					}
					this.DOM.contentAlt=[Object.values(this.DOM.PathAltData)];					
				}else{
					this.DOM.contentAlt='';
				}
				
				//Path Morph				
				this.DOM.myData = [];
				for (var i = 0; i < this.DOM.morph.path.length; ++i) {
					if(this.DOM.morph.path[i]!=undefined && this.DOM.morph.path[i]!=''){
						var obj = { 
							value: this.DOM.morph.path[i],
							duration:this.DOM.morph.duration,
						};
						this.DOM.myData.push(obj);
					}
				}
				this.DOM.content_arr=[Object.values(this.DOM.myData)];
				
				//Image morph
				this.DOM.image = this.DOM.svg.querySelector('image');
				if(this.DOM.image){
					this.DOM.imageJSON = this.DOM.el.dataset.morphimage;
					this.DOM.imageJSON = JSON.parse(this.DOM.imageJSON);
				}else{
					this.DOM.imageJSON ='';
				}
				this.CONFIG = {
					// Defaults:
					animation: {
						path: {
							duration: this.DOM.morph.duration || 3500,
							delay: this.DOM.morph.delay || 0,
							easing: this.DOM.morph.easing || 'linear',
							elasticity: this.DOM.morph.elasticity || 300
						},
						svg: {
							duration: 1,
							delay: this.DOM.morph.delay || 0,
							easing: this.DOM.morph.easing || 'linear',
							scaleX: this.DOM.morph.scaleX || 1,
							scaleY: this.DOM.morph.scaleY || 1,
							translateX: this.DOM.morph.tx || 0,
							translateY: this.DOM.morph.ty || 0,
							rotate: this.DOM.morph.rotate || 0
						},
						image: {
							duration: this.DOM.imageJSON.duration || 800,
							delay: this.DOM.imageJSON.delay || 0,
							easing: this.DOM.morph.easing || 'linear',
							elasticity: this.DOM.imageJSON.elasticity || 300,
							scaleX: this.DOM.imageJSON.scaleX || 1,
							scaleY: this.DOM.imageJSON.scaleY || 1,
							translateX: this.DOM.imageJSON.trans_x || 0,
							translateY: this.DOM.imageJSON.trans_y || 0,
							rotate: this.DOM.imageJSON.rotate || 0,
							hover_scaleX: this.DOM.imageJSON.hover_scaleX || 1,
							hover_scaleY: this.DOM.imageJSON.hover_scaleY || 1,
							hover_translateX: this.DOM.imageJSON.hover_trans_x || 0,
							hover_translateY: this.DOM.imageJSON.hover_trans_y || 0,
							hover_rotate: this.DOM.imageJSON.hover_rotate || 0
						},
					}
				};
				if(this.DOM.fixed!='yes'){
					this.initEvents();
				}else{
					this.createScrollWatchers();
				}
			}
			initEvents() {
				this.mouseenterFn = () => {
					this.mouseTimeout = setTimeout(() => {
					this.isActive = true;
					this.animate();
					}, 75);
				}
				this.mouseleaveFn = () => {
					clearTimeout(this.mouseTimeout);
					if( this.isActive ) {
						this.isActive = false;
						this.animate();
					}
				}

				this.isActive = false;
				this.animate();
				if(this.DOM.imageJSON.image_hover=='yes' || this.DOM.morph.hover_path=='yes'){
					
					this.DOM.el.addEventListener('mouseenter', this.mouseenterFn);
					this.DOM.el.addEventListener('mouseleave', this.mouseleaveFn);
					this.DOM.el.addEventListener('touchstart', this.mouseenterFn);
					this.DOM.el.addEventListener('touchend', this.mouseleaveFn);
				}
			}
			
			getAnimeObj(targetStr) {
				const target = this.DOM[targetStr];								
				let animeOpts = {
					targets: target,
					duration: this.CONFIG.animation[targetStr].duration,
					delay: this.CONFIG.animation[targetStr].delay,
					easing: this.CONFIG.animation[targetStr].easing,
					
				};
				
				if( targetStr === 'path' ) {
					if(this.DOM.morph.hover_path=='yes'){
						animeOpts.d = this.isActive ? this.DOM.content_arr[0][0] : this.DOM.content_arr[0][1];
					}else{
						animeOpts.d = this.isActive ? this.DOM.content_arr[0] : this.DOM.content_arr[0];
					}
					if(this.DOM.morph.hover_path!='yes'){
						animeOpts.loop= true;
						animeOpts.fill={
							value: this.DOM.morph.fill.color,
							duration: 500,
							easing: this.DOM.morph.fill.easing
						};
						animeOpts.direction= 'alternate';
					}
				}
				
				if(targetStr === 'svg' || targetStr === 'image'){
					animeOpts.elasticity= this.CONFIG.animation[targetStr].elasticity;
					animeOpts.scaleX= this.isActive ? this.CONFIG.animation[targetStr].hover_scaleX : this.CONFIG.animation[targetStr].scaleX;
					animeOpts.scaleY= this.isActive ? this.CONFIG.animation[targetStr].hover_scaleY : this.CONFIG.animation[targetStr].scaleY;
					animeOpts.translateX= this.isActive ? this.CONFIG.animation[targetStr].hover_translateX : this.CONFIG.animation[targetStr].translateX;
					animeOpts.translateY= this.isActive ? this.CONFIG.animation[targetStr].hover_translateY : this.CONFIG.animation[targetStr].translateY;
					animeOpts.rotate= this.isActive ? this.CONFIG.animation[targetStr].hover_rotate : this.CONFIG.animation[targetStr].rotate;
				}
				anime.remove(target);
				return animeOpts;
			}
			initShapeLoop(pos,path,conn,connAlt,color,duration,easing){
				pos = pos || 0;
				anime.remove(path);
				anime({
					targets: path,
					easing: 'linear',
					d: [connAlt[0][pos], conn[0][pos]],
					loop: true,
					fill: {
						value: this.DOM.morph.animation.fill.color[pos],
						duration: this.DOM.morph.animation.fill.duration[pos],
						easing: this.DOM.morph.animation.fill.easing[pos]
					},
					direction: 'alternate'
				});
			}
			initShapeEl(){
				anime.remove(this.DOM.svg);
				anime({
					targets: this.DOM.svg,
					duration: 1,
					easing: 'linear',
					scaleX: this.DOM.morph.animation.scaleX[0],
					scaleY: this.DOM.morph.animation.scaleY[0],
					translateX: this.DOM.morph.animation.tx[0]+'px',
					translateY: this.DOM.morph.animation.ty[0]+'px',
					rotate: this.DOM.morph.animation.rotate[0]+'deg'
				});
			}
			createScrollWatchers() {
				var step=0;
				var totalEle=this.DOM.contentElemsTotal;
				var svg=this.DOM.svg;
				var path=this.DOM.path;
				const conn=this.DOM.content_arr;
				const connAlt=this.DOM.contentAlt;
				var Animation=this.DOM.morph.animation;
				var footer = document.querySelector('.theoutset-footer-main');
				var color =this.DOM.morph.fill.color;
				var duration =500;
				var easing =this.DOM.morph.fill.easing;
				this.initShapeEl();
				this.initShapeLoop(0,path,conn,connAlt,color,duration,easing);
				var position;
				this.DOM.contentElems.forEach((el,pos) => {
					
					const scrollElemToWatch = pos ? this.DOM.contentElems[pos] : this.DOM.contentElems[pos];
					pos = pos ? pos : totalEle;
					const watcher = scrollMonitor.create(scrollElemToWatch,-300);
					
					watcher.enterViewport(function() {
						step = pos;
						if(totalEle >= conn[0].length && pos+1 > conn[0].length){
							position=0;
						}else{
							position=pos;
						}
						anime.remove(path);
						anime({
							targets: path,
							duration: Animation.path.duration[position] || 2500,
							easing: Animation.path.easing[position] || 'linear',
							elasticity: Animation.path.elasticity[position] || 0,
							d: conn[0][position],
							fill: {
								value: Animation.fill.color[position],
								duration: Animation.fill.duration[position] || 500,
								easing: Animation.fill.easing[position] || 'linear'
							},
							complete: function() {
								position = position || 0;
								anime.remove(path);
								anime({
									targets: path,
									easing: 'linear',
									d: [connAlt[0][position], conn[0][position]],
									loop: true,
									fill: {
										value: Animation.fill.color[position],
										duration: Animation.fill.duration[position],
										easing: Animation.fill.easing[position]
									},
									direction: 'alternate'
								});
							}
						});
						
						anime.remove(svg);
						anime({
							targets: svg,
							duration: Animation.svg.duration[position],
							easing: Animation.svg.easing[position],
							elasticity: 0,
							scaleX: Animation.scaleX[position],
							scaleY: Animation.scaleY[position],
							translateX: Animation.tx[position]+'px',
							translateY: Animation.ty[position]+'px',
							rotate: Animation.rotate[position]+'deg'
						});
					});
					watcher.exitViewport(function() {
						var idx = !watcher.isAboveViewport ? pos-1 : pos+1;
						if( idx <= totalEle && step !== idx ) {							
							step = idx;
							if(totalEle > conn[0].length && idx+1 > conn[0].length){
								position=0;
							}else{
								position=idx;
							}
							anime.remove(path);
							anime({
								targets: path,
								duration: Animation.path.duration[position] || 2500,
								easing: Animation.path.easing[position] || 'linear',
								elasticity: Animation.path.elasticity[position] || 0,
								d: conn[0][position],
								fill: {
									value: Animation.fill.color[position],
									duration: Animation.fill.duration[position],
									easing: Animation.fill.easing[position]
								},
								complete: function() {
									position = position || 0;
									anime.remove(path);
									anime({
										targets: path,
										easing: 'linear',
										d: [connAlt[0][position], conn[0][position]],
										loop: true,
										fill: {
											value: Animation.fill.color[position],
											duration: Animation.fill.duration[position],
											easing: Animation.fill.easing[position]
										},
										direction: 'alternate'
									});
								}
							});
							anime.remove(svg);
							anime({
								targets: svg,
								duration: Animation.svg.duration[position],
								easing: Animation.svg.easing[position],
								elasticity: 0,
								scaleX: Animation.scaleX[position],
								scaleY: Animation.scaleY[position],
								translateX: Animation.tx[position]+'px',
								translateY: Animation.ty[position]+'px',
								rotate: Animation.rotate[position]+'deg'
							});
						}
					});
				});
			}
			animate() {
				anime(this.getAnimeObj('path'));
				anime(this.getAnimeObj('svg'));
				anime(this.getAnimeObj('image'));
			}
		}
		const items = Array.from(document.querySelectorAll('.'+ids));
		const init = (() => items.forEach(item => new MorphingLayouts(item)))();
	};
})(jQuery);