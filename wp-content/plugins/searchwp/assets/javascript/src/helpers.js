export function __(source, placeholders = []) {
	let strings    = _SEARCHWP.i18n;
	let translated = strings.hasOwnProperty(source) ? strings[source] : source;

	if (placeholders.length) {
		placeholders.forEach(function(placeholder, placeholderIndex) {
			translated = translated.replace(
				"{{ searchwpPlaceholder" + parseInt(placeholderIndex + 1, 10) + " }}",
				placeholder
			);
		});
	}

	return translated;
}

export function normalizeSource(sources, source) {
	for (let attribute in sources[source].attributes) {
		if (sources[source].attributes[attribute].default
			&& !isNaN(sources[source].attributes[attribute].default)
			&& !Array.isArray(sources[source].attributes[attribute].options)
		) {
			sources[source].attributes[attribute].settings = sources[source].attributes[attribute].default;
		}
	}

	return sources[source];
}

export function removeCollapsedSources(state, engine) {
	// Remove view collapsed settings for this engine.
	const engineIndex    = Object.keys(state.engines).indexOf(engine) + _SEARCHWP.separator;
	const removedSources = state.view.collapsed.filter(source => engineIndex === source.substring(0, engineIndex.length));

	if (removedSources) {
		state.view.collapsed = state.view.collapsed.filter(x => !removedSources.includes(x));
		return true;
	}

	return false;
}

export function persistViewConfig(state) {
	jQuery.post(ajaxurl, {
		_ajax_nonce: _SEARCHWP.nonce,
		action: _SEARCHWP.misc.prefix + 'engines_view',
		config: state.view
	});
}
