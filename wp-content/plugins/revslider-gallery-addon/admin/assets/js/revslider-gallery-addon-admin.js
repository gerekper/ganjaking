/***************************************************
 * REVOLUTION 6.0.0 gallery ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';		
		var bricks = revslider_gallery_addon.bricks,
			addon = {},
		
		// ADDON CORE
			slug = "revslider-gallery-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_gallery_addon.enabled);

		// INITIALISE THE ADDON	
		RVS.DOC.on('extendmetas.gallery',function() {									
			// FIRST TIME INITIALISED
			if (!addon.meta_extended) {
				updateMetas();
				addon.meta_extended = true;
			}							
		});

		// INITIALISE THE ADDON	CONFIG PANEL (init_%SLUG%_ConfigPanel)
		RVS.DOC.on(slug+'_config',function(e,param) {		
			// FIRST TIME INITIALISED
			if (!addon.initialised) {
				initListeners();		
				addon.configpanel = $(buildConfigPanel());
				addon.initialised = true;				
				$("#"+param.container).append(addon.configpanel);			
				//AJAX TO LOAD CONTENT
				RVS.F.ajaxRequest("wp_ajax_get_values_"+slug, {}, function(response){						
					if (response.data) 
						setContent($.parseJSON(response.data));							
					else
						setContent();	
					RVS.F.updateSelectsWithSpecialOptions();					
				},undefined,undefined,RVS_LANG.loadconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.loadvalues+'"</span>');					
			} else {
				$("#"+param.container).append(addon.configpanel);
			}
			
			//Update Save Config Button
			RVS.F.configPanelSaveButton({show:true, slug:slug});

			updateInputFieldDependencies();
		});

		
		function updateInputFieldDependencies() {
			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});				
		}

		function setContent(_) {

			_ = _ === undefined ? {} : _;

			_[slug+'-slider'] = _[slug+'-slider']===undefined ? _['rs-addon-gal-slider']===undefined ? "" : _['rs-addon-gal-slider'] : _[slug+'-slider'];				
			//var form = $('#'+slug+'-form');				
			RVS.F.addOrSelectOption({select:$('#rsaddonGallerySlider'), val:_[slug+'-slider']});				
			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_gallery_form: $('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		
		}


		function buildConfigPanel() {
			var _h;				
			_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
			_h += '<form id="'+slug+'-form">';				
			_h += '	<label_a>'+bricks.slider+'</label_a><select id="rsaddonGallerySlider" name="'+slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="posts" data-subfilter="specific_post"></select>';				
			//_h += ' <div class="ale_i_content">'+bricks.info+'</div>';
			_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.info+'</div></contenthalf></row>';
			_h += '</form>';
			_h += '	<div class="div75"></div>';
			return _h;
		}

		function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1 || v==="0")
				v=false;
			else
			if (v==="true" || v===true || v==="on")
				v=true;
			return v;
		}

		//UPDATE META DATAS
		function updateMetas() {
			var _h,
				defs = [["{{title}}", "title", "Title"],
						["{{caption}}", "caption", "Caption"],
						["{{description}}", "description", "Description"],
						["{{link}}", "link", "http://yourlinktoimage.img"],
						["{{uploaded}}", "uploaded", "05.11.2018"]
						];
							 
			_h = '<div id="mdl_group_gallery" class="mdl_group_wrap">';
			
			_h += '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">monochrome_photos</i>'+bricks.biggallery+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
			for (var i in defs) {
				if(!defs.hasOwnProperty(i)) continue;
				_h += '<div data-val="'+defs[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">monochrome_photos</i>'+bricks[defs[i][1]]+'</div><div class="mdl_right_content">'+defs[i][0]+'</div><div class="mdl_placeholder_content">'+defs[i][2]+'</div></div>';
			}
			_h += '</div>';
			
			_h += '</div>';
			$('#meta_datas_list').append($(_h));
			$('#mdl_group_wrap_menu').append('<div data-show="mdl_group_gallery" class="mdl_group_wrap_menuitem">'+bricks.biggallery+'</div>');

			//Extend Image URLS		
			_h = '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">photo</i>'+bricks.galleryimages+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';					
			for (i in RVS.ENV.img_sizes) {
				if(!RVS.ENV.img_sizes.hasOwnProperty(i)) continue;
				var v = RVS.ENV.img_sizes[i].replace(" ","_").toLowerCase();
				_h += '<div data-val="{{image_'+v+'_url}}" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">photo</i>{{prev_image_'+v+'_url}}</div><div class="mdl_right_content">'+v+'</div><div class="mdl_placeholder_content">http://imagesource.img</div></div>';
			}				
			_h += '</div>';								
			$('#mdl_group_images').append($(_h));	

			RVS.F.updateMetaTranslate();
		}

})( jQuery );
