/***************************************************
 * REVOLUTION 6.0.0 WHITEBOARD ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';

	// TRANSLATABLE CONTENT
	var bricks = {
		writehand:"Write Hand",
		movehand:"Move Hand",
		type:"Type",
		direction:"Direction",
		jitteringdistance:"Jittering Area Height",
		jitteringoffset:"Jittering Area Offset",
		jitteringdistancehor:"Jittering Area Width",
		jitteringoffsethor:"Jittering Area Offset Hor.",
		jitteringrepeat:"Jittering Changes",
		handrotation:"Hand Rotation",
		maxrotationangle:"Max Rotation Angle",
		rotationvariations:"Rotation Variations",
		handangle:"Writting Angle",
		handanglerepeat:"Writting Angle Variations",
		preview:"Preview",
		pickorigin:"Pick Origin",
		active:"Active",
		hand:"Hand",
		mode:"Mode",
		draw:"Draw",
		write:"Write",
		move:"Move",
		right:"Right",
		left:"Left",
		ltr:"Left to Right",
		rtl:"Right to Left",
		ttb:"Top to Bottom",
		btt:"Bottom to Top",
		gotonextlayer:"Move to next Layer",
		hidehand:"Hide Hand when Done",
		xoffset:"Horizontal Offset",
		yoffset:"Vertical Offset",
		whendone:"At the End",
		whiteboard:"Whiteboard",
		
	};

	// ADDON CORE
	var addon = {};

	// Defaults
	var slug = "revslider-whiteboard-addon";
	
	
	// INITIALISE THE ADDON
	RVS.DOC.on('revslider-whiteboard-addon_init',function() {
		
		// FIRST TIME INITIALISED			
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {
			
			// CREATE CONTAINERS
			RVS.F.addOnContainer.create({slug: slug, icon:"touch_app", title:bricks.whiteboard, alias:bricks.whiteboard, slider:true, slide:true, layer:true});

			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug), 
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('#form_slide_'+slug)
						};			

			//CHECK STRUCTURE ON SLIDER SETTNIGS
			updateSliderObjectsStructure();
			for (var i in RVS.selLayers) updateLayerObjectStructure({layerid:RVS.selLayers[i]});
			createSliderSettingsFields();
			createLayerSettingsFields();
			initListeners();
			extendLayerAnimations();
			initHelp();
			addon.initialised = true;				
		}

		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {
			//Update Basic Image Thumbnails
			updateWBImages({target:$('#writehand_image'), src:RVS.SLIDER.settings.addOns[slug].writehand.source});
			updateWBImages({target:$('#movehand_image'), src:RVS.SLIDER.settings.addOns[slug].movehand.source});
			//Update Input Fields in Slider Settings
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_sl_'+slug+', #gst_layer_'+slug,{display:"inline-block"});
			
			$('body').removeClass('whiteboard-disabled');
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('whiteboard_addon');
			
		} else {				
			// DISABLE THINGS
			removeDrawnHand();
			punchgs.TweenLite.set('#gst_sl_'+slug+', #gst_layer_'+slug,{display:"none"});
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");
			
			$('body').addClass('whiteboard-disabled');
			
			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('whiteboard_addon');
			
		}				
	});

	
	// UPDATE THE REVBUILDER SLIDER SETTINGS ADDONS OBJECT
	function updateSliderObjectsStructure() {
		
		// RVS.SLIDER.settings.addOns.whiteboard = RVS.SLIDER.settings.addOns.whiteboard!==undefined ? RVS.SLIDER.settings.addOns.whiteboard : { enable : true}
		RVS.SLIDER.settings.addOns[slug].writehand = RVS.SLIDER.settings.addOns[slug].writehand !== undefined ? RVS.SLIDER.settings.addOns[slug].writehand : { 
			width:572,
			height:691,
			originX:49,
			originY:50,
			source: RVS.ENV.wp_plugin_url+slug+"/assets/images/write_right_angle.png"
		};
		RVS.SLIDER.settings.addOns[slug].movehand = RVS.SLIDER.settings.addOns[slug].movehand !== undefined ? RVS.SLIDER.settings.addOns[slug].movehand : { 
			width:400,
			height:1000,
			originX:185,
			originY:66,
			source:RVS.ENV.wp_plugin_url+slug+"/assets/images/hand_point_right.png"			
		};
	}


	// UPDATE THE LAYER OBJEXT STRUCTURE (EXTEND WITH THE ATTRIBUTES WE NEED)
	function updateLayerObjectStructure(_) {			
		RVS.L[_.layerid].addOns[slug] = RVS.L[_.layerid].addOns[slug]===undefined ? {} :  RVS.L[_.layerid].addOns[slug];
		RVS.L[_.layerid].addOns[slug].enable = RVS.L[_.layerid].addOns[slug].enable===undefined ? false :  RVS.L[_.layerid].addOns[slug].enable;
		RVS.L[_.layerid].addOns[slug].hand = RVS.L[_.layerid].addOns[slug].hand!=undefined ? RVS.L[_.layerid].addOns[slug].hand : 
				{
					angle:15,
					angleRepeat:10,
					direction:"left_to_right",
					rotation:0,
					rotationAngle:0,
					mode:"write",
					gotoLayer:"off",
					type:"right",
					x:0,
					y:0
				};

		RVS.L[_.layerid].addOns[slug].jitter = RVS.L[_.layerid].addOns[slug].jitter!=undefined ? RVS.L[_.layerid].addOns[slug].jitter : 	
				{
					distance:80,
					distanceHorizontal:100,
					offset:10,
					offsetHorizontal:0,
					repeat:5
				};

	}

	function removeDrawnHand() {		
		if (addon.drawnlayers!==undefined) {
				if (RVS.H[addon.drawnlayers]!==undefined) {
					if (RVS.H[addon.drawnlayers].jitterarea) RVS.H[addon.drawnlayers].jitterarea.remove();
					if (RVS.H[addon.drawnlayers].whiteboard) RVS.H[addon.drawnlayers].whiteboard.remove();
				}
				addon.drawnlayers = undefined;
			}
	}

	//DRAW HAND IN EDITOR MODE
	function showHideWhiteBoardHand(force) {
		if (!RVS.SLIDER.settings.addOns[slug].enable) return;			
		if (RVS.eMode!==undefined && RVS.eMode.menu=="#form_layer_"+slug && RVS.eMode.top==="layer" && RVS.selLayers.length>0) {
						
			if (force || addon.drawnlayers!==RVS.selLayers[0]) removeDrawnHand();
			
			//CHECK IF LAYER ALREADY HAS ENABLE/DISABLE VALUES!?
			if (RVS.L[RVS.selLayers[0]].addOns[slug]===undefined) updateLayerObjectStructure({layerid:RVS.selLayers[0]});

			// CATCH LAYERS WE NEED TO DRAW			
			if (RVS.L[RVS.selLayers[0]].addOns[slug].enable) addon.drawnlayers= RVS.selLayers[0];
			
			// DRAW HAND ON LAYER					
			if (addon.drawnlayers!==undefined)	{
				var a = RVS.L[addon.drawnlayers].addOns[slug],
					g = a.hand.mode==="move" ? RVS.SLIDER.settings.addOns[slug].movehand : RVS.SLIDER.settings.addOns[slug].writehand;							
				RVS.H[addon.drawnlayers].jitterarea = a.hand.mode!=="move" ? $('<div class="editorwhiteboardjitter"></div>') : "";
				RVS.H[addon.drawnlayers].whiteboard = $('<div class="editorwhiteboardhand" style="width:'+parseInt(g.width,0)+'px; height:'+parseInt(g.height,0)+'px; background-image:url('+g.source+')"></div>');				
				RVS.H[addon.drawnlayers].w.append(RVS.H[addon.drawnlayers].whiteboard);
				punchgs.TweenLite.set(RVS.H[addon.drawnlayers].whiteboard, {rotationZ: (a.hand.mode==="move" ? parseInt(a.hand.rotation,0)+"deg":0) ,x: (a.hand.mode==="move" ? parseInt(a.hand.x,0)+"px":0), y: (a.hand.mode==="move" ?  parseInt(a.hand.y,0)+"px":0), transformOrigin:parseInt(g.originX,0)+"px "+parseInt(g.originY,0)+"px",  scaleX:(a.hand.type==="left" ? -1 : 1),  left:0-parseInt(g.originX,0), top:0-parseInt(g.originY,0)});
				if (a.hand.mode!=="move") {
					RVS.H[addon.drawnlayers].w.append(RVS.H[addon.drawnlayers].jitterarea);
					punchgs.TweenLite.set(RVS.H[addon.drawnlayers].jitterarea,{height:parseInt(a.jitter.distance,0)+"%", y:parseInt(a.jitter.offset,0)+"%"});
					if (a.hand.mode==="draw")
						punchgs.TweenLite.set(RVS.H[addon.drawnlayers].jitterarea,{width:parseInt(a.jitter.distanceHorizontal,0)+"%", x:parseInt(a.jitter.offsetHorizontal,0)+"%"});
				} 
			} else {
				removeDrawnHand();	
			}
		} else {
			removeDrawnHand();
		}
	}

	//EXTEND LAYER ANIMATIONS
	function extendLayerAnimations() {
		RVS.F.extendLayerAnimationLists({direction:"in", handle:"whiteboard", preset:{group:"Whiteboard", 
			transitions: {	
				"writequick":{name:"Write Quick", frame_0:{transform:{opacity:1},chars:{use:true,opacity:"0"}}, frame_1:{timeline:{speed:100}, transform:{opacity:1}, chars:{ease:"Power2.easeInOut",use:true,direction:"forward",delay:5,opacity:1}}},
				"writenormal":{name:"Write Normal", frame_0:{transform:{opacity:1},chars:{use:true,opacity:"0"}}, frame_1:{timeline:{speed:300}, transform:{opacity:1}, chars:{ease:"Power2.easeInOut",use:true,direction:"forward",delay:10,opacity:1}}},
				"writeslow":{name:"Write Slow", frame_0:{transform:{opacity:1},chars:{use:true,opacity:"0"}}, frame_1:{timeline:{speed:500}, transform:{opacity:1}, chars:{ease:"Power2.easeInOut",use:true,direction:"forward",delay:20,opacity:1}}},
				"writeltr":{name:"Write Left To Right", frame_0:{transform:{opacity:1},chars:{use:true,opacity:"0"}}, frame_1:{timeline:{speed:300}, transform:{opacity:1}, chars:{ease:"Power2.easeInOut",use:true,direction:"backward",delay:10,opacity:1}}},


				"drawfromleft":{name:"Draw from Left", frame_0:{transform:{opacity:0,x:"100%"}, mask:{use:true, x:"-100%"}}, frame_1:{timeline:{speed:1000}, transform:{opacity:1,x:0}, mask:{use:true,x:0}}},
				"drawfromright":{name:"Draw from Right", frame_0:{transform:{opacity:0,x:"-100%"}, mask:{use:true, x:"100%"}}, frame_1:{timeline:{speed:1000}, transform:{opacity:1,x:0}, mask:{use:true,x:0}}},
				"drawfromtop":{name:"Draw from Top", frame_0:{transform:{opacity:0,y:"100%"}, mask:{use:true, y:"-100%"}}, frame_1:{timeline:{speed:1000}, transform:{opacity:1,y:0}, mask:{use:true,y:0}}},
				"drawfrombottom":{name:"Draw from Bottom", frame_0:{transform:{opacity:0,y:"-100%"}, mask:{use:true, y:"100%"}}, frame_1:{timeline:{speed:1000}, transform:{opacity:1,y:0}, mask:{use:true,y:0}}},
				
				"movefromleft":{name:"Move from Left", frame_0:{transform:{opacity:1, x:"left"}}, frame_1:{timeline:{speed:1000,ease:"Power3.easeInOut"}, transform:{opacity:1, x:0}}},
				"movefromright":{name:"Move from Right", frame_0:{transform:{opacity:1, x:"right"}}, frame_1:{timeline:{speed:1000,ease:"Power3.easeInOut"}, transform:{opacity:1, x:0}}},
				"movefromtop":{name:"Move from Top", frame_0:{transform:{opacity:1, y:"top"}}, frame_1:{timeline:{speed:1000,ease:"Power3.easeInOut"}, transform:{opacity:1, y:0}}},
				"movefrombottom":{name:"Move from Bottom", frame_0:{transform:{opacity:1, y:"bottom"}}, frame_1:{timeline:{speed:1000,ease:"Power3.easeInOut"}, transform:{opacity:1, y:0}}}
				
		}}});
	}
	
	//UPDATE IMAGES IN WRITE AND MOVE HAND
	function updateWBImages(obj) { punchgs.TweenLite.set(obj.target,{"background-size":"contain", backgroundPosition:"center center", backgroundRepeat:"no-repeat",backgroundImage:'url('+obj.src+')'});}	

	// INITIALISE WHITEBOARD LISTENERS
	function initListeners() {

		RVS.DOC.on('redrawWBHand',function() {
			showHideWhiteBoardHand(true);
		});

		// UPDATE THE HAND IMAGE SOURCES
		RVS.DOC.on('updatehandimages',function() {
			updateWBImages({target:$('#writehand_image'), src:RVS.SLIDER.settings.addOns[slug].writehand.source});
			updateWBImages({target:$('#movehand_image'), src:RVS.SLIDER.settings.addOns[slug].movehand.source});
		});	
		// RESET THE HAND IMAGE SOURCES
		RVS.DOC.on('resetwritehandimage',function(e,p) {			
			RVS.F.updateSliderObj({path:"settings.addOns."+slug+".writehand.source",val:RVS.ENV.wp_plugin_url+slug+"/assets/images/write_right_angle.png"});
			updateWBImages({target:$('#writehand_image'), src:RVS.SLIDER.settings.addOns[slug].writehand.source});
			return false;
		});

		RVS.DOC.on('resetmovehandimage',function(e,p) {			
			RVS.F.updateSliderObj({path:"settings.addOns."+slug+".movehand.source",val:RVS.ENV.wp_plugin_url+slug+"/assets/images/hand_point_right.png"});
			updateWBImages({target:$('#movehand_image'), src:RVS.SLIDER.settings.addOns[slug].movehand.source});
			return false;
		});

		// LAYER ENABLED FOR WHITEBOARD
		RVS.DOC.on('enableWBonLayer',function() {			
			for (var i in RVS.selLayers) {
				updateLayerObjectStructure({layerid:RVS.selLayers[i]});
			}			
			RVS.F.updateEasyInputs({container:addon.forms.layergeneral, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
			showHideWhiteBoardHand(true);
		});

		// CLOSE THE MODAL WINDOW TO PICK ORIGIN
		RVS.DOC.on('click', '#rbm_addon_whiteboard .rbm_close', function() {
			addon.modalcross.draggable("destroy");
			RVS.F.RSDialog.close({modalid:'#rbm_addon_whiteboard'});
			punchgs.TweenLite.set('#rb_modal_underlay',{background:'rgba(50,52,56,0.05)'});
		});

		// EDITOR VIEW CHANGED, WE NEED TO MAYBE DRAW HAND AND STAFF
		RVS.DOC.on('editorViewModeChange.whiteboard',function() {
			showHideWhiteBoardHand();						
		});

		RVS.DOC.on('selectLayersDone.whiteboard',function() {				
			showHideWhiteBoardHand();
		});

		// UPDATE ORIGIN ON LIVE IMAGE
		RVS.DOC.on('updateWBOrigin',function(e,p) {
			
			// BUILD CONTAINER IF NOT BUILT YET
			if (addon.modal === undefined) {
				addon.modal = jQuery('<div class="_TPRB_ rb-modal-wrapper" data-modal="rbm_addon_whiteboard"><div id="rbm_addon_whiteboard" class="rb_modal form_inner"><div class="rbm_header"><i class="rbm_symbol material-icons">open_with</i><span class="rbm_title">'+bricks.pickorigin+'</span><i class="rbm_close material-icons">close</i></div><div id="wb_origin_picker_area"><i class="material-icons" id="wb_origin_pickerpin">control_camera</i><div id="wb_origin_image"></div></div></div>');
				jQuery('body').append(addon.modal);
				addon.modalimage = document.getElementById('wb_origin_image');
				addon.modalcross = $('#wb_origin_pickerpin');				
			}
			
			//CHANGE IMG IN CONTAINER
			addon.modalimage.innerHTML ='<img src="'+RVS.SLIDER.settings.addOns[slug][p].source+'" width="'+RVS.SLIDER.settings.addOns[slug][p].width+'"" height="'+RVS.SLIDER.settings.addOns[slug][p].height+'">';									
			addon.modalwb = addon.modal.find('#rbm_addon_whiteboard');
			addon.modalwb.width(RVS.SLIDER.settings.addOns[slug][p].width);
			addon.modalwb.height(RVS.SLIDER.settings.addOns[slug][p].height);			
			RVS.F.RSDialog.create({modalid:'#rbm_addon_whiteboard'});
			punchgs.TweenLite.set('#rb_modal_underlay',{background:'rgba(50,52,56,0.35)'});
			punchgs.TweenLite.set(addon.modalcross,{top:parseInt(RVS.SLIDER.settings.addOns[slug][p].originY,0)-13,left:parseInt(RVS.SLIDER.settings.addOns[slug][p].originX,0)-13});
			var ox = document.getElementById(p+'_origin_x'),
				oy = document.getElementById(p+'_origin_y');

			addon.modalcross.draggable({					
				cursor:"crosshair",
				drag: function(a,b) {					
					ox.value = b.position.left+13;
					oy.value = b.position.top+13;
				},
				stop: function(a,b) {
					RVS.F.updateSliderObj({path:"settings.addOns."+slug+"."+p+".originX",val:ox.value});
					RVS.F.updateSliderObj({path:"settings.addOns."+slug+"."+p+".originY",val:oy.value});
				}
			});			
		});

		RVS.DOC.on('SceneUpdatedAfterRestore.whiteboard',function() {
			$('.editorwhiteboardjitter').remove();
			$('.editorwhiteboardhand').remove();
			showHideWhiteBoardHand();
		});
	}


	// CREATE THE BASIC INPUT FIELDS FOR THE ADD ON
	function createLayerSettingsFields() {

		addon.defaults = {
			whiteboard_quick : {transition:"writequick", hand: { mode :"write", angle:15, angleRepeat:10}, jitter:{ repeat:15, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_normal :{transition:"writenormal", hand: { mode :"write", angle:10, angleRepeat:10}, jitter:{ repeat:12, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_slow : {transition:"writeslow", hand: { mode :"write", angle:20, angleRepeat:5}, jitter:{ repeat:10, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_wtr : {transition:"writeltr", hand: { mode :"write", angle:20, angleRepeat:5}, jitter:{ repeat:10, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},

			whiteboard_dfl : {transition:"drawfromleft", hand: { mode :"draw", angle:15, angleRepeat:10, direction:"left_to_right"}, jitter:{ repeat:15, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_dfr : {transition:"drawfromright", hand: { mode :"draw", angle:15, angleRepeat:10, direction:"right_to_left"}, jitter:{ repeat:15, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_dft : {transition:"drawfromtop", hand: { mode :"draw", angle:15, angleRepeat:10, direction:"top_to_bottom"}, jitter:{ repeat:15, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_dfb : {transition:"drawfrombottom", hand: { mode :"draw", angle:15, angleRepeat:10, direction:"bottom_to_top"}, jitter:{ repeat:15, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},

			whiteboard_mfl : {transition:"movefromleft", hand: { mode :"move", rotation:59, angle:15, angleRepeat:0, gotoLayer:"off"}, jitter:{ repeat:0, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_mfr : {transition:"movefromright", hand: { mode :"move", rotation:284, angle:15, angleRepeat:0, gotoLayer:"off"}, jitter:{ repeat:0, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_mft : {transition:"movefromtop", hand: { mode :"move", rotation:176, angle:15, angleRepeat:0, gotoLayer:"off"}, jitter:{ repeat:0, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},
			whiteboard_mfb : {transition:"movefrombottom", hand: { mode :"move", rotation:0, angle:15, angleRepeat:0, gotoLayer:"off"}, jitter:{ repeat:0, distance:100, distanceHorizontal:100, offset:0, offsetHorizontal:0}},


		}

		var _h,			
			plist = RVS.F.createPresets({ prefix:"whiteboard", title:"Whiteboard Presets",
				  groups: { write:{	title:"Write", elements:{ quick:{ title:"Write Quick"}, normal:{ title:"Write Normal"}, slow:{ title:"Wriet Slow"}, wtr:{ title:"Write Left To Right"}}},
							draw:{	title:"Draw", elements:{ dfl:{ title:"Draw from Left" }, dfr:{ title:"Draw from Right"}, dft:{ title:"Draw from Top"} , dfb:{ title:"Draw from Bottom"}}}, 				 
							move:{	title:"Move", elements:{ mfl:{ title:"Move from Left" }, mfr:{ title:"Move from Right"}, mft:{ title:"Move from Top"} , mfb:{ title:"Move from Bottom"}}}
						  },
				  onclick: function(key) {
					// HIDE HAND BEFORE CHANGE				  	
					RVS.F.openBackupGroup({id:"whiteboard",txt:"WhiteBoard Preset",icon:"touch_app"});
					var pre = RVS.S.slideId+".layers."+RVS.selLayers[0]+".addOns."+slug+".";
			
					// OVERALL
					RVS.F.updateSliderObj({path:pre+'hand.mode',val:addon.defaults[key].hand.mode});
					RVS.F.updateSliderObj({path:pre+'hand.angle',val:addon.defaults[key].hand.angle});
					RVS.F.updateSliderObj({path:pre+'hand.angleRepeat',val:addon.defaults[key].hand.angleRepeat});
					RVS.F.updateSliderObj({path:pre+'jitter.repeat',val:addon.defaults[key].jitter.repeat});
					RVS.F.updateSliderObj({path:pre+'jitter.distance',val:100,val:addon.defaults[key].jitter.distance});
					RVS.F.updateSliderObj({path:pre+'jitter.distanceHorizontal',val:100,val:addon.defaults[key].jitter.distanceHorizontal});
					RVS.F.updateSliderObj({path:pre+'jitter.offset',val:0,val:addon.defaults[key].jitter.offset});
					RVS.F.updateSliderObj({path:pre+'jitter.offsetHorizontal',val:0,val:addon.defaults[key].jitter.offsetHorizontal});

					// DRAW
					if (addon.defaults[key].hand.direction!==undefined )RVS.F.updateSliderObj({path:pre+'hand.direction',val:0,val:addon.defaults[key].hand.direction});

					// MOVE
					if (addon.defaults[key].hand.rotation!==undefined )RVS.F.updateSliderObj({path:pre+'hand.rotation',val:0,val:addon.defaults[key].hand.rotation});
					if (addon.defaults[key].hand.gotoLayer!==undefined )RVS.F.updateSliderObj({path:pre+'hand.gotoLayer',val:0,val:addon.defaults[key].hand.gotoLayer});

										
					RVS.F.updateEasyInputs({container:addon.forms.layergeneral, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
					showHideWhiteBoardHand(true);
					RVS.F.changeLayerAnimation({direction:"in", group:"whiteboard", transition: addon.defaults[key].transition, ignoreBackupGroup:true});
					RVS.F.closeBackupGroup({id:"whiteboard"});
				  }
				});
		_h = '<div class="form_inner_header"><i class="material-icons">touch_app</i>Whiteboard Functions</div><div  class="collapsable" style="display:block !important">'; 
		_h += '<label_a>'+bricks.active+'</label_a><input type="checkbox" class="layerinput easyinit callEvent" data-evt="enableWBonLayer" data-r="addOns.'+slug+'.enable" data-showhide=".whiteboard_layer_form" data-showhidedep="true" value="on">';
		_h +='<div class="whiteboard_layer_form">';
		_h += plist;
		_h += '<label_a>'+bricks.mode+'</label_a><select class="layerinput tos2 nosearchbox easyinit callEvent" data-evt="redrawWBHand" data-r="addOns.'+slug+'.hand.mode" data-show=".wbo_*val*" data-hide=".whiteboardoptions"><option value="write">'+bricks.write+'</option><option value="draw">'+bricks.draw+'</option><option value="move">'+bricks.move+'</option></select>';
		_h += '<label_a>'+bricks.hand+'</label_a><select class="layerinput tos2 nosearchbox easyinit callEvent" data-evt="redrawWBHand" data-r="addOns.'+slug+'.hand.type"><option value="right">'+bricks.right+'</option><option value="left">'+bricks.left+'</option></select>';
		_h += '<div class="whiteboardoptions wbo_draw"><label_a>'+bricks.direction+'</label_a><select class="layerinput tos2 nosearchbox easyinit callEvent" data-evt="redrawWBHand" data-r="addOns.'+slug+'.hand.direction"><option value="left_to_right">'+bricks.ltr+'</option><option value="right_to_left">'+bricks.rtl+'</option><option value="top_to_bottom">'+bricks.ttb+'</option><option value="bottom_to_top">'+bricks.btt+'</option></select></div>';
		_h += '<div class="whiteboardoptions wbo_draw wbo_write"><label_a>'+bricks.whendone+'</label_a><select class="layerinput tos2 nosearchbox easyinit callEvent" data-evt="redrawWBHand" data-r="addOns.'+slug+'.hand.gotoLayer"><option value="on">'+bricks.gotonextlayer+'</option><option value="off">'+bricks.hidehand+'</option></select></div>';
		_h += '</div>'
		_h += '</div>'
		_h +='<div class="whiteboard_layer_form">';
		_h += '<div class="form_inner_header"><i class="material-icons">pan_tool</i>Whiteboard Hand Options</div><div  class="collapsable" style="display:block !important">'; 				
		
		// HAND ANGLE
		_h += '<longoption class="whiteboardoptions wbo_draw wbo_write"><i class="material-icons">rotate_90_degrees_ccw</i><label_a>'+bricks.handangle+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.hand.angle" data-min="-360" data-max="360" type="text"></longoption>';
		_h += '<longoption class="whiteboardoptions wbo_draw wbo_write"><i class="material-icons">shuffle</i><label_a>'+bricks.handanglerepeat+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.hand.angleRepeat" data-min="-100" data-max="100" type="text"></longoption>';	
		
		// JITTER		
		_h += '<longoption class="whiteboardoptions wbo_draw wbo_write"><i class="material-icons">swap_vert</i><label_a>'+bricks.jitteringdistance+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.jitter.distance" data-min="-400" data-max="400" type="text"></longoption>';
		_h += '<longoption class="whiteboardoptions wbo_draw wbo_write"><i class="material-icons">vertical_align_bottom</i><label_a>'+bricks.jitteringoffset+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.jitter.offset" data-min="-500" data-max="500" type="text"></longoption>';
		_h += '<longoption class="whiteboardoptions wbo_draw"><i class="material-icons">swap_horiz</i><label_a>'+bricks.jitteringdistancehor+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.jitter.distanceHorizontal" data-min="-400" data-max="400" type="text"></longoption>';
		_h += '<longoption class="whiteboardoptions wbo_draw"><i class="material-icons">keyboard_tab</i><label_a>'+bricks.jitteringoffsethor+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.jitter.offsetHorizontal" data-min="-500" data-max="500" type="text"></longoption>';
		_h += '<longoption class="whiteboardoptions wbo_draw wbo_write"><i class="material-icons">repeat_one</i><label_a>'+bricks.jitteringrepeat+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.jitter.repeat" data-min="-100" data-max="100" type="text"></longoption>';
		
		
		
		// HAND ROTATION AND MOVE
		_h += '<row class="direktrow whiteboardoptions wbo_move">';
		_h += '<onelong><label_icon class="ui_rotatez"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.hand.rotation" data-min="-360" data-max="360" type="text"></onelong>';
		_h += '<oneshort></oneshort>';
		_h += '</row>'
		_h += '<row class="direktrow whiteboardoptions wbo_move">';
		_h += '<onelong><label_icon class="ui_x"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.hand.x" type="text"></onelong>';
		_h += '<oneshort><label_icon class="ui_y"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="redrawWBHand" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.hand.y" type="text"></oneshort>';
		_h += '</row>';
				
		_h += '</div>'
		_h += '</div>'

		addon.forms.layergeneral.append($(_h));
		addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:"Select From List"
		});
		RVS.F.initOnOff();
		
		$('.whiteboard_layer_form .presets_liste').attr('data-helpkey', 'whiteboard-presets');
		
	}


	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {
		if (!addon.slidersettings) {
						
			var _h;

			// WRITE HAND IMAGE
			_h = '<div class="form_inner_header"><i class="material-icons">edit</i>'+bricks.writehand+'</div><div  class="collapsable" style="display:block !important">'; 
			_h +='<row>';
			_h += ' <onelong><label_a>'+bricks.preview+'</label_a><div class="miniprevimage_wrap"><div id="writehand_image"></div></div></onelong>';
			_h += '<oneshort>';
			_h += '		<div data-evt="updatehandimages" data-r="settings.addOns.'+slug+'.writehand.source" class="getImageFromMediaLibrary basic_action_button callEventButton"><i class="material-icons">folder</i>'+RVS_LANG.select+'</div>';
			_h += '		<div data-evt="resetwritehandimage" class="callEventButton basic_action_button"><i class="material-icons">update</i>'+RVS_LANG.reset+'</div>';			
			_h += '	</oneshort>';			
			_h += '</row>';			
			_h += '<div class="div20"></div>'
			

			// WRITEHAND SIZE
			_h += '<row class="directrow">';
			_h += '<onelong><label_icon class="ui_width"></label_icon><input data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.writehand.width" type="text" class="sliderinput smallinput easyinit"></onelong>';
			_h += '<oneshort><label_icon class="ui_height"></label_icon><input data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.writehand.height" type="text" class="sliderinput smallinput easyinit"></oneshort>';
			_h += '</row>';		


			// WRITEHAND ORIGIN
			_h += '<row class="direktrow">';
			_h += '	<onelong><label_icon class="ui_origox"></label_icon><input class="sliderinput valueduekeyboard smallinput easyinit" id="writehand_origin_x" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.writehand.originX" data-min="-3600" data-max="3600" type="text"></onelong>';
			_h += '	<oneshort><label_icon class="ui_origoy"></label_icon><input class="sliderinput valueduekeyboard smallinput easyinit" id="writehand_origin_y" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.writehand.originY" data-min="-3600" data-max="3600" type="text"></oneshort>	';							
			_h += '</row>';
			_h += '<label_a></label_a><div data-evt="updateWBOrigin" data-evtparam="writehand" class="basic_action_button callEventButton longbutton"><i class="material-icons">open_with</i>'+bricks.pickorigin+'</div>';			
			_h += '<div class="div20"></div>'
			_h += '</div>'; // END OF COLLAPSABLE
			

			_h += '<div class="form_inner_header"><i class="material-icons">pan_tool</i>'+bricks.movehand+'</div><div  class="collapsable" style="display:block !important">'; 
			// MOVE HAND IMAGE			
			_h += '<row>';
			_h += ' <onelong><label_a>'+bricks.preview+'</label_a><div class="miniprevimage_wrap"><div id="movehand_image"></div></div></onelong>';
			_h += '<oneshort>';
			_h += '		<div data-evt="updatehandimages" data-r="settings.addOns.'+slug+'.movehand.source" class="getImageFromMediaLibrary basic_action_button callEventButton"><i class="material-icons">folder</i>'+RVS_LANG.select+'</div>';
			_h += '		<div data-evt="resetmovehandimage" class="basic_action_button callEventButton"><i class="material-icons">update</i>'+RVS_LANG.reset+'</div>';
			_h += '	</oneshort>';
			_h += '</row>';
			_h += '<div class="div20"></div>'

			// MOVEHAND SIZE
			_h += '<row class="directrow">';
			_h += '<onelong><label_icon class="ui_width"></label_icon><input data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.movehand.width" type="text" class="sliderinput smallinput easyinit"></onelong>';
			_h += '<oneshort><label_icon class="ui_height"></label_icon><input data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.movehand.height" type="text" class="sliderinput smallinput easyinit"></oneshort>';
			_h += '</row>';			

			// movehand ORIGIN
			_h += '<row class="direktrow">';
			_h += '	<onelong><label_icon class="ui_origox"></label_icon><input class="sliderinput valueduekeyboard smallinput easyinit" id="movehand_origin_x" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.movehand.originX" data-min="-3600" data-max="3600" type="text"></onelong>';
			_h += '	<oneshort><label_icon class="ui_origoy"></label_icon><input class="sliderinput valueduekeyboard smallinput easyinit" id="movehand_origin_y" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.movehand.originY" data-min="-3600" data-max="3600" type="text"></oneshort>	';							
			_h += '</row>';
			_h += '<label_a></label_a><div data-evt="updateWBOrigin" data-evtparam="movehand" class="basic_action_button callEventButton longbutton"><i class="material-icons">open_with</i>'+bricks.pickorigin+'</div>';
			_h += '<div class="div20"></div>'			
			_h += '</div>'; // END OF COLLAPSABLE			
					
			addon.forms.slidergeneral.append($(_h));
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});
			
		}
	}
	
	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(typeof HelpGuide !== 'undefined' && typeof revslider_whiteboard_addon !== 'undefined' && revslider_whiteboard_addon.hasOwnProperty('help')) {
		
			var obj = {slug: 'whiteboard_addon'};
			$.extend(true, obj, revslider_whiteboard_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}

})( jQuery );