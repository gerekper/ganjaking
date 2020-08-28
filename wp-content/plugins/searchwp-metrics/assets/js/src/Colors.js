const SearchwpMetricsColors = {};

SearchwpMetricsColors.install = function (Vue, options) {

	const colorScheme = [
		'rgba(69,170,242,1)',
		'rgba(252,92,101,1)',
		'rgba(165,94,234,1)',
		'rgba(38,222,129,1)',
		'rgba(253,150,68,1)',
		'rgba(254,211,48,1)',
		'rgba(43,203,186,1)',
		'rgba(75,123,236,1)',
		'rgba(209,216,224,1)',
		'rgba(119,140,163,1)',
		'rgba(45,152,218,1)',
		'rgba(235,59,90,1)',
		'rgba(136,84,208,1)',
		'rgba(250,130,49,1)',
		'rgba(32,191,107,1)',
		'rgba(247,183,49,1)',
		'rgba(15,185,177,1)',
		'rgba(56,103,214,1)',
		'rgba(165,177,194,1)',
		'rgba(75,101,132,1)',
		'rgba(54, 162, 235, 1)',
		'rgba(255, 99, 132, 1)',
		'rgba(153, 102, 255, 1)',
		'rgba(255, 159, 64, 1)',
		'rgba(75, 192, 192, 1)',
		'rgba(201, 203, 207, 1)',
		// 'rgba(255, 205, 86, 1)',
		// '#59a14f',
		// '#b07aa1',
		// '#ff9da7',
		// '#4e79a7',
		// '#f28e2b',
		// '#e15759',
		// '#76b7b2',
		// '#edc948',
		// '#9c755f',
		// '#bab0ac',
		// '#4dc9f6',
		// '#f67019',
		// '#f53794',
		// '#537bc4',
		// '#acc236',
		// '#166a8f',
		// '#00a950',
		// '#58595b',
		// '#8549ba',
		// '#5cbae6',
		// '#b6d957',
		// '#fac364',
		// '#8cd3ff',
		// '#d998cb',
		// '#f2d249',
		// '#93b9c6',
		// '#ccc5a8',
		// '#52bacc',
		// '#dbdb46',
		// '#98aafb'
	];

	/**
	 * Implements a rotating color scheme
	 */
	Vue.SearchwpMetricsGetColor = function (i, opacity = 1, adjustment = 1) {
		while (i > colorScheme.length - 1) {
			i -= colorScheme.length;
		}

		let color = colorScheme[i].replace('1)', opacity + ')');

		let pieces = color.split(',');

		// Apply adjustment
		let r = parseInt(pieces[0].replace('rgba(', ''),10) * adjustment;
		let g = parseInt(pieces[1]) * adjustment;
		let b = parseInt(pieces[2]) * adjustment;

		// When adjusting, desaturate as well
		if (adjustment !== 1) {
			let f = 0.5; // desaturate by 20%
			let L = 0.3*r + 0.6*g + 0.1*b;

			let new_r = r + f * (L - r);
			let new_g = g + f * (L - g);
			let new_b = b + f * (L - b);

			r = new_r;
			g = new_g;
			b = new_b;
		}

		color = 'rgba(' + r + ',' + g + ',' + b + ',' + pieces[3];

		return color;
	};
}

export default SearchwpMetricsColors;
