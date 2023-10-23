'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

const $ = jQuery; // we can do this as WebPack will compact all together inside a closure.
const $body = $( 'body' );

export { $, $body };
