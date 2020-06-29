/***************************************************
 * REVOLUTION 6.0.0 DOMAIN SWITCH ADDON
 * @version: 1.0 (24.07.2019)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';

	// TRANSLATABLE CONTENT
	var bricks = {
		domainswitch:"Domain Switch",
		savevalues:"Save Values",
		saveconfig:"Save Config",
		replace_url:"Switch Domain URLs",
		olddomain:"Old Domain",
		newdomain:"New Domain",
		updateurls:"Update"
	};

	// ADDON CORE
	var addon = {};

	// Defaults
	var slug = "revslider-domain-switch-addon";
	
	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_config',function(e,param) {
		// FIRST TIME INITIALISED
		if (!addon.configinit) {
			
			RVS.DOC.on('save_'+slug,function() {
				RVS.F.ajaxRequest("wp_ajax_save_values_"+slug, {revslider_domain_switch_form: jQuery('#'+slug+'-form').serialize()}, function(response){

				},undefined,undefined,RVS_LANG.saveconfig+'<br><span style="font-size:17px; line-height:25px;">"'+bricks.savevalues+'"</span>');										
			}); //
			
			addon.configpanel = jQuery(buildConfigPanel());
			addon.configinit = true;
			jQuery("#"+param.container).append(addon.configpanel);
		} else {
			jQuery("#"+param.container).append(addon.configpanel);
		}
		
		//Update Save Config Button
		RVS.F.configPanelSaveButton({show:true, slug:slug});
	});

	
	function buildConfigPanel() {
		var _h;				
		_h =  '<div class="ale_i_title">'+bricks.replace_url+'</div>';
		_h += '<form id="'+slug+'-form">';				
		_h += '	<label_a>'+bricks.olddomain+'</label_a><input id="' + slug + '-old" class="basicinput" type="text" name="' + slug + '-old">';				
		_h += '	<label_a>'+bricks.newdomain+'</label_a><input id="' + slug + '-new" class="basicinput" type="text" name="' + slug + '-new">';				
		_h += '</form>';
		_h += '	<div class="div75"></div>';
		return _h;
	}
	
})( jQuery );