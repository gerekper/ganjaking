import Chart from 'chart.js'

Chart.NewLegend = Chart.Legend.extend({
	afterFit: function() {
		this.height = this.height + 20;
	},
});

let createNewLegendAndAttach = function(chartInstance, legendOpts) {
	if ('line'!==chartInstance.config.type) {
		return;
	}

	var legend = new Chart.NewLegend({
		ctx: chartInstance.chart.ctx,
		options: legendOpts,
		chart: chartInstance
	});

	if (chartInstance.legend) {
		Chart.layoutService.removeBox(chartInstance, chartInstance.legend);
		delete chartInstance.newLegend;
	}

	chartInstance.newLegend = legend;
	Chart.layoutService.addBox(chartInstance, legend);
}

// Register the legend plugin
Chart.plugins.register({
	beforeInit: function(chartInstance) {
		if ('line'!==chartInstance.config.type) {
			return;
		}

		var legendOpts = chartInstance.options.legend;

		if (legendOpts) {
			createNewLegendAndAttach(chartInstance, legendOpts);
		}
	},
	beforeUpdate: function(chartInstance) {
		if ('line'!==chartInstance.config.type) {
			return;
		}

		var legendOpts = chartInstance.options.legend;

		if (legendOpts) {
			legendOpts = Chart.helpers.configMerge(Chart.defaults.global.legend, legendOpts);

			if (chartInstance.newLegend) {
				chartInstance.newLegend.options = legendOpts;
			} else {
				createNewLegendAndAttach(chartInstance, legendOpts);
			}
	  	} else {
			Chart.layoutService.removeBox(chartInstance, chartInstance.newLegend);
			delete chartInstance.newLegend;
		}
	},
	afterEvent: function(chartInstance, e) {
		if ('line'!==chartInstance.config.type) {
			return;
		}

		var legend = chartInstance.newLegend;
		if (legend) {
			legend.handleEvent(e);
		}
	}
});

import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
	extends: Line,
	mixins: [reactiveProp],
	props: ['options'],
	mounted () {
		this.renderChart(this.chartData, this.options);
	}
}
