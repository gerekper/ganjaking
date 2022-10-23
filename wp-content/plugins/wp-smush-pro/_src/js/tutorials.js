/* global ajaxurl */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';

/**
 * SUI dependencies
 */
import { TutorialsList, TutorialsSlider } from '@wpmudev/shared-tutorials';

import MixPanel from "./mixpanel"

function hideTutorials() {
	const xhr = new XMLHttpRequest();

	xhr.open( 'POST', ajaxurl + '?action=smush_hide_tutorials', true );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );

	xhr.onload = () => {
		if ( 200 === xhr.status ) {
			const noticeMessage = `<p>${ window.wp_smush_msgs.tutorialsRemoved }</p>`,
				noticeOptions = {
					type: 'success',
					icon: 'check',
				};

			window.SUI.openNotice(
				'wp-smush-ajax-notice',
				noticeMessage,
				noticeOptions
			);
		}
	};

	xhr.send( '_ajax_nonce=' + window.wp_smush_msgs.nonce );
}

/**
 * Render the "Tutorials List" component.
 *
 * @since 2.8.5
 */
domReady( function() {
	// Tutorials section on Dashboard page.
	const tutorialsDiv = document.getElementById( 'smush-dash-tutorials' );
	if ( tutorialsDiv ) {
		ReactDOM.render(
			<TutorialsSlider
				category="11228"
				title={ window.smush_tutorials.tutorials }
				viewAll={ window.smush_tutorials.tutorials_link }
				onCloseClick={ hideTutorials }
			/>,
			tutorialsDiv
		);
	}

	// Tutorials page.
	const tutorialsPageBox = document.getElementById( 'smush-box-tutorials' );
	if ( tutorialsPageBox ) {
		ReactDOM.render(
			<TutorialsList
				category="11228"
				title={ window.smush_tutorials.tutorials }
				translate={ window.smush_tutorials.tutorials_strings }
			/>,
			tutorialsPageBox
		);
	}
} );

jQuery(function ($) {
	$(document).on('click', '#smush-box-tutorials li > [role="link"], #smush-dash-tutorials li > [role="link"]', function () {
		const $tutorial = $(this);
		const isDashPage = !!$tutorial.closest('#smush-dash-tutorials').length;
		const decodeHtml = (html) => {
			const txt = document.createElement("textarea");
			txt.innerHTML = html;
			return txt.value;
		};
		const title = decodeHtml($tutorial.attr('title'));

		(new MixPanel()).track('Tutorial Opened', {
			'Tutorial Name': title,
			'Triggered From': isDashPage ? 'Dashboard' : 'Tutorials Tab'
		});
	});
});
