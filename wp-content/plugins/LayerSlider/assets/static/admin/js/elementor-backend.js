( function( $ ) {

	LS_Widget.chooseSlider = function() {
		LS_SliderLibrary.open({
			onChange: function(sliderData) {
				LS_Widget.destroySlider();

				$('input[data-setting=identifier]')
					.val(sliderData.id)
					.trigger('input')
				;
			}
		});
	};

	LS_Widget.reloadSlider = function() {
		var $identifier = $('input[data-setting=identifier]'),
			id = $identifier.val();

		this.destroySlider();

		$identifier
			.val(~id.indexOf('.') ? parseInt(id) : id + '.')
			.trigger('input')
		;
	};

	LS_Widget.openEditor = function() {
		var lsId = $('input[data-setting=identifier]').val();

		kmw.modal.open({
			title: LS_Widget.i18n.modalTitle,
			modalClasses: 'ls-editor-modal ls-editor-loading',
			maxWidth: '100%',
			maxHeight: '100%',
			spacing: 20,
			padding: 20,
			content: '<iframe></iframe>',
			animationIn: 'scale',
			overlaySettings: {
				animationIn: 'fade'
			},
			reload: false,
			onOpen: function( modal ) {
				this.$iframe = modal.$element.find('iframe')
					.attr('src', LS_Widget.editorUrl + lsId + '&ls-embed=1')
					.on('load', $.proxy( this, 'onLoad' ) );
			},
			onLoad: function( modal ) {
				this.win = this.$iframe.contents()[0].defaultView;
				this.win.jQuery(this.win.document).ajaxSuccess($.proxy(this, 'onAjaxSuccess'));
			},
			onAjaxSuccess: function( e, xhr, args, res ) {
				if (args.data && ~args.data.indexOf('action=ls_save_slider') && '{"status":"ok"}' === res) {
					this.reload = true;
				}
			},
			onBeforeClose: function( e ) {
				var close = this.win && this.win.LS_editorIsDirty
					? confirm(LS_Widget.i18n.ChangesYouMadeMayNotBeSaved)
					: true
				;
				if (close && this.win && this.win.LS_editorIsDirty) {
					this.win.LS_editorIsDirty = false;
				}
				return close;
			},
			onClose: function() {
				this.reload && LS_Widget.reloadSlider();
			}
		});
	};

	LS_Widget.destroySlider = function() {
		var $ = elementor.$previewContents[0].defaultView.jQuery,
			id = elementor.panel.currentView.content.currentView.model.id;

		$('.elementor-element-' + id + ' .ls-container').layerSlider('destroy');
	};

	$('html').on('input.ls', '.ls-overrides ~ .elementor-control :input', function onChangeOverride() {
		LS_Widget.destroySlider();
	});

})( Backbone.$ );
