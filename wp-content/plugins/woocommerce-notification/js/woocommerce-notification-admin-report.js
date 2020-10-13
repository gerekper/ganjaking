'use strict';
jQuery(document).ready(function () {
	if (jQuery('.datepicker').length > 0) {
		jQuery('.datepicker').datepicker({
			inline         : true,
			showOtherMonths: true,
			maxDate        : '0'
		}).datepicker('widget').wrap('<div class="ll-skin-latoja"/>');

	}
	if (jQuery('#myChart').length) {
		var ctx = jQuery("#myChart");
		var data = {
			labels  : woo_notification_labels,
			datasets: [
				{
					label                    : woo_notification_label,
					fill                     : false,
					lineTension              : 0.1,
					backgroundColor          : "rgba(75,192,192,0.4)",
					borderColor              : "rgba(75,192,192,1)",
					borderCapStyle           : 'butt',
					borderDash               : [],
					borderDashOffset         : 0.0,
					borderJoinStyle          : 'miter',
					pointBorderColor         : "rgba(75,192,192,1)",
					pointBackgroundColor     : "#fff",
					pointBorderWidth         : 1,
					pointHoverRadius         : 5,
					pointHoverBackgroundColor: "rgba(75,192,192,1)",
					pointHoverBorderColor    : "rgba(220,220,220,1)",
					pointHoverBorderWidth    : 2,
					pointRadius              : 1,
					pointHitRadius           : 10,
					data                     : woo_notification_data,
				}
			]

		};
		var myLineChart = new Chart(ctx, {
			type: 'line',
			data: data
		});
	}
});