/**
 *    Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com
 *
 *    WordPress related JavaScript
 *
 */
jQuery(document).ready(function ($) {

    /* Menu editor */

    var the_nav_menu_editor = $('#menu-to-edit');

    function crb_disable_menu_field() {
        the_nav_menu_editor.find('input[value^="*MENU*CERBER*"]').attr('readonly', true);
    }

    the_nav_menu_editor.on('click', 'a.item-edit', function (event) {
        crb_disable_menu_field();
    });

    crb_disable_menu_field();

});
