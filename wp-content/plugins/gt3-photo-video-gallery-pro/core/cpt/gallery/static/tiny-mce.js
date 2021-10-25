(function () {
	window.tinymce && tinymce && tinymce.PluginManager && tinymce.PluginManager.add('gt3_cpt_buttons',
		function (editor, url) {
			editor.addButton('gt3_cpt_add_gallery', {
				text: '  Insert GT3 Gallery',
				type: 'button',
				icon: 'wp-media-library',
				value: '[gt3-gallery]',
				onclick: function () {
					editor.insertContent(this.value());
				},
				/*menu: [
					{
						text: 'Gallery',
						icon: 'image',
						value: '[gt3-gallery]',
						onclick: function () {
							editor.insertContent(this.value());
						}
					},
				]*/
			});
		});
})();
