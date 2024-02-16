jQuery(document).ready(function($) {
    $('#woocommerce_redsys_shipping_methods').select2({
        ajax: {
            url: ajaxurl, // URL de AJAX ya definida en el admin de WordPress
            dataType: 'json',
            delay: 250, // Retraso mientras se escribe la búsqueda
            data: function(params) {
                return {
                    term: params.term, // término de búsqueda
                    action: 'redsys_search_shipping_methods' // Acción AJAX
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 1 // Mínimo de caracteres para iniciar la búsqueda
    });
});
