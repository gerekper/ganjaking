///////////////////////////////
//	 INIT WHITEBOARD SCRIPTS //
/////////////////////////////// 
jQuery("document").ready(function() {
	
	// CHECK EDITOR MODE
	var editor_view = jQuery('#form_slider_params').length>0 ? "slider_settings" : "slide_settings";
	
	if (editor_view==="slide_settings"){
		wbLayerSettingsInit();
	}else if (editor_view==="slider_settings"){
		wbSliderSettingsInit();
	}
});



/********************************************************************

	LAYER / SLIDE SETTINGS BACKEND jQUERY EXTENSION

**********************************************************************/

var wbLayerSettingsInit = function() {
	if(typeof(wb_loaded) === 'undefined') return false; //WILL BE WRITTEN BY admin/includes/slide.class.php DEPENDING IF WHITEBOARD IS ENABLED/DISABLED IN SLIDER SETTINGS
	if(wb_loaded === false) return false; //WILL BE WRITTEN BY admin/includes/slide.class.php DEPENDING IF WHITEBOARD IS ENABLED/DISABLED IN SLIDER SETTINGS
	
	// SET UP DEFAULT CHANGES ON FIELDS, SELECT BOXES
	wbLayerGuiDefaultHandlings();
	// SET UP CALLBACKS
	wbLayerCallBacks();

	// SHOW / HIDE HAND ON SELECTED LAYER
	jQuery('#rs-addon-trigger-whiteboard').click(wbShowHideHandonLayer);
	jQuery('#wb-hand_function').on("change",wbShowHideHandonLayer);

	UniteLayersRev.attributegroups.push({id:"whiteboard",icon:"up-hand", groupname:"WhiteBoard", keys:["whiteboard.jitter_distance","whiteboard.jitter_offset","whiteboard.jitter_distance_horizontal","whiteboard.jitter_offset_horizontal","whiteboard.jitter_repeat","whiteboard.hand_angle","whiteboard.hand_angle_repeat","whiteboard.hand_gotolayer","whiteboard.hand_function","whiteboard.hand_gotolayer","whiteboard.jitter_distance","whiteboard.jitter_repeat","whiteboard.jitter_offset","whiteboard.hand_angle","whiteboard.hand_angle_repeat","whiteboard.jitter_distance_horizontal","whiteboard.jitter_offset_horizontal","whiteboard.hand_full_rotation","whiteboard.hand_x_offset","whiteboard.hand_y_offset","whiteboard.hand_function","whiteboard.hand_gotolayer","whiteboard.jitter_distance","whiteboard.jitter_offset","whiteboard.hand_type","whiteboard.hand_direction","whiteboard.hand_type"]});
}



var wbLayerCallBacks = function() {
	// DEFINE CALLBACKS ON INTERNAL FUNCTIONS
	var call_wb_updateLayerFromFields_Core = {
		callback : function(obj) {					
			obj.whiteboard = obj.whiteboard || {};
			obj.whiteboard.hand_function = jQuery('#wb-hand_function').val();
			obj.whiteboard.hand_type = jQuery('#wb-hand_type').val();
			obj.whiteboard.hand_direction = jQuery('#wb-hand_direction').val();	
			obj.whiteboard.hand_gotolayer = jQuery('#wb-gotolayer').val();			
			obj.whiteboard.jitter_distance = jQuery('#wb-hand_jitter_distance').val();
			obj.whiteboard.jitter_distance_horizontal = jQuery('#wb-hand_jitter_distance_horizontal').val();
			obj.whiteboard.jitter_repeat = jQuery('#wb-hand_jitter_repeat').val();
			obj.whiteboard.jitter_offset = jQuery('#wb-hand_jitter_offset').val();
			obj.whiteboard.jitter_offset_horizontal = jQuery('#wb-hand_jitter_offset_horizontal').val();
			obj.whiteboard.hand_angle = jQuery('#wb-hand_angle').val();
			obj.whiteboard.hand_angle_repeat = jQuery('#wb-hand_angle_repeat').val();
			obj.whiteboard.hand_x_offset = jQuery('#wb-hand_x_offset').val();
			obj.whiteboard.hand_y_offset = jQuery('#wb-hand_y_offset').val();
			obj.whiteboard.hand_full_rotation = jQuery('#wb-hand_full_rotation').val();			
			obj.whiteboard.hand_full_rotation_angle = jQuery('#wb-hand_full_rotation_angle').val();
			return obj;
		},		
		environment : "updateLayerFromFields_Core",
		function_position : "start"
	};

	var call_wb_updateLayerFormFields = {
		callback : function(objLayer) {		
				
			if (objLayer.type!="text") {
				jQuery('#wb-hand_function option[value="write"]').hide();
				jQuery('#wb-hand_preset .write-preset').hide();
			} else {
				jQuery('#wb-hand_function option[value="write"]').show();
				jQuery('#wb-hand_preset .write-preset').show();
			}

			if (objLayer.whiteboard==undefined) {
				objLayer.whiteboard = {};
				objLayer.whiteboard.hand_function = "off";	
				objLayer.whiteboard.hand_type = "right";			
			}
			
			var obj = objLayer.whiteboard.hand_function=="write" || objLayer.whiteboard.hand_function=="draw" ? wb_writehand_sources : objLayer.whiteboard.hand_function=="move" ? wb_movehand_sources : ""; 
			jQuery('#wb-hand_function').val(objLayer.whiteboard.hand_function);	
			wbShowHideOptions(objLayer.whiteboard.hand_function);

			if (objLayer.whiteboard.hand_function!=="off") {					 
				jQuery('#wb-hand_type').val(objLayer.whiteboard.hand_type===undefined ?  obj.handtype : objLayer.whiteboard.hand_type);
				jQuery('#wb-hand_jitter_distance').val(objLayer.whiteboard.jitter_distance === undefined ? obj.jitter : objLayer.whiteboard.jitter_distance );
				jQuery('#wb-hand_jitter_distance_horizontal').val(objLayer.whiteboard.jitter_distance_horizontal === undefined ? obj.jitter_offset_horizontal : objLayer.whiteboard.jitter_distance_horizontal );
				jQuery('#wb-hand_jitter_repeat').val(objLayer.whiteboard.jitter_repeat === undefined ? obj.jitter_repeat : objLayer.whiteboard.jitter_repeat );
				jQuery('#wb-hand_jitter_offset').val(objLayer.whiteboard.jitter_offset === undefined ? obj.jitter_offset : objLayer.whiteboard.jitter_offset );
				jQuery('#wb-hand_jitter_offset_horizontal').val(objLayer.whiteboard.jitter_offset_horizontal === undefined ? obj.jitter_offset_horizontal : objLayer.whiteboard.jitter_offset_horizontal );
				jQuery('#wb-hand_angle').val(objLayer.whiteboard.hand_angle === undefined ? obj.angle : objLayer.whiteboard.hand_angle );
				jQuery('#wb-hand_angle_repeat').val(objLayer.whiteboard.hand_angle_repeat === undefined ? obj.angle_repeat : objLayer.whiteboard.hand_angle_repeat );
				jQuery('#wb-hand_x_offset').val(objLayer.whiteboard.hand_x_offset === undefined ? 0 : objLayer.whiteboard.hand_x_offset);
				jQuery('#wb-hand_y_offset').val(objLayer.whiteboard.hand_y_offset === undefined ? 0 : objLayer.whiteboard.hand_y_offset);
				jQuery('#wb-hand_direction').val(objLayer.whiteboard.hand_direction === undefined ? obj.direction : objLayer.whiteboard.hand_direction);
				jQuery('#wb-hand_full_rotation').val(objLayer.whiteboard.hand_full_rotation  === undefined ? 0 : objLayer.whiteboard.hand_full_rotation );
				jQuery('#wb-hand_full_rotation_angle').val(objLayer.whiteboard.hand_full_rotation_angle  === undefined ? 0 : objLayer.whiteboard.hand_full_rotation_angle );
				jQuery('#wb-gotolayer').val(objLayer.whiteboard.hand_gotolayer  === undefined ? "off" : objLayer.whiteboard.hand_gotolayer );
					
			}		
			return objLayer;
		},		
		environment : "updateLayerFormFields",
		function_position : "start"
	};

	var call_wb_setLayerSelected = {
		callback : function(serial) {			
			wbShowHideHandonLayer();
			return;
		},		
		environment : "setLayerSelected",
		function_position : "start"
	};

	var call_wb_unselectHtmlLayers = {
		callback : function(serial) {			
			jQuery('.slide_layer #wb-editor-handlayer').remove();
			jQuery('.slide_layer #write_jitter_part').remove();
			return;
		},		
		environment : "unselectHtmlLayers",
		function_position : "end"

	};


	// ADD CALLBACKS
	UniteLayersRev.addon_callbacks.push(call_wb_unselectHtmlLayers);
	UniteLayersRev.addon_callbacks.push(call_wb_setLayerSelected);
	UniteLayersRev.addon_callbacks.push(call_wb_updateLayerFormFields);
	UniteLayersRev.addon_callbacks.push(call_wb_updateLayerFromFields_Core);
}



var  getRotationDegrees = function(obj) {
	    var matrix = obj.css("-webkit-transform") ||
	    obj.css("-moz-transform")    ||
	    obj.css("-ms-transform")     ||
	    obj.css("-o-transform")      ||
	    obj.css("transform");
	    if(matrix !== 'none') {
	        var values = matrix.split('(')[1].split(')')[0].split(',');
	        var a = values[0];
	        var b = values[1];
	        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
	    } else { var angle = 0; }
	    return (angle < 0) ? angle +=360 : angle;
	}



// SHOW HIDE HAND ON SELECTED LAYER
var wbShowHideHandonLayer = function() {
	
	var htmllayer = jQuery('#divLayers .layer_selected');
	
	jQuery('.slide_layer #wb-editor-handlayer').remove();
	jQuery('.slide_layer #write_jitter_part').remove();


	if (jQuery('#wb-hand_function').val()!="off" && jQuery('.rs-addon-tab-button').hasClass("wb-selected") && jQuery('#rs-addon-trigger-whiteboard').hasClass("selected") && htmllayer.length>0) {	
		htmllayer.append('<div id="wb-editor-handlayer"><div id="wb-editor-handscaler"><div id="wb-editr-handlayer-img"><div id="wb-origin-cross"></div></div></div></div>');
		var hl = htmllayer.find('#wb-editor-handlayer'),
			hs = htmllayer.find('#wb-editor-handscaler'),
			hli = htmllayer.find('#wb-editr-handlayer-img'),
			orig = htmllayer.find('#wb-origin-cross'),
			hand_function = jQuery("#wb-hand_function").val(),
			obj = hand_function=="write" || hand_function=="draw" ? wb_writehand_sources : hand_function=="move" ? wb_movehand_sources : {}; 
	

		if (jQuery('#wb-hand_type').val()=="left") 
			punchgs.TweenLite.set(hs,{scaleX:"-1"});
		hli.css({width:obj.width+"px",height:obj.height+"px",backgroundImage:"url("+obj.src+")"});
		punchgs.TweenLite.set(hli,{x:(0-obj.origin_x), y:(0-obj.origin_y)});
		orig.css({left:obj.origin_x+"px",top:obj.origin_y+"px"});

		var jitter = jQuery('#wb-hand_jitter_distance').val(),
			jitter_y = jQuery('#wb-hand_jitter_offset').val(),
			jitter_h = jQuery('#wb-hand_jitter_distance_horizontal').val(),
			jitter_x = jQuery('#wb-hand_jitter_offset_horizontal').val();
				
		if (jitter!=0 && hand_function!="move") {
			htmllayer.append('<div id="write_jitter_part"></div>');
			var wjp = jQuery('#write_jitter_part'),
				bv = htmllayer.hasClass("slide_layer_type_text") ? parseInt(htmllayer.find('.tp-caption').css("line-height"),0) : htmllayer.height(),
				bh = htmllayer.width(),
				h = bv*(jitter/100),
				mt = bv*(jitter_y/100),
				w = bh*(jitter_h/100),
				ml = bh*(jitter_x/100);

			if (hand_function==="draw")
				wjp.css({height:h+"px",top:mt+"px", width:w+"px",left:ml+"px"});
			else
			if (hand_function==="write")
				wjp.css({height:h+"px",top:mt+"px"});
		}
		else 
		if (hand_function==="move") {
			punchgs.TweenLite.set(hl,{left:parseInt(jQuery('#wb-hand_x_offset').val(),0), top:parseInt(jQuery('#wb-hand_y_offset').val(),0)});
			hl.draggable({
				drag:function() {
					var pos = jQuery('#wb-editor-handlayer').position();
					jQuery('#wb-hand_x_offset').val(pos.left);
					jQuery('#wb-hand_y_offset').val(pos.top);
				},
				stop:function() {
					jQuery('#wb-hand_x_offset').change();
					jQuery('#wb-hand_y_offset').change();
				}
			});

			var cr = parseFloat(jQuery('#wb-hand_full_rotation_angle').val());			

			hl.rotatable({
				angle:cr,
				create:function(event,ui) {
						jQuery('#wb-editor-handlayer .ui-rotatable-handle.ui-draggable').css({top:(10-obj.origin_y),right:(-10-obj.width)/2+"px"})
				},
				rotate:function(event,ui) {
					jQuery('#wb-hand_full_rotation').val(getRotationDegrees(ui.element));
					jQuery('#wb-hand_full_rotation_angle').val(ui.angle.current);
				},
				stop:function(event,ui) {
					jQuery('#wb-hand_full_rotation').change();
					jQuery('#wb-hand_full_rotation_angle').change();
				}
			});	
		}
	} else {		
		jQuery('#wb-editor-handlayer').remove();
	}
}



var wbShowHideOptions = function(hand_function) {
	jQuery("#wb_direction-wrapper").show();
	jQuery("#wb-angle-wrapper").show();
	jQuery("#wb-jitter-wrapper").show();
	jQuery("#wb-full-rotation-wrapper").show();
	jQuery('#wb-hand_type_wrapper').show();
	jQuery('#wb_xy_offset_wrapper').show();
	jQuery('#wb-goto_wrapper').show();
	jQuery('#wb-jitter-wrapper-horizontal').hide();
	
	switch (hand_function) {
		case "off":
			jQuery("#wb_direction-wrapper").hide();
			jQuery("#wb-angle-wrapper").hide();
			jQuery("#wb-jitter-wrapper").hide();
			jQuery("#wb-full-rotation-wrapper").hide();
			jQuery('#wb-hand_type_wrapper').hide();
			jQuery('#wb_xy_offset_wrapper').hide();
			jQuery('#wb-goto_wrapper').hide();
		break;
		case "write":
			jQuery("#wb_direction-wrapper").hide();
			jQuery("#wb-full-rotation-wrapper").hide();
			jQuery('#wb_xy_offset_wrapper').hide();
		break;
		case "draw":
			jQuery("#wb-full-rotation-wrapper").hide();
			jQuery('#wb_xy_offset_wrapper').hide();
			jQuery('#wb-jitter-wrapper-horizontal').show();
		break;
		case "move":
			jQuery("#wb_direction-wrapper").hide();
			jQuery("#wb-angle-wrapper").hide();
			jQuery("#wb-jitter-wrapper").hide();	
			jQuery('#wb-goto_wrapper').hide();		
		break;
	}
}
 
var wbLayerGuiDefaultHandlings = function() {
	// Selected Options Hide/Show Not used Fields
	jQuery("#wb-hand_function").on("change",function() {
		var hand_function = jQuery(this).val();
		
		wbShowHideOptions(hand_function);		

		var obj = hand_function=="write" || hand_function=="draw" ? wb_writehand_sources : hand_function=="move" ? wb_movehand_sources : ""; 
	
		if (hand_function!=="off") {								
			jQuery('#wb-hand_type').val(obj.handtype);
			jQuery('#wb-direction').val(obj.direction);
			jQuery('#wb-hand_jitter_distance').val(obj.jitter);
			jQuery('#wb-hand_jitter_distance_horizontal').val(obj.jitter_horizontal);
			jQuery('#wb-hand_jitter_repeat').val(obj.jitter_repeat);
			jQuery('#wb-hand_jitter_offset').val(obj.jitter_offset);
			jQuery('#wb-hand_jitter_offset_horizontal').val(obj.jitter_offset_horizontal);
			jQuery('#wb-hand_angle').val(obj.angle);
			jQuery('#wb-hand_angle_repeat').val(obj.angle_repeat);
			jQuery('#wb-hand_x_offset').val(0);
			jQuery('#wb-hand_y_offset').val(0);
			jQuery('#wb-hand_full_rotation').val(0);
			jQuery('#wb-hand_full_rotation_angle').val(0);
			jQuery('#wb-gotolayer').val("off");
			
		}	

	});
	jQuery("#wb-hand_function").change();
	jQuery('#wb-hand_type, #wb-hand_jitter_distance,#wb-hand_jitter_distance_horizontal,#wb-hand_jitter_offset,#wb-hand_jitter_offset_horizontal, #wb-hand_full_rotation').change(wbShowHideHandonLayer);
	jQuery('.rs-layer-settings-tabs li').click(function() {
		if (jQuery(this).data("content")!="#rs-addon-wrapper")
			jQuery('.rs-addon-tab-button').removeClass("wb-selected");
		else 
			jQuery('.rs-addon-tab-button').addClass("wb-selected");
		wbShowHideHandonLayer();
	});



	jQuery('#wb-hand_preset').change(function() {
		switch (jQuery(this).val()) {
			case "write_quick":	
				jQuery('#wb-hand_function').val("write").change();											
				jQuery('#wb-hand_jitter_repeat').val(15);				
				jQuery('#wb-hand_angle').val(15);
				jQuery('#wb-hand_angle_repeat').val(10);
				jQuery('#layer_split').val("chars");
				jQuery('#layer_animation').val("tp-fade").change();	
				jQuery('#layer_speed').val(100);
				jQuery('#layer_splitdelay').val(5);		
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
			break;
			case "write_normal":
				jQuery('#wb-hand_function').val("write").change();
				jQuery('#wb-hand_jitter_repeat').val(12);				
				jQuery('#wb-hand_angle').val(10);
				jQuery('#wb-hand_angle_repeat').val(10);
				jQuery('#layer_split').val("chars");
				jQuery('#layer_animation').val("tp-fade").change();	
				jQuery('#layer_speed').val(300);
				jQuery('#layer_splitdelay').val(10);	
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
			break;
			case "write_slow":
				jQuery('#wb-hand_function').val("write").change();
				jQuery('#wb-hand_jitter_repeat').val(10);				
				jQuery('#wb-hand_angle').val(20);
				jQuery('#wb-hand_angle_repeat').val(5);
				jQuery('#layer_split').val("chars");
				jQuery('#layer_animation').val("tp-fade").change();	
				jQuery('#layer_speed').val(500);
				jQuery('#layer_splitdelay').val(20);	
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
			break;
			case "draw_from_left":
				jQuery('#wb-hand_function').val("draw").change();											
				jQuery('#wb-hand_jitter_repeat').val(15);				
				jQuery('#wb-hand_angle').val(15);
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(10);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("custom").change();	
				jQuery('#wb-hand_jitter_distance').val(100);				
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				jQuery('#layer_anim_xstart').val("[100%]");
				jQuery('#masking-start').attr("checked","checked").change();
				jQuery('#mask_anim_xstart').val("[-100%]");
				jQuery('#wb-hand_direction').val("left_to_right");
			break;
			case "draw_from_right":
				jQuery('#wb-hand_function').val("draw").change();											
				jQuery('#wb-hand_jitter_repeat').val(15);				
				jQuery('#wb-hand_angle').val(15);
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(10);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("custom").change();	
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				jQuery('#layer_anim_xstart').val("[-100%]");
				jQuery('#masking-start').attr("checked","checked").change();
				jQuery('#mask_anim_xstart').val("[100%]");
				jQuery('#wb-hand_direction').val("right_to_left");
			break;
			case "draw_from_top":
				jQuery('#wb-hand_function').val("draw").change();											
				jQuery('#wb-hand_jitter_repeat').val(15);				
				jQuery('#wb-hand_angle').val(15);
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(10);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("custom").change();	
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				jQuery('#layer_anim_ystart').val("[100%]");
				jQuery('#masking-start').attr("checked","checked").change();
				jQuery('#mask_anim_ystart').val("[-100%]");
				jQuery('#wb-hand_direction').val("top_to_bottom");
			break;
			case "draw_from_bottom":
				jQuery('#wb-hand_function').val("draw").change();											
				jQuery('#wb-hand_jitter_repeat').val(15);				
				jQuery('#wb-hand_angle').val(15);
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(10);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("custom").change();	
				jQuery('#wb-hand_jitter_distance').val(100);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				jQuery('#layer_anim_ystart').val("[-100%]");
				jQuery('#masking-start').attr("checked","checked").change();
				jQuery('#mask_anim_ystart').val("[100%]");
				jQuery('#wb-hand_direction').val("bottom_to_top");
			break;
			case "move_from_left":
				jQuery('#wb-gotolayer').val("off");
				jQuery('#wb-hand_function').val("move").change();											
				jQuery('#wb-hand_jitter_repeat').val(0);				
				jQuery('#wb-hand_angle').val(0);
				jQuery('#wb-hand_full_rotation').val(59);
				jQuery('#wb-hand_full_rotation_angle').val(1.0348695207653544).change();
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(0);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("lfl").change();	
				jQuery('#wb-hand_jitter_distance').val(0);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				punchgs.TweenLite.set(jQuery('#wb-editor-handlayer'),{rotation:jQuery('#wb-hand_full_rotation').val()})
				
			break;
			case "move_from_right":
				jQuery('#wb-gotolayer').val("off");
				jQuery('#wb-hand_function').val("move").change();											
				jQuery('#wb-hand_jitter_repeat').val(0);				
				jQuery('#wb-hand_angle').val(0);
				jQuery('#wb-hand_full_rotation').val(284);
				jQuery('#wb-hand_full_rotation_angle').val(-1.33370190346773).change();
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(0);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("lfr").change();	
				jQuery('#wb-hand_jitter_distance').val(0);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				punchgs.TweenLite.set(jQuery('#wb-editor-handlayer'),{rotation:jQuery('#wb-hand_full_rotation').val()})
			break;
			case "move_from_top":
				jQuery('#wb-gotolayer').val("off");
				jQuery('#wb-hand_function').val("move").change();											
				jQuery('#wb-hand_jitter_repeat').val(0);				
				jQuery('#wb-hand_angle').val(0);
				jQuery('#wb-hand_full_rotation').val(176);
				jQuery('#wb-hand_full_rotation_angle').val(3.0731666507227318).change();
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(0);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("lft").change();	
				jQuery('#wb-hand_jitter_distance').val(0);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				punchgs.TweenLite.set(jQuery('#wb-editor-handlayer'),{rotation:jQuery('#wb-hand_full_rotation').val()})
			break;
			case "move_from_bottom":
				jQuery('#wb-gotolayer').val("off");
				jQuery('#wb-hand_function').val("move").change();											
				jQuery('#wb-hand_jitter_repeat').val(0);				
				jQuery('#wb-hand_angle').val(0);
				jQuery('#wb-hand_full_rotation').val(0);
				jQuery('#wb-hand_full_rotation_angle').val(0).change();
				jQuery('#layer_split').val("none");
				jQuery('#wb-hand_angle_repeat').val(0);				
				jQuery('#layer_animation').val("noanim").change();	
				jQuery('#layer_animation').val("lfb").change();	
				jQuery('#wb-hand_jitter_distance').val(0);
				jQuery('#wb-hand_jitter_offset').val(0);
				jQuery('#wb-hand_jitter_distance_horizontal').val(100);
				jQuery('#wb-hand_jitter_offset_horizontal').val(0);
				punchgs.TweenLite.set(jQuery('#wb-editor-handlayer'),{rotation:jQuery('#wb-hand_full_rotation').val()})
			break;			
		}
		UniteAdminRev.showInfo({type: 'info', hideon: '', event: '', content: "Layer WhiteBoard Effect Set to:"+jQuery(this).find('option[value="'+jQuery(this).val()+'"]').html(), hidedelay: 3});

		jQuery('#wb-hand_preset').val("none");
		UniteLayersRev.updateLayerFromFields();
	})
}




/********************************************************************

	SLIDER SETTINGS BACKEND jQUERY EXTENSION

**********************************************************************/

var wbSliderSettingsInit = function() {
	// SET UP DEFAULT CHANGES ON FIELDS, SELECT BOXES
	wbSliderSettingsGuiDefaultHandlings();

}

var wbLoadAndSetHandDimensions = function(env,src) {

	var img = new Image();			
	img.onload = function() {
		jQuery('#wb_'+env+'hand_width').val(this.width);
		jQuery('#wb_'+env+'hand_height').val(this.height);		
	}
	img.onerror = function() {						
	}
	img.onabort = function() {						
	}

	img.src = src;
	
}

var wbSliderSettingsGuiDefaultHandlings = function() {
	var first_time_loaded = true;
	
	
	jQuery('#wb_enable').change(function(){
		if(jQuery(this).is(':checked')){
			jQuery('#wb-settings-wrapper').show();
		}else{
			jQuery('#wb-settings-wrapper').hide();
		}
	});
	
	jQuery('#wb_enable').change();
	
	jQuery("#wb_movehand_source, #wb_writehand_source").change(function(){
		var t = jQuery(this),
			env = t.attr('id')==="wb_movehand_source" ? "move" : "write",
			src = "",
			x = 0,
			y = 0;
		

		if(t.val() == "custom"){
			jQuery(".wb_"+env+"hand_source_custom_wrapper").show();
			src = jQuery('#wb_'+env+'hand_source_custom').val();
		}else{
			if (t.val()!="off")
				src = env==="move" ? wb_movehand_sources[t.val()-1].src : wb_writehand_sources[t.val()-1].src;
			if (t.val()!="off" && !first_time_loaded) {				
				x = env==="move" ? wb_movehand_sources[t.val()-1].x : wb_writehand_sources[t.val()-1].x;
				y = env==="move" ? wb_movehand_sources[t.val()-1].y : wb_writehand_sources[t.val()-1].y;
				jQuery('#wb_'+env+'hand_origin_x').val(x);
				jQuery('#wb_'+env+'hand_origin_y').val(y);
				jQuery('#wb_global_'+env+'hand_jitter_repeat').val(5);
				jQuery('#wb_global_'+env+'hand_jitter').val(80);
				jQuery('#wb_global_'+env+'hand_jitter_offset').val(10);
				jQuery('#wb_global_'+env+'hand_jitter_horizontal').val(100);
				jQuery('#wb_global_'+env+'hand_jitter_offset_horizontal').val(00);
				jQuery('#wb_global_'+env+'hand_angle').val(10);
				jQuery('#wb_global_'+env+'hand_angle_repeat').val(5);
			}
			jQuery(".wb_"+env+"hand_source_custom_wrapper").hide();
		}

		if (src!=undefined && src.length>3 && !first_time_loaded) 
			wbLoadAndSetHandDimensions(env,src);

		jQuery('.wb_'+env+'hand_preview').css({backgroundImage:"url("+src+")"});
	});
	
	
	jQuery("#wb_movehand_source option:selected").change();
	jQuery("#wb_writehand_source option:selected").change();
	first_time_loaded = false;

	jQuery(".button-image-select-wb-hand-img").click(function(){
		if (jQuery(this).data('hand')=="write")
			UniteAdminRev.openAddImageDialog("'.__('Choose Image', 'rs_whiteboard').'",function(urlImage, imageID){
				jQuery("#wb_writehand_source_custom").val(urlImage);
				wbLoadAndSetHandDimensions("write",urlImage);
				jQuery('.wb_writehand_preview').css({backgroundImage:"url("+urlImage+")"});
			});
		else
			UniteAdminRev.openAddImageDialog("'.__('Choose Image', 'rs_whiteboard').'",function(urlImage, imageID){
				jQuery("#wb_movehand_source_custom").val(urlImage);
				wbLoadAndSetHandDimensions("move",urlImage);
				jQuery('.wb_movehand_preview').css({backgroundImage:"url("+urlImage+")"});
			});
	});

	jQuery(".button-image-select-wb-hand-origin").click(function(){
		var env = jQuery(this).data('hand'),
			w = parseInt(jQuery('#wb_'+env+'hand_width').val(),0),
			h = parseInt(jQuery('#wb_'+env+'hand_height').val(),0),
			x = parseInt(jQuery("#wb_"+env+"hand_origin_x").val(),0),
			y = parseInt(jQuery("#wb_"+env+"hand_origin_y").val(),0),
			srclist = jQuery('#wb_'+env+'hand_source').val(),
			src = srclist=="custom" ? jQuery('#wb_'+env+'hand_source_custom').val() : env==="move" ? wb_movehand_sources[srclist-1].src : wb_writehand_sources[srclist-1].src;
		


		//remove first image from #wb-origin-selector-wrapper if exists
		//check if image can be loaded, get the image size & width for the modal
		//add image into #wb-origin-selector-wrapper
		
		jQuery(".wb-origin-dialog").dialog({
			modal:true,
			resizable:false,			
			closeOnEscape:true,
			buttons:{
				"Update":function(){					
					//get the Origins that are now set in x/y and add it to the origin x/y input fields
					jQuery("#wb_"+env+"hand_origin_x").val(x);
					jQuery("#wb_"+env+"hand_origin_y").val(y);
					jQuery(this).dialog("close");
				}
			},
			open:function() {
				var sw = jQuery('#wb-origin-selector-wrapper'),
					uid = jQuery('.wb-origin-dialog').closest('.ui-dialog')

				// CREATE CONTAINERS				
				sw.find('#wb-origin-bg').remove();
				sw.append('<div id="wb-origin-bg" style="width:'+w+'px; height:'+h+'px;background-image:url('+src+');"><div id="wb-coors"></div><div id="wb-origin-cross"></div></div>');
				

				var bg = jQuery('#wb-origin-bg'),
					cross = bg.find('#wb-origin-cross'),
					cor = bg.find('#wb-coors');


				// PRESET CONTAINERS
				uid.css({width:(w+10)+"px"});			
				sw.css({minWidth:w+"px", minHeight:h+"px"});				
				cross.css({top:y+"px",left:x+"px"});

				// POSITION UID
				
				var uiw = uid.width(),
					uih = uid.height(),
					cw = jQuery(window).width(),
					ch = jQuery(window).height(),
					st = jQuery(document).scrollTop(),
					xx = (st + (ch-uih)/2),
					yy = ((cw-uiw)/2);
				
				uid.css({top:xx+"px", left: yy+"px"});

				cor.html('Origin <strong>X:'+x+"</strong>  Origin <strong>Y:"+y+"</strong>");

				cross.draggable({					
					cursor:"crosshair",
					drag: function() {
						var p = cross.position();
						cor.html('Origin <strong>X:'+p.left+"</strong>  Origin <strong>Y:"+p.top+"</strong>");
						x = p.left;
						y = p.top;
					}
				});				
				
			}
			
		});
	});
}