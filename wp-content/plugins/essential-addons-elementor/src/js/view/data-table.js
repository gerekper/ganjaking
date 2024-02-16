window.enableProSorter = function($, $_this) {
    var $enable_table = $_this.data('table_enabled'),
        $id           = $_this.data('table_id');

    if( true == $enable_table ) $("#eael-data-table-"+$id).tablesorter();
    if( $enable_table != true ) {
        $('table#eael-data-table-'+$id+' .sorting').addClass('sorting-none');
        $('table#eael-data-table-'+$id+' .sorting_desc').addClass('sorting-none');
        $('table#eael-data-table-'+$id+' .sorting_asc').addClass('sorting-none');
    }
};