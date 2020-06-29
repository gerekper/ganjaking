/***************************************************
 * REVOLUTION 6.0.0 login ADDON
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
		
		
		
		var bricks = revslider_login_addon.bricks;
		var addon = {};
		
		// ADDON CORE	
		var slug = "revslider-login-addon";

		//CHECK GLOBAL ADDONS VARIABLE		
		RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
		RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
		RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_login_addon.enabled);
		
		// INITIALISE THE ADDON	
		RVS.DOC.on('extendmetas.maintenance',function() {									
			// FIRST TIME INITIALISED
			if (!addon.meta_extended) {
				updateMetas();
				addon.meta_extended = true;
			}							
		});
		
		//UPDATE META DATAS
		function updateMetas() {
			
			var _h,
				defs = [["{{revslider_login_form}}", "login_form", "[revslider-login-form]"]];
							 
			_h = '<div id="mdl_group_loginaddon" class="mdl_group_wrap">';
			
			_h += '<div class="mdl_group">';
			_h += '<div class="mdl_group_header"><i class="material-icons">code</i>'+bricks.login+'<i class="material-icons accordiondrop">code</i></div>';
			for (var i in defs) {
				if(!defs.hasOwnProperty(i)) continue;
				_h += '<div data-val="'+defs[i][0]+'" class="mdl_group_member"><div class="mdl_left_content"><i class="material-icons">code</i>'+bricks[defs[i][1]]+'</div><div class="mdl_right_content">'+defs[i][0]+'</div><div class="mdl_placeholder_content">'+defs[i][2]+'</div></div>';
			}
			_h += '</div>';
			
			_h += '</div>';
			$('#meta_datas_list').append($(_h));
			$('#mdl_group_wrap_menu').append('<div data-show="mdl_group_loginaddon" class="mdl_group_wrap_menuitem">'+bricks.login+'</div>');
			RVS.F.updateMetaTranslate();
		}


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
				$('#loginenddate').datepicker({
					prevText:bricks.earlier,
					nextText:bricks.later,
					dateFormat:"yy-mm-dd",
					minDate: new Date()
				})
				
			} else {
				$("#"+param.container).append(addon.configpanel);
			}
			
			//Update Save Config Button
			RVS.F.configPanelSaveButton({show:true, slug:slug});

			updateInputFieldDependencies();
		});

		
		function updateInputFieldDependencies() {
			RVS.F.initOnOff(addon.configpanel);
			addon.configpanel.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});				
		}

		function setContent(_) {				
			_ = _ === undefined || _['revslider-login-addon-type']===undefined ? {	'revslider-login-addon-type' : "slider", 
									'revslider-login-addon-slider': "", 
									'revslider-login-addon-page':"", 
									'revslider-login-addon-page-title': "",
									'revslider-login-addon-remember-me':false,
									'revslider-login-addon-lost-password-link':false,
									'revslider-login-addon-lost-password-overtake':false,
									'revslider-login-addon-redirect-to':""										
								} : _;		
			//Migration Smoothies
			_[slug+'-remember-me'] = _[slug+'-remember-me']===undefined ? _['revslider-login-remember-me'] : _[slug+'-remember-me'];
			_[slug+'-lost-password-link'] = _[slug+'-lost-password-link']===undefined ? _['revslider-login-lost-password-link'] : _[slug+'-lost-password-link'];
			_[slug+'-lost-password-overtake'] = _[slug+'-lost-password-overtake']===undefined ? _['revslider-login-lost-password-overtake'] : _[slug+'-lost-password-overtake'];

			var form = $('#'+slug+'-form');

			form.find('input[name="'+slug+'-type"][value="'+_[slug+'-type']+'"]').attr('checked','checked').trigger("change");				
			form.find('#loginpagetitle').val(_[slug+'-page-title']);							
			RVS.F.addOrSelectOption({select:$('#loginselslider'), val:_[slug+'-slider']});
			RVS.F.addOrSelectOption({select:$('#loginselpage'), val:_[slug+'-page']});

			form.find('#logindefaultredlink').val(_[slug+'-redirect-to']);

			$('#loginremember')[0].checked = _truefalse(_[slug+'-remember-me']) ? "checked" : "";
			$('#loginremember').trigger("change");

			$('#logindisplink')[0].checked = _truefalse(_[slug+'-lost-password-link']) ? "checked" : "";
			$('#logindisplink').trigger("change");

			$('#loginovertakelostpass')[0].checked = _truefalse(_[slug+'-lost-password-overtake']) ? "checked" : "";
			$('#loginovertakelostpass').trigger("change");

			updateInputFieldDependencies();
		}


		// INITIALISE weather LISTENERS
		function initListeners() {		
			RVS.DOC.on('save_'+slug,function() {				
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_login_form: $('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); // End Click		
		}


		function buildConfigPanel() {
			var _h;				
			_h =  '<div class="ale_i_title">'+bricks.configuration+'</div>';
			_h += '<form id="'+slug+'-form">';
			_h += '	<label_a>'+bricks.logincontent+'</label_a>';
			_h += '	<div class="radiooption">';
			_h += '		<div class="login_slider"><input data-select=".login_slider" data-unselect=".login_page" data-show="#login_*val*_sub" data-hide=".login_all_sub" type="radio" value="slider" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.slider+'</label_sub></div>';
			_h += '		<div class="login_page"><input data-select=".login_page" data-unselect=".login_slider" data-show="#login_*val*_sub" data-hide=".login_all_sub"type="radio" value="page" name="'+slug+'-type" class="basicinput"><label_sub>'+bricks.page+'</label_sub></div>';
			_h += '	</div>';
			_h += '	<div class="div20"></div>';
			_h += '	<div id="login_slider_sub" class="login_all_sub">';
			_h += '		<label_a>'+bricks.slider+'</label_a><select id="loginselslider" name="'+slug+'-slider" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="sliders" data-valuetype="slug" data-filter="gallery"></select>';
			_h += '		<label_a>'+bricks.pagetitle+'</label_a><input id="loginpagetitle" placeholder="'+bricks.entersometitle+'" class="basicinput" type="text" name="'+slug+'-page-title"><span class="linebreak"></span>';
			_h += '	</div>';
			_h += '	<div id="login_page_sub" class="login_all_sub">';
			_h += '		<label_a>'+bricks.page+'</label_a><select id="loginselpage" name="'+slug+'-page" data-theme="inmodal" class="basicinput tos2 nosearchbox select_of_customlist" data-ctype="pages" data-valuetype="id"></select>';
			_h += '	</div>';						
			_h += '<label_a>'+bricks.defredlink+'</label_a><input id="logindefaultredlink" class="basicinput" type="text" name="'+slug+'-redirect-to">';
			_h += '<longoption><label_a>'+bricks.displink+'</label_a><input id="logindisplink" type="checkbox" name="'+slug+'-lost-password-link" class="basicinput"></longoption>';
			_h += '<longoption><label_a>'+bricks.ovlopassword+'</label_a><input id="loginovertakelostpass" type="checkbox" name="'+slug+'-lost-password-overtake" class="basicinput"></longoption>';
			_h += '<longoption><label_a>'+bricks.remember+'</label_a><input id="loginremember" type="checkbox" name="'+slug+'-remember-me" class="basicinput"></longoption>';
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