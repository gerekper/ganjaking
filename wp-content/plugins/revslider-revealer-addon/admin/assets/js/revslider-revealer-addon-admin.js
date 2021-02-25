/***************************************************
 * REVOLUTION 6.0.0 REVEALER ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function($) {		
		// TRANSLATABLE CONTENT
		var bricks = revslider_revealer_addon.bricks;

		// ADDON CORE
		var addon = {},
			slug = "revslider-revealer-addon";
			
		

		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {	
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) RVS.SLIDER.settings.addOns[slug] = sliderDefaults(RVS.SLIDER.settings.addOns[slug]);
			
			// FIRST TIME INITIALISED
			var init = !addon.initialised;
			if(init && addon.isActive) {

				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:"aspect_ratio", title:bricks.revealer, alias:bricks.revealer, slider:true});				
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms = {slidergeneral : $('#form_slidergeneral_'+slug)};					
				
				// add html
				createSliderSettingsFields();	
				
				initInputs();
				initHelp();
				
				addon.initialised = true;
				
			}
			
			if(addon.isActive) {	
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
				
				// update the stage
				if(!init) {
					
					//Update Input Fields in Slider Settings
					RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
					
					// update view
				//	events.updateDisplay();
					
				}
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('revealer_addon');
				
			} 
			else {
				
				if(!init) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});			
					$('#gst_sl_'+slug).removeClass("selected");
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('revealer_addon');
				
			}
			
		});

		//Create Defaults
		function sliderDefaults(_) {

			_ = _===undefined || _.overlay===undefined ? { 					
					enable : true,
					direction : "open_horizontal",
					color :"#000000",
					easing: "Power2.easeOut",
					duration :  500,
					delay : 0,
					overlay: {
						enable : false,
						color :  "#000000",
						easing: "Power2.easeOut",
						duration : 500,
						delay :  0
					},
					spinner : {
						type: "default",
						color : "#FFFFFF"
					}
			} : _;
				
			return _;
			
		}	
				
					
		// CREATE INPUT FIELDS
		function createSliderSettingsFields() {
									
			var _h = '';							
			_h += '<div class="form_inner_header"><i class="material-icons">aspect_ratio</i>'+bricks.reveal_settings+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';																
			_h += '		<label_a style="width: 75px">'+bricks.opening_reveal+'</label_a>';
			_h += '     <select id="revealer_direction" class="sliderinput tos2 nosearchbox easyinit" data-showprio="hide" data-show="#revealer-opening-settings:not(.revealer-os_hide_*val*), .revealer-os_show" data-hide=".revealer-os_hide_*val*, .revealer-color-wrap_hide_*val*" data-r="addOns.'+slug+'.direction">';
			_h += '         <option value="none">' + bricks.none + '</option>';
			_h += '         <option value="open_horizontal" selected="">' + bricks.open_horizontal + '</option>';
			_h += '         <option value="open_vertical">' + bricks.open_vertical + '</option>';
			_h += '         <option value="split_left_corner">' + bricks.split_left_corner + '</option>';
			_h += '         <option value="split_right_corner">' + bricks.split_right_corner + '</option>';
			_h += '         <option value="shrink_circle">' + bricks.shrink_circle + '</option>';
			_h += '         <option value="expand_circle">' + bricks.expand_circle + '</option>';
			_h += '         <option value="left_to_right">' + bricks.left_to_right + '</option>';
			_h += '         <option value="right_to_left">' + bricks.right_to_left + '</option>';
			_h += '         <option value="top_to_bottom">' + bricks.top_to_bottom + '</option>';
			_h += '         <option value="bottom_to_top">' + bricks.bottom_to_top + '</option>';
			_h += '         <option value="tlbr_skew">' + bricks.tlbr_skew + '</option>';
			_h += '         <option value="trbl_skew">' + bricks.trbl_skew + '</option>';
			_h += '         <option value="bltr_skew">' + bricks.bltr_skew + '</option>';
			_h += '         <option value="brtl_skew">' + bricks.brtl_skew + '</option>';
			_h += '     </select>';
			_h += ' 	<div class="div5"></div>';	
			_h += ' 	<div id="revealer-opening-settings" class="revealer-os_hide_none">';
			_h += ' 		<div id="revealer-color-wrap" class="revealer-os_show revealer-color-wrap_hide_expand_circle">';
			_h += '				<label_a style="width: 75px">' + bricks.reveal_color + '</label_a>';
			_h += '     		<input type="text" id="revealer_color" data-editing="' + bricks.reveal_color + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.color" value="#000000">';
			_h += '			</div>';
			_h += ' 		<div class="div5"></div>';	
			_h += '			<label_a style="width: 75px">' + bricks.reveal_easing + '</label_a>';
			_h += '     	<select class="sliderinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.easing" data-theme="dark"></select>';
			_h += ' 		<div class="div5"></div>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_duration"></label_icon><input id="revealer_duration"class="sliderinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.duration" data-min="100" data-max="10000" type="text"></onelong>';
			_h += '				<oneshort><label_icon class="ui_published"></label_icon><input id="revealer_delay" class="sliderinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.delay" data-min="10" data-max="10000" type="text"></oneshort>';
			_h += '			</row>';
			_h += '			<label_a style="width: 75px">' + bricks.enable_overlay + '</label_a>';
			_h += '     	<input type="checkbox" class="sliderinput easyinit" data-r="addOns.'+slug+'.overlay.enable" data-showhide=".revealer_overlay_wrap" data-showhidedep="true" value="on"></onelong>';
			_h += '			<div class="revealer_overlay_wrap">';	
			_h += '				<label_a style="width: 75px">' + bricks.overlay_color + '</label_a>';
			_h += '     		<input type="text" data-editing="' + bricks.overlay_color + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.overlay.color" value="#000000">';
			_h += ' 			<div class="div5"></div>';	
			_h += '				<label_a style="width: 75px">' + bricks.overlay_easing + '</label_a>';
			_h += '     		<select class="sliderinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.overlay.easing" data-theme="dark"></select>';
			_h += ' 			<div class="div5"></div>';	
			_h += '				<row class="direktrow">';
			_h += '					<onelong><label_icon class="ui_duration"></label_icon><input class="sliderinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.overlay.duration" data-min="100" data-max="10000" type="text"></onelong>';
			_h += '					<oneshort><label_icon class="ui_published"></label_icon><input class="sliderinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.overlay.delay" data-min="10" data-max="10000" type="text"></oneshort>';
			_h += '				</row>';
			_h += '			</div>';
			_h += '		</div>';
			_h += '</div>';
			
			
			
			// append settings markup
			addon.forms.slidergeneral.append(_h);

		}
		
		function initInputs() {
			
			// easings init
			addon.forms.slidergeneral.find('.easingSelect').each(function() {
				RVS.F.createEaseOptions(this);
			});			
			
			// init select2RS
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:revslider_revealer_addon.placeholder_select
			});
									
			// on/off init
			RVS.F.initOnOff(addon.forms.slidergeneral);
			
			// colorPicker init
			RVS.F.initTpColorBoxes(addon.forms.slidergeneral.find('.my-color-field'));	
			
			// update easy inits
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral, trigger:"init"});
			
		}
				
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(revslider_revealer_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {			
				var obj = {slug: 'revealer_addon'};
				$.extend(true, obj, revslider_revealer_addon.help);
				HelpGuide.add(obj);				
			}
		
		}


})(jQuery);