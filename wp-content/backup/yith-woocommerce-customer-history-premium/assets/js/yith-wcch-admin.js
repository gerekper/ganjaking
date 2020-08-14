
/*
 *	Open Admin Menu
 */

function yit_open_admin_menu( menu_id ) {

	jQuery( '#' + menu_id ).removeClass('wp-not-current-submenu').addClass('current wp-has-current-submenu');
	jQuery( '#' + menu_id + ' > ul.wp-submenu' ).css('position','static');

}