jQuery(function($) {

	var $selectedRevision 	= $('#ls-revisions-selected'),
		$revisionId 		= $('#revision-id');


	$('#ls-revisions-range').on('input', function() {

		// Update data source
		window.selectedRevision = window.lsRevisions[ ($(this).val()-1) ];
		window.lsSliderData = window.selectedRevision.data;

		if( (LS_activeSlideIndex+1) > window.lsSliderData.layers.length ) {
			LS_activeSlideIndex = window.lsSliderData.layers.length - 1;
		}

		window.LS_activeSlideData = window.lsSliderData.layers[ LS_activeSlideIndex ];
		window.LS_activeLayerIndexSet = [0];

		// Update revision details
		$('img', $selectedRevision).attr('src', window.selectedRevision.avatar);
		$('.author', $selectedRevision).text( window.selectedRevision.nickname );
		$('.time-diff', $selectedRevision).text( window.selectedRevision.time_diff );
		$('.date', $selectedRevision).text( window.selectedRevision.created );

		// Update revision ID
		$revisionId.val( window.selectedRevision.id );

		// Update UI
		LayerSlider.rebuildSlides();
		LayerSlider.stopSlidePreview();
		LayerSlider.generatePreview();
	});

	$('.ls-revisions-options').click(function(e) {
		e.preventDefault();

		kmw.modal.open({
			content: '#tmpl-revisions-options',
			minWidth: 500
		});


		$('#ls-revisions-modal-window input:checkbox').customCheckbox();

		$('#ls-revisions-modal-window .ls-checkbox').click(function(e) {

			if( ! $(this).hasClass('off') ) {
				if( ! confirm( $(this).data('warning') ) ) {
					e.preventDefault();
					return false;
				}
			}
		});
	});
});