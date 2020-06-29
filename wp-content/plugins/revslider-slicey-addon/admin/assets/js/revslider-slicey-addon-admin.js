/***************************************************
 * REVOLUTION 6.0.0 SLICEY ADDON
 * @version: 2.0 (31.08.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';	
	
	// TRANSLATABLE CONTENT
	var bricks = revslider_slicey_addon.bricks;
		
	// ADDON CORE
	var addon = {};
	var slug = "revslider-slicey-addon";		
	// INITIALISE THE ADDON
	
	RVS.DOC = RVS.DOC===undefined ? $(document)  : RVS.DOC;
	
	RVS.DOC.on(slug+'_init',function() {

		addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
		if(addon.isActive) slideDefaults();
	
		// FIRST TIME INITIALISED
		if (!addon.initialised && addon.isActive) {	

			//EXTEND THE ADD LAYER TYPES				
			extendLayerTypes();
			
			// INIT LISTENERS
			initListeners();

			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"picture_in_picture_alt", title:bricks.slicey, alias:bricks.slicey, slider:false, slide:false, layer:true});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug),
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('form_slide_'),
								layericon : $('#gst_layer_'+slug),
								kbgeneral : $('#form_slidebg_kenburn'),
								kenburnicon : document.getElementById('gst_kenburns_title_icon'),
								kenburnheader : document.getElementById('sl_pz_innerheader'),
								kenburntitle : document.getElementById('gst_kenburns_title'),
								kenburnhide : [ document.getElementById('slide_bg_settings_wrapper'),  document.getElementById('sl_pz_onoff'),  document.getElementById('sl_pz_xs_xe'),  document.getElementById('sl_pz_ys_ye'), document.getElementById('sl_pzRS_RSre')],
								kenburnunneeded  : [document.getElementById('sl_pz_fs_wrap')]
						};				

			bricks.kenburnicon = addon.forms.kenburnicon.innerHTML;
			bricks.kenburntitle = addon.forms.kenburntitle.innerHTML;
			bricks.kenburnheader = addon.forms.kenburnheader.innerHTML;

			createSlideSettingsFields();
			createLayerSettingsFields();
			updateKenBurnSettings();
			
			initHelp();
			addon.initialised = true;
		}

		// UDPATE FIELDS ID ENABLE
		if (addon.isActive) {
			$('body').addClass('slicey-addon-active');
			
			//Update Ken Burn Settings				
			updateKenBurnSettings();
			//Show Hide Areas
			punchgs.TweenLite.set(addon.forms.layericon,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('slicey_addon'); 
			
		} else {
			// DISABLE THINGS
			$('body').removeClass('slicey-addon-active');
			updateKenBurnSettings();
			punchgs.TweenLite.set(addon.forms.layericon,{display:"none"});			
			$(addon.forms.layericon).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");
			
			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('slicey_addon'); 

		}				
	});

			
	// INITIALISE SLICEY LISTENERS
	function initListeners() {	
		
		// UPDATE DUE BACKUP/RESTORE
		RVS.DOC.on('SceneUpdatedAfterRestore.slicey',function() { 				
			updateKenBurnSettings();
		});
		// $(document).on('slideFocusChanged.slicey',function() { });

		// LAYER HAS BEEN SELECTED, CHECK IF SLICEY SETTINGS CAN BE SHOWN
		RVS.DOC.on('selectLayersDone.slicey',function() {
			var allSlicey = true;
			for (var i in RVS.selLayers) allSlicey = !allSlicey || RVS.L[RVS.selLayers[i]].subtype!=="slicey" ? false : true;					
			
			if (allSlicey) {
				addon.forms.layericon[0].style.display = "inline-block";
				addon.forms.layer[0].style.visibility = "visible";
			} else {
				addon.forms.layericon[0].style.display = "none";
				addon.forms.layer[0].style.visibility = "hidden";
			}
		});

		// EDITOR VIEW CHANGED, WE NEED TO MAYBE DRAW HAND AND STAFF
		RVS.DOC.on('editorViewModeChange.slicey',function() {				
			updateKenBurnSettings();
		});
		
		RVS.DOC.on('newSlideCreated', function newSlideCreated(e, id) {
			
			if(addon.isActive) {
				RVS.SLIDER[id].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[id].slide.addOns[slug]);
			}
			
		});
		
		$('#gst_slide_3').on('click', updateKenBurnSettings);
		
	}

	//EXTEND LAYER TYPES
	function extendLayerTypes() {
		RVS.F.extendLayerTypes({ 
				icon:"picture_in_picture_alt", 
				type:"shape", 
				subtype:"slicey",
				alias:bricks.sliceylayer,
				extension: { 
						addOns : { "revslider-slicey-addon" : { scaleOffset : 20, blurStart : "inherit", blurEnd : "inherit" }},
						idle : { backgroundColor:"rgba(0,0,0,0.5)"},
						runtime : { internalClass:"tp-shape tp-shapewrapper tp-slicey"}							
				}					
		})
	}
	
	// write default data
	function checkSlideDefaults(_) {
		
		return _===undefined || _.shadow===undefined ? {shadow: { blur: 0, color: 'rgba(0, 0, 0, 0.35)', strength: 0}} : _;
		
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
	
	function addHelpKeys() {
		
		var st = this.getAttribute('data-r');
		if(st) this.setAttribute('data-helpkey', 'slicey.' + st);
	
	}
	
	function removeHelpKeys() {
		
		this.removeAttribute('data-helpkey');
		
	}

	//UPDATE KEN BURN SETTINGS
	function updateKenBurnSettings() {
		
		if (RVS.L===undefined) return;
		var anySlicey = false;			
		for (var i in RVS.L) anySlicey = anySlicey || RVS.L[i].subtype==="slicey" ? true : false;
		
		
		//MODIFICATE KEN BURNS OVERVIEW kenburnhide
		if (anySlicey && RVS.SLIDER.settings.addOns[slug].enable) {
			if (addon.slideInSliceymode !== true) {	
				var preset = {
					set : true,
					fitStart : 100,
					fitEnd : RVS.SLIDER[RVS.S.slideId].slide.panzoom.fitEnd,
					rotateStart : 0,
					rotateEnd : 0,
					xStart : 0,
					xEnd : 0,
					yStart : 0,
					yEnd : 0,
					blurStart : RVS.SLIDER[RVS.S.slideId].slide.panzoom.blurStart,
					blurEnd : RVS.SLIDER[RVS.S.slideId].slide.panzoom.blurEnd,
					duration : RVS.SLIDER[RVS.S.slideId].slide.panzoom.duration,
					ease : RVS.SLIDER[RVS.S.slideId].slide.panzoom.ease
				};

				RVS.F.updateSliderObj({path:RVS.S.slideId+".slide.panzoom",val:preset});
				RVS.F.updateEasyInputs({container:addon.forms.kbgeneral, path:RVS.S.slideId+".slide.", trigger:"init"});
				
				// for (var i in addon.forms.kenburnhide) addon.forms.kenburnhide[i].style.display = "none";
				$('#slide_bg_settings_wrapper, #sl_pz_onoff, #sl_pz_xs_xe, #sl_pz_ys_ye, #sl_pzRS_RSre').hide();
				//for (var i in addon.forms.kenburnunneeded) addon.forms.kenburnunneeded[i].style.pointerEvents = "none";	
				var fr = $('#sl_pz_fs_wrap input');
				fr[0].dataset.min = 100;
				fr.data('min',100);
				addon.forms.kenburntitle.innerHTML = bricks.slicey;
				addon.forms.kenburnicon.innerHTML = "picture_in_picture_alt";
				addon.forms.kenburnheader.innerHTML = '<i class="material-icons">picture_in_picture_alt</i>'+bricks.sliceyupdatepz;
				addon.forms.slidegeneral[0].style.display="inline-block";
				
				$('#form_slidebg_kenburn').find('*[data-r]').each(addHelpKeys);
				
			}
			
			var bg = RVS.SLIDER[RVS.S.slideId].slide.bg,
				error = true;
				
			if(bg.type == 'image' || bg.type === 'external') {
				if(bg.lastLoadedImage === undefined) RVS.F.buildKenBurn();
				var src = bg.type === 'image' ? bg.image : bg.externalSrc;
				if(src) {
					error = false;
					$('#kenburnissue').hide();
					$('#internal_kenburn_settings').show();
				}
			}
			
			if(error) {
				$('#internal_kenburn_settings').hide();
				$('#kenburnissue').show();
			}
			
			addon.slideInSliceymode = true;
		} else {
			if (addon.slideInSliceymode != false) {	
				$('#slide_bg_settings_wrapper, #sl_pz_onoff, #sl_pz_xs_xe, #sl_pz_ys_ye, #sl_pzRS_RSre').css('display', 'inline-block');									
				for (var i in addon.forms.kenburnunneeded) {
					if(addon.forms.kenburnunneeded[i] && addon.forms.kenburnunneeded[i].style) {
						addon.forms.kenburnunneeded[i].style.pointerEvents = "";
					}
				}
				addon.forms.kenburntitle.innerHTML = bricks.kenburntitle;
				addon.forms.kenburnicon.innerHTML = bricks.kenburnicon;
				addon.forms.kenburnheader.innerHTML = bricks.kenburnheader;
				addon.forms.slidegeneral[0].style.display="none";
				
				$('#form_slidebg_kenburn').find('*[data-r]').each(removeHelpKeys);
				
			}
			addon.slideInSliceymode = false;		
		}

	}


	// CREATE THE BASIC INPUT FIELDS FOR THE ADD ON
	function createLayerSettingsFields() {					
		var _h;						
		_h = '<div class="form_inner_header"><i class="material-icons">picture_in_picture_alt</i>'+bricks.sliceylayersettings+'</div>';
		_h += '<div  class="collapsable" style="display:block !important">'; 			
		_h += '		<div class="slicey_layer_form">';
		_h += '			<label_a>'+bricks.scaleoffset+'</label_a><input data-allowed="%" data-numeric="true" class="layerinput easyinit verysmallinput valueduekeyboard" data-min="-500" data-max="500" type="text" data-r="addOns.'+slug+'.scaleOffset">';
		_h += '			<row class="direktrow">';
		_h += '				<onelong><label_icon class="ui_blur_start"></label_icon><input id="slicey_layer_blur_start" data-allowed="px,inherit" data-numeric="true" class="layerinput easyinit verysmallinput valueduekeyboard input_with_presets" data-min="0" data-max="100" type="text" data-r="addOns.'+slug+'.blurStart" data-presets_text="$C$px!$I$Inherit" data-presets_val="3px!inherit"></onelong>';
		_h += '				<oneshort><label_icon class="ui_blur_end"></label_icon><input id="slicey_layer_blur_end" data-allowed="px,inherit" data-numeric="true" class="layerinput easyinit verysmallinput valueduekeyboard input_with_presets" data-min="0" data-max="100" type="text" data-r="addOns.'+slug+'.blurEnd" data-presets_text="$C$px!$I$Inherit" data-presets_val="1px!inherit"></oneshort>';
		_h += '			</row>'
		_h += '		</div>';
		_h += '</div>';


		addon.forms.layergeneral.append($(_h));
		addon.forms.patternContainer = addon.forms.layergeneral.find('#slicey_word_pattern_wrap');
		addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:"Select From List"
		});
		RVS.F.initOnOff();
		addon.forms.layergeneral.find('.input_with_presets').each(function() {RVS.F.prepareOneInputWithPresets(this);});
	}
						
	// CREATE INPUT FIELDS
	function createSlideSettingsFields() {			
		var _h = "";
		
		_h = '<div id="slicey_panzoom_extension">';
		_h += '		<div class="form_inner_header"><i class="material-icons">picture_in_picture_alt</i>'+bricks.shadowsettings+'</div>';
		_h += '		<div  class="collapsable" style="display:block !important">'; 						
		_h += '			<label_a>'+bricks.shadowcolor+'</label_a><input type="text" data-editing="'+bricks.sliceyshadow+'" data-mode="single" name="sliceycolor" id="sliceycolor" class="my-color-field slideinput easyinit" data-visible="true" data-r="addOns.'+slug+'.shadow.color" value="transparent"><span class="linebreak"></span>';
		_h += '			<row class="direktrow">';
		_h += '				<onelong><label_icon class="ui_blur"></label_icon><input class="slideinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="px" data-min="0" data-max="500" data-r="addOns.'+slug+'.shadow.blur" type="text"></onelong>';
		_h += '				<oneshort><label_icon class="ui_gap"></label_icon><input class="slideinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="px" data-min="0" data-max="500" data-r="addOns.'+slug+'.shadow.strength" type="text"></oneshort>';
		_h += '			</row>';
		_h += '		</div>';
		_h += '</div>';
		
		
		
		addon.forms.kbgeneral.append(_h);
		addon.forms.slidegeneral = $('#slicey_panzoom_extension');
		addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});
		RVS.F.initOnOff();
		RVS.F.initTpColorBoxes('#form_slidebg_kenburn .my-color-field');											
	}
	
	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(typeof HelpGuide !== 'undefined' && revslider_slicey_addon.hasOwnProperty('help')) {
		
			var obj = {slug: 'slicey_addon'};
			$.extend(true, obj, revslider_slicey_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}

})( jQuery );