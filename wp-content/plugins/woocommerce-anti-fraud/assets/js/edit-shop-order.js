(function ($) {
	$(window).ready(function () {
		$('.knob').knob();

		$({value: 0}).animate({value: $('.knob').attr('rel')}, {
			duration: 1000,
			easing  : 'swing',
			step    : function () {
				$('.knob').val(Math.ceil(this.value)).trigger('change');
			}
		});

		$('.woocommerce-af-risk-failure-list ul').hide();

		$('.woocommerce-af-risk-failure-list-toggle').click(function(){
			$('.woocommerce-af-risk-failure-list ul').slideToggle();
			var text = $(this).text();
			$(this).text( $(this).data('toggle') );
			$(this).data('toggle', text);
		});
	});
})(jQuery);