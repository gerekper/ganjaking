/* global ajaxurl */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * SUI dependencies
 */
import { NoticeBlack } from '@wpmudev/shared-notifications-black-friday';

/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';

/**
 * Hide notice.
 *
 * @since 3.9.2
 */
function hideNotice() {
	const xhr = new XMLHttpRequest();
	xhr.open('POST', ajaxurl + '?action=smush_hide_black_friday', true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.send('_ajax_nonce=' + window.wp_smush_msgs.nonce);
}

/**
 * Render the "Black Friday" component.
 *
 * @since 3.9.2
 */
domReady(function () {
	const blackFridayDiv = document.getElementById('smush-black-friday');
	if (blackFridayDiv) {
		ReactDOM.render(
			<NoticeBlack link={window.smush_bf.link} onCloseClick={hideNotice}>
				<p>
					<strong>{window.smush_bf.header}</strong>{' '}
					{window.smush_bf.message}
				</p>
				<p>
					<small>{window.smush_bf.notice}</small>
				</p>
			</NoticeBlack>,
			blackFridayDiv
		);
	}
});