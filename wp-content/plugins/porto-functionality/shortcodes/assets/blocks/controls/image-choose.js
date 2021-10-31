const PortoImageChoose = function({
	label,
	options,
	value,
	onChange
}) {
	const el = wp.element.createElement;
	return el(
		'div',
		{ className: 'components-base-control porto-image-choose' },
		el(
			'label',
			{ className: 'components-input-control__label css-1wgusda-Text-BaseLabel' },
			label
		),
		options.map(function(option, index) {
			return el(
				'img',
				{
					src: option.src,
					alt: option.alt,
					className: parseInt(option.alt) === parseInt(value) ? 'active' : '',
					onClick: function onClick(e) {
						if (e.target) {
							const activeNode = e.target.parentNode.getElementsByClassName('active');
							if (activeNode.length) {
								activeNode[0].classList.remove('active');
							}
							e.target.classList.add('active');
							const layout = e.target.getAttribute('alt');
							return onChange(layout);
						}
					}
				}
			)
		})
	);
};

export default PortoImageChoose;