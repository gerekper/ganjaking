const isAddonColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && group.label.indexOf( 'MetaBox' ) > -1 ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let icon = aca_metabox_admin.assets + 'images/metabox.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isAddonColumn( pl.result ) ) {
			value += `<img src="${icon}" alt="MetaBox" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );