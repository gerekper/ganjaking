jQuery(document).ready(function($){
	// Tokens for Entry.
	$('.searchwp-diagnostics-indexed-tokens button').click(function(e) {
		e.preventDefault();
		$('.searchwp-diagnostics-indexed-tokens-tool').addClass('searchwp-diagnostics-loading');
		setTimeout(function(){
			$.post(ajaxurl, {
				_ajax_nonce: $('.searchwp-diagnostics-indexed-tokens').data('nonce'),
				action: 'searchwp_get_indexed_tokens',
				source: $('.searchwp-diagnostics-indexed-tokens select').val(),
				id: $('.searchwp-diagnostics-indexed-tokens input').val()
			}, function(response) {
				$('.searchwp-diagnostics-indexed-tokens-tool').removeClass('searchwp-diagnostics-loading');
				if (response.success) {
					var results = '<p class="description">Found tokens: ' + parseInt(response.data.length, 10) + '</p>';
					if (response.data.length) {
						results += '<ul>';
						for (var i = 0; i < response.data.length; i++) {
							results += '<li>' + response.data[i] + '</li>';
						}
						results += '</ul>';
					}
					$('.searchwp-diagnostics-indexed-tokens-display').html(results);
				} else {
					alert('There was an error retrieving tokens.');
				}
			});
		}, 300);
	});

	// Unindexed Entries.
	$('.searchwp-diagnostics-unindexed-entries button').click(function(e) {
		e.preventDefault();
		$('.searchwp-diagnostics-unindexed-entries-tool').addClass('searchwp-diagnostics-loading');
		setTimeout(function(){
			$.post(ajaxurl, {
				_ajax_nonce: $('.searchwp-diagnostics-unindexed-entries').data('nonce'),
				action: 'searchwp_get_unindexed_entries',
				source: $('.searchwp-diagnostics-unindexed-entries select').val()
			}, function(response) {
				$('.searchwp-diagnostics-unindexed-entries-tool').removeClass('searchwp-diagnostics-loading');
				if (response.success) {
					var results = '<p class="description">Unindexed IDs: ' + parseInt(response.data.length, 10) + '</p>';

					if (response.data.length) {
						results += '<ul>';
						for (var i = 0; i < response.data.length; i++) {
							results += '<li>' + response.data[i] + '</li>';
						}
						results += '</ul>';
					}
					$('.searchwp-diagnostics-unindexed-entries-display').html(results);
				} else {
					alert('There was an error retrieving the data.');
				}
			});
		}, 300);
	});
});