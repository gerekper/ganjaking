var __webpack_exports__ = {};
/**
 * @package Polylang-WC
 */

/**
 * Module to handle order language change.
 */
const pllOrder = {
	selectLangElement: undefined,
	currentFlag: undefined,
	flags: {},

	init: () => {
		pllOrder.selectLangElement = document.querySelector( '#post_lang_choice' );
		pllOrder.currentFlag = document.querySelector( '.pll-select-flag img' );
		pllOrder.flags = JSON.parse( document.querySelector( '#select-post-language' ).dataset.flags );

		if ( null === pllOrder.selectLangElement ) {
			return;
		}

		pllOrder.selectLangElement.addEventListener( 'change', pllOrder.languageChange );
	},

	languageChange: ( event ) => {
		if ( null === pllOrder.currentFlag || null === pllOrder.flags ) {
			return;
		}

		const newLang = event.target.value;
		const langTemplate = document.createElement( 'template' );

		if ( ! newLang in pllOrder.flags ) {
			return;
		}

		// Flags are already escaped, see {PLLWC_Admin_Orders::order_language()}.
		langTemplate.innerHTML = pllOrder.flags[newLang]; // phpcs:ignore WordPressVIPMinimum.JS.InnerHTML.Found
		pllOrder.currentFlag.replaceWith( langTemplate.content ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.replaceWith
		pllOrder.currentFlag = document.querySelector( '.pll-select-flag img' );
	},
}

document.addEventListener( 'DOMContentLoaded', pllOrder.init );

