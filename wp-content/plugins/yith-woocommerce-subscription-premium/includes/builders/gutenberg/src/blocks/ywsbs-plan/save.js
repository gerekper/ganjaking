/**
 * WordPress dependencies
 */
import { InnerBlocks, RichText } from '@wordpress/block-editor';

export default function save( props ) {
	const { attributes } = props;
	const {title, textColor, titleAlign, titleBackgroundColor, titleColor, titleFontSize, titleBackgroundColorTransparent, backgroundColor, borderColor, borderRadius, animation} = attributes;
	const {shadowColor, shadowH, shadowV, shadowBlur, shadowSpread} = attributes;
	const {subtitleColor, subtitleLabel, subtitleFontSize, showSubtitleSeparator} = attributes;
	const subtitleClass = 'subtitlePlan' + ( showSubtitleSeparator ? ' with-separator' : '' );

	return (
		<div className={animation} style={{
			color:textColor,
			background: backgroundColor,
			borderColor: borderColor,
			borderRadius: borderRadius,
			boxShadow: `${shadowH}px ${shadowV}px ${shadowBlur}px ${shadowSpread}px ${shadowColor}`
		}}>
			{ subtitleLabel !== '' ?	<style>
				{ `.wp-block-yith-ywsbs-plan .subtitlePlan.with-separator:after { border-color: ${subtitleColor} }`}
			</style> : ''}

			<div className="plan-title" style={ ( titleBackgroundColorTransparent ) ? {background: 'transparent'} : {background: titleBackgroundColor} }>
				{subtitleLabel !== '' ? <div className={subtitleClass} style={{color:subtitleColor,fontSize: subtitleFontSize }}>{subtitleLabel}
				</div> : ''}
				<RichText.Content
					className={"ywsbs-plan__title"}
					tagName="h2"
					value={title}
					style = {{textAlign:titleAlign, color:titleColor, fontSize: titleFontSize } }
				/>
			</div>
			<InnerBlocks.Content/>
		</div>
	);
}
