const isBpColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && 'BuddyPress' === group.label ) {
			return true;
		}

		if( group && 'Custom' === group.label && AC.list_screen === 'bp-groups' ){
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let bpIcon = aca_bp_admin.assets + 'images/buddypress.svg';

	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl ) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isBpColumn( pl.result ) ) {
			value += `<img src="${bpIcon}" alt="BuddyPress" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} );