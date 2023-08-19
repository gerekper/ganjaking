
const isAcfColumn = result => {
	if ( result.element ) {
		let group = result.element.parentElement;

		if ( group && 'Yoast SEO' === group.label ) {
			return true;
		}
	}

	return false;
}

document.addEventListener( 'DOMContentLoaded', () => {
	let yoastIcon = aca_yoast_admin.assets + 'images/yoast.svg'
	AC_SERVICES.filters.addFilter( 'column_type_templates', ( value, pl) => {
		if ( pl.result.hasOwnProperty( 'id' ) && isAcfColumn( pl.result ) ) {
			value += `<img src="${yoastIcon}" alt="Yoast" class="ac-column-type-icon"/>`;
		}

		return value;
	}, 10 );

} )

