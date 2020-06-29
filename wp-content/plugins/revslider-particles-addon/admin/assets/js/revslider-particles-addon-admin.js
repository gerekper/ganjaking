/***************************************************
 * REVOLUTION 6.1.3 PARTICLE EFFECTS ADDON
 * @version: 2.1 (23.09.2019)
 * @author ThemePunch
***************************************************/
(function($) {
	//'use strict';

		// TRANSLATABLE CONTENT
		var addon = {},
			slug = "revslider-particles-addon",
			bricks = revslider_particles_addon.bricks;
			
		

		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {	
			
			addon.isActive = RVS.SLIDER.settings.addOns[slug].enable;
			if(addon.isActive) migrateParticles(RVS.SLIDER.settings.addOns[slug]);		
			
			var init = !addon.initialised;
			if(init && addon.isActive) {

				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:"blur_on", title:bricks.particles, alias:bricks.particles, slide:true});				
				
				// PICK THE CONTAINERS WE NEED			
				addon.forms = {slidegeneral : $('#form_slidegeneral_'+slug)};	
				
				createSlideSettingsFields();
				addon.forms.bubbles = $('#particles_bubble_mode');
				addon.forms.grab = $('#particles_grab_mode');
				addon.forms.repulse = $('#particles_repulse_mode');
				addon.forms.svgs = $('#particles_shape');
				
				addEvents();
				initInputs();			
				events.updateDisplay();
				initHelp();
				addon.initialised = true;
				
			}

			// UDPATE FIELDS ID ENABLE
			if(addon.isActive) {				

				//Update Input Fields in Slider Settings
				if(RVS.S.slideId.toString().search('static') === -1) {
					RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.'});
				}
				
				//Show Hide Areas
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:'inline-block'});
				
				// show help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.activate('particles_addon'); 
				
			} else if(!init) {
				
				// DISABLE THINGS			
				punchgs.TweenLite.set('#gst_slide_'+slug,{display:'none'});			
				$('#gst_slide_'+slug).removeClass('selected');	
				
				// hide help definitions
				if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('particles_addon'); 
				
			}	
			
		});
		
		function getDefaults() {
			
			return { 
			
				particles: {
					shape: 'circle',
					number: 80,
					size: 6,
					sizeMin: 1,
					random: true
				},
				styles: {
					border: {
						enable: false,
						color: '#ffffff',
						opacity: 100,
						size: 1
					},
					lines: {
						enable: false,
						color: '#ffffff',
						width: 1,
						opacity: 100,
						distance: 150
					},
					particle: {
						color: '#ffffff',
						opacity: 100,
						opacityMin: 25,
						opacityRandom: false,
						zIndex: 'default'
					}
				},
				movement: {
					enable: true,
					randomSpeed: true,
					speed: 1,
					speedMin: 1,
					direction: 'none',
					straight: true,
					bounce: false
				},
				interactivity: {
					hoverMode: 'none',
					clickMode: 'none'
				},
				bubble: {
					distance: 400,
					size: 40,
					opacity: 40
				},
				grab: {
					distance: 400,
					opacity: 50
				},
				repulse: {
					distance: 200,
					easing: 100
				},
				pulse: {
					size: {
						enable: false,
						speed: 40,
						min: 1,
						sync: false
					},
					opacity: {
						enable: false,
						speed: 3,
						min: 1,
						sync: false
					}
				}
				
			};
		}
		
		function migrateParticles(_) {
			
			var startSlide,
				endSlide,
				toMigrate,
				shape;
			
			// slider structure already exists
			if(_.particles) {
				
				toMigrate = true;
				startSlide = _.startSlide;
				endSlide = _.endSlide;
				addon.options = $.extend(true, {}, _);
				
				delete _.startSlide;
				delete _.endSlide;
				delete _.hideOnMobile;
				delete _.particles;
				delete _.styles;
				delete _.movement;
				delete _.interactivity;
				delete _.pulse;
				delete _.bubble;
				delete _.grab;
				delete _.repulse;
				
			}
			// no need to migrate
			else {
				
				addon.options = getDefaults();
				
			}
			
			slideDefaults(startSlide, endSlide);
			
		}
		
		// write default data
		function checkSlideDefaults(_, defaults) {
			
			return _===undefined || _.particles===undefined ? !defaults ? $.extend(true, {}, addon.options) : getDefaults() : _;
			
		}

		//Migrate Datas
		function slideDefaults(startSlide, endSlide) {

			var ids = RVS.SLIDER.slideIDs,
				len = ids.length;
			
			for(var id in ids) {
				
				if(!ids.hasOwnProperty(id)) continue;
				var slideId = ids[id];
				
				// skip writing to static slide
				if(slideId.toString().search('static') !== -1) continue;
					
				RVS.SLIDER[slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[slideId].slide.addOns[slug]);
				
			}
			
		}
		
		// old custom presets could have the old structure
		function sanitizePreset(_) {
			
			if(typeof _==="string") return JSON.parse(_);

			var newversion = _.particles!==undefined || _.movement!==undefined ? true : false;

			
			var shape = _d(newversion ? _.particles.shape :_.particles_shape_type,"circle"),
				size = _d(newversion ? _.particles.size : _.particles_size_value, 6);
				
			if(shape === 'triangle' || shape === 'edge' || shape === 'polygon') {
				size = Math.round(parseInt(size, 10) * 1.5);
			}
			else if(shape === 'star') {
				size = parseInt(size, 10) * 2;
			}
			
			return { 
			
				enable : true,
				hideOnMobile : _d(_truefalse(newversion ? _.hideOnMObile : _.particles_hide_on_mobile), false),
				particles:{
					shape: shape,
					number:_d(_.particles_number_value,80),
					size: size,
					sizeMin:_d(newversion ? _.particles.sizeMin :_.particles_size_min_value,1),
					random:_d(_truefalse(newversion ? _.particles.random :_.particles_size_random), true)				
				},
				styles:{
					border:{
						enable:_d(_truefalse(newversion ? _.styles.border.enable : _.particles_border_enable), false),
						color:_d(newversion ? _.styles.border.color :_.particles_border_color,'#ffffff'),
						opacity:_d(newversion ? _.styles.border.opacity :_.particles_border_opacity,100),
						size:_d(newversion ? _.styles.border.size :_.particles_border_size,1)
					},
					lines:{
						enable:_d(_truefalse(newversion ? _.styles.lines.enable :_.particles_line_enable), false),
						color:_d(newversion ? _.styles.lines.color :_.particles_line_color,'#ffffff'),
						width:_d(newversion ? _.styles.lines.width :_.particles_line_width,1),
						opacity:_d(newversion ? _.styles.lines.opacity :_.particles_line_opacity,100),
						distance:_d(newversion ? _.styles.lines.distance :_.particles_line_distance,150),
					},
					particle:{
						color:_d(newversion ? _.styles.particle.color : _.particles_color_value,'#ffffff'),
						opacity:_d(newversion ? _.styles.particle.opacity : _.particles_opacity_value,100),
						opacityMin:_d(newversion ? _.styles.particle.opacityMin : _.particles_opacity_min_value,10),
						opacityRandom:_d(_truefalse(newversion ? _.styles.particle.opacityRandom : _.particles_opacity_random), false),
						zIndex:_d(newversion ? _.styles.particle.zIndex : _.particles_zIndex,'default')
					}
				},
				movement:{
					enable:_d(_truefalse(newversion ? _.movement.enable :  _.particles_move_enable), true),
					randomSpeed:_d(_truefalse(newversion ? _.movement.randomSpeed :_.particles_move_random), true),					
					speed:_d(newversion ? _.movement.speed :_.particles_move_speed,1),
					speedMin:_d(newversion ? _.movement.speedMin :_.particles_move_speed_min,1),
					direction:_d(newversion ? _.movement.direction :_.particles_move_direction,"none"),
					straight:_d(_truefalse(newversion ? _.movement.straight :_.particles_move_straight), true),
					bounce:_d(_truefalse(newversion ? _.movement.bounce :_.particles_move_bounce), false)
				},
				interactivity:{					
					hoverMode:newversion ? _d(_.interactivity.hoverMode,"none") : _truefalse(_.particles_onhover_enable) ? _d(_.particles_onhover_mode,"repulse") : "none",					
 					clickMode:newversion ? _d(_.interactivity.clickMode,"none") : _truefalse(_.particles_onclick_enable) ? _d(_.particles_onclick_mode,"repulse") : "none"
				},
				bubble:{
					distance:_d(newversion ? _.bubble.distance :_.particles_modes_bubble_distance,400),
					size:_d(newversion ? _.bubble.size :_.particles_modes_bubble_opacity,40),
					opacity:_d(newversion ? _.bubble.opacity :_.particles_modes_bubble_size,40)
				},
				grab:{
					distance:_d(newversion ? _.grab.distance :_.particles_modes_grab_distance,400),
					opacity:_d(newversion ? _.grab.opacity :_.particles_modes_grab_opacity,50)
				},
				repulse:{
					distance:_d(newversion ? _.repulse.distance : _.particles_modes_repulse_distance,200),
					easing: newversion ? _.repulse.easing : 100 /* new option */
				},
				pulse:{
					size:{
						enable:_d(_truefalse(newversion ? _.pulse.size.enable :_.particles_size_anim_enable), false),
						speed:_d(newversion ? _.pulse.size.speed :_.particles_size_anim_speed,40),
						min:_d(newversion ? _.pulse.size.min :_.particles_size_anim_min,1),
						sync:_d(_truefalse(newversion ? _.pulse.size.sync :_.particles_size_anim_sync), false)
					},
					opacity:{
						enable:_d(_truefalse(newversion ? _.pulse.opacity.enable :_.particles_opacity_anim_enable), false),
						speed:_d(newversion ? _.pulse.opacity.speed :_.particles_opacity_anim_speed,3),
						min:_d(newversion ? _.pulse.opacity.min :_.particles_opacity_anim_min,0),
						sync:_d(_truefalse(newversion ? _.pulse.opacity.sync :_.particles_opacity_anim_sync), false)
					}
				}
			};
			
		}
		
		function initInputs() {
			
			addon.forms.slidegeneral.find('.tos2.nosearchbox').select2RS({
				minimumResultsForSearch:"Infinity",
				placeholder:"Select From List"
			});	
								
			// skip updating fields if currentSlide === static layers
			if(RVS.S.slideId.toString().search('static') === -1) {
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger: 'init'});
			}
			
			// on/off init
			RVS.F.initOnOff(addon.forms.slidegeneral);
			
		}

		function allColorFieldUpdate() {
			
			buildColorFields({val:"particle"});
			buildColorFields({val:"border"});
			buildColorFields({val:"lines"});
			
		}

		/*BUILD COLOR FIELDS BASED ON ARRAY*/
		function buildColorFields(_) {

			var colors = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].styles[_.val].color.split(","),
				_h = '';
			_.cname = bricks[_.val+"color"];
			_.class = "particle_"+_.val+"_colors";
			for (var i in colors) {	
				if(!colors.hasOwnProperty(i)) continue;
				_h += '<longoption class="particles_color_variant">';
				_h += i==0 ? '<i class="material-icons">color_lens</i><label_a>'+_.cname+'</label_a>' : '<i class="material-icons">color_lens</i><label_a>'+bricks.colorvariant+' '+i+'</label_a>';
				_h += '<input data-helpkey="particles-' + _.val + '-color" type="text" data-editing="'+_.cname+'" data-mode="single" name="'+_.class+'[]" data-ident="'+_.class+'" class="'+_.class+' my-particle-color-field slideinput easyinit" data-visible="false" value="'+colors[i]+'">';
				_h += i==0 ? '<div data-r="'+_.val+'" class="add_part_color_lines autosize basic_action_button onlyicon"><i class="material-icons">add</i></div>' : '<div data-ident="'+_.class+'" class="remove_part_color_lines autosize basic_action_button onlyicon"><i class="material-icons">clear</i></div>';				
				_h += '</longoption>';
			}		
			document.getElementById(_.class+"_wrap").innerHTML = _h;	
			RVS.F.initTpColorBoxes('#'+_.class+"_wrap .my-particle-color-field");
			
		}

		function getIcons() {
			
			if(this.className.search('selected') !== -1) {
				
				if(this.className.search('custom-particle') === -1) {
					
					addon.selectedIcons[addon.selectedIcons.length] = this.dataset.icon;
				
				}
				else {

					var svg = $(this).find('svg'),
						size = '',
						view;
						
					if(svg.length) view = svg[0].getAttribute('viewBox');
					if(view) {
						
						view = view.split(' ');
						if(view.length) size = '::' + view[view.length - 1];

					}
					
					addon.selectedIcons[addon.selectedIcons.length] = this.dataset.path + size;
				
				}
				
			}
			
		}
		
		function updateSelectedIcons() {
			
			$('.particles-icon').removeClass('selected');
			$('#particles-custom-svgs').html(getCustomSvgs());
			
			var svgs = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].particles.shape.split(','),
				len = svgs.length;
			
			for(var i = 0; i < len; i++) {
				
				if(svgs[i].charAt(0) !== 'M') {	
					$('.pei_' + svgs[i]).addClass('selected');
				}
				else {
					$('.particles-icon[data-path="' + svgs[i].split('::')[0] + '"]').addClass('selected');
				}
				
			}
			
		}
		
		function getCustomIcon(path, size) {
			
			if(!size) {
			
				if(path.search('::') === -1) {
					size = '24';
				}
				else {
					path = path.split('::');
					size = path[1];
					path = path[0];
				}
			
			}
			return '<span class="particles-icon custom-particle selected" data-path="' + path + '"><svg xmlns="http://www.w3.og/2000/svg" viewBox="0 0 ' + size + ' ' + size + '"><path fill="#777c80" d="' + path + '"></path></svg></span>';
			
		}
		
		function getCustomSvgs() {
			
			if(displayChecks()) return;
			
			var svgs = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].particles.shape.split(','),
				len = svgs.length,
				icons = '';
			
			for(var i = 0; i < len; i++) {
				
				if(svgs[i].charAt(0) === 'M') icons += getCustomIcon(svgs[i]);
				
			}
			
			return icons;
			
		}
		
		function displayChecks() {
			
			if(!addon.isActive || RVS.S.slideId.toString().search('static') !== -1) return true;
			RVS.SLIDER[RVS.S.slideId].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[RVS.S.slideId].slide.addOns[slug], true);
			return false;
			
		}
		
		var events = {
			
			updateDisplay: function() {
				
				if(displayChecks()) return;
				
				events.checkContainers();
				allColorFieldUpdate();
				updateSelectedIcons();
				
			},
			
			iconClick: function() {
				
				var $this = $(this),
					icons = $('.particles-icon'),
					method = $this.hasClass('selected') ? 'removeClass' : 'addClass';

				$this[method]('selected');
				addon.selectedIcons = [];
				icons.each(getIcons);
				
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].particles.shape = addon.selectedIcons.toString();
				
			},
			
			checkContainers: function() {
				
				var clickMode = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].interactivity.clickMode,
					hoverMode = RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].interactivity.hoverMode,
					method = hoverMode !== 'none' && clickMode === 'bubble' ? 'show' : 'hide';
				
				addon.forms.bubbles.hide();
				addon.forms.grab.hide();
				addon.forms.repulse.hide();
				$('#particles_'+hoverMode+'_mode').show();
				$('#particles_'+clickMode+'_mode').show();	
				$('#rsbubblemessage')[method]();
				
			},
			
			addColor: function() {
				
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].styles[this.dataset.r].color += ', #ffffff';				
				buildColorFields({val:this.dataset.r});
				
			},
			
			removeColor: function() {
				
				var ident = this.dataset.ident,
					l = [];
				$(this).closest('.particles_color_variant').remove();				
				$('.'+ident).each(function() { l.push(this.dataset.color);});
				l = l.toString();				
				$('#'+ident).val(l).trigger("change");	
				
			},
			
			colorEdit: function(e, inp, val, gradient, onSave) {	
				
				if(!addon.isActive) return;
				 
				// only write the value if the color picker was saved
				if (inp!==undefined && onSave) {
					if (inp[0].name===inp[0].dataset.ident+"[]") {
						var l = [];
						$('.'+inp[0].dataset.ident).each(function() {
							l.push(this.dataset.color);
						});
						l = l.toString();
						$('#'+inp[0].dataset.ident).val(l).trigger("change");					
					}
				}											
			},
			
			addSvg: function() {
				
				RVS.F.openObjectLibrary({types: ['svgs'], filter: 'all', selected: ['svgs'], success: {icon: 'particleSvgSelected'}});
				
			},
			
			svgSelected: function(e, p) {
				
				if(!p || !p.path) {
					
					console.log('Particle SVG could not be selected');
					return;
					
				}
				
				var viewBox = $(p.ref).find('svg').attr('width'),
					svg = getCustomIcon(p.path, viewBox);
					
				$(svg).insertBefore(addon.forms.svgs);
				RVS.SLIDER[RVS.S.slideId].slide.addOns[slug].particles.shape += ',' + p.path + '::' + viewBox;

			},
			
			// write defaults and update fields upon new slide creation
			newSlideCreated: function(e, id) {
				
				if(!addon.isActive) return;
					
				// check defaults
				RVS.SLIDER[id].slide.addOns[slug] = checkSlideDefaults(RVS.SLIDER[id].slide.addOns[slug], true);
				
				// update fields
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: id + '.slide.'});
				
				// update preview image
				events.updateDisplay();
				
				// reset tabs
				$('#particles-tab-1').click();
				
			},
			
			presets: function(key,custom) {	

				RVS.F.openBackupGroup({id: 'particles', txt: bricks.parpres, icon: 'touch_app'});
				var obj = custom === 'true' || custom === true ? addon.customs[key].preset : addon.defaults[key].preset;
				
				RVS.F.updateSliderObj({path: RVS.S.slideId + '.slide.addOns.' + slug, val: sanitizePreset(obj)});
				RVS.F.closeBackupGroup({id: 'particles'});
				
				RVS.F.updateEasyInputs({container: addon.forms.slidegeneral, path: RVS.S.slideId + '.slide.', trigger: 'init'});
				events.updateDisplay();
				
			 },
			 
			 ajax: function(e, _) {
				 								
				var preset,
					key;
								
				// GET CHANGES
				if (_.mode==="overwrite" || _.mode==="create") preset = $.extend(true,{},RVS.SLIDER[RVS.S.slideId].slide.addOns[slug]);
				
				// GET TINDEX
				if (_.mode==="overwrite" || _.mode=="rename") key = _.pl.data("key");

				// RENAME, TAKE FIRST EXISTING OBJECT
				if (_.mode==="rename") { preset = addon.customs[_.key].preset; addon.customs[_.key].title=_.newname;}
				
				if (_.mode==="delete") {
					RVS.F.ajaxRequest('delete_custom_templates_'+slug, {id:_.key},function(response) {
						if (response.success) {
							delete addon.customs[_.key];
							_.pl.remove();
						}
					});	
				} else {
					// CALL CREATE / RENAME / OVERWRITE AJAX FUNCTION
					RVS.F.ajaxRequest('save_custom_templates_'+slug, {id:_.key, obj:{title:_.newname, preset:preset}}, function(response){						
						if(response.success) {							
							addon.customs[response.data.id] = {title:_.newname, preset:preset};		
							if (_.mode==="create") _.element[0].dataset.key = response.data.id;
							if (_.mode==="rename") _.pl.find('.cla_custom_name').text(_.newname);							
						}
					});	
				}
				 
			 }
			
		};

				
		// INITIALISE typewriter LISTENERS
		function addEvents() {		
		
			RVS.DOC.on('SceneUpdatedAfterRestore.particles updateslidebasic',events.updateDisplay)
							.on('click', '.particles-icon', events.iconClick)
							.on('checkbubblegrabrepulse', events.checkContainers)
							.on('click','.add_part_color_lines', events.addColor)
							.on('click','.remove_part_color_lines', events.removeColor)
							.on('particles_ajax_calls', events.ajax)
							.on('newSlideCreated', events.newSlideCreated)
							.on('openParticlesSvgLibrary', events.addSvg)
							.on('particleSvgSelected', events.svgSelected)
							.on('coloredit colorcancel', events.colorEdit);


		}
		
					
		// CREATE INPUT FIELDS
		function createSlideSettingsFields() {
				
			addon.defaults = {
				
				spider_web: {title: 'Spider Web', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"80","size":2,"sizeMin":30,"random":false},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":true,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"50","opacityMin":"25","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"6","speedMin":"6","direction":"none","straight":true,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":1,"sync":false},"opacity":{"enable":false,"speed":"3","min":"0","sync":false}}}'},
				starry_night: {title: 'Starry Night', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"120","size":1,"sizeMin":0.1,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"50","opacityMin":"25","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":false,"speed":"6","speedMin":"2","direction":"static","straight":true,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":true,"speed":"7","min":0.1,"sync":false},"opacity":{"enable":false,"speed":"10","min":"0","sync":false}}}'},
				floating_in_space: {title: 'Floating in space', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"80","size":1,"sizeMin":0.1,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"60","opacityMin":"20","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":false,"speed":"5","speedMin":"4","direction":"top-right","straight":false,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"7","min":"10","sync":false}}}'},
				science_class: {title: 'Science Class', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"80","size":10,"sizeMin":1,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"75","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"75","opacityMin":"25","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"12","speedMin":"4","direction":"none","straight":false,"bounce":true},"interactivity":{"hoverMode":"grab","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":true,"speed":"80","min":1,"sync":false},"opacity":{"enable":false,"speed":"6","min":"20","sync":true}}}'},
				hover_bubbles: {title: 'Hover Bubbles', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"80","size":2,"sizeMin":30,"random":false},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"50","opacityMin":"25","opacityRandom":false,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"6","speedMin":"6","direction":"none","straight":true,"bounce":false},"interactivity":{"hoverMode":"bubble","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"50"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"3","min":"0","sync":false}}}'},
				soda_bubbles: {title: 'Soda Bubbles', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"60","size":20,"sizeMin":1.5,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"50","opacityMin":"10","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"5","speedMin":"2","direction":"top","straight":true,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"50"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"3","min":"0","sync":false}}}'},
				valentines_day: {title: 'Valentines Day', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"heart_1","number":"20","size":60,"sizeMin":20,"random":true},"styles":{"border":{"enable":true,"color":"#ffffff","opacity":"75","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#cf3fff,#569fff,#ff0202","opacity":"75","opacityMin":"50","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"6","speedMin":"6","direction":"none","straight":true,"bounce":true},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":25,"sync":false},"opacity":{"enable":false,"speed":"3","min":"20","sync":false}}}'},
				static_stars: {title: 'Static Stars', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"star","number":"50","size":"30","sizeMin":"10","random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"50","opacityMin":"25","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":false,"randomSpeed":true,"speed":"6","speedMin":"6","direction":"none","straight":true,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"50"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":1,"sync":false},"opacity":{"enable":false,"speed":"3","min":"0","sync":false}}}'},
				party_stars: {title: 'Party Stars', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"star","number":"40","size":"10","sizeMin":"1","random":false},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ed15f4,#f4ed15,#15f4ee","opacity":75,"opacityMin":50,"opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"6","speedMin":"2","direction":"none","straight":true,"bounce":false},"interactivity":{"hoverMode":"repulse","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"40"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":1,"sync":false},"opacity":{"enable":true,"speed":"10","min":"0","sync":false}}}'},
				moving_markets: {title: 'Moving Markets', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"arrow_2","number":"10","size":80,"sizeMin":30,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#ffffff","width":"1","opacity":"40","distance":"150"},"particle":{"color":"#ffffff","opacity":"80","opacityMin":"40","opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"12","speedMin":"6","direction":"right","straight":false,"bounce":false},"interactivity":{"hoverMode":"none","clickMode":"none"},"bubble":{"distance":"400","size":"40","opacity":"1"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"7","min":"10","sync":false}}}'},
				particle_effect_one: {title: 'Particle effect one', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"80","size":10,"sizeMin":0.5,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":true,"color":"#000000","width":"1","opacity":"20","distance":"200"},"particle":{"color":"#000000","opacity":"30","opacityMin":"25","opacityRandom":false,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"3","speedMin":"3","direction":"none","straight":false,"bounce":false},"interactivity":{"hoverMode":"bubble","clickMode":"none"},"bubble":{"distance":"400","size":"150","opacity":"100"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"200","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"3","min":"0","sync":false}}}'},
				particle_effect_two: {title: 'Particle effect two', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"triangle","number":"100","size":15,"sizeMin":0.75,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":false,"color":"#000000","width":"1","opacity":"20","distance":"200"},"particle":{"color":"#000000","opacity":55,"opacityMin":30,"opacityRandom":true,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"1","speedMin":"3","direction":"top","straight":true,"bounce":false},"interactivity":{"hoverMode":"bubble","clickMode":"repulse"},"bubble":{"distance":"400","size":"0","opacity":"5"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"500","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.75,"sync":false},"opacity":{"enable":false,"speed":"1","min":"0","sync":false}}}'},
				particle_effect_three: {title: 'Particle effect three', preset: '{"enable":true,"hideOnMobile":true,"particles":{"shape":"circle","number":"300","size":1,"sizeMin":0.5,"random":true},"styles":{"border":{"enable":false,"color":"#ffffff","opacity":"100","size":"1"},"lines":{"enable":true,"color":"#000000","width":"1","opacity":"35","distance":"80"},"particle":{"color":"#000000","opacity":"1","opacityMin":"25","opacityRandom":false,"zIndex":"default"}},"movement":{"enable":true,"randomSpeed":true,"speed":"1","speedMin":"3","direction":"right","straight":true,"bounce":false},"interactivity":{"hoverMode":"repulse","clickMode":"bubble"},"bubble":{"distance":"400","size":"100","opacity":"100"},"grab":{"distance":"400","opacity":"50"},"repulse":{"distance":"75","easing":100},"pulse":{"size":{"enable":false,"speed":"40","min":0.5,"sync":false},"opacity":{"enable":false,"speed":"1","min":"0","sync":false}}}'}
				
			};
			addon.customs = revslider_particles_addon.custom_templates===undefined ? {} : revslider_particles_addon.custom_templates;

			addon.svgs = {
				
				edge: 'M4 4h16v16H4z', 
				triangle: 'M12 6L4 20L20 20z', 
				polygon: 'M5 4 L17 4 L22 12 L17 20 L8 20 L3 12 L8 4 Z', 
				star: 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z',
				heart_1: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',		
				star_2: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z',			
				settings: 'M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z',			
				arrow_1: 'M4 18l8.5-6L4 6v12zm9-12v12l8.5-6L13 6z',
				bullseye: 'M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3-8c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z',
				plus_1: 'M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z',
				triangle_2: 'M12 7.77L18.39 18H5.61L12 7.77M12 4L2 20h20L12 4z',
				smilie: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z',
				star_3: 'M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z',
				heart_2: 'M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z',
				plus_2: 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
				close: 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z',
				arrow_2: 'M22 12l-4-4v3H3v2h15v3z',
				dollar: 'M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z',
				sun_1: 'M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z',
				sun_2: 'M7 11H1v2h6v-2zm2.17-3.24L7.05 5.64 5.64 7.05l2.12 2.12 1.41-1.41zM13 1h-2v6h2V1zm5.36 6.05l-1.41-1.41-2.12 2.12 1.41 1.41 2.12-2.12zM17 11v2h6v-2h-6zm-5-2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm2.83 7.24l2.12 2.12 1.41-1.41-2.12-2.12-1.41 1.41zm-9.19.71l1.41 1.41 2.12-2.12-1.41-1.41-2.12 2.12zM11 23h2v-6h-2v6z',
				snowflake: 'M22 11h-4.17l3.24-3.24-1.41-1.42L15 11h-2V9l4.66-4.66-1.42-1.41L13 6.17V2h-2v4.17L7.76 2.93 6.34 4.34 11 9v2H9L4.34 6.34 2.93 7.76 6.17 11H2v2h4.17l-3.24 3.24 1.41 1.42L9 13h2v2l-4.66 4.66 1.42 1.41L11 17.83V22h2v-4.17l3.24 3.24 1.42-1.41L13 15v-2h2l4.66 4.66 1.41-1.42L17.83 13H22z',
				party: 'M4.59 6.89c.7-.71 1.4-1.35 1.71-1.22.5.2 0 1.03-.3 1.52-.25.42-2.86 3.89-2.86 6.31 0 1.28.48 2.34 1.34 2.98.75.56 1.74.73 2.64.46 1.07-.31 1.95-1.4 3.06-2.77 1.21-1.49 2.83-3.44 4.08-3.44 1.63 0 1.65 1.01 1.76 1.79-3.78.64-5.38 3.67-5.38 5.37 0 1.7 1.44 3.09 3.21 3.09 1.63 0 4.29-1.33 4.69-6.1H21v-2.5h-2.47c-.15-1.65-1.09-4.2-4.03-4.2-2.25 0-4.18 1.91-4.94 2.84-.58.73-2.06 2.48-2.29 2.72-.25.3-.68.84-1.11.84-.45 0-.72-.83-.36-1.92.35-1.09 1.4-2.86 1.85-3.52.78-1.14 1.3-1.92 1.3-3.28C8.95 3.69 7.31 3 6.44 3 5.12 3 3.97 4 3.72 4.25c-.36.36-.66.66-.88.93l1.75 1.71zm9.29 11.66c-.31 0-.74-.26-.74-.72 0-.6.73-2.2 2.87-2.76-.3 2.69-1.43 3.48-2.13 3.48z',
				flower_1: 'M18.7 12.4c-.28-.16-.57-.29-.86-.4.29-.11.58-.24.86-.4 1.92-1.11 2.99-3.12 3-5.19-1.79-1.03-4.07-1.11-6 0-.28.16-.54.35-.78.54.05-.31.08-.63.08-.95 0-2.22-1.21-4.15-3-5.19C10.21 1.85 9 3.78 9 6c0 .32.03.64.08.95-.24-.2-.5-.39-.78-.55-1.92-1.11-4.2-1.03-6 0 0 2.07 1.07 4.08 3 5.19.28.16.57.29.86.4-.29.11-.58.24-.86.4-1.92 1.11-2.99 3.12-3 5.19 1.79 1.03 4.07 1.11 6 0 .28-.16.54-.35.78-.54-.05.32-.08.64-.08.96 0 2.22 1.21 4.15 3 5.19 1.79-1.04 3-2.97 3-5.19 0-.32-.03-.64-.08-.95.24.2.5.38.78.54 1.92 1.11 4.2 1.03 6 0-.01-2.07-1.08-4.08-3-5.19zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z',
				flower_2: 'M12 22c4.97 0 9-4.03 9-9-4.97 0-9 4.03-9 9zM5.6 10.25c0 1.38 1.12 2.5 2.5 2.5.53 0 1.01-.16 1.42-.44l-.02.19c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5l-.02-.19c.4.28.89.44 1.42.44 1.38 0 2.5-1.12 2.5-2.5 0-1-.59-1.85-1.43-2.25.84-.4 1.43-1.25 1.43-2.25 0-1.38-1.12-2.5-2.5-2.5-.53 0-1.01.16-1.42.44l.02-.19C14.5 2.12 13.38 1 12 1S9.5 2.12 9.5 3.5l.02.19c-.4-.28-.89-.44-1.42-.44-1.38 0-2.5 1.12-2.5 2.5 0 1 .59 1.85 1.43 2.25-.84.4-1.43 1.25-1.43 2.25zM12 5.5c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5S9.5 9.38 9.5 8s1.12-2.5 2.5-2.5zM3 13c0 4.97 4.03 9 9 9 0-4.97-4.03-9-9-9z',
				fire: 'M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z',
				pizza: 'M12 2C8.43 2 5.23 3.54 3.01 6L12 22l8.99-16C18.78 3.55 15.57 2 12 2zM7 7c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm5 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z'
			
			};	

			var _h,				
				plist = RVS.F.createPresets({ 		
					groupid:"particle_templates",			
					title:bricks.pelib,
					customevt:"particles_ajax_calls",
					groups: { defaults:{ title:bricks.parpres, elements:addon.defaults} , custom:{ title:bricks.custompres, elements:addon.customs}},
					onclick: events.presets
				});
								
			/* */
			// PARTICLE EFFECTS GENERAL SETTINGS
			_h += '<div class="form_inner_header"><i class="material-icons">edit</i>'+bricks.general+'</div>';
			_h +='<div class="collapsable" style="display:block !important">'; 
			_h += '	<label_a>'+bricks.active+'</label_a><input type="checkbox" id="particles_enable" class="slideinput easyinit" data-r="addOns.'+slug+'.enable" data-showhide="#particle_settings_wrap" data-showhidedep="true" value="off">';
			_h += '</div>';
			_h += '<div id="particle_settings_wrap">';
			_h += '<div class="form_inner_header"><i class="material-icons">scatter_plot</i>'+bricks.settings+'</div>';
			_h +='<div  id="particels_effects_form_wrap" class="collapsable" style="display:block !important">'; 				
			_h += plist;
			// PARTICLE SUBMENU 
			_h += ' <div class="div15"></div>';				
			_h += '<div id="particles-tab-1" class="settingsmenu_wrapbtn"><div data-inside="#particels_effects_form_wrap" data-showssm="#particle_settings" class="ssmbtn selected">'+bricks.singparticles+'</div></div>';
			_h += '<div id="particles-tab-2" class="settingsmenu_wrapbtn"><div data-inside="#particels_effects_form_wrap" data-showssm="#particle_styling" class="ssmbtn">'+bricks.style+'</div></div>';
			_h += '<div id="particles-tab-3" class="settingsmenu_wrapbtn"><div data-inside="#particels_effects_form_wrap" data-showssm="#particle_movement_wrap" class="ssmbtn ">'+bricks.smovement+'</div></div>';
			_h += '<div id="particles-tab-4" class="settingsmenu_wrapbtn"><div data-inside="#particels_effects_form_wrap" data-showssm="#particle_interactivity" class="ssmbtn">'+bricks.interactivity+'</div></div>';
			_h += '<div id="particles-tab-5" class="settingsmenu_wrapbtn"><div data-inside="#particels_effects_form_wrap" data-showssm="#particle_pulse" class="ssmbtn">'+bricks.pulse+'</div></div>';				
			_h += ' <div class="div25"></div>';
			
			// PARTICLE SETTING
			_h += '		<div id="particle_settings" class="ssm_content selected">'; 
			_h += '			<div id="particle_iconselector_wrap">';
			_h += '				<span data-icon="circle" class="particles-icon pei_circle"><span class="particles-circle"></span></span>';
			for (var i in addon.svgs) {
				if(!addon.svgs.hasOwnProperty(i)) continue;
				_h += '			<span class="particles-icon pei_'+i+'" data-icon="'+i+'"><svg xmlns="http://www.w3.og/2000/svg" viewBox="0 0 24 24"><path fill="#777c80" d="'+addon.svgs[i]+'"></path></svg></span>';
			}
			_h += '<div id="particles-custom-svgs">' + getCustomSvgs() + '</div>';
			_h += '				<input type="hidden" class="slideinput easyinit" id="particles_shape" data-r="addOns.'+slug+'.particles.shape" type="text">';
			_h += ' 			<div style="clear: both"></div>';
			_h += '			</div>';
			_h += '			<div class="div25"></div>';
			_h += ' 		<div id="add_particles_svg" class="basic_action_button longbutton callEventButton" style="width: 100%" data-evt="openParticlesSvgLibrary"><i class="material-icons">camera_enhance</i>' + bricks.objectlibrary + '</div>';
			_h += '			<div class="div25" style="clear: both"></div>';
			
			_h += '			<longoption><i class="material-icons">scatter_plot</i><label_a>'+bricks.amount+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_amount" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.particles.number" data-min="0" data-max="5000" type="text"></longoption>';
			_h += '			<longoption><i class="material-icons">all_out</i><label_a>'+bricks.size+'</label_a><input class="slideinput valueduekeyboard  easyinit" id="particles_size" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.particles.size" data-min="0" data-max="1000" type="text"></longoption>';						
			_h += '			<longoption><i class="material-icons">bubble_chart</i><label_a>'+bricks.randomsize+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.particles.random" data-showhide=".particle_size_random" data-showhidedep="true" value="on"></longoption>';
			_h += '			<div class="div5"></div>';
			_h += '			<longoption class="particle_size_random"><i class="material-icons">all_out</i><label_a>'+bricks.minsize+'</label_a><input class="slideinput valueduekeyboard  easyinit" id="particles_minsize" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.particles.sizeMin" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '	        <longoption><i class="material-icons">phonelink_lock</i><label_a>'+bricks.hideonmobile+'</label_a><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.hideOnMobile" value="off">';
			_h += '		</div>'; // END OF PARTICLE SETTINGS

			// PARTICLE STYLING - COLOR & OPACITY
			_h += '		<div id="particle_styling" class="ssm_content">';			
			_h += '			<longoption><i class="material-icons">layers</i><label_a>'+bricks.zindex+'</label_a><input class="slideinput  easyinit" id="particle_zindex" data-r="addOns.'+slug+'.styles.particle.zIndex" type="text"></longoption>';
			_h += '			<longoption style="display:none"><i class="material-icons">color_lens</i><label_a>'+bricks.particlecolor+'</label_a><input id="particle_particle_colors" class="slideinput easyinit" data-r="addOns.'+slug+'.styles.particle.color" type="text" data-helpkey="particles-particle-color"></longoption>';								
			_h += '			<div id="particle_particle_colors_wrap" class="particle_color_wraps"></div>';
			_h += '			<div class="div25"></div>';	
			_h += '			<longoption><i class="material-icons">opacity</i><label_a>'+bricks.partopa+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_opacity" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.styles.particle.opacity" data-min="0" data-max="100" type="text"></longoption>';
			_h += '			<longoption><i class="material-icons">shuffle</i><label_a>'+bricks.randopa+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.styles.particle.opacityRandom" data-showhide=".particle_random_opacity" data-showhidedep="true" value="on"></longoption>';
			_h += '			<div class="div5"></div>';	
			_h += '			<longoption class="particle_random_opacity"><i class="material-icons">opacity</i><label_a>'+bricks.minopa+'</label_a><input class="slideinput valueduekeyboard  easyinit" id="particles_min_opacity" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.styles.particle.opacityMin" data-min="0" data-max="100" type="text"></longoption>';
			_h += '			<div class="div25"></div>';
			// PARTICLE STYLING - BORDERS
			_h += '			<longoption><i class="material-icons">border_outer</i><label_a>'+bricks.borders+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.styles.border.enable" data-showhide=".particle_border_enabled" data-showhidedep="true" value="on"></longoption>';				
			_h += '			<div class="particle_border_enabled">';	
			_h += '			<div class="div5"></div>';
			_h += '				<longoption style="display:none"><i class="material-icons">color_lens</i><label_a>'+bricks.bordercolor+'</label_a><input id="particle_border_colors" class="slideinput easyinit" data-r="addOns.'+slug+'.styles.border.color" type="text" data-helpkey="particles-border-color"></longoption>';								
			_h += '				<div id="particle_border_colors_wrap" class="particle_color_wraps"></div>';	
			_h += '				<longoption><i class="material-icons">border_color</i><label_a>'+bricks.borsize+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_border_size" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.styles.border.size" data-min="0" data-max="100" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">opacity</i><label_a>'+bricks.boropa+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_border_opacity" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.styles.border.opacity" data-min="0" data-max="100" type="text"></longoption>';
			_h += '				<div class="div25"></div>';
			_h += '			</div>';	
			// PARTICLE STYLING - LINES
			_h += '			<longoption><i class="material-icons">timeline</i><label_a>'+bricks.conlin+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.styles.lines.enable" data-showhide=".particle_lines_enabled" data-showhidedep="true" value="on"></longoption>';
			_h += '			<div class="particle_lines_enabled">';				
			_h += '			<div class="div5"></div>';
			_h += '				<longoption style="display:none"><i class="material-icons">color_lens</i><label_a>'+bricks.linescolor+'</label_a><input id="particle_lines_colors" class="slideinput easyinit" data-r="addOns.'+slug+'.styles.lines.color" type="text" data-helpkey="particles-lines-color"></longoption>';								
			_h += '				<div id="particle_lines_colors_wrap" class="particle_color_wraps"></div>';					
			_h += '				<longoption><i class="material-icons">line_weight</i><label_a>'+bricks.linwidth+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_lines_size" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.styles.lines.width" data-min="0" data-max="200" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">opacity</i><label_a>'+bricks.linopa+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_lines_opacity" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.styles.lines.opacity" data-min="0" data-max="100" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">settings_ethernet</i><label_a>'+bricks.lindist+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_lines_distance" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.styles.lines.distance" data-min="0" data-max="400" type="text"></longoption>';
			_h += '			</div>';					
			_h += '		</div>'; // END OF PARTICLE STYLING		

			// PARTICLE MOVEMENT 
			_h += '		<div id="particle_movement_wrap" class="ssm_content">';			
			_h += '			<longoption><i class="material-icons">control_camera</i><label_a>'+bricks.movement+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.movement.enable" data-showhide=".particle_movement" data-showhidedep="true" value="on"></longoption>';
			_h += '			<div class="particle_movement">';				
			_h += '				<div class="div5"></div>';
			_h += '				<longoption><i class="material-icons">timer</i><label_a>'+bricks.speed+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_mov_speed" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.movement.speed" data-min="0" data-max="200" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">shuffle</i><label_a>'+bricks.vspeed+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.movement.randomSpeed" data-showhide=".particle_randomspeed" data-showhidedep="true" value="on"></longoption>';
			_h += '				<div class="particle_randomspeed">';								
			_h += '					<longoption><i class="material-icons">timer</i><label_a>'+bricks.minspeed+'</label_a><input class="slideinput valueduekeyboard easyinit" id="particles_mov_min_speed" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.movement.speedMin" data-min="0" data-max="200" type="text"></longoption>';
			_h += '				</div>';			
			_h += '				<longoption><i class="material-icons">call_missed</i><label_a>'+bricks.bounce+'</label_a><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.movement.bounce"value="on"></longoption>';
			_h += '				<div class="div25"></div>';
			_h += '<label_a>'+bricks.direction+'</label_a><select id="particles_mov_direction"  class="slideinput tos2 nosearchbox easyinit" data-show=".__pmd_*val*" data-hide=".__pmd_general" data-r="addOns.'+slug+'.movement.direction">';
			_h += '<option value="none">'+bricks.random+'</option>';
			_h += '<option value="static">'+bricks.static+'</option>';
			_h += '<option value="top">'+bricks.top+'</option>';
			_h += '<option value="right">'+bricks.right+'</option>';
			_h += '<option value="bottom">'+bricks.bottom+'</option>';
			_h += '<option value="left">'+bricks.left+'</option>';
			_h += '<option value="top-left">'+bricks.topleft+'</option>';
			_h += '<option value="top-right">'+bricks.topright+'</option>';
			_h += '<option value="bottom-left">'+bricks.bottomleft+'</option>';
			_h += '<option value="bottom-right">'+bricks.bottomright+'</option>';
			_h += '</select>';
			_h += '				<div class="__pmd_top __pmd_bottom __pmd_left __pmd_right __pmd_top-left __pmd_top-right __pmd_bottom-left __pmd_bottom-right __pmd_general">';
			_h += '					<longoption><i class="material-icons">gesture</i><label_a>'+bricks.vmovement+'</label_a><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.movement.straight"value="on"></longoption>';
			_h += '				</div>';
			
			_h += '			</div>';
			_h += '		</div>';// END OF PARTICLE MOVEMENT

			// PARTICLE INTERACTIVITY
			_h += '		<div id="particle_interactivity" class="ssm_content">';			
			_h += '<label_a>'+bricks.hmode+'</label_a><select class="slideinput callEvent tos2 nosearchbox easyinit" data-evt="checkbubblegrabrepulse" data-r="addOns.'+slug+'.interactivity.hoverMode">';
			_h += '<option value="none">'+bricks.nohover+'</option>';
			_h += '<option value="repulse">'+bricks.repulse+'</option>';
			_h += '<option value="grab">'+bricks.grab+'</option>';
			_h += '<option value="bubble">'+bricks.bubble+'</option>';				
			_h += '</select>';				
			_h += '<label_a>'+bricks.cmode+'</label_a><select class="slideinput callEvent tos2 nosearchbox easyinit" data-evt="checkbubblegrabrepulse" data-r="addOns.'+slug+'.interactivity.clickMode">';
			_h += '<option value="none">'+bricks.noclick+'</option>';
			_h += '<option value="repulse">'+bricks.repulse+'</option>';				
			_h += '<option value="bubble">'+bricks.bubble+'</option>';				
			_h += '</select>';
			_h += '<row id="rsbubblemessage" class="direktrow" style="display: none">';
			_h += '    <labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf>';
			_h += '    <contenthalf><div class="function_info">' + bricks.bubblemessage + '</div></contenthalf>';
			_h += '</row>';
			_h += '			<div id="particles_bubble_mode">';
			_h += '				<div class="div25"></div>';
			_h += '				<longoption><i class="material-icons">settings_ethernet</i><label_a>'+bricks.bdist+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.bubble.distance" data-min="0" data-max="1200" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">all_out</i><label_a>'+bricks.bsize+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.bubble.size" data-min="0" data-max="1200" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">opacity</i><label_a>'+bricks.bop+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.bubble.opacity" data-min="0" data-max="100" type="text"></longoption>';
			_h += '			</div>';
			_h += '			<div id="particles_grab_mode">';
			_h += '				<div class="div25"></div>';
			_h += '				<longoption><i class="material-icons">settings_ethernet</i><label_a>'+bricks.gdist+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.grab.distance" data-min="0" data-max="1200" type="text"></longoption>';				
			_h += '				<longoption><i class="material-icons">opacity</i><label_a>'+bricks.gop+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.grab.opacity" data-min="0" data-max="100" type="text"></longoption>';
			_h += '			</div>';
			_h += '			<div id="particles_repulse_mode">';
			_h += '				<div class="div25"></div>';
			_h += '				<longoption><i class="material-icons">settings_ethernet</i><label_a>'+bricks.rdist+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.repulse.distance" data-min="0" data-max="1200" type="text"></longoption>';				
			_h += '				<longoption><i class="material-icons">keyboard_tab</i><label_a>'+bricks.rease+'</label_a><input class="slideinput valueduekeyboard easyinit" data-numeric="true" data-allowed="ms" data-r="addOns.'+slug+'.repulse.easing" data-min="0" data-max="1000" type="text"></longoption>';				
			_h += '			</div>';
			_h += '	</div>'; //END OF PARTICLE INTERACTIVITY

			// PARTICLE PULSE
			_h += '		<div id="particle_pulse" class="ssm_content">';			
			_h += '			<longoption><i class="material-icons">all_out</i><label_a>'+bricks.apsize+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.pulse.size.enable" data-showhide=".particle_animate_size" data-showhidedep="true" value="on"></longoption>';				
			_h += ' 		<div class="particle_animate_size">';
			_h += '				<longoption><i class="material-icons">timer</i><label_a>'+bricks.speed+'</label_a><input class="slideinput valueduekeyboard  easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.pulse.size.speed" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">all_out</i><label_a>'+bricks.minsize+'</label_a><input class="slideinput valueduekeyboard  easyinit" data-numeric="true" data-allowed="px" data-r="addOns.'+slug+'.pulse.size.min" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">sync</i><label_a>'+bricks.sync+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.pulse.size.sync" value="on"></longoption>';	
			_h += ' 		</div>';
			_h += '			<div class="div25"></div>';

			_h += '			<longoption><i class="material-icons">opacity</i><label_a>'+bricks.apopa+'</label_a><input type="checkbox" class="slideinput easyinit" data-r="addOns.'+slug+'.pulse.opacity.enable" data-showhide=".particle_animate_opacity" data-showhidedep="true" value="on"></longoption>';				
			_h += ' 		<div class="particle_animate_opacity">';
			_h += '				<longoption><i class="material-icons">timer</i><label_a>'+bricks.speed+'</label_a><input class="slideinput valueduekeyboard  easyinit" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.pulse.opacity.speed" data-min="0" data-max="1000" type="text"></longoption>';
			_h += '				<longoption><i class="material-icons">opacity</i><label_a>'+bricks.minopa+'</label_a><input class="slideinput valueduekeyboard  easyinit" data-numeric="true" data-allowed="%" data-r="addOns.'+slug+'.pulse.opacity.min" data-min="0" data-max="100" type="text"></longoption>';						
			_h += '				<longoption><i class="material-icons">sync</i><label_a>'+bricks.sync+'</label_a><input type="checkbox" class="slideinput easyinit"  data-r="addOns.'+slug+'.pulse.opacity.sync" value="on"></longoption>';	
			_h += ' 		</div>';
			_h += '			<div class="div25"></div>';
			_h += '		</div>'; // END OF PARTICLE PULSE
			_h += ' </div>';
			_h += '</div>'; // END OF COLLAPSABLE	
					
			addon.forms.slidegeneral.append($(_h));
			

		}
		
		function initHelp() {
			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(typeof HelpGuide !== 'undefined' && revslider_particles_addon.hasOwnProperty('help')) {
			
				var obj = {slug: 'particles_addon'};
				$.extend(true, obj, revslider_particles_addon.help);
				HelpGuide.add(obj);
				
			}
		
		}
		
		/*
		SET VALUE TO A OR B DEPENDING IF VALUE A EXISTS AND NOT UNDEFINED OR NULL
		*/
		function _d(a,b) {
			if (a===undefined || a===null)
				return b;
			else
				return a;
		}

		function _truefalse(v) {
			if (v==="false" || v===false || v==="off" || v===undefined || v===0 || v===-1)
				v=false;
			else
			if (v==="true" || v===true || v==="on")
				v=true;
			return v;
		}
		
	
})(jQuery);