(function($) {
	$(document).on('tinymce-editor-setup', function(event, editor) {

		if( void 0 === wis_shortcodes ) {
			console.log('Unknown error (wis).');
			return;
		}

		if( $.isEmptyObject(wis_shortcodes) ) {
			return;
		}

		editor.settings.toolbar1 += ',wis_insert_button';

		var menu = [];

		$.each(wis_shortcodes, function(index, item) {
			menu.push({
				text: item.title,
				value: item.id,
				onclick: function() {
					var selected_content = editor.selection.getContent();

					if( '' === selected_content ) {
						editor.selection.setContent('[jr_instagram id="' + item.id + '"]');
					} else {
						editor.selection.setContent('[jr_instagram id="' + item.id + '"]');
					}
				}
			});
		});

		editor.addButton('wis_insert_button', {
			title: 'WIS',
			type: 'menubutton',
			icon: 'icon wis-shortcode-icon',
			menu: menu
		});

	});
})(jQuery);