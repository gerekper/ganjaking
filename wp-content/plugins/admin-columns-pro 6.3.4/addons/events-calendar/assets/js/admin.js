const isEcColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && group.label.indexOf( 'The Events Calendar' ) === 0 ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let ecIcon = aca_ec_admin.assets + 'images/events.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isEcColumn( pl.result ) ) {
			value += `<img src="${ecIcon}" alt="Events Calendar" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );