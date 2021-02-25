/***************************************************
 * REVOLUTION 6.0.0 MOUSETRAP ADDON
 * @version: 2.0 (31.08.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';	
	
	// TRANSLATABLE CONTENT
	var bricks = revslider_mousetrap_addon.bricks;
		
	// ADDON CORE
	var addon = {},
		actionsExtended = false,
		slug = "revslider-mousetrap-addon";		
	// INITIALISE THE ADDON
	
	RVS.DOC = RVS.DOC===undefined ? $(document)  : RVS.DOC;

	//Listen to Extension Call
	RVS.DOC.on("extendLayerActionGroups",function() {
		
		if(actionsExtended || RVS.S.ovMode) return;
		
		// Build INPUT FIELDS
		var wrap = document.getElementById("layer_action_extension_wrap"),
		_="";			
		wrap.innerHTML += _;

		$('#layer_action_extension_wrap .input_with_presets').each(function() {				
				RVS.F.prepareOneInputWithPresets(this);
			});	

		//RVS.F.createActionGroup({icon:"my_location", id:"layeraction_group_mousetrap", actions:[
		RVS.F.createActionGroup({icon:"my_location", id:"layeraction_group_layer", actions:[
				{val:"mtrap_follow", alias:bricks.mousetrap_start, inputs:"#la_settings_layertarget", layerTarget:true},
				{val:"mtrap_unfollow", alias:bricks.mousetrap_end, inputs:"#la_settings_layertarget", layerTarget:true}
				]});
		
		//if(_truefalse(revslider_mousetrap_addon.enabled)) $('body').addClass('social-addon-active');
		actionsExtended = true;
		
	});
		
	RVS.DOC.on(slug+'_init',function() {

		addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
		if(addon.isActive) slideDefaults();
	
		// FIRST TIME INITIALISED
		if (!addon.initialised && addon.isActive) {	
		
			// INIT LISTENERS
			initListeners();

			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"my_location", title:bricks.mousetrap, alias:bricks.mousetrap, slider:false, slide:false, layer:true});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug),
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('form_slide_'),
								layericon : $('#gst_layer_'+slug),
								origin : jQuery('<div id="mousetrap_origin_cross"><i class="material-icons">my_location</i></div>')							
						};				
			createSlideSettingsFields();
			createLayerSettingsFields();

			RVS.DOC.on('editorViewModeChange.mousetrap',showHideMouseTrapOrigin);
			RVS.DOC.on('updateMouseTrapOrigin',function() {updateOriginPosition(true);});
			RVS.DOC.on('updateMouseTrapRadius',function() {intelligentInherit(true)});

			addon.forms.origin.draggable({
				start:function(e,ui) {
					addon.forms.oldX = addon.forms.ofx.val();
					addon.forms.oldY = addon.forms.ofy.val();
				},
				drag:function(e,ui) {
					addon.forms.ofx.val(ui.position.left+"px");
					addon.forms.ofy.val(ui.position.top+"px");					
				},
				stop:function(e,ui) {
					RVS.F.openBackupGroup({id:"mouseTrapOrigin",txt:"MouseTrap Position",icon:"my_location"});
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.x[RVS.screen].v = ui.position.left;
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.y[RVS.screen].v = ui.position.top;
					RVS.F.backup({path:RVS.S.slideId+".layers."+RVS.selLayers[0]+".addOns."+slug+".offset.x.#size#.v", 
																lastkey:"v", 
																val:ui.position.left, 
																old:addon.forms.oldX});
					RVS.F.backup({path:RVS.S.slideId+".layers."+RVS.selLayers[0]+".addOns."+slug+".offset.y.#size#.v", 
																lastkey:"v", 
																val:ui.position.top, 
																old:addon.forms.oldY});
					RVS.F.closeBackupGroup({id:"mouseTrapOrigin"});
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.x[RVS.screen].v = ui.position.left;
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.y[RVS.screen].v = ui.position.top;
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.x[RVS.screen].e = true;
					RVS.L[RVS.selLayers[0]].addOns[slug].offset.y[RVS.screen].e = true;
					intelligentInherit(true);
				}
			})	



			initHelp();
			addon.initialised = true;
		}

		// UDPATE FIELDS ID ENABLE
		if (addon.isActive) {
			$('body').addClass('mousetrap-addon-active');
			updateAllCurrentLayer();			
			//Show Hide Areas
			punchgs.TweenLite.set(addon.forms.layericon,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('mousetrap_addon'); 
			
		} else {
			// DISABLE THINGS
			$('body').removeClass('mousetrap-addon-active');			
			punchgs.TweenLite.set(addon.forms.layericon,{display:"none"});			
			$(addon.forms.layericon).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");
			
			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('mousetrap_addon'); 

		}				
	});

	
	function intelligentInherit(nocheck) {
		if (nocheck || checkLayer(RVS.selLayers[0])) {
			var o = {calcShrink: false,key: "x", index: "v", uid: RVS.selLayers[0],iii: true,number: true, shrink: true};			
			RVS.F.iUHelp(jQuery.extend(true,{l: RVS.L[RVS.selLayers[0]].addOns[slug].offset.x, p: RVS.selLayers[0]+".addOns."+slug+".offset.x."},o));
			RVS.F.iUHelp(jQuery.extend(true,{l: RVS.L[RVS.selLayers[0]].addOns[slug].offset.y, p: RVS.selLayers[0]+".addOns."+slug+".offset.y."},o));
			RVS.F.iUHelp(jQuery.extend(true,{l: RVS.L[RVS.selLayers[0]].addOns[slug].follow.radius, p: RVS.selLayers[0]+".addOns."+slug+".follow.radius."},o));
		}
	}

	function checkLayer(a) {
		return RVS.eMode.menu==="#form_layer_revslider-mousetrap-addon" && RVS.eMode.top==="layer" && a!==undefined && RVS.L[a].addOns[slug]!==undefined && RVS.L[a].addOns[slug].follow.mode!=="disabled";
	}

	function showHideMouseTrapOrigin() {
		addon.forms.origin.detach();
		if (checkLayer(RVS.selLayers[0])){	
			RVS.H[RVS.selLayers[0]].w.append(addon.forms.origin);

			updateOriginPosition(true);
		}
	}

	function updateOriginPosition(nocheck,noinheritupdate) {		
		if (nocheck || checkLayer(RVS.selLayers[0])) {		
			addon.forms.origin[0].style.left = parseInt(RVS.L[RVS.selLayers[0]].addOns[slug].offset.x[RVS.screen].v,0)+"px";
			addon.forms.origin[0].style.top = parseInt(RVS.L[RVS.selLayers[0]].addOns[slug].offset.y[RVS.screen].v,0)+"px";
			if (noinheritupdate!==true) intelligentInherit(true);
		}
	}
	

	// INITIALISE MOUSETRAP LISTENERS
	function initListeners() {	
		
		// UPDATE DUE BACKUP/RESTORE
		 RVS.DOC.on('SceneUpdatedAfterRestore.mousetrap',function() { updateOriginPosition();});
		 RVS.DOC.on('sliderSizeChanged',function() {updateOriginPosition(undefined,true);});
		// RVS.DOC.on('slideFocusChanged.mousetrap',function() { });

		// LAYER HAS BEEN SELECTED, CHECK IF MOUSETRAP SETTINGS CAN BE SHOWN
		RVS.DOC.on('selectLayersDone.mousetrap',function() {			
			updateLayerStructure(RVS.selLayers[0]);			
			showHideMouseTrapOrigin();
		});			

		RVS.DOC.on('click','#mousetrap_customs',function() {
			RVS.F.RSDialog.create({modalid:'rbm_mousetrap', bgopacity:0.6});
			RVS.F.updateEasyInputs({container:jQuery('#rbm_mousetrap'),path:RVS.S.slideId+".layers.",trigger:"init", multiselection:true});
		});

		// CLOSE THE MODAL WINDOW
		RVS.DOC.on('click', '#rbm_mousetrap .rbm_close', function() {			
			RVS.F.RSDialog.close();			
		});		
	}


	// UPDATE ALL CURRENT LAYERS STRUCTURE
	function updateAllCurrentLayer() { for (var i in RVS.L) if (RVS.L[i].timeline!==undefined) updateLayerStructure(i);}
	
	function d(_,def) {
		return _===undefined ? def : _;
	}

	function updateLayerStructure(uid) {			
		if (RVS.L[uid]==undefined) return;			
		RVS.L[uid].addOns[slug] = d(RVS.L[uid].addOns[slug],{});
		
		// Follow Mouse
		RVS.L[uid].addOns[slug].follow = d(RVS.L[uid].addOns[slug].follow, { mode:'disabled', olayer:[99999,99999,99999,99999,99999],  delay:0, ease:'none', pointer:true, radius:RVS.F.cToResp({default:0}), blockx:false, blocky:false});
		if (RVS.L[uid].addOns[slug].follow==="disabled") RVS.L[uid].addOns[slug].follow = { mode:'disabled', olayer:[99999,99999,99999,99999,99999],  delay:0, ease:'none', pointer:true, radius:RVS.F.cToResp({default:0}), blockx:false, blocky:false};
		RVS.L[uid].addOns[slug].follow.olayer = d(RVS.L[uid].addOns[slug].follow.olayer,[]);
		if (typeof RVS.L[uid].addOns[slug].follow.olayer!=="object") RVS.L[uid].addOns[slug].follow.olayer = [RVS.L[uid].addOns[slug].follow.olayer,99999,99999,99999,99999];
		
	
		// Revert Layer after Following Mouse
		RVS.L[uid].addOns[slug].revert = d(RVS.L[uid].addOns[slug].revert,{use:false,speed:0, ease:'none'});

		// Pointer Offset
		RVS.L[uid].addOns[slug].offset = d(RVS.L[uid].addOns[slug].offset, { x: RVS.F.cToResp({default:0}),y: RVS.F.cToResp({default:0})});

		// Max Move Radius (4 Level)
		RVS.L[uid].addOns[slug].follow.radius = d(RVS.L[uid].addOns[slug].follow.radius,RVS.F.cToResp({default:0}));

		//Rules
		RVS.L[uid].addOns[slug].rules = d(RVS.L[uid].addOns[slug].rules,
		{ speed:400, ease:'none',
			rx:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"},
			ry:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"},
			rz:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"},
			sx:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"},
			sy:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"},
			op:{min:0,max:0,axis:"none",calc:"distance",offset:"0%"}
		});		

		
		var _h = '<option value="99999">'+bricks.notused+'</option>'
		for (var i in RVS.L) if (!RVS.L.hasOwnProperty(i) || i==="top" || i=="bottom" || i==="middle" || uid==i) continue; else _h += '<option value="'+i+'">'+RVS.L[i].alias+'</option>';		
		for (i=0;i<5;i++) {
			addon.forms.olayerlist[i][0].innerHTML = _h;
			addon.forms.olayerlist[i][0].val = RVS.L[uid].addOns[slug].follow.olayer[i];
			addon.forms.olayerlist[i].select2RS({minimumResultsForSearch:"Infinity", placeholder:"Select From List"});
		}		
	}	
		
	//Migrate Datas
	function slideDefaults() {
		var ids = RVS.SLIDER.slideIDs;
		for(var id in ids) {			
			if(!ids.hasOwnProperty(id)) continue;
			var slideId = ids[id];			
			// skip writing to static slide
			if(slideId.toString().search('static') !== -1) continue;
		}		
	}
	
	function addHelpKeys() {		
		var st = this.getAttribute('data-r');
		if(st) this.setAttribute('data-helpkey', 'mousetrap.' + st);
	
	}
	
	function removeHelpKeys() {	this.removeAttribute('data-helpkey');}

	
	// CREATE THE BASIC INPUT FIELDS FOR THE ADD ON
	function createLayerSettingsFields() {					
		var _h, _m;				
		_h = '<div class="form_inner_header"><i class="material-icons">my_location</i>'+bricks.mousesettings+'</div>';
		_h += '<div class="collapsable" style="display:block !important">'; 			
		_h += '		<label_a>'+bricks.listener+'</label_a><select id="mousetrap_follow" class="layerinput tos2 nosearchbox easyinit"  data-show=".mousetrap_layer_*val*" data-hide=".mousetrap_layer_form" data-r="addOns.'+slug+'.follow.mode">';
		_h +='			<option value="disabled">'+bricks.disabled+'</option>';
		_h +='			<option value="slider">'+bricks.onslider+'</option>';		
		_h +='			<option value="self">'+bricks.self+'</option>';
		_h +='			<option value="olayer">'+bricks.onotherlayer+'</option>';
		_h +='			<option value="events">'+bricks.events+'</option>';		
		_h +='		</select>';	
		_h += '		<div class="mousetrap_layer_form mousetrap_layer_olayer">';
		for (var i=0;i<5;i++) _h += '			<label_a>'+bricks.sensorlayer+'</label_a><select id="mousetrap_olayer_'+i+'" class="layerinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.follow.olayer.'+i+'"></select>';		
		_h += '			<div class="div15"></div>';
		_h += '		</div>';
		_h += '		<div class="mousetrap_layer_form mousetrap_layer_slider mousetrap_layer_ranges mousetrap_layer_events mousetrap_layer_olayer mousetrap_layer_self">';		
		_h +='			<longoption><label_icon class="ui_x"></label_icon><label_a>'+bricks.origins+'</label_a><input class="layerinput valueduekeyboard  easyinit callEvent" data-evt="updateMouseTrapOrigin" data-allowed="px" data-responsive="true" data-numeric="true" data-r="addOns.'+slug+'.offset.x.#size#.v" data-min="-3000" data-max="3000" type="text" id="mousetrack_offset_x"></longoption>';
		_h +='			<longoption><label_icon class="ui_y"></label_icon><label_a>'+bricks.origins+'</label_a><input class="layerinput valueduekeyboard  easyinit callEvent" data-evt="updateMouseTrapOrigin" data-allowed="px" data-responsive="true" data-numeric="true" data-r="addOns.'+slug+'.offset.y.#size#.v" data-min="-3000" data-max="3000" type="text" id="mousetrack_offset_y"></longoption>';		
		_h += '		</div>';
		

		_h += '</div>';
		_h += '<div class="mousetrap_layer_form mousetrap_layer_slider mousetrap_layer_ranges mousetrap_layer_events mousetrap_layer_olayer">';
		_h += '	<div class="form_inner_header"><i class="material-icons">settings_ethernet</i>'+bricks.limitations+'</div>';
		_h += '	<div class="collapsable" style="display:block !important">'; 
		_h +='		<longoption><label_icon class="ui_selectable"></label_icon><label_a>'+bricks.hidemouse+'</label_a><input type="checkbox" class="layerinput easyinit" data-r="addOns.'+slug+'.follow.pointer"></longoption>';		
		_h += '		<longoption><label_icon class="ui_x"></label_icon><label_a>'+bricks.blockx+'</label_a><input type="checkbox" class="layerinput easyinit" data-r="addOns.'+slug+'.follow.blockx"></longoption>';
		_h +='		<longoption><label_icon class="ui_y"></label_icon><label_a>'+bricks.blocky+'</label_a><input type="checkbox" class="layerinput easyinit" data-r="addOns.'+slug+'.follow.blocky"></longoption>';										
		_h +='		<longoption><label_icon class="ui_fit"></label_icon><label_a>'+bricks.moveradius+'</label_a><input type="text" class="layerinput easyinit callEvent" id="mousetrap_follow_radius" data-evt="updateMouseTrapRadius" data-numeric="true" data-allowed="px" data-min="0" data-max="10000" data-r="addOns.'+slug+'.follow.radius.#size#.v"></longoption>';		
		_h += '	</div>';
		_h += '</div>';
		_h += '<div class="mousetrap_layer_form mousetrap_layer_slider mousetrap_layer_ranges mousetrap_layer_events mousetrap_layer_olayer mousetrap_layer_self">';
		_h += '	<div class="form_inner_header"><i class="material-icons">all_out</i>'+bricks.animation+'</div>';
		_h += '	<div class="collapsable" style="display:block !important">'; 
		_h +='		<label_a>'+bricks.delay+'</label_a><input type="text" class="layerinput easyinit" data-numeric="true" data-allowed="ms" data-min="0" data-max="10000" data-r="addOns.'+slug+'.follow.delay">';			
		_h +='		<label_a>'+bricks.easing+'</label_a><select id="mousetrap_layer_delay_ease" class="layerinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.follow.ease"></select>';		
		_h +='		<div class="div15"></div>';
		_h +='		<div class="basic_action_button fullbutton" id="mousetrap_customs"><i class="material-icons">settings</i>'+bricks.customsettings+'</div>';
		_h += '	</div>';
		_h += '	<div class="form_inner_header"><i class="material-icons">settings_backup_restore</i>'+bricks.revertit+'</div>';
		_h += '	<div class="collapsable" style="display:block !important">'; 		
		_h +='			<label_a>'+bricks.revert+'</label_a><input type="checkbox" class="layerinput easyinit" data-r="addOns.'+slug+'.revert.use" data-showhide="#mousetrap_revert_details" data-showhidedep="true"><span class="linebreak"></span>';	
		_h +='			<div id="mousetrap_revert_details">';		
		_h +='				<label_a>'+bricks.speed+'</label_a><input type="text" class="layerinput easyinit" data-numeric="true" data-allowed="ms" data-min="0" data-max="10000" data-r="addOns.'+slug+'.revert.speed">';
		_h +='				<label_a>'+bricks.easing+'</label_a><select id="mousetrap_layer_revert_ease" class="layerinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.revert.ease"></select>';
		_h +='			</div>';
		_h += '	</div>';
		_h += '</div>';
		
		addon.forms.layergeneral.append($(_h));		

		_m = '<div class="_TPRB_ rb-modal-wrapper" data-modal="rbm_mousetrap">';
		_m += '	<div class="rb-modal-inner">';
		_m += '		<div class="rb-modal-content">';
		_m += '			<div id="rbm_mousetrap" class="rb_modal form_inner">';
		_m += '				<div class="rbm_header"><i class="rbm_symbol material-icons">settings</i><span class="rbm_title">'+bricks.customsettings+'</span><i class="rbm_close material-icons">close</i></div>';
		_m += '				<div class="rbm_content">';
		_m += '					<div id="mousetrap_rules">';
		_m +='<label_a>'+bricks.speed+'</label_a><input type="text" style="width:145px" class="layerinput easyinit" data-numeric="true" data-allowed="ms" data-min="0" data-max="10000" data-r="addOns.'+slug+'.rules.speed">';
		_m +='<label_a>'+bricks.easing+'</label_a><select id="mousetrap_layer_rotate_ease" class="layerinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.rules.ease"></select><div class="div25"></div>';
		_m += '					<div style="width:142px;margin-right:10px;margin-left:100px" class="modal_fields_title">'+bricks.dependonaxis+'</div><div style="width:100px;margin-right:15px;" class="modal_fields_title">'+bricks.min+'</div><div style="width:100px;margin-right:15px;" class="modal_fields_title">'+bricks.max+'</div><div style="width:145px;margin-right:10px;" class="modal_fields_title">'+bricks.calcvaule+'</div><div style="width:100px;margin-right:15px;" class="modal_fields_title">'+bricks.offset+'</div>';
		
		
		var rules = {rx:"Rotation X", ry:"Rotation Y", rz:"Rotation Z", sx:"Scale X", sy:"Scale Y", op:"Opacity"};
		for (var i in rules) {
			_m += '<div id="mousetrap_rule_'+i+'">';
			
			_m +='<label_a>'+rules[i]+'</label_a>';			
			_m +='<select class="layerinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.rules.'+i+'.axis" data-show=".mousetrap_rules_'+i+'_*val*" data-hide=".mousetrap_rules_all_'+i+'"><option value="none">'+bricks.none+'</option>'+ (i==="rz" ? '<option value="both">'+bricks.both+'</option><option value="center">'+bricks.center+'</option>' : '')+'<option value="horizontal">'+bricks.horizontal+'</option><option value="vertical">'+bricks.vertical+'</option></select>'
			_m +='<div style="display:inline-block" class="mousetrap_rules_all_'+i+' mousetrap_rules_'+i+'_horizontal mousetrap_rules_'+i+'_vertical">';
			_m +='<input type="text" style="width:100px; margin-left:15px;margin-right:15px" class="layerinput easyinit" data-numeric="true" data-allowed="" data-min="-10000" data-max="10000" data-r="addOns.'+slug+'.rules.'+i+'.min">';
			_m +='<input type="text" style="width:100px; margin-right:15px" class="layerinput easyinit" data-numeric="true" data-allowed="" data-min="-10000" data-max="10000" data-r="addOns.'+slug+'.rules.'+i+'.max">';			
			_m +='<select class="layerinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.rules.'+i+'.calc"><option value="distance">'+bricks.distance+'</option><option value="direction">'+bricks.direction+'</option></select>';
			_m +='<input type="text" style="width:100px; margin-left:15px;margin-right:15px" class="layerinput easyinit" data-numeric="true" data-allowed="%" data-min="0" data-max="100" data-r="addOns.'+slug+'.rules.'+i+'.offset">';
			_m +='</div>'
			_m +='</div>';	
		}
		_m += '					</div>';
		_m += '				</div>'
		_m += '			</div>'
		_m += '		</div>';
		_m += '	</div>';
		_m += '</div>';

		
		jQuery('#rb_tlw').append($(_m));
					
		
		addon.forms.mousetrap_rules = jQuery('#mousetrap_rules');	
		
		addon.forms.olayerlist = [5];
		for (i=0;i<5;i++) addon.forms.olayerlist[i] = jQuery('#mousetrap_olayer_'+i)
			


		addon.forms.radius = jQuery('#mousetrap_follow_radius');		
		addon.forms.ofx = jQuery('#mousetrack_offset_x');		
		addon.forms.ofy = jQuery('#mousetrack_offset_y');		
		addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:"Select From List"
		});

		addon.forms.mousetrap_rules.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:"Select From List"
		});
		// BUILD EASING LISTS
		addon.forms.mousetrap_rules.find('.tos2.easingSelect').each(function() {RVS.F.createEaseOptions(this);});	
		addon.forms.layergeneral.find('.tos2.easingSelect').each(function() {RVS.F.createEaseOptions(this);});	
		RVS.F.initOnOff();		
	}
						
	// CREATE INPUT FIELDS
	function createSlideSettingsFields() {		
	}
	
	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(typeof HelpGuide !== 'undefined' && revslider_mousetrap_addon.hasOwnProperty('help')) {		
			var obj = {slug: 'mousetrap_addon'};
			$.extend(true, obj, revslider_mousetrap_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}

	function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1 || v==="0")
				v=false;
			else
			if (v==="true" || v===true || v==="on" || v===1 || v==="1")
				v=true;
			return v;
		}

})( jQuery );