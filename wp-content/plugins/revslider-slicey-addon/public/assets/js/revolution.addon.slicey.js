/********************************************
 * REVOLUTION 5.0+ EXTENSION - SLICED
 * @version: 2.0.6 (04.05.2020)
 * @requires jquery.themepunch.revolution.js
 * @author ThemePunch
*********************************************/

;(function($) {
	
	var _R,_ISM;	
	window.RevSliderSlicey = function(slider) {				
		var hasSlicey = slider.find('.tp-slicey').length;
		if(!hasSlicey) return;		
		$('<style type="text/css">.tp-slicey {overflow: visible !important}</style>').appendTo($('head'));
		slider.one('revolution.slide.onloaded', function() {			
			_R = jQuery.fn.revolution;
			if (_R===undefined) return;
			_ISM = _R.is_mobile();					
			if(_R[slider[0].id]) init(slider, _R[slider[0].id],slider[0].id);			
		});
	
	};
	
	function getValues(st) {
		
		var obj = {
			
			duration:10000, 
			ease:'Linear.easeNone', 
			scalestart:1, 
			scaleend:1, 
			blurstart:0, 
			blurend:0
				
		};
		
		if(!st) return obj;
		st = st.split(';');
		var len = st.length;
		
		for(var i = 0; i < len; i++) {
			
			var s = st[i].split(':');
			switch($.trim(s[0])) {
				
				case 'd':
					obj.duration = parseInt(s[1], 10);
				break;
				
				case 'ss':
					obj.scalestart = parseInt(s[1], 10);
				break;
				
				case 'se':
					obj.scaleend = parseInt(s[1], 10);
				break;
				
				case 'bs':
					obj.blurstart = parseInt(s[1], 10);
				break;
				
				case 'be':
					obj.blurend = parseInt(s[1], 10);
				break;
				
				case 'e':
					obj.ease = $.trim(s[1]);
				break;
				
			}
			
		}
		
		return obj;
		
	}

	var deliverLazy = function(e,def,id) { 	
	 	return _R.gA(e,"lazyload")!==undefined ? _R.gA(e,"lazyload") : // INTERNAL LAZY LOADING
	 		   _R[id].lazyloaddata!==undefined && _R[id].lazyloaddata.length>0 && _R.gA(e,_R[id].lazyloaddata)!==undefined ? _R.gA(e,_R[id].lazyloaddata) : // CUSTOM DATA
	 		   _R.gA(e,"lazy-src")!==undefined ? _R.gA(e,"lazy-src") :  //WP ROCKET
	 		   _R.gA(e,"lazy-wpfc-original-src")!==undefined ? _R.gA(e,"lazy-wpfc-original-src") : //WP Fastes Cache Premium
	 		   _R.gA(e,"lazy")!==undefined ? _R.gA(e,"lazy") : // LAZY
	 		   def; // DEFAULT
	 }


	//////////////////////////////////////////
	//	-	INITIALISATION OF SLICEY 	-	//
	//////////////////////////////////////////
	var init = function(api, opt,id) {	
		opt.slidecobjects = [];
		
		// PREPARING THE LAYERS 
		opt.c.find('.tp-slicey').each(function(){
			 var sb = $(this),
				 li = sb.closest('rs-slide'),
				 di = li.find('rs-sbg').first().clone();
				 // shd = li.find('rs-sbg-wrap').data();
				 // _ = sb.data();
			
			 var shd = getValues(li.find('rs-sbg-wrap').data('panzoom'));
			 var _ = sb.attr('data-slicey');
			 if(!_) return;
			 
			 _ = JSON.parse(_);
				
			 _.slicey_offset_start = 1;
			 _.slicey_offset_end = _.offset===undefined ? 1 : 1 + parseInt(_.offset, 10) * 0.01;
			 
			 _.slicey_blurstart = _.blurstart===undefined || _.blurstart==="inherit" ? shd.blurstart : _.blurstart;
			 _.slicey_blurend = _.blurend===undefined || _.blurend==="inherit" ? shd.blurend : _.blurend;
			 
			 _.slicey_blurstart = parseInt(_.slicey_blurstart, 10);
			 _.slicey_blurend = parseInt(_.slicey_blurend, 10);

			 var lazy = deliverLazy(di[0], undefined,id),
				src = lazy !== undefined ? lazy : _R.gA(di[0],"svg_src") !=undefined ? _R.gA(di[0],"svg_src") : di[0].src===undefined ? di.data('src') : di[0].src;
						
			if (src!==undefined) {
				di[0].src = src;
				di[0].style.backgroundImage = "url("+src+")";
			}
			 
			di.addClass("slicedbox_defmig");


			 
			 var wp = $('<div class="slicedbox_wrapper" data-slicey_offset_start="' + 
			          _.slicey_offset_start+'" data-slicey_offset_end="'+_.slicey_offset_end+'" data-global_duration="' + 
					  shd.duration/1000+'" data-global_ease="'+shd.ease+'" data-slicey_blurstart="'+_.slicey_blurstart+'" data-slicey_blurend="'+_.slicey_blurend+'" data-global_scalestart="' + 
					  (shd.scalestart/100)+'" data-global_scaleend="' + 
					  (shd.scaleend/100)+'" style="width:100%;height:100%;position:absolute;overflow:hidden;box-shadow:' + 
					  li.attr('data-slicey')+'"></div>');
					  
			 
			 wp.append(di);
			 sb.append(wp);	  	     	     
			 var tc = wp.closest('rs-layer, .rs-layer');
			 punchgs.TweenLite.set(tc,{background:"transparent", transformStyle:"flat", perspective:"1000px", force3D:"true", transformOrigin:"50% 50%"});	     
			 opt.slidecobjects.push({caption:tc,li_index:li.data('index')});
			 punchgs.TweenLite.set(di,{opacity:1});
				 
		});

		// UPDATE LAYER SIZES IF SLIDE CHANGE (NEED TO DO -> Only Layer Reset on Current Layers in Slide !!)
		opt.c.on('revolution.slide.onafterswap',function(event,obj) {
			
			/*
				data.currentSlide and data.prevSlide are not always correct anymore inside this event
			*/
			var slideIndex = api.revcurrentslide() - 1,
				currentSlide = $('rs-slide').eq(slideIndex);
				
			if(!currentSlide.length) currentSlide = api.find('rs-slide').eq(0);
			var ind = currentSlide.data('index');

			for (var i in opt.slidecobjects) {	
				if(!opt.slidecobjects.hasOwnProperty(i)) continue;
				var l = opt.slidecobjects[i].caption,
					ls = l.data();
				if (ind===opt.slidecobjects[i].li_index)				
					updateSlicedBox(l,ls,opt);
			}
		});

		// ON LAYER ENTERSTAGE START ANIMATION ON LAYER
		opt.c.on('revolution.layeraction',function(event,obj) {	
			if (obj.eventtype==="enterstage") {			
				updateSlicedBox(obj.layer,obj.layersettings,opt);			
				animateSlicedBox(obj.layer,obj.layersettings,0);
			}
		});


		// RECALCULATE SIZE OF ELEMENTS ON RESIZE
		opt.c.on('revolution.slide.afterdraw', function() {
			for (var i in opt.slidecobjects) {
				if(!opt.slidecobjects.hasOwnProperty(i)) continue;
				var l = opt.slidecobjects[i].caption,
					ls = opt.slidecobjects[i].caption.data(),
					ali = opt.c.find('.active-revslide');

				if (ali.length===0 || ali.data('index')===opt.slidecobjects[i].li_index) {
					updateSlicedBox(l,ls,opt);	
					animateSlicedBox(l,ls,"update");	
				}
			}
		});
	};

	// UPDATE THE SLICEBOX SIZES AND CONTENT
	var updateSlicedBox = function(l,_,opt) {

		_.slicedbox_wrapper = _.slicedbox_wrapper == undefined ? l.find('.slicedbox_wrapper') : _.slicedbox_wrapper;			
		if (_.slicedbox_wrapper.length>0) {		
			_.slicedbox_defmig = _.slicedbox_defmig == undefined ? l.find('.slicedbox_defmig') : _.slicedbox_defmig;		
			_.origin_offset = {
				sx : (opt.conw/2 - _.calcx),
				sy : (opt.conh/2 - _.calcy),
				x : (opt.conw/2 - (_.calcx+(_.eow/2))),
				y : (opt.conh/2 - (_.calcy+(_.eoh/2)))
			};
			punchgs.TweenLite.set(_.slicedbox_defmig,{opacity:1,left:(0-_.calcx)+"px" , top:(0-_.calcy)+"px", width:opt.conw, height:opt.conh, position:"absolute"});
		}
	};

	// ANIMATE, RESET PROGRESSED ANIMATION ON LAYER
	var animateSlicedBox = function(l,ls,prog) {		
		if (ls.slicedbox_wrapper.length>0) {			
			var	_ = ls.slicedbox_wrapper.data();
			if (prog===undefined) prog=0;
			if (prog==="update" && _.slicedanimation!==undefined) prog = _.slicedanimation.progress();
			_.slicedanimation = new punchgs.TimelineLite();				
			_.scalestart = _.global_scalestart * _.slicey_offset_start;
			_.scaleend = _.global_scaleend * _.slicey_offset_end;

			_.slicedanimation.add(punchgs.TweenLite.fromTo(ls.slicedbox_wrapper,_.global_duration,
												{transformOrigin:(ls.origin_offset.sx+"px "+ls.origin_offset.sy+"px"),scale:(_.global_scalestart*_.slicey_offset_start)},
												{force3D:"auto", scale:(_.global_scaleend*_.slicey_offset_end),ease:_.global_ease}),0);
			
			// ADD BLUR EFFECT ON THE ELEMENTS
			if (_.slicey_blurstart!==undefined && _.slicey_blurend!==undefined &&  (_.slicey_blurstart!==0 || _.slicey_blurend!==0)) {
				_.blurElement = {a:_.slicey_blurstart};
				_.blurElementEnd = {a:_.slicey_blurend, ease:_.global_ease};
				_.blurAnimation = new punchgs.TweenLite(_.blurElement, _.global_duration, _.blurElementEnd);


				_.blurAnimation.eventCallback("onUpdate", function(_,ls) {									
					punchgs.TweenLite.set(ls.slicedbox_wrapper,{position:"absolute",'filter':'blur('+_.blurElement.a+'px)',filter:'blur('+_.blurElement.a+'px)','-webkit-filter':'blur('+_.blurElement.a+'px)'});
				},[_,ls]);
				_.slicedanimation.add(_.blurAnimation,0);			
			}

			_.slicedanimation.progress(prog);
			_.slicedanimation.play();
		}
	};


})(jQuery);