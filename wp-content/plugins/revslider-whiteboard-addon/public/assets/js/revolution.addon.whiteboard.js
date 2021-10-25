/********************************************
 * REVOLUTION 6.0+ EXTENSION - WHITEBOARD
 * @version: 2.1 (17.10.2019)
 * @requires jquery.themepunch.revolution.js
 * @author ThemePunch
*********************************************/

;(function($) {

var _R = jQuery.fn;
	
///////////////////////////////////////////
// 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
///////////////////////////////////////////
jQuery.extend(true,_R, {
	rsWhiteBoard: function(el, options) {
		
		return this.each(function() {
			
			if(jQuery.fn.revolution && jQuery.fn.revolution[this.id]) {
			
				var opt = jQuery.fn.revolution[this.id];
				jQuery(this).on('revolution.slide.onloaded',function() {
					init(opt);
				});	

			}
			
		});
	}		
});

//////////////////////////////////////////
//	-	INITIALISATION OF WHITEBOARD 	-	//
//////////////////////////////////////////
var init = function(opt) {
	
	if (opt.whiteboard===undefined) return false;
	
	var legacyDefaults = {
	
		handtype:"right",
		jittering:{
			distance:"80",
			distance_horizontal:"100",
			repeat:"5",
			offset:"10",
			offset_horizontal:"0"
		},
		rotation:{
			angle:"10",
			repeat:"3"
		}
	
	};
	
	jQuery.extend(true, opt.whiteboard.writehand, legacyDefaults);
	jQuery.extend(true, opt.whiteboard.movehand, legacyDefaults);
	
	// Merge of Defaults										
	var o = opt.whiteboard;
	
	// Define Markups
	var _css_thehand = "position:absolute;z-index:1000;top:0px;left:0px;",
		_css_hand_inner ="position:absolute;z-index:1000;top:0px;left:0px;",
		_css_hand_scale ="position:absolute;z-index:1000;top:0px;left:0px;",
		_css_hand_image = "position:absolute; top:0px;left:0px;background-size:contain;background-repeat:no-repeat;background-position:center center;";
		//opacity:0.2;border:1px dotted rgba(255,0,0,0.2);
	
	if (o.movehand!=undefined) o.movehand.markup = '<div class="wb-move-hand wb-thehand" style="'+_css_thehand+'"><div class="wb-hand-inner" style="'+_css_hand_inner+';width:'+o.movehand.width+'px;height:'+o.movehand.height+'px"><div class="wb-hand-scale" ><div class="wb-hand-image" style="'+_css_hand_image+'width:'+o.movehand.width+'px;height:'+o.movehand.height+'px;background-image:url('+o.movehand.source+');"></div></div></div>';
	if (o.writehand!=undefined)  o.writehand.markup = '<div class="wb-draw-hand wb-thehand" style="'+_css_thehand+'"><div class="wb-hand-inner" style="'+_css_hand_inner+'"><div class="wb-hand-scale" style="'+_css_hand_scale+'"><div class="wb-hand-image" style="'+_css_hand_image+'width:'+o.writehand.width+'px;height:'+o.writehand.height+'px;background-image:url('+o.writehand.source+');"></div></div></div>';
	
	
	jQuery(window).resize(function() {

		clearTimeout(opt.whiteboard_resize_timer);
		opt.whiteboard_resize_timer = setTimeout(function() {
			jQuery('.wb-thehand').each(function() {
				var h = jQuery(this).find('.wb-hand-scale');				
				punchgs.TweenLite.set(h,{scale:opt.bw});
			});
		},50);
	});
	// Listen on Layer Actions
	opt.c.on('revolution.slide.onbeforeswap',function(event,obj) {

		opt.whiteboard.animating = false;
		opt.c.find('.wb-thehand').remove();
		
	});
	opt.c.on('revolution.slide.layeraction',function(event,obj) {			
		
		var obj_wb = obj.layersettings.whiteboard;

		if (obj_wb != undefined) {
			
			if (obj_wb.configured!=true) {
				if (obj_wb.hand.mode=="write" || obj_wb.hand.mode=="draw") {
					obj_wb.jitter.distance = obj_wb.jitter.distance!=undefined ? parseInt(obj_wb.jitter.distance,10)/100 : parseInt(o.writehand.jittering.distance,10)/100;
					obj_wb.jitter.distanceHorizontal = obj_wb.jitter.distanceHorizontal!=undefined ? parseInt(obj_wb.jitter.distanceHorizontal,10)/100 : parseInt(o.writehand.jittering.distance_horizontal,10)/100;
					obj_wb.jitter.offset = obj_wb.jitter.offset!=undefined ? parseInt(obj_wb.jitter.offset,10)/100 : parseInt(o.writehand.jittering.offset,10)/100;
					obj_wb.jitter.offsetHorizontal = obj_wb.jitter.offsetHorizontal!=undefined ? parseInt(obj_wb.jitter.offsetHorizontal,10)/100 : parseInt(o.writehand.jittering.offset_horizontal,10)/100;
					obj_wb.hand.type = obj_wb.hand.type || o.writehand.handtype;
				}
				
				if (obj_wb.hand.mode=="move") {					
					obj_wb.hand.type = obj_wb.hand.type || o.movehand.handtype;
				}
				obj_wb.configured = true;
			}

			//obj.layer.css({border:"1px dashed rgba(0,0,0,0.3)"})
			
			var wb,
				_d = obj.layer.data(),
				hand = jQuery(_d.p).find('.wb-thehand');

			if (hand.length>0) {
				wb = hand.data('wb');				
			}
			else {
				wb = jQuery.extend(true,{},obj.layersettings.whiteboard);				
			}


			// ATTACH HAND TO THE RIGHT ELEMENT
			if (obj.eventtype=="enterstage") {
				
				if (obj.layer.is(':visible')===false) return;
				if (!obj.layer.hasClass("handadded")) {
					obj.layer.addClass("handadded");
						obj.layersettings.handEffect="on";												
				 		if (wb.handadded!=true) 
							attachHandTo(opt,obj,wb);							
						animateHand(obj,wb,opt);
				}								
			}		
			if (obj.eventtype=="enteredstage") {
				
				obj.layer.data('whiteboardactive', true);
				if (obj.layer.hasClass("handadded") && !obj.layer.hasClass("handremoved")) {				
					obj.layer.addClass("handremoved");
					obj.layersettings.handEffect="off";
					if (moveBetweenStages(opt,obj,wb)==false) {						
						moveoutHand(opt,obj,wb);
					}
				}				
			}

			if (obj.eventtype=="leavestage") {
				
				obj.layer.data('whiteboardactive', false);
				obj.layer.removeClass("handadded");
				obj.layer.removeClass("handremoved");
			}
		}
	});
	
};
/*********************************************************
	- 	Look For Next Drawn/Written Layer in Slide - 
*********************************************************/

var lookForNextLayer = function(obj) {
	var retobj = {};

	retobj.obj = "";
	retobj.startat=9999999;
	
	obj.layer.closest('rs-slide').find('rs-layer, .rs-layer').each(function() {
		var c = jQuery(this),
			_ = c.data();
		
		if (!_.whiteboardactive && _.whiteboard!=undefined && _.whiteboard.hand.mode!="move") {
			
			if (parseInt(_.frames.frame_1.timeline.start,10)<retobj.startat) {
				
				retobj.obj = c;
				retobj.startat = parseInt(_.frames.frame_1.timeline.start,10);
			}
		}
	});

	return retobj;

};


/**************************************
	-	Move Between Stages -
****************************************/
var moveBetweenStages = function(opt,obj,wb) {
	
	opt.whiteboard.animating = false;
	
	var ret = false,
		_ = obj.layer.data(),
		hand = jQuery(_.p).find('.wb-thehand');
		
	var checkAnime = function() {
				
		if(opt.whiteboard.animating) {
			requestAnimationFrame(function() {hand.remove();});
		}
		else {
			requestAnimationFrame(checkAnime);
		}
	
	};
			
	if (_.whiteboard.hand.gotoLayer==="on" && _.whiteboard.hand.mode!="move" && opt.c.find('.wb-between-stations').length==0) {
		
		// IF HAND HAS TO MOVE TO NEXT POSSIBLE ITEM

		var nextobj = lookForNextLayer(obj);
		
		if (nextobj!=undefined && nextobj.obj.length>0) {
			
			var hi = hand.find('.wb-hand-inner'),	
				le = _.timeline!=undefined && _.timeline._labels!=undefined &&  _.timeline._labels.frame_1_end!=undefined ? _.timeline._labels.frame_1_end : 0,
				s = Math.max(nextobj.startat/1000 - le, 0),
				pos = jQuery(_.p).position(),
				posnew = nextobj.obj.data('p').position(),
				wasinstaticlayer = hand.closest('rs-static-layers').length>0 ? true : false;

			hand.appendTo(hand.closest('rs-slides'));
			hand.addClass("wb-between-stations");
			if (wasinstaticlayer) 
				hand.css({zIndex:200});		
			else
				hand.css({zIndex:100});		
			wb.handEffect = "off";
			
			punchgs.TweenLite.fromTo(hand,s,{top:pos.top, left:pos.left},{top:posnew.top,left:posnew.left});
			punchgs.TweenLite.to(hi,s,{x:0,y:0,onComplete:checkAnime});
			ret = true;
		} 
	}
	return ret;
};



/*************************************
	- 	Draw - Jitter Hand - 
**************************************/

var rotateHand = function(hand_inner, obj, _) {
	if (_.handEffect=="off" || _.handadded!=true) return;

	
	var ang = parseInt(_.hand.angle,10) || 10,
		 ro = _.hand.mode=="write" || _.hand.mode=="draw" ? Math.random()*ang - (ang/2) : 0;		
	_.rotatespeed = _.rotatespeed || 0.05;
	
	
	_.rotating_anim = punchgs.TweenLite.to(hand_inner,_.rotatespeed,{rotationZ:ro,ease:punchgs.Power3.easeOut,onComplete:function(){rotateHand(hand_inner,obj, _);}});
};

var jitterHand = function(hand_inner, obj, _) {	

	if (_.handEffect=="off" || _.handadded!=true) return;	
	var _d = obj.layer.data();

	if (_.jitter_direction == "horizontal") {

		var	elwidth = _.maxwave || _d.eow*_.jitter.distanceHorizontal,
			eloffset = _d.eow*_.jitter.offsetHorizontal;	
					
		if (elwidth == 0) return;
		_.current_x_offset = _.current_x_offset || 0;
		_.lastwave =  Math.random()*elwidth+eloffset;
		_.jitterspeed = _.jitterspeed || 0.05;			
		_.jittering_anim = punchgs.TweenLite.to(hand_inner,_.jitterspeed,{x:_.lastwave,ease:punchgs.Power3.easeOut,onComplete:function(){jitterHand(hand_inner,obj,_);}});
	
	} else {
		var	elheight = _.maxwave || _d.eoh;
		
		if (elheight == 0) return;
		_.current_y_offset = _.current_y_offset || 0;
		_.lastwave = _.lastwave == _.current_y_offset + (elheight*_.jitter.offset)? Math.random()*(elheight*_.jitter.distance) + _.current_y_offset + (elheight*_.jitter.offset): _.current_y_offset + (elheight*_.jitter.offset);
		_.jitterspeed = _.jitterspeed || 0.05;	
		
		_.jittering_anim = punchgs.TweenLite.to(hand_inner,_.jitterspeed,{y:_.lastwave,ease:punchgs.Power3.easeOut,onComplete:function(){jitterHand(hand_inner,obj,_);}});
	}
};

/*************************************
	- 	ATTACH HAND TO THE LAYER  - 
**************************************/
var attachHandTo = function(opt,obj,wb) {
	
	// SET DEFAULTS
	var o = wb.hand.mode=="move" ? opt.whiteboard.movehand : opt.whiteboard.writehand,				
		element = o.markup,
		_d = obj.layer.data();

	
	wb.hand.rotation = wb.hand.rotation || 0;
	wb.hand_origin = o.originX+"px "+o.originY+"px";
	wb.hand_scale =  wb.hand.type=="right" ? 1 : -1;
	wb.hand.x = parseInt(wb.hand.x,10) || 0;
	wb.hand.y = parseInt(wb.hand.y,10) || 0;

	// ADD THE HAND TO THE LAYER
	jQuery(element).appendTo(jQuery(_d.p)); 
	wb.handadded = true;	

	var hand = jQuery(_d.p).find('.wb-thehand'),
		hand_image = hand.find('.wb-hand-image');

	// PREPARE HAND TRANSFORMS		
	punchgs.TweenLite.set(hand_image,{scaleX:wb.hand_scale, rotation:wb.hand.rotation,transformOrigin:wb.hand_origin, x:0-o.originX + wb.hand.x, y:0-o.originY + wb.hand.y});
	punchgs.TweenLite.set(hand.find('.wb-hand-scale'),{scale:opt.bw,transformOrigin:"0% 0%"});
};

var isAnimating = function(opt) {
	opt.whiteboard.animating = true;
};

var animateHand = function(obj,wb,opt) {
	
	var _d = obj.layer.data(),
		hand = jQuery(_d.p).find('.wb-thehand'),
		hand_inner = hand.find('.wb-hand-inner');
		
	
	// SET LOOP ANIMATION
	switch (wb.hand.mode) {
		case "write":
		case "draw":
			
			var s = _d.frames[obj.frame_index].timeline.speed/1000,
				yf, 
				yt, 
				xf, 
				xt;
			
			// IF IT IS A TEXT, WRITE TEXT
			if (_d.splitText!= undefined && _d.splitText!="none") {					
				
				wb.tweens = obj.layersettings.timeline.getChildren(true,true,false);
				
				jQuery.each(wb.tweens,function(i,tw) {

					if(obj.frame_index === 'frame_1' && this.data && this.data.splitted) {
						
						tw.eventCallback("onStart",function(hand_inner,obj,wb) {
							
							var el = jQuery(this.target),
								w=el.width(),
								h=el.height();
											
							if 	(el!==undefined && el.html() !==undefined && el.html().length>0 && el.html().charCodeAt(0)!=9 && el.html().charCodeAt(0)!=10) {
								
								var pos = el.position(),
									pa = el.parent(),
									papa = pa.parent(),								
									speed = this._duration,
									x = pos.left,
									y = pos.top;
								
								if (pa.hasClass("rs_splitted_words")) {
									x = pa.position().left + x;
									y = pa.position().top + y;
								}
								if (papa.hasClass("rs_splitted_lines")) {
									x = papa.position().left + x;	
									y = papa.position().top + y;
								}
															
								wb.rotatespeed = wb.hand.angleRepeat !== undefined ? (parseFloat(speed)/parseFloat(wb.hand.angleRepeat)) : speed>1 ? speed/6 : speed>0.5 ? speed/6 : speed / 3;
								wb.jitterspeed = wb.jitter.repeat !== undefined ? (parseFloat(speed)/parseFloat(wb.jitter.repeat)) : speed>1 ? speed/6 : speed>0.5 ? speed/6 : speed / 3;
								
								if (wb.current_y_offset != y) speed = 0.1;

								wb.current_y_offset = y || 0;
								wb.maxwave = h;			
								if (i<wb.tweens.length-1)				
									punchgs.TweenLite.to(hand_inner,speed,{x:x+w});
								else 
									punchgs.TweenLite.to(hand_inner,speed,{x:x+w,onComplete:function() {									
										wb.handEffect="off";
										try{
											wb.jittering_anim.kill(false);
										} catch(e) {}
									}});
							} else {	
								
								wb.current_y_offset = 0;
								wb.maxwave = h;							
								wb.handEffect="off";
								try{
									wb.jittering_anim.kill(false);
								} catch(e) {}
							}
							
							opt.whiteboard.animating = true;

							
						},[hand_inner,obj,wb]);
						
					}
										
				});


			 } else {			 	
				var dishor = (_d.eow*wb.jitter.distanceHorizontal),
					offhor = (_d.eow*wb.jitter.offsetHorizontal),
					disver = (_d.eoh*wb.jitter.distance),
					offver = (_d.eoh*wb.jitter.offset);
					
					wb.rotatespeed = wb.hand.angleRepeat !== undefined ? (parseFloat(s)/parseFloat(wb.hand.angleRepeat)) : s>1 ? s/6 : s>0.5 ? s/6 : s / 3;
					wb.jitterspeed = wb.jitter.repeat !== undefined ? (parseFloat(s)/parseFloat(wb.jitter.repeat)) : s>1 ? s/6 : s>0.5 ? s/6 : s / 3;

			 	if (wb.hand.direction=="right_to_left")	{
			 		xf = _d.eow-offhor;
			 		xt = xf - dishor;

			 		punchgs.TweenLite.fromTo(hand_inner,s,{x:xf},{x:xt , ease:obj.layersettings.frames.frame_1.timeline.ease,onComplete:function() {
				 		wb.handEffect="off";
					}, onStart: isAnimating, onStartParams: [opt]});			 	
				}
			 	else
			 	if (wb.hand.direction=="top_to_bottom") {
			 		yf = offver;
			 		yt = yf + disver;	

			 		punchgs.TweenLite.fromTo(hand_inner,s,{y:yf},{y:yt, ease:obj.layersettings.frames.frame_1.timeline.ease,onComplete:function() {
				 		wb.handEffect="off";
				 	}, onStart: isAnimating, onStartParams: [opt]});	
				 	wb.jitter_direction = "horizontal";
			 	} else
			 	if (wb.hand.direction=="bottom_to_top") {
			 		yf = _d.eoh-offver;
			 		yt = yf - disver;
			 		punchgs.TweenLite.fromTo(hand_inner,s,{y:yf},{y:yt, ease:obj.layersettings.frames.frame_1.timeline.ease,onComplete:function() {
				 		wb.handEffect="off";
				 		
				 	}, onStart: isAnimating, onStartParams: [opt]});			 
				 	wb.jitter_direction = "horizontal";	
			 	} else { 	
			 		xf = offhor;
			 		xt = xf + dishor;
						
				 	punchgs.TweenLite.fromTo(hand_inner,s,{x:xf},{x:xt, ease:obj.layersettings.frames.frame_1.timeline.ease,onComplete:function() {
				 		wb.handEffect="off";
				 	}, onStart: isAnimating, onStartParams: [opt]});	
				 }				 	
			 }
					
			jitterHand(hand_inner, obj,wb);
			if (_R.isFirefox==false) rotateHand(hand_inner, obj,wb);
			
			
		break;

		case "move":
			// PUT HANDS WRAPPER IN POSITION

			hand.data('outspeed',obj.layersettings.frames.frame_999.timeline.speed/1000);
			hand.data('outease',obj.layersettings.frames.frame_999.timeline.ease);
			
			function onUpdate(obj) {
				
				var target = this.target instanceof jQuery ? this.target[0] : this.target,
					trans = target._gsTransform,
					pos = {};

				// CALCULATE POSITION OF LAYER
				pos.x = trans.x;
				pos.y = trans.y;
				
										
				// SAVE ORIGINAL POSITION
				if (hand.data('pos')===undefined) hand.data('pos',pos);
				wb.hand_inner_animation = punchgs.TweenLite.set(hand,{x:pos.x,y:pos.y});
				
			}
			
			var tween = punchgs.TweenLite.getTweensOf(obj.layer),
				len = tween.length; 
				
			var animating = function() {
				opt.whiteboard.animating = true;
			};
				
			for(var i = 0; i < len; i++) {
				
				if(obj.frame_index === 'frame_1') {
					
					tween[i].eventCallback("onStart",animating);
					tween[i].eventCallback("onUpdate",onUpdate,[obj]);
				
				}
				
			}
			
		break;
	}
	hand.data('wb',wb);
	
};

/*************************************
	- 	MOVE HAND AWAY FROM LAYER  - 
**************************************/
var moveoutHand = function(opt,obj,wb) {

	opt.whiteboard.animating = false;	
	
	var _d = obj.layer.data(),
		hand = jQuery(_d.p).find('.wb-thehand'),
		pos = hand.data('pos') || {x:0, y:0},
		tl = hand.position() || {top:0,left:0},
		sp = _d.frames.frame_999.timeline.speed/1000 || 2,
		ea = _d.frames.frame_999.timeline.ease,
		alp = pos.x==0 && pos.y==0 ? 0 : 1;

	sp = sp*0.5;

	

	if (wb.hand.mode!="move") {
		var handoffset = jQuery(_d.p).position();

		tl.left = wb.hand.type=="right" ? opt.c.width() -  handoffset.left + _d.eow :  0  - handoffset.left-_d.eow;
		tl.top = opt.c.height();		

	}

	punchgs.TweenLite.to(hand,sp,{top:tl.top, left:tl.left,autoAlpha:alp,x:pos.x, y:pos.y,ease:ea,onComplete:function() {

		hand.remove();
		wb.handadded = false;
	}});

};


})(jQuery);