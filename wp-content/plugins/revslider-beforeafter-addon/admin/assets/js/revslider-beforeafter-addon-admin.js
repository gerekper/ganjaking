/***************************************************
 * REVOLUTION 6.0.0 beforeafter ADDON
 * @version: 2.0 (31.08.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	
	//'use strict';	
	// TRANSLATABLE CONTENT
	var bricks = revslider_beforeafter_addon.bricks,
		addon = {},
		slug = "revslider-beforeafter-addon";
		
	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {
		
		addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
		
		// FIRST TIME INITIALISED
		if (!addon.initialised && addon.isActive) {			
			// INIT LISTENERS
			initListeners();				
			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"flip", title:bricks.beforeafter, alias:bricks.beforeafter, slider:true, slide:true, layer:true});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug), 
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('#form_slide_'+slug)									
						};
			window.lastBeforeAfterMode = "before";

			updateSliderStructure();	
			updateSlideStructure();				
			createSliderSettingsFields();
			createSlideSettingsFields();
			updateCurrentLayersStructure();
			createLayerSettingsFields();
			createToolbarMenu();
			addHooks();
			initHelp();	
			addon.initialised = true;							
		}

		// UDPATE FIELDS ID ENABLE
		if (addon.isActive) {

			addon.isActive = true;
			
			RVS.F.updateEasyInputs({container:addon.forms.slidergeneral, trigger:"init"});
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"inline-block"});
			punchgs.TweenLite.set('#gst_layer_'+slug,{display:"inline-block"});	
			
			checkSlideEnabled();

			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('beforeafter_addon');
			
		} else {
			
			addon.isActive = false;
			
			// DISABLE THINGS		
			punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});
			punchgs.TweenLite.set('#gst_sl_'+slug,{display:"none"});
			punchgs.TweenLite.set('#gst_layer_'+slug,{display:"none"});
			$('#gst_slide_'+slug).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");	
			
			checkSlideEnabled(false, true);

			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('beforeafter_addon');
			
		}
		
	});
	
	function checkSlideEnabled(e, forceDisable) {
		
		if(!forceDisable && RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].enable) {
			$('#beaf_tb_before').click();
			$('body').removeClass('beforeafter-disabled');
		}
		else {
			$('#beaf_sbgs_before, #beaf_tb_all').click();
			$('body').addClass('beforeafter-disabled');
		}
	
	}

	// UPDATE ALL CURRENT LAYERS STRUCTURE
	function updateCurrentLayersStructure() {			
		for (var i in RVS.L) if (RVS.L[i].addOns) {
			updateLayerStructure(i);
			updateBeforeAfterLayerListIcon(i);
		}
	}

	// UPDATE LAYER SETTINGS
	function updateLayerStructure(uid) {			
		if (RVS.L[uid].addOns[slug]===undefined || RVS.L[uid].addOns[slug].position===undefined) {
			
			var curScreen = jQuery('#beaf_tb_after').hasClass('selected') ? 'after' : 'before';
			RVS.L[uid].addOns[slug] = { position:curScreen};
			jQuery('#beaficon_layerlist').val(curScreen).trigger('change.select2RS');
			updateBeforeAfterLayerListIcon(uid);
			
		}
	}

	// UPDATE SLIDER CONSTRUCT
	function updateSliderStructure() {
		if (RVS.SLIDER.settings.addOns[slug]===undefined || RVS.SLIDER.settings.addOns[slug].icon===undefined) 
		RVS.SLIDER.settings.addOns[slug] = { 
			enable : true,
			icon : {
				color:'#ffffff', size:32, space:5,
				up:'fa-caret-up', down:'fa-caret-down', left:'fa-caret-left', right:'fa-caret-right',
				shadow: { set:false, blur:10, color:'rgba(0, 0, 0, 0.35)'}
			},
			drag : {
				padding:0,
				radius:0,
				bgcolor:'transparent',
				border: { set:false, width:1, color:'#000000' },
				boxshadow: { set:false, blur:10, strength:3, color:'rgba(0, 0, 0, 0.35)'}
			},
			divider : {
				size:1,
				color:'#ffffff',
				shadow: { set:false,blur:10, strength:3, color:'rgba(0, 0, 0, 0.35)'}
			},
			onclick : { set:true, time:500, easing:'Power2.easeOut', cursor:'pointer'}
		};

	}
	// UPDATE CURRENT SLIDE OBJECT STRUCTURE
	function updateSlideStructure() {
		
		if (RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]===undefined) 
			RVS.SLIDER[RVS.S.slideId].slide.addOns[slug] =  {
				enable : false,
				direction : 'horizontal',
				delay : 500,
				time : 750,
				easing : 'Power2.easeInOut',
				animateOut : 'fade',
				moveTo: RVS.F.cToResp({default:50}),
				teaser : { set : 'none', type : 'repel', distance : 5, speed : 1500, easing : 'ease-in-out', delay : 0 },
				shift : { set : false, offset : 10, speed : 300, easing : 'ease', delay : 0},
				bg : {
					type :'trans',
					color:'#e7e7e7',
					externalSrc:'',
					fit:'cover',
					fitX:100,
					fitY:100,
					position:'center center',
					positionX:0,
					positionY:0,
					repeat:'no-repeat',
					image:'',
					imageId:'',
					imageSourceType:'',
					mpeg:'',
					vimeo:'',
					youtube:'',						
					width:'',
					height:'',
					video:{
						args:'hd=1&wmode=opaque&showinfo=0&rel=0;',
						argsVimeo:'title=0&byline=0&portrait=0&api=1',
						dottedOverlay:'none',
						startAt:'',
						endAt:'',
						forceCover:true,
						forceRewind:true,
						loop:'none',
						mute:true,
						nextSlideAtEnd:false,
						ratio:'16:9',
						speed:1,
						volume:''
					},
					videoId:""				
				}
			};
				
	}

	// ADD JQUERY HOOKS	
	function addHooks() {
		RVS.JHOOKS.createLayerListElement.push(function(data) {	
			if (data.layer.addOns)			
				data.set += data.layer.addOns[slug]!==undefined ? 
					data.layer.addOns[slug].position === "after" ? '<i id="beaf_layerlist_id_'+data.layer.uid+'" data-lid="'+data.layer.uid+'" class="material-icons beaf_aftericon">flip</i>' : '<i id="beaf_layerlist_id_'+data.layer.uid+'" data-lid="'+data.layer.uid+'" class="material-icons beaf_beforeicon">flip</i>' 
					: '<i id="beaf_layerlist_id_'+data.layer.uid+'" data-lid="'+data.layer.uid+'" class="material-icons beaf_beforeicon">flip</i>';
			return data.set;
		});


		// CHANGE THE BG OF SLIDE BASED ON SELECTED BEFORE OR AFTER 
		RVS.JHOOKS.redrawSlideBG.push(function(data) {
			if (window.lastBeforeAfterMode==="after" && data===undefined) 
				data = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug];
			 return data;
		});
		RVS.JHOOKS.prepareOneSlide.push(function(data) {
			if (window.lastBeforeAfterMode==="after" && data===undefined) 
				data = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug];
			 return data;
		});
		
		// Change Mode if Layer Selected
		RVS.JHOOKS.layerListElementClicked.push(function(uid) {				
			if (uid!==undefined && RVS.L[uid].addOns!==undefined && RVS.L[uid].addOns[slug]!==undefined && RVS.L[uid].addOns[slug].position!==window.lastBeforeAfterMode) 
					setBeforeAfterMode(RVS.L[uid].addOns[slug].position);
			 
		});
	}
	
	// INITIALISE typewriter LISTENERS
	function initListeners() {	

		RVS.DOC.on('selectLayersDone.beforeafter',function() {
			for (var i in RVS.selLayers) if (RVS.L[RVS.selLayers[i]].addOns) updateLayerStructure(RVS.L[RVS.selLayers[i]].uid);
		});

		RVS.DOC.on('change_beaficon_layerlist',function(e,param) {
			if (param!==undefined && param.layerid!==undefined)
				if (RVS.L[param.layerid].addOns !== undefined) {
					updateBeforeAfterLayerListIcon(param.layerid);
					RVS.DOC.trigger("setBeforeAfterMode",RVS.L[param.layerid].addOns[slug].position);
				}
			//updateLayerVisibility();
		});

		RVS.DOC.on('slideFocusFunctionEnd.beforeafter',function() {
			
			if(!addon.isActive) return;
			
			window.lastBeforeAfterMode = window.lastBeforeAfterMode===undefined ? "before" : window.lastBeforeAfterMode;
			RVS.DOC.trigger('setBeforeAfterMode',window.lastBeforeAfterMode);
			for (var i in RVS.L) if (RVS.L[i].addOns) updateLayerStructure(RVS.L[i].uid);
			checkSlideEnabled();
		});
		
		// UPDATE DUE BACKUP/RESTORE
		RVS.DOC.on('SceneUpdatedAfterRestore.beforeafter',function() {
			
			if(!addon.isActive) return;
			
			for (var i in RVS.L) if (RVS.L[i].addOns !== undefined) updateBeforeAfterLayerListIcon(RVS.L[i].uid);				
			updateIconPickers();
		});
		
		RVS.DOC.on('slideFocusChanged.beforeafter',function() {
			
			if(!addon.isActive) return;
			
			updateSlideStructure();
			updateCurrentLayersStructure();
		});
		
		RVS.DOC.on('newSlideCreated.beforeafter', function () {
	
			if(!addon.isActive) return;
			
			updateSlideStructure();
			RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: id + '.slide.', trigger: 'init'});
		
		});
		
		RVS.DOC.on('beforeAfterEnabled.beforeafter', checkSlideEnabled);
		
		// ADD ICONSELECTOR TO ICON PICKER ENVIRONMENT
		RVS.DOC.on('addIcontoBeforeAfter',function(e,origEvent) {
			
			if(!addon.isActive) return;
			
			//Initialise the Mini Icon Selector
			RVS.F.initIconPicker();	
			var d = origEvent.event.currentTarget.dataset;				
			RVS.F.showIconPicker({parent:d.iconparent, classlist:d.classlist, insertinto:d.insertinto, closeafterpick:d.closeafterpick, shortreturn:d.shortreturn});
		});

		//ICON PICKER EVENT FOR BEFORE AFTER ADDON
		RVS.DOC.on('beafUpdateIconPicker',function(e,origEvent) {

			if(!addon.isActive) return;
			if (origEvent!==undefined && origEvent.eventparam!==undefined) updateIconPickers();
		});

		//SWAP BETWEEN MODES FOR BEFORE AFTER
		RVS.DOC.on('setBeforeAfterMode',function(e,param) {				
			setBeforeAfterMode(param);
		});

		RVS.DOC.on("redrawSlideBGDone", function(e,param) {
			
			if(!addon.isActive) return;
			
			if (window.lastBeforeAfterMode==="after") {
				RVS.C.rb_tlw.removeClass("kenburnallowed");
				RVS.C.slide.find('.tp-kbimg-wrapper').hide();
			}
			else
				RVS.C.slide.find('.tp-kbimg-wrapper').show();
			
		});

		RVS.DOC.on('click','.beaf_toolbar_element',function() {
			RVS.DOC.trigger(this.dataset.evt,this.dataset.evtparam);
			var mic = $('#beaf_toolbar_mainicon');
			if (this.dataset.iconmirror=="true") 
				mic.addClass("mirrorhorizontal");
			else
				mic.removeClass("mirrorhorizontal");
			mic[0].innerHTML = this.dataset.icon;

		});
	}
	//CHANGE BEFORE / AFTER MODE
	function setBeforeAfterMode(param) {
		if (param!==undefined) {					
			var notparam = param==="before" || param==="both" ? "after" : "before";
			window.lastBeforeAfterMode = param;										
			updateLayerVisibility();
			RVS.DOC.trigger("updateslidebasic");					
			if (param==="both") {
				addon.forms.selector.before.removeClass("selected");
				addon.forms.selector.after.removeClass("selected");
			} else 
				addon.forms.selector.both.removeClass("selected");
			addon.forms.selector[notparam].removeClass("selected");										
			addon.forms.selectorbg[notparam].removeClass("selected");
			
			addon.forms.selector[param].addClass("selected");
			addon.forms.selectorbg[param].addClass("selected");

			addon.forms["source_"+notparam].hide();
			param = param==="both" || param==="before" ? "before" : "after";
			addon.forms["source_"+param].show();					
		}	
	}

	function updateLayerVisibility() {
		for (var lid in RVS.L) 
			if (RVS.L[lid].uid>=0 && RVS.L[lid].uid<=99999 && RVS.L[lid].addOns[slug] !==undefined) 				
			RVS.F.showHideLayer({ignoreBackup:true, uid:RVS.L[lid].uid, val:(window.lastBeforeAfterMode==="both" ? true : RVS.L[lid].addOns[slug].position === window.lastBeforeAfterMode)});		
		RVS.F.checkShowHideLayers();
	}


	// UPDATE THE BEFORE / AFTER ICONS ON THE LAYER LIST
	function updateBeforeAfterLayerListIcon(uid) {				
		var el = document.getElementById('beaf_layerlist_id_'+uid);
		if (el!==null && el!==undefined) {				
			el.className = "material-icons beaf_"+RVS.L[uid].addOns[slug].position+"icon";
		}
		else {
			el = document.getElementById('llist_too_iw_'+uid);				
			if (el!==null && el!==undefined) el.innerHTML += '<i id="beaf_layerlist_id_'+uid+'" data-lid="'+uid+'" class="material-icons beaf_'+RVS.L[uid].addOns[slug].position+'icon">flip</i>';				
		}
	}



	// UPDATE ICON PICKERS
	function updateIconPickers() {
		var _a = ["left","right","up","down"];
		for (var i in _a) {
			if(!_a.hasOwnProperty(i)) continue;
			document.getElementById('beaf_iconp_'+_a[i]).className = RVS.SLIDER.settings.addOns[slug].icon[_a[i]];
		}
	}



	// CREATE INPUT FIELDS
	function createLayerSettingsFields() {						
		var _h = "";
		_h += '<div  class="form_inner_header"><i class="material-icons">flip</i>'+bricks.general+'</div>';
		_h += '<div class="collapsable" style="display:block !important">';	
		_h += '			<label_a>'+bricks.environment+'</label_a><select id="beaficon_layerlist" class="layerinput tos2 nosearchbox easyinit callEvent" data-evt="change_beaficon_layerlist" data-r="addOns.'+slug+'.position" data-theme="dark">';
		_h += '			<option value="before">'+bricks.before+'</option>';
		_h += '			<option value="after">'+bricks.after+'</option>';
		_h += '			</select><linebreak/>';			
		_h += '</div>';

		addon.forms.layergeneral.append($(_h));			
		addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});
		RVS.F.initOnOff();
		
	}

	// CREATE TOP TOOLBAR
	function createToolbarMenu() {
		var _h = '<div class="beforeafter_selector toolbar_selector_icons" id="beforeafter_selector_wrap">';
		_h +='		<div class="selected_placeholder"><i id="beaf_toolbar_mainicon" class="toptoolbaricon material-icons ">flip</i><span class="highlight_arrow"></span></div>';
		_h +='		<div class="tool_dd_wrap">';
		_h +='			<div id="beaf_tb_before" data-iconmirror="false" data-icon="flip" class="beaf_toolbar_element toolbar_listelement callEvent" data-evt="setBeforeAfterMode" data-evtparam="before"><i class="material-icons">flip</i>'+bricks.selectbefore+'</div>';
		_h +='			<div id="beaf_tb_after" data-iconmirror="true" data-icon="flip"  class="beaf_toolbar_element toolbar_listelement callEvent" data-evt="setBeforeAfterMode" data-evtparam="after"><i class="material-icons mirrorhorizontal">flip</i>'+bricks.selectafter+'</div>';
		_h +='			<div id="beaf_tb_all" data-iconmirror="false" data-icon="all_inclusive"  class="beaf_toolbar_element toolbar_listelement callEvent" data-evt="setBeforeAfterMode" data-evtparam="both"><i class="material-icons">all_inclusive</i>'+bricks.selectbeforeafter+'</div>';
		_h +='		</div>';
		_h +='	</div>';	

		$('#right_top_toolbar_wrap').prepend($(_h));
		addon.forms.selector = { before: $('#beaf_tb_before'), after:$('#beaf_tb_after'), both:$('#beaf_tb_all')};
	}

			
	// CREATE INPUT FIELDS
	function createSlideSettingsFields() {			
		var _h = "";
		_h += '<div  class="form_inner_header"><i class="material-icons">flip</i>'+bricks.general+'</div>';
		_h += '<div class="collapsable" style="display:block !important">';	
		_h += '		<label_a>'+bricks.active+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.enable" data-showhide=".beforeafter_slide_settings" data-showhidedep="true" value="on" data-evt="beforeAfterEnabled"><linebreak/>';
		_h += '		<div class="beforeafter_slide_settings">';
		_h += '			<label_a>'+bricks.direction+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.direction" data-theme="dark">';
		_h += '			<option value="horizontal">'+bricks.horizontal+'</option>';
		_h += '			<option value="vertical">'+bricks.vertical+'</option>';
		_h += '			</select><linebreak/>';
		_h += '<label_a>'+bricks.initsplit+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.moveTo.#size#.v" data-min="0" data-max="100" type="text">';
		_h += '</div>';
		_h += '</div>';						
		_h += '<div id="beforeafter_settings" class="beforeafter_slide_settings">';
		
		// ANIMATION
		_h += '		<div  class="form_inner_header"><i class="material-icons">more_vert</i>'+bricks.initsettings+'</div>';
		_h += '		<div class="collapsable" style="display:block !important">';	
		_h += '			<label_a>'+bricks.delay+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.delay" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.duration+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.time" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.easing+'</label_a><select class="slideinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.easing" data-theme="dark"></select><linebreak/>';
		_h += '			<label_a>'+bricks.animateout+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.animateOut" data-theme="dark">';
		_h += '			<option value="fade">'+bricks.fade+'</option>';
		_h += '			<option value="collapse">'+bricks.collapse+'</option>';
		_h += '			</select><linebreak/>';			
		_h += '		</div>';
		
		// TEASER						
		_h += '		<div id="beforeafter_teaser_settings" class="form_inner_header"><i class="material-icons">compare</i>'+bricks.teasersettings+'</div>';
		_h += '		<div class="collapsable" style="display:block !important">';
		_h += '			<label_a>'+bricks.teaser+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.teaser.set" data-theme="dark" data-showprio="hide" data-show="beaf_teasersettings" data-hide="beaf_teasersettings_hide_*val*">';
		_h += '			<option value="none">'+bricks.none+'</option>';
		_h += '			<option value="initial">'+bricks.teainitial+'</option>';
		_h += '			<option value="infinite">'+bricks.tealoop+'</option>';
		_h += '			<option value="once">'+bricks.teaonce+'</option>';
		_h += '			</select><linebreak/>';
		_h += '			<div class="beaf_teasersettings beaf_teasersettings_hide_val">';
		_h += '				<label_a>'+bricks.animateout+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.teaser.type" data-theme="dark">';
		_h += '				<option value="repel">'+bricks.repel+'</option>';
		_h += '				<option value="attract">'+bricks.attract+'</option>';
		_h += '				</select><linebreak/>';			
		_h += '				<label_a>'+bricks.distance+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.teaser.distance" data-min="0" data-max="2400" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.speed+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.teaser.speed" data-min="0" data-max="5000" type="text"><linebreak/>';			
		_h += '				<label_a>'+bricks.easing+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.teaser.easing" data-theme="dark">';
		_h += '				<option value="ease-out">Out</option>';
		_h += '				<option value="ease-in">In</option>';
		_h += '				<option value="ease-in-out">InOut</option>';
		_h += '				<option value="ease">Ease</option>';
		_h += '				<option value="linear">'+bricks.linear+'</option>';
		_h += '				</select><linebreak/>';
		_h += '				<label_a>'+bricks.delay+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.teaser.delay" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			</div>';
		_h += '		</div>';

		// TEASER						
		_h += '		<div id="beforeafter_arrow_settings" class="form_inner_header"><i class="material-icons">swap_horiz</i>'+bricks.arrowsettings+'</div>';
		_h += '		<div class="collapsable" style="display:block !important">';
		_h += '			<label_a>'+bricks.arrowanim+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.shift.set" data-showhide=".beforeafter_slide_shift_settings" data-showhidedep="true" value="on"><linebreak/>';
		_h += '			<div class="beforeafter_slide_shift_settings">';
		_h += '				<label_a>'+bricks.offset+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.shift.offset" data-min="0" data-max="2400" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.speed+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.shift.speed" data-min="0" data-max="5000" type="text"><linebreak/>';			
		_h += '				<label_a>'+bricks.easing+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.shift.easing" data-theme="dark">';
		_h += '				<option value="ease-out">Out</option>';
		_h += '				<option value="ease-in">In</option>';
		_h += '				<option value="ease-in-out">InOut</option>';
		_h += '				<option value="ease">Ease</option>';
		_h += '				<option value="linear">'+bricks.linear+'</option>';
		_h += '				</select><linebreak/>';
		_h += '				<label_a>'+bricks.delay+'</label_a><input class="slideinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.shift.delay" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			</div>';
		_h += '		</div>';

		_h += '</div>'; //END OF BEFOREAFTER SLIDE SETTINGS
		
		
		addon.forms.slidegeneral.append($(_h));	

		// UPDATE SOURCE CONTAINER		
		if ($('#beaf_before_source').length===0) $('#form_slidebg_source, #form_slidebg_ssettings, #form_slidebg_pddd').wrapAll('<div id="beaf_before_source" class="ssm_content selected"></div>');
		addon.forms.source_before = $('#beaf_before_source');
		addon.forms.source_before.wrap('<div id="beaf_before_after_sourcewrap"></div>');
		addon.forms.sourcewrap = $('#beaf_before_after_sourcewrap');

		_h = '';				
		_h += '<div class="settingsmenu_wrapbtn beaf_sbgs_btn" style="width:159px"><div id="beaf_sbgs_before" data-inside="#form_slidebg" data-evt="setBeforeAfterMode" data-evtparam="before" data-showssm="#beaf_before_source" class="ssmbtn selected callEvent">'+bricks.before+'</div></div>';
		_h += '<div class="settingsmenu_wrapbtn beaf_sbgs_btn" style="width:159px"><div id="beaf_sbgs_after" data-inside="#form_slidebg" data-evt="setBeforeAfterMode" data-evtparam="after" data-showssm="#beaf_after_source" class="ssmbtn callEvent">'+bricks.after+'</div></div>';
			
		addon.forms.sourcewrap.prepend($(_h));
		addon.forms.selectorbg = { before: $('#beaf_sbgs_before'), after:$('#beaf_sbgs_after'), both:$('#beaf_sbgs_before')};

		
		
		// ADD AFTER SOURCE SETTINGS  <option value="youtube">'+bricks.youtube+'</option><option value="vimeo">'+bricks.vimeo+'</option><option value="html5">'+bricks.htmlvideo+'</option>
		_h =  '<div id="beaf_after_source" class="ssm_content">';				
		_h += '		<div id="form_slide_after_bg_source" class="form_inner open">';
		_h += '			<div class="form_inner_header"><i class="material-icons">link</i>'+bricks.source+'</div>';			
		_h += '			<div class="collapsable">';
		_h += '				<label_a>'+bricks.type+'</label_a><div class="input_with_buttonextenstion"><select id="slide_after_bg_type" data-available=".sssafter_for_*val*" data-unavailable=".sssafter_notfor_*val*" data-updatetext="#after_selected_slide_source" data-triggerinp="#slide_after_bg_*val*_alt,#slide_after_bg_*val*_title" data-evt="updateslidebasic" data-show=".slide_after_bg_*val*_settings" data-hide=".slide_after_bg_settings" class="slideinput tos2 nosearchbox easyinit "  data-r="addOns.'+slug+'.bg.type"><option value="image">'+bricks.image+'</option><option value="external">'+bricks.externalimage+'</option><option value="trans">'+bricks.transparent+'</option><option value="solid">'+bricks.colored+'</option></select>';
		_h += '				<div class="buttonextenstion slide_after_bg_image_settings slide_after_bg_external_settings slide_after_bg_settings slide_after_bg_youtube_settings slide_after_bg_html5_settings slide_after_bg_vimeo_settings">';
		_h += '					<input class="dontseeme" id="slide_after_bg_image_path" />';
		_h += '					<div class="basic_action_button copyclipboard onlyicon dark_action_button" data-clipboard-action="copy" data-clipboard-target="#slide_after_bg_image_path"><i class="material-icons">link</i></div>';
		_h += '				</div>';
		_h += '			</div>';					
		_h += '			<div id="" class="slide_after_bg_image_settings slide_after_bg_settings">';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-rid="#slide#.slide.addOns.'+slug+'.bg.imageId" class="getImageFromMediaLibrary basic_action_button longbutton callEventButton"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-rid="#slide#.slide.addOns.'+slug+'.bg.imageId" class="getImageFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div>';
		_h += '			</div>';				
		_h += '			<div id="" class="slide_after_bg_external_settings slide_after_bg_settings">';						
		_h += '				<label_a>'+bricks.source+'</label_a><input id="s_ext_src" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.externalSrc" placeholder="'+bricks.enterimageurl+'">';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" class="basic_action_button  longbutton callEventButton"><i class="material-icons">refresh</i>'+bricks.refreshsource+'</div>';
		_h += '			</div>';
		_h += '			<div id="" class="slide_after_bg_solid_settings slide_after_bg_settings"><label_a>'+bricks.bgcolor+'</label_a><input type="text" data-evt="updateslidebasic" data-editing="'+bricks.backgroundcolor+'" name="slide_after_bg_color" id="s_bg_color" data-visible="true" class="my-beaf-color-field slideinput easyinit" data-r="addOns.'+slug+'.bg.color" value="#fff"></div>';
		_h += '			<div id="" class="slide_after_bg_youtube_settings slide_after_bg_settings">';
		_h += '				<label_a>'+bricks.youtubeid+'</label_a><input id="s_bg_youtube_src" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.youtube" placeholder="'+bricks.enterytid+'">';
		_h += '				<label_a>'+bricks.posterimage+'</label_a><div data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-f="#slide#.slide.addOns.'+slug+'.bg.youtube" data-evt="updateslidebasic" class="getImageFromYouTube basic_action_button longbutton  callEventButton"><i class="material-icons">style</i>'+bricks.ytposter+'</div>';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-rid="#slide#.slide.addOns.'+slug+'.bg.imageId" class="getImageFromMediaLibrary basic_action_button longbutton callEventButton"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>';						
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" class="basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div>	';																
		_h += '			</div>';
		_h += '			<div id="" class="slide_after_bg_vimeo_settings slide_after_bg_settings">';
		_h += '				<label_a>'+bricks.vimeoid+'</label_a><input id="s_bg_vimeo_src" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.vimeo" placeholder="'+bricks.entervimeoid+'">';
		_h += '				<label_a>'+bricks.posterimage+'</label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-rid="#slide#.slide.addOns.'+slug+'.bg.imageId" class="getImageFromMediaLibrary basic_action_button  longbutton callEventButton"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>	';					
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" class="basic_action_button longbutton  callEventButton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div><span class="linebreak"></span>';										
		_h += '			</div>';
		_h += '			<div id="" class="slide_after_bg_html5_settings slide_after_bg_settings">';
		_h += '				<label_a>'+bricks.mpeg+'</label_a><input id="s_bg_mpeg_src" data-evt="updateslidebasic" class="slideinput easyinit nmarg" type="text" data-r="addOns.'+slug+'.bg.mpeg" placeholder="'+bricks.entermpegsrc+'">';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" data-target="#s_bg_mpeg_src" data-rid="#slide#.slide.addOns.'+slug+'.bg.videoId" class="getVideoFromMediaLibrary basic_action_button longbutton callEventButton"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" data-target="#s_bg_mpeg_src" data-r="addOns.'+slug+'.bg.mpeg" class="getVideoFromObjectLibrary basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div>';							
		_h += '				<div class="div25"></div>';
		_h += '				<label_a>'+bricks.posterimage+'</label_a><div data-evt="updateslidebasic" data-r="#slide#.slide.addOns.'+slug+'.bg.image" data-rid="#slide#.slide.addOns.'+slug+'.bg.imageId" class="getImageFromMediaLibrary basic_action_button longbutton callEventButton"><i class="material-icons">style</i>'+bricks.medialibrary+'</div>';
		_h += '				<label_a></label_a><div data-evt="updateslidebasic" class="basic_action_button longbutton callEventButton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div>';
		_h += '			</div>';
		_h += '		</div>';			
		_h += '		<div id="after_form_slide_after_bg_ssettings" class="form_inner open sssafter_notfor_solid sssafter_notfor_trans sssafter_for_image sssafter_for_external sssafter_for_youtube sssafter_for_vimeo sssafter_for_html5">';
		_h += '			<div class="form_inner_header"><i class="material-icons">chrome_reader_mode</i><span id="after_selected_slide_source"></span>'+bricks.settings+'</div>';				
		_h += '			<div class="collapsable">';											
		_h += '				<div class="slide_after_bg_image_settings slide_after_bg_settings">';
		_h += '					<label_a>'+bricks.sourcesize+'</label_a><select data-theme="dark" id="after_slide_after_bg_img_ssize"  class="slideinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.bg.imageSourceType">';
		_h += '						<option value="full">Original Size</option><option value="thumbnail">Thumbnail</option><option value="medium">Medium</option><option value="large">Large</option><option value="medium_large">Medium Large</option>';					
		_h += '					</select><span class="linebreak"></span>';				
		_h += '				</div>';
		_h += '				<div class="slide_after_bg_image_settings slide_after_bg_external_settings slide_after_bg_settings ">';					
		_h += '					<div id="after_ken_burn_bg_setting_off">';
		_h += '						<div id="after_slide_after_bg_settings_wrapper">';
		_h += '							<div id="after_slide_after_bg_and_repeat_fit_wrap">';
		_h += '								<label_a>'+bricks.bgfit+'</label_a>';
		_h += '								<div class="radiooption">';
		_h += '										<div><input type="radio" class="slideinput easyinit" value="cover" name="slide_after_bg_fit"  data-evt="updateslidebasic" data-show=".slide_after_bg_fit_*val*" data-hide=".slide_after_bg_fit" data-r="addOns.'+slug+'.bg.fit"><label_sub>Cover</label_sub></div>';
		_h += '										<div><input type="radio" class="slideinput easyinit" value="contain" name="slide_after_bg_fit"  data-evt="updateslidebasic" data-show=".slide_after_bg_fit_*val*" data-hide=".slide_after_bg_fit" data-r="addOns.'+slug+'.bg.fit"><label_sub>Contain</label_sub></div>';
		_h += '										<div><input type="radio" class="slideinput easyinit" value="percentage" name="slide_after_bg_fit"  data-evt="updateslidebasic" data-show=".slide_after_bg_fit_*val*" data-hide=".slide_after_bg_fit" data-r="addOns.'+slug+'.bg.fit"><label_sub>Percentage</label_sub></div>';
		_h += '										<div><input type="radio" class="slideinput easyinit" value="auto" name="slide_after_bg_fit"  data-evt="updateslidebasic" data-show=".slide_after_bg_fit_*val*" data-hide=".slide_after_bg_fit" data-r="addOns.'+slug+'.bg.fit"><label_sub>Auto</label_sub></div>';
		_h += '								</div>';
		_h += '								<div class="div15"></div>';
		_h += '								<div class="slide_after_bg_fit slide_after_bg_fit_percentage">';
		_h += '									<row class="direktrow">';
		_h += '										<onelong><label_icon class="ui_width"></label_icon><input data-allowed="%" data-numeric="true" id="after_slide_after_bg_fitX" data-evt="updateslidebasic" class="slideinput easyinit withsuffix" type="text" data-r="addOns.'+slug+'.bg.fitX"></onelong>';
		_h += '										<oneshort><label_icon class="ui_height"></label_icon><input data-allowed="%" data-numeric="true" id="after_slide_after_bg_fitY" data-evt="updateslidebasic" class="slideinput easyinit withsuffix" type="text" data-r="addOns.'+slug+'.bg.fitY"></oneshort>';
		_h += '									</row>';
		_h += '								</div>';
		_h += '								<label_a>'+bricks.repeat+'</label_a><select data-theme="dark" id="after_slide_after_bg_repeat"  data-evt="updateslidebasic" class="slideinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.bg.repeat">';
		_h += '									<option value="no-repeat" selected="selected">no-repeat</option>';
		_h += '									<option value="repeat">repeat</option>';
		_h += '									<option value="repeat-x">repeat-x</option>';
		_h += '									<option value="repeat-y">repeat-y</option>';				
		_h += '								</select><span class="linebreak"></span>';
		_h += '								<div class="div10"></div>';
		_h += '							</div>';
		_h += '							<label_a>'+bricks.position+'</label_a><select style="display:none !important" data-theme="dark" id="after_slidebg_position" data-unselect=".slide_after_bg_position_selector" data-select="#after_slidebg_position_*val*" data-evt="updateslidebasic" data-show=".slide_after_bg_pos_*val*" data-hide=".slide_after_bg_pos" class="slideinput easyinit"  data-r="addOns.'+slug+'.bg.position"><option value="left center">'+bricks.leftcenter+'</option><option value="left bottom">'+bricks.leftbottom+'</option><option value="left top">'+bricks.lefttop+'</option><option value="center top">'+bricks.centertop+'</option><option value="center center">'+bricks.centercenter+'</option><option value="center bottom">'+bricks.centerbottom+'</option><option value="right top">'+bricks.righttop+'</option><option value="right center">'+bricks.rightcenter+'</option><option value="right bottom">'+bricks.rightbottom+'</option><option value="percentage">'+bricks.xperyper+'</option>';
		_h += '							</select><div class="bg_alignselector_wrap">';
		_h += '							<div class="bg_align_row">';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="left top" id="after_slidebg_position_left-top"></div> ';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="center top" id="after_slidebg_position_center-top"></div> ';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="right top" id="after_slidebg_position_right-top"></div> ';
		_h += '							</div>';
		_h += '							<div class="bg_align_row">';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="left center" id="after_slidebg_position_left-center"></div>';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="center center" id="after_slidebg_position_center-center"></div> ';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="right center" id="after_slidebg_position_right-center"></div>'; 
		_h += '							</div>';
		_h += '							<div class="bg_align_row">';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="left bottom" id="after_slidebg_position_left-bottom"></div> ';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="center bottom" id="after_slidebg_position_center-bottom"></div> ';
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="right bottom" id="after_slidebg_position_right-bottom"></div>';
		_h += '							</div>';
		_h += '							<div class="bg_align_xy">	';									
		_h += '								<div class="triggerselect slide_after_bg_position_selector bg_alignselector" data-select="#after_slidebg_position" data-val="percentage" id="after_slidebg_position_percentage"></div> ';											
		_h += '								<xy_label>'+bricks.xperyper+'</xy_label>';										
		_h += '							</div>';
		_h += '						</div>'; 
		_h += '						<row class="directrow slide_after_bg_pos slide_after_bg_pos_percentage">';									
		_h += '							<onelong><label_icon class="ui_x"></label_icon><input id="after_slidebg_positionX" data-evt="updateslidebasic" class="slideinput easyinit shortinput" data-numeric="true" data-allowed="%" type="text" data-r="addOns.'+slug+'.bg.positionX"></onelong>';
		_h += '							<oneshort><label_icon class="ui_y"></label_icon><input id="after_slidebg_positionY" data-evt="updateslidebasic" class="slideinput easyinit" data-numeric="true" data-allowed="%" type="text" data-r="addOns.'+slug+'.bg.positionY"></oneshort>';
		_h += '						</row>';
		_h += '					</div>';
		_h += '				</div>';
		_h += '			</div>';			
		_h += '			<div class="slide_after_bg_external_settings slide_after_bg_settings">';						
		_h += '				<label_a>'+bricks.widthattr+'</label_a><input id="after_slide_after_bg_width" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.width" data-numeric="true" data-allowed="px">';
		_h += '				<label_a>'+bricks.heightattr+'</label_a><input data-numeric="true" data-allowed="px" id="after_slide_after_bg_height" data-evt="updateslidebasic" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.height">';
		_h += '			</div>';

		_h += '			<div class="slide_after_bg_youtube_settings slide_after_bg_vimeo_settings slide_after_bg_html5_settings slide_after_bg_settings">';
		_h += '				<label_a>'+bricks.aspectratio+'</label_a><select data-theme="dark" id="after_slide_vid_aratio" class="slideinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.bg.video.ratio"><option value="16:9">16:9</option><option value="4:3">4:3</option></select><span class="linebreak"></span>';
		_h += '				<div id="after_slide_after_dotted_overlay">';
		_h += '					<label_a>'+bricks.overlay+'</label_a><select data-evt="updateslidebasic" id="after_sl_vid_overlay" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.bg.video.dottedOverlay" data-theme="dark"><option value="none" selected="selected">None</option><option value="twoxtwo">2 x 2 Black</option><option value="twoxtwowhite">2 x 2 White</option><option value="threexthree">3 x 3 Black</option><option value="threexthreewhite">3 x 3 White</option></select><span class="linebreak"></span>';
		_h += '				</div>';
		_h += '				<label_a>'+bricks.loopmode+'</label_a><select data-theme="dark" id="after_slide_vid_loop" class="slideinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.bg.video.loop">';
		_h += '					<option value="none">'+bricks.disable+'</option>';
		_h += '					<option value="loop">'+bricks.slidertimepause+'</option>';
		_h += '					<option value="loopandnoslidestop">'+bricks.slidertimerkeep+'</option>';
		_h += '				</select><span class="linebreak"></span>';

		_h += '				<longoption><i class="material-icons">open_with</i><label_a>'+bricks.forcecovermode+'></label_a><input type="checkbox"  id="after_sl_vid_force_cover" class="slideinput easyinit" data-r="addOns.'+slug+'.bg.video.forceCover" data-showhide="#slide_after_dotted_overlay" data-showhidedep="true"/></longoption>';
		_h += '				<longoption><i class="material-icons">skip_next</i><label_a>'+bricks.nextslideatend+'</label_a><input type="checkbox"  id="after_sl_vid_nextslide" class="slideinput easyinit" data-r="addOns.'+slug+'.bg.video.nextSlideAtEnd" /></longoption>';
		_h += '				<longoption><i class="material-icons">fast_rewind</i><label_a>'+bricks.rewindstart+'></label_a><input type="checkbox"  id="after_sl_vid_forceRewind" class="slideinput easyinit" data-r="addOns.'+slug+'.bg.video.forceRewind" /></longoption>';
		_h += '				<longoption><i class="material-icons">volume_mute</i><label_a>'+bricks.muteatstart+'></label_a><input type="checkbox"  id="after_sl_vid_mute" class="slideinput easyinit" data-r="addOns.'+slug+'.bg.video.mute" /></longoption>';
		_h += '				<div class="div15"></div>';
		_h += '				<row class="slide_after_bg_youtube_settings slide_after_bg_vimeo_settings slide_after_bg_settings directrow">';
		_h += '					<onelong><label_icon class="ui_volume"></label_icon><input id="after_slide_vid_vol" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.video.volume"></onelong>';
		_h += '					<oneshort><label_icon class="ui_speed"></label_icon><select data-theme="dark" id="after_slide_vid_speed" class="slideinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.bg.video.speed"><option value="0.25">1/4</option><option value="0.50">1/2</option><option selected="selected" value="1">Normal</option><option value="1.5">x1.5</option><option value="2">x2</option></select></oneshort>';
		_h += '				</row>';
		_h += '				<row class="directrow">';
		_h += '					<onelong><label_icon class="ui_startat"></label_icon><input id="after_slide_vid_startat" class="slideinput easyinit" placeholder="00:00" type="text" data-r="addOns.'+slug+'.bg.video.startAt"></onelong>';
		_h += '					<oneshort><label_icon class="ui_endat"></label_icon><input id="after_slide_vid_endat" class="slideinput easyinit" placeholder="00:00" type="text" data-r="addOns.'+slug+'.bg.video.endAt"></oneshort>';
		_h += '				</row>';
		_h += '			</div>';
		_h += '			<div class="div15"></div>';
		_h += '			<div class="slide_after_bg_youtube_settings slide_after_bg_settings"><label_a>'+bricks.arguments+'</label_a><input id="after_slide_vid_argsyt" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.video.args"><span class="linebreak"></span></div>';
		_h += '			<div class="slide_after_bg_vimeo_settings slide_after_bg_settings"><label_a>'+bricks.arguments+'</label_a><input id="after_slide_vid_argvim" class="slideinput easyinit" type="text" data-r="addOns.'+slug+'.bg.video.argsVimeo"><span class="linebreak"></span></div>';
		_h += '		</div>';
		_h += '</div>';

					
		addon.forms.sourcewrap.append($(_h));			
		addon.forms.source_after = $('#beaf_after_source');
		addon.forms.source_after.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});


		addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});
		
		RVS.F.initTpColorBoxes('#beaf_after_source .my-beaf-color-field');

		RVS.F.initOnOff();
		// easings init
		addon.forms.slidegeneral.find('.easingSelect').each(function() {
			RVS.F.createEaseOptions(this);
		});

		RVS.F.updateEasyInputs({container: addon.forms.source_after, path: RVS.S.slideId + '.slide.', trigger:"init"});
		RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger:"init"});
		
	}	

	

				
	// CREATE INPUT FIELDS
	function createSliderSettingsFields() {			
		var _h = "";
		_h += '<div  class="form_inner_header"><i class="material-icons">compare</i>'+bricks.basettings+'</div>';			
		
		// BEFORE / AFTER SUBMENU 
		_h += '<div  id="beforeafter_form_wrap" class="collapsable" style="display:block !important">'; 			
		_h += '<div class="settingsmenu_wrapbtn"><div data-inside="#beforeafter_form_wrap" data-showssm="#beforeafter_arrows" class="ssmbtn selected">'+bricks.icons+'</div></div>';			
		_h += '<div class="settingsmenu_wrapbtn"><div data-inside="#beforeafter_form_wrap" data-showssm="#beforeafter_dividerline" class="ssmbtn ">'+bricks.dividerline+'</div></div>';			
		_h += '<div class="settingsmenu_wrapbtn"><div data-inside="#beforeafter_form_wrap" data-showssm="#beforeafter_misc" class="ssmbtn">'+bricks.animate+'</div></div>';	
		_h += '<div class="settingsmenu_wrapbtn" style="width:278px"><div data-inside="#beforeafter_form_wrap" data-showssm="#beforeafter_dragcontainer" class="ssmbtn">'+bricks.dragcontainer+'</div></div>';				
		_h += '<div class="div25"></div>';	
		
		// ARROWS SETTING
		_h += '		<div id="beforeafter_arrows" class="ssm_content selected">'; 
		_h += '			<div id="beforeafter_iconselector_wrap">';			
		_h += '				<label_a>'+bricks.horizontal+'</label_a><div class="triggerEvent beforeafter_iconpicker" data-helpkey="beforeafter-icon" data-shortreturn="true" data-evt="addIcontoBeforeAfter" data-closeafterpick="true" data-iconparent="#beforeafter_iconselector_wrap" data-classlist="beforeaftericonpicker" data-insertinto="#beaf_icon_left"><i id="beaf_iconp_left" class="fa-chevron-left"></i></div>';
		_h += '<div class="triggerEvent beforeafter_iconpicker" data-evt="addIcontoBeforeAfter" data-iconparent="#beforeafter_iconselector_wrap" data-helpkey="beforeafter-icon" data-shortreturn="true"  data-closeafterpick="true"  data-classlist="beforeaftericonpicker" data-insertinto="#beaf_icon_right"><i id="beaf_iconp_right" class="fa-chevron-right"></i></div><linebreak/>';
		_h += '				<label_a>'+bricks.vertical+'</label_a><div class="triggerEvent beforeafter_iconpicker" data-helpkey="beforeafter-icon" data-evt="addIcontoBeforeAfter" data-shortreturn="true"  data-closeafterpick="true"  data-iconparent="#beforeafter_iconselector_wrap" data-classlist="beforeaftericonpicker" data-insertinto="#beaf_icon_up"><i id="beaf_iconp_up" class="fa-chevron-up"></i></div>';
		_h += '<div class="triggerEvent beforeafter_iconpicker" data-evt="addIcontoBeforeAfter" data-iconparent="#beforeafter_iconselector_wrap" data-helpkey="beforeafter-icon" data-shortreturn="true"  data-closeafterpick="true"  data-classlist="beforeaftericonpicker" data-insertinto="#beaf_icon_down"><i id="beaf_iconp_down" class="fa-chevron-down"></i></div><linebreak/>';			
		_h += ' 			<div style="display:none">';
		_h += '					<input class="sliderinput easyinit callEvent" data-evt="beafUpdateIconPicker" data-evtparam="#beaf_iconp_left" id="beaf_icon_left" data-r="addOns.'+slug+'.icon.left" type="text">';
		_h += '					<input class="sliderinput easyinit callEvent" data-evt="beafUpdateIconPicker" data-evtparam="#beaf_iconp_right" id="beaf_icon_right" data-r="addOns.'+slug+'.icon.right" type="text">';
		_h += '					<input class="sliderinput easyinit callEvent" data-evt="beafUpdateIconPicker" data-evtparam="#beaf_iconp_up" id="beaf_icon_up" data-r="addOns.'+slug+'.icon.up" type="text">';
		_h += '					<input class="sliderinput easyinit callEvent" data-evt="beafUpdateIconPicker" data-evtparam="#beaf_iconp_down" id="beaf_icon_down" data-r="addOns.'+slug+'.icon.down" type="text">';
		_h += '				</div>';			
		_h += '			</div>';

		_h += '			<div class="div15"></div>';			
		_h += '			<label_a>'+bricks.iconsize+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.icon.size" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.iconspacing+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.icon.space" data-min="0" data-max="1000" type="text"><linebreak/>';						
		_h += '			<label_a>'+bricks.iconcolor+'</label_a><input type="text" id="beforeafter_iconcolor" data-editing="' + bricks.iconcolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.icon.color" value="#000000"><linebreak/>';								
		_h += '			<div class="div10"></div>';
		_h += '			<label_a>'+bricks.iconshadow+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.icon.shadow.set" data-showhide=".beforeafter_icon_shadow" data-showhidedep="true" value="on"><linebreak/>';
		_h += '			<div class="beforeafter_icon_shadow">';
		_h += '				<label_a>'+bricks.shadowblur+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.icon.shadow.blur" data-min="0" data-max="1000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.shadowcolor+'</label_a><input type="text" id="beforeafter_iconshadowcolor" data-editing="' + bricks.shadowcolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.icon.shadow.color" value="#000000"><linebreak/>';
		_h += '			</div>'; // END OF BEFORE AFTER SHADOWS 
		_h += '		</div>'; // END OF BEFORE AFTER ARROWS 

		// DRAG CONTAINER
		_h += '		<div id="beforeafter_dragcontainer" class="ssm_content">';
		_h += '			<label_a>'+bricks.padding+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.drag.padding" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.borderradius+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px,%" data-r="addOns.'+slug+'.drag.radius" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.bgcolor+'</label_a><input type="text" id="beforeafter_dragbgcolor" data-editing="' + bricks.bgcolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.drag.bgcolor" value="#000000"><linebreak/>';			
		_h += '			<div class="div10"></div>';
		_h += '			<label_a>'+bricks.border+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.drag.border.set" data-showhide=".beforeafter_drag_border" data-showhidedep="true" value="on"><linebreak/>';			
		_h += '			<div class="beforeafter_drag_border">';
		_h += '				<label_a>'+bricks.borderwidth+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.drag.border.width" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.bordercolor+'</label_a><input type="text" id="beforeafter_dragbordercolor" data-editing="' + bricks.bordercolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.drag.border.color" value="#000000"><linebreak/>';
		_h += '				<div class="div15"></div>';
		_h += '			</div>';

		_h += '			<label_a>'+bricks.boxshadow+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.drag.boxshadow.set" data-showhide=".beforeafter_boxshadow_drag_shadow" data-showhidedep="true" value="on"><linebreak/>';			
		_h += '			<div class="beforeafter_boxshadow_drag_shadow">';
		_h += '				<label_a>'+bricks.shadowblur+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.drag.boxshadow.blur" data-min="0" data-max="1000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.shadowstrength+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.drag.boxshadow.strength" data-min="0" data-max="1000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.shadowcolor+'</label_a><input type="text" id="beforeafter_dragboxshadowcolor" data-editing="' + bricks.shadowcolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.drag.boxshadow.color" value="#000000"><linebreak/>';
		_h += '			</div>'; // END OF BEFORE AFTER SHADOWS 			
		_h += '		</div>'; // END OF DRAG CONTAINER

		// DIVIDER SETTINGS
		_h += '		<div id="beforeafter_dividerline" class="ssm_content">'; 
		_h += '			<label_a>'+bricks.linesize+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.divider.size" data-min="0" data-max="5000" type="text"><linebreak/>';
		_h += '			<label_a>'+bricks.linecolor+'</label_a><input type="text" id="beforeafter_dragbgcolor" data-editing="' + bricks.linecolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.divider.color" value="#000000"><linebreak/>';
		_h += '			<div class="div10"></div>';
		_h += '			<label_a>'+bricks.lineshadow+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.divider.shadow.set" data-showhide=".beforeafter_divider_shadow" data-showhidedep="true" value="on"><linebreak/>';
		_h += '			<div class="beforeafter_divider_shadow">';
		_h += '				<label_a>'+bricks.shadowblur+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.divider.shadow.blur" data-min="0" data-max="1000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.shadowstrength+'</label_a><input class="sliderinput valueduekeyboard  easyinit"  data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.divider.shadow.strength" data-min="0" data-max="1000" type="text"><linebreak/>';
		_h += '				<label_a>'+bricks.shadowcolor+'</label_a><input type="text" id="beforeafter_dividershadowcolor" data-editing="' + bricks.shadowcolor + '" class="my-color-field sliderinput easyinit" data-visible="true" data-r="addOns.'+slug+'.divider.shadow.color" value="#000000"><linebreak/>';
		_h += '			</div>'; // END OF BEFORE AFTER SHADOWS 
		
		_h += '		</div>'; // END OF DIVIDER SETTINGS

		// MISC SETTINGS
		_h += '		<div id="beforeafter_misc" class="ssm_content">'; 
		_h += '			<longoption><i class="material-icons">mouse</i><label_a>'+bricks.animonstg+'</label_a><input type="checkbox" class="sliderinput easyinit"  data-r="addOns.'+slug+'.onclick.set" data-showhide=".beforeafter_mouseclick" data-showhidedep="true" value="on"></longoption>';	
		_h += '			<div class="div10"></div>';
		_h += '			<div class="beforeafter_mouseclick">';
		_h += '				<label_a>'+bricks.duration+'</label_a><input class="sliderinput valueduekeyboard easyinit"  data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.onclick.time" data-min="0" data-max="50000" type="text"><linebreak/>';
		_h += '				<label_a>' + bricks.easing + '</label_a><select class="sliderinput tos2 nosearchbox easyinit easingSelect" data-r="addOns.'+slug+'.onclick.easing" data-theme="dark"></select><linebreak/>';
		_h += '				<label_a>' + bricks.mousecursor + '</label_a><select class="sliderinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.onclick.cursor" data-theme="dark">';
		_h += '				<option value="pointer">pointer</option>';
		_h += '				<option value="default">default</option>';
		_h += '				<option value="none">none</option>';
		_h += '				<option value="cell">cell</option>';
		_h += '				<option value="crosshair">crosshair</option>';
		_h += '				<option value="move" selected="">move</option>';
		_h += '				<option value="all-scroll">all-scroll</option>';
		_h += '				<option value="col-resize">col-resize</option>';
		_h += '				<option value="row-resize">row-resize</option>';
		_h += '				<option value="ew-resize">ew-resize</option>';
		_h += '				<option value="ns-resize">ns-resize</option>';
		_h += '				</select><linebreak/>';
		_h += '			</div>';						
		_h += '		</div>'; // END OF MISC SETTINGS
		_h += '	</div>'; // END OF BEFOREAFTER FORM WRAP


		addon.forms.slidergeneral.append($(_h));			
		addon.forms.slidergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});

		// easings init
		addon.forms.slidergeneral.find('.easingSelect').each(function() {
			RVS.F.createEaseOptions(this);
		});

		RVS.F.initOnOff();
		RVS.F.initTpColorBoxes(addon.forms.slidergeneral.find('.my-color-field'));

															
		updateIconPickers();
										
	}	

	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_beforeafter_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'beforeafter_addon'};
			$.extend(true, obj, revslider_beforeafter_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}
	
})( jQuery );