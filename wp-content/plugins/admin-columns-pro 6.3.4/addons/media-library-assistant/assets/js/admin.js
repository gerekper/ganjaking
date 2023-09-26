const isMlaColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && 'Media Library Assistant' === group.label ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let bpIcon = aca_mla_admin.assets + 'images/mla.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isMlaColumn( pl.result ) ) {
			value += `<img src="${bpIcon}" alt="MLA" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );