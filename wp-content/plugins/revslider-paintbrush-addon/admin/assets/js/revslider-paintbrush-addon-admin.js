/***************************************************
 * REVOLUTION 6.0.0 PAINTBRUSH ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function($) {
	
		
	
		var addon = {},
			slug = "revslider-paintbrush-addon",
			bricks = revslider_paintbrush_addon.bricks;

		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {	
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) slideDefaults();				
			
			var init = !addon.initialised;
			if(init && addon.isActive) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:"format_paint", title:bricks.paintbrush, alias:bricks.paintbrush, slide:true});				
				
				// PICK THE CONTAINERS WE NEED
				addon.forms = {slidegeneral: $('#form_slidegeneral_'+slug)};
				
				createSlideSettingsFields();
				addon.forms.imageWrap = $('#paintbrush_image_wrap');
				addon.forms.imagePreview = $('#paintbrush_preview');
				addon.forms.note = $('#paintbrush_note');
				
				addEvents();
				initInputs();
				initHelp();				
				events.updateDisplay();
				addon.initialised = true;
				
			}

			// UDPATE FIELDS ID ENABLE
			if(addon.isActive) {
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
				
				// update the stage
				if(!init && RVS.S.slideId.toString().search('static') === -1) {
					
					//Update Input Fields in Slider Settings
					RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});
					
					// update view
					events.updateDisplay();
				}
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('paintbrush_addon');
				
			} 
			else {
				
				if(!init) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});			
					$('#gst_slide_'+slug).removeClass("selected");	
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('paintbrush_addon');
				
			}	
			
		});
		
		// write default data
		function checkSlideDefaults(_) {
			
			return _===undefined || _.image===undefined ? 
				{
					enable : false,
					image: {
						source: 'local',
						custom: '',
						blur: {
							enable: false,
							amount: 10,
							responsive: false,
							fixedges: {	
								enable: false,
								amount: 10
							}
						},
					},
					brush: {
						style: 'round',
						size: 80,
						responsive: false,
						disappear: {
							enable: false,
							time: 1000
						}
					},
					mobile: {
						disable: false,
						fallback: false
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
			_h += '<div class="form_inner_header"><i class="material-icons">format_paint</i>'+bricks.imagesettings+'</div>';
			_h += '<div class="collapsable" style="display:block !important; padding: 0">';																
			_h += ' 	<div style="padding: 20px 20px 0 20px">';
			_h += '			<label_a>'+bricks.active+'</label_a';
			_h += ' 		><input type="checkbox" id="paintbrush_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.enable" data-showhide=".paintbrush_slide_settings" data-showhidedep="true" value="off">';
			_h += ' 	</div>';
			_h += ' 	<div class="paintbrush_slide_settings">';
			_h += ' 		<div style="padding: 0 20px">';
			_h += '				<label_a>' + bricks.source + '</label_a';
			_h += '     		><select id="paintbrush_image_source" class="slideinput tos2 nosearchbox easyinit callEvent" data-r="addOns.'+slug+'.image.source" data-evt="paintbrushUpdatePreview" data-showprio="hide" data-show="#paintbrush_select_image:not(.paintbrush-os_hide_*val*)" data-hide=".paintbrush-os_hide_*val*">';
			_h += ' 				<option value="local" selected>'+bricks.custom+'</option>';
			_h += ' 				<option value="main">'+bricks.slidebg+'</option>';
			_h += '     		</select>';
			_h += ' 			<input type="hidden" data-r="addOns.'+slug+'.image.custom">';
			_h += ' 		    <div id="paintbrush_select_image" class="paintbrush-os_hide_main">';
			_h += '					<label_a></label_a';
			_h += ' 				><div class="getImageFromMediaLibrary basic_action_button longbutton callEventButton" data-evt="paintbrushUpdatePreview" data-r="#slide#.slide.addOns.'+slug+'.image.custom"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>';
			_h += ' 				<span class="linebreak"></span>';	
			_h += '					<label_a></label_a';
			_h += ' 				><div class="getImageFromObjectLibrary basic_action_button longbutton callEventButton" data-evt="paintbrushUpdatePreview" data-r="#slide#.slide.addOns.'+slug+'.image.custom"><i class="material-icons">style</i>'+bricks.objectlibrary+'</div>';
			_h += ' 				<span class="linebreak"></span>';	
			_h += ' 			</div>';
			_h += ' 			<div id="paintbrush_note">';
			_h += ' 				<row class="direktrow">';
			_h += ' 					<div class="div5"></div>';
			_h += '						<labelhalf><i class="material-icons">sms_failed</i></labelhalf';
			_h += ' 					><contenthalf class="function_info">' + bricks.note + '</contenthalf>';
			_h += ' 					<div class="div20"></div>';
			_h += ' 				</row>';
			_h += ' 			</div>';
			_h += ' 			<div id="paintbrush_image_wrap" style="display: none">';
			_h += '					<label_a></label_a';
			_h += ' 				><div id="paintbrush_preview" style="display: inline-block; margin-bottom: 10px; width: 185px; height: 100px; background-size: cover; background-repeat: none; background-position: center center"></div>';
			_h += ' 			</div>';
			_h += '				<label_a>'+bricks.blurimage+'</label_a';
			_h += ' 			><input type="checkbox" id="paintbrush_blur_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.image.blur.enable" data-showhide=".paintbrush_blur_settings" data-showhidedep="true" value="off">';
			_h += ' 		    <div class="paintbrush_blur_settings">';
			_h += '					<label_a>' + bricks.bluramount + '</label_a';
			_h += '     			><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.image.blur.amount" data-numeric="true" data-allowed="px" data-min="1" data-max="100" value="10">';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>'+bricks.responsive+'</label_a';
			_h += ' 				><input type="checkbox" id="paintbrush_responsive_blur" class="slideinput easyinit" data-r="addOns.'+slug+'.image.blur.responsive" value="off">';
			_h += ' 				<span class="linebreak"></span>';
			_h += '					<label_a>'+bricks.fixedges+'</label_a';
			_h += ' 				><input type="checkbox" id="paintbrush_fixedges" class="slideinput easyinit" data-r="addOns.'+slug+'.image.blur.fixedges.enable" data-showhide=".paintbrush_fixedges_settings" data-showhidedep="true" value="off">';
			_h += ' 				<div class="paintbrush_fixedges_settings">';
			_h += '						<label_a>' + bricks.stretchamount + '</label_a';
			_h += '     				><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.image.blur.fixedges.amount" data-numeric="true" data-allowed="%" data-min="0" data-max="100" value="10">';
			_h += ' 				</div>';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="paintbrush_brush_settings" class="form_inner_header" style="margin-top: 20px"><i class="material-icons">brush</i>'+bricks.brushsettings+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>' + bricks.brushstyle + '</label_a';
			_h += '     		><select id="paintbrush_brush_style" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.brush.style">';
			_h += ' 				<option value="round">Round</option>';
			_h += ' 				<option value="square">Square</option>';
			_h += ' 				<option value="butt">Butt</option>';
			_h += '     		</select>';
			_h += ' 			<span class="linebreak"></span>';
			_h += '				<label_a>' + bricks.brushsize + '</label_a';
			_h += '     		><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.brush.size" data-allowed="px" data-numeric="true" data-min="5" data-max="500" value="80">';
			_h += ' 			<span class="linebreak"></span>';
			_h += '				<label_a>' + bricks.brushstrength + '</label_a';
			_h += '     		><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.brush.strength" data-allowed="px" data-numeric="true" data-min="0" data-max="500" value="30">';
			_h += ' 			<span class="linebreak"></span>';
			_h += '				<label_a>'+bricks.responsive+'</label_a';
			_h += ' 			><input type="checkbox" id="paintbrush_responsivesize" class="slideinput easyinit" data-r="addOns.'+slug+'.brush.responsive" value="off">';
			_h += ' 			<span class="linebreak"></span>';
			_h += '				<label_a>'+bricks.disappear+'</label_a';
			_h += ' 			><input type="checkbox" id="paintbrush_disappear" class="slideinput easyinit" data-r="addOns.'+slug+'.brush.disappear.enable" data-showhide=".paintbrush_disappear_settings" data-showhidedep="true" value="off">';
			_h += ' 			<div class="paintbrush_disappear_settings">';
			_h += '					<label_a>' + bricks.fadetime + '</label_a';
			_h += '     			><input type="text" class="slideinput easyinit" data-r="addOns.'+slug+'.brush.disappear.time" data-numeric="true" data-allowed="ms" data-min="100" data-max="10000" value="1000">';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 		<div id="paintbrush_mobile_settings" class="form_inner_header"><i class="material-icons">phone_iphone</i>'+bricks.mobile+'</div>';
			_h += ' 		<div style="padding: 20px">';
			_h += '				<label_a>' + bricks.disable + '</label_a';
			_h += ' 			><input type="checkbox" id="paintbrush_mobiledisable" class="slideinput easyinit" data-r="addOns.'+slug+'.mobile.disable" value="off" data-showhide="#paintbrush_mobile_settings" data-showhidedep="true">';
			_h += ' 			<div id="paintbrush_mobile_settings">';
			_h += '					<label_a>' + bricks.fallback + '</label_a';
			_h += ' 				><input type="checkbox" id="paintbrush_mobilefallback" class="slideinput easyinit" data-r="addOns.'+slug+'.mobile.fallback" value="off">';
			_h += ' 			</div>';
			_h += ' 		</div>';
			_h += ' 	</div>';
			_h += '</div>';
			
			// append settings markup
			addon.forms.slidegeneral.append(_h);
			
		}
		
		function initInputs() {
			
			// init select2
			addon.forms.slidegeneral.find('.tos2.nosearchbox').select2({
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
				
				var img,
					src, 
					obj;
				
				if(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].image.source === 'main') {
					obj = RVS.F.getSlideBGDrawObj();
					img = src = obj.backgroundImage;
					if(src && src !== 'url()') addon.forms.note.hide();
					else addon.forms.note.show();
				}
				else {
					img = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].image.custom;
					addon.forms.note.hide();
					src = 'url(' + img + ')';
				}
						
				if(img && img !== 'url()') {
					addon.forms.imagePreview.css('background-image', src);
					addon.forms.imageWrap.show();
				}
				else {
					addon.forms.imageWrap.hide();
				}
				
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
				
			}
			
		};
		
		function addEvents() {
			
			RVS.DOC.on('SceneUpdatedAfterRestore.paintbrush redrawSlideBGDone paintbrushUpdatePreview', events.updateDisplay)
							.on('newSlideCreated', events.newSlideCreated);
		
			
		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(revslider_paintbrush_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
			
				var obj = {slug: 'paintbrush_addon'};
				$.extend(true, obj, revslider_paintbrush_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}


})(jQuery);