import {Component} from "@wordpress/element";
import {RichText} from '@wordpress/block-editor';
import {__} from '@wordpress/i18n';

class TitlePlan extends Component {
	render() {
		const { attributes, updateAttribute, subtitleLabel } = this.props;
		const { title, titleColor, titleAlign,titleBackgroundColor, titleFontSize, titleBackgroundColorTransparent} = attributes;
		const { subtitleFontSize, showSubtitleSeparator, subtitleColor} = attributes;

		const subtitleClass = 'subtitlePlan' + ( showSubtitleSeparator ? ' with-separator' : '' );

		return(
			<div className="plan-title" style={ ( titleBackgroundColorTransparent ) ? {background: 'transparent'} : {background: titleBackgroundColor} }>

				{ subtitleLabel !== ''  ?<style>
					{ `.wp-block-yith-ywsbs-plan .subtitlePlan.with-separator:after { border-color: ${subtitleColor} }`}
				</style> : ''}

			{subtitleLabel !== '' ? <div className={subtitleClass} style={{color:subtitleColor,fontSize: subtitleFontSize }}>{subtitleLabel}
				</div> : ''}
			<RichText
				className={"ywsbs-plan__title"}
				tagName="h2"
				onChange={( value ) => updateAttribute( value, 'title')}
				value={title}
				placeholder={__("Title Plan", "mytheme-blocks")}
				formatingControls={[]}
				style = {{textAlign:titleAlign, color:titleColor, fontSize: titleFontSize } }
			/>
			</div>
		);
	}
}

export default TitlePlan;