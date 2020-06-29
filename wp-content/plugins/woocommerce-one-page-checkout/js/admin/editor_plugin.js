/**
 * TinyMCE plugin
 */
( function () {
	tinymce.PluginManager.add( 'wcopc_shortcode_button', function( editor, url ) {
		var ed = tinymce.activeEditor;
		editor.addButton('wcopc_shortcode_button',{
			title: ed.getLang( 'wcopc.one_page_checkout' ),
			icon: 'wcopc dashicons-cart',
			onclick: function() {
				editor.windowManager.open( {
					title: ed.getLang( 'wcopc.one_page_checkout' ),
					id: 'wcopc_shortcode_dialog',
					width: 360,
					height: 170,
					url: ajaxurl + '?action=one_page_checkout_shortcode_iframe'
				}, {
					shortcode: ed.getLang( 'wcopc.one_page_checkout_shortcode' ),
				});
				jQuery('#wcopc_shortcode_dialog iframe').attr({width: '100%'}).iFrameResize([{heightCalculationMethod: 'lowestElement'}]);
			}
		});
	});
})();