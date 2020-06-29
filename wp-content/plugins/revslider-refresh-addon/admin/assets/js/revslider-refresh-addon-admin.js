/***************************************************
 * REVOLUTION 6.0.0 HOLIDAY PARTICLE EFFECTS ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';	
	// TRANSLATABLE CONTENT
	var bricks = revslider_refresh_addon.bricks;
	

	// ADDON CORE
	var addon = {};
	var slug = "revslider-refresh-addon";

	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
		
		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {
			
			//CHECK STRUCTURE ON SLIDER SETTINGS
			RVS.SLIDER.settings.addOns[slug] = checkDefaults(RVS.SLIDER.settings.addOns[slug]);

			// INIT LISTENERS
			initListeners();

			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"refresh", title:bricks.refresh, alias:bricks.refresh, slider:true, slide:false, layer:false});				
			
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
			initInputs();
			initHelp();	
		}
		
		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {				
			//Update Input Fields in Slider Settings
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('refresh_addon');
			
		} else {
			// DISABLE THINGS
			//removeDrawnHand();			
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});			
			$('#gst_sl_'+slug).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");	

			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('refresh_addon');
			
		}				
	});

			
	// INITIALISE typewriter LISTENERS
	function initListeners() {					
		
		RVS.DOC.on('AddOnRefreshTimes.refresh',function() {

			$('.refresh_addon_type').hide();
			$('#refresh_addon_' + RVS.SLIDER.settings.addOns[slug].type).show();
			
		});

		
	}
	
	//Migrate Datas
	function checkDefaults(_) {
		
		var obj = {
			type: 'time',
			minutes: 1,
			slide: 1,
			loops: 1,
			url_enable: false,
			custom_url: 'http://'
		};
		
		jQuery.extend(true, obj, _);
		return obj;
		
	}
			
	function initInputs() {
		
		// init select2
		addon.forms.slidergeneral.find('.tos2.nosearchbox').select2({
			minimumResultsForSearch:"Infinity",
			placeholder:revslider_refresh_addon.placeholder_select
		});
								
		// on/off init
		RVS.F.initOnOff(addon.forms.slidergeneral);
		
		// update easy inits
		RVS.F.updateEasyInputs({container:jQuery('#form_module_revslider-refresh-addon'), trigger:"init"});
		
		RVS.DOC.trigger('AddOnRefreshTimes');
		
	}		
				
	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {
		if (!addon.slidersettings) {
								
			var _h = '';									
			_h += '<div id="refresh_addon_settings" class="form_inner_header"><i class="material-icons">refresh</i>'+bricks.reload+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';															
			_h += '		<label_a>'+bricks.event+'</label_a><select class="sliderinput tos2 nosearchbox easyinit callEvent" data-evt="AddOnRefreshTimes" data-r="addOns.'+slug+'.type"><option value="time">'+bricks.after_minutes+'</option><option value="slide">'+bricks.after_slide+'</option><option value="loops">'+bricks.after_loops+'</option></select>';
			_h += '		<div class="refresh_addon_type" id="refresh_addon_time"><label_a>'+bricks.minutes+'</label_a><input class="sliderinput valueduekeyboard easyinit" data-numeric="true" data-allowed data-r="addOns.'+slug+'.minutes" data-min="0" data-max="10000" type="text"></div>';					
			_h += '		<div class="refresh_addon_type" id="refresh_addon_slide"><label_a>'+bricks.slide_number+'</label_a><input class="sliderinput valueduekeyboard easyinit" data-numeric="true" data-allowed data-r="addOns.'+slug+'.slide" data-min="0" data-max="99" type="text"></div>';
			_h += '		<div class="refresh_addon_type" id="refresh_addon_loops"><label_a>'+bricks.loops+'</label_a><input class="sliderinput valueduekeyboard easyinit" data-numeric="true" data-allowed data-r="addOns.'+slug+'.loops" data-min="0" data-max="999" type="text"></div>';
			_h += '		<onelong><label_a>'+bricks.custom_url+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.url_enable" data-showhide="#refresh_addon_url_wrap" data-showhidedep="true" value="on"></onelong>';
			_h += '		<div id="refresh_addon_url_wrap">';
			_h += '			<label_a>'+bricks.url+'</label_a><input class="sliderinput valueduekeyboard easyinit" data-r="addOns.'+slug+'.custom_url" type="text"></div>';					
			_h += '		</div>';
			_h += '	</div>';
								
					
			addon.forms.slidergeneral.append($(_h));
			
		}
	}
	
	function initHelp() {
		
		// will only get added on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_refresh_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'refresh_addon'};
			$.extend(true, obj, revslider_refresh_addon.help);
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