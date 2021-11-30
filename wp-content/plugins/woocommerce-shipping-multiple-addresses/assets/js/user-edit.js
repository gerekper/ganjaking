jQuery(document).ready(function($) {
    $(".view-addresses-table").click(function(e) {
        e.preventDefault();
        $("#other_addresses_div").toggle();
    });

    $(".edit-address").click(function(e) {
        e.preventDefault();

        var index = $(this).data("index");
        $("#address-form-" + index).toggle();
    });

    $("tr.address-form .btn-cancel").click(function() {
        $(this).parents("tr").toggle();
    });

    $("tr.address-form .btn-save").click(function() {
        var $tr = $(this).parents("tr.address-form");
        var fields = $(this).parents("tr").find(":input").serialize();
        var index = $tr.data("index");
        var data = {
            action: 'wcms_edit_user_address',
            user: $("#user_id").val(),
            index: index,
            data: fields
        }

        $.post( ajaxurl, data, function(resp) {
            $("#address-" + index + " div.address").html( resp );
            $tr.toggle();
        });
    });
});