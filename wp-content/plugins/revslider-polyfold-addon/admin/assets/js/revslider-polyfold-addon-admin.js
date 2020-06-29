/***************************************************
 * REVOLUTION 6.0.0 POLYFOLDEFFECTS ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';	
	// TRANSLATABLE CONTENT
	var bricks = revslider_polyfold_addon.bricks,
		addon = {};
		
	

	// ADDON CORE
	var slug = "revslider-polyfold-addon";

	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
		
		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {

			//CHECK STRUCTURE ON SLIDER SETTINGS
			RVS.SLIDER.settings.addOns[slug] = migrate(RVS.SLIDER.settings.addOns[slug]);

			// INIT LISTENERS
			// initListeners();

			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"send", title:bricks.polyfold, alias:bricks.polyfold, slider:true, slide:false, layer:false});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug), 
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('#form_slide_'+slug)
						};				
			createSliderSettingsFields();			
			addon.initialised = true;
			initHelp();	
		}
		
		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {				
			//Update Input Fields in Slider Settings
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('polyfold_addon');
			
		} else {
			// DISABLE THINGS
			//removeDrawnHand();			
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});			
			$('#gst_sl_'+slug).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");	

			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('polyfold_addon');
			
		}				
	});

	/*
	// INITIALISE typewriter LISTENERS
	function initListeners() {					
		
		// UPDATE DUE BACKUP/RESTORE
		// RVS.DOC.on('SceneUpdatedAfterRestore.polyfold',function() {});		
	}
	*/

	
	//Migrate Datas
	function migrate(_) {

		var o = _===undefined || _.bottom===undefined ? 
			{ 					
				enable : _truefalse(_.enable),
				bottom: {
					enabled:_d(_truefalse(_.polyfold_bottom_enabled),false),
					animated:_d(_truefalse(_.polyfold_bottom_animated),false),
					color:_d( _.polyfold_bottom_color , "#ffffff"),
					ease:_d( _.polyfold_bottom_ease , "ease-in-out"),					
					height:_d( _.polyfold_bottom_height , 100),
					hideOnMobile:_d(_truefalse(_.polyfold_bottom_hide_mobile),false),
					inverted:_d(_truefalse(_.polyfold_bottom_inverted),false),
					leftWidth:_d( _.polyfold_bottom_left_width , 50),
					rightWidth:_d( _.polyfold_bottom_right_width , 50),
					negative:_d(_truefalse(_.polyfold_bottom_negative),false),					
					placement:_d( _.polyfold_bottom_placement , 1),
					point:_d( _.polyfold_bottom_point , "sides"),
					range:_d( _.polyfold_bottom_range , "slider"),
					responsive:_d(_truefalse(_.polyfold_bottom_responsive),true),
					scroll:_d(_truefalse(_.polyfold_bottom_scroll),true),
					time:_d( _.polyfold_bottom_time , 0.3)			
				},
				top:{
					enabled:_d(_truefalse(_.polyfold_top_enabled),false),
					animated:_d(_truefalse(_.polyfold_top_animated),false),
					color:_d( _.polyfold_top_color , "#ffffff"),
					ease:_d( _.polyfold_top_ease , "ease-in-out"),					
					height:_d( _.polyfold_top_height , 100),
					hideOnMobile:_d(_truefalse(_.polyfold_top_hide_mobile),false),
					inverted:_d(_truefalse(_.polyfold_top_inverted),false),
					leftWidth:_d( _.polyfold_top_left_width , 50),
					rightWidth:_d( _.polyfold_top_right_width , 50),
					negative:_d(_truefalse(_.polyfold_top_negative),false),					
					placement:_d( _.polyfold_top_placement , 1),
					point:_d( _.polyfold_top_point , "sides"),
					range:_d( _.polyfold_top_range , "slider"),
					responsive:_d(_truefalse(_.polyfold_top_responsive),true),
					scroll:_d(_truefalse(_.polyfold_top_scroll),true),
					time:_d( _.polyfold_top_time , 0.3)
				}			
			} : _;
		
		return o;	
	}
			
			
				
	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {
		if (!addon.slidersettings) {
								
			var _h = '';									
			/* */
			// TOP EDGE
			_h += '<div class="form_inner_header"><i class="material-icons">vertical_align_top</i>'+bricks.topedge+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';
			_h += '		<row>';
			_h += '			<onelong><label_a>'+bricks.active+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.enabled" data-showhide=".polyfold_top_edge_wrap" data-showhidedep="true" value="on"></onelong>';
			_h += '			<oneshort><div class="polyfold_top_edge_wrap"><label_icon class="ui_hide_on_mobile"></label_icon><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.hideOnMobile" value="on"></div></oneshort>';
			_h += '		</row>';
			_h += '		<div class="polyfold_top_edge_wrap">';																
			_h += '			<label_a>'+bricks.bgcolor+'</label_a><input type="text" data-editing="'+bricks.topedgebgcolor+'" data-mode="single" name="polyfoldtopcolor" id="polyfoldtopcolor" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.top.color" value="transparent">';
			_h += ' 		<div class="div5"></div>';	
			_h += '			<label_a>'+bricks.drawfrom+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.top.point"><option value="sides">'+bricks.sside+'</option><option value="center">'+bricks.scenter+'</option></select>';
			_h += '			<label_a>'+bricks.drawtheedge+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.top.placement"><option value="1">'+bricks.dte_1+'</option><option value="2">'+bricks.dte_2+'</option><option value="3">'+bricks.dte_3+'</option></select>';				
			_h += ' 		<div class="div15"></div>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_a>'+bricks.responsive+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.responsive" value="on"></onelong>';
			_h += '				<oneshort><label_icon class="ui_stopafterloop"></label_icon><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.negative" value="on"></oneshort>';
			_h += '			</row>';
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_x_start"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.top.leftWidth" data-min="0" data-max="100" type="text"></onelong>';
			_h += '				<oneshort><label_icon class="ui_x_end"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.top.rightWidth" data-min="0" data-max="100" type="text"></oneshort>';
			_h += '			</row>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_height"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.top.height" data-min="0" data-max="2000" type="text"></onelong>';
			_h += '				<oneshort></oneshort>';
			_h += '			</row>';
			_h += ' 		<div class="div15"></div>';										
			_h += '			<label_a>'+bricks.drawonscroll+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.scroll" data-showhide=".polyfold_top_draw_wrap" data-showhidedep="true" value="on"><span class="linebreak"></span>';								
			_h += '			<div class="polyfold_top_draw_wrap">';				
			_h += '				<label_a>'+bricks.drange+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.top.range"><option value="slider">'+bricks.sliderheight+'</option><option value="window">'+bricks.windowheight+'</option></select>';				
			_h += '				<row>';
			_h += '					<onelong><label_a>'+bricks.usetrans+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.animated" data-showhide=".polyfold_top_edge_animated" data-showhidedep="true" value="on"><label_a>'+bricks.invert+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.top.invert" value="on"></onelong>';
			_h += '					<oneshort><div class="polyfold_top_edge_animated"><label_icon class="ui_easing"></label_icon><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.top.ease"><option value="ease-out">Out</option><option value="ease-in">In</option><option value="ease-in-out">InOut</option><option value="ease">Ease</option><option value="linear">'+bricks.linear+'</option></select><label_icon class="ui_speed"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.top.time" data-min="0" data-max="1000" type="text"></div></oneshort>';
			_h += '				</row>';										
			_h += '			</div>';				
			_h += '		</div>';
			_h += '	</div>';


			_h += '<div class="form_inner_header"><i class="material-icons">vertical_align_bottom</i>'+bricks.bottomedge+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';
			_h += '		<row>';
			_h += '			<onelong><label_a>'+bricks.active+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.enabled" data-showhide=".polyfold_bottom_edge_wrap" data-showhidedep="true" value="on"></onelong>';
			_h += '			<oneshort><div class="polyfold_bottom_edge_wrap"><label_icon class="ui_hide_on_mobile"></label_icon><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.hideOnMobile" value="on"></div></oneshort>';
			_h += '		</row>';
			_h += '		<div class="polyfold_bottom_edge_wrap">';																
			_h += '			<label_a>'+bricks.bgcolor+'</label_a><input type="text" data-editing="'+bricks.bottomedgebgcolor+'" data-mode="single" name="polyfoldbottomcolor" id="polyfoldbottomcolor" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.bottom.color" value="transparent">';
			_h += ' 		<div class="div5"></div>';	
			_h += '			<label_a>'+bricks.drawfrom+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.bottom.point"><option value="sides">'+bricks.sside+'</option><option value="center">'+bricks.scenter+'</option></select>';
			_h += '			<label_a>'+bricks.drawtheedge+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.bottom.placement"><option value="1">'+bricks.dte_1+'</option><option value="2">'+bricks.dte_2+'</option><option value="3">'+bricks.dte_3+'</option></select>';				
			_h += ' 		<div class="div15"></div>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_a>'+bricks.responsive+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.responsive" value="on"></onelong>';
			_h += '				<oneshort><label_icon class="ui_stopafterloop"></label_icon><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.negative" value="on"></oneshort>';
			_h += '			</row>';
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_x_start"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.bottom.leftWidth" data-min="0" data-max="100" type="text"></onelong>';
			_h += '				<oneshort><label_icon class="ui_x_end"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.bottom.rightWidth" data-min="0" data-max="100" type="text"></oneshort>';
			_h += '			</row>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_height"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.bottom.height" data-min="0" data-max="2000" type="text"></onelong>';
			_h += '				<oneshort></oneshort>';
			_h += '			</row>';
			_h += ' 		<div class="div15"></div>';										
			_h += '			<label_a>'+bricks.drawonscroll+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.scroll" data-showhide=".polyfold_bottom_draw_wrap" data-showhidedep="true" value="on"><span class="linebreak"></span>';								
			_h += '			<div class="polyfold_bottom_draw_wrap">';				
			_h += '				<label_a>'+bricks.drange+'</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.bottom.range"><option value="slider">'+bricks.sliderheight+'</option><option value="window">'+bricks.windowheight+'</option></select>';				
			_h += '				<row>';
			_h += '					<onelong><label_a>'+bricks.usetrans+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.animated" data-showhide=".polyfold_bottom_edge_animated" data-showhidedep="true" value="on"><label_a>'+bricks.invert+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.bottom.invert" value="on"></onelong>';
			_h += '					<oneshort><div class="polyfold_bottom_edge_animated"><label_icon class="ui_easing"></label_icon><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.bottom.ease"><option value="ease-out">Out</option><option value="ease-in">In</option><option value="ease-in-out">InOut</option><option value="ease">Ease</option><option value="linear">'+bricks.linear+'</option></select><label_icon class="ui_speed"></label_icon><input class="sliderinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.bottom.time" data-min="0" data-max="1000" type="text"></div></oneshort>';
			_h += '				</row>';										
			_h += '			</div>';				
			_h += '		</div>';
			_h += '	</div>';
								
			
					
			addon.forms.slidergeneral.append($(_h));
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:revslider_polyfold_addon.placeholder_select
			});
			RVS.F.updateEasyInputs({container:$('.slider_general_collector'),path:"settings."});
			$('#form_module_revslider-polyfold-addon input').trigger("update");
			RVS.F.initOnOff();
			RVS.F.initTpColorBoxes(addon.forms.slidergeneral.find('.my-color-field'));
		}
	}
	
	function initHelp() {
		
		// will only get added on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_polyfold_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'polyfold_addon'};
			$.extend(true, obj, revslider_polyfold_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}
	

	/*
	SET VALUE TO A OR B DEPENDING IF VALUE A EXISTS AND NOT UNDEFINED OR NULL
	*/
	function _d(a,b) {
		if (a===undefined || a===null)
			return b;
		else
			return a;
	}

	function _truefalse(v) {
		if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1)
			v=false;
		else
		if (v==="true" || v===true || v==="on")
			v=true;
		return v;
	}

})( jQuery );