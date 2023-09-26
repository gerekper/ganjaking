const isAddonColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && group.label.indexOf( 'Toolset' ) === 0 ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let icon = aca_types_admin.assets + 'images/toolset.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isAddonColumn( pl.result ) ) {
			value += `<img src="${icon}" alt="Toolset Types" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );