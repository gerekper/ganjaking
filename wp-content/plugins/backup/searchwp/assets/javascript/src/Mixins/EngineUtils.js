export const EngineUtils = {
	methods: {
		getEngineProperty: function(engineName, propertyName) {
			return this.$store.state.engines[engineName][propertyName];
		},
		getEngineSourceProperty: function(engineName, sourceName, propertyName) {
			return this.$store.state.engines[engineName].sources[sourceName][propertyName];
		}
	}
}
