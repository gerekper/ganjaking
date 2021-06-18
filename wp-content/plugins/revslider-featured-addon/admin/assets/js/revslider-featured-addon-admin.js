/***************************************************
 * REVOLUTION 6.0.0 featured ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
					
		var bricks = revslider_featured_addon.bricks,
			addon = {},
		
		// ADDON CORE	
			slug = "revslider-featured-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_featured_addon.enabled);
		
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
						try{
							setContent(JSON.parse(response.data));							
						} catch(e){}
					else
						setContent();	
					RVS.F.updateSelectsWithSpecialOptions();
					addon.configpanel.find('.tos2').ddTP('change');
					RVS.F.initOnOff();					
				},undefined,undefined,RVS_LANG.loadconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.loadvalues+'"</span>');										
			} else {
				$("#"+param.container).append(addon.configpanel);
				addon.configpanel.find('.tos2').ddTP('change');
			}
			
			//Update Save Config Button
			RVS.F.configPanelSaveButton({show:true, slug:slug});

			updateInputFieldDependencies();
			RVS.F.initOnOff();
		});

		
		function updateInputFieldDependencies() {
			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').ddTP({
				
				placeholder:"Select From List"
			});				
		}

		function setContent(_) {				
			_ = _ === undefined || _['revslider-featured-addon-type']===undefined ? {	'revslider-featured-addon-type' : "auto", 
									'revslider-featured-addon-slider': "", 										
									'revslider-featured-addon-overwrite-featured-image':false,
									'revslider-featured-addon-overwrite-featured-slider':false,
									'revslider-featured-addon-write-when-no-featured-image':false										
								} : _;		
		
			var form = $('#'+slug+'-form');

			form.find('input[name="'+slug+'-type"][value="'+_[slug+'-type']+'"]').attr('checked','checked').trigger("change");
			
			RVS.F.addOrSelectOption({select:$('#featuredselslider'), val:_[slug+'-slider']});
			
			$('#overwritefeaturedimage')[0].checked = _truefalse(_[slug+'-overwrite-featured-image']) ? "checked" : "";
			$('#overwritefeaturedimage').trigger("change");

			$('#overwritefeaturedslider')[0].checked = _truefalse(_[slug+'-overwrite-featured-slider']) ? "checked" : "";
			$('#overwritefeaturedslider').trigger("change");

			$('#writewhennofeaturedimage')[0].checked = _truefalse(_[slug+'-write-when-no-featured-image']) ? "checked" : "";
			$('#writewhennofeaturedimage').trigger("change");

			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_featured_form: $('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		
		}


		function buildConfigPanel() {
			var _h;				
			_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
			_h += '<form id="'+slug+'-form">';
			_h += '	<label_a>'+bricks.featuredcontent+'</label_a>';
			_h += '	<div class="radiooption">';
			_h += '		<div class="featured_single_post"><input data-select=".featured_single_post" data-unselect=".featured_all_posts" data-show="#featured_*val*_sub" data-hide=".featured_all_sub" type="radio" value="single" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.singlepost+'</label_sub></div>';
			_h += '		<div class="featured_all_posts"><input data-select=".featured_all_posts" data-unselect=".featured_single_post" data-show="#featured_*val*_sub" data-hide=".featured_all_sub"type="radio" value="auto" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.allposts+'</label_sub></div>';
			_h += '	</div>';
			_h += '<div class="div15"></div>';
			_h += '<row class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.infoslidertype+'</div></contenthalf></row>';
					
			_h += '	<div class="div20"></div>';
			_h += '	<div id="featured_auto_sub" class="featured_all_sub">';
			_h += '		<label_a>'+bricks.slider+'</label_a><select id="featuredselslider" name="'+slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="all"></select>';				
			_h += '		<longoption><label_a>'+bricks.overwritefeaturedimage+'</label_a><input id="overwritefeaturedimage" type="checkbox" name="'+slug+'-overwrite-featured-image" class="basicinput"></longoption>';
			_h += '		<longoption><label_a>'+bricks.overwritefeaturedslider+'</label_a><input id="overwritefeaturedslider" type="checkbox" name="'+slug+'-overwrite-featured-slider" class="basicinput"></longoption>';
			_h += '		<longoption><label_a>'+bricks.writewhennofeaturedimage+'</label_a><input id="writewhennofeaturedimage" type="checkbox" name="'+slug+'-write-when-no-featured-image" class="basicinput"></longoption>';
			_h += '	</div>';
			_h += '	<div id="featured_single_sub" class="featured_all_sub">';		
			_h += '	</div>';														
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

})( jQuery );