<template>
	<div class="searchwp-index-stats">
		<table>
			<thead>
				<tr>
					<th>{{ 'Statistic' | i18n }}</th>
					<th>{{ 'Value' | i18n }}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>{{ 'Last Activity' | i18n }}</th>
					<td>{{ lastActivity }}</td>
				</tr>
				<tr>
					<th>{{ 'Indexed' | i18n }}</th>
					<td><code>{{ indexed }}</code></td>
				</tr>
				<tr>
					<th>{{ 'Total' | i18n }}</th>
					<td><code>{{ total }}</code></td>
				</tr>
				<tr v-if="omitted.length">
					<th>{{ 'Omitted' | i18n }}</th>
					<td>
						<code>{{ omitted.length }}</code>
						<button class="button searchwp-button-subtle"
							style="min-height: 0; line-height: 1;"
							@click="$modal.show('omitted')">{{ 'More Info' | i18n }}</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import Notice from './../Notice.vue';
import Tooltip from './../Tooltip.vue';

export default {
	name: 'IndexStats',
	components: {
		Notice,
		Tooltip
	},
	props: {
		lastActivity: {
			type: String,
			required: true,
			default: '--'
		},
		indexed: {
			type: Number,
			required: true,
			default: 0
		},
		omitted: {
			type: Array,
			required: true,
			default: function() {
				return [];
			}
		},
		total: {
			type: Number,
			required: true,
			default: 0
		}
	},
	data () {
		return {
			showingOmittedInfo: false
		}
	}
}
</script>

<style lang="scss">
	.searchwp-index-stats > table {

		thead {
			display: none;
		}

		th {
			width: 45%;
		}

		td {
			width: 55%;
		}
	}
</style>
