/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Component for updating or canceling the product match changes
 *
 * @param {Object} props
 * @param {Function} props.setCancelButton Callback function when cancel button is clicked
 * @param {Function} props.setDoneButton Callback function when done button is clicked
 * @param {boolean} props.shouldUpdate True if component meets the update criteria, false otherwise
 * @return {JSX} JSX Buttons
 */

const DoneCloseMatchesButtons = ( props ) => {
	const { setCancelButton, setDoneButton, shouldUpdate } = props;
	return (
		<div className="wc-cart-addons-block__edit-done-link-wrapper">
			<Button
				variant="isLink"
				className="is-link wc-cart-addons-block__cancel-button"
				onClick={ () => setCancelButton() }
			>
				{ __( 'Cancel', 'sfn_cart_addons' ) }
			</Button>
			{ shouldUpdate && (
				<Button
					variant="isSecondary"
					className="is-secondary"
					onClick={ () => setDoneButton() }
				>
					{ __( 'Done', 'sfn_cart_addons' ) }
				</Button>
			) }
		</div>
	);
};

export default DoneCloseMatchesButtons;
