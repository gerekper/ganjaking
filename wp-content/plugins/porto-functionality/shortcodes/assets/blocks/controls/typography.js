const PortoTypographyControl = function({
	label,
	value,
	options,
	onChange
}) {
	const __ = wp.i18n.__,
		TextControl = wp.components.TextControl,
		SelectControl = wp.components.SelectControl,
		RangeControl = wp.components.RangeControl,
		PanelColorSettings = wp.blockEditor.PanelColorSettings,
		el = wp.element.createElement;

	if ( ! value ) {
		value = {};
	}

	let fonts = [{label: __('Default', 'porto-functionality'), value: ''}];
	porto_block_vars.googlefonts.map(function(font, index) {
		fonts.push({label: font, value: font});
	});
	return el(
		'div',
		{ className: 'porto-typography-control' },
		el(
			'h3',
			{ className: 'components-base-control', style: {marginBottom: 15} },
			label
		),
		(! options || false !== options.fontFamily) && el(SelectControl, {
			label: __('Font Family', 'porto-functionality'),
			value: value.fontFamily,
			options: fonts,
			help: __('If you want to use other Google font, please add it in Theme Options -> Skin -> Typography -> Custom Font.', 'porto-functionality'),
			onChange: ( val ) => { value.fontFamily = val; onChange( value ) },
		}),
		el( TextControl, {
			label: __('Font Size', 'porto-functionality'),
			value: value.fontSize,
			help: __('Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality'),
			onChange: ( val ) => { value.fontSize = val; onChange( value ) },
		} ),
		el( RangeControl, {
			label: __('Font Weight', 'porto-functionality'),
			value: value.fontWeight,
			min: 100,
			max: 900,
			step: 100,
			onChange: ( val ) => { value.fontWeight = val; onChange( value ) },
		}),
		(! options || false !== options.textTransform) && el(SelectControl, {
			label: __('Text Transform', 'porto-functionality'),
			value: value.textTransform,
			options: [{label: __('Default', 'porto-functionality'), value: ''}, {label: __('Inherit', 'porto-functionality'), value: 'inherit'}, {label: __('Uppercase', 'porto-functionality'), value: 'uppercase'}, {label: __('Lowercase', 'porto-functionality'), value: 'lowercase'}, {label: __('Capitalize', 'porto-functionality'), value: 'capitalize'}, {label: __('None', 'porto-functionality'), value: 'none'}],
			onChange: ( val ) => { value.textTransform = val; onChange( value ) },
		}),
		(! options || false !== options.lineHeight) && el( TextControl, {
			label: __('Line Height', 'porto-functionality'),
			value: value.lineHeight,
			help: __('Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality'),
			onChange: ( val ) => { value.lineHeight = val; onChange( value ) },
		} ),
		(! options || false !== options.letterSpacing) && el( TextControl, {
			label: __('Letter Spacing', 'porto-functionality'),
			value: value.letterSpacing,
			help: __('Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality'),
			onChange: ( val ) => { value.letterSpacing = val; onChange( value ) },
		} ),
		el( PanelColorSettings, {
			title: __('Color Settings', 'porto-functionality'),
			initialOpen: false,
			colorSettings: [{
				label: __('Font Color', 'porto-functionality'),
				value: value.color,
				onChange: ( val ) => { value.color = val; onChange( value ); }
			}]
		}),
	);
};

export default PortoTypographyControl;