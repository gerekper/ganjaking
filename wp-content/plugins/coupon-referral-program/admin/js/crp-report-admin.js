/** Data Table for the Referral Coupons  **/
jQuery(document).ready(function($){
   	/** Data Table for the Referral Coupons  **/
    $('#mwb-crp-referral-table').DataTable( {
        "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {
            "lengthMenu": mwb_crp_admin.display_record,
            "zeroRecords": mwb_crp_admin.nothing_found,
            "info": mwb_crp_admin.Showing_page,
            "infoEmpty": mwb_crp_admin.no_record,
            "infoFiltered":mwb_crp_admin.filtered_info,
            "search": mwb_crp_admin.search,
            "paginate": {
                "previous": mwb_crp_admin.previous,
                "next"	  : mwb_crp_admin.next
            }
        }
    });
})