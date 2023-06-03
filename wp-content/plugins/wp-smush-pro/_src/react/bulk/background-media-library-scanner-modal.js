import React, {useRef, useState} from "react";
import {post} from "../utils/request";
import MediaLibraryScannerModal from "./media-library-scanner-modal";

export default function BackgroundMediaLibraryScannerModal(
	{
		nonce = '',
		onScanCompleted = () => false,
		onClose = () => false,
		focusAfterClose = ''
	}
) {
	const [inProgress, setInProgress] = useState(false);
	const [progress, setProgress] = useState(0);
	const [cancelled, setCancelled] = useState(false);
	const progressTimeoutId = useRef(0);

	function start() {
		post('wp_smush_start_background_scan', nonce).then(() => {
			setInProgress(true);
			progressTimeoutId.current = setTimeout(updateProgress, 2000);
		});
	}

	function clearProgressTimeout() {
		if (progressTimeoutId.current) {
			clearTimeout(progressTimeoutId.current);
		}
	}

	function updateProgress() {
		post('wp_smush_get_background_scan_status', nonce).then(response => {
			const isCompleted = response?.is_completed;
			if (isCompleted) {
				clearProgressTimeout();
				onScanCompleted();
				return;
			}

			const isCancelled = response?.is_cancelled;
			if (isCancelled) {
				clearProgressTimeout();
				changeStateToCancelled();
				return;
			}

			const totalItems = response?.total_items;
			const processedItems = response?.processed_items;
			const progress = (processedItems / totalItems) * 100;
			setProgress(progress);

			progressTimeoutId.current = setTimeout(updateProgress, 1000);
		});
	}

	function cancelScan() {
		clearProgressTimeout();
		post('wp_smush_cancel_background_scan', nonce)
			.then(changeStateToCancelled);
	}

	function changeStateToCancelled() {
		setCancelled(true);
		setProgress(0);
		setInProgress(false);
	}

	return <MediaLibraryScannerModal
		inProgress={inProgress}
		progress={progress}
		onCancel={cancelScan}
		focusAfterClose={focusAfterClose}
		onClose={onClose}
		onStart={start}
	/>;
};
