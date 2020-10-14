export const SourceAttribute = {
	methods: {
		isApplicable: function(attribute) {
			// An attribute is Applicable if it has settings. Settings can be an
			// integer weight or if the attribute has options it will be an object.
			if (attribute.settings && 'object'===typeof attribute.settings && Object.keys(attribute.settings).length===0) {
				return false;
			}

			if (!attribute.settings) {
				return false;
			}

			return true;
		}
	}
}
