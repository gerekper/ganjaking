(function($) {

	$(function() {
		
		$('.revaddon-dismiss-notice').off().each(function() {
			
			var noticeId = this.dataset.noticeid,
				storedNoticeId;
				
			try {
				storedNoticeId = localStorage.getItem(this.dataset.addon);
			}
			catch(e) {
				storedNoticeId = false;
			}
				
			if(noticeId !== storedNoticeId) {
				$(this).closest('.revaddon-notice').show();
			}
			else {
				$(this).closest('.revaddon-notice').hide();
			}
			
			// localStorage.removeItem(this.dataset.addon);
			
		}).on('click', function() {
			
			var $this = $(this);
			$this.closest('.revaddon-notice').fadeOut(400, function() {$this.hide();});
			
			try {
				localStorage.setItem(this.dataset.addon, this.dataset.noticeid);
			}
			catch(e) {}
			
		});
		
	});
	
})(jQuery);