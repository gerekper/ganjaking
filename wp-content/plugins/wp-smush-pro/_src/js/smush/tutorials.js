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
import { TutorialsList } from '@wpmudev/react-tutorials-list';

/**
 * Render the "Tutorials List" component.
 *
 * @since 2.8.5
 */
domReady(function () {
	const tutorialsPageBox = document.getElementById('smush-box-tutorials');
	if (tutorialsPageBox) {
		ReactDOM.render(
			<TutorialsList
				category="11228"
				title={window.wp_smush_msgs.tutorials}
				viewAll={window.wp_smush_msgs.tutorials_link}
				translate={window.wp_smush_msgs.tutorials_strings}
			/>,
			tutorialsPageBox
		);
	}
});
