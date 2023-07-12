import select2 from 'select2/dist/js/select2.full';

export { select2 };

// Select2 on focus add/remove (.select2-focus) class to parent (.form-floating)
function select2Focus(ele) {
  $(ele).on('select2:open', function (e) {
    $(e.target.closest('.form-floating')).addClass('select2-focus');
  });
  $(ele).on('select2:close', function (e) {
    $(e.target.closest('.form-floating')).removeClass('select2-focus');
  });
}
export { select2Focus };
