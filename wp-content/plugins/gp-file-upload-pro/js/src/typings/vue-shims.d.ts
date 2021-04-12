declare module '*.vue' {
	import Vue from 'vue';
	export default Vue
}

declare module 'vue-advanced-cropper' {
	import Vue from 'vue';

	export {
		Vue as Cropper,
	}
}
