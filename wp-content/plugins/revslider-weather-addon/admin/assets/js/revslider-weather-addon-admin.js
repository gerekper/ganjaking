/***************************************************
 * REVOLUTION 6.0.0 WEATHER ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
jQuery(function() {
	
	//'use strict';

	var bricks = revslider_weather_addon.bricks,
		addon = {};

	// ADDON CORE
	var slug = "revslider-weather-addon";
	RVS.DOC = RVS.DOC===undefined ? jQuery(document) : RVS.DOC;

	// INITIALISE THE ADDON	
	RVS.DOC.on('extendmetas.weather',function() {	
	
		// FIRST TIME INITIALISED
		if (!addon.meta_extended) {
			updateMetas();
			addon.meta_extended = true;
		}							
	});
	
	// INITIALISE THE ADDON	CONFIG PANEL (init_%SLUG%_ConfigPanel)
	RVS.DOC.on(slug+'_config',function(e,param) {
		
		// FIRST TIME INITIALISED
		if (!addon.configinit) {
			
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_weather_form: jQuery('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); //
			
			addon.configpanel = jQuery(buildConfigPanel());
			addon.configinit = true;				
			jQuery("#"+param.container).append(addon.configpanel);			
			//AJAX TO LOAD CONTENT
			RVS.F.ajaxRequest("wp_ajax_get_values_"+slug, {}, function(response){						
				if (response.data) 
					setContent(jQuery.parseJSON(response.data));							
				else
					setContent();	
				// RVS.F.updateSelectsWithSpecialOptions();					
			},undefined,undefined,RVS_LANG.loadconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.loadvalues+'"</span>');					
		} else {
			jQuery("#"+param.container).append(addon.configpanel);
		}
		
		//Update Save Config Button
		RVS.F.configPanelSaveButton({show:true, slug:slug});
		RVS.F.initOnOff(addon.configpanel);	
		
	});
		
	// INITIALISE THE ADDON	
	RVS.DOC.on(slug+'_init',function() {	

		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {

			// CREATE CONTAINERS
			RVS.F.addOnContainer.create({slug: slug, icon:"cloud", title:bricks.weather ,alias:bricks.weather, slider:true, slide:false, layer:true});
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : jQuery('#form_slidergeneral_'+slug), 
								slidegeneral : jQuery('#form_slidegeneral_'+slug), 
								layergeneral : jQuery('#form_layerinner_'+slug),
								module : jQuery('#form_module_'+slug),
								layer : jQuery('#form_layer_'+slug),
								slide : jQuery('#form_slide_'+slug)
						};			
			
			//CHECK STRUCTURE ON SLIDER SETTNIGS
			createSliderSettingsFields();
			createLayerSettingsFields();	
			updateSliderObjectsStructure();
			updateLayerObjectStructure();
			initListeners();			
			addon.initialised = true;
		}

		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {
			
			//Update Input Fields in Slider Settings
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});

			//Show Hide Areas
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
			
			// make sure metas get added when AddOn is activated on-demand
			RVS.DOC.trigger('extendmetas.weather');
			RVS.DOC.trigger('layerselectioncomplete.weather');
			
		} else {		
			// DISABLE AND HIDE THINGS	
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");
		}
		
	});
	
	function setContent(obj) {	
		
		var val = obj && typeof obj === 'object' && obj[slug + '-api'] ? obj[slug + '-api'] : '';
		jQuery('#' + slug + '-api').val(val);
		
	}
	
	function buildConfigPanel() {
		var _h;				
		_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
		_h += '<form id="'+slug+'-form">';				
		_h += '	<label_a>'+bricks.apikey+'</label_a><input id="' + slug + '-api" class="basicinput" type="text" name="' + slug + '-api">';				
		_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.info+'</div></contenthalf></row>';
		_h += '</form>';
		_h += '	<div class="div75"></div>';
		return _h;
	}
	
	function getDefaults() {
	
		return { 
			enable : true,
			location: 'Cologne',
			unit: 'c',
			refresh: '0'
		};
	
	}

	// UPDATE THE REVBUILDER SLIDER SETTINGS ADDONS OBJECT
	function updateSliderObjectsStructure() {
		
		RVS.SLIDER.settings.addOns[slug] = RVS.SLIDER.settings.addOns[slug]!==undefined ? jQuery.extend(getDefaults(), RVS.SLIDER.settings.addOns[slug]) : getDefaults();
		
	}
	
	function updateLayerObjectStructure() {
	
		for (var i in RVS.selLayers) {
			if(!RVS.selLayers.hasOwnProperty(i)) continue;
			updateLayerObject({layerid:RVS.selLayers[i]});
		}
	
	}

	// UPDATE THE LAYER OBJEXT STRUCTURE (EXTEND WITH THE ATTRIBUTES WE NEED)
	function updateLayerObject(_) {
		
		RVS.L[_.layerid].addOns[slug] = RVS.L[_.layerid].addOns[slug]===undefined ? {} : RVS.L[_.layerid].addOns[slug];
		RVS.L[_.layerid].addOns[slug].location = RVS.L[_.layerid].addOns[slug].location!=undefined ? RVS.L[_.layerid].addOns[slug].location : (RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]!==undefined && RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].location!==undefined) ?  RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].location : RVS.SLIDER.settings.addOns[slug].location;
		RVS.L[_.layerid].addOns[slug].unit = RVS.L[_.layerid].addOns[slug].unit!=undefined ? RVS.L[_.layerid].addOns[slug].unit : (RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]!==undefined && RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].unit!==undefined) ?  RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].unit : RVS.SLIDER.settings.addOns[slug].unit;
		
		addon.forms.selectList.val(RVS.L[_.layerid].addOns[slug].location).trigger('change.select2RS');
		addon.forms.temperature.val(RVS.L[_.layerid].addOns[slug].unit).trigger('change.select2RS');

	}


	// INITIALISE weather LISTENERS
	function initListeners() {

		// this event never fires
		RVS.DOC.on('layerTextContentUpdate.weather', function(a,b) {
			
			if (b!==undefined && b.eventparam==="placeholder") {
				// DO NOTHING 
			} else {				
				if (b!==undefined && b.val!==undefined && b.val.indexOf("{{weather_")>=0) { 
					addon.forms.weatherLocationExtension.style.display = "block";
				}
				else {
					addon.forms.weatherLocationExtension.style.display = "none";
				}
			}
		});
		
		RVS.DOC.on('click.weather', '.mdl_group_member', function() {
			
			if(this.dataset.val && this.dataset.val.search('{{weather_') !== -1) {
				addon.forms.weatherLocationExtension.style.display = "block";
				updateLayerObjectStructure();
			}
			
		});
		
		RVS.DOC.on('layerselectioncomplete.weather', function() {
			
			if(!RVS.selLayers || !RVS.selLayers.length) return;
			var txt = RVS.SLIDER[RVS.S.slideId].layers[RVS.selLayers[0]].text;
			if(!txt) return;
			
			var hasWeather = txt.search('{{weather_') !== -1;
			addon.forms.weatherLocationExtension.style.display = hasWeather ? 'block' : 'none';
			
		});

		RVS.DOC.on('selectLayersDone.weather',function() {
			for (var i in RVS.selLayers) {
				if(!RVS.selLayers.hasOwnProperty(i)) continue;
				if (RVS.L[RVS.selLayers[i]].text.indexOf("{{weather_")>=0) {
					updateLayerObject({layerid:RVS.selLayers[i]});
					addon.forms.weatherLocationExtension.style.display = "block";
				}
				else
					addon.forms.weatherLocationExtension.style.display = "none";
			}
		
		});
		
		RVS.DOC.on('click', '#weather_meta_btn', function() {
			
			jQuery('.mdl_group_wrap_menuitem[data-show="mdl_group_weather"]').click();
			
		});

	}


	// CREATE THE BASIC INPUT FIELDS FOR THE ADD ON
	function createLayerSettingsFields() {

		var list = [],
			h = '<label_a>'+bricks.wlocation+'</label_a><select id="weather_layer_city" data-r="addOns.'+slug+'.location" class="layerinput tos2 easyinit">';
		if (RVS.SLIDER.settings.addOns[slug] && RVS.SLIDER.settings.addOns[slug].location)
			list.push(RVS.SLIDER.settings.addOns[slug].location);
		else
			list.push("Cologne");

		for (var i in RVS.SLIDER.slideIDs) {	
			if(!RVS.SLIDER.slideIDs.hasOwnProperty(i)) continue;
			if (RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug]!==undefined && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug].location!==undefined)
				if (jQuery.inArray(RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug].location,list)==-1) 
					list.push(RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug].location);
			for (var j in RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers) {
				if(!RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers.hasOwnProperty(j)) continue;
				if (RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug] && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug].location) {
					if (jQuery.inArray(RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug].location,list)==-1) 
						list.push(RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug].location);
				} else 
				if (RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug] && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug].location===undefined)
					RVS.SLIDER[RVS.SLIDER.slideIDs[i]].layers[j].addOns[slug].location = RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug] && RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug].location ? RVS.SLIDER[RVS.SLIDER.slideIDs[i]].slide.addOns[slug].location : RVS.SLIDER.settings.addOns[slug] && RVS.SLIDER.settings.addOns[slug].location ? RVS.SLIDER.settings.addOns[slug].location : "Cologne";
			}			
		}
			
		for (i in list) {
			if(!list.hasOwnProperty(i)) continue;
			h += '<option value="'+list[i]+'">'+list[i]+'</option>';
		}
		h += '</select>';	
		h += '<label_a>'+bricks.weather_temperature+'</label_a>';
		h += '<select id="weather_layer_unit" data-r="addOns.'+slug+'.unit" class="layerinput tos2 nosearchbox easyinit">';
		h += '<option value="c">&degC</option>';
		h += '<option value="f">&degF</option>';
		h += '</select>';
		
		jQuery('#ta_layertext_extension').append('<div id="ta_layertext_extension_weather">'+h+'</div>');
		addon.forms.selectList = jQuery('#weather_layer_city');
		addon.forms.selectList.select2RS({
			tags:true,				
			placeholder:"Location / WOEID",			
		});
		
		addon.forms.temperature = jQuery('#weather_layer_unit');
		addon.forms.temperature.select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.weather_temperature
		});

		addon.forms.selectList.on('select2RS:select',function(e) {
			addon.forms.selectList.find('option').each(function() {
				if (this.dataset.select2RSTag)  delete this.dataset.select2RSTag;
			});
		});

		addon.forms.weatherLocationExtension = document.getElementById('ta_layertext_extension_weather');
		addon.forms.metabutton = jQuery('<div id="weather_meta_btn" class="triggerEvent icon_trigger" data-evt="addMetaToLayer" data-evtparam="layer" style="margin-left: 3px"><i class="material-icons" style="position: relative; left: 2px; top: 1px">cloud</i></div>');
		addon.forms.metabutton.insertAfter(jQuery('#add_meta_to_layer'));
		
	}


	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {
		if (!addon.slidersettings) {
						
			var _h;
			_h = '<div class="form_inner_header"><i class="material-icons">touch_app</i>'+bricks.wdefaults+'</div><div  class="collapsable" style="display:block !important">'; 
			_h += '<label_a>'+bricks.location+'</label_a><input type="text" class="sliderinput easyinit longinput" data-r="addOns.'+slug+'.location">';
			_h += '<label_a>'+bricks.weather_temperature+'</label_a>';
			_h += '<select data-r="addOns.'+slug+'.unit" class="sliderinput tos2 nosearchbox easyinit">';
			_h += '<option value="c">&degC</option>';
			_h += '<option value="f">&degF</option>';
			_h += '</select>';
			_h += '<label_a>'+bricks.refresh+'</label_a><input type="text" class="sliderinput easyinit valueduekeyboard" data-r="addOns.'+slug+'.refresh" data-allowed="" data-numeric="true" data-min="0" data-max="500">';
			_h += '</div>';
			
			
			addon.forms.slidergeneral.append(jQuery(_h));
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});
			
			addon.slidersettings = true;
			
		}
	}

	//UPDATE META DATAS
	function updateMetas() {
		
		var _h,
			defs = [["{{weather_title}}", "weather_title", "Title","Weather - New York, NY, US"],
						 ["{{weather_temp}}", "weather_temp", "75"],
						 ["{{weather_code}}", "weather_code", "32"],
						 ["{{weather_date}}", "weather_date", "21 Aug 2018"],
						 ["{{weather_day}}", "weather_day", "Mon"],
						 ["{{weather_todayCode}}", "weather_todayCode","28" ],
						 ["{{weather_currently}}", "weather_currently", "Partly Cloudy"],
						 ["{{weather_high}}", "weather_high", "32"],
						 ["{{weather_low}}", "weather_low" , "8"],
						 ["{{weather_text}}", "weather_text" ,"Partly Cloudy"],
						 ["{{weather_humidity}}", "weather_humidity", "79"],
						 ["{{weather_pressure}}", "weather_pressure" , "1012.0"],
						 ["{{weather_rising}}", "weather_rising" , "0"],
						 ["{{weather_visbility}}", "weather_visbility" , "16.1"],
						 ["{{weather_sunrise}}", "weather_sunrise" , "6:54 am"],
						 ["{{weather_sunset}}", "weather_sunset" , "11:16 pm"],
						 ["{{weather_city}}", "weather_city", "New York"],
						 ["{{weather_country}}", "weather_country", "United States"],
						 ["{{weather_region}}", "weather_region", "NY"],
						 ["{{weather_updated}}", "weather_updated", "Thu, 16 Aug 2018 07:16 AM ED"],
						 ["{{weather_link}}", "weather_link","{{weather link}}"],
						// ["{{weather_thumbnail}}", "weather_thumbnail","<img width='61' height='34' src='https://s.yimg.com/zz/combo?a/i/us/nws/weather/gr/28ds.png'>"],
						// ["{{weather_image}}", "weather_image", "<img width='250' height='180' src='https://s.yimg.com/zz/combo?a/i/us/nws/weather/gr/28d.png'>"],
						 ["{{weather_icon}}", "weather_icon" , '&lt;i class="revslider-weather-icon-28"&lt;&lt;/i&lt;'],
						 ["{{weather_units_temp}}", "weather_units_temp", "F"],
						 ["{{weather_units_distance}}", "weather_units_distance","mi"],
						 ["{{weather_units_pressure}}", "weather_units_pressure" , "in"],
						 ["{{weather_units_speed}}", "weather_units_speed" , "mph"],
						 ["{{weather_wind_chill}}", "weather_wind_chill" , "75"],
						 ["{{weather_wind_direction}}", "weather_wind_direction", "270"],
						 ["{{weather_wind_speed}}", "weather_wind_speed" , "11"],
						 ["{{weather_alt_temp}}", "weather_alt_temp" , "32"],
						 ["{{weather_alt_high}}", "weather_alt_high" , "30"],
						 ["{{weather_alt_low}}", "weather_alt_low", "23"],
						 ["{{weather_alt_unit}}", "weather_alt_unit", "C" ],
						 ["{{weather_description}}", "weather_description" , '<b>Current Conditions:</b><br>Partly Cloudy<br><b>Forecast:</b><br> Thu - Thunderstorms. High: 32Low: 23<br> Fri - Thunderstorms. High: 31Low: 24<br> Sat - Thunderstorms. High: 28Low: 23<br> Sun - Partly Cloudy. High: 25Low: 21<br> Mon - Partly Cloudy. High: 24Low: 19<br>\<a href="http://us.rd.yahoo.com/dailynews/rss/weather/Country__Country/*https://weather.yahoo.com/country/state/city-2459115/">Full Forecast at Yahoo! Weather</a><br>'],
						 ["{{weather_date_forecast:x}}", "weather_date_forecast" , "22 Aug 2018"],
						 ["{{weather_day_forecast:x}}", "weather_day_forecast" , "Tue"],
						 ["{{weather_code_forecast:x}}", "weather_code_forecast" , "4"],
						 ["{{weather_high_forecast:x}}", "weather_high_forecast" , "30"],
						 ["{{weather_low_forecast:x}}", "weather_low_forecast" , "20"],
						 ["{{weather_alt_high_forecast:x}}", "weather_alt_high_forecast" , "22"],
						 ["{{weather_alt_low_forecast:x}}", "weather_alt_low_forecast" , "21"],
						 //["{{weather_thumbnail_forecast:x}}", "weather_thumbnail_forecast" , "<img width='61' height='34' src='https://s.yimg.com/zz/combo?a/i/us/nws/weather/gr/4ds.png'>"],
						// ["{{weather_image_forecast:x}}", "weather_image_forecast" , "<img width='250' height='180' src='https://s.yimg.com/zz/combo?a/i/us/nws/weather/gr/4d.png'>"],
						 ["{{weather_icon_forecast:x}}", "weather_icon_forecast" , '&lt;i class="revslider-weather-icon-4"&gt;&lt;/i&gt;'],
						 ["{{weather_text_forecast:x}}", "weather_text_forecast", "Thunderstorms"]];

		_h = '<div id="mdl_group_weather" class="mdl_group_wrap">';
		
		_h += '<div class="mdl_group">';
		_h += '<div class="mdl_group_header"><i class="material-icons">cloud</i>Weather Basics<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
		for (var i in defs) {
			if(!defs.hasOwnProperty(i)) continue;
			_h += '<div data-val="'+defs[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">cloud</i>'+bricks[defs[i][1]]+'</div><div class="mdl_right_content">'+defs[i][0]+'</div><div class="mdl_placeholder_content">'+defs[i][2]+'</div></div>';
		}
		_h += '</div>';
		
		_h += '</div>';
		jQuery('#meta_datas_list').append(jQuery(_h));
		jQuery('#mdl_group_wrap_menu').append('<div data-show="mdl_group_weather" class="mdl_group_wrap_menuitem">Weather</div>');
		RVS.F.updateMetaTranslate();
	}

});