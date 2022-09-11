import {__} from '@wordpress/i18n';
import {RichText, InspectorControls} from '@wordpress/block-editor';
import {FontSizePicker, PanelBody, ToggleControl} from '@wordpress/components';

export default function edit(props) {

	const {attributes, setAttributes, className} = props;
	const {price, priceFontSize,  textColor } = attributes;
	const {recurringBillingPeriod, billingPeriodFontSize, billingPeriodPosition} = attributes;
	const {feeText, feeFontSize, feeShow} = attributes;
	const {trialText, trialFontSize, trialShow} = attributes;
	const fontSizes = [
		{
			name: __('Small'),
			slug: 'small',
			size: 11,
		},
		{
			name: __('Medium'),
			slug: 'small',
			size: 13,
		},
		{
			name: __('Big'),
			slug: 'big',
			size: 40,
		},
	];

	const billingPeriodClassName = "ywsbs-plan__price-billing " + billingPeriodPosition;
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Price settings', 'yith-woocommerce-subscription')}>
					<FontSizePicker
						fontSizes={fontSizes}
						value={priceFontSize || 40 }
						fallbackFontSize={ 40 }
						withSlider = {true}
						onChange={(newFontSize) => {
							setAttributes({priceFontSize: newFontSize});
						}}
					/>
				</PanelBody>
				<PanelBody title={__('Billing period settings', 'yith-woocommerce-subscription')}>

					<FontSizePicker
						fontSizes={fontSizes}
						value={billingPeriodFontSize || 11}
						fallbackFontSize={ 13 }
						withSlider = {true}
						onChange={(newFontSize) => {
							setAttributes({billingPeriodFontSize: newFontSize});
						}}
					/>
				</PanelBody>
				<PanelBody title={__('Fee settings', 'yith-woocommerce-subscription')}>
					<ToggleControl
						label={__('Show Fee Text?', 'yith-woocommerce-subscription')}
						help={feeShow ? __('The fee wil be displayed', 'yith-woocommerce-subscription') : __('There is no fee', 'yith-woocommerce-subscription')}
						checked={feeShow}
						onChange={(value) => setAttributes({feeShow: value})}
					/>
					<FontSizePicker
						fontSizes={fontSizes}
						value={feeFontSize || 13 }
						fallbackFontSize={ 13 }
						withSlider = {true}
						onChange={(newFontSize) => {
							setAttributes({feeFontSize: newFontSize});
						}}
					/>
				</PanelBody>
				<PanelBody title={__('Trial settings', 'yith-woocommerce-subscription')}>
					<ToggleControl
						label={__('Show Trial Text?', 'yith-woocommerce-subscription')}
						help={trialShow ? __('The trial text wil be displayed', 'yith-woocommerce-subscription') : __('There is no trial', 'yith-woocommerce-subscription')}
						checked={trialShow}
						onChange={(value) => setAttributes({trialShow: value})}
					/>
					<FontSizePicker
						fontSizes={fontSizes}
						value={trialFontSize || 13}
						fallbackFontSize={ 13 }
						withSlider = {true}
						onChange={(newFontSize) => {
							setAttributes({trialFontSize: newFontSize});
						}}
					/>
				</PanelBody>
			</InspectorControls>
			<div className={className} style={{
				color: textColor,
			}}>
				<div className="ywsbs-price__content">
					<RichText
						className={"ywsbs-plan__price"}
						tagName="span"
						onChange={(value) => setAttributes({price: value})}
						value={price}
						style={{fontSize: priceFontSize}}
					/>
					<RichText
						className="ywsbs-plan__price-billing"
						tagName="span"
						onChange={(value) => setAttributes({recurringBillingPeriod: value})}
						value={recurringBillingPeriod}
						style={{fontSize: billingPeriodFontSize+'px'}}
					/>

				</div>
				{feeShow && <RichText
					className={"ywsbs-plan__fee"}
					tagName="div"
					onChange={(value) => setAttributes({feeText: value})}
					value={feeText}
					style={{fontSize: feeFontSize}}
				/>}
				{ trialShow && <RichText
					className={"ywsbs-plan__trial"}
					tagName="div"
					onChange={(value) => setAttributes({trialText: value})}
					value={trialText}
					style={{fontSize: trialFontSize}}
				/>}

			</div>
		</>
	)
}
