/***************************************************
 * REVOLUTION 6.0.0 rel-posts ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
	
		
		
		var bricks = revslider_rel_posts_addon.bricks;
		var addon = {};
		
		// ADDON CORE
		var slug = "revslider-rel-posts-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_rel_posts_addon.enabled);

		// INITIALISE THE ADDON	
		/*
		RVS.DOC.on('extendmetas.rel-posts',function() {									
			// FIRST TIME INITIALISED
			if (!addon.meta_extended) {
				updateMetas();
				addon.meta_extended = true;
			}							
		});
		*/

		// INITIALISE THE ADDON	CONFIG PANEL (init_%SLUG%_ConfigPanel)
		RVS.DOC.on(slug+'_config',function(e,param) {		
			// FIRST TIME INITIALISED
			if (!addon.initialised) {
				initListeners();
				RVS.F.getCustomPostTypes(function() {						
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
						// extendDefaultOptions();					
					},undefined,undefined,RVS_LANG.loadconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.loadvalues+'"</span>');
				});				
				
			} else {
				$("#"+param.container).append(addon.configpanel);
			}
			
			//Update Save Config Button
			RVS.F.configPanelSaveButton({show:true, slug:slug});

			if (addon.initialised) updateInputFieldDependencies();
		});

		//Add "Do not add a slider" option on first Place
		/*
		function extendDefaultOptions() {
			addon.configpanel.find('.relsliderlist').each(function() {
				RVS.F.addOrSelectOption({select:$(this), val:_[slug+'-slider'], selected:false}); // commenting out because "_" was undefined before
			});
		}
		*/

		function updateInputFieldDependencies() {

			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});				
		}

		function setContent(_) {	
			
			_ = _ === undefined ? {} : _;
			// Update Old Values
			for (var i in _) {
				if(!_.hasOwnProperty(i)) continue;
				if (i.indexOf("rs-addon-rel-")>=0) {
					_[i.replace("rs-addon-rel","revslider-rel-posts-addon")] = _[i];
				}
			}
		
			// var form = $('#'+slug+'-form');

			//Check All Available Post Type Settings
			for (i in RVS.LIB.POST_TYPES) {	
				if(!RVS.LIB.POST_TYPES.hasOwnProperty(i)) continue;
				RVS.F.addOrSelectOption({select:$('#rel_slider_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider']===undefined ? "none" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider'])});
				$('#rel_slider_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#rel_startwith_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-start-with']===undefined ? "none" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-start-with'])});
				$('#rel_startwith_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#rel_fillwith_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-fill-with']===undefined ? "none" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-fill-with'])});
				$('#rel_slider_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				RVS.F.addOrSelectOption({select:$('#rel_position_'+RVS.LIB.POST_TYPES[i].slug),val:(_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position']===undefined ? "top" : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position'])});
				$('#rel_position_'+RVS.LIB.POST_TYPES[i].slug).trigger("change");

				$('#rel_number_'+RVS.LIB.POST_TYPES[i].slug).val((_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-number']===undefined ? 5 : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-number'])).trigger("change");
				$('#rel_caching_'+RVS.LIB.POST_TYPES[i].slug).val((_[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-caching']===undefined ? 0 : _[slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-caching'])).trigger("change");

			}

			/*form.find('input[name="'+slug+'-type"][value="'+_[slug+'-type']+'"]').attr('checked','checked').trigger("change");
			form.find('#rel-postspagetitle').val(_[slug+'-page-title']);				
			RVS.F.addOrSelectOption({select:$('#rel-postsselslider'), val:_[slug+'-slider']});				

			$('#rel-poststimeractive')[0].checked = _truefalse(_[slug+'-countdown-active']) ? "checked" : "";
			$('#rel-poststimeractive').trigger("change");*/
			
			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_rel_posts_form: $('#revslider-rel-posts-addon-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		

			RVS.DOC.on('change','.relsliderlist',function() {					
				if (this.value==="none")
					$("#"+this.dataset.showhide).hide();
				else
					$("#"+this.dataset.showhide).show();

			});
		}


		function buildConfigPanel() {				
			var _h;				
			
			_h += '<form id="'+slug+'-form">';
			for (var i in RVS.LIB.POST_TYPES) {
				if(!RVS.LIB.POST_TYPES.hasOwnProperty(i)) continue;
				_h +=  '<div class="ale_i_title">'+RVS.LIB.POST_TYPES[i].title+'</div>';										
				_h += '<label_a style="text-align: left">'+bricks.slider+'</label_a><select id="rel_slider_'+RVS.LIB.POST_TYPES[i].slug+'" data-showhide="rel_posts_addon_setting_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox relsliderlist select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="posts" data-subfilter="specific_post"></select><span class="line-break"></span>';
				_h += '<div id="rel_posts_addon_setting_'+RVS.LIB.POST_TYPES[i].slug+'">';
				_h += '	<label_a>'+bricks.numofposts+'</label_a><input id="rel_number_'+RVS.LIB.POST_TYPES[i].slug+'" data-numeric="true" data-allowed="" data-min="0" data-max="9999" class="basicinput valueduekeyboard" type="text" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-number"><span class="line-break"></span>';
				_h += '	<label_a>'+bricks.searchin+'</label_a><select id="rel_fillwith_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-fill-with" data-theme="inmodal" class="basicinput tos2 nosearchbox">';
				_h += '			<option value="none">'+bricks.nothing+'</option>';
				_h += '			<option value="category" selected="selected">'+bricks.categories+'</option>';
				_h += '			<option value="post_tag">'+bricks.tags+'</option>';
				_h += '			<option value="post_format">'+bricks.format+'</option>';
				_h += '			<option value="random">'+bricks.randomposts+'</option>';
				_h += '			<option value="recent">'+bricks.recentposts+'</option>';
				_h += '			<option value="popular">'+bricks.mostcommentedposts+'</option>';
				_h += '		</select><span class="line-break"></span>';
				_h += '	<label_a>'+bricks.fillwith+'</label_a><select id="rel_startwith_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-start-with" data-theme="inmodal" class="basicinput tos2 nosearchbox">';
				_h += '			<option value="none">'+bricks.nothing+'</option>';
				_h += '			<option value="category" selected="selected">'+bricks.categories+'</option>';
				_h += '			<option value="post_tag">'+bricks.tags+'</option>';
				_h += '			<option value="post_format">'+bricks.format+'</option>';
				_h += '		</select><span class="line-break"></span>';
				_h += '	<label_a>'+bricks.caching+'</label_a><input id="rel_caching_'+RVS.LIB.POST_TYPES[i].slug+'" data-numeric="true" data-allowed="" data-min="0" data-max="9999" class="basicinput valueduekeyboard" type="text" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-caching"><span class="line-break"></span>';
				_h += '	<label_a>'+bricks.position+'</label_a><select id="rel_position_'+RVS.LIB.POST_TYPES[i].slug+'" name="'+slug+'-'+RVS.LIB.POST_TYPES[i].slug+'-position" data-theme="inmodal" class="basicinput tos2 nosearchbox"><option value="top">'+bricks.above+'</option><option value="bottom">'+bricks.below+'</option></select><span class="line-break"></span>';
				_h += '</div>';
				_h += '<div class="div50"></div>';					
			}				
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
		//function updateMetas() {				
		//}

})( jQuery );