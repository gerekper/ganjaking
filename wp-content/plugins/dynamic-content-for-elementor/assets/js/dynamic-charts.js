(function ($) {
    var WidgetElements_DynamicCharts = function ($scope, $) {
		let elementSettings = dceGetElementSettings($scope);
		let labels;
		let chart = $scope.find('#myChart');
		let useRandomColors = Boolean( elementSettings.background_random_colors );
		let backgroundColors = [];
		let borderColors = [];
		let color;
		let colors = [];
		let backgroundOpacity;
		let borderOpacity;
		let randomColors = [];
		let options;
		let scales;
		let background_data_temp = [];
		let background_data = elementSettings.background_data;
        const ctx = chart[0].getContext('2d');

		if ( elementorFrontend.isEditMode() && ! useRandomColors ) {
			background_data.models.forEach( function(item){
				background_data_temp.push( item.attributes );
			});
			background_data = background_data_temp;
		}

		// Set labels and data for different inputs
		if( 'csv' === elementSettings.input ) {
			labels = chart.data('chart-labels');
			data = chart.data('chart-data');
		} else if ( 'simple' === elementSettings.input ) {
			labels = elementSettings.labels.split(',');
			data = elementSettings.data.split(',');
		}

		if( useRandomColors ) {
			randomColors = {
				dynamic1: '#ff577a',
				dynamic2: '#33b8b7',
				dynamic3: '#ffc858',
				dynamic4: '#c2c4c8',
				dynamic5: '#008efa',
				indianred: "#cd5c5c",
				cadetblue: "#5f9ea0",
				cyan: "#00ffff",
				darkblue: "#00008b",
				darkcyan: "#008b8b",
				darkgoldenrod: "#b8860b",
				darkgreen: "#006400",
				darkgrey: "#a9a9a9",
				darkkhaki: "#bdb76b",
				darkmagenta: "#8b008b",
				darkolivegreen: "#556b2f",
				darkorange: "#ff8c00",
				darkorchid: "#9932cc",
				darkred: "#8b0000",
				darksalmon: "#e9967a",
				darkseagreen: "#8fbc8f",
				brown: "#a52a2a",
				darkslateblue: "#483d8b",
				darkslategrey: "#2f4f4f",
				darkturquoise: "#00ced1",
				darkviolet: "#9400d3",
				deeppink: "#ff1493",
				deepskyblue: "#00bfff",
				dimgray: "#696969",
				dimgrey: "#696969",
				dodgerblue: "#1e90ff",
				firebrick: "#b22222",
				floralwhite: "#fffaf0",
				forestgreen: "#228b22",
				gainsboro: "#dcdcdc",
				ghostwhite: "#f8f8ff",
				gold: "#ffd700",
				goldenrod: "#daa520",
				greenyellow: "#adff2f",
				grey: "#808080",
				honeydew: "#f0fff0",
				hotpink: "#ff69b4",
				indigo: "#4b0082",
				ivory: "#fffff0",
				khaki: "#f0e68c",
				lavender: "#e6e6fa",
				lavenderblush: "#fff0f5",
				lawngreen: "#7cfc00",
				lemonchiffon: "#fffacd",
				lightblue: "#add8e6",
				lightcoral: "#f08080",
				lightcyan: "#e0ffff",
				lightgoldenrodyellow: "#fafad2",
				lightgray: "#d3d3d3",
				chartreuse: "#7fff00",
				lightgreen: "#90ee90",
				blueviolet: "#8a2be2",
				lightgrey: "#d3d3d3",
				lightpink: "#ffb6c1",
				lightsalmon: "#ffa07a",
				lightseagreen: "#20b2aa",
				lightskyblue: "#87cefa",
				lightslategray: "#778899",
				lightslategrey: "#778899",
				lightsteelblue: "#b0c4de",
				lightyellow: "#ffffe0",
				limegreen: "#32cd32",
				linen: "#faf0e6",
				magenta: "#ff00ff",
				mediumaquamarine: "#66cdaa",
				mediumblue: "#0000cd",
				mediumorchid: "#ba55d3",
				mediumpurple: "#9370db",
				crimson: "#dc143c",
				mediumseagreen: "#3cb371",
				burlywood: "#deb887",
				chocolate: "#d2691e",
				coral: "#ff7f50",
				mediumslateblue: "#7b68ee",
				mediumspringgreen: "#00fa9a",
				mediumturquoise: "#48d1cc",
				mediumvioletred: "#c71585",
				midnightblue: "#191970",
				mintcream: "#f5fffa",
				mistyrose: "#ffe4e1",
				moccasin: "#ffe4b5",
				navajowhite: "#ffdead",
				oldlace: "#fdf5e6",
				olivedrab: "#6b8e23",
				orange: "#ffa500",
				orangered: "#ff4500",
				orchid: "#da70d6",
				palegoldenrod: "#eee8aa",
				palegreen: "#98fb98",
				paleturquoise: "#afeeee",
				palevioletred: "#db7093",
				papayawhip: "#ffefd5",
				peachpuff: "#ffdab9",
				peru: "#cd853f",
				pink: "#ffc0cb",
				plum: "#dda0dd",
				powderblue: "#b0e0e6",
				rebeccapurple: "#663399",
				rosybrown: "#bc8f8f",
				royalblue: "#4169e1",
				saddlebrown: "#8b4513",
				salmon: "#fa8072",
				sandybrown: "#f4a460",
				seagreen: "#2e8b57",
				seashell: "#fff5ee",
				sienna: "#a0522d",
				skyblue: "#87ceeb",
				slateblue: "#6a5acd",
				slategrey: "#708090",
				snow: "#fffafa",
				springgreen: "#00ff7f",
				steelblue: "#4682b4",
				tan: "#d2b48c",
				thistle: "#d8bfd8",
				tomato: "#ff6347",
				turquoise: "#40e0d0",
				violet: "#ee82ee",
				wheat: "#f5deb3",
				whitesmoke: "#f5f5f5",
				yellowgreen: "#9acd32",
				cornflowerblue: "#6495ed",
				cornsilk: "#fff8dc",
				blanchedalmond: "#ffebcd",
			};
			colors = Object.values( randomColors );
		} else {
			let i;
			for ( i = 0; i < background_data.length; i++) {
				colors.push( background_data[i].color );
			}
			if ( background_data.length < data.length ) {
				// Set colors are less than the data, so set the last colour for all the remaining ones
				let lastColor = background_data[ i - 1 ].color;
				while( i < data.length ) {
					colors.push( lastColor );
					i++;
				}
			}
		}

		// Set Colors
		for (let i in data) {
			color = jQuery.Color( colors[i] ).rgba();
			// Set opacity
			backgroundOpacity = 0.2;
			borderOpacity = 0.7;
			// Set different background opacity for doughnut and pie
			if( 'doughnut' === elementSettings.type || 'pie' === elementSettings.type ) {
				backgroundOpacity = 1;
				borderOpacity = 1;
			}
			backgroundColors.push( `rgba(${color[0]}, ${color[1]}, ${color[2]}, ${backgroundOpacity})` );
			borderColors.push( `rgba(${color[0]}, ${color[1]}, ${color[2]}, ${borderOpacity})` );
		}

		options =  {
			responsive: true,
			maintainAspectRatio: false,
		};

		// Legend Options
		if( elementSettings.show_legend || elementSettings.show_title ) {
			
			options.plugins = {
				legend: {
					display: elementSettings.show_legend || true,
					labels: {
						filter: (item, chart) => {
							return elementSettings.show_legend;
						},
						color: elementSettings.legend_color || Chart.defaults.color,
					},
					title: {
						display: elementSettings.show_title,
						text: elementSettings.title,
					},
					position: elementSettings.legend_position || 'top',
					align: elementSettings.legend_align || 'center',
				},
			};
		}else{
			options.plugins = {
				legend: {
					display: false,
				},
			};
		}

		//Create Y axis Step Size
		const min = Math.min(...data);
		const max = Math.max(...data);
		// check number of divisions
		const divisions = 10;

		const increment = (max - min) / divisions;

		const range = Array.from({length: divisions + 1}, (_, i) => min + i * increment);

		const tickValues = range.map(value => value.toString());
		
		yAxis= {
			scale: {
			  ticks: {
				max: tickValues.length -1,
				stepSize: elementSettings.stepsize,
			  },
			},
		  },
		$.extend(options, yAxis);
	

		scales = {
			scales: {
				y: {
					beginAtZero: Boolean(elementSettings.begin_at_zero),
					grace: elementSettings.grace || 0,
					ticks: {
						color: elementSettings.axis_y_labels_color || Chart.defaults.color,
                    },
					grid: {
						color: elementSettings.axis_y_grid_color || Chart.defaults.color,
					}
				},
				x: {
					ticks: { 
						color: elementSettings.axis_x_labels_color || Chart.defaults.color,
					},
					grid: {
						color: elementSettings.axis_x_grid_color || Chart.defaults.color,
					}
				},
			},
		};
	
		if( 'line' === elementSettings.type || 'bar' === elementSettings.type ) {
			$.extend(options, scales);
		}

		// Display Chart

		new Chart(ctx, {
			type: elementSettings.type,
			data: {
				labels: labels,
				datasets: [{
					label: elementSettings.legend,
					data: data,
					backgroundColor: backgroundColors,
					borderColor: borderColors,
					borderWidth: elementSettings.border_width_data || 0,
				}]
			},
			options: options,
		});
    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-charts.default', WidgetElements_DynamicCharts);
    });
})(jQuery);
