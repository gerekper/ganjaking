jQuery( document ).ready( function ( $ ) {
	$("#woocommerce_multiple_shipping_checkout_datepicker").change(function() {
		if ( $(this).is(":checked") ) {
			$(":input.show-if-checkout-datepicker").removeAttr("disabled");
		} else {
			$(":input.show-if-checkout-datepicker").attr("disabled", true);
		}
	}).change();

	$(".datepicker-div").datepicker({
		dateFormat: "mm-d-yy",
		showButtonPanel: true,
		onSelect: function(date) {
			var select = $(this).parents("fieldset").find("select.excluded-list");

			select
				.append("<option selected value="+date+">"+date+"</option>")
				.trigger("change");
		}
	}).hide();

	$("#woocommerce_multiple_shipping_checkout_exclude_dates").on("select2-focus", function() {
		show_excluded_dates_calendar();
	});

	$("#woocommerce_multiple_shipping_checkout_notes").change(function() {
		if ( $(this).is(":checked") ) {
			$("#woocommerce_multiple_shipping_checkout_notes_limit").parents("tr").show();
		} else {
			$("#woocommerce_multiple_shipping_checkout_notes_limit").parents("tr").hide();
		}
	}).change();

	$("#woocommerce_multiple_shipping_checkout_datepicker").change(function() {
		if ( $(this).is(":checked") ) {
			$(".show-if-checkout-datepicker").parents("tr").show();
		} else {
			$(".show-if-checkout-datepicker").parents("tr").hide();
		}
	}).change();

	$("#show_excluded_dates_calendar").click(function() {
		show_excluded_dates_calendar();
	});

	$("#hide_excluded_dates_calendar").click(function() {
		hide_excluded_dates_calendar();
	});

	function show_excluded_dates_calendar() {
		$(".datepicker-div").show();
		$("#show_excluded_dates_calendar").hide();
		$("#hide_excluded_dates_calendar").show();
	}

	function hide_excluded_dates_calendar() {
		$(".datepicker-div").hide();
		$("#show_excluded_dates_calendar").show();
		$("#hide_excluded_dates_calendar").hide();
	}
} );
