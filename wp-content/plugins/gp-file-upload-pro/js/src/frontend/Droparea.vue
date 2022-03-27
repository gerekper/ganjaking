<template>
	<div ref="droparea"
		:class="{ 'gpfup__droparea': true, 'gpfup__droparea--over': dropareaCounter, 'gpfup__droprea--maxed': reachedMaxFiles }"
		@drop.prevent="drop"
		@dragenter.prevent="allowDrop"
		@dragleave.prevent="dragleave"
	>
		<div v-if="!reachedMaxFiles">
			<span>
				{{ strings.drop_files_here }}
			</span>
			<span class="gpfup__select-files-container">
				{{ strings.or }}
				<button type="button" class="gpfup__select-files" @click.prevent="onBrowse">{{ strings.select_files }}</button>
			</span>
		</div>
		<div v-else>
			<span>
				{{ strings.max_reached }}
			</span>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from "vue";

	export default Vue.extend({
		name: "Droparea",
		data: function() {
			return {
				/**
				* dragenter will fire every time a child element is entered as will dragleave.
				* This means we need to use a counter to count how many levels deep dragenter has
				* fired to prevent flickering.
				*
				* see: https://stackoverflow.com/a/10906204
				*/
				dropareaCounter: 0,
			}
		},
		/**
		* Prevent dropping outside of GPFUP dropareas and other explicit dropareas from navigating to the dropped
		* file.
		*/
		mounted() {
			/**
			 * Filter whether or not drop events should be blocked outside of the File Upload Pro drop area.
			 *
			 * @since 1.0-alpha-3.0
			 *
			 * @param prevent_drop_outside 	boolean  Whether or not to block outside drop events.
			 */
			if (window.gform.applyFilters( 'gpfup_prevent_drop_outside', true)) {
				window.addEventListener('dragover', this.preventDragAndDrop);
				window.addEventListener('drop', this.preventDragAndDrop);
			}
		},
		destroyed() {
			if (window.gform.applyFilters( 'gpfup_prevent_drop_outside', true)) {
				window.removeEventListener('dragover', this.preventDragAndDrop);
				window.removeEventListener('drop', this.preventDragAndDrop);
			}
		},
		props: [
			'reachedMaxFiles',
			'files',
			'strings',
			'onDrop',
			'onBrowse',
		],
		methods: {
			/**
			* Helper method for preventing default on window dragover/drop
			*
			* @todo During testing, I found that e.dataTransfer.dropEffect would get rid of the drop/add cursor,
			* however, it broke other dropareas.
			*/
			preventDragAndDrop: function(e: DragEvent) {
				e.preventDefault();

				return false;
			},
			allowDrop: function(e: DragEvent) {
				if (this.reachedMaxFiles) {
					return;
				}

				this.dropareaCounter++;
			},
			drop: function(event: DragEvent) {
				if (this.reachedMaxFiles) {
					return;
				}

				this.dropareaCounter = 0;
				this.onDrop(event);
			},
			dragleave: function() {
				this.dropareaCounter--;
			}
		},
	});
</script>

<style>
	.gpfup__droparea {
		background: rgba( 0, 0, 0, 0.02 );
		border-radius: 2px;
		border: 1px dashed rgba( 0, 0, 0, 0.15 );
		text-align: center;
		position: relative;
		z-index: 1;
		color: #aaa;
		transition: all 0.25s ease-out;
		display: table;
		width: 100%;
		height: 6rem;
		padding: 1rem;
	}

	.gpfup--has-files .gpfup__droparea {
		border-top: 0;
	}

	.gpfup__droparea > div {
		display: table-cell;
		height: 100%;
		width: 100%;
		vertical-align: middle;
		text-align: center;
	}

	.gpfup__droparea--over {
		background: rgba( 0, 0, 0, 0.06 );
		border-color: rgba( 0, 0, 0, 0.3 );
		color: #333;
	}

	.gpfup__droparea--over .gpfup__select-files-container {
		display: none;
	}

</style>
