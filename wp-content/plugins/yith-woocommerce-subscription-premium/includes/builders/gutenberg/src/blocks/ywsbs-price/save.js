/**
 * WordPress dependencies
 */
import { InnerBlocks, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const {price, priceFontSize } = attributes;
	const {recurringBillingPeriod, billingPeriodFontSize, billingPeriodPosition} = attributes;
	const {feeText, feeFontSize, feeShow} = attributes;
	const {trialText, trialFontSize, trialShow} = attributes;
	return (
		<div>
			<div className="ywsbs-price">
				<div className="ywsbs-price__content">
					<RichText.Content
						className={"ywsbs-plan__price"}
						tagName="span"

						value={price}
						style={{fontSize: priceFontSize+'px'}}
					/>
					<RichText.Content
						className="ywsbs-plan__price-billing"
						tagName="span"

						value={recurringBillingPeriod}
						style={{fontSize: billingPeriodFontSize+'px'}}
					/>

				</div>
				{feeShow && <RichText.Content
					className={"ywsbs-plan__fee"}
					tagName="div"
					value={feeText}
					style={{fontSize: feeFontSize+'px'}}
				/>}
				{ trialShow && <RichText.Content
					className={"ywsbs-plan__trial"}
					tagName="div"
					value={trialText}
					style={{fontSize: trialFontSize+'px'}}
				/>}
		</div>
		</div>
	);
}
