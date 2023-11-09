import React, {useEffect, useRef, useState} from "react";
import Modal from "../common/modal";
import {post} from "../utils/request";
import Button from "../common/button";
import ProgressBar from "../common/progress-bar";

const {__} = wp.i18n;

export default function MediaLibraryScannerModal(
	{
		inProgress = false,
		progress = 0,
		onClose = () => false,
		onStart = () => false,
		onCancel = () => false,
		focusAfterClose = ''
	}
) {
	function content() {
		if (inProgress) {
			return <>
				<ProgressBar progress={progress}/>
				<Button id="wp-smush-cancel-media-library-scan"
						icon="sui-icon-close"
						text={__('Cancel', 'wp-smushit')}
						ghost={true}
						onClick={onCancel}
				/>
			</>;
		} else {
			return <>
				<Button id="wp-smush-start-media-library-scan"
						icon="sui-icon-play"
						text={__('Start', 'wp-smushit')}
						onClick={onStart}
				/>
			</>;
		}
	}

	return <Modal id="wp-smush-media-library-scanner-modal"
				  title={__('Scan Media Library', 'wp-smushit')}
				  description={__('Scans the media library to detect items to Smush.', 'wp-smushit')}
				  onClose={onClose}
				  focusAfterClose={focusAfterClose}
				  disableCloseButton={inProgress}>
		{content()}
	</Modal>;
};
