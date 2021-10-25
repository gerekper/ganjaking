/***************************************************
 * REVOLUTION 6.0.0 DUOTONE ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/

(function() {
	
		var addon = {},
			slug = "revslider-duotonefilters-addon",
			bricks = revslider_duotonefilters_addon.bricks;
		
		// INITIALISE THE ADDON
		jQuery(document).on(slug+'_init',function() {			
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) {
				
				RVS.SLIDER.settings.addOns[slug] = sliderDefaults(RVS.SLIDER.settings.addOns[slug]);
				slideDefaults();
				
			}	
			
			// FIRST TIME INITIALISED
			var init = !addon.initialised;
			if(init && addon.isActive) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon: 'graphic_eq', title: bricks.duotone, alias: bricks.duotone, slider: true, slide: true});				
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms = {
					slidergeneral : jQuery('#form_slidergeneral_'+slug),
					slidegeneral : jQuery('#form_slidegeneral_'+slug)
				};
				
				addon.body = jQuery('body');
				createSliderSettingsFields();
				createSlideSettingsFields();	
				
				// init events
				addEvents();	
				initInputs();
				initHelp();
				events.updateDisplay();
				addon.initialised = true;
				
			}

			if(addon.isActive) {				
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
				
				// update the stage
				if(!init && RVS.S.slideId.toString().search('static') === -1) {
						
					//Update Input Fields
					RVS.F.updateEasyInputs({container:addon.forms.slidergeneral});
					RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});	
					
					// update view
					events.updateDisplay();
					
				}
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('duotonefilters_addon'); 
				
			} 
			else {
				
				if(!init) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});
					punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});	
					
					jQuery('#gst_sl_'+slug).removeClass("selected");	
					jQuery('#gst_slide_'+slug).removeClass("selected");	
					
					// for duotone CSS
					addon.body.removeClass('duotone_active');
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('duotonefilters_addon'); 
				
			}	
			
		});
		
		function checkSlideDefaults(_) {
			
			return _===undefined || _.filter===undefined ? {filter : 'none'} : _;
			
		}
		
		// write default slide data
		function slideDefaults() {
			
			var ids = RVS.SLIDER.slideIDs,	
				_;
				
			for(var id in ids) {
				
				if(!ids.hasOwnProperty(id)) continue;
				var slideId = ids[id];
				
				// skip writing to static slide
				if(slideId.toString().search('static') !== -1) continue;
				
				// check defaults
				RVS.SLIDER[slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[slideId].slide.addOns[slug]);
				
			}
			
		}
		
		//Migrate Datas
		function sliderDefaults(_) {
			
			_ = _===undefined || _.simplify===undefined ? { 					
					enable : true,
					simplify: {
						enable: false,
						easing: 'ease-in',
						duration: 750
					}
			} : _;
				
			return _;
			
		}
		
		// CREATE INPUT FIELDS
		function createSliderSettingsFields() {
									
			var _h = '';							
			_h += '<div class="form_inner_header"><i class="material-icons">graphic_eq</i>'+bricks.duotone+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';																
			_h += '		<label_a style="width: 75px">'+bricks.simplify+'</label_a';
			_h += '     ><input type="checkbox" class="sliderinput easyinit" data-r="addOns.'+slug+'.simplify.enable" data-showhide=".duotone-simplify-wrap" data-showhidedep="true" value="on">';
			_h += '		<div class="duotone-simplify-wrap">';
			_h += '			<label_a style="width: 75px">'+bricks.easing+'</label_a';
			_h += '     	><select id="duotone_easing" class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.simplify.easing">';
			_h += ' 			<option value="ease">ease</option>';
			_h += ' 			<option value="ease-in">ease-in</option>';
			_h += ' 			<option value="ease-out">ease-out</option>';
			_h += ' 			<option value="ease-in-out">ease-in-out</option>';
			_h += ' 			<option value="linear">linear</option>';
			_h += ' 			<option value="cubic-bezier(0.550, 0.055, 0.675, 0.190)">easeInCubic</option>';
			_h += ' 			<option value="cubic-bezier(0.215, 0.610, 0.355, 1.000)">easeOutCubic</option>';
			_h += ' 			<option value="cubic-bezier(0.645, 0.045, 0.355, 1.000)">easeInOutCubic</option>';
			_h += ' 			<option value="cubic-bezier(0.895, 0.030, 0.685, 0.220)">easeInQuart</option>';
			_h += ' 			<option value="cubic-bezier(0.165, 0.840, 0.440, 1.000)">easeOutQuart</option>';
			_h += ' 			<option value="cubic-bezier(0.770, 0.000, 0.175, 1.000)">easeInOutQuart</option>';
			_h += ' 		</select>';
			_h += ' 		<span class="linebreak"></span>';
			_h += '			<label_a style="width: 75px">'+bricks.duration+'</label_a';
			_h += '     	><input type="text" class="sliderinput easyinit" data-r="addOns.'+slug+'.simplify.duration" data-numeric="true" data-allowed="ms" value="750" data-min="300" data-max="10000">';
			_h += ' 	</div>';
			_h += '</div>';
			
			addon.forms.slidergeneral.append(_h);
			
		}
					
		// CREATE INPUT FIELDS
		function createSlideSettingsFields() {
			
			var filters = ['none','blue','blue-dark','blue-light','orange','orange-dark','orange-light','red','red-dark','red-light','green','green-dark','green-light','yellow','yellow-dark','yellow-light','purple','purple-dark','purple-light','pink','pink-dark','pink-light','blue-yellow','blue-yellow-dark','blue-yellow-light','pink-yellow','pink-yellow-dark','pink-yellow-light','red-blue','red-blue-dark','red-blue-light'],
				len = filters.length;
				
			var filtr,
				_h = '',
				prefix = '',
				blends = '',
				filterTitle;
				
			_h += '<div class="form_inner_header"><i class="material-icons">graphic_eq</i>'+bricks.duotone+'</div>';
			_h += '<div class="collapsable" style="display:block !important">';						
			_h += '<label_a>' + bricks.bgfilter + '</label_a';
			_h += '><select id="duotone_bg_filter" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.filter" data-evt="updateDuotone">';
			
			function capitalize(st) {
				return st.toUpperCase();
			}
			
			for(var i = 0; i < len; i++) {
				
				filtr = filters[i];
				filterTitle = filtr.replace(/\-/g, ' ').replace(/\b\w/g, capitalize);
				_h += '<option value="' + filtr + '">' + filterTitle + '</option>';
				blends += prefix + '<div id="rs-duotone-' + filtr + '" class="inst-filter-griditem rsaddon-duotone-filter callhoverevt triggerselect" data-name="' + filterTitle + '" data-val="' + filtr + '" data-hoverevt="duotoneItmHover" data-hoverevtparam="' + filtr + '" data-select="#duotone_bg_filter">';
				blends += '    <div data-duotone="rs-duotone-' + filtr + '">';
				blends += '        <div class="rs-duotone-thumb"></div>';
				blends += '    </div>';
				blends += '</div';
				prefix = '>';
				
			}
			
			_h += '</select><span class="linebreak" style="height: 20px"></span>';
			_h += '<div class="duotone_active callhoverevt" data-hoverevt="duotoneEnter" data-leaveevt="duotoneLeave">' + blends + '></div>';
			
			addon.forms.slidegeneral.append(_h);
			
		}
		
		function initInputs() {
			
			// init select2RS slider
			addon.forms.slidergeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:bricks.placeholder_select
			});
			
			// init select2RS slide
			addon.selectFilter = addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:bricks.placeholder_select
			});
			
			// on/off init - slider
			RVS.F.initOnOff(addon.forms.slidergeneral);
			
			// init easy inputs slide
			if(RVS.S.slideId.toString().search('static') === -1) {
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger: 'init'});
			}
			
			// init easy inputs - slider
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral, trigger:"init"});
			
		}
		
		// needed to sanitize the "updateslidebasic" event
		function displayChecks() {
			
			if(!addon.isActive) return true;
			if(RVS.S.slideId.toString().search('static') !== -1) {
					
				addon.body.removeClass('duotone_active');
				return true;
				
			}
			
			// make sure defaults exist
			RVS.SLIDER[RVS.S.slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]);
			return false;
			
		}
		
		var events = {
			
			drawThumbs: function() {
				
				var obj = RVS.F.getSlideBGDrawObj();
				punchgs.TweenLite.set(jQuery('.rs-duotone-thumb'), obj);
				
			},
			
			updateDisplay: function() {
				
				if(addon.suppressUpdate || displayChecks()) return;
				
				// solves an issue where the select doesn't autoInit when importing a duotone slider from 5.0
				if(addon.selectFilter.val()) events.updateDuotone();
				else jQuery('#' + RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].filter).click();
				
			},
			
			updateDuotone: function() {
				
				var filter = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].filter;
				
				// switch thumb selection
				jQuery('.rsaddon-duotone-filter').removeClass('selected');
				jQuery('#rs-duotone-' + filter).addClass('selected');
				
				// set duotone active/inactive
				if(filter !== 'none') addon.body.addClass('duotone_active');
				else addon.body.removeClass('duotone_active');
				
				// draw filter onto the stage
				jQuery('.slots_wrapper').attr('data-duotone', 'rs-duotone-' + filter);
				
			},
			
			duotoneItmHover: function(e, filter) {
				
				if(filter !== 'none') addon.body.addClass('duotone_active');
				else addon.body.removeClass('duotone_active');
					
				// draw filter onto the stage
				jQuery('.slots_wrapper').attr('data-duotone', 'rs-duotone-' + filter);
				
			},
			
			/* 
				for compatibility between reg. filters and duotone filters for hover previews
			*/
			defaultHover: function(e) {
				
				// bounce if addon not active or duotone filter is 'none'
				if(!addon.isActive || RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].filter === 'none') return;
				
				// duotone is active, disable it
				if(e.type === 'mouseenter') {
					
					addon.suppressUpdate = true;
					addon.body.removeClass('duotone_active');
					
				}
				// mouseleave
				else {
					
					// if regular media filter was selected, change duotone to 'none'
					if(RVS.SLIDER[RVS.S.slideId].slide.bg.mediaFilter !== 'none') {
					
						jQuery('.rsaddon-duotone-filter').removeClass('selected');
						jQuery('#rs-duotone-none').addClass('selected');
						
						RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].filter = 'none';
						RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});
						
					}
					// restore duotone
					else {
						addon.body.addClass('duotone_active');
					}
					
					addon.suppressUpdate = false;
					
				}
				
			},
			
			/* 
				for compatibility between reg. filters and duotone filters for hover previews
			*/
			duotoneHover: function(e) {
				
				var defFilter = RVS.SLIDER[RVS.S.slideId].slide.bg.mediaFilter;
				
				// mouseenter: disable default filter when duotone is played with
				if(e.type === 'duotoneEnter') {
					
					addon.body.addClass('duotone_active');
					if(defFilter !== 'none') jQuery('.slots_wrapper').attr('class', 'slots_wrapper');
					
				}
				// mouseleave
				else {
					
					var duotone = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].filter;
					
					// set default filter to none if duotone is selected
					if(duotone !== 'none') {
						
						jQuery('.inst-filter-griditem').not('.rsaddon-duotone-filter').removeClass('selected');
						jQuery('#filter_none').addClass('selected');
						
						RVS.SLIDER[RVS.S.slideId].slide.bg.mediaFilter = 'none';
						RVS.F.updateEasyInputs({container: jQuery('#form_slidebg_filters_int'), path: RVS.S.slideId + '.slide.'});
						
						// draw filter onto the stage
						addon.body.addClass('duotone_active');
						jQuery('.slots_wrapper').attr('data-duotone', 'rs-duotone-' + duotone);
						
					}
					// restore default filter
					else {
						
						addon.body.removeClass('duotone_active');
						if(defFilter !== 'none') jQuery('.slots_wrapper').addClass(defFilter);
						
					}
					
				}
				
			},
			
			newSlideCreated: function(e, id) {

				if(!addon.isActive) return;
				
				// check defaults
				RVS.SLIDER[id].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[id].slide.addOns[slug]);
				
				// update fields
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: id + '.slide.', trigger: 'init'});
				
				// update display
				events.updateDisplay();
				
			},
			
		};
		
		function addEvents() {
			
			jQuery(document).on('duotoneItmHover', events.duotoneItmHover)
							.on('duotoneEnter duotoneLeave', events.duotoneHover)
							.on('newSlideCreated', events.newSlideCreated)
							.on('updateDuotone', events.updateDuotone)
							.on('redrawSlideBGDone', events.drawThumbs)
							.on('SceneUpdatedAfterRestore.duotone updateslidebasic', events.updateDisplay);
							
			
			// listen for when the defaults are played with (default events to listen for in this case are not compatible, as the events available to listen for are triggered by other things as well)
			addon.body.on('mouseenter mouseleave', '#inst-filter-grid', events.defaultHover);
			
		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(typeof HelpGuide !== 'undefined' && revslider_duotonefilters_addon.hasOwnProperty('help')) {
			
				var obj = {slug: 'duotonefilters_addon'};
				jQuery.extend(true, obj, revslider_duotonefilters_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}


})();