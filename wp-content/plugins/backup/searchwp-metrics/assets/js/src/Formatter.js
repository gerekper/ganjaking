const SearchwpMetricsFormatFor = {};

SearchwpMetricsFormatFor.install = function (Vue, options) {

	const library = 'chartjs';

	Vue.SearchwpMetricsFormatForChart = function (data, options = {}) {
		switch (library) {
			case 'chartjs':
				data = Vue.SearchwpMetricsFormatForChartjs(data, options);
				break;

			default:
				data = data;
				break;
		}

		return data;
	};

	Vue.SearchwpMetricsFormatForChartjs = function (data, options = {}) {
		let chartData = {
			labels: data.labels,
			datasets: []
		};

		for (let i = 0; i < data.datasets.length; i++) {
			let dataset = data.datasets[i];
			let defaults = {
				label: dataset.engine,
				borderColor: Vue.SearchwpMetricsGetColor(i),
				backgroundColor: Vue.SearchwpMetricsGetColor(i, 0.15, 1.35),
				data: dataset.dataset
			};

			// Merge the defaults into any options set
			chartData.datasets.push({ ...options, ...defaults});
		}

		return chartData;
	}
}

export default SearchwpMetricsFormatFor;
