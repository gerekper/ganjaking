jQuery(document).ready(function ($) {
	/*
	 Progressive enhancement.  If javascript is enabled we change the body class.  Which in turn hides the checkboxes with css.
	 */
	$('.woocommerce-roles-wrap').attr("class", "dynamic-pricing-js");
	$('.woocommerce-category-wrap').attr("class", "dynamic-pricing-js");

	/*
	 Add toggle switch after each checkbox.  If checked, then toggle the switch.
	 */
	$('.checkbox', '.dynamic-pricing-js').after(function () {
		if ($(this).is(":checked")) {
			return "<a href='#' class='toggle checked' ref='" + $(this).attr("id") + "'></a>";
		} else {
			return "<a href='#' class='toggle' ref='" + $(this).attr("id") + "'></a>";
		}


	});

	/*
	 When the toggle switch is clicked, check off / de-select the associated checkbox
	 */
	$('.toggle', '.dynamic-pricing-js').click(function (e) {
		var checkboxID = $(this).attr("ref");
		var checkbox = $('#' + checkboxID);

		if (checkbox.is(":checked")) {
			checkbox.removeAttr("checked");

		} else {
			checkbox.attr("checked", "true");
		}
		$(this).toggleClass("checked");

		e.preventDefault();

	});


	$('#woocommerce-pricing-rules-wrap').on('click', 'h4.first', function () {
		$(this).parent().toggleClass('closed');
	});

});