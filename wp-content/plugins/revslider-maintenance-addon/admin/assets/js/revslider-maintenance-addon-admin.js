/***************************************************
 * REVOLUTION 6.0.0 maintenance ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
		var bricks = revslider_maintenance_addon.bricks;
		var addon = {};

		// ADDON CORE
		var slug = "revslider-maintenance-addon";

		//CHECK GLOBAL ADDONS VARIABLE
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_maintenance_addon.enabled);

		// INITIALISE THE ADDON
		RVS.DOC.on('extendmetas.maintenance',function() {
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
				$('#maintenanceenddate').datepicker({
					prevText:bricks.earlier,
					nextText:bricks.later,
					dateFormat:"yy-mm-dd",
					minDate: new Date()
				});

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
			_ = _ === undefined || _['revslider-maintenance-addon-type']===undefined ? {	'revslider-maintenance-addon-type' : "slider",
									'revslider-maintenance-addon-slider': "",
									'revslider-maintenance-addon-page':"",
									'revslider-maintenance-addon-page-title': "",
									'revslider-maintenance-addon-countdown-active':false,
									'revslider-maintenance-addon-auto-deactive':false,
									'revslider-maintenance-addon-countdown-day':"",
									'revslider-maintenance-addon-countdown-hour':"",
									'revslider-maintenance-addon-countdown-minute':""
								} : _;

			var form = $('#'+slug+'-form');

			form.find('input[name="'+slug+'-type"][value="'+_[slug+'-type']+'"]').attr('checked','checked').trigger("change");
			form.find('#maintenancepagetitle').val(_[slug+'-page-title']);
			form.find('#maintenanceenddate').val(_[slug+'-countdown-day']);
			form.find('#maintenancehour').val(_[slug+'-countdown-hour']);
			form.find('#maintenancemin').val(_[slug+'-countdown-minute']);

			RVS.F.addOrSelectOption({select:$('#maintenanceselslider'), val:_[slug+'-slider']});
			RVS.F.addOrSelectOption({select:$('#maintenanceselpage'), val:_[slug+'-page']});

			$('#maintenancetimeractive')[0].checked = _truefalse(_[slug+'-countdown-active']) ? "checked" : "";
			$('#maintenancetimeractive').trigger("change");

			$('#maintenanceautodeactivate')[0].checked = _truefalse(_[slug+'-auto-deactive']) ? "checked" : "";
			$('#maintenanceautodeactivate').trigger("change");

			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {
			RVS.DOC.on('save_'+slug,function() {
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_maintenance_form: $('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');
			}); // End Click
			/*
			RVS.DOC.on('click', '#preview-maintanence-page', function(){
				RVS.F.ajaxRequest("wp_ajax_preview_"+slug, {}, function(response){
					
					console.log(response);
				});
			});
			*/
		}


		function buildConfigPanel() {
			var _h;
			_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
			_h += '<form id="'+slug+'-form">';
			_h += '	<label_a>'+bricks.maintenancecontent+'</label_a>';
			_h += '	<div class="radiooption">';
			_h += '		<div class="maintenance_slider"><input data-select=".maintenance_slider" data-unselect=".maintenance_page" data-show="#maintenance_*val*_sub" data-hide=".maintenance_all_sub" type="radio" value="slider" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.slider+'</label_sub></div>';
			_h += '		<div class="maintenance_page"><input data-select=".maintenance_page" data-unselect=".maintenance_slider" data-show="#maintenance_*val*_sub" data-hide=".maintenance_all_sub"type="radio" value="page" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.page+'</label_sub></div>';
			_h += '	</div>';
			_h += '	<div class="div20"></div>';
			_h += '	<div id="maintenance_slider_sub" class="maintenance_all_sub">';
			_h += '		<label_a>'+bricks.slider+'</label_a><select id="maintenanceselslider" name="'+slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="gallery"></select>';
			_h += '		<label_a>'+bricks.pagetitle+'</label_a><input id="maintenancepagetitle" placeholder="'+bricks.entersometitle+'" class="basicinput" type="text" name="'+slug+'-page-title"><span class="linebreak"></span>';
			_h += '	</div>';
			_h += '	<div id="maintenance_page_sub" class="maintenance_all_sub">';
			_h += '		<label_a>'+bricks.page+'</label_a><select id="maintenanceselpage" name="'+slug+'-page" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="pages" data-valuetype="id"></select>';
			_h += '	</div>';
			_h += '<label_a>'+bricks.usetimer+'</label_a><input id="maintenancetimeractive" type="checkbox" name="'+slug+'-countdown-active" class="basicinput" data-showhide=".maintenance_timer_form" data-showhidedep="true">';
			_h += '<div class="maintenance_timer_form">';
			_h += '	<div class="div15"></div>';
			_h += '<div class="ale_i_title">'+bricks.timersettings+'</div>';
			_h += '		<label_a>'+bricks.enddate+'</label_a><input id="maintenanceenddate" placeholder="'+bricks.enddateform+'" class="basicinput" type="text" name="'+slug+'-countdown-day"><span class="linebreak"></span>';
			_h += '		<row class="directrow">';
			_h += '			<onelong><label_a>'+bricks.hour+'</label_a><input id="maintenancehour" data-numeric="true" data-allowed="" data-min="0" data-max="23" class="basicinput valueduekeyboard" type="text" name="'+slug+'-countdown-hour"></onelong>';
			_h += '			<oneshort><label_a>'+bricks.minute+'</label_a><input id="maintenancemin" data-numeric="true" data-allowed="" data-min="0" data-max="59" class="basicinput valueduekeyboard" type="text" name="'+slug+'-countdown-minute"></oneshort>';
			_h += ' 	</row>';
			_h += '<label_a>'+bricks.autodeactivate+'</label_a><input id="maintenanceautodeactivate" type="checkbox" name="'+slug+'-auto-deactive" class="basicinput">';
			_h += '</div>';
			//_h += '<div class="div20"></div>';
			//_h += '<label_a>'+bricks.preview+'</label_a><div class="basic_action_coloredbutton autosize basic_action_button" id="preview-maintanence-page">'+bricks.preview+'</div>';
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
				defs = [["{{t_days}}", "remaining_days", "18"],
						["{{t_hours}}", "remaining_hours", "22"],
						["{{t_minutes}}", "remaining_minutes", "32"],
						["{{t_seconds}}", "remaining_seconds", "14"]];

			_h = '<div id="mdl_group_maintenence" class="mdl_group_wrap">';

			_h += '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">build</i>'+bricks.maintenance+'<i class="material-icons accordiondrop">arrow_drop_down</i></div>';
			for (var i in defs) {
				if(!defs.hasOwnProperty(i)) continue;
				_h += '<div data-val="'+defs[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">build</i>'+bricks[defs[i][1]]+'</div><div class="mdl_right_content">'+defs[i][0]+'</div><div class="mdl_placeholder_content">'+defs[i][2]+'</div></div>';
			}
			_h += '</div>';

			_h += '</div>';
			$('#meta_datas_list').append($(_h));
			$('#mdl_group_wrap_menu').append('<div data-show="mdl_group_maintenence" class="mdl_group_wrap_menuitem">Maintenence</div>');
			RVS.F.updateMetaTranslate();
		}

})( jQuery );