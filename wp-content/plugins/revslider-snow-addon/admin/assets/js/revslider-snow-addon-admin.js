/***************************************************
 * REVOLUTION 6.0.0 HOLIDAY SNOW ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';

	// TRANSLATABLE CONTENT
	var bricks = revslider_snow_addon.bricks;

	// ADDON CORE
	var addon = {};
	var slug = "revslider-snow-addon";
	
	RVS.DOC = RVS.DOC===undefined ? $(document)  : RVS.DOC;

	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
		
		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {

			// CREATE CONTAINERS
			RVS.F.addOnContainer.create({slug: slug, icon:"star", title:bricks.snow, alias:bricks.snow, slider:true, slide:false, layer:false});
			
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
			createSliderSettingsFields();					
			initListeners();
			initHelp();					
			addon.initialised = true;
		}

		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {				
			//Update Input Fields in Slider Settings
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('snow_addon');
			
		} else {
			// DISABLE THINGS
			//removeDrawnHand();			
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});			
			$('#gst_sl_'+slug).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");

			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('snow_addon');
			
		}				
	});

	//BUILD FORMS

	
	// UPDATE THE REVBUILDER SLIDER SETTINGS ADDONS OBJECT
	function updateSliderObjectsStructure() {
		RVS.SLIDER.settings.addOns[slug] = RVS.SLIDER.settings.addOns[slug]!==undefined ? RVS.SLIDER.settings.addOns[slug] : { enable : true};
		RVS.SLIDER.settings.addOns[slug].startSlide = RVS.SLIDER.settings.addOns[slug].startSlide===undefined ? "first" : RVS.SLIDER.settings.addOns[slug].startSlide;
		RVS.SLIDER.settings.addOns[slug].endSlide = RVS.SLIDER.settings.addOns[slug].endSlide===undefined ? "last" : RVS.SLIDER.settings.addOns[slug].endSlide;
		RVS.SLIDER.settings.addOns[slug].max = RVS.SLIDER.settings.addOns[slug].max !== undefined ? RVS.SLIDER.settings.addOns[slug].max : {
				number:400,
				opacity:1,
				sinus:100,
				size:6,
				speed:100
		};
		RVS.SLIDER.settings.addOns[slug].min = RVS.SLIDER.settings.addOns[slug].min !== undefined ? RVS.SLIDER.settings.addOns[slug].min : {					
				opacity:0.3,
				sinus:1,
				size:0.2,
				speed:30
		}						
	}

	// UPDATE THE LAYER OBJEXT STRUCTURE (EXTEND WITH THE ATTRIBUTES WE NEED)
	function updateLayerObjectStructure(_) {			
	}

	
	
	// INITIALISE typewriter LISTENERS
	function initListeners() {		
		RVS.DOC.on('slideAmountUpdated.snow',updateStartEndList);	
	}

	/*
	UPDATE STATIC LAYER START / END LISTS
	*/
	function updateStartEndList() {
		
		window.snowLayerStartIndex = window.snowLayerStartIndex===undefined ? $('#snow_start_slide') : window.snowLayerStartIndex;
		window.snowLayerEndIndex = window.snowLayerEndIndex===undefined ? $('#snow_end_slide') : window.snowLayerEndIndex;
		RVS.F.removeAllOptionsS2({select:window.snowLayerStartIndex});
		RVS.F.removeAllOptionsS2({select:window.snowLayerEndIndex});
		RVS.F.addOptionS2({select:window.snowLayerStartIndex, val:"first", txt:bricks.firstslide});
		for (var i=1;i<RVS.SLIDER.slideIDs.length-2;i++) {				
			RVS.F.addOptionS2({select:window.snowLayerStartIndex, val:(i+1).toString(), txt:(i+1)});				
			RVS.F.addOptionS2({select:window.snowLayerEndIndex, val:(i+1).toString(), txt:(i+1)});
		} 
		RVS.F.addOptionS2({select:window.snowLayerEndIndex, val:"last", txt:bricks.lastslide});
		
		// need to update the easy inputs here
		RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
		
	}

	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {
		if (!addon.slidersettings) {
						
			var _h;

			// SNOW SETTINGS
			_h = '<div class="form_inner_header"><i class="material-icons">edit</i>'+bricks.general+'</div><div  class="collapsable" style="display:block !important">'; 
			_h += '<label_icon class="ui_easing_in singlerow"></label_icon><select id="snow_start_slide" class="sliderinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.startSlide"><option value="first" selected="selected">'+bricks.firstslide+'</option><option value="2">2</option><option value="3">3</option></select>';
			_h += '<label_icon class="ui_easing_out singlerow"></label_icon><select id="snow_end_slide" class="sliderinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.endSlide"><option value="2" selected="selected">2</option><option value="3">3</option><option value="4">4</option><option value="last">'+bricks.lastslide+'</option></select>';											
			_h += '</div>'; // END OF COLLAPSABLE			
			_h += '<div id="snowflake_settings" class="form_inner_header"><i class="material-icons">star</i>'+bricks.snowflake+'</div><div  class="collapsable" style="display:block !important">'; 
			_h += '<longoption><label_a>'+bricks.maxsnow+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snowflake_amount" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.max.number" data-min="0" data-max="4000" type="text"></longoption>';

			_h += '<longoption><label_a>'+bricks.minsize+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_min_size" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.min.size" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<longoption><label_a>'+bricks.maxsize+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_max_size" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.max.size" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<div class="div15"></div>';

			_h += '<longoption><label_a>'+bricks.minop+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_min_op" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.min.opacity" data-min="0" data-max="1" type="text"></longoption>';
			_h += '<longoption><label_a>'+bricks.maxop+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_max_op" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.max.opacity" data-min="0" data-max="1" type="text"></longoption>';
			_h += '<div class="div15"></div>';

			_h += '<longoption><label_a>'+bricks.minspeed+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_min_speed" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.min.speed" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<longoption><label_a>'+bricks.maxspeed+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_max_speed" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.max.speed" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<div class="div15"></div>';

			_h += '<longoption><label_a>'+bricks.minamp+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_min_sinus" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.min.sinus" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<longoption><label_a>'+bricks.maxamp+'</label_a><input class="sliderinput valueduekeyboard smallinput easyinit" id="snow_max_sinus" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.max.sinus" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '<div class="div15"></div>';
			
			_h += '</div>'; // END OF COLLAPSABLE			
					
			addon.forms.slidergeneral.append($(_h));
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});

			updateStartEndList();
		}
	}
	
	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_snow_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'snow_addon'};
			$.extend(true, obj, revslider_snow_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}
		
	
})( jQuery );