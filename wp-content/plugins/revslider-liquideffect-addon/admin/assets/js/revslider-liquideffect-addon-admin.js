/***************************************************
 * REVOLUTION 6.0.0 DISTORTION EFFECT ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function($) {		
		var addon = {},
			slug = 'revslider-liquideffect-addon',
			bricks = revslider_liquideffect_addon.bricks;

		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {	
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) slideDefaults();				
			
			var init = !addon.initialised;
			if(init && addon.isActive) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:'fingerprint', title:bricks.distortion, alias:bricks.distortion, slide:true});				
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms = {slidegeneral : $('#form_slidegeneral_'+slug)};
				
				initTemplates();
				createSlideSettingsFields();
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms.imageWrap = $('#distortion_image_wrap');
				addon.forms.preview = $('#distortion_preview');
				addon.forms.note = $('#distortion_note');
				
				addEvents();
				initInputs();	
				initHelp();
				events.updateDisplay();
				addon.initialised = true;
				
			}

			// UDPATE FIELDS ID ENABLE
			if(addon.isActive) {
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:'inline-block'});
				
				// update the stage
				if(!init && RVS.S.slideId.toString().search('static') === -1) {
					
					//Update Input Fields in Slider Settings
					RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});
					
					// update view
					events.updateDisplay();
				}
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('liquideffect_addon'); 
				
			} 
			else {
				
				if(!init) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set('#gst_slide_'+slug,{display:'none'});			
					$('#gst_slide_'+slug).removeClass('selected');	
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('liquideffect_addon'); 
				
			}	
			
		});
		
		// write default data
		function checkSlideDefaults(_) {
			
			return _===undefined || _.map===undefined ? 
				{
					enable: false,
					map: {
						image: 'Ripple',
						custom: '',
						size: 'Large'
					},
					animation: {
						enable: true,
						speedx: 2,
						speedy: 20,
						rotation: 0,
						rotationx: 20,
						rotationy: 0,
						scalex: 20,
						scaley: 20
					},
					transition: {
						enable: true,
						cross: true,
						duration: 1000,
						easing: 'Power3.easeOut',
						speedx: 2,
						speedy: 100,
						rotation: 0,
						rotationx: 20,
						rotationy: 0,
						scalex: 2,
						scaley: 1280,
						power: false
					},
					interaction: {
						enable: false,
						event: 'mousemove',
						duration: 500,
						easing: 'Power2.easeOut',
						speedx: 0,
						speedy: 0,
						rotation: 0,
						scalex: 2,
						scaley: 1280,
						disablemobile: false
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
		
		function initTemplates() {
			
			addon.defaults = {
			
				clouds_small: {title: 'Clouds Small', preset: {enable: true, map: {image:'Clouds',size:'Small',custom:''},animation: {enable:true,speedx:2,speedy:2,rotationx:0,rotationy:0,rotation:0,scalex:5,scaley:5},transition: {enable:true,cross:false,duration:1000,easing:'Power3.easeOut',speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:200,scaley:200,power:false},interaction: {enable:true,event:'mousedown',duration:500,easing:'Power2.easeOut',speedx:-2,speedy:-2,scalex:600,scaley:300,rotation:0,disablemobile:true}}},
				clouds_large: {title: 'Clouds Large', preset: {enable: true, map: {image:'Clouds',size:'Large',custom:''},animation: {enable:true,speedx:10,speedy:3,rotationx:0,rotationy:0,rotation:0,scalex:20,scaley:20},transition: {enable:true,cross:false,duration:2000,easing:'Power3.easeOut',speedx:10,speedy:3,rotationx:0,rotationy:0,rotation:0,scalex:5,scaley:5,power:false},interaction: {enable:false,event:'mousemove',duration:500,easing:'Power2.easeOut',speedx:0,speedy:0,scalex:2,scaley:1280,rotation:0,disablemobile:false}}},
				ripple_large: {title: 'Ripple Large', preset: {enable: true, map: {image:'Ripple',size:'Large',custom:''},animation: {enable:true,speedx:2,speedy:20,rotationx:20,rotationy:0,rotation:0,scalex:20,scaley:20},transition: {enable:true,cross:true,duration:2000,easing:'Power3.easeOut',speedx:2,speedy:100,rotationx:20,rotationy:0,rotation:0,scalex:200,scaley:2000,power:false},interaction: {enable:true,event:'mousemove',duration:500,easing:'Power2.easeOut',speedx:0,speedy:0,scalex:200,scaley:200,rotation:0,disablemobile:true}}},
				crystalize_small: {title: 'Crystalize Small', preset: {enable: true, map: {image:'Crystalize',size:'Small',custom:''},animation: {enable:false,speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:0,scaley:0},transition: {enable:true,cross:true,duration:1000,easing:'Power4.easeOut',speedx:1000,speedy:1000,rotationx:0,rotationy:0,rotation:0,scalex:300,scaley:300,power:true},interaction: {enable:false,event:'mousedown',duration:1000,easing:'Linear.easeNone',speedx:0,speedy:0,scalex:1200,scaley:200,rotation:0,disablemobile:false}}},
				swirl_large: {title: 'Swirl Large', preset: {enable: true, map: {image:'Swirl',size:'Large',custom:''},animation: {enable:true,speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0.2,scalex:2,scaley:2},transition: {enable:true,cross:true,duration:2000,easing:'Power3.easeOut',speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:2,scaley:2,power:false},interaction: {enable:true,event:'mousemove',duration:300,easing:'Power2.easeOut',speedx:0,speedy:0,scalex:0,scaley:0,rotation:0.4,disablemobile:true}}},
				fibers_small: {title: 'Fibers Small', preset: {enable: true, map: {image:'Fibers',size:'Small',custom:''},animation: {enable:true,speedx:0.5,speedy:-0.5,rotationx:0,rotationy:0,rotation:0,scalex:4,scaley:4},transition: {enable:false,cross:true,duration:2000,easing:'Power3.easeOut',speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:0,scaley:0,power:false},interaction: {enable:false,event:'mousemove',duration:300,easing:'Power2.easeOut',speedx:0,speedy:0,scalex:2,scaley:2,rotation:0,disablemobile:false}}},
				spiral_large: {title: 'Spiral Large', preset: {enable: true, map: {image:'Spiral',size:'Large',custom:''},animation: {enable:true,speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:-0.2,scalex:5,scaley:5},transition: {enable:false,cross:true,duration:2000,easing:'Power3.easeOut',speedx:0,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:0,scaley:0,power:false},interaction: {enable:false,event:'mousemove',duration:300,easing:'Power2.easeOut',speedx:0,speedy:0,scalex:0,scaley:0,rotation:-0.3,disablemobile:false}}},
				glitch_small: {title: 'Glitch Small', preset: {enable: true, map: {image:'Glitch',size:'Small',custom:''},animation: {enable:true,speedx:50,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:0,scaley:0},transition: {enable:false,cross:true,duration:2000,easing:'Power3.easeOut',speedx:50,speedy:0,rotationx:0,rotationy:0,rotation:0,scalex:15,scaley:5,power:false},interaction: {enable:true,event:'mousemove',duration:300,easing:'Power2.easeOut',speedx:10,speedy:0,scalex:15,scaley:5,rotation:0,disablemobile:true}}}
				
			};
			
			addon.customs = revslider_liquideffect_addon.custom_templates===undefined ? {} : revslider_liquideffect_addon.custom_templates;
			
		}
					
		// CREATE INPUT FIELDS
		function createSlideSettingsFields() {
			
			var plist = RVS.F.createPresets({ 
			
				groupid: 'distortion_templates',			
				title: bricks.bmlibrary,
				customevt: 'distortionAjax',
				groups: {defaults: {title: bricks.presets, elements: addon.defaults}, custom: {title: bricks.customprests, elements: addon.customs}},
				onclick: events.presets
				 
			});
			
			var _h = '';							
			_h += '<div class="form_inner_header"><i class="material-icons">fingerprint</i>'+bricks.settings+'</div>';
			_h += '<div class="collapsable" style="display:block !important; padding: 0">';																
			_h += ' 	<div style="padding: 20px 20px 0 20px">';
			_h += '			<label_a>'+bricks.active+'</label_a';
			_h += ' 		><input type="checkbox" id="distortion_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.enable" data-showhide="#distortion_slide_settings" data-showhidedep="true" value="off">';
			_h += ' 		<div class="div10"></div>';
			_h += ' 	</div>';
			_h += ' 	<div id="distortion_slide_settings">';
			_h += ' 		<div style="padding: 0 20px">';
			_h += ' 			<row id="distortion_note" class="direktrow" style="margin-top: -10px">';
			_h += ' 				<div class="div5"></div>';
			_h += '					<labelhalf><i class="material-icons">sms_failed</i></labelhalf';
			_h += ' 				><contenthalf class="function_info">' + bricks.note + '</contenthalf>';
			_h += ' 				<div class="div10"></div>';
			_h += ' 			</row>';
			_h += ' 		</div>';
			
			_h += ' 		<div id="distortion_map_wrap" class="form_inner_header" style="margin-top: 20px"><i class="material-icons">map</i>'+bricks.map+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += plist;
			_h += ' 			<label_a>' + bricks.imagemap + '</label_a';
			_h += '     		><select id="distortion_map" class="slideinput tos2 nosearchbox easyinit callEvent" data-r="addOns.'+slug+'.map.image" data-evt="distortionUpdateDisplay" data-showprio="hide" data-hide="#distortion_choose_image:not(.distortion-choose_hide_*val*), .distortion-size_hide_*val*" data-show="#distortion_size_wrap:not(.distortion-size_hide_*val*), .distortion-choose_hide_*val*">';
			_h += ' 				<option value="Ripple">Ripple</option>';
			_h += ' 				<option value="Clouds">Clouds</option>';
			_h += ' 				<option value="Crystalize">Crystalize</option>';
			_h += ' 				<option value="Fibers">Fibers</option>';
			_h += ' 				<option value="Pointilize">Pointilize</option>';
			_h += ' 				<option value="Rings">Rings</option>';
			_h += ' 				<option value="Maze">Maze</option>';
			_h += ' 				<option value="Glitch">Glitch</option>';
			_h += ' 				<option value="Swirl">Swirl</option>';
			_h += ' 				<option value="Spiral">Spiral</option>';
			_h += ' 				<option value="Custom Map">Custom Map</option>';
			_h += '     		</select>';
			_h += ' 			<span class="linebreak"></span>';
			_h += ' 			<div id="distortion_size_wrap" class="distortion-size_hide_CustomMap">';
			_h += '					<label_a>' + bricks.size + '</label_a';
			_h += '     			><select id="distortion_map_size" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.map.size">';
			_h += ' 					<option value="Small">Small</option>';
			_h += ' 					<option value="Large">Large</option>';
			_h += ' 				</select>';
			_h += ' 				<span class="linebreak"></span>';
			_h += ' 			</div>';
			_h += ' 			<div id="distortion_choose_image" class="distortion-choose_hide_CustomMap">';
			_h += '					<label_a></label_a';
			_h += ' 				><div id="distortion_custom_image" class="getImageFromMediaLibrary basic_action_button longbutton callEventButton" data-evt="distortionChooseImage"><i class="material-icons">style</i>'+bricks.library+'</div>';
			_h += ' 			</div>';
			_h += ' 			<div id="distortion_image_wrap">';
			_h += '					<label_a></label_a';
			_h += ' 				><div id="distortion_preview" style="display: inline-block; margin-bottom: 10px; width: 185px; height: 100px; background-size: cover; background-repeat: none; background-position: center center"></div>';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="distortion_animation_wrap" class="form_inner_header"><i class="material-icons">movie</i>'+bricks.animation+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>'+bricks.active+'</label_a';
			_h += ' 			><input type="checkbox" id="distortion_animation_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.animation.enable" data-showhide="#distortion_animation_settings" data-showhidedep="true" value="off">';
			_h += ' 			<div id="distortion_animation_settings">';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.animation.speedx" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.animation.speedy" type="text"></oneshort>';
			_h += '					</row>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.animation.scalex" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.animation.scaley" type="text"></oneshort>';
			_h += '					</row>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_rotatex"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.animation.rotationx" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_rotatey"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.animation.rotationy" type="text"></oneshort>';
			_h += '					</row>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_rotatez"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.animation.rotation" type="text"></onelong>';
			_h += '					</row>';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="distortion_transition_wrap" class="form_inner_header"><i class="material-icons">flash_on</i>'+bricks.transition+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>'+bricks.active+'</label_a';
			_h += ' 			><input type="checkbox" id="distortion_transition_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.transition.enable" data-showhide="#distortion_transition_settings" data-showhidedep="true" value="off">';
			_h += ' 			<div id="distortion_transition_settings">';
	
			_h += '					<label_a>' + bricks.easing + '</label_a';
			_h += '     			><select class="slideinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.transition.easing" data-theme="dark"></select>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>' + bricks.duration + '</label_a';
			_h += '     			><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.transition.duration" data-numeric="true" data-allowed="ms" data-min="300" data-max="10000">';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.transition.speedx" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.transition.speedy" type="text"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.transition.scalex" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.transition.scaley" type="text"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_rotatex"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.transition.rotationx" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_rotatey"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.transition.rotationy" type="text"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_rotatez"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.transition.rotation" type="text"></onelong>';
			_h += '						<oneshort><i class="material-icons" style="width: 30px; height: 30px; text-align: center; color: #b7bbc0; font-size: 17px; margin-right: 5px">power</i><input type="checkbox" id="distortion_transpower" class="slideinput easyinit" data-r="addOns.'+slug+'.transition.power" value="off"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow" style="padding-top: 10px">';
			_h += '						<labelhalf><i class="material-icons">sms_failed</i></labelhalf>';
			_h += '						<contenthalf><div class="function_info">' + bricks.transmessage + '</div></contenthalf>';
			_h += '					</row>';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="distortion_interaction_wrap" class="form_inner_header"><i class="material-icons">gamepad</i>'+bricks.interaction+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>'+bricks.active+'</label_a';
			_h += ' 			><input type="checkbox" id="distortion_interaction_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.interaction.enable" data-showhide="#distortion_interaction_settings" data-showhidedep="true" value="off">';
			_h += ' 			<div id="distortion_interaction_settings">';
			_h += '					<label_a>' + bricks.mobile + '</label_a';
			_h += ' 				><input type="checkbox" id="distortion_disablemobile" class="slideinput easyinit" data-r="addOns.'+slug+'.interaction.disablemobile" value="off">';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>' + bricks.mouse + '</label_a';
			_h += '     			><select id="distortion_mouse_event" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.interaction.event">';
			_h += ' 					<option value="mousedown">Mouse Down</option>';
			_h += ' 					<option value="mousemove">Mouse Move</option>';
			_h += ' 				</select>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>' + bricks.easing + '</label_a';
			_h += '     			><select class="slideinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.interaction.easing" data-theme="dark"></select>';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>' + bricks.duration + '</label_a';
			_h += '     			><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.interaction.duration" data-numeric="true" data-allowed="ms" data-min="300" data-max="10000">';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.interaction.speedx" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y_start"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.interaction.speedy" type="text"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_x"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.interaction.scalex" type="text"></onelong>';
			_h += '						<oneshort><label_icon class="ui_y"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.interaction.scaley" type="text"></oneshort>';
			_h += '					</row>';
			_h += '					<row class="direktrow">';
			_h += '						<onelong><label_icon class="ui_rotatez"></label_icon><input class="slideinput easyinit shortfield" data-numeric="true" data-allowed="deg" data-r="addOns.'+slug+'.interaction.rotation" type="text"></onelong>';
			_h += '					</row>';
			_h += '					<row class="direktrow" style="padding-top: 10px">';
			_h += '						<labelhalf><i class="material-icons">sms_failed</i></labelhalf>';
			_h += '						<contenthalf><div class="function_info">' + bricks.intermessage + '</div></contenthalf>';
			_h += '					</row>';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 	</div>';
			_h += '</div>';
			
			// append settings markup
			addon.forms.slidegeneral.append(_h);
			
		}
		
		function initInputs() {
			
			// easings init
			addon.forms.slidegeneral.find('.easingSelect').each(function() {
				RVS.F.createEaseOptions(this);
			});			
			
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
		
		// needed to sanitize the "updateslidebasic" event
		function displayChecks() {
			
			if(!addon.isActive || RVS.S.slideId.toString().search('static') !== -1) return true;
			
			// make sure defaults exist
			RVS.SLIDER[RVS.S.slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]);
			return false;
			
		}
		
		var events = {
		
			updateDisplay: function() {
				
				if(displayChecks()) return;
				var map = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].map.image;
				
				if(map !== 'Custom Map') map = revslider_liquideffect_addon.baseurl + map.toLowerCase() + '_small.jpg';
				else map = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].map.custom;
				
				if(map) {
					addon.forms.preview.css('background-image', 'url(' + map + ')');
					addon.forms.imageWrap.show();
				}
				else {
					addon.forms.imageWrap.hide();
				}
				
			},
			
			chooseImage: function(e, url) {
				
				if(!url || !url.urlImage) return;
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].map.custom = url.urlImage;
				events.updateDisplay();
				
			},
			
			slideBgUpdated: function() {
			
				var bgType = RVS.SLIDER[RVS.S.slideId].slide.bg.type,
					src;
					
				if(bgType === 'image' || bgType === 'external') {
					
					if(bgType === 'image') src = RVS.SLIDER[RVS.S.slideId].slide.bg.image;
					else src = RVS.SLIDER[RVS.S.slideId].slide.bg.externalSrc;
					
				}
				
				if(src) addon.forms.note.hide();
				else addon.forms.note.show();
				
				events.updateDisplay();
				
			},
		
			// write defaults and update fields upon new slide creation
			newSlideCreated: function(e, id) {
				
				if(!addon.isActive) return;
					
				// check defaults
				RVS.SLIDER[id].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[id].slide.addOns[slug]);
				
				// update fields
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: id + '.slide.'});
				
				// update preview image
				events.updateDisplay();
				
			},
			
			presets: function(key, custom) {
				
				RVS.F.openBackupGroup({id: 'liquideffect', txt: bricks.presets, icon: 'touch_app'});
				var obj = custom === 'true' || custom === true ? addon.customs[key].preset : addon.defaults[key].preset;
				
				RVS.F.updateSliderObj({path: RVS.S.slideId + '.slide.addOns.' + slug, val: obj});
				RVS.F.closeBackupGroup({id: 'liquideffect'});
				
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger: 'init'});
				events.updateDisplay();
				
			},
			
			ajax: function(e, _) {
				
				var preset,
					key;
								
				// GET CHANGES
				if (_.mode==="overwrite" || _.mode==="create") preset = $.extend(true,{}, RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]);
				
				// GET TINDEX
				if (_.mode==="overwrite" || _.mode=="rename") key = _.pl.data("key");

				// RENAME, TAKE FIRST EXISTING OBJECT
				if (_.mode==="rename") { preset = addon.customs[_.key].preset; addon.customs[_.key].title=_.newname;}
				
				if (_.mode==="delete") {
					RVS.F.ajaxRequest('delete_custom_templates_'+slug, {id:_.key},function(response) {
						if (response.success) {
							delete addon.customs[_.key];
							_.pl.remove();
						}
					});	
				} else {
					
					// CALL CREATE / RENAME / OVERWRITE AJAX FUNCTION
					RVS.F.ajaxRequest('save_custom_templates_'+slug, {id:_.key, obj:{title:_.newname, preset:preset}}, function(response){						
						if(response.success) {
							addon.customs[response.data.id] = {title:_.newname, preset:preset};		
							if (_.mode==="create") _.element[0].dataset.key = response.data.id;
							if (_.mode==="rename") _.pl.find('.cla_custom_name').text(_.newname);
						}
					});	
				}
				
				runonce = true;
				
			}
			
		};
		
		function addEvents() {
			
			RVS.DOC.on('SceneUpdatedAfterRestore.distortioneffect distortionUpdateDisplay', events.updateDisplay)
							.on('newSlideCreated', events.newSlideCreated)
							.on('redrawSlideBGDone', events.slideBgUpdated)
							.on('distortionChooseImage', events.chooseImage)
							.on('distortionAjax', events.ajax);

		
			
		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(revslider_liquideffect_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
			
				var obj = {slug: 'liquideffect_addon'};
				$.extend(true, obj, revslider_liquideffect_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}


})(jQuery);