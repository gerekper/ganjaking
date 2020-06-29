jQuery(document).ready(function($) {

	// In some rare cases the globally loaded LS_MCE_l10n
	// variable might not be available due to plugins making
	// changes in the WP script queue. The below makes sure
	// that we can at least avoid undef JS errors.
	if( typeof LS_MCE_l10n === 'undefined' ) {
		LS_MCE_l10n = {};
	}

	tinymce.create('tinymce.plugins.layerslider_plugin', {

		init : function(ed, url) {

			// Close event
			$(document).on('click', '.mce-layerslider-overlay', $.proxy(function() {
				this.closePopup();
			}, this));

			$(document).on('click', '.mce-layerslider-window .close-modal', $.proxy(function(e) {

				e.preventDefault();
				this.closePopup();
			}, this));

			$(document).on('keydown', $.proxy(function(e) {

				if( e.which === 27 ) {
					this.closePopup();
				}

			}, this)).on('keyup', $.proxy(function(e) {


			}));

			// Select slider
			$(document).on('click', '.mce-layerslider-window .slider-item', $.proxy(function(e) {
				this.selectSlider( e, $(e.currentTarget) );
			}, this));


			// Insert slider
			$(document).on('click', '.mce-layerslider-insert-button', $.proxy(function(e) {
				this.insertSlider();
				this.closePopup();
			}, this));

			// Button props
			ed.addButton('layerslider_button', {
				title : LS_MCE_l10n.MCEAddLayerSlider,
				cmd : 'layerslider_insert_shortcode',
				onClick : $.proxy(this.openPopup, this)
			});
		},


		openPopup : function(e) {

			// Get the popup
			var $modal = $('.mce-layerslider-window');

			// If the popup isn't already open, create it and load its content using ajax
			if( ! $('.mce-layerslider-window').length ) {

				var modalMarkup =
				'<div class="mce-layerslider-window" tabindex="-1">\
					<a href="#" class="close-modal"></a>\
					<h3 class="header" tabindex="0">'+LS_MCE_l10n.MCEInsertSlider+'</h3>\
					<div class="inner"></div>\
					<div class="footer">\
						<div class="options">\
							<strong>'+LS_MCE_l10n.MCEEmbedOptions+'</strong>\
							<span>'+LS_MCE_l10n.MCEStartingSlide+'</span>\
							<input type="text" data-option="firstslide" placeholder="'+LS_MCE_l10n.MCENoOverride+'">\
						</div>\
						<button class="button button-primary mce-layerslider-insert-button" disabled>'+LS_MCE_l10n.MCEInsertButton+'</button>\
					</div>\
				</div>';

				// Prepend modal
				$modal = $( modalMarkup ).prependTo('body');
				var $inner = $('.inner', $modal);

				// Set focus on the window to allow keyboard shortcuts
				setTimeout(function() {
					$modal.focus();
				}, 100);

				// Add overlay
				$('<div>', { 'class' : 'mce-layerslider-overlay'}).prependTo('body');

				var itemMarkup =
				'<div class="slider-item">\
					<div class="slider-item-wrapper">\
						<div class="preview">\
							<div class="no-preview">\
								<h5>'+LS_MCE_l10n.MCENoPreview+'</h5>\
								<small>'+LS_MCE_l10n.MCENoPreviewText+'</small>\
							</div>\
						</div>\
						<div class="info">\
							<div class="name"></div>\
						</div>\
					</div>\
					<div class="selection">\
						<span class="dashicons dashicons-yes"></span>\
					</div>\
				</div>';

				// Get sliders
				$.getJSON(ajaxurl, { action : 'ls_get_mce_sliders' }, function(data) {
					$.each(data, function(index, item) {
						var $item = $(itemMarkup);

						$item.data({
							'id': item.id,
							'slug': item.slug
						});

						if( item.preview ) {
							$('.preview', $item).empty().css({
								'background-image': 'url('+item.preview+')'
							});
						}

						$('.name', $item).html( item.name );

						$item.appendTo( $inner );
					});

				});
			}
		},

		searchSlider : function() {

		},

		selectSlider : function( event, $item ) {

			// Add to multi-select
			if( event.ctrlKey || event.metaKey ) {
				$item.toggleClass('selected');

			// Single select
			} else {
				$item.addClass('selected').siblings().removeClass('selected');
			}

			// Enable insert button
			$('.mce-layerslider-insert-button').attr('disabled', false);
		},


		insertSlider: function() {

			// Get modal window
			var $modal = $('.mce-layerslider-window');

			// Get selected element
			$('.slider-item.selected', $modal).each(function() {

				// Get options
				var $item 		= $(this),
					sliderId 	= $item.data('id'),
					sliderSlug 	= $item.data('slug'),
					embedId 	= sliderSlug ? sliderSlug : sliderId,
					firstSlide 	= $('input[data-option="firstslide"]', $modal).val();

				if( firstSlide ) {
					firstSlide = ' firstslide="'+firstSlide+'"';
				}

				tinymce.execCommand('mceInsertContent', false, '[layerslider id="'+embedId+'"'+firstSlide+']');
			});
		},

		closePopup : function() {

			if($('.mce-layerslider-window').length) {
				$('.mce-layerslider-overlay').remove();
				$('.mce-layerslider-window').remove();
			}
		}
	});

	// Add button
	tinymce.PluginManager.add('layerslider_button', tinymce.plugins.layerslider_plugin);
});
