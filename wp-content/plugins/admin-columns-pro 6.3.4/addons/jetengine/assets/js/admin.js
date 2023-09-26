const isAddonColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && group.label.indexOf( 'JetEngine' ) === 0 ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let icon = aca_jetengine_admin.assets + 'images/jetengine.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isAddonColumn( pl.result ) ) {
			value += `<img src="${icon}" alt="JetEngine" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );