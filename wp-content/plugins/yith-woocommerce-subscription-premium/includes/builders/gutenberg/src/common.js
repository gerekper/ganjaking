let el = wp.element.createElement;
const yith_icon = el(
	'svg',
	{width: 22, height: 22},
	el('path', {d: "M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z"})
)

const attributesPlans = {
	plans: {
		type: 'integer',
		default: 3
	},
	planTemplate: {
		type: 'array',
		default: [
			['yith/ywsbs-plan'],
			['yith/ywsbs-plan'],
			['yith/ywsbs-plan']
		]
	},
	preview: {
		type: 'boolean',
		default: false
	}
};

const attributesPlan = {
	textColor:{
		type: 'text',
		default: '#000'
	},
	title:{
		type: 'text',
		default: 'Title'
	},
	titleColor: {
		type: 'text',
		default: '#000'
	},
	titleAlign: {
		type: 'text',
		default: 'center'
	},
	titleBackgroundColor: {
		type: 'text',
		default: '#fff'
	},
	titleBackgroundColorTransparent: {
		type: 'boolean',
		default: false
	},
	titleFontSize: {
		type: 'number',
		default: '40'
	},
	backgroundColor: {
		type: 'text',
		default: '#fff'
	},
	borderColor: {
		type: 'text',
		default: '#dedede'
	},
	subtitleFontSize: {
		type: 'number',
		default: '40'
	},
	subtitleColor: {
		type: 'text',
		default: '#000'
	},
	showSubtitleSeparator: {
		type: 'boolean',
		default: false
	},
	subtitleLabel: {
		type: 'text',
		default: ''
	},
	animation:{
		type:'text',
		default: ''
	},
	borderRadius: {
		type: 'integer',
		default: 10
	},
	showList : {
		type: 'boolean',
		default: false
	},
	showImage : {
		type: 'boolean',
		default: false
	},
	shadowColor: {
		type: 'text',
		default: '#d0d0d0'
	},
	shadowH: {
		type: 'number',
		default: 1,
	},
	shadowV: {
		type: 'number',
		default: 1,
	},
	shadowBlur: {
		type: 'number',
		default: 12,
	},
	shadowSpread: {
		type: 'number',
		default: 0,
	},
	id: {
		type: "number"
	},
	alt: {
		type: "string",
		source: "attribute",
		selector: "img",
		attribute: "alt",
		default: ""
	},
	url: {
		type: "string",
		source: "attribute",
		selector: "img",
		attribute: "src"
	}
};

const attributesPrice = {
	price: {
		type: 'text',
		default: '$19.90'
	},
	priceFontSize: {
		type: 'number',
		default: '40'
	},
	recurringBillingPeriod: {
		type: 'text',
		default: '/ Month'
	},
	billingPeriodFontSize:{
		type: 'number',
		default: '11'
	},
	billingPeriodPosition:{
	 type:'text',
	 default: 'on-top'
	},

	feeText: {
		type: 'text',
		default: '+ a signup fee of 5,00$'
	},
	feeShow:{
		type:'boolean',
		default: true
	},
	feeFontSize:{
		type: 'number',
		default: '13'
	},
	trialText: {
		type: 'text',
		default: 'Try it for 1 week free!'
	},
	trialShow:{
		type:'boolean',
		default: true
	},
	trialFontSize:{
		type: 'number',
		default: '13'
	},
};

export {yith_icon, attributesPlans, attributesPlan, attributesPrice};
