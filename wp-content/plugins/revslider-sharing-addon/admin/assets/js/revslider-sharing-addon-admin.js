/***************************************************
 * REVOLUTION 6.0.0 404 SOCIAL SHARE
 * @version: 2.0 (15.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
	
	

	var bricks = revslider_sharing_addon.bricks;
	var actionsExtended;
	// var addon = {};
	var slug = "revslider-sharing-addon";

	//CHECK GLOBAL ADDONS VARIABLE		
	RVS.LIB.ADDONS = RVS.LIB.ADDONS === undefined ? {} : RVS.LIB.ADDONS;
	RVS.LIB.ADDONS[slug] = RVS.LIB.ADDONS[slug]===undefined ? {} : RVS.LIB.ADDONS[slug];			
	RVS.LIB.ADDONS[slug].enable = _truefalse(revslider_sharing_addon.enabled);

	//Listen to Extension Call
	RVS.DOC.on("extendLayerActionGroups",function() {
		
		if(actionsExtended || RVS.S.ovMode) return;
		
		// Build INPUT FIELDS
		var wrap = document.getElementById("layer_action_extension_wrap"),

			//FACEBOOK FIELDS
			_ = '<div id="la_settings_fbfields" class="la_settings">';
			_ += '<label_a>'+bricks.linkurl+'</label_a><input type="text" class="easyinit actioninput input_with_presets" id="facebook_link" data-presets_text="$CL$Clear!$LI$Parent Site!$LI$Post URL" data-presets_val="!%site_url%!%post_url%" data-r="actions.action.#actionindex#.facebook_link" placeholder="'+bricks.enterlink+'" ><span class="linebreak"></span>';		
			_ += '</div>';
			
			// Google+ is deprecated
			/*
			//GOOGLEPLUS FIELDS
			_ += '<div id="la_settings_gpfields" class="la_settings">';
			_ += '<label_a>'+bricks.linkurl+'</label_a><input type="text" class="easyinit actioninput input_with_presets" id="googleplus_link" data-presets_text="$CL$Clear!$LI$Parent Site!$LI$Post URL" data-presets_val="!%site_url%!%post_url%" data-r="actions.action.#actionindex#.googleplus_link" placeholder="'+bricks.enterlink+'" ><span class="linebreak"></span>';		
			_ += '</div>';
			*/

			//TWITTER FIELDS
			_ += '<div id="la_settings_twfields" class="la_settings">';
			_ += '<label_a>'+bricks.linkurl+'</label_a><input type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$LI$Parent Site!$LI$Post URL" data-presets_val="!%site_url%!%post_url%" id="twitter_link" data-r="actions.action.#actionindex#.twitter_link" placeholder="'+bricks.enterlink+'" ><span class="linebreak"></span>';			
			_ += '<label_a>'+bricks.text+'</label_a><textarea type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$CY$Pick Meta" data-presets_val="!###metapicker###" id="twitter_text" data-r="actions.action.#actionindex#.twitter_text" placeholder="'+bricks.entertext+'" ></textarea><span class="linebreak"></span>';			
			_ += '</div>';
			_ += '</div>';

			//LINKED FIELDS
			_ += '<div id="la_settings_ldfields" class="la_settings">';
			_ += '<label_a>'+bricks.linkurl+'</label_a><input type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$LI$Parent Site!$LI$Post URL" data-presets_val="!%site_url%!%post_url%" id="linkedin_link" data-r="actions.action.#actionindex#.linkedin_link" placeholder="'+bricks.enterlink+'" ><span class="linebreak"></span>';
			_ += '<label_a>'+bricks.title+'</label_a><input type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$CY$Pick Meta" data-presets_val="!###metapicker###" id="linkedin_link_title" data-r="actions.action.#actionindex#.linkedin_link_title" placeholder="'+bricks.entertitle+'" ><span class="linebreak"></span>';		
			_ += '<label_a>'+bricks.summary+'</label_a><textarea type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$CY$Pick Meta" data-presets_val="!###metapicker###" id="linkedin_link_summary" data-r="actions.action.#actionindex#.linkedin_link_summary" placeholder="'+bricks.entersummary+'" ></textarea><span class="linebreak"></span>';		
			_ += '</div>';

			//PINTEREST FIELDS
			_ += '<div id="la_settings_psfields" class="la_settings">';
			_ += '<label_a>'+bricks.linkurl+'</label_a><input type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$LI$Parent Site!$LI$Post URL" data-presets_val="!%site_url%!%post_url%" id="pinterest_link" data-r="actions.action.#actionindex#.pinterest_link" placeholder="'+bricks.enterlink+'" ><span class="linebreak"></span>';
			_ += '<label_a>'+bricks.image+'</label_a><input type="text" class="easyinit actioninput" id="pinterest_image" data-r="actions.action.#actionindex#.pinterest_image" placeholder="'+bricks.enterimage+'" ><span class="linebreak"></span>';		
			_ += '<label_a>'+bricks.description+'</label_a><textarea type="text" class="easyinit actioninput input_with_presets" data-presets_text="$CL$Clear!$CY$Pick Meta" data-presets_val="!###metapicker###" id="pinterest_link_description" data-r="actions.action.#actionindex#.pinterest_link_description" placeholder="'+bricks.enterdescription+'" ></textarea><span class="linebreak"></span>';		
			_ += '</div>';


		wrap.innerHTML += _;

		$('#layer_action_extension_wrap .input_with_presets').each(function() {				
				RVS.F.prepareOneInputWithPresets(this);
			});	

		RVS.F.createActionGroup({icon:"favorite_border", id:"layeraction_group_link", actions:[
				{val:"share_facebook", alias:bricks.share_facebook, inputs:"#la_settings_fbfields"},
				{val:"share_twitter", alias:bricks.share_twitter, inputs:"#la_settings_twfields"},
				{val:"share_linkedin", alias:bricks.share_linkedin, inputs:"#la_settings_ldfields"},
				/*{val:"share_googleplus", alias:bricks.share_googleplus, inputs:"#la_settings_gpfields"},*/
				{val:"share_pinterest", alias:bricks.share_pinterest, inputs:"#la_settings_psfields"}]});
		
		if(_truefalse(revslider_sharing_addon.enabled)) $('body').addClass('social-addon-active');
		actionsExtended = true;
		
	});
	
	// show/hide "interaction" and "delay" options
	RVS.DOC.on('layer_action_selected', function() {
		
		if(RVS.L[RVS.selLayers[0]].actions.action.length) {
			var action = RVS.L[RVS.selLayers[0]].actions.action[RVS.S.actionIdx].action;
			if(action === 'share_facebook' || action === 'share_twitter' || action === 'share_linkedin' || action === 'share_pinterest') {
				$('body').addClass('share-action-selected');
				return;
			}
		}
		$('body').removeClass('share-action-selected');
		
	});
	
	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
	
		revslider_sharing_addon.enabled = RVS.LIB.ADDONS[slug].enable;
		if(!actionsExtended) RVS.DOC.trigger("extendLayerActionGroups");
		if(_truefalse(revslider_sharing_addon.enabled)) $('body').addClass('social-addon-active');
		else $('body').removeClass('social-addon-active');
	
	});

	function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1 || v==="0")
				v=false;
			else
			if (v==="true" || v===true || v==="on" || v===1 || v==="1")
				v=true;
			return v;
		}


})( jQuery );


