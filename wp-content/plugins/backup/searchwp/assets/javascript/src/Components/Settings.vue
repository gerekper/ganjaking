<template>
	<div :class="['searchwp-settings' ]">
		<div class="searchwp-settings-view-header">
			<h1>{{ 'SearchWP Settings' | i18n }}</h1>
		</div>
		<div :class="['searchwp-settings-types' ]">
			<Stopwords></Stopwords>
			<Synonyms></Synonyms>
		</div>
	</div>
</template>

<script>
import Synonyms from './Synonyms.vue';
import Stopwords from './Stopwords.vue';

export default {
	name: 'Settings',
	components: {
		Stopwords,
		Synonyms
	}
}
</script>

<style lang="scss">
	@import './../global.scss';

	.searchwp-settings-types {
		display: flex;
		justify-content: space-between;
		width: 100%;

		> * {
			width: 49%;
			margin-top: 1em;
		}
	}

	.searchwp-meta-box-heading {
		background: white;
		position: sticky;
		top: 32px; // TODO: Admin Bar height.
		z-index: 999;
	}

	@media screen and (max-width:1024px) {
		.searchwp-settings-view .searchwp-settings-types {
			display: block;

			> * {
				width: auto;
				margin-bottom: 1.5em;
			}
		}
	}
</style>
