import {__} from '@wordpress/i18n';
import {createBlock} from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings,
	getColorObjectByAttributeValues
} from '@wordpress/block-editor';
import {TextControl, PanelBody, PanelRow, RangeControl} from '@wordpress/components';
import {withDispatch, useDispatch, select, dispatch, withSelect} from '@wordpress/data';
import {dropRight, times} from 'lodash';
import {compose} from "@wordpress/compose";

const ALLOWED_BLOCKS = ['yith/ywsbs-plan'];

function PlansEditorContainer(props) {

	if (props.attributes.preview) {
		return (
			<>
				<img src={ywsbs_plans_object.ywsbs_plans_preview}/>
			</>
		)
	}

	const {attributes, setAttributes, className, isSelected, clientId, updatePlans, updatePlansAttributes, newplans, updateLabel} = props;
	const {plans, planTemplate, subtitleLabel} = attributes;
	const wrapperClass = className + ' ywsbs-plans ' + ' ywsbs-plans-' + newplans;
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('General Settings', 'yith-woocommerce-subscription')}>
					<RangeControl
						label={__('Number of Plans', 'yith-woocommerce-subscription')}
						value={newplans}
						onChange={(value) => updatePlans(newplans, value)}
						min={1}
						max={5}
					/>

				</PanelBody>
			</InspectorControls>
			<div className={wrapperClass}>
				<InnerBlocks allowedBlocks={ALLOWED_BLOCKS} template={planTemplate}
							 templateInsertUpdatesSelection={true}
							 __experimentalCaptureToolbars={false}
							 renderAppender={false}
							 __experimentalMoverDirection="horizontal"
				/>
			</div>
		</>
	);

}

const PlansEditContainerWrapper = compose([
	withSelect((select, ownProps) => {
		const {clientId} = ownProps;
		return {
			newplans: select('core/block-editor').getBlocks(clientId).length
		};
	}),
	withDispatch((dispatch, ownProps, registry) => ({
		updateLabel(newLabel) {
			const {clientId, setAttributes} = ownProps;
			const {getBlocks} = registry.select('core/block-editor');

			let innerBlocks = getBlocks(clientId);
			innerBlocks.forEach(
				function (block) {
					dispatch('core/editor').updateBlockAttributes(block.clientId, {
						subtitleLabel: ownProps.attributes.subtitleLabel
					});
				}
			);

			setAttributes({subtitleLabel: newLabel});
		},
		updatePlans(previousColumns, newColumns) {
			const {clientId, setAttributes, isSelected} = ownProps;

			const {replaceInnerBlocks, selectionChange} = dispatch('core/block-editor');
			const {getBlocks} = registry.select('core/block-editor');

			let innerBlocks = getBlocks(clientId);

			newColumns = newColumns > 5 ? 5 : newColumns;
			const isAddingColumn = newColumns > previousColumns;

			if (isAddingColumn) {
				innerBlocks = [
					...innerBlocks,
					...times(
						newColumns - previousColumns,
						() => {
							return createBlock('yith/ywsbs-plan');
						}
					),
				];
			} else {
				// The removed column will be the last of the inner blocks.
				innerBlocks = dropRight(
					innerBlocks,
					previousColumns - newColumns
				);

			}

			setAttributes({plans: newColumns});
			replaceInnerBlocks(clientId, innerBlocks, false);

		}
	}))
])(PlansEditorContainer);


const PlansEdit = (props) => {
	return <PlansEditContainerWrapper {...props} />;
};
export default PlansEdit;
