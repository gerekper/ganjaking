jQuery(document).ready(function($){
	
	var modalHolder=null;
	var modalDom=null;
	var modalOpen=false;
	
	function feedbackCloseModal(){
		modalOpen = false;
		if (modalHolder&&modalHolder.empty()) {
			jQuery("body").removeClass("unlimited-elements--modal-open");
		}
	}
	
	function feedbackOpenModal(id, href){
		
		modalHolder = jQuery(".unlimited-elements__modal-holder");
		modalDom = jQuery('#'+id).html();
		if (href) 
			modalDom = modalDom.replace('{{skip}}', href);
		
		modalHolder.empty();
		modalHolder.append(modalDom);
		
		modalOpen = true;
		
		$("body").addClass("unlimited-elements--modal-open");
	};
	
	
	jQuery(document).on('click', '#the-list [data-slug="unlimited-elements-for-elementor"] span.deactivate a', function(event){
		event.preventDefault();
		feedbackCloseModal();
		feedbackOpenModal("tmpl-unlimited-elements__plugin-feedback", $(this).attr('href'));
	});
	
	
	jQuery('body').click(function(event) {
		if ($(this).hasClass('unlimited-elements--modal-open') && !$(event.target).closest('.unlimited-elements__modal-inner-bg').length) {
			feedbackCloseModal();
		}
	});
	
	
	jQuery(document).on('click', '.unlimited-elements__modal-close', function(event){
		event.preventDefault();
		feedbackCloseModal();
	});
	
	
	jQuery(document).on('click', '.unlimited-elements__disable-submit', function(event){
		
		event.preventDefault();
		var $this = $(this);
		var modal = $this.closest('.unlimited-elements__modal');
		
		$this.width($this.width()).text("Submitting...").prop("disabled",false);
		
		var answer_elem = modal.find("input[name='elements_deactivation_reason']:checked");
		var answer = modal.find('label[for="'+answer_elem.attr('id')+'"]').text();
		var answer_text = modal.find("input[name='elements_deactivation_reason_"+answer_elem.val()+"']").val();
		
		var urlAction = $this.data('action');
		var nonce = $this.data("nonce");
		
		var data = {};
		
		if(!answer)
			answer = "no answer";
		
		data.action = "unlimited_elements_feedback";
		data.answer = answer;
		data.answer_text = answer_text;
		data.nonce = nonce;
		
		var urlDisable = modal.find('.unlimited-elements__disable-skip').attr("href");
		
		jQuery.ajax({
			type: "POST",
			url: urlAction,
			data: data,
			success: function (data) {
				
				window.location.href = urlDisable;
			}
		});
	});

})