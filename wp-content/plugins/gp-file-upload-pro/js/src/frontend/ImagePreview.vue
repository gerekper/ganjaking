<template>
	<div :class="{ 'gpfup__preview': true, 'gpfup__preview--crop': croppingAvailable }">
		<div v-if="error" class="gpfup__upload-error-icon">
			<svg width="48" height="48" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;">
				<path d="M82.4,25.6l-20-20C62,5.2,61.5,5,61,5H23c-3.3,0-6,2.7-6,6v78c0,3.3,2.7,6,6,6h54c3.3,0,6-2.7,6-6V27,C83,26.5,82.8,26,82.4,25.6z M63,11.8L76.2,25H65c-1.1,0-2-0.9-2-2V11.8z M77,91H23c-1.1,0-2-0.9-2-2V11c0-1.1,0.9-2,2-2h36v14,c0,3.3,2.7,6,6,6h14v60C79,90.1,78.1,91,77,91z"/>
				<path d="M62.3,49.6l-7.1,7.1l7.1,7.1c1.6,1.6,1.6,4,0,5.6c-0.8,0.8-1.8,1.2-2.8,1.2s-2-0.4-2.8-1.2l-7.1-7.1l-7.1,7.1,c-0.8,0.8-1.8,1.2-2.8,1.2s-2-0.4-2.8-1.2c-1.6-1.6-1.6-4,0-5.6l7.1-7.1l-7.1-7.1c-1.6-1.6-1.6-4,0-5.6c1.6-1.6,4-1.6,5.6,0l7.1,7.1,l7.1-7.1c1.6-1.6,4-1.6,5.6,0C63.9,45.6,63.9,48,62.3,49.6z"/>
			</svg>
		</div>
		<img v-else-if="showPreview && !file.loading" :src="imgSrc" :style="previewSize" @load="storePreviewDimensions(file)" ref="image" />
		<div v-else-if="!isImage() && !file.loading" class="gpfup__file-icon">
			<svg width="48" height="48" style="enable-background:new 0 0 100 100;" version="1.1" viewBox="0 0 100 100" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<path d="M82.4,25.6l-20-20C62,5.2,61.5,5,61,5H23c-3.3,0-6,2.7-6,6v78c0,3.3,2.7,6,6,6h54c3.3,0,6-2.7,6-6V27  C83,26.5,82.8,26,82.4,25.6z M63,11.8L76.2,25H65c-1.1,0-2-0.9-2-2V11.8z M77,91H23c-1.1,0-2-0.9-2-2V11c0-1.1,0.9-2,2-2h36v14  c0,3.3,2.7,6,6,6h14v60C79,90.1,78.1,91,77,91z"/>
			</svg>
		</div>
		<div class="gpfup__preview-pending" :style="previewSize" v-else>
			<!-- By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL -->
			<svg width="50%" height="50%" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<linearGradient x1="8.042%" y1="0%" x2="65.682%" y2="23.865%" id="a">
						<stop stop-color="#000" stop-opacity="0" offset="0%"/>
						<stop stop-color="#000" stop-opacity=".631" offset="63.146%"/>
						<stop stop-color="#000" offset="100%"/>
					</linearGradient>
				</defs>
				<g fill="none" fill-rule="evenodd">
					<g transform="translate(1 1)">
						<path d="M36 18c0-9.94-8.06-18-18-18" id="Oval-2" stroke="url(#a)" stroke-width="2">
							<animateTransform
								attributeName="transform"
								type="rotate"
								from="0 18 18"
								to="360 18 18"
								dur="0.9s"
								repeatCount="indefinite" />
						</path>
						<circle fill="#fff" cx="36" cy="18" r="1">
							<animateTransform
								attributeName="transform"
								type="rotate"
								from="0 18 18"
								to="360 18 18"
								dur="0.9s"
								repeatCount="indefinite" />
						</circle>
					</g>
				</g>
			</svg>
		</div>
		<button v-if="croppingAvailable && isImage(file)" @click.prevent="editFile(file)" class="gpfup__edit">
			<svg width="60%" height="60%" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false">
				<path d="M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z"></path>
			</svg>
		</button>
	</div>
</template>

<script lang="ts">
	import isImage from './helpers/isImage';
	import Vue from 'vue';

	export default Vue.extend({
		name: "ImagePreview",
		props: ['file', 'error', 'enableCrop', 'onlyImagesAllowed', 'cropRequired'],
		computed: {
			imgSrc: function() : string {
				return this.$store.state.imgPreviews[this.file.id];
			},
			croppingAvailable: function() : boolean {
				if (!this.enableCrop) {
					return false;
				}

				/**
				* Disable for Internet Explorer due to vue-advanced-cropper not supporting IE.
				*/
				return !/MSIE|Trident/.test(window.navigator.userAgent);
			},
			previewSize: function (): { width: string, height: string } | undefined {
				if (!this.onlyImagesAllowed) {
					return;
				}

				return {
					width: this.file?.previewWidth + 'px',
					height: this.file?.previewHeight + 'px',
				};
			},
			showPreview: function() : boolean {
				if (!this.imgSrc) {
					return false;
				}

				if (this.file.rehydrated) {
					return true;
				}

				if (this.cropRequired && !this.file.cropped) {
					return false;
				}

				return this.file.processed;
			}
		},
		methods: {
			isImage: function() : boolean {
				return isImage(this.file);
			},
			editFile: function (file: MOxieFile) : void {
				this.$store.dispatch('setCurrentFile', { file });
				this.$store.commit('OPEN_EDITOR');
			},
			storePreviewDimensions: function(file: MOxieFile) : void {
				const { clientWidth, clientHeight } = this.$refs.image as Element;

				// Skip saving dimensions if either is 0.
				// This prevents issue where multi-page forms change the dimensions to 0.
				if (!clientWidth || !clientHeight) {
					return;
				}

				this.$store.state.storage.storePreviewDimensions(file.id, {
					width: clientWidth,
					height: clientHeight,
				});
			}
		}
	});
</script>

<style>

	.gpfup--images-only {}

	.gpfup__preview {
		margin: auto 1rem auto 0;
		position: relative;
	}

	.gpfup__preview img,
	.gpfup__preview-pending {
		width: 48px;
		height: 48px;
		border-radius: 4px;
	}

	.gpfup__preview img {
		object-fit: cover;
		background-color: rgba( 0, 0, 0, 0.5 );
	}

	.gpfup__preview-pending {
		background-color: rgba( 0, 0, 0, 0.1 );
		text-align: center;
	}

	.gpfup__preview-pending svg {
		opacity: 0.5;
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		margin: auto;
	}

	.gpfup--images-only .gpfup__preview {
		width: 100px;
	}

	.gpfup--images-only .gpfup__preview img,
	.gpfup--images-only .gpfup__preview-pending {
		width: 100px;
		height: 100px;
		object-fit: cover;
	}

	.gpfup--images-only .gpfup__upload-error-icon {
		width: 100px;
		text-align: center;
		position: absolute;
		top: 50%;
		transform: translate(0, -50%);
	}

	.gpfup--images-only .gpfup__preview--crop img {
		height: auto;
		object-fit: contain;
	}

	.gpfup--images-only.gpfup--circle-stencil .gpfup__preview img {
		border-radius: 50%;
	}

	.gpfup__file-icon svg,
	.gpfup__upload-error-icon svg {
		vertical-align: middle;
	}

	.gpfup__edit {
		display: none;
		padding: 0;
		background: black;
		border: 0;
		border-radius: 50%;
		cursor: pointer;
		position: absolute;
		width: 30px;
		height: 30px;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		margin: auto;
		opacity: 0.5;
		transition: opacity 0.25s ease-out;
		line-height: 1;
	}

	.gpfup__edit path {
		fill: white;
	}

	.gpfup__edit:hover {
		opacity: 1.0;
	}

	.gpfup__edit:focus {
		outline: 0;
		border: 2px solid #fff;
	}

	.gpfup__file:hover .gpfup__edit,
	.gpfup__file:focus-within .gpfup__edit {
		display: block;
	}

	.gpfup__upload-error-icon path:last-child {
		fill: #790000;
	}

</style>
