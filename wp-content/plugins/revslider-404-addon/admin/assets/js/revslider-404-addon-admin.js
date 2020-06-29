/***************************************************
 * REVOLUTION 6.0.0 404 ADDON
 * @version: 2.0 (15.07.2019)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
				
		var bricks = revslider_404_addon.bricks;
		var addon = {};
		
		// ADDON CORE	
		var slug = "revslider-404-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_404_addon.enabled);


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
			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').select2({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});
		});

		
		function setContent(_) {				
			_ = _ === undefined || _['revslider-404-addon-type']===undefined ? {	'revslider-404-addon-type' : "slider", 'revslider-404-addon-slider': "", 'revslider-404-addon-page':"", 'revslider-404-addon-page-title': revslider_404_addon.title_placeholder } : _;				
			var form = $('#'+slug+'-form');
			form.find('input[name="'+slug+'-type"][value="'+_[slug+'-type']+'"]').attr('checked','checked').trigger("change");
			form.find('#fofpagetitle').val(_[slug+'-page-title']);

			RVS.F.addOrSelectOption({select:$('#fofselslider'), val:_[slug+'-slider']});
			RVS.F.addOrSelectOption({select:$('#fofselpage'), val:_[slug+'-page']});

			//form.find('#fofselslider').val(_['revslider-404-addon-slider']).trigger('change');
			//form.find('#fofselpage').val(_['revslider-404-addon-page']).trigger('change');				
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_404_form: $('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		
		}


		function buildConfigPanel() {
			var _h;				
			_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
			_h += '<form id="'+slug+'-form">';
			_h += '	<label_a>'+bricks.fofcontent+'</label_a>';
			_h += '	<div class="radiooption">';
			_h += '		<div class="fof_slider"><input data-select=".fof_slider" data-unselect=".fof_page" data-show="#fof_*val*_sub" data-hide=".fof_all_sub" type="radio" value="slider" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.slider+'</label_sub></div>';
			_h += '		<div class="fof_page"><input data-select=".fof_page" data-unselect=".fof_slider" data-show="#fof_*val*_sub" data-hide=".fof_all_sub"type="radio" value="page" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.page+'</label_sub></div>';
			_h += '	</div>';
			_h += '	<div class="div20"></div>';
			_h += '	<div id="fof_slider_sub" class="fof_all_sub">';
			_h += '		<label_a>'+bricks.slider+'</label_a>';
			_h += '		<select id="fofselslider" name="'+slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="all"></select>';					
			_h += '		<label_a>'+bricks.pagetitle+'</label_a>';
			_h += '		<input id="fofpagetitle" placeholder="'+bricks.entersometitle+'" class="basicinput" type="text" name="'+slug+'-page-title"><span class="linebreak"></span>';
			_h += '	</div>';
			_h += '	<div id="fof_page_sub" class="fof_all_sub">';
			_h += '		<label_a>'+bricks.page+'</label_a>';
			_h += '		<select id="fofselpage" name="'+slug+'-page" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="pages" data-valuetype="id"></select>';				
			_h += '	</div>';
			_h += '	<div class="div20"></div>';
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
