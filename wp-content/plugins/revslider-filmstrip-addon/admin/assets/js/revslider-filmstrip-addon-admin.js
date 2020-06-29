/***************************************************
 * REVOLUTION 6.0.0 FILMSTRIP ADDON
 * @version: 2.0 (31.08.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';	
	// TRANSLATABLE CONTENT
	var bricks = revslider_filmstrip_addon.bricks;	
	
	

	// ADDON CORE
	var addon = {};
	var slug = "revslider-filmstrip-addon";

	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
				
		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {			
			// INIT LISTENERS
			initListeners();

			// CREATE CONTAINERS				
			RVS.F.addOnContainer.create({slug: slug, icon:"local_movies", title:bricks.filmstrip, alias:bricks.filmstrip, slider:false, slide:true, layer:false});				
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug), 
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('#form_slide_'+slug),
								kbgeneral : $('#form_slidebg_kenburn')
						};				

			// INFO TO DISABLE FILM STRIP
			addon.forms.kbgeneral.append('<row id="fs_disable_kenburn" class="direktrow"><labelhalf><i class="material-icons">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.cantusekenburn+'</div></contenthalf></row>')
			addon.forms.disableKenburn = $('#fs_disable_kenburn');

			createSlideSettingsFields();
			initHelp();	
			addon.initialised = true;
		}

		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {									
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_slide_'+slug,{display:"inline-block"});
			RVS.DOC.trigger("FS_enabelDisableKenBurn");
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('filmstrip_addon');
			
		} else {
			// DISABLE THINGS
			//removeDrawnHand();			
			punchgs.TweenLite.set('#gst_slide_'+slug,{display:"none"});			
			$('#gst_slide_'+slug).removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");
			RVS.DOC.trigger("FS_enabelDisableKenBurn");
			
			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('filmstrip_addon');
			
		}				
	});

			
	// INITIALISE typewriter LISTENERS
	function initListeners() {	
		// UPDATE INPUT FIELDS WHEN SCREEN SELECTOR CHANGED
		RVS.DOC.on('screenSelectorChanged',function() {
			RVS.F.updateEasyInputs({container:$('#filmstrip_inner_settings'), path:RVS.S.slideId+".slide.", trigger:"init"});
		})				
		
		// UPDATE DUE BACKUP/RESTORE
		RVS.DOC.on('SceneUpdatedAfterRestore.filmstrip',function() {drawFilmStripSlides()});
		RVS.DOC.on('slideFocusChanged.filmstrip',function() {drawFilmStripSlides()});

		// NEW SLIDE OF FILMSTRIP HAS BEEN SELECTED
		RVS.DOC.on('click','.filmstrip_slide',function() { 
			$('.filmstrip_slide').removeClass("selected");
			this.className +=" selected";
			drawFilmStripSlideSettings($(this).index());
		});

		// ADD NEW FILMSTRIP SLIDE
		RVS.DOC.on('click','#add_filmstrip_image',function() {
			RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings.push(newFilmstripSetting());
			drawFilmStripSlides(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings.length-1);
		});


		//ENABLE DISABLE KEN BURN
		RVS.DOC.on('FS_enabelDisableKenBurn',function(a,b) {
			var onoffwrap = $('#sl_pz_onoff');
			if (RVS.SLIDER.settings.addOns[slug].enable && RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].enable) {
				RVS.SLIDER[RVS.S.slideId].slide.panzoom.set = false;
				onoffwrap[0].style.pointerEvents = "none";
				addon.forms.disableKenburn.show();
			} else {
				onoffwrap[0].style.pointerEvents = "";
				addon.forms.disableKenburn.hide();
			}
			RVS.F.updateEasyInputs({container:onoffwrap, path:RVS.S.slideId+".slide."});
			RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});

		});

		//MEDIA HSA BEEN CHANGED FOR 1 SLIDE
		RVS.DOC.on('filmstripmediaupdate',function(evt,si) {  if (si!==undefined)  setTimeout(function() { updateSlideURL(si); },50)});
		//MEDIA EXTERNAL URL HAS BEEN CHANGED TO CUSTOM
		RVS.DOC.on('filmstripExternalURLChanged',function(a,b) { if (b!==undefined && b.val!==undefined && b.eventparam!==undefined) updateSlideURL(b.eventparam);});

		//REMOVE SLIDE
		RVS.DOC.on('removeFSSlide',function(evt,param) {
			if (param!==undefined ) {
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings = $.map(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings,function(val, i) {return val});
				RVS.F.openBackupGroup({id:"filmstrip_delete",txt:bricks.deleteslide,icon:"delete",lastkey:"settings"});
				var oldval =  $.extend(true,{},RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings);
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings.splice(parseInt(param,0), 1);
				RVS.F.backup({	path:RVS.S.slideId+".slide.addOns."+slug+".settings",									
								icon:"delete",
								txt:bricks.deleteslide,
								lastkey:"settings",
								force:true,
								val:RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings,
								old:oldval,									
								bckpGrType:"filmstrip_delete"
							});		
				RVS.F.closeBackupGroup({id:"filmstrip_delete"});
				drawFilmStripSlides("none");
			}					
		})
	}

	// REORDER THE SLIDES AND CREATE BACKUP OF IT
	function rebuildSettingsArray(neworder,oldorder) {	
		if (neworder.toString()===oldorder.toString()) return;
		var oldval =  $.extend(true,{},RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings);
		
		RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings = new Array();			
		for (var i in neworder) RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings.push(oldval[neworder[i]]);							
		
		RVS.F.openBackupGroup({id:"filmstrip_reorder",txt:bricks.sortslide,icon:"sort",lastkey:"settings"});						
		RVS.F.backup({	path:RVS.S.slideId+".slide.addOns."+slug+".settings",									
						icon:"sort",
						txt:bricks.sortslide,
						lastkey:"settings",
						force:true,
						val:RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings,
						old:oldval,									
						bckpGrType:"filmstrip_reorder"
					});		
		RVS.F.closeBackupGroup({id:"filmstrip_reorder"});
	}


	// ADD NEW SETTINGS EMPTY OBJECT
	function newFilmstripSetting() {
		return {
			alt:"",
			custom:"",
			ids:"",
			size:"full",
			thumb:"",
			type:"",
			url:""
		}
	}

	// UPDATE CURRENT SLIDE OBJECT STRUCTURE
	function updateSlideStructure() {
		RVS.SLIDER[RVS.S.slideId].slide.addOns[slug] = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]==undefined ? {
			enable : false,
			direction : "right-to-left",
			mobile : false,
			times : RVS.F.cToResp({default:40}),
			settings : [newFilmstripSetting()]
		} : RVS.SLIDER[RVS.S.slideId].slide.addOns[slug];
	}


	// UPDATE URL OF FILMSTRIP SLIDE AND UPDATE THUMB IF NEEDED
	function updateSlideURL(si) {
		var _ = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings;
					
		// update urls based on size selection
		if(_[si].type === 'objectlibrary' || _[si].type==="objlib") {					
			var imgSize;					
			switch(_[si].size) {							
				case 'large':imgSize = '-75.jpg';break;						
				case 'medium':imgSize = '-50.jpg';break;						
				case 'small':imgSize = '-25.jpg';break;						
				case 'thumb':imgSize = '-10.jpg';break;						
				default:imgSize = '.jpg';						
			}					
			_[si].url = _[si].url.replace(/\-75|\-50|\-25|\-10|\.jpg/gi, '')+ imgSize;
			_[si].thumb = _[si].url.replace(/\-75|\-50|\-25|\-10|\.jpg/gi, '')+ '-10.jpg';
		} else 
			_[si].thumb = _[si].url;
								
		var fpi = document.getElementById("filmstrip_previewimage_"+si);					
		if (fpi!==undefined && fpi!==null) fpi.style.backgroundImage = "url("+_[si].thumb+")";
		
		drawFilmStripSlideSettings(si);
			
	}
				
	// CREATE INPUT FIELDS
	function createSlideSettingsFields() {			
		var _h = "";
		_h += '<div  class="form_inner_header"><i class="material-icons">local_movies</i>'+bricks.filmstrip+'</div>';
		_h += '<div id="filmstrip_inner_settings" class="collapsable" style="display:block !important">';			
		_h += '		<label_a>'+bricks.active+'</label_a><input type="checkbox" class="slideinput easyinit callEvent" data-evt="FS_enabelDisableKenBurn" data-r="addOns.'+slug+'.enable" data-showhide=".filmstrip_slide_settings" data-showhidedep="true" value="on">';
		_h += '		<div class="filmstrip_slide_settings">';
		_h += '			<label_a>'+bricks.movefrom+'</label_a><select class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.direction"><option value="right-to-left">'+bricks.rtl+'</option><option value="left-to-right">'+bricks.ltr+'</option><option value="top-to-bottom">'+bricks.ttb+'</option><option value="bottom-to-top">'+bricks.btt+'</option></select>';
		_h += '			<row>';
		_h += '				<onelong><label_icon class="ui_speed"></label_icon><input class="slideinput valueduekeyboard easyinit " data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.times.#size#.v" data-min="0" data-max="1000" type="text"></onelong>';
		_h += '				<oneshort><label_icon class="ui_hide_on_mobile"></label_icon><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.mobile" value="on"></oneshort>';
		_h += '			</row>';
		_h += '			<ul id="filmstrip_slide_images"></ul>';			
		_h += '			<div id="add_filmstrip_image" class="basic_action_button longbutton callEventButton rightbutton"><i class="material-icons">add</i>'+bricks.addnewslide+'</div>';
		_h += '			<div id="filmstrip_slide_image_settings"></div>'
		_h += '		</div>';
		_h += '</div>'

		addon.forms.slidegeneral.append($(_h));
		addon.forms.filmstripimages = $('#filmstrip_slide_images');
		addon.forms.filmstripimagesettings = $('#filmstrip_slide_image_settings');
		addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});
		RVS.F.initOnOff();
		RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger:"init"});
		drawFilmStripSlides();														
	}

	function getCurrentOrder() {
		var a = new Array();
		$('#filmstrip_slide_images .filmstrip_slide').each(function() { 
			if (this.dataset.oldindex!==undefined) a.push(this.dataset.oldindex);
		});
		return a;
	}

	// BUILD A NEW LIST AND DRAW OF FILM STRIP SLIDE IMAGES
	function drawFilmStripSlides(si) {			
		si = si===undefined ? 0 : si;
		updateSlideStructure();
		var _ = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings,
			_h = "";
		
		for (var i in _) {
			_h += '<li id="filmstrip_slide_'+i+'" data-oldindex="'+i+'" class="filmstrip_slide miniprevimage_wrap '+(i==0 || i=="0" ? "selected" : "")+'"><i class="material-icons">filter_hdr</i>';
			_h += '<div id="filmstrip_previewimage_'+i+'" class="filmstrip_previewimage" data-helpkey="film-strip-preview-image" style="background-image:url('+_[i].thumb+')"></div>';
			_h += '</li>';
		}
		//_h += '<li class="miniprevimage_wrap" id="add_filmstrip_image"><i class="material-icons">add_photo_alternate</i></li>';
							
		addon.forms.filmstripimages[0].innerHTML = _h;
					
		if (si!=="none")
			drawFilmStripSlideSettings(si);
		else
			addon.forms.filmstripimagesettings[0].innerHTML="";
		
		addon.forms.filmstripimages.sortable({				
			items: '.filmstrip_slide',
			start: function() {
				addon.beforesort = getCurrentOrder();					
			},
			stop: function(evt,ui) {					
				rebuildSettingsArray(getCurrentOrder(), addon.beforesort);					
			}				
		});
	}

	


	// DRAW THE FILM STRIP SETTING OF 1 SINGLE SLIDE ELEMENT
	function drawFilmStripSlideSettings(si) {
		if (RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings===null || RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings.length===0) {
			addon.forms.filmstripimagesettings[0].innerHTML="";
			return false;
		}
		var _ = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].settings,
		
		_h = '<div class="tp-clearfix"></div>';			
		_h += '<div class="div25"></div>';			
		_h += '<div class="miniprevimage_wrap"><i class="material-icons">filter_hdr</i>';
		_h += '<div id="filmstrip_previewimage_currentEditing" class="filmstrip_previewimage" data-helpkey="film-strip-preview-image" style="background-image:url('+_[si].thumb+')"></div>';
		_h += '</div>';
		_h += '<div class="miniimage_buttons_wrap">';				
		_h += '		<div data-r="#slide#.slide.addOns.'+slug+'.settings.'+si+'.url" data-rid="#slide#.slide.addOns.'+slug+'.settings.'+si+'.ids" data-rty="#slide#.slide.addOns.'+slug+'.settings.'+si+'.type" data-evt="filmstripmediaupdate" data-evtparam="'+si+'" class="getImageFromMediaLibrary basic_action_button longbutton "><i class="material-icons">folder</i>'+bricks.medialibrary+'</div>';
		_h += '		<div data-r="#slide#.slide.addOns.'+slug+'.settings.'+si+'.url" data-rid="#slide#.slide.addOns.'+slug+'.settings.'+si+'.ids" data-rty="#slide#.slide.addOns.'+slug+'.settings.'+si+'.type" data-evt="filmstripmediaupdate" data-evtparam="'+si+'" class="getImageFromObjectLibrary basic_action_button longbutton"><i class="material-icons">camera_enhance</i>'+bricks.objectlibrary+'</div>';
		_h += '</div>';
		_h += '<label_a>'+bricks.url+'</label_a><input id="filmstrip_imageurl_currentEditing" class="slideinput easyinit callEvent" data-evt="filmstripExternalURLChanged" data-evtparam="'+si+'" data-r="addOns.'+slug+'.settings.'+si+'.url" value="'+_[si].url+'" type="text">';
		_h += '<label_a>'+bricks.ssize+'</label_a><select id="filmstrip_source_size" class="slideinput tos2 nosearchbox easyinit callEvent" data-evt="filmstripExternalURLChanged" data-evtparam="'+si+'"data-r="addOns.'+slug+'.settings.'+si+'.size">';
						
		if (_[si].type!=="objectlibrary" && _[si].type!=="objlib")
			for (var j in RVS.ENV.img_sizes) _h += '<option value="'+j+'">'+RVS.ENV.img_sizes[j]+'</option>';
		else
			_h += '<option value="original">'+bricks.original+'</option><option value="large">'+bricks.original+'</option><option value="medium">'+bricks.medium+'</option><option value="small">'+bricks.small+'</option><option value="thumb">'+bricks.thumb+'</option>';
		_h += '</select>';
		_h += '<label_a>'+bricks.alttext+'</label_a><select id="filmstrip_alt_text" data-theme="dark" data-show="#_*val*_filmstrip_alt"  data-hide=".filmstrip_alt_wrap" class="slideinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.settings.'+si+'.alt">';				
		_h += '<option value="media_library">'+bricks.medialibrary+'</option>';
		_h += '<option value="file_name">'+bricks.filename+'</option>';
		_h += '<option value="custom">'+bricks.custom+'</option>';				
		_h += '</select>';
		_h += '<div id="_custom_filmstrip_alt" class="filmstrip_alt_wrap"><label_a>'+bricks.custom+'</label_a><input id="filmstrip_imagealt_currentEditing" class="slideinput easyinit"  data-r="addOns.'+slug+'.settings.'+si+'.custom" value="'+_[si].custom+'" type="text"></div>';			
		_h += '<div data-evt="removeFSSlide" data-evtparam="'+si+'" class="basic_action_button longbutton callEventButton rightbutton"><i class="material-icons">delete</i>'+bricks.deleteslide+'</div>';
		_h += '<div class="tp-clearfix"></div>';
		
		addon.forms.filmstripimagesettings[0].innerHTML = _h;
		addon.forms.filmstripimagesettings.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:bricks.placeholder
		});
		RVS.F.updateEasyInputs({container:addon.forms.filmstripimagesettings, path:RVS.S.slideId+".slide.", trigger:"init"});
		
	}

	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_filmstrip_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'filmstrip_addon'};
			$.extend(true, obj, revslider_filmstrip_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}

})( jQuery );