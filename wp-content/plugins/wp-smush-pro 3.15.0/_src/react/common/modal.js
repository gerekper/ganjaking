import React, {useEffect} from 'react';
import classnames from 'classnames';
import SUI from 'SUI';
import $ from 'jquery';

const {__} = wp.i18n;

export default function Modal(
	{
		id = '',
		title = '',
		description = '',
		small = false,
		headerActions = false,
		focusAfterOpen = '',
		focusAfterClose = 'container',
		dialogClasses = [],
		disableCloseButton = false,
		enterDisabled = false,
		beforeTitle = false,
		onEnter = () => false,
		onClose = () => false,
		footer,
		children
	}
) {
	useEffect(() => {
		SUI.openModal(
			id,
			focusAfterClose,
			focusAfterOpen ? focusAfterOpen : getTitleId(),
			false,
			false
		);

		return () => SUI.closeModal();
	}, []);

	const handleKeyDown = (event) => {
		const isTargetInput = $(event.target).is('.sui-modal.sui-active input');
		if (isTargetInput && event.keyCode === 13) {
			event.preventDefault();
			event.stopPropagation();

			if (!enterDisabled && onEnter) {
				onEnter(event);
			}
		}
	}

	function getTitleId() {
		return id + '-modal-title';
	}

	function getHeaderActions() {
		const closeButton = getCloseButton();
		if (small) {
			return closeButton;
		} else if (headerActions) {
			return headerActions;
		} else {
			return <div className="sui-actions-right">{closeButton}</div>
		}
	}

	function getCloseButton() {
		return <button id={id + '-close-button'}
					   type="button"
					   onClick={() => onClose()}
					   disabled={disableCloseButton}
					   className={classnames("sui-button-icon", {
						   'sui-button-float--right': small
					   })}>

			<span className="sui-icon-close sui-md" aria-hidden="true"/>
			<span className="sui-screen-reader-text">
				{__('Close this dialog window', 'wds')}
			</span>
		</button>
	}

	function getDialogClasses() {
		return Object.assign({}, {
			'sui-modal-sm': small,
			'sui-modal-lg': !small
		}, dialogClasses);
	}

	return <div className={classnames('sui-modal', getDialogClasses())}
				onKeyDown={e => handleKeyDown(e)}>
		<div role="dialog"
			 id={id}
			 className={classnames('sui-modal-content', id + '-modal')}
			 aria-modal="true"
			 aria-labelledby={id + '-modal-title'}
			 aria-describedby={id + '-modal-description'}>

			<div className="sui-box" role="document">
				<div className={classnames('sui-box-header', {
					'sui-flatten sui-content-center sui-spacing-top--40': small
				})}>
					{beforeTitle}

					<h3 id={getTitleId()}
						className={classnames('sui-box-title', {
							'sui-lg': small
						})}>

						{title}
					</h3>

					{getHeaderActions()}
				</div>

				<div className={classnames('sui-box-body', {
					'sui-content-center': small
				})}>
					{description &&
						<p className="sui-description"
						   id={id + '-modal-description'}>
							{description}
						</p>}

					{children}
				</div>

				{footer && <div className="sui-box-footer">
					{footer}
				</div>}
			</div>
		</div>
	</div>;
}
