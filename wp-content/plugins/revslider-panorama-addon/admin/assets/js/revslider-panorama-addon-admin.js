/***************************************************
 * REVOLUTION 6.0.0 PANORAMA ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function($) {
	
		
	
		var addon = {},
			slug = "revslider-panorama-addon",
			bricks = revslider_panorama_addon.bricks;

		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {	
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) slideDefaults();	
			
			// FIRST TIME INITIALISED
			var init = !addon.initialised;
			if(init && addon.isActive) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:"360", title:bricks.panorama, alias:bricks.panorama, slide:true});				
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms = {slidegeneral : $('#form_slidegeneral_'+slug)};				
				createSlideSettingsFields();	

				// init events
				addEvents();
				initInputs();
				initHelp();
				addon.initialised = true;
				
			}

			// UDPATE FIELDS ID ENABLE
			if(addon.isActive) {	
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
				
				// update the stage
				if(!init && RVS.S.slideId.toString().search('static') === -1) {
					RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});
				}
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('panorama_addon'); 
				
				// show actions
				$('body').addClass('panorama-active');
				
			} 
			else {
				
				if(!init) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});			
					$('#gst_slide_'+slug).removeClass("selected");	
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('panorama_addon'); 
				
				// hide actions
				$('body').removeClass('panorama-active');
				
			}	
			
		}).on("extendLayerActionGroups",function() {
			
			// Build INPUT FIELDS
			var wrap = document.getElementById("layer_action_extension_wrap"),
				_ = '';
			
			_ += '<div id="la_settings_panorama_fields" class="la_settings">';
			_ += '    <label_a>'+bricks.distance+'</label_a><select class="easyinit actioninput" id="panorama_amount" data-r="actions.action.#actionindex#.panorama_amount" value="5">';
			_ += '        <option value="5" selected>5%</option>';
			_ += '        <option value="10">10%</option>';
			_ += '        <option value="15">15%</option>';
			_ += '        <option value="20">20%</option>';
			_ += '        <option value="25">25%</option>';
			_ += '        <option value="33">33%</option>';
			_ += '        <option value="50">50%</option>';
			_ += '    </select>';
			_ += '</div>';

			wrap.innerHTML += _;
			
			$('#panorama_amount').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:bricks.placeholder_select
			});
			
			RVS.F.createActionGroup({title: bricks.panorama, icon:"360", id:"layeraction_group_panorama", actions:[
			
				{val:"panorama_left", alias:bricks.actions_left, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_leftstart", alias:bricks.actions_leftstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_leftend", alias:bricks.actions_leftend},
				{val:"panorama_right", alias:bricks.actions_right, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_rightstart", alias:bricks.actions_rightstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_rightend", alias:bricks.actions_rightend},
				{val:"panorama_up", alias:bricks.actions_up, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_upstart", alias:bricks.actions_upstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_upend", alias:bricks.actions_upend},
				{val:"panorama_down", alias:bricks.actions_down, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_downstart", alias:bricks.actions_downstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_downend", alias:bricks.actions_downend},
				{val:"panorama_zoomin", alias:bricks.actions_zoomin, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_zoominstart", alias:bricks.actions_zoominstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_zoominend", alias:bricks.actions_zoominend},
				{val:"panorama_zoomout", alias:bricks.actions_zoomout, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_zoomoutstart", alias:bricks.actions_zoomoutstart, inputs:"#la_settings_panorama_fields"},
				{val:"panorama_zoomoutend", alias:bricks.actions_zoomoutend}
					
			]});
			
			/*
				NOTE: show/hide
				hack to fix bug where select2RS doesn't populate with a default option for some reason
			*/
			$('body').on('click', '#layeraction_group_panorama .lal_group_member', function() {
				
				if($(this).attr('data-val').search('end') !== -1) return;
				
				setTimeout(function() {
					$('#panorama_amount').val('5').trigger('change');
				}, 100);
				
			});
			
		});
		
		// write default data
		function checkSlideDefaults(_) {
			
			return _===undefined || _.interaction===undefined ? 
				{
					enable : false,
					mobilelock: true,
					autoplay: {
						enable: false,
						direction: "forward",
						speed: 100
					},
					interaction: {
						controls: "throw",
						speed: 750,
						lockVertical:false,
					},
					zoom: {
						enable: false,
						smooth: true,
						min: 75,
						max: 150
					},
					camera: {
						fov: 75,
						far: 1000
					},
					sphere: {
						radius: 100,
						wsegments: 100,
						hsegments: 40
					}
				} : _;	
		}
		
		//Migrate Datas
		function slideDefaults() {

			var ids = RVS.SLIDER.slideIDs;
			for(var id in ids) {
				
				if(!ids.hasOwnProperty(id)) continue;
				var slideId = ids[id];
				
				// skip writing to static slide
				if(slideId.toString().search('static') !== -1) continue;
				RVS.SLIDER[slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[slideId].slide.addOns[slug]);
				
			}
			
		}
					
		// CREATE INPUT FIELDS
		function createSlideSettingsFields() {
									
			var _h = '';							
			_h += '<div class="form_inner_header"><i class="material-icons">360</i>'+bricks.settings+'</div>';
			_h += '<div class="collapsable" style="display:block !important; padding: 0">';																
			_h += ' 	<div style="padding: 20px 20px 0 20px">';
			_h += '			<label_a>'+bricks.active+'</label_a';
			_h += ' 		><input type="checkbox" id="panorama_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.enable" data-showhide=".panorama_slide_settings" data-showhidedep="true" value="off">';
			_h += ' 	</div>';
			_h += ' 	<div class="panorama_slide_settings">';
			_h += ' 		<div style="padding: 0 20px">';
			_h += '				<label_a>'+bricks.autoplay+'</label_a';
			_h += ' 			><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.autoplay.enable" data-showhide=".panorama_autoplay_settings" data-showhidedep="true" value="off">';
			_h += ' 		</div>';
			_h += ' 		<div style="height: 20px"></div>';
			_h += ' 		<div class="panorama_autoplay_settings" style="display: none; padding: 0 20px 20px 20px; margin-top: -20px">';
			_h += '				<label_a>' + bricks.direction + '</label_a';
			_h += '     		><select id="panorama_direction" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.autoplay.direction">';
			_h += ' 				<option value="forward" selected>'+bricks.forward+'</option>';
			_h += ' 				<option value="backward">'+bricks.backward+'</option>';
			_h += '     		</select>';
			_h += ' 			<span class="linebreak"></span>';	
			_h += '				<label_a>' + bricks.speed + '</label_a';
			_h += '     		><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.autoplay.speed" data-numeric="true" data-allowed="ms" value="100">';
			_h += ' 		</div>';
			_h += ' 		<div id="panorama-interaction-wrap" class="form_inner_header"><i class="material-icons">gamepad</i>'+bricks.interaction+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>' + bricks.interaction + '</label_a';
			_h += '     		><select id="panorama_interaction" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.interaction.controls" data-showprio="hide" data-hide="#panorama_interaction_speed:not(.panorama-os_show_*val*")" data-show=".panorama-os_show_*val*">';
			_h += ' 				<option value="throw" selected>'+bricks.throww+'</option>';
			_h += ' 				<option value="drag">'+bricks.drag+'</option>';
			_h += ' 				<option value="mouse" selected>'+bricks.mouse+'</option>';
			_h += ' 				<option value="click">'+bricks.click+'</option>';
			_h += ' 				<option value="none" selected>'+bricks.none+'</option>';
			_h += '     		</select>';
			_h += ' 			<div id="panorama_interaction_speed" class="panorama-os_show_throw">';				
			_h += '					<label_a>' + bricks.speed + '</label_a';
			_h += '     			><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.interaction.speed" value="750" data-numeric="true" data-allowed="ms">';
			_h += ' 			</div>';
			_h += '				<label_a>'+bricks.lockvertical+'</label_a';
			_h += ' 			><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.interaction.lockVertical" value="off">';
			_h += ' 		</div>';
			_h += ' 		<div id="panorama-mousewheel-wrap" class="form_inner_header"><i class="material-icons">mouse</i>'+bricks.zoom+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>'+bricks.active+'</label_a';
			_h += ' 			><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.zoom.enable" data-showhide=".panorama_zoom_settings" data-showhidedep="true" value="off">';
			_h += ' 			<div class="panorama_zoom_settings" style="display: none">';				
			_h += '					<label_a>'+bricks.smooth+'</label_a';
			_h += ' 				><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.zoom.smooth" value="off">';	
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon style="color: #b7bbc0; text-align: center"><i class="material-icons" style="font-size: 20px">zoom_in</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.zoom.min" data-min="25" data-max="100" type="text" value="75"></onelong>';
			_h += '						<oneshort><label_icon style="color: #b7bbc0; text-align: center"><i class="material-icons" style="font-size: 20px">zoom_out</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.zoom.max" data-min="100" data-max="175" type="text" value="150"></oneshort>';
			_h += '					</row>';	
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="panorama-camera-wrap" class="form_inner_header"><i class="material-icons">camera</i>'+bricks.camera_sphere+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>' + bricks.radius + '</label_a';
			_h += '     		><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.sphere.radius" value="100" data-numeric="true" data-allowed="deg">';
			_h += '				<row class="direktrow">';
			_h += '					<onelong><label_icon style="color: #b7bbc0; text-align: center"><i class="material-icons" style="font-size: 20px">more_horiz</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.sphere.wsegments" type="text" value="100"></onelong>';
			_h += '					<oneshort><label_icon style="color: #b7bbc0; text-align: center; text-align: center"><i class="material-icons" style="font-size: 20px">more_vert</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.sphere.hsegments" type="text" value="40"></oneshort>';
			_h += '				</row>';
			_h += '				<row class="direktrow">';
			_h += '					<onelong><label_icon style="color: #b7bbc0; text-align: center"><i class="material-icons" style="font-size: 20px">camera_enhance</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.camera.fov" type="text"></onelong>';
			_h += '					<oneshort><label_icon style="color: #b7bbc0; text-align: center"><i class="material-icons" style="font-size: 20px">switch_camera</i></label_icon><input class="slideinput valueduekeyboard easyinit shortfield" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.camera.far" type="text"></oneshort>';
			_h += '				</row>';
			_h += ' 		</div>';
			_h += ' 		<div id="panorama-mobile-settings" class="form_inner_header"><i class="material-icons">screen_lock_landscape</i>'+bricks.mobile_settings+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>'+bricks.mobile_lock+'</label_a';
			_h += ' 			><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.mobilelock" value="on">';
			_h += ' 		</div>';
			_h += ' 		</div>';
			_h += ' 	</div>';
			_h += '</div>';
			
			// append settings markup
			addon.forms.slidegeneral.append(_h);
			
		}
		
		function initInputs() {
			
			// init select2RS
			addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:bricks.placeholder_select
			});
			
			// skip updating fields if currentSlide === static layers
			if(RVS.S.slideId.toString().search('static') === -1) {
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger: 'init'});
			}
			
			// on/off init
			RVS.F.initOnOff(addon.forms.slidegeneral);
			
		}
		
		var events = {
			
			// write defaults and update fields upon new slide creation
			newSlideCreated: function(e, id) {
				
				if(!addon.isActive) return;
				
				// check defaults
				RVS.SLIDER[id].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[id].slide.addOns[slug]);
				
				// update fields
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: id + '.slide.'});
				
			}
			
		};
		
		function addEvents() {
			
			// callbacks
			RVS.DOC.on('newSlideCreated', events.newSlideCreated);
			
		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(typeof HelpGuide !== 'undefined' && revslider_panorama_addon.hasOwnProperty('help')) {
			
				var obj = {slug: 'panorama_addon'};
				$.extend(true, obj, revslider_panorama_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}


})(jQuery);