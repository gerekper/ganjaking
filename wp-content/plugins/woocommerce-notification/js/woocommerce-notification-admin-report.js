jQuery(document).ready(function ($) {
	'use strict';

	if ($('.datepicker').length > 0) {
		$('.datepicker').datepicker({
			inline         : true,
			showOtherMonths: true,
			maxDate        : '0'
		}).datepicker('widget').wrap('<div class="ll-skin-latoja"/>');

	}
	if ($('#myChart').length) {
		let ctx = document.getElementById('myChart').getContext('2d');
		let data = {
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

		let myLineChart = new Chart(ctx, {
			type: 'line',
			data: data
		});
	}
});