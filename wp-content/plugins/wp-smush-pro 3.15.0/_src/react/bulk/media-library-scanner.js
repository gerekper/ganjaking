import React, {useState} from "react";
import domReady from '@wordpress/dom-ready';
import ReactDOM from "react-dom";
import Button from "../common/button";
import FloatingNoticePlaceholder from "../common/floating-notice-placeholder";
import {showSuccessNotice} from "../utils/notices";
import AjaxMediaLibraryScannerModal from "./ajax-media-library-scanner-modal";
import BackgroundMediaLibraryScannerModal from "./background-media-library-scanner-modal";

const {__} = wp.i18n;

function MediaLibraryScanner({}) {
	const [modalOpen, setModalOpen] = useState(false);

	return <>
		<FloatingNoticePlaceholder id="wp-smush-media-library-scanner-notice"/>

		{modalOpen &&
			<BackgroundMediaLibraryScannerModal
				focusAfterClose="wp-smush-open-media-library-scanner"
				nonce={mediaLibraryScan.nonce}
				onScanCompleted={() => {
					showSuccessNotice(
						'wp-smush-media-library-scanner-notice',
						__('Scan completed successfully!', 'wp-smushit'),
						true
					);
					setModalOpen(false);
					window.location.reload();
				}}
				onClose={() => setModalOpen(false)}
			/>
		}

		<Button id="wp-smush-open-media-library-scanner" text={__('Re-Check Images', 'wp-smushit')}
				className="wp-smush-scan"
				icon="sui-icon-update"
				disabled={modalOpen}
				onClick={() => setModalOpen(true)}
		/>
	</>;
}

domReady(function () {
	const scannerContainer = document.getElementById('wp-smush-media-library-scanner');
	if (scannerContainer) {
		ReactDOM.render(
			<MediaLibraryScanner/>,
			scannerContainer
		);
	}
});