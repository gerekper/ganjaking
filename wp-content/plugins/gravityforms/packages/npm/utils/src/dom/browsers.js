const android = /(android)/i.test( window.navigator.userAgent );
const chrome = !! window.chrome;
const firefox = typeof InstallTrigger !== 'undefined';
const ie = /* @cc_on!@ */ false || document.documentMode || false;
const edge = ! ie && !! window.StyleMedia;
const ios = !! window.navigator.userAgent.match( /(iPod|iPhone|iPad)/i );
const iosMobile = !! window.navigator.userAgent.match( /(iPod|iPhone)/i );
const opera =
	!! window.opera || window.navigator.userAgent.indexOf( ' OPR/' ) >= 0;
const safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line
const os = window.navigator.platform;

export default function() {
	return {
		android,
		chrome,
		edge,
		firefox,
		ie,
		ios,
		iosMobile,
		opera,
		safari,
		os,
	};
}
