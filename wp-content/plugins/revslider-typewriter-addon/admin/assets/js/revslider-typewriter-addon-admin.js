/***************************************************
 * REVOLUTION 6.0.0 typewriter ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/
(function( $ ) {
	//'use strict';
	
	

	// TRANSLATABLE CONTENT
	var bricks = revslider_typewriter_addon.bricks;
	
	// ALL DEFAULTS
	var defaults = {	
	
		enable : false,				
		delays :  '',
		speed :  '30',
		cursor_type :  'one',
		start_delay :  '1000',
		newline_delay :  '1000',
		blinking_speed :  '500',
		deletion_speed :  '20',
		linebreak_delay :  '60',
		deletion_delay :  '1000',
		looped :  false,
		blinking :  false,
		sequenced :  false,
		word_delay :  false,
		hide_cursor :  false,
		lines : ''
	};

	// ADDON CORE
	var addon = {};
	var slug = "revslider-typewriter-addon";

	// INITIALISE THE ADDON
	RVS.DOC.on(slug+'_init',function() {	
		
		// FIRST TIME INITIALISED
		if (!addon.initialised && RVS.SLIDER.settings.addOns[slug].enable) {

			// CREATE CONTAINERS
			RVS.F.addOnContainer.create({slug: slug, icon:"format_italic", title:bricks.typewriter, alias:bricks.typewriter, slider:true, slide:true, layer:true});
			
			// PICK THE CONTAINERS WE NEED			
			addon.forms = { 	slidergeneral : $('#form_slidergeneral_'+slug), 
								slidegeneral : $('#form_slidegeneral_'+slug), 
								layergeneral : $('#form_layerinner_'+slug),
								module : $('#form_module_'+slug),
								layer : $('#form_layer_'+slug),
								slide : $('#form_slide_'+slug)
						};	

			//CHECK STRUCTURE ON SLIDER SETTNIGS
			updateSliderObjectsStructure();
			for (var i in RVS.selLayers) {
				if(RVS.selLayers.hasOwnProperty(i)) {
					updateLayerObjectStructure({layerid:RVS.selLayers[i]});
				}
			}
			createLayerSettingsFields();
			initListeners();
			initHelp();						
			addon.initialised = true;
			
		}

		// UDPATE FIELDS ID ENABLE
		if (RVS.SLIDER.settings.addOns[slug].enable) {				
			//Update Input Fields in Slider Settings
			
			RVS.F.updateEasyInputs({container:addon.forms.layergeneral, trigger:"init"});
			
			//Show Hide Areas
			punchgs.TweenLite.set('#gst_layer_'+slug,{display:"inline-block"});
			
			// show help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.activate('typewriter_addon');
			
		} else {
			// DISABLE THINGS
			//removeDrawnHand();			
			punchgs.TweenLite.set('#gst_layer_'+slug,{display:"none"});			
			$('#gst_layer_typewriter').removeClass("selected");	
			addon.forms.module.addClass("collapsed");
			addon.forms.layer.addClass("collapsed");
			addon.forms.slide.addClass("collapsed");

			// hide help definitions
			if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('typewriter_addon');
			
		}				
	});

	//BUILD FORMS

	
	// UPDATE THE REVBUILDER SLIDER SETTINGS ADDONS OBJECT
	function updateSliderObjectsStructure() {
		RVS.SLIDER.settings.addOns[slug] = RVS.SLIDER.settings.addOns[slug]!==undefined ? RVS.SLIDER.settings.addOns[slug] : { enable : true};
	}
	
	function updateDefaults(_) {
		
		var allDefaults = $.extend(true, {}, defaults);
		_ = $.extend(true, allDefaults, _);
		return _;
		
	}

	// UPDATE THE LAYER OBJEXT STRUCTURE (EXTEND WITH THE ATTRIBUTES WE NEED)
	function updateLayerObjectStructure(_) {

		if(RVS.L[_.layerid].addOns[slug]!=undefined) {
			var allDefaults = $.extend(true, {}, defaults);
			RVS.L[_.layerid].addOns[slug] = $.extend(true, allDefaults, RVS.L[_.layerid].addOns[slug]);
		}
		else {
			RVS.L[_.layerid].addOns[slug] = $.extend(true, {}, defaults);	
		}
				
	}

	
	// INITIALISE typewriter LISTENERS
	function initListeners() {
		
		// LAYER ENABLED FOR typewriter
		RVS.DOC.on('selectLayersDone.beforeafter',function() {
		
			for (var i in RVS.selLayers) {
				if(RVS.selLayers.hasOwnProperty(i)) {
					updateLayerObjectStructure({layerid:RVS.selLayers[i]});
				}
			}			
			RVS.F.updateEasyInputs({container:addon.forms.layergeneral, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
			
		});

			
		// EDITOR VIEW CHANGED, WE NEED TO MAYBE DRAW HAND AND STAFF
		RVS.DOC.on('editorViewModeChange.typewriter',function() {
			
		});

		// LAYER HAS BEEN SELECTED, WORD PATTERN NEED TO BE CREATED
		RVS.DOC.on('selectLayersDone.typewriter',function() {
			if (RVS.selLayers.length!==1) return;			
			if (RVS.L[RVS.selLayers[0]].addOns[slug]!==undefined && RVS.L[RVS.selLayers[0]].addOns[slug].enable) {
				readDelays(RVS.L[RVS.selLayers[0]].addOns[slug]);
				readLines(RVS.L[RVS.selLayers[0]].addOns[slug]);
			} else {				
				document.getElementById('ta_layertext_extension_typewriter').innerHTML = "";
			}
		});

		// MULTIPLE LINES CHANGE LISTENER
		RVS.DOC.on('TypeWriterMultpileLinesChange',function() {
			if (RVS.selLayers.length!==1) return;			
			if (RVS.L[RVS.selLayers[0]].addOns[slug]!==undefined && RVS.L[RVS.selLayers[0]].addOns[slug].enable) 				
				readLines(RVS.L[RVS.selLayers[0]].addOns[slug]);	
			else {				
				document.getElementById('ta_layertext_extension_typewriter').innerHTML = "";
			}
		});
		
		// REDRAW LAYER WORD PATTERNS AND LINES AFTER RESTORE
		RVS.DOC.on('SceneUpdatedAfterRestore.typewriter',function() {
			RVS.DOC.trigger("TypeWriterWordPatternUpdate").trigger("TypeWriterLinesUpdate");
		});

		// ADD A NEW WORD PATTERN
		RVS.DOC.on('add_tw_word_pattern',function() {			
			document.getElementById('typewriter_word_pattern_wrap').innerHTML += addWordDelay(1, 250);
			RVS.DOC.trigger("TypeWriterWordPatternUpdate");
		});
		// DELETE A WORD PATTERN
		RVS.DOC.on('click','.delete_tw_word_pattern',function() {
			$(this).closest('row.directrow').remove();
			RVS.DOC.trigger("TypeWriterWordPatternUpdate");
		});

		// UPDATE OBJECT ATTRIBUTE WITH CURRENT SETTINGS
		RVS.DOC.on('TypeWriterWordPatternUpdate',function(e,p) {
			var a = addon.forms.patternContainer[0].getElementsByClassName("tw_word_pattern_a"),
				b = addon.forms.patternContainer[0].getElementsByClassName("tw_word_pattern_b"),
				words = [];		
			for (var i in a) {
				if(a.hasOwnProperty(i)) {
					if (a[i].value!==undefined && b[i].value!==undefined) words.push(escape(a[i].value+"|"+b[i].value));
				}
			}
			RVS.F.updateLayerObj({path:"addOns."+slug+".delays",val:words.join()});		
		});

		// ADD A NEW TYPEWRITER LINE (MULTILINE)
		RVS.DOC.on('click','.add_tw_lines',function() {			
			$(addLinesTextArea("")).insertAfter($(this).closest('.tw_textarea_wrap'));			
		});

		// REMOVE A TYPEWRITER LINE (MULTILINE)
		RVS.DOC.on('click','.remove_tw_lines',function() {
			if ($('.tw_textarea_wrap').length>1) {
				$(this).closest('.tw_textarea_wrap').remove();
				RVS.DOC.trigger("TypeWriterLinesUpdate");
			}
		});
		
		//UPDATE OBJECT ATTRIBUTE LINES WITH CURRENT SETTINGS
		RVS.DOC.on('TypeWriterLinesUpdate',function(e,p) {
			var a = document.getElementById('ta_layertext_extension_typewriter').getElementsByClassName('typewriterline'),
				lines = [];
			for (var i in a) {
				if(a.hasOwnProperty(i)) {
					if (a[i].value!==undefined) lines.push(escape(a[i].value));
				}
			}
			RVS.F.updateLayerObj({path:"addOns."+slug+".lines",val:lines.join()});	
		});

		
	}

	function addWordDelay(a,b) {
		var _ ='<row class="directrow">';
		_ += '<oneabsolute><div class="autosize basic_action_button onlyicon delete_tw_word_pattern"><i class="material-icons">delete</i></div></oneabsolute>';
		_ += '<onelong><label_icon class="ui_letterspacing"></label_icon><input class="tw_word_pattern_a layerinput valueduekeyboard smallinput callEvent" data-helpkey="typewriter-word-delay-a" data-updateviaevt="true" data-evt="TypeWriterWordPatternUpdate" data-evtparam="word" data-numeric="true" data-allowed="" data-min="0" data-max="999" type="text" value="'+a+'"></onelong>';
		_ += '<oneshort><label_icon class="ui_duration"></label_icon><input class="tw_word_pattern_b layerinput valueduekeyboard smallinput callEvent" data-helpkey="typewriter-word-delay-b" data-updateviaevt="true" data-evt="TypeWriterWordPatternUpdate" data-evtparam="delay" data-numeric="true" data-allowed="" data-min="0" data-max="999" type="text" value="'+b+'"></oneshort>';
		_ += '</row>';
		return _;
	}

	function addLinesTextArea(a) {
		var _ = '<div class="tw_textarea_wrap">';
		_ += '<textarea placeholder="'+bricks.entertext+'" class="rsmaxtextarea layerinput callEvent typewriterline" data-evt="TypeWriterLinesUpdate" data-updateviaevt="true">'+a+'</textarea>';
		_ += '<div class="add_tw_lines autosize basic_action_button onlyicon"><i class="material-icons">add</i></div>';
		_ += '<div class="remove_tw_lines autosize basic_action_button onlyicon"><i class="material-icons">delete</i></div>';
		_ += '</div>';
		return _;
	}

	function readLines(_) {			
		if(!_ || !_.lines) _ = updateDefaults(_ || {});
		
		var lines = _.lines.split(','),
			len = lines.length,			
			res = "";
		if (_.sequenced) { 
			if (len && lines[0]) for (var i = 0; i<len; i++) res += addLinesTextArea(unescape(lines[i]));				
			res = res==="" ? addLinesTextArea("") : res;
		}		
		document.getElementById('ta_layertext_extension_typewriter').innerHTML = res;
	}

	function readDelays(_) {
		
		if(!_ || !_.delays) _ = updateDefaults(_ || {});
		
		var words = _.delays.split(','),
			len   = words.length,
			val,
			res = "";
				
		if(len && words[0])
			for(var i = 0; i < len; i++) {				
				val = unescape(words[i]).split('|');
				res += addWordDelay(val[0], val[1]);				
			}			
		document.getElementById('typewriter_word_pattern_wrap').innerHTML = res;		
	}
	

	// CREATE THE BASIC INPUT FIELDS FOR THE ADD ON
	function createLayerSettingsFields() {
		
		$('#ta_layertext_extension').append('<div id="ta_layertext_extension_typewriter"></div>');
		var _h;			
			
		_h = '<div class="form_inner_header"><i class="material-icons">format_italic</i>'+bricks.twgeneral+'</div><div  class="collapsable" style="display:block !important">'; 
		_h += '	<longoption><label_a>'+bricks.active+'</label_a><input type="checkbox" class="layerinput easyinit"  data-r="addOns.'+slug+'.enable" data-showhide=".typewriter_layer_form" data-showhidedep="true" value="on"></longoption>';
		_h +='	<div class="typewriter_layer_form">';
		_h += '		<longoption><label_a>'+bricks.blinking+'</label_a><input type="checkbox" class="layerinput easyinit typewriter_layer_form" data-r="addOns.'+slug+'.blinking" data-showhide=".typewriter_layer_blinking_form" data-showhidedep="true" value="on"></longoption>';
		_h += '		<longoption><label_a>'+bricks.multiplelines+'</label_a><input type="checkbox" class="layerinput easyinit typewriter_layer_form callEvent" data-evt="TypeWriterMultpileLinesChange" data-r="addOns.'+slug+'.sequenced" data-showhide=".typewriter_layer_sequenced_form" data-showhidedep="true" value="on"></longoption>';
		_h += '		<longoption><label_a>'+bricks.worddelays+'</label_a><input type="checkbox" class="layerinput easyinit typewriter_layer_form" data-r="addOns.'+slug+'.word_delay" data-showhide=".typewriter_layer_worddelay_form" data-showhidedep="true" value="on"></longoption>';	
		_h += '	</div>';
		_h += '</div>';
		
		_h +='<div class="typewriter_layer_form">';
		_h +='	<div class="typewriter_layer_blinking_form">';
		_h += '		<div class="form_inner_header"><i class="material-icons">minimize</i>'+bricks.blinkeffect+'</div><div  class="collapsable" style="display:block !important">'; 				
		_h += '			<longoption><i class="material-icons">not_interested</i><label_a>'+bricks.blinkinghide+'</label_a><input type="checkbox" class="layerinput easyinit typewriter_layer_form" data-r="addOns.'+slug+'.hide_cursor" value="on"></longoption>';
		_h += '			<longoption><i class="material-icons">timer</i><label_a>'+bricks.blinkspeed+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.blinking_speed" data-min="0" data-max="9999" type="text"></longoption>';
		_h += '			<label_a>'+bricks.cursor+'</label_a><select class="layerinput tos2 nosearchbox easyinit" data-r="addOns.'+slug+'.cursor_type"><option value="one">_</option><option value="two">|</option></select>';
		_h += '		</div>';
		_h += '	</div>';		
		_h +='	<div id="typewriter_behavior" class="form_inner_header"><i class="material-icons">skip_next</i>'+bricks.twtyping+'</div><div  class="collapsable" style="display:block !important">'; 
		_h += '		<longoption><i class="material-icons">timer</i><label_a>'+bricks.typespeed+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.speed" data-min="0" data-max="9999" type="text"></longoption>';
		_h += '		<longoption><i class="material-icons">access_time</i><label_a>'+bricks.offsetdelay+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.start_delay" data-min="0" data-max="99999" type="text"></longoption>';
		_h += '		<longoption><i class="material-icons">timelapse</i><label_a>'+bricks.lbdelay+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.linebreak_delay" data-min="0" data-max="99999" type="text"></longoption>';
		_h += '	</div>';
		_h +='	<div class="typewriter_layer_sequenced_form">';
		_h += '		<div class="form_inner_header"><i class="material-icons">skip_previous</i>'+bricks.multilinebe+'</div><div  class="collapsable" style="display:block !important">'; 				
		_h += '			<longoption><i class="material-icons">timer</i><label_a>'+bricks.deletionspeed+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.deletion_speed" data-min="0" data-max="9999" type="text"></longoption>';
		_h += '			<longoption><i class="material-icons">access_time</i><label_a>'+bricks.deletiondelay+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.deletion_delay" data-min="0" data-max="9999" type="text"></longoption>';
		_h += '			<longoption><i class="material-icons">access_time</i><label_a>'+bricks.newlinedelay+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.newline_delay" data-min="0" data-max="9999" type="text"></longoption>';
		_h += '			<longoption><i class="material-icons">repeat_one</i><label_a>'+bricks.loop+'</label_a><input type="checkbox" class="layerinput easyinit typewriter_layer_form" data-r="addOns.'+slug+'.looped" value="off"></longoption>';	
		_h += '		</div>';
		_h += '	</div>';
		_h +='	<div class="typewriter_layer_worddelay_form">';
		_h += '		<div class="form_inner_header"><i class="material-icons">space_bar</i>'+bricks.wordpattern+'</div><div  class="collapsable" style="display:block !important">'; 
		_h += '			<longoption style="display:none"><label_a>Pattern</label_a><input class="layerinput valueduekeyboard smallinput easyinit" data-r="addOns.'+slug+'.delays" type="text"></longoption>';
		_h += ' 		<div id="typewriter_word_pattern_wrap"></div>';
		_h += '			<div class="div15"></div>';
		_h += ' 		<div class="autosize rightbutton basic_action_button callEventButton" data-evt="add_tw_word_pattern" data-helpkey="typewriter-word-delay"><i class="material-icons">add</i>'+bricks.worddelaypattern+'</div>';
		_h += '			<div class="tp-clearfix"></div><div class="div25"></div>';
		_h += '		</div>';
		_h += '	</div>';
		
		_h += '</div>';

		addon.forms.layergeneral.append($(_h));
		addon.forms.patternContainer = addon.forms.layergeneral.find('#typewriter_word_pattern_wrap');
		addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
			minimumResultsForSearch:"Infinity",
			placeholder:"Select From List"
		});
		RVS.F.initOnOff();
	}


	// CREATE INPUT FIELDS
	/*
	function createSliderSettingsFields() {		
	}
	*/
	
	function initHelp() {
		
		// only add on-demand if the AddOn plugin is activated from inside the editor
		// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
		if(revslider_typewriter_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
		
			var obj = {slug: 'typewriter_addon'};
			$.extend(true, obj, revslider_typewriter_addon.help);
			HelpGuide.add(obj);
			
		}
	
	}

})( jQuery );