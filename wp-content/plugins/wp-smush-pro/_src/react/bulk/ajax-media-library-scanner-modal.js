import React, {useEffect, useRef, useState} from "react";
import {post} from "../utils/request";
import MediaLibraryScannerModal from "./media-library-scanner-modal";

export default function AjaxMediaLibraryScannerModal(
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
	const cancelledRef = useRef();

	useEffect(() => {
		cancelledRef.current = cancelled;
	}, [cancelled]);

	function start() {
		setInProgress(true);

		post('wp_smush_before_scan_library', nonce).then((response) => {
			const sliceCount = response?.slice_count;
			const slicesList = range(sliceCount, 1);
			const parallelRequests = response?.parallel_requests;

			handleBatch(slicesList, sliceCount, parallelRequests)
				.then(() => {
					setTimeout(() => {
						onScanCompleted();
					}, 1000);
				});
		});
	}

	function handleBatch(remainingSlicesList, sliceCount, parallelRequests) {
		const batchPromises = [];
		const completedSliceCount = Math.max(sliceCount - remainingSlicesList.length, 0);
		const batch = remainingSlicesList.splice(0, parallelRequests);

		updateProgress(completedSliceCount, sliceCount);

		batch.forEach((sliceNumber) => {
			batchPromises.push(
				post('wp_smush_scan_library_slice', nonce, {slice: sliceNumber})
			);
		});

		return new Promise((resolve) => {
			Promise.all(batchPromises)
				.then(() => {
					if (!cancelledRef.current) {
						if (remainingSlicesList.length) {
							handleBatch(remainingSlicesList, sliceCount, parallelRequests)
								.then(resolve);
						} else {
							updateProgress(sliceCount, sliceCount);
							resolve();
						}
					}
				});
		});
	}

	function range(size, startAt = 0) {
		return [...Array(size).keys()].map(i => i + startAt);
	}

	function cancelScan() {
		setCancelled(true);
		setInProgress(false);
		setProgress(0);
	}

	function updateProgress(completedSlices, totalSlices) {
		const progress = (completedSlices / totalSlices) * 100;
		setProgress(progress);
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
