"use strict";

(function() {
	let previewButton = document.getElementById('dce-preview-pdf');
	let errorDiv = document.getElementById('dce-preview-error');

	let ajaxUrl = previewButton.dataset.url;
	let ajaxAction = previewButton.dataset.action;
	previewButton.addEventListener( 'click', async () => {
		errorDiv.style.display = 'none';
		let codeMirror = document.getElementsByClassName('CodeMirror')[0];
		let form = document.getElementById('post');
		// Refresh the textarea with the value in the code editor.
		codeMirror.CodeMirror.refresh();
		codeMirror.CodeMirror.save();
		let data = new FormData(form);
		data.set('action', ajaxAction);
		let response;
		try {
			response = await fetch(ajaxUrl, {
				method: 'POST',
				body: new URLSearchParams(data)
			});
		} catch (e) {
			errorDiv.style.display = 'block';
			errorDiv.textContent  = e.message;
			return;
		}
		if (response.headers.get('Content-Type') !== 'application/pdf') {
			const json = await response.json();
			errorDiv.style.display = 'block';
			errorDiv.textContent  = json.data.message;
			return;
		}
		let blob = await response.blob()
		let link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = 'PDF Preview';
		link.click();
	})

	const getSelect2Options = (postType) => {
		return {
			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					let args = {
						q: params.term, // search query
						action: 'dce_get_posts', // AJAX action for admin-ajax.php
					}
					if (postType) {
						args.dce_post_type = postType;
					}
					return args;
				},
				processResults: function( data ) {
					var options = [];
					if ( data ) {
						// data is the array of arrays, and each of them contains ID and the Label of the option
						jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[0], text: text[1]	} );
						});
					}
					return {
						results: options
					};
				},
				cache: true
			},
			minimumInputLength: 3 // the minimum of symbols to input before perform a search
		}
	}

	// Code from https://rudrastyh.com/wordpress/select2-for-metaboxes-with-ajax.html
	// initialize Select2 with ajax for searching posts.
	jQuery(function($){
		// multiple select with AJAX search
		$('#dce-preview-post').select2( getSelect2Options( false ));
		$('#dce-html-template-id').select2( getSelect2Options( 'elementor_library' ));
	});

	window.addEventListener('load', (_) => {
		let isTemplate = document.getElementById('dce-html-is-template');
		let templateId = document.getElementById('dce-html-template-section');
		let code = document.getElementById('dce-html-code-section');
		if (isTemplate.checked) {
			code.style.display = 'none';
			templateId.style.display = 'block';
		} else {
			code.style.display = 'block';
			templateId.style.display = 'none';
		}
		isTemplate.addEventListener('change', function() {
			if (this.checked) {
				code.style.display = 'none';
				templateId.style.display = 'block';
			} else {
				code.style.display = 'block';
				templateId.style.display = 'none';
			}
		})
	});
})();
