/***************************************************
 * REVOLUTION 6.0.0 BUBBLEMORTH ADDON
 * @version: 2.0 (11.07.2018)
 * @author ThemePunch
***************************************************/

(function($) {
		
		var addon = {},
			slug = 'revslider-bubblemorph-addon',
			bricks = revslider_bubblemorph_addon.bricks;
					
		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {			
			
			if(!addon.initialised) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:'bubble_chart', title:bricks.bubblemorph, alias:bricks.bubblemorph, layer:true});				
				
				// PICK THE CONTAINERS WE NEED	
				addon.forms = {
					
					layergeneral: $('#form_layerinner_' + slug),
					layericon: $('#gst_layer_' + slug),
					layer: $('#form_layer_' + slug),
					
				};
				
				// ADD ADDON HTML
				createLayerSettingsFields();
				
				// INIT
				addEvents();
				initInputs();	
				initHelp();	
				extendLayerTypes();
				addon.initialised = true;
				
			}

			// UDPATE FIELDS ID ENABLE
			if(RVS.SLIDER.settings.addOns[slug].enable) {
				
				//Show Hide Areas
				punchgs.TweenLite.set(addon.forms.layericon,{display: 'inline-block'});
				$('body').removeClass('bubblemorph-disabled');
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('bubblemorph_addon');
				
			} 
			else {
				
				if(!addon.initialised) {
				
					// DISABLE THINGS		
					punchgs.TweenLite.set(addon.forms.layericon,{display: 'none'});			
					$(addon.forms.layericon).removeClass('selected');	
					addon.forms.layer.addClass('collapsed');
					$('body').addClass('bubblemorph-disabled');
					
				}
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('bubblemorph_addon');
				
			}	
			
		});
		
		//EXTEND LAYER TYPES
		function extendLayerTypes() {
			RVS.S.layerIcons = jQuery.extend(true, RVS.S.layerIcons, {
				bubblemorph: "bubble_chart"
			});
			
			RVS.F.extendLayerTypes({ 
			
				icon: 'bubble_chart',
				type: 'shape', 
				subtype: 'bubblemorph',
				alias: 'BubbleMorph',
				extension: { 
					addOns: {
						'revslider-bubblemorph-addon' : {
							settings: {
								maxmorphs: {d: {v: 6}, m: {v: 6}, n: {v: 6}, t: {v: 6}},
								speedx: {d: {v: 0.25}, m: {v: 0.25}, n: {v: 0.25}, t: {v: 0.25}},
								speedy: {d: {v: 1}, m: {v: 1}, n: {v: 1}, t: {v: 1}},
								bufferx: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}},
								buffery: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}}
							},
							shadow: {
								strength: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}},
								color: {d: {v: 'rgba(0, 0, 0, 0.35)'}, m: {v: 'rgba(0, 0, 0, 0.35)'}, n: {v: 'rgba(0, 0, 0, 0.35)'}, t: {v: 'rgba(0, 0, 0, 0.35)'}},
								offsetx: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}},
								offsety: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}}
							},
							border: {
								size: {d: {v: 0}, m: {v: 0}, n: {v: 0}, t: {v: 0}},
								color: {d: {v: '#000000'}, m: {v: '#000000'}, n: {v: '#000000'}, t: {v: '#000000'}}
							}
						}
					},
					runtime: {internalClass: 'tp-shape tp-shapewrapper tp-bubblemorph'}							
				}	
					
			});
			
		}
					
		// CREATE INPUT FIELDS
		function createLayerSettingsFields() {
			
			var _h;						
			_h = '<div class="form_inner_header"><i class="material-icons">bubble_chart</i>'+bricks.settings+'</div>';
			_h += '<div class="collapsable" style="display:block !important; padding: 0">';
			_h += ' 	<div style="padding: 20px">';
			_h += '			<label_a>'+bricks.maxmorphs+'</label_a';
			_h += ' 		><input type="text" class="layerinput easyinit" data-allowed="" data-numeric="true" data-r="addOns.'+slug+'.settings.maxmorphs.#size#.v">';
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_x_start"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="" data-numeric="true" data-r="addOns.'+slug+'.settings.speedx.#size#.v"></onelong>';
			_h += '				<oneshort><label_icon class="ui_y_start"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="" data-numeric="true" data-r="addOns.'+slug+'.settings.speedy.#size#.v"><oneshort>';
			_h += '			</row>';
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_x"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.settings.bufferx.#size#.v"></onelong>';
			_h += '				<oneshort><label_icon class="ui_y"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.settings.buffery.#size#.v"><oneshort>';
			_h += ' 		</row>';
			_h += ' 	</div>';
			_h += ' 	<div id="bubblemorph_shadow" class="form_inner_header"><i class="material-icons">flip_to_back</i>'+bricks.shadow+'</div>';
			_h += ' 	<div style="padding: 20px">';
			_h += '			<label_a>'+bricks.strength+'</label_a';
			_h += ' 		><input type="text" class="layerinput easyinit" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.shadow.strength.#size#.v">';
			_h += ' 		<span class="linebreak"></span>';
			_h += '			<label_a>' + bricks.color + '</label_a';
			_h += '     	><input type="text" id="bubblemorph_shadow_color" data-mode="single" data-editing="' + bricks.color + '" class="my-color-field layerinput easyinit" data-visible="true" data-r="addOns.'+slug+'.shadow.color.#size#.v" value="rgba(0, 0, 0, 0.35)">';
			_h += ' 		<div class="div5"></div>';	
			_h += '			<row class="direktrow">';
			_h += '				<onelong><label_icon class="ui_x"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.shadow.offsetx.#size#.v"></onelong>';
			_h += '				<oneshort><label_icon class="ui_y"></label_icon><input type="text" class="layerinput easyinit shortfield" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.shadow.offsety.#size#.v"><oneshort>';
			_h += '			</row>';
			_h += ' 	</div>';
			_h += ' 	<div id="bubblemorph_border" class="form_inner_header"><i class="material-icons">border_outer</i>'+bricks.border+'</div>';
			_h += ' 	<div style="padding: 20px">';
			_h += '			<label_a>'+bricks.size+'</label_a';
			_h += ' 		><input type="text" class="layerinput easyinit" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.border.size.#size#.v">';
			_h += ' 		<span class="linebreak"></span>';
			_h += '			<label_a>' + bricks.color + '</label_a';
			_h += '     	><input type="text" id="bubblemorph_border_color" data-mode="single" data-editing="' + bricks.color + '" class="my-color-field layerinput easyinit" data-visible="true" data-r="addOns.'+slug+'.border.color.#size#.v" value="#000000">';
			_h += ' 	</div>';
			_h += '</div>';
			
			// append settings markup
			addon.forms.layergeneral.append(_h);
			
		}
		
		function initInputs() {			
			
			// init select2RS
			addon.forms.layergeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:bricks.placeholder_select
			});
			
			// colorPicker init
			RVS.F.initTpColorBoxes(addon.forms.layergeneral.find('.my-color-field'));	
			
			// on/off init
			RVS.F.initOnOff(addon.forms.layergeneral);
			delete addon.forms.layergeneral;
			
		}
		
		var events = {

			layerSelected: function() {
				
				var allAddOn,
					len = RVS.selLayers.length;
					
				if(len) {
					
					allAddOn = true;
					for(var i = 0; i < len; i++) {
						
						if(RVS.L[RVS.selLayers[i]].subtype !== 'bubblemorph') {
							
							allAddOn = false;
							break;
							
						}
						
					}
					
				}
				
				if(allAddOn) {
					
					addon.forms.layericon.css('display', 'inline-block');
					addon.forms.layer.css('visibility', 'visible');
					
				} else {
					
					addon.forms.layericon.css('display', 'none');
					addon.forms.layer.css('visibility', 'hidden');
					
				}
				
			}
			
		};
		
		function addEvents() {
			
			RVS.DOC.on('selectLayersDone.bubblemorph', events.layerSelected);
			
		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(revslider_bubblemorph_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
			
				var obj = {slug: 'bubblemorph_addon'};
				$.extend(true, obj, revslider_bubblemorph_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}


})(jQuery);