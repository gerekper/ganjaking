var lsTrBuilder = {
	timeout: null,

	selectTransition: function(el) {

		// Gather info
		var	$el = jQuery(el),
			$parent = $el.closest('ul'),
			type = $parent.data('type'),
			$target = jQuery('.ls-tr-list-'+type),
			index = $el.index();

		// Stop preview
		this.stopPreview();

		// Maintain selection on sidebar
		jQuery('.ls-transitions-sidebar li').removeClass('active');
		$el.addClass('active');

		// Switch between transition types and select new transition
		$target.show().siblings().hide();
		jQuery('.ls-transition-item').removeClass('active');
		$target.children().eq(index).addClass('active');

		// Start preview
		this.startPreview(true);
	},



	addTransition: function(el) {

		// Get select
		var	$list = jQuery(el).parent().next(),
			type = $list.data('type'),
			$target = jQuery('.ls-tr-list-'+type);


		// Remove notification (if any)
		$list.next().addClass('ls-hidden');

		// Append clone
		var $template = jQuery( jQuery('#ls-'+type+'-transition-template').text() );
		var $tr = $template.clone().prependTo($target);


		// Append tr to list and select it
		jQuery('.ls-transitions-sidebar li').removeClass('active');
		var $item = jQuery('<li class="active"> \
						<span class="dashicons dashicons-menu"></span> \
						<input type="text" value="'+LS_l10n.untitled+'" placeholder="'+LS_l10n.TBTransitionName+'"> \
						<a href="#" title="'+LS_l10n.TBRemoveTransition+'" class="dashicons dashicons-trash remove"></a> \
					</li>').hide().prependTo($list);


		// Animate list item
		$item.css({ opacity: 0, marginLeft: -300 }).show().animate({ opacity: 1, marginLeft: 0 });

		// Select new transition and focus into text field
		$item.click().find('input').focus().select();
	},



	removeTransition: function(el) {

		// Ask confirmation to continue ...
		if(!confirm(LS_l10n.TBRemoveConfirmation)) { return; }

		// Gather info
		var	$el = jQuery(el).closest('li'),
			$parent = $el.parent(),
			type = $parent.data('type'),
			$target = jQuery('.ls-tr-list-'+type),
			index = $el.index(),
			$tr = $target.children().eq(index);

		if($parent.children().length > 1) {

			// Get next items
			var newIndex = ($tr.prev().length > 0) ? (index -1) : (index+1);

			// Select new transition
			$parent.children().eq(newIndex).click();
		}

		// Remove transition
		$tr.remove();
		$el.remove();

		// Add notification if needed
		if($parent.children().length === 0) {
			$parent.next().removeClass('ls-hidden');
		} else {
			$parent.next().addClass('ls-hidden');
		}
	},



	importTransition: function(el) {

		// Get transition object
		var $el = jQuery(el),
			index = jQuery(el).data('key'),
			section = jQuery(el).closest('section').index(),
			type = (section === 0) ? '2d' : '3d',
			transition = layerSliderTransitions['t'+type][(index-1)],
			$list = jQuery('.ls-transitions-sidebar ul[data-type="'+type+'"'),
			$target = jQuery('.ls-tr-list-'+type);

		// Hide transition gallery
		window.lsHideTransition();
		LS_TransitionGallery.closeTransitionGallery();

		setTimeout(function() {

			// Add transition entry
			jQuery('.ls-transitions-sidebar h3').eq(section).find('a:last-child').click();

			// Change name
			$list.find('li:first-child input').val(
				jQuery('<div/>').html(transition.name).text()
			);

			// Update form items
			lsTrBuilder.updateControls($target.children('.active'), transition);
			lsTrBuilder.startPreview(true);

		}, 1000);
	},



	updateControls: function($tr, transition) {

		jQuery.each(transition, function(idx, val) {

			var $area;

			// General options
			if(idx === 'cols' || idx === 'rows') {
				if(jQuery.type(val) === 'array') {
					val = val.join(',');
				}
				jQuery(':input[name="'+idx+'"]', $tr).val(val);

			// Animation objects
			} else if(jQuery.type(val) === 'object') {
				$area = jQuery('tbody.'+idx, $tr);

				// Iterate over animation objects
				jQuery.each(val, function(aIdx, aVal) {

					if(jQuery.type(aVal) !== 'object') {
						jQuery(':input[name="'+aIdx+'"]', $area).val(aVal);

					} else {

						$area.prev().find(':checkbox').click();
						jQuery.each(aVal, function(tIdx, tVal) {

							// Remove previous transition properties
							jQuery('.ls-tr-tags', $area).empty();

							// Add transition property
							jQuery('.ls-tr-tags', $area).append( jQuery('<li>')
								.append( jQuery('<p>')
									.append('<span>'+tIdx+'</span><input value="'+tVal+'" name="'+tIdx+'">')
								).append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-dismiss' }) )
							);
						});
					}
				});
			}

		});
	},



	toggleTableGroup: function(el) {

		var $tbody = jQuery(el).closest('thead').next();
		if($tbody.hasClass('ls-builder-collapsed')) {
			$tbody.removeClass('ls-builder-collapsed');
		} else {
			$tbody.addClass('ls-builder-collapsed');
		}
	},



	addProperty: function(el) {

		// Gather info
		var list = jQuery(el).parent().prev(),
			select = jQuery(el).next(),
			title = select.children(':selected').text(),
			name = select.children(':selected').val().split(',')[0],
			value = select.children(':selected').val().split(',')[1];

		// Build tag
		list.append( jQuery('<li>')
			.append( jQuery('<p>')
				.append('<span>'+title+'</span><input value="'+value+'" name="'+name+'">')
			).append( jQuery('<a>', { 'href' : '#', 'class' : 'dashicons dashicons-dismiss' }) )
		);

		this.startPreview(true);
	},



	removeProperty: function(el) {
		jQuery(el).closest('li').remove();
		this.startPreview(true);
	},



	serializeTransitions: function(el) {

		// Prepare transition object
		layerSliderCustomTransitions = { t2d: [], t3d: [] };

		jQuery('.ls-tr-options').children().each(function() {

			// Iterate over 3D transitions
			jQuery('.ls-transition-item', this).each(function(index) {

				// Get working transition object & store its name
				var trType = jQuery(this).closest('.ls-tr-list-2d').length ? '2d' : '3d';
				var tr = layerSliderCustomTransitions['t'+trType][index] = {};
				tr.name = jQuery('.ls-transitions-sidebar ul.'+trType+' li:eq('+index+') input').val();

				// Iterate over other sections
				jQuery(this).find('tbody:gt(0)').each(function() {

					// Skip 'before' and 'after' transitions if they aren't set
					if( jQuery(this).prev().find(':checkbox:not(:checked)').length ) {
						return;
					}

					// Get section name
					var area, section = jQuery(this).attr('class');
					if( section ) {
						section = section.split(' ')[0];
						tr[ section ] = {};
						area = tr[ section ];
					} else {
						area = tr;
					}


					// Iterate over the fields
					var trOptions = false,
						elements = jQuery(this).find('input,select'),
						$this, name, val;

					for( var c = 0; c < elements.length; c++) {

						// Get input details
						$this = jQuery( elements[c] );
						name = $this.attr('name');
						val = $this.val();

						// Skip iteration if it's
						// a GUI only element
						if( ! name ) { return; }

						// Get working area
						if( ! trOptions && $this.closest('tr.transition').length) {
							trOptions = true;
							area = area.transition = {};
						}

						// Set values
						if($this.is(':checkbox')){
							if($this.prop('checked')) {
								area[name] = $this.val();
							}
						} else if(val !== '') {

							if( (name === 'rows' && val.indexOf(',') !== -1) ||
								(name === 'cols' && val.indexOf(',') !== -1)) {
								val = val.split(',');
								area[name] = [parseInt(jQuery.trim(val[0])), parseInt(jQuery.trim(val[1]))];
							} else {
								area[name] = jQuery.isNumeric(val) ? Number(val) : val;
							}
						}
					}
				});


			});
		});
	},



	stopPreview: function() {

		jQuery('.ls-transition-item.active').each(function() {

			var $item = jQuery(this);

			if($item.hasClass('playing')) {
				$item.removeClass('playing');
				var $parent = jQuery($item).find('.ls-builder-preview');

				jQuery('.transitionpreview', $parent).layerSlider( 'destroy', true );
				$parent.append(
					jQuery('<img>', { 'src' : lsTrImgPath + 'sample_slide_1.png' })
				);
			}
		});
	},



	startPreview: function(forceStart) {

		var $item = jQuery('.ls-transition-item.active');

		// Check playing status
		if($item.hasClass('playing')) {
			this.stopPreview();

			if(!forceStart) {
				return;
			}
		}

		// Serialize
		this.serializeTransitions();
		$item.addClass('playing');

		// Get transition details
		var index = $item.index(),
			trType = $item.closest('.ls-tr-list-3d').length ? '3d' : '2d',
			trObj = layerSliderCustomTransitions['t'+trType+''][index];

		// Try preview
		try {

			jQuery($item).find('.ls-builder-preview').empty();
			window.lsStartTransitionPreview( jQuery('.ls-builder-preview', $item ), {
				type: 'fixedsize',
				width: 300,
				height: 150,
				transitionType: trType,
				transitionObject: trObj,
				imgPath: lsTrImgPath,
				skinsPath: pluginPath+'layerslider/skins/',
				sliderFadeInDuration: 0,
				delay: 500,
			});

		// Aw, Snap! Something went wrong.
		// Stop preview and display error message.
		} catch(err) {

			//alert('Oops, something went wrong, please check your transitions settings and enter valid values. Error: '+err);
			this.stopPreview();
		}
	},



	save: function(el) {

		// Temporary disable submit button
		jQuery('.ls-publish').addClass('saving').find('button').text(LS_l10n.saving).attr('disabled', true);

		// Serialize & store JSON
		this.serializeTransitions();
		jQuery(el).children('input[name="ls-transitions"]').val(
			JSON.stringify( layerSliderCustomTransitions )
		);

		// Post
		jQuery.post( window.location.href, jQuery(el).serialize(), function() {

			// Give feedback
			jQuery('.ls-publish').removeClass('saving').addClass('saved').find('button').text(LS_l10n.saved);

			// Re-enable the button
			setTimeout(function() {
				jQuery('.ls-publish').removeClass('saved').find('button').attr('disabled', false).text(LS_l10n.save);
			}, 2000);
		});
	}
};

var LS_TransitionGallery = {

	openTransitionGallery: function() {

		kmw.modal.open({
			content: '#tmpl-ls-transition-modal',
			minWidth: 980,
			maxWidth: 980,
			maxHeight: '100%',

			onBeforeClose: function() {

				if( window.lsHideTransition ) {
					window.lsHideTransition();
				}
			}
		});

		// Append transitions
		LS_TransitionGallery.appendTransition(0, '', '2d_transitions', layerSliderTransitions.t2d);
		LS_TransitionGallery.appendTransition(1, '', '3d_transitions', layerSliderTransitions.t3d);

		// Select proper tab
		jQuery('#ls-transition-window .filters li.active').click();
	},


	closeTransitionGallery: function() {

		kmw.modal.close();
	},


	appendTransition: function(index, title, tbodyclass, transitions) {

		// Append new section
		var section = jQuery('#ls-transitions-list section').eq(index).empty();

		if(title) {
			section.append('<h3>'+title+'</h3>');
		}

		if( transitions && transitions.length ) {
			for( c = 0; c < transitions.length; c++ ){
				section.append( jQuery( '<div class="tr-item"data-key="' + ( c + 1 ) + '"><span><i>' + ( c + 1 ) + '</i><i class="dashicons dashicons-yes"></i></span><span>' + transitions[c].name + '</span></div>' ) );
			}
		}
	}
};


jQuery(document).ready(function() {

	// Transition select
	jQuery('.ls-transitions-sidebar').on('click', 'li', function() {
		lsTrBuilder.selectTransition(this);

	// Add transition
	}).on('click', '.ls-add-transition', function(e) {
		e.preventDefault();
		lsTrBuilder.addTransition(this);

	// Import transition
	}).on('click', '.ls-import-transition', function(e) {
		e.preventDefault();
		LS_TransitionGallery.openTransitionGallery();
		if( jQuery(this).parent().index() !== 0 ) {
			jQuery('#ls-transition-window .filters li:last-child').click();
		}

	// Remove transition
	}).on('click', '.remove', function(e) {
		e.preventDefault(); e.stopPropagation();
		lsTrBuilder.removeTransition(this);

	// Select first transition
	}).find('li').eq(0).click();



	// Collapsable toggles
	jQuery('.ls-tr-builder').on('input change click', ':input', function() {

		// Bail out early if there was a click event
		// fired on a non-checkbox form item
		if(event.type === 'click' && !jQuery(this).is(':checkbox')) {
			return false;
		}

		// Prevent triggering the change event
		// on non-select form items
		if(event.type === 'change' && !jQuery(this).is('select')) {
			return false;
		}

		// Everything OK, restart preview
		clearTimeout(lsTrBuilder.timeout);
		lsTrBuilder.timeout = setTimeout(function() {
			lsTrBuilder.startPreview(true);
		}, (event.type === 'input') ? 500 : 50);


	}).on('click', '.ls-builder-collapse-toggle', function() {
		lsTrBuilder.toggleTableGroup(this);

	// Add property
	}).on('click', '.ls-tr-add-property a', function(e) {
		e.preventDefault();
		lsTrBuilder.addProperty(this);

	// Remove property
	}).on('click', '.ls-tr-tags a', function(e) {
		e.preventDefault();
		lsTrBuilder.removeProperty(this);

	}).on('click', '.ls-builder-preview-button button', function(e) {
		e.preventDefault();
		lsTrBuilder.startPreview();
	});

	// Form submit
	jQuery('#ls-tr-builder-form').submit(function(e) {
		e.preventDefault();
		lsTrBuilder.save(this);
	});


	// Show/Hide transition
	jQuery(document).on('mouseenter', '#ls-transitions-list .tr-item', function() {
		window.lsShowTransition(this);
	}).on('mouseleave', '#ls-transitions-list .tr-item', function() {
		window.lsHideTransition();

	// Close transition gallery
	}).on('click', '#ls-transition-window header b', function(e) {
		e.preventDefault();
		LS_TransitionGallery.closeTransitionGallery();

	// Transitions gallery
	}).on('click', '#ls-transition-window .filters li', function() {

		// Update navigation
		jQuery(this).addClass('active').siblings().removeClass('active');

		// Update view
		jQuery('#ls-transitions-list section').removeClass('active');
		jQuery('#ls-transitions-list section').eq( jQuery(this).index() ).addClass('active');

	// Import transition
	}).on('click', '#ls-transitions-list .tr-item', function(e) {
		e.preventDefault();
		lsTrBuilder.importTransition(this);
	});

	// Transitions draggable
	var dragIndex;
	jQuery('.ls-transitions-sidebar ul').sortable({
		handle : 'span.dashicons-menu',
		containment : 'parent',
		tolerance : 'pointer',
		axis : 'y',
		start: function(event, ui) {
			dragIndex = jQuery(ui.item).index();
		},
		stop: function(event, ui) {
			var	$item = jQuery(ui.item),
				$list = $item.parent(),
				type = $list.data('type'),
				$target = jQuery('.ls-tr-list-'+type),
				index = $item.index(),
				$tr = $target.children().eq(dragIndex);

			if(index === 0) { $tr.prependTo($target); }
				else { $tr.insertAfter( $target.children().eq(index) ); }
		}
	});
});
