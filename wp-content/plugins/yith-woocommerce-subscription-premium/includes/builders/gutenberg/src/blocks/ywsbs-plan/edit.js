import {__} from '@wordpress/i18n';
import {
	InnerBlocks,
	InspectorControls,
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings
} from '@wordpress/block-editor';
import {createBlock} from '@wordpress/blocks';
import {withDispatch} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {
	FontSizePicker,
	PanelBody,
	RangeControl,
	TextControl,
	ToggleControl,
	SelectControl
} from "@wordpress/components";
import {AlignmentToolbar, BlockControls} from "@wordpress/block-editor";

import TitlePlan from '../../components/title-plan';

function EditorPlan(props) {


	const {attributes, setAttributes} = props;
	let {className} = props;
	className = className + ((typeof animation !== 'undefined' && animation !== '') ? ' ' + animation : '');
	const onSelectImage = ({id, url, alt}) => {
		props.setAttributes({
			id,
			url,
			alt
		});
	};
	const
		onSelectURL = url => {
			props.setAttributes({
				url,
				id: null,
				alt: ""
			});
		};

	const onChangeColor = (value, type) => {

		if (typeof value !== 'undefined') {
			if (type === 'backgroundColor') {
				setAttributes({backgroundColor: value});
			}
			if (type === 'borderColor') {
				setAttributes({borderColor: value});
			}
			if (type === 'titleBackgroundColor') {
				setAttributes({titleBackgroundColor: value});
			}
			if (type === 'titleColor') {
				setAttributes({titleColor: value});
			}
			if (type === 'subtitleColor') {
				setAttributes({subtitleColor: value});
			}
			if (type === 'textColor') {
				setAttributes({textColor: value});
			}

		}
	};

	const updateAttributes = (value, name) => {
		if (name === 'borderRadius') {
			setAttributes({borderRadius: value});
		}
		if (name === 'title') {
			setAttributes({title: value});
		}
	};

	const {backgroundColor, borderColor, borderRadius, titleAlign, titleFontSize, textColor} = attributes;
	const {shadowColor, shadowH, shadowV, shadowBlur, shadowSpread} = attributes;
	const {subtitleFontSize} = attributes;

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

	const isGradient = (color) => {
		const isLinear = props.attributes.backgroundColor?.includes('linear-gradient');
		if (isLinear) return true;

		const isRadial = props.attributes.backgroundColor?.includes('radial-gradient');
		if (isRadial) return true;

		return false;

	}

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={titleAlign}
					onChange={(nextAlign) => {
						setAttributes({titleAlign: nextAlign});
					}}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={__('Settings', 'yith-woocommerce-subscription')}>
					<ToggleControl
						label={__('Show image', 'yith-woocommerce-subscription')}
						help={props.attributes.showImage ? __('Show image', 'yith-woocommerce-subscription') : __('Hide image', 'yith-woocommerce-subscription')}
						checked={props.attributes.showImage}
						onChange={(value) => props.onShowBlock(value, 'core/image')}
					/>

					<ToggleControl
						label={__('Show list', 'yith-woocommerce-subscription')}
						help={props.attributes.showList ? __('Show list', 'yith-woocommerce-subscription') : __('Hide list', 'yith-woocommerce-subscription')}
						checked={props.attributes.showList}
						onChange={(value) => props.onShowBlock(value, 'core/list')}
					/>
				</PanelBody>
				<PanelBody title={__('General Settings', 'yith-woocommerce-subscription')}>
					<RangeControl
						label={__('Border Radius', 'yith-woocommerce-subscription')}
						value={props.attributes.borderRadius}
						onChange={(value) => updateAttributes(value, 'borderRadius')}
						min={0}
						max={100}
					/>
					<SelectControl
						label={__('Animated hover effect', 'yith-woocommerce-subscription')}
						value={props.attributes.animation}
						options={[
							{label: __('No Effects', 'yith-woocommerce-subscription'), value: ''},
							{label: __('Grow', 'yith-woocommerce-subscription'), value: 'grow'},
							{label: __('Float', 'yith-woocommerce-subscription'), value: 'float'},
							{label: __('Sink', 'yith-woocommerce-subscription'), value: 'sink'},
							{label: __('Shrink', 'yith-woocommerce-subscription'), value: 'shrink'},

						]}
						onChange={(animation) => setAttributes({animation})}
					/>
				</PanelBody>

				<PanelColorGradientSettings
					title={__('General Color Settings', 'yith-woocommerce-subscription')}
					initialOpen={false}
					settings={[
						{
							label: __('Background Color', 'yith-woocommerce-subscription'),
							onColorChange: (color) => onChangeColor(color, 'backgroundColor'),
							colorValue: props.attributes.backgroundColor,
							gradientValue: isGradient(props.attributes.backgroundColor) ? props.attributes.backgroundColor : '',
							onGradientChange: (color) => onChangeColor(color, 'backgroundColor'),
						},
						{
							label: __('Text Color'),
							onColorChange: (color) => onChangeColor(color, 'textColor'),
							colorValue: props.attributes.textColor,
						},
						{
							label: __('Border Color', 'yith-woocommerce-subscription'),
							onColorChange: (color) => onChangeColor(color, 'borderColor'),
							colorValue: props.attributes.borderColor,
						}
					]}
				>
				</PanelColorGradientSettings>


				<PanelColorGradientSettings
					title={__('Titles Bar', 'yith-woocommerce-subscription')}
					initialOpen={false}
					settings={[
						{
							label: __('Text Color'),
							onColorChange: (color) => onChangeColor(color, 'titleColor'),
							colorValue: props.attributes.titleColor,
						},
						{
							label: __('Subtitle Color'),
							onColorChange: (color) => onChangeColor(color, 'subtitleColor'),
							colorValue: props.attributes.subtitleColor,
						},
						{
							label: __('Background Color'),
							onColorChange: (color) => onChangeColor(color, 'titleBackgroundColor'),
							colorValue: props.attributes.titleBackgroundColor,
							gradientValue: isGradient(props.attributes.titleBackgroundColor) ? props.attributes.titleBackgroundColor : '',
							onGradientChange: (color) => onChangeColor(color, 'titleBackgroundColor'),

						}
					]}
				>
					<ToggleControl
						label={__('Set a transparent background color', 'yith-woocommerce-subscription')}
						checked={props.attributes.titleBackgroundColorTransparent}
						onChange={(value) => props.setAttributes({titleBackgroundColorTransparent: value})}
					/>
					<h4>{__('Title font size', 'yith-woocommerce-subscription')}</h4>
					<FontSizePicker
						fontSizes={fontSizes}
						value={titleFontSize || 20}
						fallbackFontSize={20}
						withSlider={true}
						onChange={(newFontSize) => {
							setAttributes({titleFontSize: newFontSize});
						}}
					/>

					<TextControl
						label={__('Subtitle Text', 'yith-woocommerce-subscription')}
						value={props.attributes.subtitleLabel}
						onChange={(value) => setAttributes({subtitleLabel: value})}
					/>

					<h4>{__('Subtitle font size', 'yith-woocommerce-subscription')}</h4>
					<FontSizePicker
						label={__('Show separator between subtitle and title', 'yith-woocommerce-subscription')}
						fontSizes={fontSizes}
						value={subtitleFontSize || 20}
						fallbackFontSize={20}
						withSlider={true}
						onChange={(newFontSize) => {
							setAttributes({subtitleFontSize: newFontSize});
						}}
					/>

					<ToggleControl
						label={__('Show separator between subtitle and title', 'yith-woocommerce-subscription')}
						help={props.attributes.showSubtitleSeparator ? __('Show separator', 'yith-woocommerce-subscription') : __('Hide separator', 'yith-woocommerce-subscription')}
						checked={props.attributes.showSubtitleSeparator}
						onChange={(value) => props.setAttributes({showSubtitleSeparator: value})}
					/>

				</PanelColorGradientSettings>


				<PanelColorGradientSettings
					title={__('Box Shadow', 'yith-woocommerce-subscription')}
					initialOpen={false}
					settings={[
						{
							label: __('Shadow color', 'yith-woocommerce-subscription'),
							onColorChange: (color) => setAttributes({shadowColor: color}),
							value: shadowColor,
							colorValue: props.attributes.shadowColor,
						},
					]}
				>
					<RangeControl
						label={__('Shadow H offset', 'yith-woocommerce-subscription')}
						value={shadowH || ''}
						onChange={(value) => setAttributes({shadowH: value})}
						min={-50}
						max={50}
					/>
					<RangeControl
						label={__('Shadow V offset', 'yith-woocommerce-subscription')}
						value={shadowV || ''}
						onChange={(value) => setAttributes({shadowV: value})}
						min={-50}
						max={50}
					/>
					<RangeControl
						label={__('Shadow blur', 'yith-woocommerce-subscription')}
						value={shadowBlur || ''}
						onChange={(value) => setAttributes({shadowBlur: value})}
						min={0}
						max={50}
					/>
					<RangeControl
						label={__('Shadow spread', 'yith-woocommerce-subscription')}
						value={shadowSpread || ''}
						onChange={(value) => setAttributes({shadowSpread: value})}
						min={0}
						max={50}
					/>
				</PanelColorGradientSettings>

			</InspectorControls>
			<div className={className} style={{
				color: textColor,
				background: backgroundColor,
				borderColor: borderColor,
				borderRadius: borderRadius,
				boxShadow: `${shadowH}px ${shadowV}px ${shadowBlur}px ${shadowSpread}px ${shadowColor}`
			}}>

				<TitlePlan attributes={props.attributes} subtitleLabel={props.attributes.subtitleLabel}
					updateAttribute={updateAttributes}/>

				<InnerBlocks
					templateInsertUpdatesSelection={ false }
					__experimentalCaptureToolbars={false}
					template={[
						['yith/ywsbs-price'],
						['core/paragraph', {placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.'}],
						['core/button', {value: 'Subscribe'}],
					]}
					templateLock="insert"
				/>
			</div>
		</>
	);
}

export default compose([
	/*	withSelect( (select, ownProps ) => {
			const {clientId} = ownProps;
			const parentClientId = select( 'core/block-editor' ).getBlockHierarchyRootClientId( clientId ); //Pass Child's Client Id.
			const parentAttributes = select('core/block-editor').getBlockAttributes( parentClientId );
			return {
				featuredLabel: parentAttributes.featuredLabel
			};
		}),*/
	withDispatch((dispatch, ownProps, registry) => ({

		onShowBlock(show, blockType) {
			const {clientId, setAttributes} = ownProps;
			const {replaceInnerBlocks} = dispatch('core/block-editor');
			const {getBlocks} = registry.select('core/block-editor');

			let innerBlocks = getBlocks(clientId);

			let newInnerBlocks = [];

			if (show) {

				switch (blockType) {
					case 'core/image':
						newInnerBlocks = [createBlock(blockType), ...innerBlocks];
						break;
					case 'core/list':
						innerBlocks.forEach((b) => {
							newInnerBlocks.push(b);
							if (b.name === 'core/paragraph') {
								newInnerBlocks.push(createBlock(blockType));
							}
						})
						break;
				}
			} else {
				innerBlocks.forEach(
					function (block) {
						if (block.name !== blockType) {
							newInnerBlocks.push(block);
						}
					}
				);

			}

			switch (blockType) {
				case 'core/image':
					setAttributes({showImage: show});
					break;
				case 'core/list':
					setAttributes({showList: show});
					break;
			}

			replaceInnerBlocks(clientId, newInnerBlocks, false);
		}
	}))
])(EditorPlan);
